<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDuoCapabilityDuoPhonePivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('duo_capability_duo_phone', function (Blueprint $table) {
            $table->integer('duo_capability_id')->unsigned()->index();
            $table->foreign('duo_capability_id')->references('id')->on('duo_capabilities')->onDelete('cascade');
            $table->integer('duo_phone_id')->unsigned()->index();
            $table->foreign('duo_phone_id')->references('id')->on('duo_phones')->onDelete('cascade');
            $table->primary(['duo_capability_id', 'duo_phone_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('duo_capability_duo_phone');
    }
}
