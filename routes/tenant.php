<?php

declare(strict_types=1);

use App\Http\Controllers\SsoController;
use App\Http\Controllers\Tenant\AccountController;
use App\Http\Controllers\Tenant\CompanyController;
use App\Http\Controllers\Tenant\CompanyScopeController;
use App\Http\Controllers\Tenant\ContactController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\OrderController;
use App\Http\Controllers\Tenant\RetentionController;
use App\Http\Controllers\Tenant\ShopController;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    Route::get('/auth/sso', [SsoController::class, 'handle'])->name('tenant.sso');

    Route::middleware(['auth:tenant'])->group(function () {

        Route::get('/dashboard', DashboardController::class)->name('tenant.dashboard');

        Route::resource('companies', CompanyController::class)
            ->except(['show'])
            ->names([
                'index' => 'tenant.companies.index',
                'create' => 'tenant.companies.create',
                'store' => 'tenant.companies.store',
                'edit' => 'tenant.companies.edit',
                'update' => 'tenant.companies.update',
                'destroy' => 'tenant.companies.destroy',
            ]);

        Route::get('companies/resolve/{identification}', [CompanyController::class, 'resolve'])->name('tenant.companies.resolve');

        Route::get('contacts/resolve/{identification}', [ContactController::class, 'resolve'])->name('tenant.contacts.resolve');

        Route::post('shops/import', [ShopController::class, 'import'])->name('tenant.shops.import');

        Route::resource('shops', ShopController::class)
            ->except(['show'])
            ->names([
                'index' => 'tenant.shops.index',
                'create' => 'tenant.shops.create',
                'store' => 'tenant.shops.store',
                'edit' => 'tenant.shops.edit',
                'update' => 'tenant.shops.update',
                'destroy' => 'tenant.shops.destroy',
            ]);

        Route::post('shops/{shop}/retention', [ShopController::class, 'storeRetention'])
            ->name('tenant.shops.retention.store');

        Route::patch('shops/{shop}/account', [ShopController::class, 'updateAccount'])
            ->name('tenant.shops.account.update');

        Route::post('orders/import', [OrderController::class, 'import'])->name('tenant.orders.import');

        Route::resource('orders', OrderController::class)
            ->except(['show'])
            ->names([
                'index' => 'tenant.orders.index',
                'create' => 'tenant.orders.create',
                'store' => 'tenant.orders.store',
                'edit' => 'tenant.orders.edit',
                'update' => 'tenant.orders.update',
                'destroy' => 'tenant.orders.destroy',
            ]);

        Route::post('orders/{order}/retention', [OrderController::class, 'storeRetention'])
            ->name('tenant.orders.retention.store');

        Route::get('accounts', [AccountController::class, 'index'])->name('tenant.accounts.index');
        Route::post('accounts/import', [AccountController::class, 'import'])->name('tenant.accounts.import');

        Route::get('retentions', [RetentionController::class, 'index'])->name('tenant.retentions.index');
        Route::post('retentions/import', [RetentionController::class, 'import'])->name('tenant.retentions.import');

        Route::post('/company-scope', [CompanyScopeController::class, 'store'])
            ->name('tenant.company-scope.store');

        Route::post('/logout', function (Request $request) {
            app(AuthService::class)->logout($request, 'tenant');

            return redirect()->away(config('app.url').'/login');
        })->name('tenant.logout');

    });

});
