<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreShopRequest;
use App\Http\Requests\Tenant\UpdateShopRequest;
use App\Models\Tenant\Retention;
use App\Models\Tenant\Shop;
use App\Models\Tenant\VoucherType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShopController extends Controller
{
    public function index(): Response
    {
        $shops = Shop::with(['company:id,name', 'contact:id,name', 'retentionItems.retention'])
            ->when(session('current_company_id'), fn ($q) => $q->where('company_id', session('current_company_id')))
            ->orderByDesc('emision')
            ->get([
                'id', 'company_id', 'contact_id', 'serie', 'emision', 'total', 'sub_total',
                'state', 'serie_retention', 'date_retention', 'state_retention', 'autorization_retention',
            ]);

        return Inertia::render('Shops/Index', [
            'shops' => $shops,
            'retentions' => Retention::orderBy('code')->get(['id', 'code', 'type', 'description', 'percentage']),
        ]);
    }

    public function create(): Response
    {
        $voucherTypes = VoucherType::whereIn('code', ['01', '02', '03', '04', '05'])->get();

        return Inertia::render('Shops/Create', [
            'voucherTypes' => $voucherTypes,
        ]);
    }

    public function store(StoreShopRequest $request): RedirectResponse
    {
        Shop::create(array_merge($request->validated(), [
            'company_id' => session('current_company_id'),
        ]));

        return redirect()->route('tenant.shops.index')
            ->with('success', 'Compra registrada correctamente.');
    }

    public function edit(Shop $shop): Response
    {
        $voucherTypes = VoucherType::whereIn('code', ['01', '02', '03', '04', '05'])->get();

        return Inertia::render('Shops/Edit', [
            'shop' => $shop,
            'voucherTypes' => $voucherTypes,
        ]);
    }

    public function update(UpdateShopRequest $request, Shop $shop): RedirectResponse
    {
        $shop->update($request->validated());

        return redirect()->route('tenant.shops.index')
            ->with('success', 'Compra actualizada correctamente.');
    }

    public function destroy(Shop $shop): RedirectResponse
    {
        $shop->delete();

        return redirect()->route('tenant.shops.index')
            ->with('success', 'Compra eliminada correctamente.');
    }

    public function storeRetention(Request $request, Shop $shop): RedirectResponse
    {
        $validated = $request->validate([
            'serie_retention' => ['required', 'string', 'max:17'],
            'date_retention' => ['required', 'date'],
            'autorization_retention' => ['required', 'string', 'max:49'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.retention_id' => ['required', 'integer', 'exists:retentions,id'],
            'items.*.base' => ['required', 'numeric', 'min:0'],
            'items.*.porcentage' => ['required', 'numeric', 'min:0'],
            'items.*.value' => ['required', 'numeric', 'min:0'],
        ]);

        $shop->update([
            'serie_retention' => $validated['serie_retention'],
            'date_retention' => $validated['date_retention'],
            'autorization_retention' => $validated['autorization_retention'],
            'state_retention' => 'AUTORIZADO',
        ]);

        $shop->retentionItems()->delete();

        $shop->retentionItems()->createMany($validated['items']);

        return redirect()->route('tenant.shops.index')
            ->with('success', 'Retención registrada correctamente.');
    }
}
