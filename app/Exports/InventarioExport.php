<?php

namespace App\Exports;

use App\Detalle_almacen_productos;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class InventarioExport implements FromCollection,WithHeadings,WithTitle,ShouldAutoSize,WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($inventario=null)
    {
        $this->inventario=$inventario;
    }


    public function collection()
    {
        //$idsede = session('key')->sede_id;

        $inventario=DB::table('productos as p')
        ->leftjoin('detalle_almacen_productos as dp','dp.producto_id','=','p.id')
        ->leftjoin('stock_location as sl','dp.ubicacion_id','=','sl.id')
        ->leftjoin('almacenes as a','sl.almacen_id','=','a.id')
        ->leftjoin('precios as pr','pr.articulo_id','=','p.id')
        ->leftjoin('categorias as c','p.categoria_id','=','c.id')
        ->leftjoin('sub_categorias as sub','p.subcategoria_id','=','sub.id')
        ->leftjoin('unidad_medidas as u','p.unidad_medida_id','=','u.id')
        ->leftjoin('marcas as m','p.marca_id','=','m.id')
        ->leftjoin('colores as co','p.color_id','=','co.id')
        ->select('p.id','a.nombre as ubicacion', 'sl.name as nombrestock', 'p.nomb_pro', 'm.descripcion as marca','c.categoria' ,
        'sub.subcategoria',
        'u.descripcion as unidad',
         DB::raw('SUM(dp.stock) as stock'), 'p.costo')
        ->groupBy('p.id','p.nomb_pro','p.costo','c.categoria','sub.subcategoria','c.id','u.descripcion',
        'm.descripcion', 'a.nombre', 'sl.name')
        //->where('a.sede_id','=', $idsede)
        ->where('p.estado','=','1')
        ->get();

        return $this->inventario?:$inventario;


    }

    public function headings(): array
    {
        return [
         
            [
                
                'CÓDIGO PRODUCTO',
                'UBICACIÓN',
                'UB.INTERNA',
                'NOMBRE PRODUCTO',
                'MARCA',
                'CATEGORÍA',
                'SUB_CATEGORÍA',
                'UNIDAD DE MEDIDA',
                'CANTIDAD INVENTARIADA',
                'COSTO PRODUCTO',
               
            ]
        ];
    }

    //AGREGAR EL TAMAÑO DE LETRA

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'INVENTARIO';
    }




}
