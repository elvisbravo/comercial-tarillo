@extends('layouts.main')

@section('title', 'Mi Historial de Cargas - Ventas Móviles')

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
    /* Lista de Cargas */
    .carga-card-item {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 14px;
        padding: 16px;
        margin-bottom: 12px;
        transition: all 0.2s ease;
    }
    .carga-card-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        border-color: #cbd5e1;
    }
    .badge-cargado {
        background: #d1fae5; color: #059669;
        border-radius: 20px; padding: 4px 10px;
        font-size: 11px; font-weight: 700;
    }
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
        background: #f1f5f9; border-radius: 8px; padding: 6px 12px;
        font-size: 11px; font-weight: 600; color: #475569;
    }
    .info-chip span { color: #1e293b; font-size: 13px; display: block; margin-top: 2px; }
    .empty-state { text-align: center; padding: 40px 20px; color: #94a3b8; }
    .empty-state i { font-size: 48px; display: block; margin-bottom: 8px; }
    .empty-state p { font-size: 13px; font-weight: 500; }
    
    .btn-ver-detalle {
        border-radius: 8px; font-weight: 600; font-size: 12px;
        padding: 6px 16px; transition: all 0.2s;
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
                        <i class="mdi mdi-history" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 text-white font-weight-bold" style="font-size: 16px;">Mi Historial de Cargas</h4>
                        <p class="text-white mb-0" style="font-size: 12px; opacity: 0.85;">Stock recibido en mi furgoneta.</p>
                    </div>
                </div>
                <a href="{{ route('vendedor.dashboard') }}" class="btn btn-light btn-action btn-sm">
                    <i class="mdi mdi-arrow-left me-1"></i> Panel
                </a>
            </div>
        </div>
    </div>

    {{-- Filtros por fecha --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('vendedor.historial_cargas') }}" id="form_filtros">
            <div class="row g-2 align-items-end">
                <div class="col-6 col-sm-5">
                    <label class="form-label font-weight-bold text-dark mb-1" style="font-size: 11px; text-transform: uppercase;">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}" style="border-radius: 8px; font-size: 12px;">
                </div>
                <div class="col-6 col-sm-5">
                    <label class="form-label font-weight-bold text-dark mb-1" style="font-size: 11px; text-transform: uppercase;">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}" style="border-radius: 8px; font-size: 12px;">
                </div>
                <div class="col-12 col-sm-2 d-flex gap-1 mt-2 mt-sm-0">
                    <button type="submit" class="btn btn-primary btn-action w-100 btn-sm py-2">
                        <i class="mdi mdi-magnify me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('vendedor.historial_cargas') }}" class="btn btn-light btn-action btn-sm py-2" title="Limpiar filtros">
                        <i class="mdi mdi-refresh"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Listado de Cargas (Mobile-First Card Based Layout) --}}
    <div class="row">
        <div class="col-12">
            @if(count($cargas) > 0)
                @foreach($cargas as $i => $carga)
                <div class="carga-card-item d-flex justify-content-between align-items-center">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge" style="background: #1e293b; color: white; border-radius: 6px; font-size: 11px; padding: 4px 8px; font-weight: 700; letter-spacing: 0.5px;">
                                {{ $carga->serie }}-{{ str_pad($carga->correlativo, 4, '0', STR_PAD_LEFT) }}
                            </span>
                            <span class="badge-cargado">
                                <i class="mdi mdi-check-circle me-1"></i> Cargado
                            </span>
                        </div>
                        <div class="text-dark font-weight-bold mb-1" style="font-size: 13px;">
                            Fecha: {{ \Carbon\Carbon::parse($carga->fecha)->format('d/m/Y') }}
                        </div>
                        <div class="text-muted" style="font-size: 11px;">
                            <i class="mdi mdi-clock-outline me-1"></i>{{ $carga->hora ? substr($carga->hora, 0, 5) : '—' }} &middot;
                            Autorizado por: {{ $carga->usuario_nombre ?? 'Administrador' }}
                        </div>
                    </div>
                    <div>
                        <button type="button"
                                class="btn btn-outline-primary btn-ver-detalle btn-sm"
                                data-id="{{ $carga->id }}"
                                title="Ver productos de esta carga">
                            <i class="mdi mdi-eye-outline me-1"></i> Ver
                        </button>
                    </div>
                </div>
                @endforeach
            @else
                <div class="card mobile-card p-4 text-center">
                    <div class="empty-state">
                        <i class="mdi mdi-truck-off-outline"></i>
                        <p>No tienes registros de cargas cargados con los filtros seleccionados.</p>
                        <a href="{{ route('vendedor.historial_cargas') }}" class="btn btn-light btn-action btn-sm mt-2">
                            <i class="mdi mdi-refresh me-1"></i> Ver todos
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>

