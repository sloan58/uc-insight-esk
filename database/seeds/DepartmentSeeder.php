<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DepartmentSeeder extends Seeder
{
    /**
     * @var array
     */
    protected $tables = [
        'departments',
    ];

    public function run()
    {
        Model::unguard();

        $this->cleanDatabase();

        App\Models\Department::create([
            'name' => 'uc-insight'
        ]);

        App\Models\Department::create([
            'name' => 'jfs-insight'
        ]);

        Model::reguard();

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
