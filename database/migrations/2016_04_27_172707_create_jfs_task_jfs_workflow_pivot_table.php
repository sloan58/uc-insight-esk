<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJfsTaskJfsWorkflowPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jfs_task_jfs_workflow', function (Blueprint $table) {
            $table->integer('jfs_task_id')->unsigned()->index();
            $table->foreign('jfs_task_id')->references('id')->on('jfs_tasks')->onDelete('cascade');
            $table->integer('jfs_workflow_id')->unsigned()->index();
            $table->foreign('jfs_workflow_id')->references('id')->on('jfs_workflows')->onDelete('cascade');
            $table->primary(['jfs_task_id', 'jfs_workflow_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('jfs_task_jfs_workflow');
    }
}
