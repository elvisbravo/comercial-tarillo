<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Cuotas Vencidas</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h3 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 5px 0; font-size: 12px; color: #555; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f4f4f4; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-danger { color: red; font-weight: bold; }
        .group-info { font-size: 10px; color: #444; }
    </style>
</head>
<body>

    <div class="header">
        <h3>REPORTE DE CUOTAS VENCIDAS</h3>
        <p>Generado al: {{ date('d/m/Y', strtotime($hoy)) }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Cliente y Documento</th>
                <th>Productos</th>
                <th>N° Cuota</th>
                <th>Vencimiento</th>
                <th>Monto</th>
                <th>Saldo</th>
                <th>Método Pago</th>
            </tr>
        </thead>
        <tbody>
            @php
                $agrupados = collect($datos)->groupBy('credito_id');
            @endphp

            @forelse($agrupados as $credito_id => $cuotas)
                @php
                    $primer_cuota = $cuotas->first();
                    $rowspan = $cuotas->count();
                    $firstRow = true;
                @endphp

                @foreach($cuotas as $cuota)
                    <tr>
                        @if($firstRow)
                            <td rowspan="{{ $rowspan }}">
                                <strong>{{ $primer_cuota['cliente'] }}</strong><br>
                                <span class="group-info">Doc: {{ $primer_cuota['documento'] }}</span>
                            </td>
                            <td rowspan="{{ $rowspan }}">
                                <span class="group-info">{{ $primer_cuota['productos'] }}</span>
                            </td>
                            @php $firstRow = false; @endphp
                        @endif
                        
                        <td class="text-center">{{ $cuota['numero_cuo'] }}</td>
                        <td class="text-center">{{ date('d-m-Y', strtotime($cuota['fecha_amortizacion'])) }}</td>
                        <td class="text-end">S/ {{ number_format($cuota['mont_cuo'], 2) }}</td>
                        <td class="text-end text-danger">S/ {{ number_format($cuota['saldo_cuo'], 2) }}</td>
                        <td class="text-center">{{ $cuota['metodo_pago'] ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="7" class="text-center">No hay cuotas vencidas para mostrar con los filtros seleccionados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
