<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\DB;

class CreditosExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = [];
        $sedes = DB::table('sedes')->get();

        foreach ($sedes as $sede) {
            // Check if this sede has active credits
            $count = DB::table('creditos')
                ->where('esta_cre', 1)
                ->where('sede_id', $sede->id)
                ->count();
            
            if ($count > 0) {
                $sheets[] = new CreditosSedeExport($sede);
            }
        }

        return $sheets;
    }
}
