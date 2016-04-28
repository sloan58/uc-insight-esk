<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJfsSiteJfsTaskPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jfs_site_jfs_task', function (Blueprint $table) {
            $table->integer('jfs_site_id')->unsigned()->index();
            $table->foreign('jfs_site_id')->references('id')->on('jfs_sites')->onDelete('cascade');
            $table->integer('jfs_task_id')->unsigned()->index();
            $table->foreign('jfs_task_id')->references('id')->on('jfs_tasks')->onDelete('cascade');
            $table->primary(['jfs_site_id', 'jfs_task_id']);
            $table->boolean('completed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('jfs_site_jfs_task');
    }
}
