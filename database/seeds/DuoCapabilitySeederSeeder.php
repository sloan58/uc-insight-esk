<?php

use Illuminate\Database\Seeder;

// composer require laracasts/testdummy
use Laracasts\TestDummy\Factory as TestDummy;

class DuoCapabilitySeeder extends Seeder
{
    public function run()
    {
        \App\Models\Duo\Capability::create([
            'name' => 'push'
        ]);

        \App\Models\Duo\Capability::create([
            'name' => 'phone'
        ]);

        \App\Models\Duo\Capability::create([
            'name' => 'sms'
        ]);

        \App\Models\Duo\Capability::create([
            'name' => 'mobile_otp'
        ]);
    }
}
