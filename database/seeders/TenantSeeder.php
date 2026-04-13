<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\Tenant\User as TenantUser;
use App\Models\User;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::create(['id' => 'acme']);

        $tenant->domains()->create(['domain' => 'acme.localhost']);

        $centralUser = User::factory()->create([
            'name' => 'Acme User',
            'email' => 'user@acme.test',
            'username' => 'acme_user',
            'tenant_id' => $tenant->id,
            'is_active' => true,
        ]);

        $tenant->run(function () use ($centralUser): void {
            TenantUser::create([
                'name' => $centralUser->name,
                'email' => $centralUser->email,
                'username' => $centralUser->username,
                'central_user_id' => $centralUser->id,
                'is_active' => true,
            ]);
        });
    }
}
