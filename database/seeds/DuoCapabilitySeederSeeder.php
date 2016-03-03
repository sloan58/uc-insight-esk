<?php

use Illuminate\Database\Seeder;

// composer require laracasts/testdummy
use Laracasts\TestDummy\Factory as TestDummy;

class DuoCapabilitySeeder extends Seeder
{
    public function run()
    {
        \App\Models\DuoCapability::create([
            'name' => 'push'
        ]);

        \App\Models\DuoCapability::create([
            'name' => 'phone'
        ]);

        \App\Models\DuoCapability::create([
            'name' => 'sms'
        ]);

        \App\Models\DuoCapability::create([
            'name' => 'mobile_otp'
        ]);
    }
}
