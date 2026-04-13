<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreShopRequest;
use App\Http\Requests\Tenant\UpdateShopRequest;
use App\Models\Tenant\Contact;
use App\Models\Tenant\Shop;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ShopController extends Controller
{
    public function index(): Response
    {
        $shops = Shop::with(['company:id,name', 'contact:id,name'])
            ->when(session('current_company_id'), fn ($q) => $q->where('company_id', session('current_company_id')))
            ->orderByDesc('emision')
            ->get();

        return Inertia::render('Shops/Index', [
            'shops' => $shops,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Shops/Create', [
            'contacts' => Contact::orderBy('name')->get(['id', 'name']),
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
        return Inertia::render('Shops/Edit', [
            'shop' => $shop,
            'contacts' => Contact::orderBy('name')->get(['id', 'name']),
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
}
