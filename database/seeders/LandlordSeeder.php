<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class LandlordSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Landlord Admin',
            'email' => 'admin@declarame.test',
            'username' => 'admin',
            'is_active' => true,
        ]);
    }
}
