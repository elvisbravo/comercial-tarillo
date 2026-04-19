<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Facades\Auth;
use App\Sede;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
        //$this->middleware('Modulo Usuarios');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $buscar = $request->get('buscar');

        $query = User::orderBy('id', 'DESC')->where('estado', '!=', 0);

        if (!empty($buscar)) {
            $query->where('name', 'LIKE', '%' . $buscar . '%');
        }

        $datos = $query->paginate(5);
        $datos->appends(['buscar' => $buscar]); // Para que la paginación mantenga el filtro

        return view('users.index', compact('datos', 'buscar'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::pluck('name','name')->all();
        $sedes=Sede::all();
        return view('users.create',compact('roles','sedes'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required',
            'sede_id' => 'required',
        ]);



        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['estado'] = 1;
        
        if($request->hasFile("img")){

            $file = $request->file("img");
            $nombre = $file->getClientOriginalName();
            $ruta= public_path("perfil");
            $input['img']=$nombre;
            $file->move($ruta,$nombre);
            //print_r($nombre);exit();

        }


        //print_r($input);exit();


        $user = User::create($input);
        $user->assignRole($request->input('roles'));


        return redirect()->route('users.index')
                        ->with('success','User created successfully');


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
         $user = User::find($id);
          return view('users.show',compact('user'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name','name')->all();
        $sedes=Sede::all();


        return view('users.edit',compact('user','roles','userRole','sedes'));


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
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'roles' => 'required',
            'sede_id' => 'required'
        ]);

        $file = $request->file("img");


        $input = $request->all();

        if(!empty($input['password'])){
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = array_except($input,array('password'));
        }



        if($request->hasFile("img")){

            $file = $request->file("img");
            $nombre = $file->getClientOriginalName();

            $ruta= public_path("perfil");

            $input['img']=$nombre;
            $file->move($ruta,$nombre);

            //print_r($nombre);exit();

        }
        //
        $input['sede_id'] =$request->sede_id;



        $user = User::find($id);

        //print_r($user);exit();
        $user->save($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();


        $user->assignRole($request->input('roles'));


        return redirect()->route('users.index')
                        ->with('success','User updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->estado = 0;
            $user->save();
        }
        return redirect()->route('users.index')
                        ->with('success','Usuario dado de baja exitosamente');

    }
}
