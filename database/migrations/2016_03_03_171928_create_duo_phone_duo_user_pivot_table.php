<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDuoPhoneDuoUserPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('duo_phone_duo_user', function (Blueprint $table) {
            $table->integer('duo_phone_id')->unsigned()->index();
            $table->foreign('duo_phone_id')->references('id')->on('duo_phones')->onDelete('cascade');
            $table->integer('duo_user_id')->unsigned()->index();
            $table->foreign('duo_user_id')->references('id')->on('duo_users')->onDelete('cascade');
            $table->primary(['duo_phone_id', 'duo_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('duo_phone_duo_user');
    }
}
