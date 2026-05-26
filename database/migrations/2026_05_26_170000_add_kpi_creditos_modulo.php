<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddKpiCreditosModulo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Verificar si ya existe el módulo
        $existing = DB::table('modulo')->where('url', 'reportes/kpi-creditos')->first();
        if (!$existing) {
            $moduloId = DB::table('modulo')->insertGetId([
                'name' => 'KPI de Créditos',
                'url' => 'reportes/kpi-creditos',
                'icon' => '#',
                'order' => 1,
                'state' => true,
                'padre_id' => 7
            ]);

            // Copiar los permisos de los submódulos hermanos (Reportes IDs: 50, 51, 52, 53, 54) para la acción "Ver"
            $existingReportesPerms = DB::table('permisos')
                ->select('rol_id', 'sede_id')
                ->whereIn('modulo_id', [50, 51, 52, 53, 54])
                ->distinct()
                ->get();
            foreach ($existingReportesPerms as $perm) {
                // Verificar si ya tiene el permiso para evitar duplicados
                $hasPerm = DB::table('permisos')
                    ->where('rol_id', $perm->rol_id)
                    ->where('modulo_id', $moduloId)
                    ->where('accion_id', 1) // Ver
                    ->where('sede_id', $perm->sede_id)
                    ->exists();

                if (!$hasPerm) {
                    DB::table('permisos')->insert([
                        'rol_id' => $perm->rol_id,
                        'modulo_id' => $moduloId,
                        'accion_id' => 1, // Ver
                        'sede_id' => $perm->sede_id
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $modulo = DB::table('modulo')->where('url', 'reportes/kpi-creditos')->first();
        if ($modulo) {
            DB::table('permisos')->where('modulo_id', $modulo->id)->delete();
            DB::table('modulo')->where('id', $modulo->id)->delete();
        }
    }
}
