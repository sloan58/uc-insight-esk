<?php

use App\Models\Report;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    /**
     * @var array
     */
    protected $tables = [
        'reports'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->cleanDatabase();

        /*
         * Seed CUCM Daily Reports Table
         */
        Report::create([
            'name' => 'NonePartitionReport',
            'path' => 'reports/cucm/none-pt/',
            'type' => 'cucm_daily',
            'job'  => 'App\Jobs\GetDnsInNonePartition',
            'csv_headers' => 'Directory Number,Description'
        ]);
        Report::create([
            'name' => 'CallForwardLoopReport',
            'path' => 'reports/cucm/cfwd-loop/',
            'type' => 'cucm_daily',
            'job'  => 'App\Jobs\CheckForCallForwardLoop',
            'csv_headers' => 'Directory Number,Description,Forward Number'
        ]);
        Report::create([
            'name' => 'PhoneFirmwareReport',
            'path' => 'reports/cucm/phone-firmware/',
            'type' => 'phone_firmware',
            'job'  => 'App\Jobs\GetPhoneFirmware',
            'csv_headers' => 'DeviceName,Product,Description,IsRegistered,IpAddress,Model,Firmware',
        ]);
        Report::create([
            'name' => 'DuoRegisteredUsersReport',
            'path' => 'reports/duo/registered-users/',
            'type' => 'duo_registered_users',
            'job'  => 'App\Jobs\GenerateRegisteredDuoUsersReport',
            'csv_headers' => 'username,email,status,last_login,group_name',
        ]);

    }

    /**
     * Clean out the database for a new seed generation
     */
    private function cleanDatabase()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach($this->tables as $table)
        {

            DB::table($table)->truncate();

        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

    }
}
