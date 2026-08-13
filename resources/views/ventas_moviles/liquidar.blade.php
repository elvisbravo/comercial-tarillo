@extends('layouts.main')

@section('title', 'Dashboard de Liquidación de Caja - Furgonetas')

@section('css')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #119f82 0%, #0d705c 100%);
        --info-gradient: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #b45309 100%);
        --danger-gradient: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
        --success-gradient: linear-gradient(135deg, #06b6d4 0%, #0e7490 100%);
        --glass-bg: rgba(255, 255, 255, 0.85);
        --glass-border: rgba(255, 255, 255, 0.4);
    }
    
    .dashboard-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
    }

    .metric-card {
        border: none;
        border-radius: 16px;
        color: white;
        padding: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.07);
    }

    .metric-card::before {
        content: '';
        position: absolute;
        width: 150px;
        height: 150px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 50%;
        top: -30px;
        right: -30px;
    }

    .metric-title {
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.85;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .metric-value {
        font-size: 32px;
        font-weight: 800;
        letter-spacing: -0.5px;
    }

    .metric-icon {
        font-size: 38px;
        position: absolute;
        bottom: 15px;
        right: 20px;
        opacity: 0.25;
    }

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
        padding: 15px;
        border: none;
    }

    .table-premium tbody td {
        padding: 15px;
        vertical-align: middle;
        border-color: #f1f5f9;
    }

    .badge-premium {
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
    }

    .btn-action {
        border-radius: 10px;
        font-weight: 600;
        font-size: 13px;
        padding: 8px 16px;
        transition: all 0.2s ease;
    }

    .efectivo-badge {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        font-weight: 800;
        font-size: 14px;
        padding: 8px 14px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
    }

    /* El tema admin le baja el color/opacidad al título de los modales por
       defecto, dejándolo poco legible sobre el header oscuro — se fuerza a
       blanco opaco en los modales de esta página. */
    .modal .modal-header .modal-title {
        color: #fff !important;
        opacity: 1 !important;
    }
</style>
@endsection

