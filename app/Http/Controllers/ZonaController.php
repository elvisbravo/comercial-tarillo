<?php

namespace App\Http\Controllers;

use App\Zona;
use Illuminate\Http\Request;

class ZonaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('zonas.index');
    }

    public function listado()
    {
        $zonas = Zona::all();
        return response()->json($zonas);
    }

    public function crear(Request $request)
    {
        $this->validate($request, [
            'nomb_zona' => 'required|string|max:255',
        ]);

        $zona = new Zona;
        $zona->nomb_zona = $request->nomb_zona;
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
        ]);

        $zona = Zona::find($request->id);
        $zona->nomb_zona = $request->nomb_zona;
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
