<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DB;
use App\Recibos;
use Illuminate\Support\Facades\Date;
use App\Amortizaciones;
use App\Cuotas;
use App\Creditos;
use Illuminate\Support\Facades\Auth;

class AnularAmortizacionController extends Controller
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
        return view('anular-amortizaciones.index');
    }

    public function recibo($id){

        $recibo=Recibos::with('Amortizaciones')
        ->where('id','=',$id)
        ->where('esta_rec','=','EMITIDO')
        ->get();

        return response()->json($recibo);



    }

    public function anular(Request $request){

        DB::beginTransaction();

        try{

            $recibos=Recibos::with('Amortizaciones')
            ->where('id','=',$request->id)
            ->get();


             $user = Auth::user();
             $idsede = session('key')->sede_id;
             for($i=0;$i<count($recibos);$i++){

                $recibo=Recibos::where('id','=',$recibos[$i]->id)->first();
                $recibo->esta_rec='ANULADO';
                $recibo->f_anulacion=Date('Y-m-d');
                $recibo->obse_rec=$request->obse_rec." Fecha: ".Date('Y-m-d')." Usuario: ".$user->name." Codigo Sede".$idsede;
                $recibo->usuario_anulo=$user->name;
                $recibo->mont_rec=(-1)*$recibo->mont_rec;
                $recibo->save();

                foreach($recibos[$i]["Amortizaciones"] as $de){

                    $amortizaciones=Amortizaciones::where('id','=',$de->id)->first();
                    $cuota=Cuotas::where('id','=',$amortizaciones->cuota_id)->first();
                    $validar_cuota=$cuota->mont_cuo-$amortizaciones->mont_amo;

                    if( $validar_cuota==0){

                         $cuota->saldo_cuo=$amortizaciones->mont_amo;
                         $cuota->sald_cap=$amortizaciones->mont_amo;

                    }else{

                        $cuota->saldo_cuo= $cuota->saldo_cuo+$amortizaciones->mont_amo;
                        $cuota->sald_cap=$cuota->sald_cap+$amortizaciones->mont_amo;
                    }

                     $cuota->esta_cuo='PENDIENTE';
                     $cuota->save();

                    if( $this->validar_estado_credito($cuota->credito_id)==0){

                         $credito=Creditos::where('id','=',$cuota->credito_id)->first();
                         $credito->esta_cre='1';
                         $credito->save();



                    }




             }





             }




            DB::commit();

            return response()->json('OK');


           }catch (Exception $e) {

               return  response()->json($e);

           }


    }

    //FUNCIONA PARA VALIDAR EL ESTADO DEL CREDITO Y PODER ACTIVARLO


        public function validar_estado_credito($id){

            $respuesta=0;
            $credito=Creditos::find($id);

            if($credito->esta_cre=='2'){

                $respuesta=0;

            }else{

                $respuesta=1;

            }

            return $respuesta;

        }



}


