<?php

namespace App\Exports;

use App\Compra;
use APP\Detalle_compra;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use DB;

class CompraExport implements FromCollection,WithHeadings,WithTitle,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($compra=null)
    {
       
        $this->compra=$compra;
    }
    public function collection()
    {
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
        ->where('compras.estado','=','1')
        ->get();
        
        return $this->compra?:$compra;
    }

    public function headings(): array
    {
        return [
         
            [
                
                'CÓDIGO COMPRA',
                'CÓDIGO PRODUCTO',
                'NOMBRE PRODUCTO',
                'CÓDIGO BARRAS PRODUCTO',
                'CATEGORÍA',
                'MARCA',
                'CANTIDAD',
                'PRECIO UNITARIO',
                'COLOR',
                'PROVEEDOR',
                'UBICACIÓN',
                'MONEDA',
                'USUARIO',
                'FORMA PAGO',
                'TIPO PAGO',
                'T.COMPROBANTE',
                'FECHA INGRESO',
                'SERIE',
                'CORRELATIVO',
                'MONTO TOTAL',
                'TOTAL IGV',
                'TOTAL COMPRA',
                '%IGV',
                'SEDE',
                'FLETE'

            ]
        ];
    }

     //AGREGAR EL TAMAÑO DE LETRA

     public function styles(Worksheet $sheet)
     {
         return [
             // Style the first row as bold text.
             1    => ['font' => ['bold' => true]],
 
             // Styling a specific cell by coordinate.
             'B2' => ['font' => ['italic' => true]],
 
             // Styling an entire column.
             'C'  => ['font' => ['size' => 16]],
         ];
     }
 
     public function title(): string
     {
         return 'INVENTARIO';
     }
 
 

}
