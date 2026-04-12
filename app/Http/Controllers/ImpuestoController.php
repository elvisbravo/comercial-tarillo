<?php

namespace App\Http\Controllers;

use App\Impuesto;
use App\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImpuestoController extends Controller
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
        return view('impuestos.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function listadoimpuesto(){

        $impuesto = DB::table('impuesto')
            ->join('empresas', 'impuesto.empresa_id', '=', 'empresas.id')
            ->select('impuesto.*', 'empresas.nombre_comercial')
            ->get();
        

        return response()->json($impuesto);


        
    }
    public function listadoempresas(){

        $empresas = DB::table('empresas')->get();

        return response()->json($empresas);

    }
    public function crear(Request $request)
    {
        //
        $this->validate($request,[
            'tipo_impuesto'=>'required'

        ]);

        $impuesto=Impuesto::create($request->all());

        return response()->json('OK');

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Impuesto  $impuestos
     * @return \Illuminate\Http\Response
     */
    public function editarimpuesto($id)
    {
        //

        $impuesto = Impuesto::findOrFail($id);
        return response()->json($impuesto);
    }

   /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Impuesto  $impuestos
     * @return \Illuminate\Http\Response
     */
    public function modificar(Request $request)
    {
        //
        $this->validate($request,[
            'tipo_impuesto'=>'required'

        ]);

        $impuesto = Impuesto::find($request->id);
        $impuesto->impuesto=$request->impuesto;
        $impuesto->tipo_impuesto=$request->tipo_impuesto;
        $impuesto->etiqueta_factura=$request->etiqueta_factura;
        $impuesto->empresa_id=$request->empresa_id;
        $impuesto->save();

        return response()->json('OK');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Impuesto  $marcas
     * @return \Illuminate\Http\Response
     */
    public function eliminar($id)
    {
        //
        Impuesto::find($id)->delete();
        return response()->json('OK');
    }
}
