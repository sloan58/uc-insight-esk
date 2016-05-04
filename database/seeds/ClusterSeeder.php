<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ClusterSeeder extends Seeder
{
    /**
     * @var array
     */
    protected $tables = [
        'clusters',
    ];

    public function run()
    {
        Model::unguard();

        $this->cleanDatabase();

        App\Models\Cluster::create([
            'name' => 'Demo Cluster',
            'ip' => '10.1.10.1',
            'user_type' => 'AppUser',
            'version' => '10.5',
            'username' => 'AppUser',
            'password' => 'password',
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
