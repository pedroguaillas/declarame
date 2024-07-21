<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function searchCompany(string $identification)
    {
        // $result = [];

        // $response = Http::get('https://srienlinea.sri.gob.ec/sri-catastro-sujeto-servicio-internet/rest/Persona/obtenerPorTipoIdentificacion', [
        //     'numeroIdentificacion' => $identification,
        //     'tipoIdentificacion' => 'R'
        // ]);

        // if ($response->getStatusCode() === 200) {
        //     // Ajustar la respuesta
        //     $result = [
        //         'name' => trim($response),
        //         'data' => $response->json(),
        //         'header' => $response->headers()
        //     ];
        // }
        // return $result;

        $response = Http::get('https://srienlinea.sri.gob.ec/sri-catastro-sujeto-servicio-internet/rest/ConsolidadoContribuyente/obtenerPorNumerosRuc?&ruc=0201605011001');
        // $response = Http::get('https://srienlinea.sri.gob.ec/sri-en-linea/SriRucWeb/ConsultaRuc/Consultas/consultaRuc');

        if ($response->ok()) {
            // Ajustar la respuesta
            return $response->headers();
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        //
    }
}
