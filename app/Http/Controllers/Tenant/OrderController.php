<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreOrderRequest;
use App\Http\Requests\Tenant\UpdateOrderRequest;
use App\Models\Tenant\Order;
use App\Models\Tenant\Retention;
use App\Services\OrderImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(): Response
    {
        $orders = Order::with(['company:id,name', 'contact:id,name', 'retentionItems.retention'])
            ->when(session('current_company_id'), fn ($q) => $q->where('company_id', session('current_company_id')))
            ->orderByDesc('emision')
            ->paginate(50, [
                'id', 'company_id', 'contact_id', 'serie', 'emision', 'total', 'sub_total',
                'state', 'serie_retention', 'date_retention', 'state_retention', 'autorization_retention',
            ]);

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
            'retentions' => Retention::orderBy('code')->get(['id', 'code', 'type', 'description', 'percentage']),
        ]);
    }

    public function create(): Response
    {
        $voucherTypes = VoucherType::whereIn('code', ['01', '02', '04'])->get();

        return Inertia::render('Orders/Create', [
            'voucherTypes' => $voucherTypes,
        ]);
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        Order::create(array_merge($request->validated(), [
            'company_id' => session('current_company_id'),
        ]));

        return redirect()->route('tenant.orders.index')
            ->with('success', 'Venta registrada correctamente.');
    }

    public function edit(Order $order): Response
    {
        $voucherTypes = VoucherType::whereIn('code', ['01', '02', '04'])->get();

        return Inertia::render('Orders/Edit', [
            'order' => $order,
            'voucherTypes' => $voucherTypes,
        ]);
    }

    public function update(UpdateOrderRequest $request, Order $order): RedirectResponse
    {
        $order->update($request->validated());

        return redirect()->route('tenant.orders.index')
            ->with('success', 'Venta actualizada correctamente.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return redirect()->route('tenant.orders.index')
            ->with('success', 'Venta eliminada correctamente.');
    }

    public function import(Request $request, OrderImportService $service): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:5120'],
        ]);

        $content = file_get_contents($request->file('file')->getRealPath());

        ['imported' => $imported, 'skipped' => $skipped] = $service->import($content, session('current_company_id'));

        return redirect()->route('tenant.orders.index')
            ->with('success', "Importación completada: {$imported} ventas importadas, {$skipped} omitidas.");
    }

    public function storeRetention(Request $request, Order $order): RedirectResponse
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

        $order->update([
            'serie_retention' => $validated['serie_retention'],
            'date_retention' => $validated['date_retention'],
            'autorization_retention' => $validated['autorization_retention'],
            'state_retention' => 'AUTORIZADO',
        ]);

        $order->retentionItems()->delete();

        $order->retentionItems()->createMany($validated['items']);

        return redirect()->route('tenant.orders.index')
            ->with('success', 'Retención registrada correctamente.');
    }
}
