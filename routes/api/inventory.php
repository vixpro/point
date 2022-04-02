<?php

Route::prefix('inventory')->namespace('Inventory')->group(function () {
  Route::get('inventory-recapitulations', 'InventoryRecapitulationController@index');
  Route::get('inventory-warehouse-recapitulations/{itemId}', 'InventoryWarehouseRecapitulationController@index');
  Route::get('inventory-details/{itemId}', 'InventoryDetailController@index');
  Route::get('inventory-dna/{itemId}', 'InventoryDnaController@index');
  Route::get('inventory-dna/{itemId}/all', 'InventoryDnaController@allDna');
  Route::post('usages/{id}/approve', 'InventoryUsage\\InventoryUsageApprovalController@approve');
  Route::post('usages/{id}/reject', 'InventoryUsage\\InventoryUsageApprovalController@reject');
  Route::get('usages/{id}/history', 'InventoryUsage\\InventoryUsageController@history');
  Route::apiResource('audits', 'InventoryAudit\\InventoryAuditController');
  Route::apiResource('usages', 'InventoryUsage\\InventoryUsageController');
  // Route::apiResource('inventory-corrections', 'InventoryCorrectionController');
  // Route::apiResource('transfer-items', 'TransferItemController');
});
