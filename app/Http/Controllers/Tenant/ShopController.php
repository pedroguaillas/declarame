<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreShopRequest;
use App\Http\Requests\Tenant\UpdateShopRequest;
use App\Models\Tenant\Account;
use App\Models\Tenant\Company;
use App\Models\Tenant\Retention;
use App\Models\Tenant\Shop;
use App\Services\ShopImportService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShopController extends Controller
{
    public function index(): Response
    {
        $shops = Shop::with(['company:id,name', 'contact:id,name', 'retentionItems.retention', 'account:id,code,name'])
            ->when(session('current_company_id'), fn ($q) => $q->where('company_id', session('current_company_id')))
            ->orderByDesc('emision')
            ->paginate(50, [
                'id', 'acount_id', 'company_id', 'contact_id', 'serie', 'emision', 'total', 'sub_total',
                'state', 'serie_retention', 'date_retention', 'state_retention', 'autorization_retention',
            ]);

        $isRetentionAgent = (bool) Company::find(session('current_company_id'))?->retention_agent;

        return Inertia::render('Shops/Index', [
            'shops' => $shops,
            'retentions' => Retention::orderBy('code')->get(['id', 'code', 'type', 'description', 'percentage']),
            'accounts' => $this->expenseAccounts(),
            'isRetentionAgent' => $isRetentionAgent,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Shops/Create', [
            'voucherTypes' => VoucherType::whereIn('code', ['01', '02', '03', '04', '05'])->get(),
            'accounts' => $this->expenseAccounts(),
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
            'voucherTypes' => VoucherType::whereIn('code', ['01', '02', '03', '04', '05'])->get(),
            'accounts' => $this->expenseAccounts(),
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

    /** @return Collection<int, Account> */
    private function expenseAccounts(): Collection
    {
        return Account::where('code', 'like', '5%')
            ->where('is_detail', true)
            ->orderBy('code')
            ->get(['id', 'code', 'name']);
    }

    public function import(Request $request, ShopImportService $service): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:5120'],
        ]);

        $content = file_get_contents($request->file('file')->getRealPath());

        ['imported' => $imported, 'skipped' => $skipped] = $service->import($content, session('current_company_id'));

        return redirect()->route('tenant.shops.index')
            ->with('success', "Importación completada: {$imported} compras importadas, {$skipped} omitidas.");
    }

    public function updateAccount(Request $request, Shop $shop): RedirectResponse
    {
        $request->validate([
            'acount_id' => ['nullable', 'integer', 'exists:acounts,id'],
        ]);

        $shop->update(['acount_id' => $request->acount_id]);

        return redirect()->route('tenant.shops.index')
            ->with('success', 'Cuenta contable asignada correctamente.');
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
