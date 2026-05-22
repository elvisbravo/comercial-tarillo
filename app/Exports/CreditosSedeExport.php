<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CreditosSedeExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    protected $sede;

    public function __construct($sede)
    {
        $this->sede = $sede;
    }

    public function collection()
    {
        $creditos = DB::table('creditos')
            ->join('clientes', 'creditos.cliente_id', '=', 'clientes.id')
            ->leftJoin('sectores', 'clientes.id_sector', '=', 'sectores.id')
            ->select(
                'creditos.id as n_credito',
                'clientes.documento as dni',
                DB::raw("COALESCE(clientes.razon_social, CONCAT(clientes.nomb_per, ' ', clientes.pate_per, ' ', clientes.mate_per)) as cliente"),
                DB::raw("(SELECT STRING_AGG(p.nomb_pro, ', ') FROM detalle_venta dv JOIN productos p ON dv.producto_id = p.id WHERE dv.venta_id = creditos.id_venta) as productos"),
                'clientes.dire_per as direccion',
                'sectores.nomb_sec as sector',
                'creditos.mont_cre as monto_credito',
                DB::raw("(SELECT SUM(c.saldo_cuo) FROM cuotas c WHERE c.credito_id = creditos.id) as saldo_por_pagar")
            )
            ->where('creditos.esta_cre', 1)
            ->where('creditos.sede_id', $this->sede->id)
            ->get();

        return $creditos;
    }

    public function headings(): array
    {
        return [
            'N° de credito',
            'dni',
            'cliente',
            'productos',
            'direccion',
            'sector',
            'monto credito',
            'saldo por pagar'
        ];
    }

    public function title(): string
    {
        $nombreSede = str_replace(['*', ':', '/', '\\', '?', '[', ']'], '', $this->sede->nombre);
        return substr($nombreSede, 0, 31);
    }
}
