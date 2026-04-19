<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\InventarioExport;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use App\StokLocation;
use App\Productos;

class ReporteInventarioController extends Controller
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
        //
        $idsede = session('key')->sede_id;
        //var_dump($idsede); die;
        $tipo = DB::table('sedes as s')->select('s.tipo_envio')->where('s.id', '=', $idsede)->first();

        $ubicacion = DB::table('stock_location as sl')
            ->join('almacenes as a', 'sl.almacen_id', '=', 'a.id')
            ->select('sl.*')
            ->where('a.sede_id', '=', $idsede)
            //->where('s.tipo_envio','=',$tipo)
            ->get();
        //print_r( $ubicacion);exit();

        return view('reporteinventarios.index', compact('ubicacion'));
    }
    public function listarinventario($ubicacion)
    {
        $idsede = session('key')->sede_id;

        $inventario = DB::table('productos as p')
            ->leftjoin('detalle_almacen_productos as dp', 'dp.producto_id', '=', 'p.id')
            ->leftjoin('stock_location as sl', 'dp.ubicacion_id', '=', 'sl.id')
            ->leftjoin('almacenes as a', 'sl.almacen_id', '=', 'a.id')
            ->leftjoin('precios as pr', 'pr.articulo_id', '=', 'p.id')
            ->leftjoin('categorias as c', 'p.categoria_id', '=', 'c.id')
            ->leftjoin('sub_categorias as sub', 'p.subcategoria_id', '=', 'sub.id')
            ->leftjoin('unidad_medidas as u', 'p.unidad_medida_id', '=', 'u.id')
            ->leftjoin('marcas as m', 'p.marca_id', '=', 'm.id')
            ->leftjoin('colores as co', 'p.color_id', '=', 'co.id')
            ->select(
                'p.id',
                'a.nombre as ubicacion',
                'sl.name as nombrestock',
                'p.nomb_pro',
                'm.descripcion as marca',
                'c.categoria',
                'u.descripcion as unidad',
                'sub.subcategoria',
                DB::raw('SUM(dp.stock) as stock'),
                'p.costo'
            )
            ->groupBy(
                'p.id',
                'p.nomb_pro',
                'p.costo',
                'c.categoria',
                'c.id',
                'u.descripcion',
                'm.descripcion',
                'a.nombre',
                'sl.name',
                'sub.subcategoria'
            )
            ->where('a.sede_id', '=', $idsede)
            ->where('sl.id', '=', $ubicacion)
            ->where('p.estado', '=', '1')
            ->get();


        return response()->json($inventario);
    }
    public function exportarInventario($ubicacion)
    {

        $idsede = session('key')->sede_id;


        $inventario = DB::table('productos as p')
            ->leftjoin('detalle_almacen_productos as dp', 'dp.producto_id', '=', 'p.id')
            ->leftjoin('stock_location as sl', 'dp.ubicacion_id', '=', 'sl.id')
            ->leftjoin('almacenes as a', 'sl.almacen_id', '=', 'a.id')
            ->leftjoin('precios as pr', 'pr.articulo_id', '=', 'p.id')
            ->leftjoin('categorias as c', 'p.categoria_id', '=', 'c.id')
            ->leftjoin('sub_categorias as sub', 'p.subcategoria_id', '=', 'sub.id')
            ->leftjoin('unidad_medidas as u', 'p.unidad_medida_id', '=', 'u.id')
            ->leftjoin('marcas as m', 'p.marca_id', '=', 'm.id')
            ->leftjoin('colores as co', 'p.color_id', '=', 'co.id')
            ->select(
                'p.id',
                'a.nombre as ubicacion',
                'sl.name as nombrestock',
                'p.nomb_pro',
                'm.descripcion as marca',
                'c.categoria',
                'sub.subcategoria',
                'u.descripcion as unidad',
                DB::raw('SUM(dp.stock) as stock'),
                'p.costo'
            )
            ->groupBy(
                'p.id',
                'p.nomb_pro',
                'p.costo',
                'c.categoria',
                'sub.subcategoria',
                'c.id',
                'u.descripcion',
                'm.descripcion',
                'a.nombre',
                'sl.name'
            )
            ->where('a.sede_id', '=', $idsede)
            ->where('sl.id', '=', $ubicacion)
            ->where('p.estado', '=', '1')
            ->get();


        return Excel::download(new InventarioExport($inventario), 'inventario.xlsx');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
