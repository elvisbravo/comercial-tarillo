@extends('layouts.main')

@section('title', 'Historial de Cargas Diarias - Ventas Móviles')

@section('css')
<style>
    .cargar-card {
        border-radius: 16px;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
        border: none;
        background: #ffffff;
    }
    .btn-action {
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    /* Tabla */
    .table-premium {
        border-radius: 12px;
        overflow: hidden;
        border: none;
    }
    .table-premium thead {
        background: #1e293b;
        color: white;
    }
    .table-premium thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
        padding: 13px 16px;
        border: none;
    }
    .table-premium tbody td {
        padding: 13px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-premium tbody tr:last-child td { border-bottom: none; }
    .table-premium tbody tr:hover { background: #f8fafc; }
    /* Badges */
    .badge-cargado {
        background: #d1fae5; color: #059669;
        border-radius: 20px; padding: 4px 12px;
        font-size: 11px; font-weight: 700;
    }
    .badge-anulado {
        background: #fee2e2; color: #dc2626;
        border-radius: 20px; padding: 4px 12px;
        font-size: 11px; font-weight: 700;
    }
    .badge-cerrada {
        background: #dbeafe; color: #1d4ed8;
        border-radius: 20px; padding: 4px 12px;
        font-size: 11px; font-weight: 700;
    }
    /* Filtros */
    .filter-card {
        background: #f8fafc;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        padding: 18px 20px;
        margin-bottom: 20px;
    }
    /* Stats */
    .stat-pill {
        background: linear-gradient(135deg, #0f2027, #2c5364);
        color: white;
        border-radius: 14px;
        padding: 14px 22px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .stat-pill i { font-size: 26px; opacity: 0.8; }
    .stat-pill .label { font-size: 11px; font-weight: 600; opacity: 0.7; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-pill .value { font-size: 22px; font-weight: 800; line-height: 1.1; }
    /* Modal */
    .modal-header-custom {
        background: linear-gradient(135deg, #0f2027, #2c5364);
        color: white;
        border-radius: 16px 16px 0 0;
        padding: 20px 24px;
    }
    .modal-content { border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
    .table-modal thead { background: #f1f5f9; }
    .table-modal thead th { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; padding: 10px 14px; border: none; }
    .table-modal tbody td { padding: 10px 14px; font-size: 13px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
    .table-modal tbody tr:last-child td { border-bottom: none; }
    .info-chip {
        background: #f1f5f9; border-radius: 8px; padding: 8px 14px;
        font-size: 12px; font-weight: 600; color: #475569;
    }
    .info-chip span { color: #1e293b; font-size: 14px; }
    .empty-state { text-align: center; padding: 60px 20px; color: #94a3b8; }
    .empty-state i { font-size: 56px; display: block; margin-bottom: 12px; }
    .empty-state p { font-size: 14px; font-weight: 500; }
    .btn-ver-detalle {
        border-radius: 8px; font-weight: 600; font-size: 12px;
        padding: 5px 14px; transition: all 0.2s;
    }
    .btn-anular-carga {
        border-radius: 8px; font-weight: 600; font-size: 12px;
        padding: 5px 14px; transition: all 0.2s;
    }
</style>
@endsection

@section('contenido')
<div class="container-fluid py-3">

    {{-- Navegación del Módulo --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; background: #ffffff;">
                <div class="card-body p-2 d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.asignar') }}" class="btn {{ Request::routeIs('admin.asignar') ? 'btn-primary' : 'btn-light' }} btn-action">
                        <i class="mdi mdi-map-marker-path me-1"></i> 1. Asignar Rutas
                    </a>
                    <a href="{{ route('admin.cargar_stock') }}" class="btn {{ Request::routeIs('admin.cargar_stock') ? 'btn-primary' : 'btn-light' }} btn-action">
                        <i class="mdi mdi-truck-load me-1"></i> 2. Cargar Furgonetas
                    </a>
                    <a href="{{ route('admin.liquidar') }}" class="btn {{ Request::routeIs('admin.liquidar') ? 'btn-primary' : 'btn-light' }} btn-action">
                        <i class="mdi mdi-cash-multiple me-1"></i> 3. Liquidar Caja
                    </a>
                    <a href="{{ route('admin.retorno') }}" class="btn {{ Request::routeIs('admin.retorno') ? 'btn-primary' : 'btn-light' }} btn-action">
                        <i class="mdi mdi-swap-horizontal-bold me-1"></i> 4. Retorno de Stock
                    </a>
                    <a href="{{ route('admin.cargar_stock.historial') }}" class="btn {{ Request::routeIs('admin.cargar_stock.historial') ? 'btn-warning' : 'btn-light' }} btn-action">
                        <i class="mdi mdi-history me-1"></i> Historial de Cargas
                    </a>
                    <a href="{{ route('admin.recojo') }}" class="btn {{ Request::routeIs('admin.recojo') ? 'btn-danger' : 'btn-light' }} btn-action">
                        <i class="mdi mdi-package-variant-remove me-1"></i> Recojo de Mercadería
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Título --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18 text-primary font-weight-bold">
                    <i class="mdi mdi-history me-2"></i> Historial de Carga Diaria de Stock a Furgonetas
                </h4>
            </div>
        </div>
    </div>

    {{-- Estadísticas rápidas --}}
    <div class="row mb-4 g-3" id="historial-stats">
        <div class="col-sm-6 col-lg-3">
            <div class="stat-pill">
                <i class="mdi mdi-truck-delivery-outline"></i>
                <div>
                    <div class="label">Total de Cargas</div>
                    <div class="value">{{ count($cargas) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-pill" style="background: linear-gradient(135deg, #065f46, #10b981);">
                <i class="mdi mdi-calendar-today"></i>
                <div>
                    <div class="label">Cargas Hoy</div>
                    <div class="value">{{ $cargas->where('fecha', date('Y-m-d'))->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-pill" style="background: linear-gradient(135deg, #7c3aed, #a78bfa);">
                <i class="mdi mdi-account-group-outline"></i>
                <div>
                    <div class="label">Vendedores con Carga</div>
                    <div class="value">{{ $cargas->whereNotNull('vendedor_nombre')->pluck('vendedor_nombre')->unique()->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-pill" style="background: linear-gradient(135deg, #b45309, #f59e0b);">
                <i class="mdi mdi-calendar-month-outline"></i>
                <div>
                    <div class="label">Cargas Este Mes</div>
                    <div class="value">{{ $cargas->where('fecha', '>=', date('Y-m-01'))->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="filter-card">
        <form id="form_filtros">
            <div class="row g-3 align-items-end">
                <div class="col-md-4 col-sm-6">
                    <label class="form-label font-weight-bold text-dark mb-1" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Vendedor</label>
                    <select id="filtro_vendedor_id" class="form-select" style="border-radius: 8px; font-size: 13px;">
                        <option value="">— Todos los vendedores —</option>
                        @foreach($vendedores as $v)
                            <option value="{{ $v->usuario_id }}" {{ request('vendedor_id') == $v->usuario_id ? 'selected' : '' }}>
                                {{ $v->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label font-weight-bold text-dark mb-1" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Desde</label>
                    <input type="date" id="filtro_fecha_desde" class="form-control" value="{{ request('fecha_desde', date('Y-m-d')) }}" style="border-radius: 8px; font-size: 13px;">
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label font-weight-bold text-dark mb-1" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Hasta</label>
                    <input type="date" id="filtro_fecha_hasta" class="form-control" value="{{ request('fecha_hasta', date('Y-m-d')) }}" style="border-radius: 8px; font-size: 13px;">
                </div>
                <div class="col-md-2 col-sm-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-action w-100">
                        <i class="mdi mdi-magnify me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.cargar_stock.historial') }}" class="btn btn-light btn-action" title="Limpiar filtros">
                        <i class="mdi mdi-refresh"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Tabla de Historial --}}
    <div id="historial-table-card">
    <div class="card cargar-card">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="font-weight-bold text-dark mb-0">Registro de Cargas</h5>
                <p class="text-muted font-size-12 mb-0">Todas las cargas de stock realizadas a furgonetas en esta sede.</p>
            </div>
            <span class="badge bg-soft-primary text-primary font-size-12 px-3 py-2">
                {{ count($cargas) }} registro(s)
            </span>
        </div>
        <div class="card-body p-4">
            @if(count($cargas) > 0)
            <div class="table-responsive">
                <table class="table table-premium align-middle mb-0" id="tabla_historial">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Fecha y Hora</th>
                            <th>Código</th>
                            <th>Vendedor / Furgoneta</th>
                            <th>Autorizado por</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center" style="width: 160px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cargas as $i => $carga)
                        <tr>
                            <td class="font-weight-bold text-muted" style="font-size: 12px;">{{ $i + 1 }}</td>
                            <td>
                                <div class="font-weight-bold text-dark" style="font-size: 14px;">
                                    {{ \Carbon\Carbon::parse($carga->fecha)->format('d/m/Y') }}
                                </div>
                                @if($carga->hora)
                                <div class="text-muted" style="font-size: 11px;">
                                    <i class="mdi mdi-clock-outline me-1"></i>{{ substr($carga->hora, 0, 5) }}
                                </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge" style="background: #1e293b; color: white; border-radius: 8px; font-size: 12px; padding: 5px 10px; font-weight: 700; letter-spacing: 0.5px;">
                                    {{ $carga->serie }}-{{ str_pad($carga->correlativo, 4, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>
                            <td>
                                @if($carga->vendedor_nombre)
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 36px; height: 36px; background: linear-gradient(135deg, #3b82f6, #6366f1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 14px; flex-shrink: 0;">
                                            {{ strtoupper(substr($carga->vendedor_nombre, 0, 1)) }}
                                        </div>
                                        <span class="font-weight-bold text-dark" style="font-size: 13px;">{{ $carga->vendedor_nombre }}</span>
                                    </div>
                                @else
                                    <span class="text-muted font-size-12"><i class="mdi mdi-account-off-outline me-1"></i>Sin registro</span>
                                @endif
                            </td>
                            <td>
                                @if($carga->usuario_nombre)
                                    <span class="text-dark" style="font-size: 13px;">
                                        <i class="mdi mdi-shield-account-outline me-1 text-muted"></i>{{ $carga->usuario_nombre }}
                                    </span>
                                @else
                                    <span class="text-muted font-size-12">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($carga->estado == 1)
                                    <span class="badge-cargado">
                                        <i class="mdi mdi-check-circle me-1"></i> Cargado
                                    </span>
                                @elseif($carga->estado == 2)
                                    <span class="badge-cerrada">
                                        <i class="mdi mdi-truck-check-outline me-1"></i> Cerrada (Retorno)
                                    </span>
                                @else
                                    <span class="badge-anulado">
                                        <i class="mdi mdi-close-circle me-1"></i> Anulado
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <button type="button"
                                            class="btn btn-outline-primary btn-ver-detalle"
                                            data-id="{{ $carga->id }}"
                                            title="Ver productos de esta carga">
                                        <i class="mdi mdi-eye-outline me-1"></i> Ver
                                    </button>
                                    @if($carga->estado == 1)
                                    <button type="button"
                                            class="btn btn-outline-success btn-agregar-productos"
                                            data-id="{{ $carga->id }}"
                                            data-vendedor="{{ $carga->vendedor_nombre ?? 'Sin registro' }}"
                                            data-codigo="{{ $carga->serie }}-{{ str_pad($carga->correlativo, 4, '0', STR_PAD_LEFT) }}"
                                            title="Agregar más productos a esta carga">
                                        <i class="mdi mdi-plus-circle-outline me-1"></i> Agregar
                                    </button>
                                    @endif
                                    @if($carga->estado == 1)
                                    <button type="button"
                                            class="btn btn-outline-danger btn-anular-carga"
                                            data-id="{{ $carga->id }}"
                                            data-codigo="{{ $carga->serie }}-{{ str_pad($carga->correlativo, 4, '0', STR_PAD_LEFT) }}"
                                            data-vendedor="{{ $carga->vendedor_nombre ?? 'Sin registro' }}"
                                            title="Anular esta carga y revertir stock">
                                        <i class="mdi mdi-close-circle-outline me-1"></i> Anular
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <i class="mdi mdi-truck-off-outline"></i>
                <p>No se encontraron registros de cargas con los filtros seleccionados.</p>
                <a href="{{ route('admin.cargar_stock.historial') }}" class="btn btn-light btn-action mt-2">
                    <i class="mdi mdi-refresh me-1"></i> Ver todos los registros
                </a>
            </div>
            @endif
        </div>
    </div>
    </div>

</div>

{{-- Modal de Detalle --}}
<div class="modal fade" id="modalDetallesCarga" tabindex="-1" aria-labelledby="modalDetallesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header-custom">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 42px; height: 42px; background: rgba(255,255,255,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="mdi mdi-clipboard-list-outline" style="font-size: 22px;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold" id="modalDetallesLabel">Detalle de Carga</h5>
                        <small style="opacity: 0.75;" id="modal_subtitulo">Cargando información...</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                {{-- Loading --}}
                <div id="modal_loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted mt-3 mb-0">Cargando detalle de productos...</p>
                </div>
                {{-- Contenido --}}
                <div id="modal_contenido" class="d-none">
                    {{-- Chips de info --}}
                    <div class="row g-2 mb-4" id="modal_chips">
                        <div class="col-6 col-sm-3">
                            <div class="info-chip">Código<br><span id="chip_codigo">—</span></div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="info-chip">Fecha<br><span id="chip_fecha">—</span></div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="info-chip">Vendedor<br><span id="chip_vendedor">—</span></div>
                        </div>
                    </div>
                    {{-- Tabla de productos --}}
                    <div class="table-responsive">
                        <table class="table table-modal mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Producto</th>
                                    <th class="text-center">Cant. Cargada</th>
                                    <th class="text-end">P. Unit. (S/)</th>
                                    <th class="text-end">Subtotal (S/)</th>
                                </tr>
                            </thead>
                            <tbody id="modal_tabla_body">
                            </tbody>
                            <tfoot>
                                <tr style="background: #f8fafc;">
                                    <td colspan="2" class="font-weight-bold text-dark text-end" style="font-size: 13px; padding: 12px 14px;">TOTALES</td>
                                    <td class="text-center font-weight-bold text-primary" style="font-size: 14px;" id="modal_total_unidades">0</td>
                                    <td></td>
                                    <td class="text-end font-weight-bold text-success" style="font-size: 14px;" id="modal_total_valor">S/ 0.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                {{-- Error --}}
                <div id="modal_error" class="d-none text-center py-5">
                    <i class="mdi mdi-alert-circle-outline text-danger" style="font-size: 48px;"></i>
                    <p class="text-muted mt-3 mb-0">No se pudo cargar el detalle. Intente de nuevo.</p>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div>
                        <small class="text-muted">
                            <i class="mdi mdi-shield-account-outline me-1"></i> Autorizado por: <strong id="chip_usuario">—</strong>
                        </small>
                    </div>
                    <button type="button" class="btn btn-light btn-action" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Agregar Productos --}}
<div class="modal fade" id="modalAgregarProductos" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #065f46, #10b981); border-radius: 16px 16px 0 0; padding: 20px 24px;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="mdi mdi-plus-circle-outline" style="font-size: 22px; color: white;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-white" id="modalAgregarLabel">Agregar Productos a la Carga</h5>
                        <small style="color: rgba(255,255,255,0.75);">Código: <span id="agregar_codigo">—</span></small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="agregar_traslado_id" value="">
                <input type="hidden" id="agregar_vendedor_id" value="">

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Vendedor</label>
                    <input type="text" class="form-control" id="agregar_vendedor_nombre" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Buscar Producto</label>
                    <select id="select_producto_buscar" class="form-select" style="border-radius: 8px;">
                        <option value="">— Seleccionar producto —</option>
                    </select>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0">
                        <thead style="background: #f1f5f9;">
                            <tr>
                                <th style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Producto</th>
                                <th style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; width: 100px;">Cantidad</th>
                                <th style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="agregar_productos_lista">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0 d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btn_confirmar_agregar" style="border-radius: 10px; font-weight: 600;">
                    <i class="mdi mdi-check-circle me-1"></i> Agregar Productos
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal de Confirmación de Anulación --}}
<div class="modal fade" id="modalAnularCarga" tabindex="-1" aria-labelledby="modalAnularLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #7f1d1d, #dc2626); border-radius: 16px 16px 0 0; padding: 20px 24px;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="mdi mdi-alert-outline" style="font-size: 22px; color: white;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-white" id="modalAnularLabel">Anular Carga de Stock</h5>
                        <small style="color: rgba(255,255,255,0.75);">Esta acción revertirá el stock al almacén</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-warning border-0 rounded-3 mb-3" style="background: #fef3c7;">
                    <i class="mdi mdi-information-outline me-2 text-warning"></i>
                    <strong>¿Está seguro de anular esta carga?</strong>
                </div>
                <p class="text-muted mb-1" style="font-size: 13px;">Código: <strong id="anular_codigo" class="text-dark">—</strong></p>
                <p class="text-muted mb-3" style="font-size: 13px;">Vendedor: <strong id="anular_vendedor" class="text-dark">—</strong></p>
                <p class="text-dark" style="font-size: 13px;">
                    El stock de todos los productos de esta carga será <strong>devuelto al almacén principal</strong> y el kardex quedará registrado con la anulación.
                </p>
                <div class="alert alert-danger border-0 rounded-3" style="font-size: 12px; background: #fee2e2;">
                    <i class="mdi mdi-alert me-1"></i>
                    Si el vendedor ya vendió parte del stock, <strong>no se podrá anular</strong> hasta regularizar el inventario.
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0 d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btn_confirmar_anular" style="border-radius: 10px; font-weight: 600;">
                    <i class="mdi mdi-close-circle me-1"></i> Sí, Anular Carga
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
const csrfToken = '{{ csrf_token() }}';
const urlHistorialDatos = "{{ route('admin.cargar_stock.historial.datos') }}";

$(document).ready(function () {
    $(".loader").fadeOut("slow");

    // Filtrar por fetch POST, sin recargar la página ni tocar la URL
    $('#form_filtros').on('submit', async function (e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        try {
            const response = await fetch(urlHistorialDatos, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    vendedor_id: $('#filtro_vendedor_id').val(),
                    fecha_desde: $('#filtro_fecha_desde').val(),
                    fecha_hasta: $('#filtro_fecha_hasta').val()
                })
            });
            if (!response.ok) throw new Error('Error al filtrar el historial.');
            const htmlText = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(htmlText, 'text/html');
            $('#historial-stats').html(doc.querySelector('#historial-stats').innerHTML);
            $('#historial-table-card').html(doc.querySelector('#historial-table-card').innerHTML);
        } catch (error) {
            console.error(error);
            $('body').prepend(
                `<div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 shadow-lg" style="z-index:9999; border-radius:12px; min-width:360px;">
                    <i class="mdi mdi-alert-circle me-2"></i> No se pudo filtrar el historial. Intente de nuevo.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`
            );
        } finally {
            $btn.prop('disabled', false).html(originalHtml);
        }
    });

    // Ver Detalle
    $(document).on('click', '.btn-ver-detalle', function () {
        const id = $(this).data('id');

        // Reset modal
        $('#modal_loading').removeClass('d-none');
        $('#modal_contenido').addClass('d-none');
        $('#modal_error').addClass('d-none');
        $('#modal_subtitulo').text('Cargando información...');
        $('#modal_tabla_body').html('');
        $('#chip_codigo, #chip_fecha, #chip_vendedor, #chip_usuario').text('—');
        $('#modal_total_unidades').text('0');
        $('#modal_total_valor').text('S/ 0.00');

        $('#modalDetallesCarga').modal('show');

        $.ajax({
            url: '{{ url("ventas-moviles/historial-cargas/detalle") }}/' + id,
            type: 'GET',
            success: function (res) {
                // Cabecera
                const t = res.traslado;
                const fechaFmt = t.fecha ? t.fecha.substring(0, 10).split('-').reverse().join('/') : '—';
                const horaFmt = t.hora ? t.hora.substring(0, 5) : '';

                $('#chip_codigo').text(t.serie + '-' + String(t.correlativo).padStart(4, '0'));
                $('#chip_fecha').text(fechaFmt + (horaFmt ? ' ' + horaFmt : ''));
                $('#chip_vendedor').text(t.vendedor_nombre || 'Sin registro');
                $('#chip_usuario').text(t.usuario_nombre || '—');
                $('#modal_subtitulo').text('Carga ' + t.serie + '-' + String(t.correlativo).padStart(4, '0') + ' · ' + (t.vendedor_nombre || ''));

                // Productos
                let html = '';
                res.productos.forEach(function (p, i) {
                    const precioFmt  = parseFloat(p.precio_unitario).toFixed(2);
                    const subtotalFmt = parseFloat(p.subtotal).toFixed(2);
                    html += `<tr>
                        <td class="text-muted" style="font-size:12px;">${i + 1}</td>
                        <td class="font-weight-bold text-dark" style="font-size:13px;">${p.nomb_pro}</td>
                        <td class="text-center">
                            <span class="badge" style="background:#dbeafe; color:#1d4ed8; border-radius:8px; font-size:13px; padding:5px 12px; font-weight:700;">
                                ${p.cantidad}
                            </span>
                        </td>
                        <td class="text-end text-muted" style="font-size:13px;">S/ ${precioFmt}</td>
                        <td class="text-end font-weight-bold text-dark" style="font-size:13px;">S/ ${subtotalFmt}</td>
                    </tr>`;
                });
                $('#modal_tabla_body').html(html);
                $('#modal_total_unidades').text(res.total_unidades);
                $('#modal_total_valor').text('S/ ' + parseFloat(res.total_valor).toFixed(2));

                $('#modal_loading').addClass('d-none');
                $('#modal_contenido').removeClass('d-none');
            },
            error: function () {
                $('#modal_loading').addClass('d-none');
                $('#modal_error').removeClass('d-none');
            }
        });
    });

    // ---- ANULAR CARGA ----
    let anularCargaId = null;

    $(document).on('click', '.btn-anular-carga', function () {
        anularCargaId = $(this).data('id');
        const codigo   = $(this).data('codigo');
        const vendedor = $(this).data('vendedor');

        $('#anular_codigo').text(codigo);
        $('#anular_vendedor').text(vendedor);
        $('#btn_confirmar_anular').prop('disabled', false).html('<i class="mdi mdi-close-circle me-1"></i> Sí, Anular Carga');

        $('#modalAnularCarga').modal('show');
    });

    $('#btn_confirmar_anular').on('click', function () {
        if (!anularCargaId) return;

        const $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Anulando...');

        $.ajax({
            url: '{{ url("ventas-moviles/historial-cargas/anular") }}/' + anularCargaId,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function (res) {
                $('#modalAnularCarga').modal('hide');

                // Mostrar alerta de éxito y recargar tabla
                $('body').prepend(
                    `<div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 shadow-lg" style="z-index:9999; border-radius:12px; min-width:360px;">
                        <i class="mdi mdi-check-circle me-2"></i> ${res.success}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`
                );

                setTimeout(function () { location.reload(); }, 2200);
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html('<i class="mdi mdi-close-circle me-1"></i> Sí, Anular Carga');
                const msg = xhr.responseJSON ? xhr.responseJSON.error : 'Error desconocido al intentar anular.';

                $('#modalAnularCarga .modal-body').prepend(
                    `<div class="alert alert-danger border-0 rounded-3 mb-3 anular-error-msg" style="font-size:13px;">
                        <i class="mdi mdi-alert me-1"></i> ${msg}
                    </div>`
                );

                // Auto-remover error anterior si había
                setTimeout(function () { $('.anular-error-msg').fadeOut(400, function(){ $(this).remove(); }); }, 6000);
            }
        });
    });

    // Limpiar errores al cerrar el modal de anulación
    $('#modalAnularCarga').on('hidden.bs.modal', function () {
        $('.anular-error-msg').remove();
        anularCargaId = null;
    });

    // ---- AGREGAR PRODUCTOS ----
    let productosAgregar = [];

    $(document).on('click', '.btn-agregar-productos', function () {
        const id = $(this).data('id');
        const codigo = $(this).data('codigo');
        const vendedor = $(this).data('vendedor');

        $('#agregar_traslado_id').val(id);
        $('#agregar_codigo').text(codigo);
        $('#agregar_vendedor_nombre').val(vendedor);
        $('#agregar_productos_lista').html('');
        productosAgregar = [];

        // Cargar productos disponibles en el almacén Stock
        $('#select_producto_buscar').html('<option value="">Cargando...</option>');
        $.ajax({
            url: '{{ url("ventas-moviles/productos-stock") }}',
            type: 'GET',
            success: function (res) {
                let html = '<option value="">— Seleccionar producto —</option>';
                res.forEach(function (p) {
                    html += '<option value="' + p.id + '" data-precio="' + p.precio_contado + '">' + p.nomb_pro + ' (Stock: ' + p.stock + ')</option>';
                });
                $('#select_producto_buscar').html(html);
            },
            error: function () {
                $('#select_producto_buscar').html('<option value="">Error al cargar</option>');
            }
        });

        $('#modalAgregarProductos').modal('show');
    });

    // Agregar producto a la lista
    $('#select_producto_buscar').on('change', function () {
        const val = $(this).val();
        if (!val) return;

        const text = $(this).find('option:selected').text();
        const precio = $(this).find('option:selected').data('precio');
        const nombre = text.split(' (Stock:')[0].trim();

        // Verificar si ya está en la lista
        if (productosAgregar.find(p => p.id == val)) {
            alert('Este producto ya está en la lista');
            return;
        }

        productosAgregar.push({ id: val, nombre: nombre, cantidad: 1, precio: precio });
        renderListaAgregar();
        $(this).val('');
    });

    function renderListaAgregar() {
        let html = '';
        productosAgregar.forEach(function (p, i) {
            html += '<tr>';
            html += '<td class="fw-bold text-dark" style="font-size: 13px;">' + p.nombre + '</td>';
            html += '<td><input type="number" class="form-control form-control-sm text-center" value="' + p.cantidad + '" min="1" onchange="productosAgregar[' + i + '].cantidad = parseInt(this.value) || 1"></td>';
            html += '<td><button type="button" class="btn btn-sm btn-outline-danger" onclick="productosAgregar.splice(' + i + ', 1); renderListaAgregar();"><i class="mdi mdi-trash-can-outline"></i></button></td>';
            html += '</tr>';
        });

        if (productosAgregar.length === 0) {
            html = '<tr><td colspan="3" class="text-center text-muted" style="padding: 20px;">Agregue productos usando el buscador</td></tr>';
        }

        $('#agregar_productos_lista').html(html);
    }

    // Confirmar agregar productos
    $('#btn_confirmar_agregar').on('click', function () {
        if (productosAgregar.length === 0) {
            alert('Agregue al menos un producto');
            return;
        }

        const $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Procesando...');

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('traslado_id', $('#agregar_traslado_id').val());
        formData.append('productos', JSON.stringify(productosAgregar));

        $.ajax({
            url: '{{ url("ventas-moviles/historial-cargas/agregar-productos") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                $('#modalAgregarProductos').modal('hide');
                var alertHtml = '<div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 shadow-lg" style="z-index:9999; border-radius:12px; min-width:360px;">' +
                    '<i class="mdi mdi-check-circle me-2"></i> ' + res.success +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                $('body').prepend(alertHtml);
                setTimeout(function () { location.reload(); }, 1800);
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html('<i class="mdi mdi-check-circle me-1"></i> Agregar Productos');
                const msg = xhr.responseJSON ? xhr.responseJSON.error : 'Error desconocido';
                alert(msg);
            }
        });
    });
});
</script>
@endsection