@section('contenido')
<div class="container-fluid py-4">
    <!-- Navegación del Módulo de Ventas Móviles -->
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

    <!-- Encabezado del Dashboard -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="font-weight-bold text-dark mb-1" style="letter-spacing: -0.5px;">
                <i class="mdi mdi-cash-multiple text-primary me-2"></i> Dashboard de Liquidación de Furgonetas
            </h3>
            <p class="text-muted mb-0">Rendición y control de cuentas consolidadas de vendedores en ruta.</p>
        </div>
        <div class="col-md-6 mt-3 mt-md-0">
            <form method="GET" action="{{ route('admin.liquidar') }}" id="filtro-fecha-form" class="d-flex justify-content-md-end align-items-center gap-2">
                <label for="fecha" class="text-muted mb-0 font-size-12">Fecha:</label>
                <input type="date" id="fecha" name="fecha" value="{{ $fecha }}" class="form-control" style="max-width: 170px;">
                <button type="submit" class="btn btn-primary btn-action shadow">
                    <i class="mdi mdi-filter-variant me-1"></i> Filtrar
                </button>
            </form>
        </div>
    </div>

    <!-- Alertas -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
            <div class="d-flex align-items-center">
                <i class="mdi mdi-check-circle-outline font-size-22 me-2"></i>
                <div>
                    <strong>¡Éxito!</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
            <div class="d-flex align-items-center">
                <i class="mdi mdi-alert-circle-outline font-size-22 me-2"></i>
                <div>
                    <strong>¡Atención!</strong> {{ session('error') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tarjetas de Métricas Generales -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-5 g-4 mb-4">
        <div class="col">
            <div class="metric-card" style="background: var(--primary-gradient); cursor: pointer;"
                 role="button" data-bs-toggle="modal" data-bs-target="#modalResumenFormaPago"
                 title="Ver resumen por forma de pago">
                <div class="metric-title">Total Pendiente</div>
                <div class="metric-value" id="metric-efectivo">S/ {{ number_format($totalGralEfectivo, 2) }}</div>
                <i class="mdi mdi-wallet-giftcard metric-icon"></i>
            </div>
        </div>
        <div class="col">
            <div class="metric-card" style="background: var(--info-gradient);">
                <div class="metric-title">Ventas Contado (Ruta)</div>
                <div class="metric-value" id="metric-contado">S/ {{ number_format($totalGralVentasContado, 2) }}</div>
                <i class="mdi mdi-cart-outline metric-icon"></i>
            </div>
        </div>
        <div class="col">
            <div class="metric-card" style="background: var(--warning-gradient);">
                <div class="metric-title">Ventas Crédito (Ruta)</div>
                <div class="metric-value" id="metric-credito">S/ {{ number_format($totalGralVentasCredito, 2) }}</div>
                <i class="mdi mdi-credit-card-outline metric-icon"></i>
            </div>
        </div>
        <div class="col">
            <div class="metric-card" style="background: var(--success-gradient);">
                <div class="metric-title">Inicial Crédito</div>
                <div class="metric-value" id="metric-inicial">S/ {{ number_format($totalGralInicialCredito, 2) }}</div>
                <i class="mdi mdi-hand-coin-outline metric-icon"></i>
            </div>
        </div>
        <div class="col">
            <div class="metric-card" style="background: var(--danger-gradient);">
                <div class="metric-title">Cobranzas Recaudadas</div>
                <div class="metric-value" id="metric-cobranzas">S/ {{ number_format($totalGralCobranzas, 2) }}</div>
                <i class="mdi mdi-cash-usd metric-icon"></i>
            </div>
        </div>
    </div>


    <!-- Tabla Principal de Vendedores -->
    <div class="row">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="font-weight-bold text-dark mb-0">Consolidado de Vendedores</h5>
                    <span class="text-muted font-size-12">Corte al: <strong id="corte-al-fecha">{{ date('d-m-Y', strtotime($fecha)) }}</strong></span>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-premium align-middle text-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Vendedor / Furgoneta</th>
                                    <th class="text-center">Ventas Contado</th>
                                    <th class="text-center">Ventas Crédito</th>
                                    <th class="text-center">Inicial Crédito</th>
                                    <th class="text-center">Cobranzas Crédito</th>
                                    <th class="text-center">Efectivo Pendiente</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center" style="width: 250px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-vendedores-body">
                                @forelse($vendedores as $v)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                    <i class="mdi mdi-account font-size-20"></i>
                                                </div>
                                                <div>
                                                    <h6 class="font-weight-bold text-dark mb-1">{{ $v->nombre }}</h6>
                                                    <small class="text-muted"><i class="mdi mdi-truck-delivery me-1"></i>{{ $v->stockLocation->name ?? 'Sin furgoneta' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center font-weight-bold text-muted">S/ {{ number_format($v->total_ventas_contado, 2) }}</td>
                                        <td class="text-center font-weight-bold text-muted">S/ {{ number_format($v->total_ventas_credito, 2) }}</td>
                                        <td class="text-center font-weight-bold text-muted">S/ {{ number_format($v->total_inicial_credito, 2) }}</td>
                                        <td class="text-center font-weight-bold text-muted">S/ {{ number_format($v->total_cobros_credito, 2) }}</td>
                                        <td class="text-center">
                                            <span class="efectivo-badge">
                                                S/ {{ number_format($v->total_efectivo_pendiente, 2) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($v->tiene_pendiente)
                                                <span class="badge-premium bg-soft-warning text-warning">
                                                    <i class="mdi mdi-clock-outline me-1"></i>Pendiente Rendir
                                                </span>
                                            @else
                                                <span class="badge-premium bg-soft-success text-success">
                                                    <i class="mdi mdi-check-circle-outline me-1"></i>Caja al Día
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex gap-2 justify-content-center">
                                                <button class="btn btn-outline-info btn-action btn-sm btn-ver-detalle" 
                                                        data-vendedor-id="{{ $v->id }}" 
                                                        data-vendedor-nombre="{{ $v->nombre }}">
                                                    <i class="mdi mdi-eye-outline me-1"></i> Detalle
                                                </button>
                                                
                                                @if($v->total_efectivo_pendiente > 0)
                                                    <form action="{{ route('admin.liquidar.procesar') }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <input type="hidden" name="vendedor_id" value="{{ $v->id }}">
                                                        <input type="hidden" name="fecha" value="{{ $fecha }}">
                                                        <button type="submit"
                                                                class="btn btn-success btn-action btn-sm btn-liquidar-vendedor"
                                                                onclick="return confirm('¿Está seguro de procesar la liquidación de {{ $v->nombre }} del {{ date('d-m-Y', strtotime($fecha)) }}? S/ {{ number_format($v->total_efectivo_pendiente, 2) }} ingresarán a su caja activa.');">
                                                            <i class="mdi mdi-check-all me-1"></i> Liquidar Caja
                                                        </button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-light btn-action btn-sm" disabled>
                                                        Liquidar Caja
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            No hay vendedores con pendientes en esta fecha.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalle Transacciones del Vendedor -->
<div class="modal fade" id="modalDetalleVendedor" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 15px 40px rgba(0,0,0,0.15);">
            <div class="modal-header bg-dark text-white p-4 border-0">
                <div>
                    <h5 class="modal-title font-weight-bold mb-1" id="modalTitleNombre">Transacciones del Vendedor</h5>
                    <small class="text-white-50"><i class="mdi mdi-clipboard-text me-1"></i>Detalle de transacciones pendientes de liquidación en la fecha seleccionada</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Pestañas -->
                <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#modal-ventas-tab" role="tab">
                            Ventas Pendientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#modal-inicial-tab" role="tab">
                            Iniciales Pendientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#modal-cobros-tab" role="tab">
                            Cobranzas Pendientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#modal-resumen-forma-pago-tab" role="tab">
                            Resumen por Forma de Pago
                        </a>
                    </li>
                </ul>

                <div class="tab-content pt-2">
                    <!-- Tab Ventas -->
                    <div class="tab-pane active" id="modal-ventas-tab" role="tabpanel">
                        <div class="table-responsive" style="max-height: 300px;">
                            <table class="table table-bordered table-striped font-size-13 align-middle text-nowrap" id="tabla_ventas_detalle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Serie/Correlativo</th>
                                        <th>Tipo Venta</th>
                                        <th>Forma de Pago</th>
                                        <th class="text-end">Monto</th>
                                        <th class="text-center">Productos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab Inicial de crédito -->
                    <div class="tab-pane" id="modal-inicial-tab" role="tabpanel">
                        <div class="table-responsive" style="max-height: 300px;">
                            <table class="table table-bordered table-striped font-size-13 align-middle text-nowrap" id="tabla_inicial_detalle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Serie/Correlativo</th>
                                        <th>Forma de Pago</th>
                                        <th class="text-end">Monto Inicial</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab Cobros -->
                    <div class="tab-pane" id="modal-cobros-tab" role="tabpanel">
                        <div class="table-responsive" style="max-height: 300px;">
                            <table class="table table-bordered table-striped font-size-13 align-middle text-nowrap" id="tabla_cobros_detalle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>N° Recibo</th>
                                        <th>Referencia</th>
                                        <th>Forma de Pago</th>
                                        <th class="text-end">Monto Cobrado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab Resumen por Forma de Pago -->
                    <div class="tab-pane" id="modal-resumen-forma-pago-tab" role="tabpanel">
                        <div class="table-responsive" style="max-height: 300px;">
                            <table class="table table-bordered table-striped font-size-13 align-middle text-nowrap" id="tabla_resumen_forma_pago_detalle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Forma de Pago</th>
                                        <th class="text-end">Monto Pendiente</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla_resumen_forma_pago_detalle_body">
                                    <!-- AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Resumen en Modal -->
                <div class="row mt-4 pt-3 border-top">
                    <div class="col-md-7">
                        <div class="bg-light p-3 rounded d-flex justify-content-between align-items-center">
                            <span class="font-weight-bold text-dark">Efectivo Total a Liquidar:</span>
                            <span class="font-weight-bold text-success font-size-16" id="modal_total_rendir">S/ 0.00</span>
                        </div>
                    </div>
                    <div class="col-md-5 text-end mt-3 mt-md-0">
                        <form action="{{ route('admin.liquidar.procesar') }}" method="POST" id="form_liquidar_modal">
                            @csrf
                            <input type="hidden" name="vendedor_id" id="modal_vendedor_id_input">
                            <input type="hidden" name="fecha" value="{{ $fecha }}">
                            <button type="submit" class="btn btn-success w-100 py-2 font-weight-bold" style="border-radius: 10px;" id="btn_liquidar_modal_submit">
                                <i class="mdi mdi-check-all me-1"></i> Liquidar Todo
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Resumen por Forma de Pago (general, todos los vendedores) -->
<div class="modal fade" id="modalResumenFormaPago" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 15px 40px rgba(0,0,0,0.15);">
            <div class="modal-header bg-dark text-white p-4 border-0">
                <h5 class="modal-title font-weight-bold mb-0">Resumen por Forma de Pago</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-premium align-middle text-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>Forma de Pago</th>
                                <th class="text-end">Monto Pendiente</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-forma-pago-general-body">
                            @foreach($resumenFormaPagoGeneral as $r)
                                <tr>
                                    <td>{{ $r['descripcion'] }}</td>
                                    <td class="text-end font-weight-bold">S/ {{ number_format($r['monto'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Productos de una Venta -->
<div class="modal fade" id="modalProductosVenta" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 15px 40px rgba(0,0,0,0.15);">
            <div class="modal-header bg-dark text-white p-4 border-0">
                <div>
                    <h5 class="modal-title font-weight-bold mb-1" id="modalProductosVentaTitulo">Productos de la Venta</h5>
                    <small class="text-white-50" id="modalProductosVentaSubtitulo"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped font-size-13 align-middle" id="tabla_productos_venta">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Precio</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="tabla_productos_venta_body">
                            <!-- AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    const csrfToken = '{{ csrf_token() }}';
    const urlLiquidarDatos = "{{ route('admin.liquidar.datos') }}";
    const urlLiquidarProcesar = "{{ route('admin.liquidar.procesar') }}";

    function formatDate(dateStr) {
        if (!dateStr) return '---';
        let cleanDate = dateStr.split(' ')[0]; // por si viene con hora
        let parts = cleanDate.split('-');
        if (parts.length === 3) {
            return parts[2] + '-' + parts[1] + '-' + parts[0];
        }
        return dateStr;
    }

    function actualizarTarjetas(totales) {
        $('#metric-efectivo').text('S/ ' + parseFloat(totales.efectivo).toFixed(2));
        $('#metric-contado').text('S/ ' + parseFloat(totales.ventas_contado).toFixed(2));
        $('#metric-credito').text('S/ ' + parseFloat(totales.ventas_credito).toFixed(2));
        $('#metric-inicial').text('S/ ' + parseFloat(totales.inicial_credito).toFixed(2));
        $('#metric-cobranzas').text('S/ ' + parseFloat(totales.cobranzas).toFixed(2));
    }

    function renderResumenFormaPago(containerId, resumen) {
        if (!resumen || resumen.length === 0) {
            $('#' + containerId).html('<tr><td colspan="2" class="text-center text-muted">Sin montos pendientes.</td></tr>');
            return;
        }
        let html = '';
        resumen.forEach(function(r) {
            html += '<tr><td>' + r.descripcion + '</td><td class="text-end font-weight-bold">S/ ' + parseFloat(r.monto).toFixed(2) + '</td></tr>';
        });
        $('#' + containerId).html(html);
    }

    function renderTablaVendedores(vendedores, fecha) {
        if (!vendedores || vendedores.length === 0) {
            $('#tabla-vendedores-body').html('<tr><td colspan="8" class="text-center text-muted py-4">No hay vendedores con pendientes en esta fecha.</td></tr>');
            return;
        }
        let html = '';
        vendedores.forEach(function(v) {
            const furgoneta = v.furgoneta || 'Sin furgoneta';
            const efectivo = parseFloat(v.total_efectivo_pendiente);
            const estadoHtml = v.tiene_pendiente
                ? '<span class="badge-premium bg-soft-warning text-warning"><i class="mdi mdi-clock-outline me-1"></i>Pendiente Rendir</span>'
                : '<span class="badge-premium bg-soft-success text-success"><i class="mdi mdi-check-circle-outline me-1"></i>Caja al Día</span>';

            let accionesHtml;
            if (efectivo > 0) {
                accionesHtml = '<form action="' + urlLiquidarProcesar + '" method="POST" style="display:inline;">'
                    + '<input type="hidden" name="_token" value="' + csrfToken + '">'
                    + '<input type="hidden" name="vendedor_id" value="' + v.id + '">'
                    + '<input type="hidden" name="fecha" value="' + fecha + '">'
                    + '<button type="submit" class="btn btn-success btn-action btn-sm btn-liquidar-vendedor" '
                    + 'onclick="return confirm(\'¿Está seguro de procesar la liquidación de ' + v.nombre + ' del ' + formatDate(fecha) + '? S/ ' + efectivo.toFixed(2) + ' ingresarán a su caja activa.\');">'
                    + '<i class="mdi mdi-check-all me-1"></i> Liquidar Caja</button></form>';
            } else {
                accionesHtml = '<button class="btn btn-light btn-action btn-sm" disabled>Liquidar Caja</button>';
            }

            html += '<tr>';
            html += '<td><div class="d-flex align-items-center">'
                + '<div class="avatar-sm bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;"><i class="mdi mdi-account font-size-20"></i></div>'
                + '<div><h6 class="font-weight-bold text-dark mb-1">' + v.nombre + '</h6>'
                + '<small class="text-muted"><i class="mdi mdi-truck-delivery me-1"></i>' + furgoneta + '</small></div></div></td>';
            html += '<td class="text-center font-weight-bold text-muted">S/ ' + parseFloat(v.total_ventas_contado).toFixed(2) + '</td>';
            html += '<td class="text-center font-weight-bold text-muted">S/ ' + parseFloat(v.total_ventas_credito).toFixed(2) + '</td>';
            html += '<td class="text-center font-weight-bold text-muted">S/ ' + parseFloat(v.total_inicial_credito).toFixed(2) + '</td>';
            html += '<td class="text-center font-weight-bold text-muted">S/ ' + parseFloat(v.total_cobros_credito).toFixed(2) + '</td>';
            html += '<td class="text-center"><span class="efectivo-badge">S/ ' + efectivo.toFixed(2) + '</span></td>';
            html += '<td class="text-center">' + estadoHtml + '</td>';
            html += '<td class="text-center"><div class="d-flex gap-2 justify-content-center">'
                + '<button class="btn btn-outline-info btn-action btn-sm btn-ver-detalle" data-vendedor-id="' + v.id + '" data-vendedor-nombre="' + v.nombre + '"><i class="mdi mdi-eye-outline me-1"></i> Detalle</button>'
                + accionesHtml
                + '</div></td>';
            html += '</tr>';
        });
        $('#tabla-vendedores-body').html(html);
    }

    function cargarResumen(fecha) {
        $('#tabla-vendedores-body').html('<tr><td colspan="8" class="text-center text-muted py-4">Cargando...</td></tr>');
        $.ajax({
            url: urlLiquidarDatos,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            data: { format: 'json', fecha: fecha },
            success: function(res) {
                actualizarTarjetas(res.totales);
                renderResumenFormaPago('tabla-forma-pago-general-body', res.resumen_forma_pago);
                renderTablaVendedores(res.vendedores, res.fecha);
                $('#corte-al-fecha').text(formatDate(res.fecha));
            },
            error: function() {
                $('#tabla-vendedores-body').html('<tr><td colspan="8" class="text-center text-danger py-4">Error al cargar los datos.</td></tr>');
            }
        });
    }

    $(document).ready(function() {
        $(".loader").fadeOut("slow");

        // Filtro de fecha: fetch en vez de recargar la página
        $('#filtro-fecha-form').on('submit', function(e) {
            e.preventDefault();
            cargarResumen($('#fecha').val());
        });

        // Carga dinámica de detalles de vendedor en modal
        // (delegado sobre el tbody porque las filas se regeneran vía fetch al cambiar el filtro)
        $('#tabla-vendedores-body').on('click', '.btn-ver-detalle', function() {
            let id = $(this).attr('data-vendedor-id');
            let nombre = $(this).attr('data-vendedor-nombre');

            $('#modalTitleNombre').text('Transacciones de ' + nombre);
            $('#modal_vendedor_id_input').val(id);

            // Vaciar tablas
            $('#tabla_ventas_detalle tbody').html('<tr><td colspan="7" class="text-center text-muted">Cargando transacciones...</td></tr>');
            $('#tabla_inicial_detalle tbody').html('<tr><td colspan="5" class="text-center text-muted">Cargando transacciones...</td></tr>');
            $('#tabla_cobros_detalle tbody').html('<tr><td colspan="6" class="text-center text-muted">Cargando transacciones...</td></tr>');
            $('#tabla_resumen_forma_pago_detalle_body').html('<tr><td colspan="2" class="text-center text-muted">Cargando...</td></tr>');

            $('#modalDetalleVendedor').modal('show');

            $.ajax({
                url: urlLiquidarDatos,
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                data: {
                    format: 'json',
                    vendedor_id: id,
                    fecha: $('#fecha').val()
                },
                success: function(res) {
                    // Cargar Ventas
                    let htmlVentas = '';
                    let sumVentasContado = 0;
                    if (res.ventas && res.ventas.length > 0) {
                        res.ventas.forEach(function(v) {
                            let tipoStr = v.tipo_pago_id == 1 ? '<span class="badge bg-soft-success text-success">CONTADO</span>' : '<span class="badge bg-soft-warning text-warning">CRÉDITO</span>';
                            if (v.tipo_pago_id == 1) {
                                sumVentasContado += parseFloat(v.monto);
                            }
                            const comprobante = v.serie_comprobante + '-' + v.numero_comprobante;
                            htmlVentas += '<tr>';
                            htmlVentas += '<td>' + formatDate(v.fecha) + '</td>';
                            htmlVentas += '<td>' + v.nombre_cliente + '</td>';
                            htmlVentas += '<td>' + comprobante + '</td>';
                            htmlVentas += '<td>' + tipoStr + '</td>';
                            htmlVentas += '<td>' + (v.forma_pago || '-') + '</td>';
                            htmlVentas += '<td class="text-end font-weight-bold">S/ ' + parseFloat(v.monto).toFixed(2) + '</td>';
                            htmlVentas += '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-secondary btn-ver-productos" data-venta-id="' + v.id + '" data-cliente="' + v.nombre_cliente + '" data-comprobante="' + comprobante + '" title="Ver productos"><i class="mdi mdi-package-variant"></i></button></td>';
                            htmlVentas += '</tr>';
                        });
                    } else {
                        htmlVentas = '<tr><td colspan="7" class="text-center text-muted">Sin ventas pendientes de liquidación.</td></tr>';
                    }
                    $('#tabla_ventas_detalle tbody').html(htmlVentas);

                    // Cargar Inicial de crédito
                    let htmlInicial = '';
                    let sumInicial = 0;
                    if (res.iniciales && res.iniciales.length > 0) {
                        res.iniciales.forEach(function(i) {
                            sumInicial += parseFloat(i.monto);
                            htmlInicial += '<tr>';
                            htmlInicial += '<td>' + formatDate(i.fecha) + '</td>';
                            htmlInicial += '<td>' + i.nombre_cliente + '</td>';
                            htmlInicial += '<td>' + i.serie_comprobante + '-' + i.numero_comprobante + '</td>';
                            htmlInicial += '<td>' + (i.forma_pago || '-') + '</td>';
                            htmlInicial += '<td class="text-end font-weight-bold">S/ ' + parseFloat(i.monto).toFixed(2) + '</td>';
                            htmlInicial += '</tr>';
                        });
                    } else {
                        htmlInicial = '<tr><td colspan="5" class="text-center text-muted">Sin iniciales de crédito pendientes.</td></tr>';
                    }
                    $('#tabla_inicial_detalle tbody').html(htmlInicial);

                    // Cargar Cobros
                    let htmlCobros = '';
                    let sumCobros = 0;
                    if (res.cobros && res.cobros.length > 0) {
                        res.cobros.forEach(function(c) {
                            sumCobros += parseFloat(c.mont_rec);
                            htmlCobros += '<tr>';
                            htmlCobros += '<td>' + formatDate(c.fech_rec) + '</td>';
                            htmlCobros += '<td>' + (c.nombre_cliente || 'Cliente Desconocido') + '</td>';
                            htmlCobros += '<td>' + (c.num_recibo || '---') + '</td>';
                            htmlCobros += '<td>' + (c.docu_ref || '---') + '</td>';
                            htmlCobros += '<td>' + (c.forma_pago || '-') + '</td>';
                            htmlCobros += '<td class="text-end font-weight-bold">S/ ' + parseFloat(c.mont_rec).toFixed(2) + '</td>';
                            htmlCobros += '</tr>';
                        });
                    } else {
                        htmlCobros = '<tr><td colspan="6" class="text-center text-muted">Sin cobranzas de créditos pendientes.</td></tr>';
                    }
                    $('#tabla_cobros_detalle tbody').html(htmlCobros);

                    // Cargar Resumen por Forma de Pago
                    renderResumenFormaPago('tabla_resumen_forma_pago_detalle_body', res.resumen_forma_pago);

                    // Calcular total a rendir en efectivo (Ventas Contado + Inicial Crédito + Cobros Crédito)
                    let totalEfectivo = sumVentasContado + sumInicial + sumCobros;
                    $('#modal_total_rendir').text('S/ ' + totalEfectivo.toFixed(2));

                    // Habilitar/Deshabilitar botón de liquidación
                    if (totalEfectivo > 0) {
                        $('#btn_liquidar_modal_submit').prop('disabled', false).removeClass('btn-secondary').addClass('btn-success');
                    } else {
                        $('#btn_liquidar_modal_submit').prop('disabled', true).removeClass('btn-success').addClass('btn-secondary');
                    }
                },
                error: function() {
                    $('#tabla_ventas_detalle tbody').html('<tr><td colspan="7" class="text-center text-danger">Error al cargar datos.</td></tr>');
                    $('#tabla_inicial_detalle tbody').html('<tr><td colspan="5" class="text-center text-danger">Error al cargar datos.</td></tr>');
                    $('#tabla_cobros_detalle tbody').html('<tr><td colspan="6" class="text-center text-danger">Error al cargar datos.</td></tr>');
                    $('#tabla_resumen_forma_pago_detalle_body').html('<tr><td colspan="2" class="text-center text-danger">Error al cargar datos.</td></tr>');
                }
            });
        });

        // Ver productos de una venta puntual (delegado: las filas de ventas se
        // regeneran vía AJAX cada vez que se abre el detalle de un vendedor)
        $('#tabla_ventas_detalle').on('click', '.btn-ver-productos', function() {
            const ventaId = $(this).data('venta-id');
            const cliente = $(this).data('cliente');
            const comprobante = $(this).data('comprobante');

            $('#modalProductosVentaTitulo').text('Productos de ' + cliente);
            $('#modalProductosVentaSubtitulo').text('Comprobante: ' + comprobante);
            $('#tabla_productos_venta_body').html('<tr><td colspan="4" class="text-center text-muted">Cargando...</td></tr>');
            $('#modalProductosVenta').modal('show');

            $.ajax({
                url: urlLiquidarDatos,
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                data: { format: 'json', venta_id: ventaId },
                success: function(res) {
                    let html = '';
                    if (res.productos && res.productos.length > 0) {
                        res.productos.forEach(function(p) {
                            html += '<tr>';
                            html += '<td>' + p.nombre + '</td>';
                            html += '<td class="text-center">' + parseFloat(p.cantidad) + '</td>';
                            html += '<td class="text-end">S/ ' + parseFloat(p.precio).toFixed(2) + '</td>';
                            html += '<td class="text-end font-weight-bold">S/ ' + parseFloat(p.subtotal).toFixed(2) + '</td>';
                            html += '</tr>';
                        });
                    } else {
                        html = '<tr><td colspan="4" class="text-center text-muted">Sin productos registrados.</td></tr>';
                    }
                    $('#tabla_productos_venta_body').html(html);
                },
                error: function() {
                    $('#tabla_productos_venta_body').html('<tr><td colspan="4" class="text-center text-danger">Error al cargar productos.</td></tr>');
                }
            });
        });

        // Evento submit de liquidación en el modal
        $('#form_liquidar_modal').on('submit', function() {
            let nombre = $('#modalTitleNombre').text().replace('Transacciones de ', '');
            let total = $('#modal_total_rendir').text();
            return confirm('¿Está seguro de liquidar la caja de ' + nombre + ' por un monto total de ' + total + '?');
        });
    });
</script>
@endsection
