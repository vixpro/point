<?php

namespace App\Http\Controllers\Api\Inventory\InventoryUsage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\Usage\StoreRequest;
use App\Http\Requests\Inventory\Usage\UpdateRequest;
use App\Http\Resources\ApiCollection;
use App\Http\Resources\ApiResource;
use Carbon\Carbon;
use App\Model\Form;
use App\Model\Master\User as TenantUser;
use App\Mail\InventoryUsageApprovalNotificationMail;
use App\Model\Inventory\InventoryUsage\InventoryUsage;
use App\Model\Inventory\InventoryUsage\InventoryUsageActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class InventoryUsageController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @param Request $request
   * @return ApiCollection
   */
  public function index(Request $request)
  {
    $inventoryUsages = InventoryUsage::eloquentFilter($request);

    if ($request->get('join')) {
      $fields = explode(',', $request->get('join'));

      if (in_array('form', $fields)) {
        $inventoryUsages = $inventoryUsages->join(Form::getTableName() . ' as ' . Form::$alias, function ($q) {
          $q->on(Form::$alias . '.formable_id', '=', InventoryUsage::getTableName('id'))
            ->where(Form::$alias . '.formable_type', InventoryUsage::$morphName);
        });
      }
    }

    $inventoryUsages = pagination($inventoryUsages, $request->get('limit'));

    return new ApiCollection($inventoryUsages);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param StoreRequest $request
   * @return mixed
   * @throws \Throwable
   */
  public function store(StoreRequest $request)
  {
    $result = DB::connection('tenant')->transaction(function () use ($request) {
      $inventoryUsage = InventoryUsage::create($request->all());
      $inventoryUsage
        ->load('form')
        ->load('warehouse')
        ->load('items.item')
        ->load('items.allocation');

      return new ApiResource($inventoryUsage);
    });

    $this->sendApprovalEmail($request, $result);

    InventoryUsageActivity::create([
      'activity_name' => 'Created',
      'inventory_usage_id' => $result->id,
      'user_id' => auth()->user()->id,
    ]);

    return $result;
  }

  /**
   * Display the specified resource.
   *
   * @param Request $request
   * @param $id
   * @return ApiResource
   */
  public function show(Request $request, $id)
  {
    $inventoryUsage = InventoryUsage::eloquentFilter($request)->with('form.createdBy')->findOrFail($id);

    if ($request->has('with_archives')) {
      $inventoryUsage->archives = $inventoryUsage->archives();
    }

    if ($request->has('with_origin')) {
      $inventoryUsage->origin = $inventoryUsage->origin();
    }

    return new ApiResource($inventoryUsage);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param UpdateRequest $request
   * @param $id
   * @return ApiResource
   * @throws \Throwable
   */
  public function update(UpdateRequest $request, $id)
  {
    $inventoryUsage = InventoryUsage::with('form')->findOrFail($id);

    $inventoryUsage->isAllowedToUpdate();

    $result = DB::connection('tenant')->transaction(function () use ($request, $inventoryUsage) {
      $inventoryUsage->form->archive();
      $request['number'] = $inventoryUsage->form->edited_number;
      $request['old_increment'] = $inventoryUsage->form->increment;

      $inventoryUsage = InventoryUsage::create($request->all());
      $inventoryUsage
        ->load('form')
        ->load('items.item')
        ->load('items.allocation');

      return new ApiResource($inventoryUsage);
    });

    InventoryUsageActivity::create([
      'activity_name' => 'Updated',
      'inventory_usage_id' => $result->id,
      'user_id' => auth()->user()->id,
    ]);

    return $result;
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param Request $request
   * @param $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, $id)
  {
    $inventoryUsage = InventoryUsage::findOrFail($id);

    $inventoryUsage->isAllowedToDelete();

    $inventoryUsage->requestCancel($request);

    InventoryUsageActivity::create([
      'activity_name' => 'Deleted',
      'inventory_usage_id' => $inventoryUsage->id,
      'user_id' => auth()->user()->id,
    ]);

    return response()->json([], 204);
  }

  /**
   * Get activity history form
   *
   * @param Request $request
   * @param $id
   * @return string
   */

  public function history(Request $request, $id)
  {
    $inventoryUsage = InventoryUsage::findOrFail($id);
    $inventoryUsageActivity = InventoryUsageActivity::where(['inventory_usage_id' => $inventoryUsage->id])->eloquentFilter($request);

    if ($request->get('join')) {
      $fields = explode(',', $request->get('join'));

      if (in_array('form', $fields)) {
        $inventoryUsageActivity = $inventoryUsageActivity->join(Form::getTableName(), function ($q) {
          $q->on(Form::getTableName('formable_id'), '=', InventoryUsageActivity::getTableName('inventory_usage_id'))
            ->where(Form::getTableName('formable_type'), "InventoryUsage");
        });
      }
    }

    $inventoryUsageActivity = pagination($inventoryUsageActivity, $request->get('limit'));

    return new ApiResource($inventoryUsageActivity);
  }

  /**
   * Send email to user approver
   *
   * @param Request $request
   * @param $result
   * @return string
   */

  private function sendApprovalEmail($request, $result)
  {

    $fromTz = config()->get('app.timezone');
    $toTz = config()->get('project.timezone');
    $payload = [];
    $payload["approver_name"] = $request->approver_name;
    $payload["approver_email"] = $request->approver_email;
    $payload["form"] = $result->form;
    $payload["warehouse"] = $result->warehouse;
    $payload["day_time"] = Carbon::parse($result->date, $fromTz)->timezone($toTz)->isoFormat("DD MMMM YYYY");
    $payload["created_at"] = Carbon::parse($result->form->created_at, $fromTz)->timezone($toTz)->isoFormat("DD MMMM YYYY HH:mm");
    $payload["created_by"] = TenantUser::findOrFail($result->form->created_by)->name;
    $payload["notes"] = $result->form->notes;
    $payload["items"] = $request->items;

    $payload["base_url"] = 'https://' . env('TENANT_DOMAIN');
    $payload["urls"] = [
      "check_url" => $payload["base_url"] . '/inventory/usage/' . $result->id,
      "approve_url" => $payload["base_url"] . '/inventory/usage/' . $result->id,
      "reject_url" => $payload["base_url"] . '/inventory/usage/' . $result->id,
    ];

    Mail::to($payload["approver_email"])->queue(new InventoryUsageApprovalNotificationMail($payload));

    return "success";
  }
}
