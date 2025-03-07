<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        // Create or update the system PIN
        Setting::updateOrCreate(
            ['key' => 'system_pin'],
            ['value' => Hash::make('000000')] 
        );
    }
}