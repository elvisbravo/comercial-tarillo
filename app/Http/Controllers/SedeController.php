<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use App\Sede;
use App\Empresa;
use App\Almacen;
use App\Tipo_comprobantes;
use App\Correlativos;
use Illuminate\Http\Request;

class SedeController extends Controller
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


    public function index(Request $request)
    {
        $usuario = $request->session()->get('key');

        if($usuario->sede_id==1){

            $sedes = Sede::where('id','!=',1)->get();
            $comprobantes = Tipo_comprobantes::all();

            return view('sedes.index',compact('sedes','comprobantes'));

        }else{

            return redirect()->route('home');
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sedes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'nombre' => 'required',
            'direccion' => 'required',
            'telefono' => 'required',
            'anexo' => 'required',
            'principal' => 'required',
        ]);

        $empresa = Empresa::first();

        $sede = new Sede;
        $sede->nombre = $request->nombre;
        $sede->direccion = $request->direccion;
        $sede->telefono = $request->telefono;
        $sede->anexo = $request->anexo;
        $sede->sede_principal = $request->principal;
        $sede->estado = 1;
        $sede->logo_sede = "no hay";
        $sede->tipo_envio = 0;
        $sede->empresa_id = $empresa['id'];
        $sede->save();

        $consulta_idsede = Sede::orderBy('id','asc')->get();
        $ultimo_id_sede = $consulta_idsede->last();

        $almacen = new Almacen;

        $almacen->nombre = $request->nombre;
        $almacen->direccion = $request->direccion;
        $almacen->estado = 1;
        $almacen->sede_id = $ultimo_id_sede['id'];

        $almacen->save();

        return redirect()->route('sedes.index')
                        ->with('success','Sede created successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Sede  $sede
     * @return \Illuminate\Http\Response
     */
    public function show(Sede $sede)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Sede  $sede
     * @return \Illuminate\Http\Response
     */
    public function edit(Sede $sede)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Sede  $sede
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sede $sede)
    {
        //
    }

    public function update_envio(Request $request)
    {
        $sede= Sede::find($request->idsede);
        $sede->tipo_envio = $request->envio;

        $sede->save();

        return response()->json($request);
    }

    public function update_estado(Request $request)
    {
        $sede= Sede::find($request->idsede);
        $sede->estado = $request->estado;

        $sede->save();

        return response()->json($request);
    }

    public function correlativos($id)
    {
        $correlativos = DB::table('correlativos as co')->select('co.id','co.serie','co.correlativo','co.sede_id','co.tipo_comprobante_id','co.tipo_envio','tc.descripcion')->join('tipo_comprobantes as tc','co.tipo_comprobante_id','=','tc.id')->where('co.sede_id','=',$id)->get();

        echo json_encode($correlativos);
    }

    public function select_comprobante(Request $request)
    {
        $correlativos = Correlativos::where('sede_id','=',$request->idsede)->where('tipo_comprobante_id','=',$request->comprobante)->get();

        if (count($correlativos) === 0) {
            $comp = Tipo_comprobantes::find($request->comprobante);

            $json = array(
                "respuesta" => "ok",
                "mensaje" => "si puede agregar",
                "comprobante" => $comp['descripcion'],
                "idcomprobante" => $comp['id']
            );
        } else {
            $json = array(
                "respuesta" => "existe",
                "mensaje" => "ya existe dicho comprobante"
            );

        }

        return response()->json($json);

    }

    public function guardar_correlativos(Request $request)
    {
        $serie_prueba = $request->serie_prueba;
        $serie_produccion = $request->serie_produccion;
        $correlativo_prueba = $request->correlativo_prueba;
        $correlativo_produccion = $request->correlativo_produccion;
        $comprobantes = $request->tipocomprobante;

        $lenght = count($comprobantes);

        for ($i=0; $i < $lenght; $i++) {
            $correlativo = new Correlativos;
            $correlativo->serie = $serie_prueba[$i];
            $correlativo->correlativo = $correlativo_prueba[$i];
            $correlativo->sede_id = $request->idsede;
            $correlativo->tipo_comprobante_id = $comprobantes[$i];
            $correlativo->tipo_envio = 0;
            $correlativo->save();
        }

        for ($i=0; $i < $lenght; $i++) {
            $correlativo = new Correlativos;
            $correlativo->serie = $serie_produccion[$i];
            $correlativo->correlativo = $correlativo_produccion[$i];
            $correlativo->sede_id = $request->idsede;
            $correlativo->tipo_comprobante_id = $comprobantes[$i];
            $correlativo->tipo_envio = 1;
            $correlativo->save();
        }

        return response()->json("ok");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Sede  $sede
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sede $sede)
    {
        //
    }
}
