@extends('layouts.main')

@section('title', 'Recojo de Mercadería - Ventas Móviles')

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
    .filter-card {
        background: #f8fafc;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        padding: 18px 20px;
        margin-bottom: 20px;
    }
    .info-chip {
        background: #f1f5f9; border-radius: 8px; padding: 8px 14px;
        font-size: 12px; font-weight: 600; color: #475569;
    }
    .info-chip span { color: #1e293b; font-size: 14px; }
    .empty-state { text-align: center; padding: 60px 20px; color: #94a3b8; }
    .empty-state i { font-size: 56px; display: block; margin-bottom: 12px; }
    .empty-state p { font-size: 14px; font-weight: 500; }
    #resultados_cliente .list-group-item {
        cursor: pointer;
        font-size: 13px;
    }
    #resultados_cliente .list-group-item:hover {
        background: #f1f5f9;
    }
    .input-recuperar {
        max-width: 110px;
        font-weight: 700;
        text-align: center;
        border-radius: 8px;
    }
    .modal-header-custom {
        background: linear-gradient(135deg, #0f2027, #2c5364);
        color: white;
        border-radius: 16px 16px 0 0;
        padding: 20px 24px;
    }
    .modal-content { border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
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
                    <i class="mdi mdi-package-variant-remove me-2"></i> Recojo de Mercadería por Crédito Incobrable
                </h4>
            </div>
            <p class="text-muted mb-0">Registre la recuperación de productos cuando un cliente no podrá seguir pagando su crédito. La mercadería recuperada reingresa al almacén principal. Si lo recuperado cubre todo el saldo pendiente (o usted marca "Cerrar crédito"), el crédito se cierra; si no, el crédito continúa activo por el saldo restante y podrá procesar otro recojo más adelante.</p>
        </div>
    </div>

    {{-- Alertas --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
            <div class="d-flex align-items-center">
                <i class="mdi mdi-check-circle-outline font-size-22 me-2"></i>
                <div><strong>¡Éxito!</strong> {{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
            <div class="d-flex align-items-center">
                <i class="mdi mdi-alert-circle-outline font-size-22 me-2"></i>
                <div><strong>¡Atención!</strong> {{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        {{-- Columna izquierda: buscador de cliente y crédito --}}
        <div class="col-lg-5 mb-4 mb-lg-0">
            <div class="card cargar-card h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="font-weight-bold text-dark mb-0"><i class="mdi mdi-account-search-outline me-1"></i> 1. Buscar Cliente</h5>
                    <p class="text-muted font-size-12 mb-0">Solo se muestran clientes con crédito activo en esta sede.</p>
                </div>
                <div class="card-body p-4">
                    <div class="position-relative mb-3">
                        <input type="text" id="buscar_cliente" class="form-control" placeholder="Nombre, razón social o documento..." autocomplete="off">
                        <div id="resultados_cliente" class="list-group position-absolute w-100 shadow" style="z-index: 1000; max-height: 260px; overflow-y: auto; display: none;"></div>
                    </div>

                    <div id="cliente_seleccionado_box" class="d-none">
                        <div class="alert alert-info border-0 shadow-sm mb-3" style="border-radius: 10px;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong id="cliente_nombre_sel">—</strong><br>
                                    <small class="text-muted" id="cliente_doc_sel">—</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btn_cambiar_cliente">
                                    <i class="mdi mdi-close"></i> Cambiar
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold text-dark mb-1" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Crédito</label>
                            <select id="select_credito" class="form-select"></select>
                        </div>

                        <div id="credito_resumen_box" class="d-none">
                            <div class="d-flex gap-2 flex-wrap">
                                <div class="info-chip">Saldo pendiente<br><span class="text-danger" id="resumen_saldo">S/ 0.00</span></div>
                                <div class="info-chip">Cuotas pendientes<br><span id="resumen_cuotas">0</span></div>
                            </div>
                        </div>
                    </div>

                    <div id="sin_cliente_msg" class="text-center text-muted py-4">
                        <i class="mdi mdi-account-search-outline" style="font-size: 40px; opacity: .4;"></i>
                        <p class="mb-0 font-size-13">Busque un cliente para iniciar el recojo.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Columna derecha: productos, recogido por y confirmación --}}
        <div class="col-lg-7">
            <div class="card cargar-card h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="font-weight-bold text-dark mb-0"><i class="mdi mdi-package-variant-remove me-1"></i> 2. Mercadería a Recuperar</h5>
                    <p class="text-muted font-size-12 mb-0">Indique cuánto de cada producto se recupera físicamente. Puede dejar todo en 0 si nada es recuperable.</p>
                </div>
                <div class="card-body p-4">
                    <form id="form_recojo" action="{{ route('admin.recojo.procesar') }}" method="POST">
                        @csrf
                        <input type="hidden" name="credito_id" id="form_credito_id">

                        <div class="table-responsive mb-3" style="max-height: 260px;">
                            <table class="table table-bordered table-striped align-middle font-size-13 mb-0" id="tabla_productos_recojo">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center" style="width: 90px;">Precio</th>
                                        <th class="text-center" style="width: 90px;">Vendido</th>
                                        <th class="text-center" style="width: 90px;">Disponible</th>
                                        <th class="text-center" style="width: 150px;">Cant. a Recuperar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td colspan="5" class="text-center text-muted py-3">Seleccione un cliente y un crédito primero.</td></tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold text-dark mb-1" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Recogido por</label>
                                <select name="vendedor_recojo_id" id="select_vendedor_recojo" class="form-select" required>
                                    <option value="">— Seleccionar —</option>
                                    @foreach($vendedores as $v)
                                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Quién recogió físicamente la mercadería (no necesariamente quien registra).</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold text-dark mb-1" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Motivo / Observación</label>
                                <textarea name="observacion" id="input_observacion" class="form-control" rows="2" placeholder="Ej: Cliente no ubicable, se recoge mercadería restante..."></textarea>
                            </div>
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input type="checkbox" name="cerrar_credito" value="1" id="input_cerrar_credito" class="form-check-input">
                                    <label class="form-check-label" for="input_cerrar_credito">
                                        Cerrar crédito y condonar el saldo restante como incobrable (cliente ilocalizable / no pagará el resto)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="resumen_resultado_box" class="alert alert-light border mt-3 mb-0 d-none" style="border-radius: 10px; font-size: 13px;"></div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-danger btn-action px-4 py-2" id="btn_confirmar_recojo" disabled>
                                <i class="mdi mdi-basket-remove me-1"></i> Confirmar Recojo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Historial --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="filter-card">
                <form id="form_filtros_historial">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4 col-sm-6">
                            <label class="form-label font-weight-bold text-dark mb-1" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Desde</label>
                            <input type="date" id="filtro_fecha_desde" class="form-control" value="{{ $fechaDesde }}" style="border-radius: 8px; font-size: 13px;">
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="form-label font-weight-bold text-dark mb-1" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Hasta</label>
                            <input type="date" id="filtro_fecha_hasta" class="form-control" value="{{ $fechaHasta }}" style="border-radius: 8px; font-size: 13px;">
                        </div>
                        <div class="col-md-4 col-sm-6 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-action w-100">
                                <i class="mdi mdi-magnify me-1"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div id="historial-table-card">
                <div class="card cargar-card">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="font-weight-bold text-dark mb-0">Historial de Recojos</h5>
                            <p class="text-muted font-size-12 mb-0">Recojos de mercadería registrados en esta sede.</p>
                        </div>
                        <span class="badge bg-soft-danger text-danger font-size-12 px-3 py-2">
                            {{ count($historial) }} registro(s)
                        </span>
                    </div>
                    <div class="card-body p-4">
                        @if(count($historial) > 0)
                        <div class="table-responsive">
                            <table class="table table-premium align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Recogido por</th>
                                        <th>Registrado por</th>
                                        <th class="text-end">Recuperado</th>
                                        <th class="text-end">Saldo condonado</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-center" style="width: 100px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($historial as $r)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($r->fecha)->format('d/m/Y') }}</td>
                                        <td class="font-weight-bold text-dark">
                                            {{ optional($r->cliente)->razon_social ?: optional($r->cliente)->nomb_per ?: 'Cliente' }}
                                        </td>
                                        <td>{{ optional($r->vendedorRecojo)->name ?? '—' }}</td>
                                        <td>{{ optional($r->usuario)->name ?? '—' }}</td>
                                        <td class="text-end font-weight-bold text-success">S/ {{ number_format($r->valor_recuperado, 2) }}</td>
                                        <td class="text-end font-weight-bold text-danger">S/ {{ number_format($r->saldo_incobrable, 2) }}</td>
                                        <td class="text-center">
                                            @if($r->credito_cerrado)
                                                <span class="badge bg-secondary">Cerrado</span>
                                            @else
                                                <span class="badge bg-success">Activo (reducido)</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-primary btn-sm btn-ver-recojo" data-id="{{ $r->id }}">
                                                <i class="mdi mdi-eye-outline me-1"></i> Ver
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="empty-state">
                            <i class="mdi mdi-package-variant-remove"></i>
                            <p>No se encontraron recojos de mercadería con los filtros seleccionados.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Detalle de Recojo --}}
<div class="modal fade" id="modalDetalleRecojo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header-custom">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 42px; height: 42px; background: rgba(255,255,255,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="mdi mdi-package-variant-remove" style="font-size: 22px;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Detalle de Recojo</h5>
                        <small style="opacity: 0.75;" id="modal_recojo_subtitulo">Cargando información...</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="modal_recojo_loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted mt-3 mb-0">Cargando detalle...</p>
                </div>
                <div id="modal_recojo_contenido" class="d-none">
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        <div class="info-chip">Recogido por<br><span id="modal_recojo_vendedor">—</span></div>
                        <div class="info-chip">Registrado por<br><span id="modal_recojo_usuario">—</span></div>
                        <div class="info-chip">Recuperado<br><span class="text-success" id="modal_recojo_recuperado">S/ 0.00</span></div>
                        <div class="info-chip">Saldo condonado<br><span class="text-danger" id="modal_recojo_saldo">S/ 0.00</span></div>
                        <div class="info-chip">Estado del crédito<br><span id="modal_recojo_estado">—</span></div>
                    </div>
                    <div class="mb-3" id="modal_recojo_obs_box">
                        <strong class="font-size-12 text-muted text-uppercase">Observación</strong>
                        <p class="mb-0" id="modal_recojo_observacion">—</p>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <thead style="background: #f1f5f9;">
                                <tr>
                                    <th style="font-size: 11px; font-weight: 700; text-transform: uppercase;">Producto Recuperado</th>
                                    <th style="font-size: 11px; font-weight: 700; text-transform: uppercase; width: 120px;" class="text-center">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody id="modal_recojo_productos"></tbody>
                        </table>
                    </div>
                </div>
                <div id="modal_recojo_error" class="d-none text-center py-5">
                    <i class="mdi mdi-alert-circle-outline text-danger" style="font-size: 48px;"></i>
                    <p class="text-muted mt-3 mb-0">No se pudo cargar el detalle. Intente de nuevo.</p>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light btn-action" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    const csrfToken = '{{ csrf_token() }}';
    const urlBuscarCliente = "{{ route('admin.recojo.buscar_cliente') }}";
    const urlCreditosClienteBase = "{{ url('ventas-moviles/recojo-mercaderia/creditos') }}";
    const urlDetalleCreditoBase = "{{ url('ventas-moviles/recojo-mercaderia/detalle') }}";
    const urlVerRecojoBase = "{{ url('ventas-moviles/recojo-mercaderia/ver') }}";
    const urlRecojoDatos = "{{ route('admin.recojo.datos') }}";

    let creditoSeleccionadoId = null;
    let saldoPendienteActual = 0;

    function fmtMoney(n) {
        return 'S/ ' + (parseFloat(n) || 0).toFixed(2);
    }

    function resetFormulario() {
        creditoSeleccionadoId = null;
        saldoPendienteActual = 0;
        $('#form_credito_id').val('');
        $('#cliente_seleccionado_box').addClass('d-none');
        $('#sin_cliente_msg').removeClass('d-none');
        $('#credito_resumen_box').addClass('d-none');
        $('#select_credito').html('');
        $('#tabla_productos_recojo tbody').html('<tr><td colspan="5" class="text-center text-muted py-3">Seleccione un cliente y un crédito primero.</td></tr>');
        $('#btn_confirmar_recojo').prop('disabled', true);
        $('#buscar_cliente').val('');
        $('#resultados_cliente').hide().html('');
        $('#resumen_resultado_box').addClass('d-none').html('');
        $('#input_cerrar_credito').prop('checked', false);
    }

    function calcularValorRecuperadoEstimado() {
        let total = 0;
        $('.input-recuperar').each(function() {
            const cantidad = parseFloat($(this).val()) || 0;
            const precio = parseFloat($(this).data('precio')) || 0;
            total += cantidad * precio;
        });
        return total;
    }

    function actualizarResumenResultado() {
        const valorRecuperado = calcularValorRecuperadoEstimado();
        const cerrarManual = $('#input_cerrar_credito').is(':checked');
        const nuevoSaldo = Math.max(0, saldoPendienteActual - valorRecuperado);

        let mensaje = '';
        if (cerrarManual) {
            mensaje = '<i class="mdi mdi-lock-outline me-1"></i> El crédito se <strong>cerrará</strong> y se condonará el saldo restante (' + fmtMoney(nuevoSaldo) + ') como incobrable.';
        } else if (nuevoSaldo <= 0.009) {
            mensaje = '<i class="mdi mdi-check-circle-outline me-1"></i> Lo recuperado cubre todo el saldo pendiente: el crédito se <strong>cerrará</strong> automáticamente, sin nada que condonar.';
        } else {
            mensaje = '<i class="mdi mdi-progress-check me-1"></i> El crédito quedará <strong>activo</strong>, con un saldo estimado de ' + fmtMoney(nuevoSaldo) + ' repartido en las cuotas pendientes.';
        }
        $('#resumen_resultado_box').removeClass('d-none').html(mensaje);
    }

    function cargarProductosYResumenCredito(creditoId) {
        creditoSeleccionadoId = creditoId;
        $('#form_credito_id').val(creditoId);
        $('#tabla_productos_recojo tbody').html('<tr><td colspan="5" class="text-center text-muted py-3">Cargando productos...</td></tr>');
        $('#btn_confirmar_recojo').prop('disabled', true);
        $('#resumen_resultado_box').addClass('d-none').html('');

        fetch(urlDetalleCreditoBase + '/' + creditoId, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                saldoPendienteActual = parseFloat(data.saldo_pendiente) || 0;
                $('#resumen_saldo').text(fmtMoney(data.saldo_pendiente));
                $('#resumen_cuotas').text(data.cuotas_pendientes);
                $('#credito_resumen_box').removeClass('d-none');

                let html = '';
                if (data.productos && data.productos.length > 0) {
                    data.productos.forEach(function(p, i) {
                        const disponible = parseFloat(p.cantidad_disponible) || 0;
                        html += '<tr>';
                        html += '<td class="font-weight-bold text-dark">' + p.nomb_pro + '</td>';
                        html += '<td class="text-center">' + fmtMoney(p.precio) + '</td>';
                        html += '<td class="text-center">' + p.cantidad_vendida + '</td>';
                        html += '<td class="text-center">' + disponible + '</td>';
                        html += '<td class="text-center">';
                        html += '<input type="hidden" name="productos[' + i + '][producto_id]" value="' + p.producto_id + '">';
                        html += '<input type="number" step="0.01" min="0" max="' + disponible + '" value="0" ';
                        html += 'name="productos[' + i + '][cantidad]" class="form-control input-recuperar" data-producto-id="' + p.producto_id + '" data-precio="' + (p.precio || 0) + '"';
                        html += disponible <= 0 ? ' readonly' : '';
                        html += '>';
                        html += '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="5" class="text-center text-muted py-3">No se encontraron productos para este crédito.</td></tr>';
                }
                $('#tabla_productos_recojo tbody').html(html);
                $('#btn_confirmar_recojo').prop('disabled', false);
                actualizarResumenResultado();
            })
            .catch(function() {
                $('#tabla_productos_recojo tbody').html('<tr><td colspan="5" class="text-center text-danger py-3">Error al cargar los productos del crédito.</td></tr>');
            });
    }

    function seleccionarCliente(cliente) {
        $('#resultados_cliente').hide().html('');
        $('#buscar_cliente').val('');
        $('#sin_cliente_msg').addClass('d-none');
        $('#cliente_seleccionado_box').removeClass('d-none');
        $('#cliente_nombre_sel').text(cliente.nombre);
        $('#cliente_doc_sel').text(cliente.documento || '');

        $('#select_credito').html('<option value="">Cargando créditos...</option>');
        $('#credito_resumen_box').addClass('d-none');
        $('#tabla_productos_recojo tbody').html('<tr><td colspan="3" class="text-center text-muted py-3">Seleccione un crédito.</td></tr>');
        $('#btn_confirmar_recojo').prop('disabled', true);

        fetch(urlCreditosClienteBase + '/' + cliente.id, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function(res) { return res.json(); })
            .then(function(creditos) {
                if (!creditos || creditos.length === 0) {
                    $('#select_credito').html('<option value="">Este cliente no tiene créditos activos</option>');
                    return;
                }
                let html = '';
                creditos.forEach(function(c) {
                    html += '<option value="' + c.id + '">Crédito #' + c.id + ' — Saldo: ' + fmtMoney(c.saldo_pendiente) + '</option>';
                });
                $('#select_credito').html(html);
                cargarProductosYResumenCredito(creditos[0].id);
            })
            .catch(function() {
                $('#select_credito').html('<option value="">Error al cargar créditos</option>');
            });
    }

    $(document).ready(function() {
        $(".loader").fadeOut("slow");

        // Buscador de cliente
        let buscarTimeout = null;
        $('#buscar_cliente').on('input', function() {
            const q = $(this).val().trim();
            clearTimeout(buscarTimeout);
            if (q.length < 2) {
                $('#resultados_cliente').hide().html('');
                return;
            }
            buscarTimeout = setTimeout(function() {
                fetch(urlBuscarCliente + '?q=' + encodeURIComponent(q), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function(res) { return res.json(); })
                    .then(function(clientes) {
                        let html = '';
                        if (clientes && clientes.length > 0) {
                            clientes.forEach(function(c) {
                                html += '<a href="#" class="list-group-item list-group-item-action btn-elegir-cliente" ';
                                html += 'data-id="' + c.id + '" data-nombre="' + c.nombre + '" data-documento="' + (c.documento || '') + '">';
                                html += '<strong>' + c.nombre + '</strong><br><small class="text-muted">' + (c.documento || '') + '</small>';
                                html += '</a>';
                            });
                        } else {
                            html = '<div class="list-group-item text-muted">Sin resultados con crédito activo.</div>';
                        }
                        $('#resultados_cliente').html(html).show();
                    })
                    .catch(function() {
                        $('#resultados_cliente').html('<div class="list-group-item text-danger">Error al buscar.</div>').show();
                    });
            }, 350);
        });

        $(document).on('click', '.btn-elegir-cliente', function(e) {
            e.preventDefault();
            seleccionarCliente({
                id: $(this).data('id'),
                nombre: $(this).data('nombre'),
                documento: $(this).data('documento')
            });
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#buscar_cliente, #resultados_cliente').length) {
                $('#resultados_cliente').hide();
            }
        });

        $('#btn_cambiar_cliente').on('click', function() {
            resetFormulario();
        });

        $(document).on('input', '.input-recuperar', actualizarResumenResultado);
        $('#input_cerrar_credito').on('change', actualizarResumenResultado);

        $('#select_credito').on('change', function() {
            const id = $(this).val();
            if (id) {
                cargarProductosYResumenCredito(id);
            }
        });

        // Envío del formulario (submit nativo, para que la redirección con el mensaje flash funcione igual que en el resto del módulo)
        $('#form_recojo').on('submit', function(e) {
            if (!creditoSeleccionadoId) {
                e.preventDefault();
                alert('Seleccione un cliente y un crédito primero.');
                return false;
            }

            const valorRecuperado = calcularValorRecuperadoEstimado();
            const cerrarManual = $('#input_cerrar_credito').is(':checked');
            const nuevoSaldo = Math.max(0, saldoPendienteActual - valorRecuperado);
            const seCierra = cerrarManual || nuevoSaldo <= 0.009;

            let mensajeConfirm;
            if (seCierra) {
                mensajeConfirm = '¿Está seguro de procesar este recojo? Los productos recuperados ingresarán al almacén principal y el crédito quedará CERRADO'
                    + (nuevoSaldo > 0.009 ? (', condonando ' + fmtMoney(nuevoSaldo) + ' como incobrable.') : '.');
            } else {
                mensajeConfirm = '¿Está seguro de procesar este recojo? Los productos recuperados ingresarán al almacén principal y el crédito quedará ACTIVO con un saldo estimado de ' + fmtMoney(nuevoSaldo) + '.';
            }

            if (!confirm(mensajeConfirm)) {
                e.preventDefault();
                return false;
            }

            $('#btn_confirmar_recojo').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Procesando...');
        });

        // Filtro de historial por fetch POST, sin tocar la URL
        $('#form_filtros_historial').on('submit', async function(e) {
            e.preventDefault();
            const $btn = $(this).find('button[type="submit"]');
            const originalHtml = $btn.html();
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

            try {
                const response = await fetch(urlRecojoDatos, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams({
                        fecha_desde: $('#filtro_fecha_desde').val(),
                        fecha_hasta: $('#filtro_fecha_hasta').val()
                    })
                });
                if (!response.ok) throw new Error('Error al filtrar el historial.');
                const htmlText = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(htmlText, 'text/html');
                $('#historial-table-card').html(doc.querySelector('#historial-table-card').innerHTML);
            } catch (error) {
                console.error(error);
                alert('No se pudo filtrar el historial. Intente de nuevo.');
            } finally {
                $btn.prop('disabled', false).html(originalHtml);
            }
        });

        // Ver detalle de un recojo del historial
        $(document).on('click', '.btn-ver-recojo', function() {
            const id = $(this).data('id');

            $('#modal_recojo_loading').removeClass('d-none');
            $('#modal_recojo_contenido').addClass('d-none');
            $('#modal_recojo_error').addClass('d-none');
            $('#modal_recojo_subtitulo').text('Cargando información...');
            $('#modalDetalleRecojo').modal('show');

            fetch(urlVerRecojoBase + '/' + id, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data.error) throw new Error(data.error);

                    $('#modal_recojo_subtitulo').text(data.cliente_nombre + ' — ' + data.fecha);
                    $('#modal_recojo_vendedor').text(data.vendedor_recojo_nombre || '—');
                    $('#modal_recojo_usuario').text(data.usuario_nombre || '—');
                    $('#modal_recojo_recuperado').text(fmtMoney(data.valor_recuperado));
                    $('#modal_recojo_saldo').text(fmtMoney(data.saldo_incobrable));
                    $('#modal_recojo_estado').html(data.credito_cerrado
                        ? '<span class="badge bg-secondary">Cerrado</span>'
                        : '<span class="badge bg-success">Activo (reducido)</span>');
                    $('#modal_recojo_observacion').text(data.observacion || 'Sin observación.');

                    let html = '';
                    if (data.productos && data.productos.length > 0) {
                        data.productos.forEach(function(p) {
                            html += '<tr><td>' + p.nomb_pro + '</td><td class="text-center">' + p.cantidad + '</td></tr>';
                        });
                    } else {
                        html = '<tr><td colspan="2" class="text-center text-muted py-3">No se recuperó ningún producto en este recojo.</td></tr>';
                    }
                    $('#modal_recojo_productos').html(html);

                    $('#modal_recojo_loading').addClass('d-none');
                    $('#modal_recojo_contenido').removeClass('d-none');
                })
                .catch(function() {
                    $('#modal_recojo_loading').addClass('d-none');
                    $('#modal_recojo_error').removeClass('d-none');
                });
        });

        // Si se llega desde /anular-credito con un cliente ya identificado, precargar el buscador
        const paramsUrl = new URLSearchParams(window.location.search);
        const documentoPrellenado = paramsUrl.get('documento');
        if (documentoPrellenado) {
            $('#buscar_cliente').val(documentoPrellenado).trigger('input');
        }
    });
</script>
@endsection
