<?php

namespace App\Http\Controllers;

use App\Proveedor;
use Illuminate\Http\Request;
use App\Empresa;
use DB;
use App\Tipo_documento;
class ProveedorController extends Controller
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
        //
        $tipo_documento = Tipo_documento::all();
        return view('proveedores.index',compact('tipo_documento'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function liestadoproveedores()
    {
        //
        $proveedores=Proveedor::all();
        return response()->json($proveedores);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function crear(Request $request)
    {



        $empresa=DB::table('empresas')
        ->select('id')
        ->first();


        //return response()->json($empresa);

        //print_r("");exit();

        $proveedor = new Proveedor;
        $proveedor->empresa_id=$empresa->id;
        $proveedor->ruc=$request->ruc;
        $proveedor->razon_social=$request->razon_social;
        $proveedor->nombre_comercial=$request->nombre_comercial;
        $proveedor->telefono=$request->telefono;
        $proveedor->direccion=$request->direccion;
        $proveedor->email=$request->email;
        $proveedor->web_sitie=$request->web_sitie;
        $proveedor->estado=1;
        $proveedor->contacto=$request->contacto;
        $proveedor->documento_identidad=$request->documento_identidad;
        $proveedor->save();

        return response()->json('OK');


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Proveedor  $proveedor
     * @return \Illuminate\Http\Response
     */
    public function show(Proveedor $proveedor)
    {
        //
        $proveedores = Proveedor::find($id);
        return view('proveedores.show',compact('proveedores'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Proveedor  $proveedor
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        //
        $proveedor= Proveedor::find($id);
        return response()->json($proveedor);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Proveedor  $proveedor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
        $this->validate($request,[
            'ruc'=>'required',
            'razon_social'=>'required',
            'nombre_comercial'=>'required',

        ]);


        $empresa=DB::table('empresas')
        ->select('id')
        ->first();


        $proveedor= Proveedor::find($request->id);
        $proveedor->empresa_id=$empresa->id;
        $proveedor->ruc=$request->ruc;
        $proveedor->razon_social=$request->razon_social;
        $proveedor->nombre_comercial=$request->nombre_comercial;
        $proveedor->telefono=$request->telefono;
        $proveedor->direccion=$request->direccion;
        $proveedor->email=$request->email;
        $proveedor->web_sitie=$request->web_sitie;
        $proveedor->estado=1;
        $proveedor->contacto=$request->contacto;
        $proveedor->documento_identidad=$request->documento_identidad;
        $proveedor->save();

        return response()->json('OK');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Proveedor  $proveedor
     * @return \Illuminate\Http\Response
     */
    public function eliminar($id)
    {
        //
        $proveedor= Proveedor::find($id);
        $proveedor->estado=0;
        $proveedor->save();

        return response()->json('OK');

    }

    public function activar($id)
    {
        //
        $proveedor= Proveedor::find($id);
        $proveedor->estado=1;
        $proveedor->save();

        return response()->json('OK');

    }


}
