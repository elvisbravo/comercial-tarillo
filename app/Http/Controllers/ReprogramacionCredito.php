<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Concepto_credito;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Cuotas;
use App\candado;
use App\Clientes;
use App\Empresa;
use PDF;
use App\User;
use App\Creditos;
use DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReprogramacionCredito extends Controller
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
        $conceptos=Concepto_credito::where('estado','=',1)->get();

        return view('creditos-reprogramacion.index',compact('conceptos'));
    }

    public function cuotas_activas($id_credito){

         
        $cuotas=Cuotas::where('credito_id','=',$id_credito)
        ->where('esta_cuo','=','PENDIENTE')
        ->get();

        return response()->json($cuotas);


    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function crear(Request $request)
    {
        //
        DB::beginTransaction();

        try{

            $resultado=$request->all();
            $user = Auth::user();
            $idsede = session('key')->sede_id;

           $borrado=$this->modificar_cuotas_activas($resultado[0]["credito_id"]);
           $ultima=$this->ultima_cuota($resultado[0]["credito_id"]);


           


            for($i=1; $i <count($resultado) ; $i++){


                $cuota=new Cuotas;
                $cuota->mont_cuo=$resultado[$i]["mont_cuo"];
                $cuota->fven_cuo=Carbon::parse($resultado[$i]["fven_cuo"])->format('Y-m-d');
                $cuota->saldo_cuo=$resultado[$i]["mont_cuo"];
                $cuota->capi_cuo=$resultado[$i]["mont_cuo"];
                $cuota->credito_id= $resultado[0]["credito_id"];
                $cuota->esta_cuo= 'PENDIENTE';
                $cuota->numero_cuo=$ultima + $i;
                $cuota->sald_cap= $resultado[$i]["mont_cuo"];
                $cuota->version=1;
                $cuota->save();



            }

            DB::commit();

            return response()->json($ultima);





         }catch (Exception $e) {

            return  response()->json($e);

        }
    }


    public function modificar_cuotas_activas($credito){

        //PENDIENTE
        //REPROGRAMADO

        $cuotas=Cuotas::where('credito_id','=',$credito)
        ->where('esta_cuo','=','PENDIENTE')
        ->get();

        foreach($cuotas as $c){

             $cuota=Cuotas::where('id','=',$c->id)->first();
             $cuota->esta_cuo='REPROGRAMADA';
             $cuota->saldo_cuo= 0;
             $cuota->capi_cuo=0;
             $cuota->save();
        }

        return 'OK';
      

    }

    //CAPTURAR LA ULTIMA CUOTA ACTIVA DEL CREDITO

    public function ultima_cuota($credito){

        $cuotas=Cuotas::where('credito_id','=',$credito)
        //->where('esta_cuo','=','REPROGRAMADO')
        ->orderBy('numero_cuo','DESC')
        ->get();

         
        return $cuotas[0]["numero_cuo"];





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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
