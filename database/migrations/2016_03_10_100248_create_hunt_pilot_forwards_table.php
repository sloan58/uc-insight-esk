<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHuntPilotForwardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hunt_pilot_forwards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('hunt_pilot_uuid');
            $table->string('dn_uuid');
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
        Schema::drop('hunt_pilot_forwards');
    }
}
