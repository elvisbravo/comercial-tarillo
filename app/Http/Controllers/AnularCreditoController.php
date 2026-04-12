<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DB;
use App\Cuotas;
use App\Creditos;
use Illuminate\Support\Facades\Auth;

class AnularCreditoController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth');


    }

    public function index()
    {
        //

       return view('anular-credito.index');

    }

    //METODO PARA VALIDAR SI EL CREDITO TIENE ALGUNA CUOTA COBRADA

    public function verificardorcuota($id){

            $cuotas=Cuotas::where('credito_id','=',$id)->get();
            $repuesta=0;

             foreach($cuotas as $c){

                 if($c->esta_cuo=='COBRADA'){
                     $repuesta=1;
                     break;
                 }


             }

             return response()->json($repuesta);

    }

    public function anular(Request $request){

        DB::beginTransaction();
        try{

        $creditos=Creditos::with('Detalle')
        ->where('id','=',$request->id)
        ->get();

        $user = Auth::user();

        $idsede = session('key')->sede_id;

         for($i=0;$i<count($creditos);$i++){

              $data=Creditos::where('id','=',$creditos[$i]->id)->first();
              $data->obse_cre=$request->obse_cre." Fecha: ".Date('Y-m-d')." Usuario: ".$user->name." Codigo Sede".$idsede;
              $data->f_anulacion=Date('Y-m-d');
              $data->esta_cre=0;
              $data->save();

              foreach($creditos[$i]["Detalle"] as $de){

                     $cuota=Cuotas::where('id','=',$de->id)->first();
                     $cuota->esta_cuo='ANULADA';
                     $cuota->save();

              }

         }

         DB::commit();

         return response()->json('ok');

        }catch (Exception $e) {

            return  response()->json($e);

        }





    }



}
