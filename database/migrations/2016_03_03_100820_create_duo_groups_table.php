<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDuoGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('duo_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('group_id')->unique();
            $table->string('name');
            $table->string('desc');
            $table->string('status');
            $table->boolean('mobile_otp_enabled');
            $table->boolean('push_enabled');
            $table->boolean('sms_enabled');
            $table->boolean('voice_enabled');
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
        Schema::drop('duo_groups');
    }
}
