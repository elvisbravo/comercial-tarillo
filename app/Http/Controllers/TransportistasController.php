<?php

namespace App\Http\Controllers;

Use App\Transportista;
use App\Tipo_documento;
use Illuminate\Http\Request;
use DB;

class TransportistasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');


    }

    public function index(){

        $tipo_documento = Tipo_documento::all();
        return view('transportistas.index',compact('tipo_documento'));
    }

    public function liestadotransportistas()
    {
        //
        $transportistas=Transportista::where('estado', '=',1)->get();
        return response()->json($transportistas);

    }

    public function crear(Request $request)
    {
        //return response()->json($empresa);

        //print_r("");exit();

        $transportistas = new Transportista;
        
        $transportistas->ruc=$request->ruc;
        $transportistas->razon_social=$request->razon_social;
        $transportistas->nombre_comercial=$request->nombre_comercial;
        $transportistas->telefono=$request->telefono;
        $transportistas->direccion=$request->direccion;
        $transportistas->documento_identidad=$request->documento_identidad;
        $transportistas->estado=1;
        $transportistas->save();

        return response()->json('OK');


    }

    public function editar($id)
    {
        //
        $transportistas= Transportista::find($id);
        return response()->json($transportistas);

    }

    public function update(Request $request)
    {
        //
        $this->validate($request,[
            'ruc'=>'required',
            'razon_social'=>'required',
            'nombre_comercial'=>'required',

        ]);

        $transportistas= Transportista::find($request->id);
        $transportistas->ruc=$request->ruc;
        $transportistas->razon_social=$request->razon_social;
        $transportistas->nombre_comercial=$request->nombre_comercial;
        $transportistas->telefono=$request->telefono;
        $transportistas->direccion=$request->direccion;
        $transportistas->estado=1;
        $transportistas->documento_identidad=$request->documento_identidad;
        $transportistas->save();

        return response()->json('OK');

    }


    public function eliminar($id)
    {
        //
        //var_dump($id); die;
        $transportistas= Transportista::find($id);
        $transportistas->estado=0;
        $transportistas->save();

        return response()->json('OK');

    }
}
