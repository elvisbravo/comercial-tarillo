@extends('layouts.main')

@section('title', 'Mi Historial de Cobros - Ventas Móviles')

@section('css')
<style>
    .mobile-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        background: #ffffff;
    }
    .btn-action {
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    /* Lista de Cobros */
    .cobro-card-item {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 14px;
        padding: 16px;
        margin-bottom: 12px;
        transition: all 0.2s ease;
    }
    .cobro-card-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        border-color: #cbd5e1;
    }
    .badge-status {
        border-radius: 20px;
        padding: 4px 10px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .badge-emitido { background: #d1fae5; color: #059669; }
    .badge-anulado { background: #fee2e2; color: #dc2626; }
    .badge-liquidado { background: #e0f2fe; color: #0369a1; }
    .badge-pendiente { background: #ffedd5; color: #d97706; }
    .badge-pago { background: #f3e8ff; color: #6b21a8; }
    
    /* Filtros */
    .filter-card {
        background: #f8fafc;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        padding: 16px;
        margin-bottom: 16px;
    }
    /* Modal */
    .modal-header-custom {
        background: linear-gradient(135deg, #1f4068, #162447);
        color: white;
        border-radius: 16px 16px 0 0;
        padding: 16px 20px;
    }
    .modal-content { border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
    .table-modal thead { background: #f1f5f9; }
    .table-modal thead th { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; padding: 8px 12px; border: none; }
    .table-modal tbody td { padding: 10px 12px; font-size: 13px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
    .table-modal tbody tr:last-child td { border-bottom: none; }
    .info-chip {
        background: #f1f5f9; border-radius: 8px; padding: 8px 12px;
        font-size: 11px; font-weight: 600; color: #475569;
        height: 100%;
    }
    .info-chip span { color: #1e293b; font-size: 12px; display: block; margin-top: 2px; font-weight: 700; }
    .empty-state { text-align: center; padding: 40px 20px; color: #94a3b8; }
    .empty-state i { font-size: 48px; display: block; margin-bottom: 8px; }
    .empty-state p { font-size: 13px; font-weight: 500; }
    
    .btn-ver-detalle {
        border-radius: 8px; font-weight: 600; font-size: 12px;
        padding: 6px 14px; transition: all 0.2s;
    }
    .title-banner {
        background: linear-gradient(135deg, #1f4068 0%, #162447 100%); 
        color: white; 
        border-radius: 14px;
    }
</style>
@endsection

@section('contenido')
<div class="container-fluid py-3">

    {{-- Encabezado --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="title-banner p-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 42px; height: 42px; background: rgba(255,255,255,0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="mdi mdi-cash-multiple" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 text-white font-weight-bold" style="font-size: 16px;">Mi Historial de Cobros</h4>
                        <p class="text-white mb-0" style="font-size: 12px; opacity: 0.85;">Registro de amortizaciones y cobros en ruta.</p>
                    </div>
                </div>
                <a href="{{ route('vendedor.dashboard') }}" class="btn btn-light btn-action btn-sm">
                    <i class="mdi mdi-arrow-left me-1"></i> Panel
                </a>
            </div>
        </div>
    </div>

    {{-- Filtros y Búsqueda --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('vendedor.historial_cobros') }}" id="form_filtros">
            <div class="row g-2">
                <div class="col-6 col-sm-4">
                    <label class="form-label font-weight-bold text-dark mb-1" style="font-size: 11px; text-transform: uppercase;">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}" style="border-radius: 8px; font-size: 12px;">
                </div>
                <div class="col-6 col-sm-4">
                    <label class="form-label font-weight-bold text-dark mb-1" style="font-size: 11px; text-transform: uppercase;">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}" style="border-radius: 8px; font-size: 12px;">
                </div>
                <div class="col-12 col-sm-4">
                    <label class="form-label font-weight-bold text-dark mb-1" style="font-size: 11px; text-transform: uppercase;">Buscar Cliente / Recibo</label>
                    <div class="input-group">
                        <input type="text" name="buscar" class="form-control" value="{{ request('buscar') }}" placeholder="Nombre, RUC, Recibo..." style="border-radius: 8px 0 0 8px; font-size: 12px;">
                        <button type="submit" class="btn btn-primary btn-action btn-sm px-3" style="border-radius: 0 8px 8px 0;">
                            <i class="mdi mdi-magnify"></i>
                        </button>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end gap-1 mt-2">
                    <button type="submit" class="btn btn-primary btn-action btn-sm px-4">
                        <i class="mdi mdi-filter-variant me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('vendedor.historial_cobros') }}" class="btn btn-light btn-action btn-sm" title="Limpiar filtros">
                        <i class="mdi mdi-refresh me-1"></i> Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Listado de Cobranzas --}}
    <div class="row">
        <div class="col-12">
            @if(count($cobros) > 0)
                @foreach($cobros as $cobro)
                @php
                    $isAnulado = ($cobro->esta_rec === 'ANULADO');
                    $isLiquidado = ($cobro->estado_liquidacion === 'LIQUIDADO');
                @endphp
                <div class="cobro-card-item">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge" style="background: #1e293b; color: white; border-radius: 6px; font-size: 11px; padding: 4px 8px; font-weight: 700; letter-spacing: 0.5px;">
                                Recibo: {{ $cobro->num_recibo ?? ('REC-' . str_pad($cobro->id, 6, '0', STR_PAD_LEFT)) }}
                            </span>
                        </div>
                        <div class="d-flex flex-wrap gap-1 align-items-center">
                            {{-- Badge Forma de Pago --}}
                            @if($cobro->forma_pago)
                                <span class="badge-status badge-pago">
                                    <i class="mdi mdi-wallet-outline"></i> {{ $cobro->forma_pago }}
                                </span>
                            @endif
                            
                            {{-- Badge Liquidacion --}}
                            @if($isLiquidado)
                                <span class="badge-status badge-liquidado" title="Cobro Liquidado">
                                    <i class="mdi mdi-cash-check"></i> Liquidado
                                </span>
                            @else
                                <span class="badge-status badge-pendiente" title="Cobro Pendiente de Liquidación">
                                    <i class="mdi mdi-cash-clock"></i> Pendiente Liq.
                                </span>
                            @endif

                            {{-- Badge Estado --}}
                            @if($isAnulado)
                                <span class="badge-status badge-anulado">
                                    <i class="mdi mdi-cancel"></i> Anulado
                                </span>
                            @else
                                <span class="badge-status badge-emitido">
                                    <i class="mdi mdi-check-circle"></i> Emitido
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-12 col-md-8">
                            <div class="text-dark font-weight-bold mb-1" style="font-size: 13px;">
                                <i class="mdi mdi-account-outline text-muted me-1"></i>{{ $cobro->nombre_cliente }}
                            </div>
                            <div class="text-muted" style="font-size: 11px;">
                                <span>Doc: {{ $cobro->documento_cliente ?? '—' }}</span> &middot; 
                                <span><i class="mdi mdi-calendar-blank-outline me-1"></i>{{ \Carbon\Carbon::parse($cobro->fech_rec)->format('d/m/Y') }} {{ $cobro->created_at ? \Carbon\Carbon::parse($cobro->created_at)->format('H:i') : '' }}</span>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 text-start text-md-end">
                            <span class="text-muted font-size-11 d-md-block">Total Cobrado</span>
                            <span class="text-dark font-weight-bold" style="font-size: 16px; color: #2e7d32;">S/ {{ number_format($cobro->mont_rec, 2) }}</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-2 mt-2">
                        <button type="button"
                                class="btn btn-outline-primary btn-ver-detalle btn-sm px-3"
                                data-id="{{ $cobro->id }}">
                            <i class="mdi mdi-eye-outline me-1"></i> Detalles
                        </button>
                        <a href="{{ url('consulta-amortizaciones/recibo', $cobro->id) }}" 
                           target="_blank" 
                           class="btn btn-outline-secondary btn-sm px-3 btn-action" 
                           title="Ver / Imprimir Recibo">
                            <i class="mdi mdi-printer me-1"></i> Ticket
                        </a>
                    </div>
                </div>
                @endforeach
            @else
                <div class="card mobile-card p-4 text-center">
                    <div class="empty-state">
                        <i class="mdi mdi-cash-remove"></i>
                        <p>No tienes registros de cobros con los filtros seleccionados.</p>
                        <a href="{{ route('vendedor.historial_cobros') }}" class="btn btn-light btn-action btn-sm mt-2">
                            <i class="mdi mdi-refresh me-1"></i> Ver todos
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>

{{-- Modal de Detalle de Cobro --}}
<div class="modal fade" id="modalDetalleCobro" tabindex="-1" aria-labelledby="modalDetallesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header-custom">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 36px; height: 36px; background: rgba(255,255,255,0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="mdi mdi-cash-multiple" style="font-size: 20px;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold" id="modalDetallesLabel" style="font-size: 15px;">Detalle de Cobro</h5>
                        <small style="opacity: 0.75; font-size: 11px;" id="modal_subtitulo">Cargando información...</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                {{-- Loading --}}
                <div id="modal_loading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status" style="width: 1.5rem; height: 1.5rem;"></div>
                    <p class="text-muted mt-2 mb-0" style="font-size: 12px;">Cargando detalles del recibo...</p>
                </div>
                {{-- Contenido --}}
                <div id="modal_contenido" class="d-none">
                    {{-- Chips de info --}}
                    <div class="row g-2 mb-3" id="modal_chips">
                        <div class="col-6 col-md-3">
                            <div class="info-chip">Nro. Recibo<span id="chip_recibo">—</span></div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="info-chip">Fecha Cobro<span id="chip_fecha">—</span></div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="info-chip">Forma de Pago<span id="chip_pago">—</span></div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="info-chip">Liquidación<span id="chip_liquidacion">—</span></div>
                        </div>
                        <div class="col-12">
                            <div class="info-chip">Cliente<span id="chip_cliente">—</span></div>
                        </div>
                        <div class="col-12" id="box_observacion" style="display: none;">
                            <div class="info-chip">Observación<span id="chip_observacion" style="font-weight: normal; font-style: italic;">—</span></div>
                        </div>
                    </div>
                    {{-- Tabla de amortizaciones --}}
                    <div class="table-responsive">
                        <table class="table table-modal mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 30px;">#</th>
                                    <th>Crédito / Comprobante</th>
                                    <th class="text-center">Nro Cuota</th>
                                    <th class="text-end">Monto Cuota</th>
                                    <th class="text-end">Capital Amo.</th>
                                    <th class="text-end">Interés Amo.</th>
                                    <th class="text-end">Total Amo.</th>
                                    <th class="text-end">Saldo Restante</th>
                                </tr>
                            </thead>
                            <tbody id="modal_tabla_body">
                            </tbody>
                            <tfoot>
                                <tr style="background: #f8fafc;">
                                    <td colspan="6" class="font-weight-bold text-dark text-end" style="font-size: 12px; padding: 8px 12px;">TOTAL COBRADO</td>
                                    <td class="text-end font-weight-bold text-success" style="font-size: 13px;" id="modal_total_monto">S/ 0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                {{-- Error --}}
                <div id="modal_error" class="d-none text-center py-4">
                    <i class="mdi mdi-alert-circle-outline text-danger" style="font-size: 36px;"></i>
                    <p class="text-muted mt-2 mb-0" style="font-size: 12px;">No se pudo cargar el detalle del cobro. Intente de nuevo.</p>
                </div>
            </div>
            <div class="modal-footer border-0 px-3 pb-3 pt-0">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div>
                        <a href="#" id="modal_link_ticket" target="_blank" class="btn btn-outline-secondary btn-action btn-sm">
                            <i class="mdi mdi-printer me-1"></i> Imprimir Recibo
                        </a>
                    </div>
                    <button type="button" class="btn btn-light btn-action btn-sm" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function () {
    $(".loader").fadeOut("slow");

    // Preseleccionar fecha de hoy si no hay filtros activos
    @if(!request()->hasAny(['fecha_desde','fecha_hasta','buscar']))
    $('input[name="fecha_desde"]').val('{{ date('Y-m-d') }}');
    $('input[name="fecha_hasta"]').val('{{ date('Y-m-d') }}');
    @endif

    // Ver Detalle del Cobro
    $(document).on('click', '.btn-ver-detalle', function () {
        const id = $(this).data('id');

        // Reset modal
        $('#modal_loading').removeClass('d-none');
        $('#modal_contenido').addClass('d-none');
        $('#modal_error').addClass('d-none');
        $('#modal_subtitulo').text('Cargando información...');
        $('#modal_tabla_body').html('');
        $('#chip_recibo, #chip_fecha, #chip_pago, #chip_liquidacion, #chip_cliente, #chip_observacion').text('—');
        $('#box_observacion').hide();
        $('#modal_total_monto').text('S/ 0.00');
        $('#modal_link_ticket').attr('href', '#');

        $('#modalDetalleCobro').modal('show');

        $.ajax({
            url: '{{ url("vendedor/historial-cobros/detalle") }}/' + id,
            type: 'GET',
            success: function (res) {
                const r = res.recibo;
                const fechaFmt = r.fech_rec ? r.fech_rec.substring(0, 10).split('-').reverse().join('/') : '—';
                const timeFmt = r.created_at ? r.created_at.substring(11, 16) : '';

                // Cabecera e información básica
                const reciboFmt = r.num_recibo || ('REC-' + String(r.id).padStart(6, '0'));
                $('#chip_recibo').text(reciboFmt);
                $('#chip_fecha').text(fechaFmt + (timeFmt ? ' ' + timeFmt : ''));
                $('#chip_pago').text(r.forma_pago || '—');
                
                // Liquidacion chip
                if (r.estado_liquidacion === 'LIQUIDADO') {
                    $('#chip_liquidacion').html('<span class="text-success"><i class="mdi mdi-check-circle me-1"></i>LIQUIDADO</span>');
                } else {
                    $('#chip_liquidacion').html('<span class="text-warning"><i class="mdi mdi-clock-outline me-1"></i>PENDIENTE</span>');
                }
                
                // Cliente chip
                $('#chip_cliente').text(r.nombre_cliente + (r.documento_cliente ? ' (Doc: ' + r.documento_cliente + ')' : ''));
                $('#modal_subtitulo').text(reciboFmt);

                // Observacion chip
                if (r.obse_rec) {
                    $('#chip_observacion').text(r.obse_rec);
                    $('#box_observacion').show();
                }

                // Configurar enlace de ticket en modal
                $('#modal_link_ticket').attr('href', '{{ url("consulta-amortizaciones/recibo") }}/' + r.id);

                // Llenar tabla de amortizaciones
                let html = '';
                res.amortizaciones.forEach(function (a, i) {
                    const montAmoVal = parseFloat(a.mont_amo) || 0;
                    const capVal = parseFloat(a.capi_amo) || 0;
                    const intVal = parseFloat(a.inte_amo) || 0;
                    const cuoVal = parseFloat(a.mont_cuo) || 0;
                    const salRestVal = parseFloat(a.saldo_restante_cuota) || 0;
                    
                    let refCredito = 'Crédito #' + a.credito_id;
                    if (a.serie_comprobante && a.numero_comprobante) {
                        refCredito += ` (${a.tipo_comprobante}: ${a.serie_comprobante}-${String(a.numero_comprobante).padStart(6, '0')})`;
                    }

                    html += `<tr>
                        <td class="text-muted" style="font-size:11px;">${i + 1}</td>
                        <td class="font-weight-bold text-dark" style="font-size:12px;">${refCredito}</td>
                        <td class="text-center">Cuota ${a.numero_cuo}</td>
                        <td class="text-end">S/ ${cuoVal.toFixed(2)}</td>
                        <td class="text-end">S/ ${capVal.toFixed(2)}</td>
                        <td class="text-end">S/ ${intVal.toFixed(2)}</td>
                        <td class="text-end font-weight-bold text-dark">S/ ${montAmoVal.toFixed(2)}</td>
                        <td class="text-end font-weight-bold text-primary">S/ ${salRestVal.toFixed(2)}</td>
                    </tr>`;
                });
                $('#modal_tabla_body').html(html);

                // Total
                const totalVal = parseFloat(res.total_amortizado) || 0;
                $('#modal_total_monto').text('S/ ' + totalVal.toFixed(2));

                $('#modal_loading').addClass('d-none');
                $('#modal_contenido').removeClass('d-none');
            },
            error: function () {
                $('#modal_loading').addClass('d-none');
                $('#modal_error').removeClass('d-none');
            }
        });
    });
});
</script>
@endsection
