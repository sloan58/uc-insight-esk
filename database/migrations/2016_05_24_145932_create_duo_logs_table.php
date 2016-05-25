<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDuoLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('duo_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('device')->nullable();
            $table->string('factor');
            $table->string('integration');
            $table->string('ip');
            $table->boolean('new_enrollment');
            $table->string('reason')->nullable();
            $table->string('result');
            $table->timestamp('timestamp');
            $table->string('username');
            $table->integer('duo_user_id')->unsigned()->index()->nullable();
            $table->timestamps();

            $table->foreign('duo_user_id')->references('id')->on('duo_users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('duo_logs');
    }
}
