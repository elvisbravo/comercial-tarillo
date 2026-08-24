<?php

namespace App\Http\Controllers;

use App\Zona;
use App\Sede;
use Illuminate\Http\Request;

class ZonaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $sedes = Sede::where('estado', 1)->where('nombre', 'not ilike', 'todos')->get();
        return view('zonas.index', compact('sedes'));
    }

    public function listado()
    {
        $zonas = Zona::with('sede')->get();
        return response()->json($zonas);
    }

    public function crear(Request $request)
    {
        $this->validate($request, [
            'nomb_zona' => 'required|string|max:255',
            'sede_id' => 'required|exists:sedes,id',
        ]);

        $zona = new Zona;
        $zona->nomb_zona = $request->nomb_zona;
        $zona->sede_id = $request->sede_id;
        $zona->estado = 'ACTIVO';
        $zona->save();

        return response()->json('OK');
    }

    public function edit($id)
    {
        $zona = Zona::find($id);

        return response()->json($zona);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'nomb_zona' => 'required|string|max:255',
            'sede_id' => 'required|exists:sedes,id',
        ]);

        $zona = Zona::find($request->id);
        $zona->nomb_zona = $request->nomb_zona;
        $zona->sede_id = $request->sede_id;
        $zona->save();

        return response()->json('OK');
    }

    public function eliminar($id)
    {
        $zona = Zona::find($id);
        $zona->estado = 'INACTIVO';
        $zona->save();
        return response()->json('OK');
    }
}
