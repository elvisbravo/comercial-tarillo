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

    @media print {
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body * { visibility: hidden !important; }
        #print-area, #print-area * { visibility: visible !important; }
        #print-area {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            padding: 10px !important;
        }
        #form-filters, .page-title-box, .loader, .btn, nav { display: none !important; }
        .print-header { display: block !important; }
        table { font-size: 10px !important; }
        th, td { padding: 3px 4px !important; }
    }

    .print-header { display: none; }

    #reporte-table th {
        background-color: #3b5de7;
        color: #fff;
        font-size: 12px;
        white-space: nowrap;
    }
    #reporte-table td {
        font-size: 12px;
        vertical-align: middle;
    }
    .badge-cuotas {
        font-size: 11px;
        padding: 3px 7px;
        border-radius: 10px;
        background: #e9ecef;
        color: #495057;
        font-weight: 600;
    }

    /* Loading overlay */
    #loading-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(255,255,255,0.82);
        z-index: 9998;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 16px;
    }
    #loading-overlay.active { display: flex; }
    .loading-spinner {
        width: 52px;
        height: 52px;
        border: 5px solid #dee2e6;
        border-top-color: #3b5de7;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    .loading-text {
        font-size: 16px;
        font-weight: 600;
        color: #3b5de7;
        letter-spacing: 0.5px;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Resultado stats */
    #result-stats { display: none; }
</style>
@endsection

@section('contenido')

{{-- Loading overlay --}}
<div id="loading-overlay">
    <div class="loading-spinner"></div>
    <div class="loading-text">Cargando datos&hellip;</div>
</div>

<div class="container-fluid">

    {{-- Cabecera para impresion --}}
    <div class="print-header" id="print-header">
        <h4 style="margin-bottom:4px; text-transform:uppercase;">Reporte de Cuotas Vencidas</h4>
        <p style="margin:0; font-size:13px;" id="print-summary"></p>
        <hr style="border-top:2px solid #000; margin:6px 0;">
    </div>

    {{-- Titulo --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">
                    Reporte de Cuotas Vencidas
                    <span class="badge bg-primary ms-2" id="badge-count" style="display:none;"></span>
                    <span class="badge bg-danger ms-2" id="badge-saldo" style="display:none;"></span>
                </h4>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="row mb-3" id="form-filters">
        <div class="col-12">
            <div class="card shadow-sm mb-0">
                <div class="card-body py-3">
                    <form id="filtro-form">
                        <div class="row align-items-end">
                            <div class="col-md-5 mb-2 mb-md-0">
                                <label class="form-label fw-bold mb-1">Buscar Cliente / Documento</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" id="buscar"
                                        placeholder="Raz&oacute;n social o documento...">
                                </div>
                            </div>
                            <div class="col-md-5 mb-2 mb-md-0">
                                <label class="form-label fw-bold mb-1">Sector(es)</label>
                                <select class="form-control select2" id="sectores" multiple
                                    data-placeholder="Seleccione sectores...">
                                    @foreach($sectores as $sector)
                                    <option value="{{ $sector->id }}">{{ $sector->nomb_sec }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-filter me-1"></i> Filtrar
                                </button>
                                @if(App\Permisos::hasPermission('reportecuotas', 7))
                                <button type="button" onclick="window.print()" class="btn btn-danger w-100" id="btn-print" style="display:none;">
                                    <i class="fas fa-file-pdf me-1"></i> Exportar PDF
                                </button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div id="print-area">
        <div class="card shadow-sm" id="card-tabla" style="display:none;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="reporte-table" class="table table-bordered table-hover table-sm mb-0 align-middle w-100">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>N&deg; Cr&eacute;dito</th>
                                <th>Cliente</th>
                                <th>Direcci&oacute;n</th>
                                <th>Sector</th>
                                <th class="text-end">Monto</th>
                                <th class="text-end">Saldo</th>
                                <th class="text-center">Cuotas</th>
                                <th class="text-center">&Uacute;lt. Pago</th>
                                <th class="text-center">Pr&oacute;x. Vcto.</th>
                                <th class="text-center">Celular</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-data">
                        </tbody>
                        <tfoot id="tfoot-totales" style="display:none;">
                            <tr class="fw-bold" style="background:#f8f9fa;">
                                <td colspan="5" class="text-end">TOTALES:</td>
                                <td class="text-end" id="total-monto"></td>
                                <td class="text-end text-danger" id="total-saldo"></td>
                                <td colspan="4"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Estado vacio inicial --}}
        <div id="estado-inicial" class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Aplica los filtros y presiona <strong>Filtrar</strong> para ver los resultados</h5>
            </div>
        </div>
    </div>

</div>
@endsection

@section('js')
<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
<script>
    const urlGeneral = $("#url_raiz_proyecto").val();

    $(document).ready(function() {
        $('.select2').select2({ width: '100%', allowClear: true });
        // Pequena pausa para que el navegador pinte la pagina antes de iniciar la carga
        setTimeout(cargarDatos, 150);
    });

    $('#filtro-form').on('submit', function(e) {
        e.preventDefault();
        cargarDatos();
    });

    function cargarDatos() {
        const buscar   = $('#buscar').val();
        const sectores = $('#sectores').val();

        // Mostrar loading
        $('#loading-overlay').addClass('active');
        $('#card-tabla').hide();
        $('#estado-inicial').hide();

        $.ajax({
            url: urlGeneral + '/reportecuotas/getData',
            method: 'GET',
            data: {
                buscar: buscar,
                'sectores[]': sectores
            },
            success: function(res) {
                renderTabla(res);
            },
            error: function() {
                Swal.fire('Error', 'No se pudo cargar los datos. Intente nuevamente.', 'error');
                $('#estado-inicial').show();
            },
            complete: function() {
                $('#loading-overlay').removeClass('active');
            }
        });
    }

    function formatMoney(val) {
        return 'S/ ' + parseFloat(val).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function renderTabla(res) {
        const datos = res.datos;
        let html = '';

        if (datos.length === 0) {
            html = `<tr>
                        <td colspan="11" class="text-center text-muted py-4">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            No hay cuotas vencidas con los filtros seleccionados.
                        </td>
                    </tr>`;
            $('#tfoot-totales').hide();
            $('#btn-print').hide();
        } else {
            datos.forEach(function(row, i) {
                const proxFecha = row.proxima_fecha
                    ? `<span class="text-danger fw-bold">${formatFecha(row.proxima_fecha)}</span>`
                    : '-';

                html += `<tr>
                    <td class="text-center text-muted">${i + 1}</td>
                    <td class="text-center fw-bold text-primary">${row.credito_id}</td>
                    <td>${row.cliente}</td>
                    <td class="text-muted" style="max-width:160px; white-space:normal;">${row.direccion ?? '-'}</td>
                    <td>${row.sector}</td>
                    <td class="text-end">${formatMoney(row.monto_total)}</td>
                    <td class="text-end fw-bold text-danger">${formatMoney(row.saldo_total)}</td>
                    <td class="text-center"><span class="badge-cuotas">${row.cuotas_pagadas} / ${row.total_cuotas}</span></td>
                    <td class="text-center text-muted small">${row.ultima_pago}</td>
                    <td class="text-center small">${proxFecha}</td>
                    <td class="text-center">${row.telefono}</td>
                </tr>`;
            });

            // Totales
            $('#total-monto').text(formatMoney(res.total_monto));
            $('#total-saldo').text(formatMoney(res.total_saldo));
            $('#tfoot-totales').show();

            // Badges titulo
            $('#badge-count').text(res.count + ' cr\u00e9ditos').show();
            $('#badge-saldo').text('Deuda: ' + formatMoney(res.total_saldo)).show();

            // Print summary
            $('#print-summary').html(
                `<strong>Total cr\u00e9ditos:</strong> ${res.count} &nbsp;|&nbsp;
                 <strong style="color:#dc3545;">Saldo total: ${formatMoney(res.total_saldo)}</strong>`
            );

            $('#btn-print').show();
        }

        $('#tbody-data').html(html);
        $('#card-tabla').show();
        $('#estado-inicial').hide();
    }

    function formatFecha(dateStr) {
        if (!dateStr) return '-';
        const parts = dateStr.split('-');
        if (parts.length !== 3) return dateStr;
        return parts[2] + '/' + parts[1] + '/' + parts[0];
    }
</script>
@endsection
