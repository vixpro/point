<?php

namespace App\Http\Controllers\Api\Inventory\InventoryUsage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Approval\ApproveRequest;
use App\Http\Requests\Approval\RejectRequest;
use App\Http\Resources\ApiResource;
use App\Model\Inventory\InventoryUsage\InventoryUsage;

class InventoryUsageApprovalController extends Controller
{
  /**
   * @param ApproveRequest $request
   * @param $id
   * @return ApiResource
   */
  public function approve(ApproveRequest $request, $id)
  {
    $inventoryUsage = InventoryUsage::findOrFail($id);

    if ($inventoryUsage->form->request_approval_to !== auth()->user()->id) {
      return response()->json(['message' => "User don't have approval access!"], 403);
    };

    $inventoryUsage->form->approval_by = auth()->user()->id;
    $inventoryUsage->form->approval_at = now();
    $inventoryUsage->form->approval_status = 1;
    if ($inventoryUsage->form->request_cancellation_by) {
      $inventoryUsage->form->cancellation_status = true;
    }
    $inventoryUsage->form->save();

    InventoryUsage::updateInventory($inventoryUsage->form, $inventoryUsage);

    return new ApiResource($inventoryUsage);
  }

  /**
   * @param RejectRequest $request
   * @param $id
   * @return ApiResource
   */
  public function reject(RejectRequest $request, $id)
  {
    $inventoryUsage = InventoryUsage::findOrFail($id);

    if ($inventoryUsage->form->request_approval_to !== auth()->user()->id) {
      return response()->json(['message' => "User don't have reject access!"], 403);
    };

    $inventoryUsage->form->approval_by = auth()->user()->id;
    $inventoryUsage->form->approval_at = now();
    $inventoryUsage->form->approval_reason = $request->get('reason');
    $inventoryUsage->form->approval_status = -1;
    $inventoryUsage->form->save();

    return new ApiResource($inventoryUsage);
  }
}
