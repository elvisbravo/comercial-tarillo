<?php

namespace App\Http\Controllers;

use App\Clientes;
use Illuminate\Http\Request;
use App\DireEntrega;
use App\DireccionesImagenes;
use DB;
use App\Http\Controllers\servicios\FuncionesController;
use App\Tipo_documento;
use App\Sector;

use Illuminate\Support\Facades\Storage;

class ClientesController extends Controller
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

    public function index(Request $request)
    {
        $buscar = $request->get('buscar');
        $estado = $request->get('estado', '1');

        $clientes = Clientes::where('estado_per', '=', $estado)
            ->when($buscar, function ($query, $buscar) {
                return $query->where(function($q) use ($buscar) {
                    $q->where('nomb_per', 'ilike', "%$buscar%")
                      ->orWhere('documento', 'like', "%$buscar%");
                });
            })->paginate(10);

        return view('clientes.index', compact('clientes', 'buscar', 'estado'));
    }

    public function create()
    {
        $tipo_documento = Tipo_documento::all();
        $sector = Sector::where('estado', '=', 'ACTIVO')->get();
        return view('clientes.create', compact('tipo_documento', 'sector'));
    }

    public function validar($documento)
    {

        $cliente = Clientes::where('documento', '=', $documento)->first();
        $repuesta = '';

        if (isset($cliente)) {
            $repuesta = $cliente->documento;
        } else {
            $repuesta = 0;
        }

        return $repuesta;
    }

    public function crear(Request $request)
    {
        $validar = $this->validar($request->documento);

        if ($validar == $request->documento) {
            $datos = array(
                "respuesta" => "error",
                "mensaje"   => "Número de documento ya registrado"
            );

            return response()->json($datos);
        }

        $name = session('key')->name;

        $clientes = new Clientes;

        $path = "";

        if ($request->hasFile('foto_referencia')) {
            $files = $request->file('foto_referencia');
            $fileName = uniqid() . '.' . $files->getClientOriginalExtension();
            $path = $files->storeAs('img/foto_referencia', $fileName);
            $files->move('img/foto_referencia', $fileName);
        }

        $clientes->nomb_per = $request->nomb_per;
        $clientes->pate_per = $request->pate_per;
        $clientes->mate_per = $request->mate_per;
        $clientes->sexo_per = $request->sexo_per;
        $clientes->documento = $request->documento;
        $clientes->dire_per = $request->dire_per;
        $clientes->estado_per = '1';
        $clientes->anexo_concar = $request->anexo_concar;
        $clientes->tipo_doc = $request->tipo_doc;
        $clientes->usuario_registro = $name;
        $clientes->telefono = $request->telefono;
        $clientes->email = $request->email;
        $clientes->password = '12345678';
        $clientes->ubigeo_id = $request->ubigeo_id;
        $clientes->pais = 'PERÚ';
        $clientes->razon_social = $request->razon_social;
        $clientes->codigo = $request->codigo;
        $clientes->id_sector = $request->id_sector;
        $clientes->tipo_cliente = $request->tipo_cliente;
        $clientes->conyugue = $request->conyugue;
        $clientes->referencia = $request->referencia;
        $clientes->path_image = $path;
        $clientes->save();

        $direentrega = new DireEntrega;
        $direentrega->cliente_id = $clientes->id;
        $direentrega->nombre_contacto = $clientes->razon_social;
        $direentrega->direccion = $clientes->dire_per;
        $direentrega->pais = 'PERÚ';
        $direentrega->correo = $clientes->email;
        $direentrega->telefono = $request->telefono;
        $direentrega->ubigeo_id = $request->ubigeo_id;
        $direentrega->usuario_registro = $name;
        $direentrega->id_sector = $request->id_sector;
        $direentrega->estado = 1;
        $direentrega->save();

        $datos = array(
            "respuesta" => "ok",
            "mensaje"   => "Se registro correctamente el cliente"
        );

        return response()->json($datos);
    }

    public function edit($id)
    {
        $clientes = Clientes::findOrFail($id);
        $direcciones = DB::table('dire_entrega')
            ->select(
                'id',
                'cliente_id',
                'nombre_contacto',
                'direccion',
                'telefono',
                'correo',
                'ubigeo_id',
                'pais',
                'usuario_registro',
                'referencia',
                'id_sector'
            )
            ->where('cliente_id', '=', $id)
            ->get();
        $tipo_documento = Tipo_documento::all();
        $sector = Sector::where('estado', '=', 'ACTIVO')->get();

        return view('clientes.edit', compact('clientes', 'direcciones', 'tipo_documento', 'sector'));
    }

    public function sector()
    {
        $sector = Sector::where('estado', '=', 'ACTIVO')->get();
        return response()->json($sector);
    }

    public function listadirecciones($id_cliente)
    {

        $direcciones = DB::table('dire_entrega')
            ->select(
                'id',
                'cliente_id',
                'nombre_contacto',
                'direccion',
                'telefono',
                'correo',
                'ubigeo_id',
                'pais',
                'usuario_registro',
                'referencia',
                'id_sector'
            )
            ->where('cliente_id', '=', $id_cliente)
            ->where('estado', '=', 1)
            ->orderBy('id', 'asc')
            ->get();

        return response()->json($direcciones);
    }

    public function direccion($id)
    {
        $direccion = DireEntrega::findOrFail($id);
        return response()->json($direccion);
    }

    public function guardardireccion(Request $request)
    {

        $name = session('key')->name;

        if ($request->id == 0) {
            $direentrega = new DireEntrega;
            $direentrega->usuario_registro = $name;
            $name = '';
        } else {
            $direentrega = DireEntrega::findOrFail($request->id);
        }

        $direentrega->cliente_id = $request->cliente_id;
        $direentrega->nombre_contacto = $request->nombre_contacto;
        $direentrega->direccion = $request->direccion;
        $direentrega->pais = 'PERÚ';
        $direentrega->correo = $request->correo;
        $direentrega->telefono = $request->telefono;
        $direentrega->ubigeo_id = $request->ubigeo_id;
        $direentrega->usuario_modifico = $name;
        $direentrega->referencia = $request->referencia;
        $direentrega->id_sector = $request->id_sector;
        $direentrega->estado = 1;
        $direentrega->save();

        $ultimoRegistro = DireEntrega::find($direentrega->id);

        if ($request->hasFile('fileImages')) {
            $files = $request->file('fileImages');

            foreach ($files as $file) {
                $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('img/imagenes_direcciones', $fileName);

                $file->move('img/imagenes_direcciones', $fileName);

                $imagenes_direc = new DireccionesImagenes;

                $imagenes_direc->path_image = $path;
                $imagenes_direc->status = 1;
                $imagenes_direc->dire_id = $ultimoRegistro->id;

                $imagenes_direc->save();
            }
        }

        return response()->json('OK');
    }

    public function update(Request $request)
    {
        $name = session('key')->name;

        $path = $request->imagen_path;

        if ($request->hasFile('foto_referencia')) {
            $files = $request->file('foto_referencia');
            $fileName = uniqid() . '.' . $files->getClientOriginalExtension();
            $path = $files->storeAs('img/foto_referencia', $fileName);
            $files->move('img/foto_referencia', $fileName);
        }

        $clientes = Clientes::findOrFail($request->id);
        $clientes->nomb_per = $request->nomb_per;
        $clientes->pate_per = $request->pate_per;
        $clientes->mate_per = $request->mate_per;
        $clientes->sexo_per = $request->sexo_per;
        $clientes->documento = $request->documento;
        $clientes->dire_per = $request->dire_per;
        $clientes->estado_per = '1';
        $clientes->anexo_concar = $request->anexo_concar;
        $clientes->tipo_doc = $request->tipo_doc;
        $clientes->usuario_registro = $name;
        $clientes->telefono = $request->telefono;
        $clientes->email = $request->email;
        //$clientes->sector_id=$request->sector_id;
        $clientes->password = '12345678';
        $clientes->ubigeo_id = $request->ubigeo_id;
        $clientes->pais = 'PERÚ';
        $clientes->razon_social = $request->razon_social;
        $clientes->usuario_modifico = $name;
        $clientes->codigo = $request->codigo;
        $clientes->id_sector = $request->id_sector;
        $clientes->tipo_cliente = $request->tipo_cliente;
        $clientes->conyugue = $request->conyugue;
        $clientes->referencia = $request->referencia;
        $clientes->path_image = $path;
        $clientes->save();

        return response()->json('OK');
    }

    public function activar($id)
    {
        $clientes = Clientes::findOrFail($id);
        $clientes->estado_per = '1';
        $clientes->save();
    }

    public function eliminar($id)
    {
        $cuotas_pendientes = DB::table('creditos as c')
            ->join('cuotas as cu', 'c.id', '=', 'cu.credito_id')
            ->where('c.cliente_id', '=', $id)
            ->where('cu.esta_cuo', '=', 'PENDIENTE')
            ->count();

        if ($cuotas_pendientes > 0) {
            return response()->json([
                'status' => 'error', 
                'mensaje' => 'El cliente no puede ser anulado porque tiene cuota(s) pendiente(s) por pagar.'
            ]);
        }

        $clientes = Clientes::findOrFail($id);
        $clientes->estado_per = '0';
        $clientes->save();

        return response()->json(['status' => 'ok']);
    }

    public function getImagesAddress($id)
    {
        $imagenes = DireccionesImagenes::where('dire_id', '=', $id)->where('status', '=', true)->orderBy('id', 'asc')->get();

        return response()->json($imagenes);
    }

    public function deleteImagenDireccion($id)
    {
        $imagen = DireccionesImagenes::findOrFail($id);
        $imagen->status = false;
        $imagen->save();

        return response()->json('ok');
    }
}
