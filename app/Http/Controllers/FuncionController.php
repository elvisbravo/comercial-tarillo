<?php
namespace App\Http\Controllers;
use App\Funcion;
use Illuminate\Http\Request;
use App\Constantes;
use App\Funcion_Modulo;

class FuncionController extends Controller{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('funcion.index');
    }

    public function getListFunction()
    {
        $funcion = Funcion::where('state', Constantes::STATUS_ACTIVE)->get();
        return response()->json($funcion);
    }

    public function getFunctionById($id)
    {
        $funcion = Funcion::find($id);
        return response()->json($funcion);
    }

   public function create(Request $request)
    {
        $this->validate($request,[
            'name'=>'required',
            'button' => 'required'
        ]);
        $funcion=Funcion::create($request->all());
        return response()->json('OK');
    }

    public function edit(Request $request)
    {
        $this->validate($request,[
            'name'=>'required',
            'button' => 'required'
        ]);
        $funcion = Funcion::find($request->id);
        $funcion->name=$request->name;
        $funcion->icon=$request->icon;
        $funcion->class=$request->class;
        $funcion->button=$request->button;
        $funcion->order=$request->order;
        $funcion->save();
        return response()->json('OK');
    }

    public function delete($id)
    {
        $modulo_funcion = Funcion_Modulo::where('state', Constantes::STATUS_ACTIVE)->where('idfuncion', $id)->first();
        if ($modulo_funcion) {
            return response()->json(['error' => 'No se puede eliminar el registro porque está siendo utilizado.'], 500);
        }else{
            $funcion = Funcion::find($id);
            if ($funcion) {
                $funcion->state = Constantes::STATUS_INACTIVE;
                $funcion->save();
                return response()->json('Ok');
            } else {
                return response()->json('Registro no encontrado.', 404);
            }
        }
    }
}