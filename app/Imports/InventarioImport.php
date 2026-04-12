<?php

namespace App\Imports;

use App\Detalle_almacen_productos;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;

class InventarioImport implements ToModel,WithHeadingRow,WithValidation
{
    private $numRows = 0;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        ++$this->numRows;
        $detalle=Detalle_almacen_productos::where('ubicacion_id','=',$row['ubicacion_id'])
        ->where('producto_id','=',$row['producto_id'])
        ->where('tipo_envio','=',$row['tipo_envio'])
        ->first();

        if ($detalle) {
            
              // actualizar registro existente
            $detalle->stock =$detalle->stock + $row['stock'];
            $detalle->tipo_envio = $row['tipo_envio'];
            $detalle->ubicacion_id = $row['ubicacion_id'];
            $detalle->save();


        }else{

            $detalle= new Detalle_almacen_productos([
                //
                'producto_id'  => $row['producto_id'],
                'tipo_envio'  => $row['tipo_envio'],
                'stock'    => $row['stock'],
                'estado' =>  1,
                'ubicacion_id' => $row['ubicacion_id'],
               
            ]);
            $detalle->save();

        }

        return $detalle;

       
    }

    public function rules(): array
    {
        return [
            'producto_id' => 'required',
            'stock' => 'required',
            'ubicacion_id' => 'required',
            'tipo_envio' => 'required',

        ];
    }

    public function getRowCount(): int
    {
        return $this->numRows;
    }
}
