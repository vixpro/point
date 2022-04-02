<?php

namespace App\Model\Inventory\InventoryUsage;

use App\User;
use App\Model\Form;
use App\Model\TransactionModel;

class InventoryUsageActivity extends TransactionModel
{

  public static $morphName = 'InventoryUsageActivity';

  protected $connection = 'tenant';

  public static $alias = 'inventory_usage_activity';

  public $timestamps = true;

  protected $fillable = [
    'activity_name',
    'inventory_usage_id',
    'user_id'
  ];

  public function form()
  {
    return $this->belongsTo(Form::class, 'inventory_usage_id');
  }

  public function user()
  {
    return $this->belongsTo(User::class, 'user_id');
  }
}
