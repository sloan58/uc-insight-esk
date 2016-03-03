<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDuoGroupDuoUserPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('duo_group_duo_user', function (Blueprint $table) {
            $table->integer('duo_group_id')->unsigned()->index();
            $table->foreign('duo_group_id')->references('id')->on('duo_groups')->onDelete('cascade');
            $table->integer('duo_user_id')->unsigned()->index();
            $table->foreign('duo_user_id')->references('id')->on('duo_users')->onDelete('cascade');
            $table->primary(['duo_group_id', 'duo_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('duo_group_duo_user');
    }
}
