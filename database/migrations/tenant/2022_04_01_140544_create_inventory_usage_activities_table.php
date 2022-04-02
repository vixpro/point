<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryUsageActivitiesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('inventory_usage_activities', function (Blueprint $table) {
      $table->id();
      $table->string('activity_name');
      $table->unsignedBigInteger('inventory_usage_id')->index();
      $table->unsignedBigInteger('user_id')->index();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('inventory_usage_activities');
  }
}
