<?php

namespace App\Http\Controllers;

use App\Acciones;
use Illuminate\Http\Request;

class AccionesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('acciones.index');
    }

    public function getList()
    {
        $acciones = Acciones::where('estado', true)->get();
        return response()->json($acciones);
    }

    public function getById($id)
    {
        $acciones = Acciones::find($id);
        return response()->json($acciones);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'nombre' => 'required'
        ]);

        $accion = new Acciones();
        $accion->nombre = $request->nombre;
        $accion->estado = true;
        $accion->save();

        return response()->json('OK');
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'nombre' => 'required'
        ]);

        $accion = Acciones::find($request->id);
        $accion->nombre = $request->nombre;
        $accion->save();

        return response()->json('OK');
    }

    public function destroy($id)
    {
        $accion = Acciones::find($id);
        if ($accion) {
            $accion->estado = false;
            $accion->save();
            return response()->json('Ok');
        }
        return response()->json('Error', 404);
    }
}
