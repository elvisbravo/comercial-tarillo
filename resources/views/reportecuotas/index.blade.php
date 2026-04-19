@extends('layouts.main')

@section('title')
Reporte Cuotas Vencidas
@endsection

@section('css')
<link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    .select2-container .select2-selection--multiple .select2-selection__choice {
        background-color: #556ee6;
        border: 1px solid #556ee6;
        color: #fff;
    }
    .select2-container .select2-selection--multiple .select2-selection__choice__remove {
        color: #fff;
    }

    /* Print styles */
    @media print {
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body * {
            visibility: hidden !important;
        }
        .container-fluid, .container-fluid * {
            visibility: visible !important;
        }
        .container-fluid {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
        }
        
        #form-filters, #form-filters *, .loader, .loader *, button, button *, .btn, .btn *, .page-title-box, .page-title-box * {
            display: none !important;
            visibility: hidden !important;
        }
        .print-header {
            display: block !important;
            margin-bottom: 20px;
        }
        .main-content {
            margin: 0 !important;
            padding: 0 !important;
        }
        .card { 
            border: 1px solid #ddd !important; 
            box-shadow: none !important; 
            page-break-inside: avoid;
        }
        /* Force two columns per row in print */
        .col-md-6 { 
            width: 50% !important; 
            float: left !important; 
        }
        .row::after {
            content: "";
            clear: both;
            display: table;
        }
    }
    
    .print-header {
        display: none;
    }
</style>
@endsection

@section('contenido')
<div class="loader" style="position: fixed; left: 0px; top: 0px; width: 100%; height: 100%; z-index: 9999; background: url('{{asset('img/loader-meta.gif')}}') 50% 50% no-repeat rgb(249,249,249); opacity: .8;"></div>

<div class="container-fluid">
    @php
        $agrupados = collect($datos)->groupBy('credito_id');
    @endphp

    <div class="print-header">
        <h3 style="color: #000; margin-bottom: 5px; text-transform: uppercase;">Reporte de Cuotas Vencidas al {{ date('d/m/Y', strtotime($hoy)) }}</h3>
        <p style="margin: 0; font-size: 16px;">
            <strong style="color: #175bb8;">Total encontrados: {{ $agrupados->count() }}</strong> &nbsp;|&nbsp; 
            <strong style="color: #dc3545;">Total de Deuda: S/ {{ number_format(collect($datos)->sum('saldo_cuo'), 2) }}</strong>
        </p>
        @if(!empty($sectoresSeleccionados))
            @php
                $nombresSectores = collect($sectores)->whereIn('id', $sectoresSeleccionados)->pluck('nomb_sec')->implode(', ');
            @endphp
            <p style="margin-top: 5px; margin-bottom: 0px; font-size: 14px; color: #555;">
                <strong>Sectores mostrados:</strong> {{ $nombresSectores }}
            </p>
        @endif
        <hr style="border-top: 2px solid #000; margin-top: 10px;">
    </div>

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">
                    Reporte de Cuotas Vencidas al {{ date('d/m/Y', strtotime($hoy)) }}
                    <span class="badge bg-primary ms-2 font-size-14">Total encontrados: {{ $agrupados->count() }}</span>
                    <span class="badge bg-danger ms-2 font-size-14">Total de Deuda: S/ {{ number_format(collect($datos)->sum('saldo_cuo'), 2) }}</span>
                </h4>
            </div>
        </div>
    </div>

    <!-- Buscador y Select -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm mb-0">
                <div class="card-body">
                    <form action="{{ route('reportecuotas.index') }}" method="GET" id="form-filters">
                        <div class="row align-items-end">
                            <div class="col-md-5 mb-3 mb-md-0">
                                <label for="buscar" class="form-label fw-bold">Buscar Cliente / Documento</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" id="buscar" name="buscar" value="{{ $buscar }}" placeholder="Razón social o número de documento...">
                                </div>
                            </div>
                            <div class="col-md-5 mb-3 mb-md-0">
                                <label for="sectores" class="form-label fw-bold">Sector(es)</label>
                                <select class="form-control select2" id="sectores" name="sectores[]" multiple="multiple" data-placeholder="Seleccione los sectores...">
                                    @foreach($sectores as $sector)
                                        <option value="{{ $sector->id }}" {{ in_array($sector->id, $sectoresSeleccionados) ? 'selected' : '' }}>
                                            {{ $sector->nomb_sec }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100 mb-2"><i class="fas fa-filter me-2"></i>Filtrar</button>
                                <button type="button" onclick="window.print()" class="btn btn-danger w-100"><i class="fas fa-file-pdf me-2"></i>Exportar PDF</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @foreach($agrupados as $credito_id => $cuotas)
        @php
        $primer_cuota = $cuotas->first();
        @endphp
        <div class="col-md-6 mb-4">
            <div class="card h-100 border shadow-sm">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-7">
                            <h5 class="card-title text-primary"><i class="fas fa-user-circle me-2"></i>{{ $primer_cuota['cliente'] }}</h5>
                            <h6 class="card-subtitle text-muted mt-2"><i class="fas fa-id-card me-2"></i>Doc: {{ $primer_cuota['documento'] }}</h6>
                        </div>
                        <div class="col-sm-5 text-sm-end">
                            <h6 class="card-subtitle text-muted mb-2"><i class="fas fa-map-marker-alt me-2"></i>{{ $primer_cuota['sector'] ?? 'N/A' }}</h6>
                            <h6 class="card-subtitle text-muted" style="line-height: 1.4;"><i class="fas fa-home me-2"></i>{{ $primer_cuota['direccion'] ?? 'N/A' }}</h6>
                        </div>
                    </div>
                    <p class="card-text mb-3">
                        <span class="badge bg-info"><i class="fas fa-box-open me-1"></i>Productos</span><br>
                        <small class="text-secondary">{{ $primer_cuota['productos'] }}</small>
                    </p>

                    <h6 class="fw-bold mt-4 mb-2"><i class="fas fa-calendar-alt me-2"></i>Cronograma de Pagos Vencidos</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">N° Cuota</th>
                                    <th class="text-center">Vencimiento</th>
                                    <th class="text-end">Monto</th>
                                    <th class="text-end">Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cuotas as $cuota)
                                <tr>
                                    <td class="text-center">{{ $cuota['numero_cuo'] }}</td>
                                    <td class="text-center">{{ date('d-m-Y', strtotime($cuota['fecha_amortizacion'])) }}</td>
                                    <td class="text-end">S/ {{ number_format($cuota['mont_cuo'], 2) }}</td>
                                    <td class="text-end text-danger fw-bold">S/ {{ number_format($cuota['saldo_cuo'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
<script>
    $(document).ready(function() {
        setTimeout(() => {
            $(".loader").fadeOut("slow");
        }, 300);

        $('.select2').select2({
            width: '100%',
            allowClear: true
        });
    });
</script>
@endsection