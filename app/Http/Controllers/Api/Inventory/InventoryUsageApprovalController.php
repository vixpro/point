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
    $inventoryUsage->form->approval_by = auth()->user()->id;
    $inventoryUsage->form->approval_at = now();
    $inventoryUsage->form->approval_status = 1;
    $inventoryUsage->form->save();

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
    $inventoryUsage->form->approval_by = auth()->user()->id;
    $inventoryUsage->form->approval_at = now();
    $inventoryUsage->form->approval_reason = $request->get('reason');
    $inventoryUsage->form->approval_status = -1;
    $inventoryUsage->form->save();

    return new ApiResource($inventoryUsage);
  }
}
