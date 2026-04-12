<?php
namespace App\Http\Controllers;
use App\Modulo;
use App\Funcion;
use App\Funcion_Modulo;
use Illuminate\Http\Request;
use App\Constantes;

class ModuloController extends Controller{
    private $dataView;

    public function __construct()
    {
        $this->middleware('auth');
        $this->dataView = [
            'functions' => Funcion::where('state', Constantes::STATUS_ACTIVE)->get()
        ];
    }

    public function index()
    {
        return view('modulo.index', $this->dataView);
    }

    public function getListModule()
    {
        $modulo = Modulo::where('state', Constantes::STATUS_ACTIVE)->with('moduloPadre')->get();
        return response()->json($modulo);
    }

    public function getModuleById($id)
    {
        $modulo = Modulo::with('moduloPadre')->with('moduloFuncion')->find($id);
        return response()->json($modulo);
    }

    public function create(Request $request)
    {
        $this->validate($request,[
            'name'=>'required',
            'url'=>'required',
            'order'=>'required',
            'idmodulo_padre' => 'required',
            'idsFunctions' => 'required'
        ]);
        $idsFunctions = explode(',', $request->input('idsFunctions'));
        $modulo = new Modulo();
        $modulo->name = $request->name;
        $modulo->url = $request->url;
        $modulo->order = $request->order;
        $modulo->idmodulo_padre = $request->idmodulo_padre;
        $modulo->save();
        $moduloId = $modulo->id;
        foreach ($idsFunctions as $idFunction) {
            $funcion_modulo = new Funcion_Modulo();
            $funcion_modulo->idmodulo = $moduloId; 
            $funcion_modulo->idfuncion = $idFunction; 
            $funcion_modulo->save();
        }
        return response()->json('OK');
    }

    public function edit(Request $request)
    {  
        $this->validate($request,[
            'name'=>'required',
            'url'=>'required',
            'order'=>'required',
            'idmodulo_padre' => 'required'
        ]);
        $idsFunctions = explode(',', $request->input('idsFunctions'));
        $modulo = Modulo::find($request->id);
        $modulo->name=$request->name;
        $modulo->url=$request->url;
        $modulo->order=$request->order;
        $modulo->idmodulo_padre=$request->idmodulo_padre;
        $modulo->save();
        Funcion_Modulo::where('idmodulo', $request->id)->update(['state' => Constantes::STATUS_INACTIVE]);
        foreach ($idsFunctions as $idFunction){
            $existingFuncionModulo = Funcion_Modulo::where('idmodulo', $request->id)
            ->where('idfuncion', $idFunction)
            ->first();
            if ($existingFuncionModulo) {
                $existingFuncionModulo->update(['state' => Constantes::STATUS_ACTIVE]);
            }else{
                $funcion_modulo = Funcion_Modulo::create([
                    'idmodulo' => $request->id,
                    'idfuncion' => $idFunction
                ]);
            }
        }   
        return response()->json('OK');
    }

    public function delete($id)
    {
        $modulo = Modulo::find($id);
        if ($modulo) {
            $modulo->state = Constantes::STATUS_INACTIVE;
            $modulo->save();
            return response()->json('Ok');
        } else {
            return response()->json('Registro no encontrado.', 404);
        }
    }
}