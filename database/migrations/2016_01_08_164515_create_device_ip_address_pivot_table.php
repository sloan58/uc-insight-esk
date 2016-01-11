<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceIpAddressPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_ip_address', function (Blueprint $table) {
            $table->integer('device_id')->unsigned()->index();
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
            $table->integer('ip_address_id')->unsigned()->index();
            $table->foreign('ip_address_id')->references('id')->on('ip_addresses')->onDelete('cascade');
            $table->primary(['device_id', 'ip_address_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('device_ip_address');
    }
}