{{-- Modal de Detalle --}}
<div class="modal fade" id="modalDetallesCarga" tabindex="-1" aria-labelledby="modalDetallesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header-custom">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 36px; height: 36px; background: rgba(255,255,255,0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="mdi mdi-clipboard-list-outline" style="font-size: 20px;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold" id="modalDetallesLabel" style="font-size: 15px;">Detalle de Carga</h5>
                        <small style="opacity: 0.75; font-size: 11px;" id="modal_subtitulo">Cargando información...</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                {{-- Loading --}}
                <div id="modal_loading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status" style="width: 1.5rem; height: 1.5rem;"></div>
                    <p class="text-muted mt-2 mb-0" style="font-size: 12px;">Cargando detalle de productos...</p>
                </div>
                {{-- Contenido --}}
                <div id="modal_contenido" class="d-none">
                    {{-- Chips de info --}}
                    <div class="row g-2 mb-3" id="modal_chips">
                        <div class="col-6">
                            <div class="info-chip">Código<span><strong id="chip_codigo">—</strong></span></div>
                        </div>
                        <div class="col-6">
                            <div class="info-chip">Fecha<span><strong id="chip_fecha">—</strong></span></div>
                        </div>
                    </div>
                    {{-- Tabla de productos --}}
                    <div class="table-responsive">
                        <table class="table table-modal mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 30px;">#</th>
                                    <th>Producto</th>
                                    <th class="text-center">Cant. Cargada</th>
                                </tr>
                            </thead>
                            <tbody id="modal_tabla_body">
                            </tbody>
                            <tfoot>
                                <tr style="background: #f8fafc;">
                                    <td colspan="2" class="font-weight-bold text-dark text-end" style="font-size: 12px; padding: 8px 12px;">TOTAL UNIDADES</td>
                                    <td class="text-center font-weight-bold text-primary" style="font-size: 13px;" id="modal_total_unidades">0</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                {{-- Error --}}
                <div id="modal_error" class="d-none text-center py-4">
                    <i class="mdi mdi-alert-circle-outline text-danger" style="font-size: 36px;"></i>
                    <p class="text-muted mt-2 mb-0" style="font-size: 12px;">No se pudo cargar el detalle. Intente de nuevo.</p>
                </div>
            </div>
            <div class="modal-footer border-0 px-3 pb-3 pt-0">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div>
                        <small class="text-muted" style="font-size: 11px;">
                            <i class="mdi mdi-shield-account-outline me-1"></i> Autorizado por: <strong id="chip_usuario">—</strong>
                        </small>
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
    @if(!request()->hasAny(['fecha_desde','fecha_hasta']))
    $('input[name="fecha_desde"]').val('{{ date('Y-m-d') }}');
    $('input[name="fecha_hasta"]').val('{{ date('Y-m-d') }}');
    @endif

    // Ver Detalle
    $(document).on('click', '.btn-ver-detalle', function () {
        const id = $(this).data('id');

        // Reset modal
        $('#modal_loading').removeClass('d-none');
        $('#modal_contenido').addClass('d-none');
        $('#modal_error').addClass('d-none');
        $('#modal_subtitulo').text('Cargando información...');
        $('#modal_tabla_body').html('');
        $('#chip_codigo, #chip_fecha, #chip_usuario').text('—');
        $('#modal_total_unidades').text('0');

        $('#modalDetallesCarga').modal('show');

        $.ajax({
            url: '{{ url("vendedor/historial-cargas/detalle") }}/' + id,
            type: 'GET',
            success: function (res) {
                // Cabecera
                const t = res.traslado;
                const fechaFmt = t.fecha ? t.fecha.substring(0, 10).split('-').reverse().join('/') : '—';
                const horaFmt = t.hora ? t.hora.substring(0, 5) : '';

                $('#chip_codigo').text(t.serie + '-' + String(t.correlativo).padStart(4, '0'));
                $('#chip_fecha').text(fechaFmt + (horaFmt ? ' ' + horaFmt : ''));
                $('#chip_usuario').text(t.usuario_nombre || '—');
                $('#modal_subtitulo').text('Carga ' + t.serie + '-' + String(t.correlativo).padStart(4, '0'));

                // Productos
                let html = '';
                res.productos.forEach(function (p, i) {
                    html += `<tr>
                        <td class="text-muted" style="font-size:11px;">${i + 1}</td>
                        <td class="font-weight-bold text-dark" style="font-size:12px;">${p.nomb_pro}</td>
                        <td class="text-center">
                            <span class="badge" style="background:#dbeafe; color:#1d4ed8; border-radius:6px; font-size:12px; padding:4px 10px; font-weight:700;">
                                ${p.cantidad}
                            </span>
                        </td>
                    </tr>`;
                });
                $('#modal_tabla_body').html(html);
                $('#modal_total_unidades').text(res.total_unidades);

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
