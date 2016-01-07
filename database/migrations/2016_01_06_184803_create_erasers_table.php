<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateErasersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('erasers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->string('device_description');
            $table->string('result');
            $table->string('fail_reason');
            $table->integer('ip_address_id')->unsigned();
            $table->foreign('ip_address_id')->references('id')->on('ip_addresses');
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
        Schema::drop('erasers');
    }
}
