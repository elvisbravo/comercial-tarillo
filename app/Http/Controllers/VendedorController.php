<?php

namespace App\Http\Controllers;
use App\Vendedor;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendedorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');


    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("vendedores.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function listadovendedores(){
        $vendedores = DB::table('vendedores')->get();
        //var_dump($bancos); die;
        return response()->json($vendedores);
    }
    public function listadousuarios(){
        $users = DB::table('users')->get();
        //var_dump($users); die;
        return response()->json($users);
    }
    public function crear(Request $request)
    {
        //
        $this->validate($request,[
            'nombre'=>'required'
        ]);

        $respuesta=$this->validar( $request->usuario_id);
        if($respuesta=='1'){
            return response()->json(['success'=> false]);
        }else{
            $vendedores= new Vendedor;
            $vendedores->nombre = $request->nombre;
            $vendedores->documento = $request->documento;
            $vendedores->direccion = $request->direccion;
            $vendedores->usuario_id = $request->usuario_id;
            $vendedores->save();
    
            return response()->json(['success'=> true]);
        }

    }

    public function validar($id){

        $respuesta='';

        $vendedores = Vendedor::where('usuario_id','=',$id)->first();
        if(isset($vendedores)){
            $respuesta='1';

        }else{
            $respuesta='0';
        }

        return $respuesta;


    }
   
    public function editarvendedor($id)
    {
        //

        $vendedores = Vendedor::findOrFail($id);
        return response()->json($vendedores);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

     public function validareditar($id, $iduser){

        $respuesta = '';
        $vendedores = Vendedor:: where ('id', '=', $id)->where('usuario_id', '=', $iduser)->first();
        
        if(isset($vendedores)){
           $respuesta = '1';
          
        }else{
            $respuesta = '0';
        }
        return $respuesta;

    }
    public function validarusuarios($iduser){
        $respuesta = '';
        $vendedores = Vendedor::where ('usuario_id', '=', $iduser)->first();
        if(isset($vendedores)){
            $respuesta = '0';
        }else{
            $respuesta = '1';
        }
        return $respuesta;
    }

    public function modificar(Request $request)
    {
        //
        $this->validate($request,[
            'nombre'=>'required'

        ]);
        $respuesta=$this->validareditar( $request->id,$request->usuario_id);
        

            $vendedores = Vendedor::find($request->id);
            $vendedores->nombre = $request->nombre;
            $vendedores->documento = $request->documento;
            $vendedores->direccion = $request->direccion;
            if($respuesta =='1'){

             $vendedores->usuario_id = $request->usuario_id;
             $vendedores->save();
            
             return response()->json(['success'=> true]);
                
            }
            else{
                $respuesta=$this->validarusuarios( $request->usuario_id);
                if($respuesta == '0'){
                    return response()->json(['success'=> false]);
                }else{
                    $vendedores->usuario_id = $request->usuario_id;
                    $vendedores->save();
            
                    return response()->json(['success'=> true]);
                }
                
            }
            
    
            
         
        

       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar($id)
    {
        //
        Vendedor::find($id)->delete();
        return response()->json('OK');
    }
}
