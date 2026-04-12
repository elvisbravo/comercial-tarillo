<?php

namespace App\Http\Controllers;

use App\Cuentas_bancarias;
use App\Bancos;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CuentasBancariasController extends Controller
{
    function index(){
        return view('cuentasbancarias.index');
    }

    public function listadoCuentasBancarias(){

        $cuentas_bancarias = DB::table('cuentas_bancarias')
            ->join('bancos', 'cuentas_bancarias.banco_id', '=', 'bancos.id')
            ->select('cuentas_bancarias.*', 'bancos.nombre')
            ->get();
        
        //var_dump($cuentas_bancarias); die;

        return response()->json($cuentas_bancarias);
        
    }
    public function listadoBancos(){

        $bancos = DB::table('bancos')->get();

        return response()->json($bancos);

    }
    public function crear(Request $request)
    {
        //
        $this->validate($request,[
            'cuenta_corriente'=>'required'

        ]);

        $cuentas_bancarias=Cuentas_bancarias::create($request->all());

        return response()->json('OK');

    }

    public function editarCuentasBancarias($id)
    {
        //

        $cuentas_bancarias = Cuentas_bancarias::findOrFail($id);
        return response()->json($cuentas_bancarias);
    }
    public function modificar(Request $request)
    {
        //
        $this->validate($request,[
            'cuenta_corriente'=>'required'

        ]);

        $cuentas_bancarias = Cuentas_bancarias::find($request->id);
        $cuentas_bancarias->cuenta_corriente=$request->cuenta_corriente;
        $cuentas_bancarias->cuenta_cci=$request->cuenta_cci;
        $cuentas_bancarias->banco_id=$request->banco_id;
        $cuentas_bancarias->save();

        return response()->json('OK');
    }
    public function eliminar($id)
    {
        //
        Cuentas_bancarias::find($id)->delete();
        return response()->json('OK');
    }
}
