<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Permissions;

class PermisosController extends Controller
{
    //

    public function __construct()
    {
        //$this->middleware('auth');modulo-permisos
        //$this->middleware('permission:modulo-permisos');
    }



    public function index()
    {
        
        
        return view('permisos.index');
    }

    public function listapermisos(){

        $permisos=Permissions::all();

        return response()->json($permisos);


    }

    public function crear(Request $request)
    {
        
        $this->validate($request,[
            'name'=>'required',
            'guard_name'=>'required'

        ]);

        $permisos=Permissions::create($request->all());

     //alert()->success('Proceso Exitoso.','Guardado Con exito')->autoclose(3000);
        return response()->json('OK');


    }

   







}
