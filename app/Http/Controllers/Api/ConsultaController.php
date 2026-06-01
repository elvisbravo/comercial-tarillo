<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\servicios\FuncionesController;
use App\Clientes;
use Illuminate\Http\Request;

class ConsultaController extends Controller
{
    /**
     * POST /api/consultar-dni-ruc
     * Body: { "tipo_documento": 1|6, "num_doc": "..." }
     *  - 1 = DNI
     *  - 6 = RUC
     *
     * Si el cliente ya existe en la BD local, devuelve sus datos ("existe_base_datos").
     * Si no, consulta la API externa de facturalahoy.com y normaliza el resultado.
     */
    public function consultarDniRuc(Request $request)
    {
        $tipo  = (int) $request->input('tipo_documento');
        $numero = trim((string) $request->input('num_doc', ''));

        if ($numero === '') {
            return response()->json([
                'status'  => false,
                'mensaje' => 'Debe enviar el número de documento.',
            ], 422);
        }

        try {
            $existente = Clientes::where('documento', $numero)->first();
            if ($existente) {
                return response()->json([
                    'status'     => true,
                    'origen'     => 'base_datos',
                    'exception'  => 'existe_base_datos',
                    'cliente'    => [
                        'id'           => (int) $existente->id,
                        'documento'    => $existente->documento,
                        'tipo_doc'     => $existente->tipo_doc,
                        'razon_social' => $existente->razon_social,
                        'nombres'      => $existente->nomb_per,
                        'apellidos'    => trim(($existente->pate_per ?? '') . ' ' . ($existente->mate_per ?? '')),
                        'direccion'    => $existente->dire_per,
                        'telefono'     => $existente->telefono,
                        'sector_id'    => (int) $existente->id_sector,
                    ],
                ]);
            }

            $servicios = new FuncionesController;
            $respuesta = $servicios->api_dni_ruc($tipo, $numero);

            return $respuesta;
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }
}
