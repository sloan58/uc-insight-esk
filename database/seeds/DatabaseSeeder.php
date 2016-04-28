<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{

    /**
     * @var array
     */
    protected $tables = [
        'audits',
        'cluster_user',
        'clusters',
        'devices',
        'erasers',
        'ip_addresses',
        'permission_role',
        'permission_user',
        'permissions',
        'role_user',
        'roles',
        'routes',
        'users',
        'reports',
        'report_user'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->cleanDatabase();

        $this->call('DepartmentSeeder');
        $this->call('ClusterSeeder');
        $this->call('ProductionSeeder');
        $this->call('ReportSeeder');
        $this->call('DuoCapabilitySeeder');
        $this->call('JfsDashboardSeeder');

        if( App::environment() === 'development' )
        {
            $this->call('DevelopmentSeeder');
        }

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
