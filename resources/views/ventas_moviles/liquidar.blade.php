@extends('layouts.main')

@section('title', 'Dashboard de Liquidación de Caja - Furgonetas')

@section('css')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #119f82 0%, #0d705c 100%);
        --info-gradient: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #b45309 100%);
        --danger-gradient: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
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
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <button class="btn btn-primary btn-action shadow" onclick="window.location.reload();">
                <i class="mdi mdi-refresh me-1"></i> Actualizar Métricas
            </button>
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
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-4 mb-xl-0">
            <div class="metric-card" style="background: var(--primary-gradient);">
                <div class="metric-title">Efectivo Total Pendiente</div>
                <div class="metric-value">S/ {{ number_format($totalGralEfectivo, 2) }}</div>
                <i class="mdi mdi-wallet-giftcard metric-icon"></i>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-4 mb-xl-0">
            <div class="metric-card" style="background: var(--info-gradient);">
                <div class="metric-title">Ventas Contado (Ruta)</div>
                <div class="metric-value">S/ {{ number_format($totalGralVentasContado, 2) }}</div>
                <i class="mdi mdi-cart-outline metric-icon"></i>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-4 mb-xl-0">
            <div class="metric-card" style="background: var(--warning-gradient);">
                <div class="metric-title">Ventas Crédito (Ruta)</div>
                <div class="metric-value">S/ {{ number_format($totalGralVentasCredito, 2) }}</div>
                <i class="mdi mdi-credit-card-outline metric-icon"></i>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card" style="background: var(--danger-gradient);">
                <div class="metric-title">Cobranzas Recaudadas</div>
                <div class="metric-value">S/ {{ number_format($totalGralCobranzas, 2) }}</div>
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
                    <span class="text-muted font-size-12">Corte al: <strong>{{ date('d-m-Y') }}</strong></span>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-premium align-middle text-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Vendedor / Furgoneta</th>
                                    <th class="text-center">Ventas Contado</th>
                                    <th class="text-center">Ventas Crédito</th>
                                    <th class="text-center">Cobranzas Crédito</th>
                                    <th class="text-center">Efectivo Pendiente</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center" style="width: 250px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vendedores as $v)
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
                                                        <button type="submit" 
                                                                class="btn btn-success btn-action btn-sm btn-liquidar-vendedor"
                                                                onclick="return confirm('¿Está seguro de procesar la liquidación de {{ $v->nombre }}? S/ {{ number_format($v->total_efectivo_pendiente, 2) }} ingresarán a su caja activa.');">
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
                                @endforeach
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
                    <small class="text-white-50"><i class="mdi mdi-clipboard-text me-1"></i>Detalle de transacciones de hoy pendientes de liquidación</small>
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
                        <a class="nav-link" data-bs-toggle="tab" href="#modal-cobros-tab" role="tab">
                            Cobranzas Pendientes
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
                                        <th class="text-end">Monto</th>
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
                                        <th class="text-end">Monto Cobrado</th>
                                    </tr>
                                </thead>
                                <tbody>
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
@endsection

@section('js')
<script>
    function formatDate(dateStr) {
        if (!dateStr) return '---';
        let cleanDate = dateStr.split(' ')[0]; // por si viene con hora
        let parts = cleanDate.split('-');
        if (parts.length === 3) {
            return parts[2] + '-' + parts[1] + '-' + parts[0];
        }
        return dateStr;
    }

    $(document).ready(function() {
        $(".loader").fadeOut("slow");

        // Carga dinámica de detalles de vendedor en modal
        $('.btn-ver-detalle').on('click', function() {
            let id = $(this).attr('data-vendedor-id');
            let nombre = $(this).attr('data-vendedor-nombre');

            $('#modalTitleNombre').text('Transacciones de ' + nombre);
            $('#modal_vendedor_id_input').val(id);
            
            // Vaciar tablas
            $('#tabla_ventas_detalle tbody').html('<tr><td colspan="5" class="text-center text-muted">Cargando transacciones...</td></tr>');
            $('#tabla_cobros_detalle tbody').html('<tr><td colspan="5" class="text-center text-muted">Cargando transacciones...</td></tr>');
            
            $('#modalDetalleVendedor').modal('show');

            $.ajax({
                url: "{{ route('admin.liquidar') }}",
                data: {
                    format: 'json',
                    vendedor_id: id
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
                            htmlVentas += '<tr>';
                            htmlVentas += '<td>' + formatDate(v.fecha) + '</td>';
                            htmlVentas += '<td>' + v.nombre_cliente + '</td>';
                            htmlVentas += '<td>' + v.serie_comprobante + '-' + v.numero_comprobante + '</td>';
                            htmlVentas += '<td>' + tipoStr + '</td>';
                            htmlVentas += '<td class="text-end font-weight-bold">S/ ' + parseFloat(v.monto).toFixed(2) + '</td>';
                            htmlVentas += '</tr>';
                        });
                    } else {
                        htmlVentas = '<tr><td colspan="5" class="text-center text-muted">Sin ventas pendientes de liquidación.</td></tr>';
                    }
                    $('#tabla_ventas_detalle tbody').html(htmlVentas);

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
                            htmlCobros += '<td class="text-end font-weight-bold">S/ ' + parseFloat(c.mont_rec).toFixed(2) + '</td>';
                            htmlCobros += '</tr>';
                        });
                    } else {
                        htmlCobros = '<tr><td colspan="5" class="text-center text-muted">Sin cobranzas de créditos pendientes.</td></tr>';
                    }
                    $('#tabla_cobros_detalle tbody').html(htmlCobros);

                    // Calcular total a rendir en efectivo (Ventas Contado + Cobros Crédito)
                    let totalEfectivo = sumVentasContado + sumCobros;
                    $('#modal_total_rendir').text('S/ ' + totalEfectivo.toFixed(2));

                    // Habilitar/Deshabilitar botón de liquidación
                    if (totalEfectivo > 0) {
                        $('#btn_liquidar_modal_submit').prop('disabled', false).removeClass('btn-secondary').addClass('btn-success');
                    } else {
                        $('#btn_liquidar_modal_submit').prop('disabled', true).removeClass('btn-success').addClass('btn-secondary');
                    }
                },
                error: function() {
                    $('#tabla_ventas_detalle tbody').html('<tr><td colspan="5" class="text-center text-danger">Error al cargar datos.</td></tr>');
                    $('#tabla_cobros_detalle tbody').html('<tr><td colspan="5" class="text-center text-danger">Error al cargar datos.</td></tr>');
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
