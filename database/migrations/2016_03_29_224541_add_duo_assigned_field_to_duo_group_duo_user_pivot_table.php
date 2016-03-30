<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDuoAssignedFieldToDuoGroupDuoUserPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('duo_group_duo_user', function (Blueprint $table) {
            $table->boolean('duo_assigned')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('duo_group_duo_user', function (Blueprint $table) {
            $table->dropColumn('duo_assigned');
        });
    }
}
