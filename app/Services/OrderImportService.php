<?php

namespace App\Services;

use App\Models\Tenant\Contact;
use App\Models\Tenant\Order;
use App\Models\Tenant\VoucherType;
use Carbon\Carbon;

class OrderImportService
{
    /** @var array<string, string> */
    private array $voucherTypeMap = [
        'factura' => '01',
        'nota de crédito' => '04',
        'nota de débito' => '05',
    ];

    /**
     * @return array{imported: int, skipped: int}
     */
    public function import(string $content, int $companyId): array
    {
        if (! mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'ISO-8859-1');
        }

        $lines = preg_split('/\r?\n/', $content);

        $consumidorFinal = Contact::firstOrCreate(
            ['identification' => '9999999999999'],
            ['name' => 'Consumidor Final'],
        );

        $imported = 0;
        $skipped = 0;

        foreach (array_slice($lines, 1) as $line) {
            $line = trim($line);

            if (empty($line)) {
                continue;
            }

            $cols = explode("\t", $line);

            if (count($cols) < 8) {
                continue;
            }

            [$tipoComprobante, $serie, $claveAcceso, $fechaAutorizacion,
                $fechaEmision, $valorSinImpuestos, $iva, $total] = $cols;

            $claveAcceso = trim($claveAcceso);

            if (strlen($claveAcceso) === 49 && Order::where('autorization', $claveAcceso)->exists()) {
                $skipped++;

                continue;
            }

            $voucherType = $this->resolveVoucherType($tipoComprobante);

            if (! $voucherType) {
                $skipped++;

                continue;
            }

            $subTotal = (float) $valorSinImpuestos;
            $ivaAmount = (float) $iva;
            $totalAmount = (float) $total;
            [$base0, $base5, $base8, $base12, $base15, $iva5, $iva8, $iva12, $iva15] = $this->distributeIva($subTotal, $ivaAmount);

            Order::create([
                'company_id' => $companyId,
                'contact_id' => $consumidorFinal->id,
                'voucher_type_id' => $voucherType->id,
                'emision' => Carbon::createFromFormat('d/m/Y H:i:s', trim($fechaEmision))->format('Y-m-d'),
                'autorization' => $claveAcceso,
                'autorized_at' => Carbon::createFromFormat('d/m/Y H:i:s', trim($fechaAutorizacion))->format('Y-m-d H:i:s'),
                'serie' => trim($serie),
                'sub_total' => $subTotal,
                'base0' => $base0,
                'base5' => $base5,
                'base8' => $base8,
                'base12' => $base12,
                'base15' => $base15,
                'iva5' => $iva5,
                'iva8' => $iva8,
                'iva12' => $iva12,
                'iva15' => $iva15,
                'total' => $totalAmount,
                'state' => 'AUTORIZADO',
            ]);

            $imported++;
        }

        return ['imported' => $imported, 'skipped' => $skipped];
    }

    private function resolveVoucherType(string $label): ?VoucherType
    {
        $code = $this->voucherTypeMap[mb_strtolower(trim($label))] ?? null;

        return $code ? VoucherType::where('code', $code)->first() : null;
    }

    /**
     * Distribute sub_total and IVA amount into the appropriate tax-rate buckets.
     *
     * @return array{float, float, float, float, float, float, float, float, float}
     */
    private function distributeIva(float $subTotal, float $ivaAmount): array
    {
        if ($ivaAmount <= 0 || $subTotal <= 0) {
            return [$subTotal, 0, 0, 0, 0, 0, 0, 0, 0];
        }

        $ratio = $ivaAmount / $subTotal;
        $rates = [5 => 0.05, 8 => 0.08, 12 => 0.12, 15 => 0.15];
        $closest = 15;
        $minDiff = PHP_FLOAT_MAX;

        foreach ($rates as $rate => $value) {
            $diff = abs($ratio - $value);

            if ($diff < $minDiff) {
                $minDiff = $diff;
                $closest = $rate;
            }
        }

        return match ($closest) {
            5 => [0, $subTotal, 0, 0, 0, $ivaAmount, 0, 0, 0],
            8 => [0, 0, $subTotal, 0, 0, 0, $ivaAmount, 0, 0],
            12 => [0, 0, 0, $subTotal, 0, 0, 0, $ivaAmount, 0],
            default => [0, 0, 0, 0, $subTotal, 0, 0, 0, $ivaAmount],
        };
    }
}
