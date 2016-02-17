<?php

use App\Models\Report;
use Illuminate\Database\Seeder;

class CucmReportSeeder extends Seeder
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
