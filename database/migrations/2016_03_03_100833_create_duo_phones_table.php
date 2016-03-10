<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDuoPhonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('duo_phones', function (Blueprint $table) {
            $table->increments('id');
            $table->string('phone_id')->unique();
            $table->string('name');
            $table->string('number');
            $table->string('extension');
            $table->string('type');
            $table->string('platform');
            $table->string('postdelay');
            $table->string('predelay');
            $table->string('sms_passcodes_sent');
            $table->boolean('actived');
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
        Schema::drop('duo_phones');
    }
}
