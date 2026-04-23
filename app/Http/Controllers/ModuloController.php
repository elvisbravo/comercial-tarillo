<?php
namespace App\Http\Controllers;
use App\Modulo;
use App\Funcion;
use App\Funcion_Modulo;
use Illuminate\Http\Request;
use App\Constantes;

class ModuloController extends Controller{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('modulo.index');
    }

    public function getListModule()
    {
        $modulo = Modulo::where('state', true)->get();
        return response()->json($modulo);
    }

    public function getModuleById($id)
    {
        $modulo = Modulo::find($id);
        return response()->json($modulo);
    }

    public function getParentModules()
    {
        $parents = Modulo::where('padre_id', 0)->where('state', true)->get();
        return response()->json($parents);
    }

    public function create(Request $request)
    {
        $this->validate($request,[
            'name'=>'required',
            'url'=>'required',
            'icon'=>'required',
            'order'=>'required',
            'padre_id' => 'required'
        ]);

        $modulo = new Modulo();
        $modulo->name = $request->name;
        $modulo->url = $request->url;
        $modulo->icon = $request->icon;
        $modulo->order = $request->order;
        $modulo->padre_id = $request->padre_id;
        $modulo->state = true;
        $modulo->save();

        return response()->json('OK');
    }

    public function edit(Request $request)
    {  
        $this->validate($request,[
            'name'=>'required',
            'url'=>'required',
            'icon'=>'required',
            'order'=>'required',
            'padre_id' => 'required'
        ]);

        $modulo = Modulo::find($request->id);
        $modulo->name=$request->name;
        $modulo->url=$request->url;
        $modulo->icon=$request->icon;
        $modulo->order=$request->order;
        $modulo->padre_id=$request->padre_id;
        $modulo->save();

        return response()->json('OK');
    }

    public function delete($id)
    {
        $modulo = Modulo::find($id);
        if ($modulo) {
            $modulo->state = false;
            $modulo->save();
            return response()->json('Ok');
        } else {
            return response()->json('Registro no encontrado.', 404);
        }
    }
}