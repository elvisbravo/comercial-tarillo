@extends('layouts.main')

@section('title', 'Retorno de Stock y Reconciliación de Furgonetas')

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

    .retorno-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .retorno-card:hover {
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

    .input-fisico {
        max-width: 100px;
        font-weight: 700;
        text-align: center;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
    }

    .diff-badge {
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: bold;
    }

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

    <!-- Encabezado -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="font-weight-bold text-dark mb-1" style="letter-spacing: -0.5px;">
                <i class="mdi mdi-swap-horizontal text-primary me-2"></i> Retorno y Reconciliación de Stock
            </h3>
            <p class="text-muted mb-0">Control de devolución de mercadería de furgonetas y auditoría de diferencias (mermas).</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <form id="form_filtro_fecha" class="d-flex gap-2 justify-content-md-end align-items-center flex-wrap">
                <label for="filtro_fecha" class="text-muted mb-0 font-size-13">Fecha de carga:</label>
                <input type="date" id="filtro_fecha" name="fecha" value="{{ $fecha }}" class="form-control form-control-sm" style="max-width: 160px;">
                <button type="submit" class="btn btn-primary btn-action shadow">
                    <i class="mdi mdi-magnify me-1"></i> Filtrar
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

    <!-- Tarjetas de Métricas -->
    <div class="row mb-4">
        <div class="col-md-4 mb-4 mb-md-0">
            <div class="metric-card" style="background: var(--info-gradient);">
                <div class="metric-title">Furgonetas Registradas</div>
                <div class="metric-value" id="metric_furgonetas">{{ $vendedores->count() }}</div>
                <i class="mdi mdi-truck-delivery metric-icon"></i>
            </div>
        </div>
        <div class="col-md-4 mb-4 mb-md-0">
            <div class="metric-card" style="background: var(--primary-gradient);">
                <div class="metric-title">Productos en Ruta</div>
                <div class="metric-value" id="metric_items">{{ $vendedores->sum('total_items') }} items</div>
                <i class="mdi mdi-package-variant metric-icon"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card" style="background: var(--warning-gradient);">
                <div class="metric-title">Total Unidades en Ruta</div>
                @php($totalUnidRuta = $vendedores->sum('total_unidades'))
                <div class="metric-value" id="metric_unidades">{{ $totalUnidRuta == floor($totalUnidRuta) ? number_format($totalUnidRuta, 0) : number_format($totalUnidRuta, 2) }} unid.</div>
                <i class="mdi mdi-buffer metric-icon"></i>
            </div>
        </div>
    </div>

    <!-- Consolidado de Stock en Furgonetas -->
    <div class="row">
        <div class="col-12">
            <div class="card retorno-card">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="font-weight-bold text-dark mb-0">Inventario Actual en Ruta</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-premium align-middle text-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Vendedor / Furgoneta</th>
                                    <th class="text-center">Variedad de Productos</th>
                                    <th class="text-center">Total Unidades</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center" style="width: 250px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla_vendedores_body">
                                @forelse($vendedores as $v)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-soft-info text-info rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                    <i class="mdi mdi-truck font-size-20"></i>
                                                </div>
                                                <div>
                                                    <h6 class="font-weight-bold text-dark mb-1">{{ $v->nombre }}</h6>
                                                    <small class="text-muted"><i class="mdi mdi-store me-1"></i>{{ $v->stockLocation->name ?? 'Sin furgoneta' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center font-weight-bold text-dark font-size-14">{{ $v->total_items }} items</td>
                                        <td class="text-center font-weight-bold text-primary font-size-14">{{ $v->total_unidades == floor($v->total_unidades) ? number_format($v->total_unidades, 0) : number_format($v->total_unidades, 2) }} unidades</td>
                                        <td class="text-center">
                                            @if($v->total_unidades > 0)
                                                <span class="badge-premium bg-soft-warning text-warning">
                                                    <i class="mdi mdi-package-variant me-1"></i>Con Carga
                                                </span>
                                            @else
                                                <span class="badge-premium bg-soft-success text-success">
                                                    <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i>Vacía / Rendida
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($v->total_unidades > 0)
                                                <button class="btn btn-success btn-action btn-sm btn-reconciliar" 
                                                        data-vendedor-id="{{ $v->id }}" 
                                                        data-vendedor-nombre="{{ $v->nombre }}"
                                                        data-furgoneta-nombre="{{ $v->stockLocation->name ?? 'Furgoneta' }}">
                                                    <i class="mdi mdi-swap-horizontal-bold me-1"></i> Reconciliar Retorno
                                                </button>
                                            @else
                                                <button class="btn btn-light btn-action btn-sm" disabled>
                                                    Reconciliar Retorno
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            No hay furgonetas con carga registrada en la fecha seleccionada.
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

<!-- Modal para Reconciliación de Stock -->
<div class="modal fade" id="modalReconciliarStock" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 15px 40px rgba(0,0,0,0.15);">
            <div class="modal-header bg-dark text-white p-4 border-0">
                <div>
                    <h5 class="modal-title font-weight-bold mb-1" id="modalTitleVendedor">Reconciliar Retorno de Stock</h5>
                    <small class="text-white-50" id="modalTitleFurgoneta">---</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.retorno.procesar') }}" method="POST" id="form_retorno_modal">
                @csrf
                <input type="hidden" name="vendedor_id" id="modal_vendedor_id">
                <input type="hidden" name="fecha" id="modal_fecha" value="{{ $fecha }}">

                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 shadow-sm mb-3 d-flex align-items-center" style="border-radius: 10px;">
                        <i class="mdi mdi-information-outline font-size-22 me-2"></i>
                        <div>
                            Verifique físicamente la mercadería devuelta por el vendedor. El stock confirmado ingresará de inmediato al **Almacén Principal (Stock)** y las mermas o pérdidas se registrarán en el Kardex.
                        </div>
                    </div>

                    <div class="table-responsive" style="max-height: 350px;">
                        <table class="table table-bordered table-striped align-middle font-size-13 text-nowrap" id="tabla_retorno_productos">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto / Artículo</th>
                                    <th class="text-center" style="width: 90px;">Cargado</th>
                                    <th class="text-center" style="width: 90px;">Vendido</th>
                                    <th class="text-center" style="width: 130px;">Stock Teórico</th>
                                    <th class="text-center" style="width: 150px;">Físico Recibido</th>
                                    <th class="text-center" style="width: 140px;">Diferencia / Mermas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- AJAX content -->
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <label for="modal_observacion" class="form-label font-size-13 text-muted mb-1">
                            Observación de mermas (opcional)
                        </label>
                        <textarea name="observacion" id="modal_observacion" class="form-control" rows="2"
                                  placeholder="Motivo de las mermas, si aplica..."></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0 bg-light p-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Atrás</button>
                    <button type="submit" class="btn btn-success px-4 py-2 font-weight-bold" style="border-radius: 10px;">
                        Confirmar y Procesar Retorno
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    const csrfToken = '{{ csrf_token() }}';
    const urlRetornoDatos = "{{ route('admin.retorno.datos') }}";
    let fechaFiltro = "{{ $fecha }}";

    // Muestra enteros sin decimales y cantidades fraccionarias con 2 decimales
    function fmtQty(n) {
        n = parseFloat(n) || 0;
        return (n % 1 === 0) ? n.toString() : n.toFixed(2);
    }

    function renderTablaVendedores(vendedores) {
        let html = '';
        if (vendedores && vendedores.length > 0) {
            vendedores.forEach(function(v) {
                let unidades = fmtQty(v.total_unidades);
                let conCarga = parseFloat(v.total_unidades) > 0;
                html += '<tr>';
                html += '<td><div class="d-flex align-items-center">';
                html += '<div class="avatar-sm bg-soft-info text-info rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;"><i class="mdi mdi-truck font-size-20"></i></div>';
                html += '<div><h6 class="font-weight-bold text-dark mb-1">' + v.nombre + '</h6><small class="text-muted"><i class="mdi mdi-store me-1"></i>' + v.furgoneta + '</small></div>';
                html += '</div></td>';
                html += '<td class="text-center font-weight-bold text-dark font-size-14">' + v.total_items + ' items</td>';
                html += '<td class="text-center font-weight-bold text-primary font-size-14">' + unidades + ' unidades</td>';
                html += '<td class="text-center">';
                html += conCarga
                    ? '<span class="badge-premium bg-soft-warning text-warning"><i class="mdi mdi-package-variant me-1"></i>Con Carga</span>'
                    : '<span class="badge-premium bg-soft-success text-success"><i class="mdi mdi-checkbox-marked-circle-outline me-1"></i>Vacía / Rendida</span>';
                html += '</td>';
                html += '<td class="text-center">';
                html += conCarga
                    ? '<button class="btn btn-success btn-action btn-sm btn-reconciliar" data-vendedor-id="' + v.id + '" data-vendedor-nombre="' + v.nombre + '" data-furgoneta-nombre="' + v.furgoneta + '"><i class="mdi mdi-swap-horizontal-bold me-1"></i> Reconciliar Retorno</button>'
                    : '<button class="btn btn-light btn-action btn-sm" disabled>Reconciliar Retorno</button>';
                html += '</td>';
                html += '</tr>';
            });
        } else {
            html = '<tr><td colspan="5" class="text-center text-muted py-4">No hay furgonetas con carga registrada en la fecha seleccionada.</td></tr>';
        }
        $('#tabla_vendedores_body').html(html);
    }

    function actualizarMetricas(vendedores) {
        let totalItems = 0, totalUnidades = 0;
        (vendedores || []).forEach(function(v) {
            totalItems += parseFloat(v.total_items) || 0;
            totalUnidades += parseFloat(v.total_unidades) || 0;
        });
        $('#metric_furgonetas').text(vendedores ? vendedores.length : 0);
        $('#metric_items').text(fmtQty(totalItems) + ' items');
        $('#metric_unidades').text(fmtQty(totalUnidades) + ' unid.');
    }

    function cargarListado(fecha) {
        $('#tabla_vendedores_body').html('<tr><td colspan="5" class="text-center text-muted py-4">Cargando...</td></tr>');
        fetch(urlRetornoDatos, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({ format: 'json', fecha: fecha })
        })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                fechaFiltro = data.fecha;
                $('#modal_fecha').val(fechaFiltro);
                renderTablaVendedores(data.vendedores);
                actualizarMetricas(data.vendedores);
            })
            .catch(function() {
                $('#tabla_vendedores_body').html('<tr><td colspan="5" class="text-center text-danger py-4">Error al cargar el listado.</td></tr>');
            });
    }

    $(document).ready(function() {
        $(".loader").fadeOut("slow");

        $('#form_filtro_fecha').on('submit', function(e) {
            e.preventDefault();
            cargarListado($('#filtro_fecha').val());
        });

        // Cargar productos de furgoneta por AJAX (delegado: las filas se regeneran vía fetch)
        $('#tabla_vendedores_body').on('click', '.btn-reconciliar', function() {
            let id = $(this).attr('data-vendedor-id');
            let nombre = $(this).attr('data-vendedor-nombre');
            let furgoneta = $(this).attr('data-furgoneta-nombre');

            $('#modalTitleVendedor').text('Reconciliar Retorno: ' + nombre);
            $('#modalTitleFurgoneta').text('Ubicación: ' + furgoneta);
            $('#modal_vendedor_id').val(id);
            $('#modal_fecha').val(fechaFiltro);

            $('#tabla_retorno_productos tbody').html('<tr><td colspan="6" class="text-center text-muted">Cargando productos de furgoneta...</td></tr>');
            $('#modal_observacion').val('');
            $('#modalReconciliarStock').modal('show');

            fetch(urlRetornoDatos, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({ format: 'json', vendedor_id: id, fecha: fechaFiltro })
            })
                .then(function(res) { return res.json(); })
                .then(function(res) {
                    let html = '';
                    if (res && res.length > 0) {
                        res.forEach(function(item) {
                            let stock = fmtQty(item.stock);
                            let cargado = fmtQty(item.cargado);
                            let vendido = fmtQty(item.vendido);
                            html += '<tr>';
                            html += '<td>';
                            html += '<span class="font-weight-bold text-dark">' + item.nomb_pro + '</span>';
                            html += '<input type="hidden" name="productos[]" value="' + item.id + '">';
                            html += '</td>';
                            html += '<td class="text-center text-muted">' + cargado + '</td>';
                            html += '<td class="text-center text-muted">' + vendido + '</td>';
                            html += '<td class="text-center font-weight-bold font-size-14 text-primary">';
                            html += '<span class="teorico-val" id="teorico_' + item.id + '">' + stock + '</span>';
                            html += '</td>';
                            html += '<td class="text-center">';
                            html += '<div class="d-flex justify-content-center">';
                            html += '<input type="number" step="0.01" name="fisico[' + item.id + ']" class="form-control input-fisico form-control-sm" min="0" max="' + stock + '" value="' + stock + '" data-id="' + item.id + '" required>';
                            html += '</div>';
                            html += '</td>';
                            html += '<td class="text-center">';
                            html += '<span class="diff-badge bg-soft-success text-success" id="diff_' + item.id + '">0 mermas</span>';
                            html += '</td>';
                            html += '</tr>';
                        });
                    } else {
                        html = '<tr><td colspan="6" class="text-center text-muted">No se encontraron productos cargados en esta unidad móvil.</td></tr>';
                    }
                    $('#tabla_retorno_productos tbody').html(html);

                    // Escuchar inputs en tiempo real para calcular mermas
                    $('.input-fisico').on('input', function() {
                        let prodId = $(this).attr('data-id');
                        let teorico = parseFloat($('#teorico_' + prodId).text()) || 0;
                        let fisico = parseFloat($(this).val()) || 0;
                        let diff = teorico - fisico;

                        let badge = $('#diff_' + prodId);
                        if (diff > 0) {
                            badge.removeClass('bg-soft-success text-success').addClass('bg-soft-danger text-danger');
                            badge.text(fmtQty(diff) + ' pérdida' + (diff > 1 ? 's' : ''));
                        } else {
                            badge.removeClass('bg-soft-danger text-danger').addClass('bg-soft-success text-success');
                            badge.text('0 mermas');
                        }
                    });
                })
                .catch(function() {
                    $('#tabla_retorno_productos tbody').html('<tr><td colspan="6" class="text-center text-danger">Error al cargar productos de furgoneta.</td></tr>');
                });
        });

        // Evento submit de retorno
        $('#form_retorno_modal').on('submit', function() {
            return confirm('¿Está seguro de procesar el retorno de inventario? El stock ingresará al almacén central y las mermas calculadas se procesarán.');
        });
    });
</script>
@endsection
