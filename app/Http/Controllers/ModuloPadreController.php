<?php
namespace App\Http\Controllers;
use App\Modulo_padre;
use Illuminate\Http\Request;
use App\Constantes;
use App\Modulo;

class ModuloPadreController extends Controller{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('modulo_padre.index');
    }

    public function getListParentModule()
    {
        $modulo_padre = Modulo_padre::where('state', Constantes::STATUS_ACTIVE)->get();
        return response()->json($modulo_padre);
    }

    public function getParentModuleById($id)
    {
        $modulo_padre = Modulo_padre::find($id);
        return response()->json($modulo_padre);
    }

    public function create(Request $request)
    {
        $this->validate($request,[
            'name'=>'required',
            'icon'=>'required',
            'order'=>'required'
        ]);
        $modulo_padre=Modulo_padre::create($request->all());
        return response()->json('OK');
    }

    public function edit(Request $request)
    {
        $this->validate($request,[
            'name'=>'required',
            'icon'=>'required',
            'order'=>'required'
        ]);
        $modulo_padre = Modulo_padre::find($request->id);
        $modulo_padre->name=$request->name;
        $modulo_padre->icon=$request->icon;
        $modulo_padre->order=$request->order;
        $modulo_padre->save();
        return response()->json('OK');
    }

    public function delete($id)
    {
        $modulo = Modulo::where('state', Constantes::STATUS_ACTIVE)->where('idmodulo_padre', $id)->first();
        if ($modulo) {
            return response()->json(['error' => 'No se puede eliminar el registro porque está siendo utilizado.'], 500);
        }else{
            $modulo_padre = Modulo_padre::find($id);
            if ($modulo_padre) {
                $modulo_padre->state = Constantes::STATUS_INACTIVE;
                $modulo_padre->save();
                return response()->json('Ok');
            } else {
                return response()->json('Registro no encontrado.', 404);
            }
        }
        
    }
}