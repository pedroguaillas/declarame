<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\Tenant\ContributorType;
use App\Models\Tenant\User as TenantUser;
use App\Models\Tenant\VoucherType;
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
            'username' => 'peters',
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
            ContributorType::insert([
                ['description' => 'GENERAL'],
                ['description' => 'RIMPE EMPRENDEDOR'],
                ['description' => 'RIMPE NEGOCIO POPULAR'],
            ]);
            VoucherType::insert([
                ['code' => '01', 'description' => 'Factura'],
                ['code' => '02', 'description' => 'Nota de Venta'],
                ['code' => '03', 'description' => 'Liquidación de Compras'],
                ['code' => '04', 'description' => 'Nota de Crédito'],
                ['code' => '05', 'description' => 'Nota de Débito'],
                ['code' => '06', 'description' => 'Guía de Remisión'],
                ['code' => '07', 'description' => 'Comprobante de Retención'],
            ]);
        });
    }
}
