@extends('layouts.main')

@section('title', 'Mi Historial de Ventas - Ventas Móviles')

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
    /* Lista de Ventas */
    .venta-card-item {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 14px;
        padding: 16px;
        margin-bottom: 12px;
        transition: all 0.2s ease;
    }
    .venta-card-item:hover {
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
    .badge-activo { background: #d1fae5; color: #059669; }
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
                        <i class="mdi mdi-receipt" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 text-white font-weight-bold" style="font-size: 16px;">Mi Historial de Ventas</h4>
                        <p class="text-white mb-0" style="font-size: 12px; opacity: 0.85;">Registro de ventas que he realizado.</p>
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
        <form method="GET" action="{{ route('vendedor.historial_ventas') }}" id="form_filtros">
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
                    <label class="form-label font-weight-bold text-dark mb-1" style="font-size: 11px; text-transform: uppercase;">Buscar Cliente / Comprobante</label>
                    <div class="input-group">
                        <input type="text" name="buscar" class="form-control" value="{{ request('buscar') }}" placeholder="Nombre, RUC, Nro..." style="border-radius: 8px 0 0 8px; font-size: 12px;">
                        <button type="submit" class="btn btn-primary btn-action btn-sm px-3" style="border-radius: 0 8px 8px 0;">
                            <i class="mdi mdi-magnify"></i>
                        </button>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end gap-1 mt-2">
                    <button type="submit" class="btn btn-primary btn-action btn-sm px-4">
                        <i class="mdi mdi-filter-variant me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('vendedor.historial_ventas') }}" class="btn btn-light btn-action btn-sm" title="Limpiar filtros">
                        <i class="mdi mdi-refresh me-1"></i> Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Listado de Ventas --}}
    <div class="row">
        <div class="col-12">
            @if(count($ventas) > 0)
                @foreach($ventas as $venta)
                @php
                    $isAnulado = (!is_null($venta->fecha_eliminacion) || $venta->estado_nota == 2);
                    $isLiquidado = ($venta->estado_liquidacion === 'LIQUIDADO');
                @endphp
                <div class="venta-card-item">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge" style="background: #1e293b; color: white; border-radius: 6px; font-size: 11px; padding: 4px 8px; font-weight: 700; letter-spacing: 0.5px;">
                                {{ $venta->tipo_comprobante }}: {{ $venta->serie_comprobante }}-{{ str_pad($venta->numero_comprobante, 6, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>
                        <div class="d-flex flex-wrap gap-1 align-items-center">
                            {{-- Badge Pago --}}
                            <span class="badge-status badge-pago">
                                <i class="mdi mdi-credit-card-outline"></i> {{ $venta->tipo_pago ?? ($venta->tipo_pago_id == 2 ? 'Crédito' : 'Contado') }}
                            </span>
                            
                            {{-- Badge Liquidacion --}}
                            @if($isLiquidado)
                                <span class="badge-status badge-liquidado" title="Venta Liquidada">
                                    <i class="mdi mdi-cash-check"></i> Liquidado
                                </span>
                            @else
                                <span class="badge-status badge-pendiente" title="Venta Pendiente de Liquidación">
                                    <i class="mdi mdi-cash-clock"></i> Pendiente Liq.
                                </span>
                            @endif

                            {{-- Badge Estado --}}
                            @if($isAnulado)
                                <span class="badge-status badge-anulado">
                                    <i class="mdi mdi-cancel"></i> Anulado
                                </span>
                            @else
                                <span class="badge-status badge-activo">
                                    <i class="mdi mdi-check-circle"></i> Activo
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-12 col-md-8">
                            <div class="text-dark font-weight-bold mb-1" style="font-size: 13px;">
                                <i class="mdi mdi-account-outline text-muted me-1"></i>{{ $venta->nombre_cliente }}
                            </div>
                            <div class="text-muted" style="font-size: 11px;">
                                <span>Doc: {{ $venta->documento_cliente ?? '—' }}</span> &middot; 
                                <span><i class="mdi mdi-calendar-blank-outline me-1"></i>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }} {{ substr($venta->hora, 0, 5) }}</span>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 text-start text-md-end">
                            <span class="text-muted font-size-11 d-md-block">Total Venta</span>
                            <span class="text-dark font-weight-bold" style="font-size: 16px;">S/ {{ number_format($venta->monto, 2) }}</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-2 mt-2">
                        <button type="button"
                                class="btn btn-outline-primary btn-ver-detalle btn-sm px-3"
                                data-id="{{ $venta->id }}">
                            <i class="mdi mdi-eye-outline me-1"></i> Detalles
                        </button>
                        <a href="{{ url('venta/ticket', $venta->id) }}" 
                           target="_blank" 
                           class="btn btn-outline-secondary btn-sm px-3 btn-action" 
                           title="Ver / Imprimir Ticket">
                            <i class="mdi mdi-printer me-1"></i> Ticket
                        </a>
                    </div>
                </div>
                @endforeach
            @else
                <div class="card mobile-card p-4 text-center">
                    <div class="empty-state">
                        <i class="mdi mdi-receipt-text-minus-outline"></i>
                        <p>No tienes registros de ventas con los filtros seleccionados.</p>
                        <a href="{{ route('vendedor.historial_ventas') }}" class="btn btn-light btn-action btn-sm mt-2">
                            <i class="mdi mdi-refresh me-1"></i> Ver todas
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>

{{-- Modal de Detalle de Venta --}}
<div class="modal fade" id="modalDetalleVenta" tabindex="-1" aria-labelledby="modalDetallesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header-custom">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 36px; height: 36px; background: rgba(255,255,255,0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="mdi mdi-receipt" style="font-size: 20px;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold" id="modalDetallesLabel" style="font-size: 15px;">Detalle de Venta</h5>
                        <small style="opacity: 0.75; font-size: 11px;" id="modal_subtitulo">Cargando información...</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                {{-- Loading --}}
                <div id="modal_loading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status" style="width: 1.5rem; height: 1.5rem;"></div>
                    <p class="text-muted mt-2 mb-0" style="font-size: 12px;">Cargando detalle de venta...</p>
                </div>
                {{-- Contenido --}}
                <div id="modal_contenido" class="d-none">
                    {{-- Chips de info --}}
                    <div class="row g-2 mb-3" id="modal_chips">
                        <div class="col-6 col-md-3">
                            <div class="info-chip">Comprobante<span id="chip_comprobante">—</span></div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="info-chip">Fecha / Hora<span id="chip_fecha">—</span></div>
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
                    </div>
                    {{-- Tabla de productos --}}
                    <div class="table-responsive">
                        <table class="table table-modal mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 30px;">#</th>
                                    <th>Producto</th>
                                    <th class="text-center">Cant</th>
                                    <th class="text-end">P. Unit</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="modal_tabla_body">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="font-weight-bold text-dark text-end" style="font-size: 12px; padding: 8px 12px;">Descuento</td>
                                    <td class="text-end font-weight-bold text-danger" style="font-size: 13px;" id="modal_descuento">S/ 0.00</td>
                                </tr>
                                <tr style="background: #f8fafc;">
                                    <td colspan="4" class="font-weight-bold text-dark text-end" style="font-size: 12px; padding: 8px 12px;">TOTAL VENTA</td>
                                    <td class="text-end font-weight-bold text-primary" style="font-size: 13px;" id="modal_total_monto">S/ 0.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                {{-- Error --}}
                <div id="modal_error" class="d-none text-center py-4">
                    <i class="mdi mdi-alert-circle-outline text-danger" style="font-size: 36px;"></i>
                    <p class="text-muted mt-2 mb-0" style="font-size: 12px;">No se pudo cargar el detalle de la venta. Intente de nuevo.</p>
                </div>
            </div>
            <div class="modal-footer border-0 px-3 pb-3 pt-0">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div>
                        <a href="#" id="modal_link_ticket" target="_blank" class="btn btn-outline-secondary btn-action btn-sm">
                            <i class="mdi mdi-printer me-1"></i> Imprimir Ticket
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

    // Ver Detalle de la Venta
    $(document).on('click', '.btn-ver-detalle', function () {
        const id = $(this).data('id');

        // Reset modal
        $('#modal_loading').removeClass('d-none');
        $('#modal_contenido').addClass('d-none');
        $('#modal_error').addClass('d-none');
        $('#modal_subtitulo').text('Cargando información...');
        $('#modal_tabla_body').html('');
        $('#chip_comprobante, #chip_fecha, #chip_pago, #chip_liquidacion, #chip_cliente').text('—');
        $('#modal_descuento').text('S/ 0.00');
        $('#modal_total_monto').text('S/ 0.00');
        $('#modal_link_ticket').attr('href', '#');

        $('#modalDetalleVenta').modal('show');

        $.ajax({
            url: '{{ url("vendedor/historial-ventas/detalle") }}/' + id,
            type: 'GET',
            success: function (res) {
                const v = res.venta;
                const fechaFmt = v.fecha ? v.fecha.substring(0, 10).split('-').reverse().join('/') : '—';
                const horaFmt = v.hora ? v.hora.substring(0, 5) : '';

                // Cabecera e información básica
                const comprobanteFmt = v.tipo_comprobante + ' ' + v.serie_comprobante + '-' + String(v.numero_comprobante).padStart(6, '0');
                $('#chip_comprobante').text(comprobanteFmt);
                $('#chip_fecha').text(fechaFmt + ' ' + horaFmt);
                $('#chip_pago').text(v.tipo_pago || (v.tipo_pago_id == 2 ? 'Crédito' : 'Contado'));
                
                // Liquidacion chip
                if (v.estado_liquidacion === 'LIQUIDADO') {
                    $('#chip_liquidacion').html('<span class="text-success"><i class="mdi mdi-check-circle me-1"></i>LIQUIDADO</span>');
                } else {
                    $('#chip_liquidacion').html('<span class="text-warning"><i class="mdi mdi-clock-outline me-1"></i>PENDIENTE</span>');
                }
                
                // Cliente chip
                $('#chip_cliente').text(v.nombre_cliente + (v.documento_cliente ? ' (Doc: ' + v.documento_cliente + ')' : ''));
                $('#modal_subtitulo').text(comprobanteFmt);

                // Configurar enlace de ticket en modal
                $('#modal_link_ticket').attr('href', '{{ url("venta/ticket") }}/' + v.id);

                // Llenar tabla de productos
                let html = '';
                res.productos.forEach(function (p, i) {
                    const priceVal = parseFloat(p.precio) || 0;
                    const subtotalVal = parseFloat(p.subtotal) || (p.cantidad * priceVal);
                    
                    html += `<tr>
                        <td class="text-muted" style="font-size:11px;">${i + 1}</td>
                        <td class="font-weight-bold text-dark" style="font-size:12px;">${p.nomb_pro}</td>
                        <td class="text-center font-weight-bold text-dark">${p.cantidad}</td>
                        <td class="text-end">S/ ${priceVal.toFixed(2)}</td>
                        <td class="text-end font-weight-bold text-dark">S/ ${subtotalVal.toFixed(2)}</td>
                    </tr>`;
                });
                $('#modal_tabla_body').html(html);

                // Descuento y total
                const descVal = parseFloat(v.descuento) || 0;
                const totalVal = parseFloat(res.total_monto) || 0;
                
                $('#modal_descuento').text('S/ ' + descVal.toFixed(2));
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
