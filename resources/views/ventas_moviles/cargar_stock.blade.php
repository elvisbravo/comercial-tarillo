@extends('layouts.main')

@section('title', 'Carga Diaria de Stock a Furgonetas')

@section('css')
<style>
    .cargar-card {
        border-radius: 16px;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
        border: none;
        background: #ffffff;
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
        padding: 12px 15px;
        border: none;
    }
    .table-premium tbody td {
        padding: 12px 15px;
        vertical-align: middle;
    }
    .btn-action {
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .select2-container .select2-selection--single {
        height: 38px !important;
        border-radius: 8px !important;
        border: 1px solid #cbd5e1 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
</style>
<!-- Select2 CSS si existe, si no, se usa un select estándar estilizado -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('contenido')
<div class="container-fluid py-3">
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
                </div>
            </div>
        </div>
    </div>

    <!-- Título de Página -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18 text-primary font-weight-bold">
                    <i class="mdi mdi-truck-load me-2"></i> Carga Diaria de Stock a Furgonetas
                </h4>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 10px;">
            <strong>¡Éxito!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 10px;">
            <strong>¡Atención!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Formulario e Inputs de Selección -->
        <div class="col-lg-4 mb-4">
            <div class="card cargar-card">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="font-weight-bold text-dark mb-0">Detalles de la Carga</h5>
                    <p class="text-muted font-size-12 mb-0">Seleccione el vendedor y agregue los productos a la carga diaria.</p>
                </div>
                <div class="card-body px-4">
                    <form action="{{ route('admin.cargar_stock.procesar') }}" method="POST" id="form_cargar_stock">
                        @csrf
                        
                        <!-- Selección del Vendedor -->
                        <div class="mb-4">
                            <label class="form-label font-weight-bold text-dark">Vendedor Destinatario</label>
                            <select class="form-select" name="vendedor_id" id="vendedor_select" required style="border-radius: 8px;">
                                <option value="">-- Seleccionar Vendedor --</option>
                                @foreach($vendedores as $v)
                                    <option value="{{ $v->id }}" data-furgoneta="{{ $v->stockLocation->name ?? 'Furgoneta' }}">
                                        {{ $v->nombre }} ({{ $v->stockLocation->name ?? 'Sin furgoneta' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <hr class="my-4" style="border-top: 1px dashed #cbd5e1;">

                        <!-- Selección del Producto -->
                        <h6 class="font-weight-bold text-dark mb-3">Agregar Producto</h6>
                        
                        <div class="mb-3">
                            <label class="form-label font-size-13 text-muted">Buscar Producto (Almacén Principal)</label>
                            <select class="form-select select2" id="producto_select" style="width: 100%;">
                                <option value="">-- Buscar Producto --</option>
                                @foreach($productos as $p)
                                    <option value="{{ $p->id }}" data-nombre="{{ $p->nomb_pro }}" data-stock="{{ $p->stock }}">
                                        {{ $p->nomb_pro }} [Stock: {{ $p->stock }}]
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label font-size-13 text-muted">Stock Disponible</label>
                                <input type="text" class="form-control" id="stock_disponible" readonly style="border-radius: 8px; background-color: #f1f5f9; font-weight: bold; text-align: center;">
                            </div>
                            <div class="col-6">
                                <label class="form-label font-size-13 text-muted">Cantidad a Cargar</label>
                                <input type="number" class="form-control" id="cantidad_cargar" min="1" style="border-radius: 8px; font-weight: bold; text-align: center;">
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="button" class="btn btn-info btn-action" id="btn_agregar_item">
                                <i class="mdi mdi-plus-circle me-1"></i> Agregar a la Lista
                            </button>
                        </div>

                        <!-- Botón Submit Final -->
                        <div class="d-grid pt-3 border-top mt-4">
                            <button type="submit" class="btn btn-success btn-lg btn-action py-2" id="btn_confirmar_carga" disabled>
                                <i class="mdi mdi-check-all me-1"></i> Procesar Carga de Stock
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabla de Productos Agregados -->
        <div class="col-lg-8 mb-4">
            <div class="card cargar-card">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="font-weight-bold text-dark mb-0">Lista de Productos a Transferir</h5>
                    <span class="badge bg-soft-info text-info font-size-12 px-2 py-1" id="items_count">0 productos agregados</span>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive" style="min-height: 250px;">
                        <table class="table table-premium align-middle text-nowrap mb-0" id="tabla_items_carga">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center" style="width: 150px;">Stock Origen</th>
                                    <th class="text-center" style="width: 150px;">Cant. Cargar</th>
                                    <th class="text-center" style="width: 100px;">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="tr-empty">
                                    <td colspan="4" class="text-center text-muted py-5">
                                        <i class="mdi mdi-package-variant-closed font-size-36 d-block mb-2"></i>
                                        La lista de productos está vacía. Seleccione productos a la izquierda para agregarlos a la furgoneta.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $(".loader").fadeOut("slow");

        // Inicializar Select2 para búsqueda rápida
        $('.select2').select2({
            placeholder: "-- Buscar Producto --",
            allowClear: true
        });

        // Al cambiar de producto, mostrar su stock disponible
        $('#producto_select').on('change', function() {
            let option = $(this).find(':selected');
            let stock = option.attr('data-stock') || 0;
            $('#stock_disponible').val(stock);
            if(stock > 0) {
                $('#cantidad_cargar').val(1).attr('max', stock);
            } else {
                $('#cantidad_cargar').val('');
            }
        });

        // Agregar producto a la tabla
        $('#btn_agregar_item').on('click', function() {
            let prodId = $('#producto_select').val();
            let option = $('#producto_select').find(':selected');
            let nombre = option.attr('data-nombre');
            let stock = parseInt(option.attr('data-stock')) || 0;
            let cantidad = parseInt($('#cantidad_cargar').val()) || 0;

            if (!prodId) {
                alert('Por favor, seleccione un producto.');
                return;
            }
            if (cantidad <= 0) {
                alert('La cantidad a cargar debe ser mayor a 0.');
                return;
            }
            if (cantidad > stock) {
                alert('No puede transferir más del stock disponible en el origen (Stock: ' + stock + ').');
                return;
            }

            // Validar si ya está agregado en la tabla
            if ($('#tabla_items_carga tbody find input[name="productos[]"][value="' + prodId + '"]').length > 0 || $('#item_row_' + prodId).length > 0) {
                alert('Este producto ya ha sido agregado a la lista de carga.');
                return;
            }

            // Quitar fila vacía si existe
            $('.tr-empty').remove();

            // Agregar fila a la tabla
            let htmlRow = `
                <tr id="item_row_${prodId}">
                    <td>
                        <span class="font-weight-bold text-dark">${nombre}</span>
                        <input type="hidden" name="productos[]" value="${prodId}">
                    </td>
                    <td class="text-center font-weight-bold text-muted">${stock} unidades</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <input type="number" name="cantidades[${prodId}]" class="form-control text-center font-weight-bold input-cantidad-tabla" value="${cantidad}" min="1" max="${stock}" required style="max-width: 100px; border-radius: 8px;">
                        </div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-circle btn-remove-item" data-id="${prodId}" title="Quitar de la carga">
                            <i class="mdi mdi-close"></i>
                        </button>
                    </td>
                </tr>
            `;

            $('#tabla_items_carga tbody').append(htmlRow);

            // Limpiar selección
            $('#producto_select').val(null).trigger('change');
            $('#stock_disponible').val('');
            $('#cantidad_cargar').val('');

            actualizarContadorItems();
        });

        // Eliminar fila de la tabla
        $(document).on('click', '.btn-remove-item', function() {
            let id = $(this).attr('data-id');
            $('#item_row_' + id).remove();

            // Si queda vacío, volver a mostrar el mensaje
            if ($('#tabla_items_carga tbody tr').length === 0) {
                let htmlEmpty = `
                    <tr class="tr-empty">
                        <td colspan="4" class="text-center text-muted py-5">
                            <i class="mdi mdi-package-variant-closed font-size-36 d-block mb-2"></i>
                            La lista de productos está vacía. Seleccione productos a la izquierda para agregarlos a la furgoneta.
                        </td>
                    </tr>
                `;
                $('#tabla_items_carga tbody').html(htmlEmpty);
            }

            actualizarContadorItems();
        });

        // Actualizar contador y habilitar/deshabilitar botón de confirmar
        function actualizarContadorItems() {
            let count = $('#tabla_items_carga tbody tr').not('.tr-empty').length;
            $('#items_count').text(count + ' producto' + (count !== 1 ? 's' : '') + ' agregado' + (count !== 1 ? 's' : ''));
            
            if (count > 0) {
                $('#btn_confirmar_carga').prop('disabled', false);
            } else {
                $('#btn_confirmar_carga').prop('disabled', true);
            }
        }

        // Validación final antes de enviar
        $('#form_cargar_stock').on('submit', function() {
            let vendedorNombre = $('#vendedor_select option:selected').text().trim();
            let count = $('#tabla_items_carga tbody tr').not('.tr-empty').length;

            if ($('#vendedor_select').val() === '') {
                alert('Por favor, seleccione el vendedor.');
                return false;
            }

            return confirm('¿Está seguro de procesar la carga diaria de ' + count + ' producto(s) a ' + vendedorNombre + '?');
        });
    });
</script>
@endsection
