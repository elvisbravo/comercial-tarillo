<?php

namespace App\Http\Controllers;
use App\Tipo_comprobantes;
use APP\Sede;
use APP\Detalle_compra;
use APP\Compra;
use APP\User;
use App\Exports\CompraExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReporteComprasController extends Controller
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
    public function index(Request $request){
        $idsede = session('key')->sede_id;
        $user_id = session('key')->id;

        //var_dump($idsede,$user_id);die;
        return view('reportecompras.index');
    }

    public function tipocomprobantes(){
        $tipo_comprobantes = DB::table('tipo_comprobantes')->whereIn('descripcion',['BOLETA DE VENTA ELECTRONICA', 'FACTURA ELECTRONICA'])->get();

        return response()->json($tipo_comprobantes);
    }

    public function sede(){

        $idsede = session('key')->sede_id;
        $user_id = session('key')->id;

        $sede= DB::table('users')
        ->join('sedes', 'users.sede_id', '=', 'sedes.id')
        ->select('sedes.nombre', 'sedes.id')
        ->where('users.sede_id','=',$idsede)
        ->where('users.id', '=', $user_id)->get();
        
            return response()->json($sede);
    }
    public function listareporte($desde, $hasta, $T_comprobante,$sede){
        $idsede = session('key')->sede_id;
        $user_id = session('key')->id;

        if($T_comprobante== '0'){
            if($sede=='0'){
                $compras = DB::table('compras')
                ->join('proveedors','compras.proveedor_id','=','proveedors.id')
                ->join('stock_location', 'compras.ubicacion_id', '=', 'stock_location.id')
                ->join('almacenes', 'stock_location.almacen_id', '=', 'almacenes.id')
                ->join('monedas', 'compras.moneda_id', '=', 'monedas.id')
                ->join('users', 'compras.user_id','=','users.id')
                ->leftjoin('forma_pagos','compras.forma_pago_id','=','forma_pagos.id')
                ->join('tipo_pagos', 'compras.tipo_pago_id', '=', 'tipo_pagos.id')
                ->join('tipo_comprobantes', 'compras.tipo_comprobante_id','=','tipo_comprobantes.id')
                ->join('sedes', 'compras.sede_id', '=', 'sedes.id')
                ->select('compras.id','proveedors.nombre_comercial','almacenes.nombre', 'monedas.descripcion as moneda', 'users.name', 
                'forma_pagos.descripcion as forma_pago','tipo_pagos.descripcion as tipo_pago', 'tipo_comprobantes.descripcion as tipo_comprobante',
                'compras.fecha_ingreso', 'compras.serie_comprobante',
                'compras.correlativo_comprobante','compras.compra_venta', 'compras.total_igv', 'compras.total_compra', 
                'compras.porcentaje_igv', 'sedes.nombre as sede','compras.total_compra_flete')
                ->where('compras.user_id','=',$user_id)
                ->where('compras.sede_id','=',$idsede)
                ->whereBetween('compras.fecha_ingreso', [$desde, $hasta])
                ->get();
            }else{
                $compras = DB::table('compras')
                ->join('proveedors','compras.proveedor_id','=','proveedors.id')
                ->join('stock_location', 'compras.ubicacion_id', '=', 'stock_location.id')
                ->join('almacenes', 'stock_location.almacen_id', '=', 'almacenes.id')
                ->join('monedas', 'compras.moneda_id', '=', 'monedas.id')
                ->join('users', 'compras.user_id','=','users.id')
                ->leftjoin('forma_pagos','compras.forma_pago_id','=','forma_pagos.id')
                ->join('tipo_pagos', 'compras.tipo_pago_id', '=', 'tipo_pagos.id')
                ->join('tipo_comprobantes', 'compras.tipo_comprobante_id','=','tipo_comprobantes.id')
                ->join('sedes', 'compras.sede_id', '=', 'sedes.id')
                ->select('compras.id','proveedors.nombre_comercial','almacenes.nombre', 'monedas.descripcion as moneda', 'users.name', 
                            'forma_pagos.descripcion as forma_pago','tipo_pagos.descripcion as tipo_pago', 'tipo_comprobantes.descripcion as tipo_comprobante',
                            'compras.fecha_ingreso', 'compras.serie_comprobante',
                            'compras.correlativo_comprobante','compras.compra_venta', 'compras.total_igv', 'compras.total_compra', 
                            'compras.porcentaje_igv', 'sedes.nombre as sede','compras.total_compra_flete')
                ->where('compras.user_id','=',$user_id)
                ->where('compras.sede_id','=',$idsede)
                ->where('sedes.id','=',$sede)
                ->whereBetween('compras.fecha_ingreso', [$desde, $hasta])
                ->get();
            }
            
        }else{
            if($sede=='0'){
                $compras = DB::table('compras')
                ->join('proveedors','compras.proveedor_id','=','proveedors.id')
                ->join('stock_location', 'compras.ubicacion_id', '=', 'stock_location.id')
                ->join('almacenes', 'stock_location.almacen_id', '=', 'almacenes.id')
                ->join('monedas', 'compras.moneda_id', '=', 'monedas.id')
                ->join('users', 'compras.user_id','=','users.id')
                ->leftjoin('forma_pagos','compras.forma_pago_id','=','forma_pagos.id')
                ->join('tipo_pagos', 'compras.tipo_pago_id', '=', 'tipo_pagos.id')
                ->join('tipo_comprobantes', 'compras.tipo_comprobante_id','=','tipo_comprobantes.id')
                ->join('sedes', 'compras.sede_id', '=', 'sedes.id')
                ->select('compras.id','proveedors.nombre_comercial','almacenes.nombre', 'monedas.descripcion as moneda', 'users.name', 
                            'forma_pagos.descripcion as forma_pago','tipo_pagos.descripcion as tipo_pago', 'tipo_comprobantes.descripcion as tipo_comprobante',
                            'compras.fecha_ingreso', 'compras.serie_comprobante',
                            'compras.correlativo_comprobante','compras.compra_venta', 'compras.total_igv', 'compras.total_compra', 
                            'compras.porcentaje_igv', 'sedes.nombre as sede','compras.total_compra_flete')
                ->where('compras.user_id','=',$user_id)
                ->where('compras.sede_id','=',$idsede)
                ->where('tipo_comprobantes.id','=',$T_comprobante)
                ->whereBetween('compras.fecha_ingreso', [$desde, $hasta])
                ->get();
            }else{
                $compras = DB::table('compras')
                ->join('proveedors','compras.proveedor_id','=','proveedors.id')
                ->join('stock_location', 'compras.ubicacion_id', '=', 'stock_location.id')
                ->join('almacenes', 'stock_location.almacen_id', '=', 'almacenes.id')
                ->join('monedas', 'compras.moneda_id', '=', 'monedas.id')
                ->join('users', 'compras.user_id','=','users.id')
                ->leftjoin('forma_pagos','compras.forma_pago_id','=','forma_pagos.id')
                ->join('tipo_pagos', 'compras.tipo_pago_id', '=', 'tipo_pagos.id')
                ->join('tipo_comprobantes', 'compras.tipo_comprobante_id','=','tipo_comprobantes.id')
                ->join('sedes', 'compras.sede_id', '=', 'sedes.id')
                ->select('compras.id','proveedors.nombre_comercial','almacenes.nombre', 'monedas.descripcion as moneda', 'users.name', 
                            'forma_pagos.descripcion as forma_pago','tipo_pagos.descripcion as tipo_pago', 'tipo_comprobantes.descripcion as tipo_comprobante',
                            'compras.fecha_ingreso', 'compras.serie_comprobante',
                            'compras.correlativo_comprobante','compras.compra_venta', 'compras.total_igv', 'compras.total_compra', 
                            'compras.porcentaje_igv', 'sedes.nombre as sede','compras.total_compra_flete')
                ->where('compras.user_id','=',$user_id)
                ->where('compras.sede_id','=',$idsede)
                ->where('tipo_comprobantes.id','=',$T_comprobante)
                ->where('sedes.id','=',$sede)
                ->whereBetween('compras.fecha_ingreso', [$desde, $hasta])
                ->get();
            }
        }
        
        //var_dump($compras); die;
        
        //,'monedas.descripcion','users.name', 'forma_pagos.descripcion', 'tipo_pagos.descripcion','tipo_comprobantes','compras.fecha_ingreso', 'compras.serie_comprobante','compras.correlativo_comprobante','compras.compra_venta', 'compras.total_igv', 'compras.total_compra', 'compras.porcentaje_igv', 'sedes.nombre'
        //var_dump($compras); die;
        return response()->json($compras);



    }

    public function exportarCompra($desde, $hasta, $T_comprobante, $sede){
        $idsede = session('key')->sede_id;
        $user_id = session('key')->id;
        /* 
        print_r($desde); 
        print_r($hasta);
        print_r($T_comprobante);
        print_r($sede); exit; */
        $compra = "";

        if($T_comprobante== '0'){
            if($sede=='0'){
                $compra = DB::table('compras')
                ->join('detalle_compras','compras.id','=','detalle_compras.compra_id')
                ->join('productos','detalle_compras.producto_id','=','productos.id')
                ->join('categorias','productos.categoria_id','=','categorias.id')
                ->join('proveedors','compras.proveedor_id','=','proveedors.id')
                ->join('marcas' , 'productos.marca_id', '=', 'marcas.id')
                ->join('colores' , 'productos.color_id', '=', 'colores.id')
                ->join('stock_location', 'compras.ubicacion_id', '=', 'stock_location.id')
                ->join('almacenes', 'stock_location.almacen_id', '=', 'almacenes.id')
                ->join('monedas', 'compras.moneda_id', '=', 'monedas.id')
                ->join('users', 'compras.user_id','=','users.id')
                ->leftjoin('forma_pagos','compras.forma_pago_id','=','forma_pagos.id')
                ->join('tipo_pagos', 'compras.tipo_pago_id', '=', 'tipo_pagos.id')
                ->join('tipo_comprobantes', 'compras.tipo_comprobante_id','=','tipo_comprobantes.id')
                ->join('sedes', 'compras.sede_id', '=', 'sedes.id')
                ->select('compras.id','productos.id', 'productos.nomb_pro', 'productos.codigo_barras', 'categorias.categoria', 'marcas.descripcion as marca',
                            'detalle_compras.cantidad', 'detalle_compras.precio', 'colores.descripcion as color','proveedors.nombre_comercial','almacenes.nombre', 
                            'monedas.descripcion as moneda', 'users.name','forma_pagos.descripcion as forma_pago','tipo_pagos.descripcion as tipo_pago',
                            'tipo_comprobantes.descripcion as tipo_comprobante',  'compras.fecha_ingreso', 'compras.serie_comprobante', 
                            'compras.correlativo_comprobante','compras.compra_venta', 'compras.total_igv', 'compras.total_compra', 
                            'compras.porcentaje_igv', 'sedes.nombre as sede','compras.total_compra_flete')
                ->where('compras.user_id','=',$user_id)
                ->whereBetween('compras.fecha_ingreso', [$desde, $hasta])
                ->get();
            }else{
                $compra = DB::table('compras')
                ->join('detalle_compras','compras.id','=','detalle_compras.compra_id')
                ->join('productos','detalle_compras.producto_id','=','productos.id')
                ->join('categorias','productos.categoria_id','=','categorias.id')
                ->join('proveedors','compras.proveedor_id','=','proveedors.id')
                ->join('marcas' , 'productos.marca_id', '=', 'marcas.id')
                ->join('colores' , 'productos.color_id', '=', 'colores.id')
                ->join('stock_location', 'compras.ubicacion_id', '=', 'stock_location.id')
                ->join('almacenes', 'stock_location.almacen_id', '=', 'almacenes.id')
                ->join('monedas', 'compras.moneda_id', '=', 'monedas.id')
                ->join('users', 'compras.user_id','=','users.id')
                ->leftjoin('forma_pagos','compras.forma_pago_id','=','forma_pagos.id')
                ->join('tipo_pagos', 'compras.tipo_pago_id', '=', 'tipo_pagos.id')
                ->join('tipo_comprobantes', 'compras.tipo_comprobante_id','=','tipo_comprobantes.id')
                ->join('sedes', 'compras.sede_id', '=', 'sedes.id')
                ->select('compras.id','productos.id', 'productos.nomb_pro', 'productos.codigo_barras', 'categorias.categoria', 'marcas.descripcion as marca',
                            'detalle_compras.cantidad', 'detalle_compras.precio', 'colores.descripcion as color','proveedors.nombre_comercial','almacenes.nombre', 
                            'monedas.descripcion as moneda', 'users.name','forma_pagos.descripcion as forma_pago','tipo_pagos.descripcion as tipo_pago',
                            'tipo_comprobantes.descripcion as tipo_comprobante',  'compras.fecha_ingreso', 'compras.serie_comprobante', 
                            'compras.correlativo_comprobante','compras.compra_venta', 'compras.total_igv', 'compras.total_compra', 
                            'compras.porcentaje_igv', 'sedes.nombre as sede','compras.total_compra_flete')
                ->where('compras.user_id','=',$user_id)
                ->where('compras.sede_id','=',$idsede)
                ->where('sedes.id','=',$sede)
                ->whereBetween('compras.fecha_ingreso', [$desde, $hasta])
                ->get();
            }
            
        }else{
            if($sede=='0'){
                $compra = DB::table('compras')
                ->join('detalle_compras','compras.id','=','detalle_compras.compra_id')
                ->join('productos','detalle_compras.producto_id','=','productos.id')
                ->join('categorias','productos.categoria_id','=','categorias.id')
                ->join('proveedors','compras.proveedor_id','=','proveedors.id')
                ->join('marcas' , 'productos.marca_id', '=', 'marcas.id')
                ->join('colores' , 'productos.color_id', '=', 'colores.id')
                ->join('stock_location', 'compras.ubicacion_id', '=', 'stock_location.id')
                ->join('almacenes', 'stock_location.almacen_id', '=', 'almacenes.id')
                ->join('monedas', 'compras.moneda_id', '=', 'monedas.id')
                ->join('users', 'compras.user_id','=','users.id')
                ->leftjoin('forma_pagos','compras.forma_pago_id','=','forma_pagos.id')
                ->join('tipo_pagos', 'compras.tipo_pago_id', '=', 'tipo_pagos.id')
                ->join('tipo_comprobantes', 'compras.tipo_comprobante_id','=','tipo_comprobantes.id')
                ->join('sedes', 'compras.sede_id', '=', 'sedes.id')
                ->select('compras.id','productos.id', 'productos.nomb_pro', 'productos.codigo_barras', 'categorias.categoria', 'marcas.descripcion as marca',
                            'detalle_compras.cantidad', 'detalle_compras.precio', 'colores.descripcion as color','proveedors.nombre_comercial','almacenes.nombre', 
                            'monedas.descripcion as moneda', 'users.name','forma_pagos.descripcion as forma_pago','tipo_pagos.descripcion as tipo_pago',
                            'tipo_comprobantes.descripcion as tipo_comprobante',  'compras.fecha_ingreso', 'compras.serie_comprobante', 
                            'compras.correlativo_comprobante','compras.compra_venta', 'compras.total_igv', 'compras.total_compra', 
                            'compras.porcentaje_igv', 'sedes.nombre as sede','compras.total_compra_flete')
                ->where('compras.user_id','=',$user_id)
                ->where('compras.sede_id','=',$idsede)
                ->where('tipo_comprobantes.id','=',$T_comprobante)
                ->whereBetween('compras.fecha_ingreso', [$desde, $hasta])
                ->get();
            }else{
                $compra = DB::table('compras')
                ->join('detalle_compras','compras.id','=','detalle_compras.compra_id')
                ->join('productos','detalle_compras.producto_id','=','productos.id')
                ->join('categorias','productos.categoria_id','=','categorias.id')
                ->join('proveedors','compras.proveedor_id','=','proveedors.id')
                ->join('marcas' , 'productos.marca_id', '=', 'marcas.id')
                ->join('colores' , 'productos.color_id', '=', 'colores.id')
                ->join('stock_location', 'compras.ubicacion_id', '=', 'stock_location.id')
                ->join('almacenes', 'stock_location.almacen_id', '=', 'almacenes.id')
                ->join('monedas', 'compras.moneda_id', '=', 'monedas.id')
                ->join('users', 'compras.user_id','=','users.id')
                ->leftjoin('forma_pagos','compras.forma_pago_id','=','forma_pagos.id')
                ->join('tipo_pagos', 'compras.tipo_pago_id', '=', 'tipo_pagos.id')
                ->join('tipo_comprobantes', 'compras.tipo_comprobante_id','=','tipo_comprobantes.id')
                ->join('sedes', 'compras.sede_id', '=', 'sedes.id')
                ->select('compras.id','productos.id', 'productos.nomb_pro', 'productos.codigo_barras', 'categorias.categoria', 'marcas.descripcion as marca',
                            'detalle_compras.cantidad', 'detalle_compras.precio', 'colores.descripcion as color','proveedors.nombre_comercial','almacenes.nombre', 
                            'monedas.descripcion as moneda', 'users.name','forma_pagos.descripcion as forma_pago','tipo_pagos.descripcion as tipo_pago',
                            'tipo_comprobantes.descripcion as tipo_comprobante',  'compras.fecha_ingreso', 'compras.serie_comprobante', 
                            'compras.correlativo_comprobante','compras.compra_venta', 'compras.total_igv', 'compras.total_compra', 
                            'compras.porcentaje_igv', 'sedes.nombre as sede','compras.total_compra_flete')
                ->where('compras.user_id','=',$user_id)
                ->where('compras.sede_id','=',$idsede)
                ->where('compras.tipo_comprobante_id','=',$T_comprobante)
                ->whereBetween('compras.fecha_ingreso', [$desde, $hasta])
                ->where('sedes.id','=',$sede)
                ->get();
            }
        }
        print_r(count($compra)); exit;
        return Excel::download(new CompraExport($compra), 'inventario.xlsx');
    }
}
