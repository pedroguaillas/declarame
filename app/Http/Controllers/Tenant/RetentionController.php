<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Retention;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RetentionController extends Controller
{
    public function index(): Response
    {
        $retentions = Retention::orderBy('code')->paginate(50);

        return Inertia::render('Retentions/Index', [
            'retentions' => $retentions,
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:dat,xml', 'max:5120'],
        ]);

        $content = file_get_contents($request->file('file')->getRealPath());

        $xml = simplexml_load_string($content);

        if ($xml === false) {
            return back()->withErrors(['file' => 'El archivo no tiene un formato XML válido.']);
        }

        $ns = 'http://tempuri.org/DsAts.xsd';
        $nodes = $xml->children($ns)->DtRetenciones ?? $xml->DtRetenciones;

        /** @var array<int, array{code: string, type: string, description: string, percentage: float}> $rows */
        $rows = [];

        foreach ($nodes as $node) {
            $retencion = trim((string) $node->retencion);

            if (! is_numeric($retencion)) {
                continue;
            }

            $rows[] = [
                'code' => trim((string) $node->code),
                'type' => 'renta',
                'description' => trim((string) $node->concepto),
                'percentage' => (float) $retencion,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Retention::query()->truncate();

        foreach (array_chunk($rows, 100) as $chunk) {
            Retention::insert($chunk);
        }

        return redirect()->route('tenant.retentions.index')
            ->with('success', 'Retenciones importadas correctamente.');
    }
}
