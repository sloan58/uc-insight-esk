<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJfsWorkflowJfsSitePivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jfs_workflow_jfs_site', function (Blueprint $table) {
            $table->integer('jfs_workflow_id')->unsigned()->index();
            $table->foreign('jfs_workflow_id')->references('id')->on('jfs_workflows')->onDelete('cascade');
            $table->integer('jfs_site_id')->unsigned()->index();
            $table->foreign('jfs_site_id')->references('id')->on('jfs_sites')->onDelete('cascade');
            $table->primary(['jfs_workflow_id', 'jfs_site_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('jfs_workflow_jfs_site');
    }
}
