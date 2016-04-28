<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJfsTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jfs_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->integer('jfs_workflow_id')->unsigned()->index()->nullable();
            $table->foreign('jfs_workflow_id')->references('id')->on('jfs_workflows');
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
        Schema::drop('jfs_tasks');
    }
}
