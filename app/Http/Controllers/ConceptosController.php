<?php

namespace App\Http\Controllers;

use App\Conceptos;
use Illuminate\Http\Request;

class ConceptosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {

        $this->middleware('auth');

    }


    public function index()
    {
        return view('conceptos.index');
    }

    public function listado(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->get('search')['value'];

        $query = Conceptos::where('estado', '=', 1);

        $totalRecords = $query->count();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('descripcion', 'ilike', "%$search%")
                  ->orWhere('tipo_movimiento', 'ilike', "%$search%");
            });
        }

        $filteredRecords = $query->count();

        // Ordenamiento
        $orderColumnIndex = $request->get('order')[0]['column'] ?? 0;
        $orderDir = $request->get('order')[0]['dir'] ?? 'desc';
        $columns = ['id', 'descripcion', 'tipo_movimiento'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';

        $data = $query->orderBy($orderColumn, $orderDir)
                      ->offset($start)
                      ->limit($length)
                      ->get();

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    public function guardar(Request $request)
    {
        if ($request->idconcepto == 0) {
            $concepto = new Conceptos;

            $concepto->descripcion = $request->descripcion;
            $concepto->tipo_movimiento = $request->tipo_movimiento;
            $concepto->estado = 1;

            $concepto->save();

            $json = array(
                "respuesta" => "ok",
                "mensaje" => "Se agrego correctamente"
            );

            return response()->json($json);
        } else {
            $concepto = Conceptos::find($request->idconcepto);

            $concepto->descripcion = $request->descripcion;
            $concepto->tipo_movimiento = $request->tipo_movimiento;
            $concepto->estado = 1;

            $concepto->save();

            $json = array(
                "respuesta" => "ok",
                "mensaje" => "Se edito correctamente"
            );

            return response()->json($json);
        }

    }

    public function filtrar($tipo)
    {
        $filtrado = Conceptos::where('tipo_movimiento','=',$tipo)->where('estado','=',1)->get();

        return response()->json($filtrado);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('conceptos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Conceptos  $conceptos
     * @return \Illuminate\Http\Response
     */
    public function show(Conceptos $conceptos)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Conceptos  $conceptos
     * @return \Illuminate\Http\Response
     */
    public function edit(Conceptos $conceptos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Conceptos  $conceptos
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Conceptos $conceptos)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Conceptos  $conceptos
     * @return \Illuminate\Http\Response
     */
    public function destroy(Conceptos $conceptos)
    {
        //
    }
}
