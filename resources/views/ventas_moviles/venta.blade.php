@extends('layouts.main')

@section('title', 'Registrar Venta')

@section('css')
<style>
    .mobile-container {
        max-width: 600px;
        margin: 0 auto;
        padding-bottom: 80px;
    }
    .product-list-item {
        border-radius: 12px;
        border: 1px solid #eef2f3;
        padding: 12px;
        background: #ffffff;
        margin-bottom: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s ease;
    }
    .product-list-item:active {
        background: #f8f9fa;
    }
    .cart-summary-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: #ffffff;
        box-shadow: 0 -4px 15px rgba(0,0,0,0.08);
        padding: 12px 20px;
        z-index: 1000;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .search-box {
        border-radius: 10px;
        border: 1px solid #ced4da;
        padding: 10px 15px;
    }
    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .qty-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: none;
        background: #e9ecef;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: #495057;
        transition: all 0.2s;
    }
    .qty-btn:active {
        background: #ced4da;
    }
    .btn-green-grad {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        padding: 10px 20px;
    }
    .btn-green-grad:hover, .btn-green-grad:focus {
        color: white;
    }
    .badge-stock {
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 6px;
    }
</style>
@endsection

@section('contenido')
<div class="mobile-container py-3">
    <!-- Encabezado / Regresar -->
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('vendedor.dashboard') }}" class="btn btn-light btn-rounded me-2">
            <i class="mdi mdi-arrow-left"></i>
        </a>
        <h4 class="mb-0 font-weight-bold" style="color: #2c3e50;">Registrar Nueva Venta</h4>
    </div>

    <!-- Tipo de Venta y Cliente -->
    <div class="card shadow-sm border-0 mb-3" style="border-radius: 12px;">
        <div class="card-body">
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label font-weight-bold">Tipo de Venta</label>
                    <select class="form-select" id="tipo_venta" style="border-radius: 8px;">
                        <option value="1">VENTA AL CONTADO</option>
                        <option value="2">VENTA AL CREDITO</option>
                    </select>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label font-weight-bold">Cliente en Ruta</label>
                    <select class="form-select select2" id="select_cliente" style="border-radius: 8px;">
                        <option value="">-- Seleccionar Cliente --</option>
                        @foreach($clientes as $cli)
                            <option value="{{ $cli->id }}" 
                                    data-documento="{{ $cli->documento }}" 
                                    data-nombre="{{ $cli->razon_social }}" 
                                    data-direccion="{{ $cli->dire_per }}" 
                                    data-telefono="{{ $cli->telefono }}"
                                    data-sector="{{ $cli->id_sector }}"
                                    data-tipodoc="{{ $cli->tipo_doc }}">
                                {{ $cli->razon_social }} ({{ $cli->documento }})
                            </option>
                        @endforeach
                        <option value="NUEVO">-- NUEVO CLIENTE (REGISTRAR) --</option>
                    </select>
                </div>
            </div>

            <!-- Formulario Cliente Rápido (Oculto inicialmente) -->
            <div id="form_nuevo_cliente" class="d-none border-top pt-3 mt-2">
                <h6 class="font-weight-bold mb-3 text-primary">Registrar Cliente Rápido</h6>
                <div class="row">
                    <div class="col-6 mb-2">
                        <label class="form-label font-size-12">Tipo Doc.</label>
                        <select class="form-select form-select-sm" id="tipo_doc_nuevo">
                            @foreach($tipo_documento as $td)
                                <option value="{{ $td->id }}">{{ $td->nomb_doc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label font-size-12">Número Doc.</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" id="doc_nuevo">
                            <button class="btn btn-outline-primary" type="button" id="btn_buscar_api">
                                <i class="mdi mdi-magnify"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-12 mb-2">
                        <label class="form-label font-size-12">Nombre / Razón Social</label>
                        <input type="text" class="form-control form-control-sm" id="nombre_nuevo">
                    </div>
                    <div class="col-12 mb-2">
                        <label class="form-label font-size-12">Dirección</label>
                        <input type="text" class="form-control form-control-sm" id="direccion_nuevo">
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label font-size-12">Celular</label>
                        <input type="text" class="form-control form-control-sm" id="celular_nuevo">
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label font-size-12">Sector</label>
                        <select class="form-select form-select-sm" id="sector_nuevo">
                            @foreach($sectores as $sec)
                                <option value="{{ $sec->id }}">{{ $sec->nomb_sec }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Productos en la Furgoneta -->
    <h5 class="font-weight-bold mb-3" style="color: #2c3e50; padding-left: 5px;">Productos en Furgoneta</h5>
    <div class="mb-3">
        <input type="text" class="form-control search-box" id="buscar_producto" placeholder="🔍 Buscar producto por nombre...">
    </div>

    <div id="productos_lista_wrapper">
        @if($productos->isEmpty())
            <div class="text-center py-4 bg-white shadow-sm rounded p-3">
                <i class="mdi mdi-inbox-remove-outline text-muted" style="font-size: 40px;"></i>
                <p class="text-muted mt-2 mb-0">No hay stock disponible en la furgoneta.</p>
            </div>
        @else
            @foreach($productos as $prod)
                <div class="product-list-item" data-id="{{ $prod->id }}" data-nombre="{{ $prod->nomb_pro }}">
                    <div style="flex: 1;">
                        <span class="font-weight-bold d-block text-truncate" style="max-width: 250px; font-size: 14px;">{{ $prod->nomb_pro }}</span>
                        <div class="mt-1 d-flex gap-2 align-items-center">
                            <span class="badge-stock bg-soft-info text-info font-weight-bold">Contado: S/ <span class="precio-contado">{{ $prod->precio_contado }}</span></span>
                            <span class="badge-stock bg-soft-warning text-warning font-weight-bold">Crédito: S/ <span class="precio-credito">{{ $prod->precio_credito }}</span></span>
                        </div>
                        <span class="badge bg-soft-success text-success mt-2 d-inline-block">Stock: <span class="prod-stock">{{ $prod->stock }}</span></span>
                    </div>

                    <div class="quantity-controls">
                        <button class="qty-btn btn-minus" type="button">-</button>
                        <span class="qty-display font-weight-bold" style="font-size: 16px; min-width: 20px; text-align: center;">0</span>
                        <button class="qty-btn btn-plus" type="button">+</button>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

<!-- Barra de Resumen / Acción Inferior -->
<div class="cart-summary-bar">
    <div>
        <span class="text-muted font-size-12 d-block">Monto Total</span>
        <h4 class="mb-0 font-weight-bold text-success" id="cart_total_display">S/ 0.00</h4>
    </div>
    <button class="btn btn-green-grad px-4 py-2 shadow" id="btn_continuar_venta" disabled>
        Continuar <i class="mdi mdi-arrow-right ms-1"></i>
    </button>
</div>

<!-- Modal para finalizar venta y cobrar -->
<div class="modal fade" id="modalFinalizarVenta" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title font-weight-bold text-dark">Finalizar y Cobrar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_finalizar_venta">
                <div class="modal-body p-4">
                    <h2 class="text-center font-weight-bold text-success mb-4" id="modal_total_display">S/ 0.00</h2>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Comprobante</label>
                        <select class="form-select" name="documento" id="select_documento" required>
                            @foreach($comprobantes as $comp)
                                <option value="{{ $comp->id }}" {{ $comp->id == 9 ? 'selected' : '' }}>{{ $comp->descripcion }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3" id="wrapper_forma_pago">
                        <label class="form-label font-weight-bold">Forma de Pago</label>
                        <select class="form-select" name="forma_pago" id="select_forma_pago" required>
                            @foreach($forma_pagos as $fp)
                                <option value="{{ $fp->id }}">{{ $fp->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Detalles depósito / aplicativos -->
                    <div id="pago_operacion_wrapper" class="d-none border p-3 rounded mb-3 bg-light">
                        <div class="mb-2">
                            <label class="form-label font-weight-bold">Banco</label>
                            <select class="form-select" name="banco_venta" id="select_banco">
                                <option value="">-- Seleccionar Banco --</option>
                                @foreach($bancos as $b)
                                    <option value="{{ $b->id }}">{{ $b->abreviatura }} - {{ $b->cuenta_corriente }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label font-weight-bold">N° de Operación</label>
                            <input type="text" class="form-control" name="numero_operacion" id="input_operacion">
                        </div>
                    </div>

                    <!-- Campos de Efectivo -->
                    <div class="row mb-3" id="efectivo_calculo_wrapper">
                        <div class="col-6">
                            <label class="form-label font-weight-bold">Recibido (S/)</label>
                            <input type="number" step="0.01" class="form-control" name="total_recibido" id="input_recibido" value="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label font-weight-bold">Vuelto (S/)</label>
                            <input type="number" class="form-control bg-light" name="vuelto" id="input_vuelto" value="0" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Atrás</button>
                    <button type="submit" class="btn btn-success px-4" id="btn_guardar_venta_final">
                        Confirmar Venta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Éxito / Ticket -->
<div class="modal fade" id="modalVentaExitosa" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <i class="mdi mdi-checkbox-marked-circle-outline text-success" style="font-size: 72px;"></i>
                </div>
                <h4 class="font-weight-bold text-dark mb-2">¡Venta Registrada!</h4>
                <p class="text-muted mb-4">La venta ha sido guardada exitosamente y queda pendiente de liquidación.</p>

                <div class="d-grid gap-2">
                    <a href="#" id="link_ticket" target="_blank" class="btn btn-primary btn-lg">
                        <i class="mdi mdi-receipt me-2"></i> Imprimir / Ver Ticket
                    </a>
                    <a href="{{ route('vendedor.dashboard') }}" class="btn btn-outline-secondary">
                        Volver al Dashboard
                    </a>
                    <button type="button" class="btn btn-light" onclick="window.location.reload();">
                        Registrar Otra Venta
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $(".loader").fadeOut("slow");

        // Estado del Carrito
        let cart = {};

        // Filtrar productos
        $('#buscar_producto').on('keyup', function() {
            let value = $(this).val().toLowerCase();
            $('#productos_lista_wrapper .product-list-item').filter(function() {
                $(this).toggle($(this).attr('data-nombre').toLowerCase().indexOf(value) > -1)
            });
        });

        // Manejar Cliente Nuevo vs Existente
        $('#select_cliente').on('change', function() {
            if ($(this).val() === 'NUEVO') {
                $('#form_nuevo_cliente').removeClass('d-none');
            } else {
                $('#form_nuevo_cliente').addClass('d-none');
            }
        });

        // Buscar en RUC/DNI API
        $('#btn_buscar_api').on('click', function() {
            let doc = $('#doc_nuevo').val();
            let tipo = $('#tipo_doc_nuevo').val();
            if (!doc) return alert('Ingrese un número de documento.');

            $(".loader").fadeIn("fast");
            $.ajax({
                url: "{{ url('ventas/consultar_dni_ruc') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    tipo_documento: tipo,
                    num_doc: doc
                },
                success: function(res) {
                    $(".loader").fadeOut("fast");
                    if (res.exception === 'existe_base_datos') {
                        alert('El cliente ya existe en la base de datos local. Seleccionándolo...');
                        $('#form_nuevo_cliente').addClass('d-none');
                        // Buscar el cliente en el select
                        let option = $('#select_cliente option').filter(function() {
                            return $(this).attr('data-documento') === doc;
                        });
                        if (option.length) {
                            $('#select_cliente').val(option.val()).trigger('change');
                        }
                    } else if (res.nombres || (res.original && res.original.nombres)) {
                        let nombres = res.nombres || res.original.nombres;
                        let direccion = res.direccion || res.original.direccion || '';
                        $('#nombre_nuevo').val(nombres);
                        $('#direccion_nuevo').val(direccion);
                    } else {
                        alert('No se encontraron resultados en la consulta.');
                    }
                },
                error: function() {
                    $(".loader").fadeOut("fast");
                    alert('Error al consultar el documento.');
                }
            });
        });

        // Cambios en los botones de cantidad (+/-)
        $('.btn-plus').on('click', function() {
            let item = $(this).closest('.product-list-item');
            let id = item.attr('data-id');
            let nombre = item.attr('data-nombre');
            let stock = parseInt(item.find('.prod-stock').text());
            let priceContado = parseFloat(item.find('.precio-contado').text());
            let priceCredito = parseFloat(item.find('.precio-credito').text());

            if (!cart[id]) {
                cart[id] = { id: id, nombre: nombre, qty: 0, stock: stock, priceContado: priceContado, priceCredito: priceCredito };
            }

            if (cart[id].qty < stock) {
                cart[id].qty++;
                item.find('.qty-display').text(cart[id].qty);
                updateTotals();
            } else {
                alert('No hay más stock disponible en la furgoneta.');
            }
        });

        $('.btn-minus').on('click', function() {
            let item = $(this).closest('.product-list-item');
            let id = item.attr('data-id');

            if (cart[id] && cart[id].qty > 0) {
                cart[id].qty--;
                item.find('.qty-display').text(cart[id].qty);
                if (cart[id].qty === 0) {
                    delete cart[id];
                }
                updateTotals();
            }
        });

        $('#tipo_venta').on('change', function() {
            updateTotals();
        });

        // Actualizar Totales del carrito
        function updateTotals() {
            let tipoVenta = $('#tipo_venta').val();
            let total = 0;
            let count = 0;

            for (let id in cart) {
                let p = cart[id];
                let price = (tipoVenta === '1') ? p.priceContado : p.priceCredito;
                total += p.qty * price;
                count += p.qty;
            }

            $('#cart_total_display').text('S/ ' + total.toFixed(2));
            $('#modal_total_display').text('S/ ' + total.toFixed(2));
            $('#input_recibido').val(total.toFixed(2));
            $('#input_vuelto').val('0.00');

            if (count > 0) {
                $('#btn_continuar_venta').prop('disabled', false);
            } else {
                $('#btn_continuar_venta').prop('disabled', true);
            }
        }

        // Continuar a la pantalla de Pago
        $('#btn_continuar_venta').on('click', function() {
            let clienteVal = $('#select_cliente').val();
            if (!clienteVal) {
                alert('Debe seleccionar o registrar un cliente antes de continuar.');
                return;
            }
            $('#modalFinalizarVenta').modal('show');
        });

        // Ocultar/mostrar banco e input de operación según forma de pago
        $('#select_forma_pago').on('change', function() {
            let val = $(this).val();
            if (val === '1') { // Efectivo
                $('#pago_operacion_wrapper').addClass('d-none');
                $('#efectivo_calculo_wrapper').removeClass('d-none');
            } else {
                $('#pago_operacion_wrapper').removeClass('d-none');
                $('#efectivo_calculo_wrapper').addClass('d-none');
            }
        });

        // Calcular vuelto
        $('#input_recibido').on('input', function() {
            let recibido = parseFloat($(this).val()) || 0;
            let totalText = $('#modal_total_display').text().replace('S/ ', '');
            let total = parseFloat(totalText) || 0;
            let vuelto = recibido - total;
            $('#input_vuelto').val(vuelto >= 0 ? vuelto.toFixed(2) : '0.00');
        });

        // Enviar Formulario Venta Completo
        $('#form_finalizar_venta').on('submit', function(e) {
            e.preventDefault();

            let clienteVal = $('#select_cliente').val();
            let numDoc = '', nomCli = '', dirCli = '', telCli = '', tipoDoc = '1', sectorId = '';

            if (clienteVal === 'NUEVO') {
                numDoc = $('#doc_nuevo').val();
                nomCli = $('#nombre_nuevo').val();
                dirCli = $('#direccion_nuevo').val();
                telCli = $('#celular_nuevo').val();
                tipoDoc = $('#tipo_doc_nuevo').val();
                sectorId = $('#sector_nuevo').val();

                if (!numDoc || !nomCli) {
                    alert('Debe completar los campos del cliente nuevo.');
                    return;
                }
            } else {
                let opt = $('#select_cliente option:selected');
                numDoc = opt.attr('data-documento');
                nomCli = opt.attr('data-nombre');
                dirCli = opt.attr('data-direccion');
                telCli = opt.attr('data-telefono');
                tipoDoc = opt.attr('data-tipodoc');
                sectorId = opt.attr('data-sector');
            }

            // Preparar arrays del carrito
            let quanty = [];
            let idproducto = [];
            let priceproducto = [];
            let nameproducto = [];
            let importe = [];
            let ubicacion = [];
            let tipoVenta = $('#tipo_venta').val();

            for (let id in cart) {
                let p = cart[id];
                let price = (tipoVenta === '1') ? p.priceContado : p.priceCredito;
                quanty.push(p.qty);
                idproducto.push(p.id);
                priceproducto.push(price);
                nameproducto.push(p.nombre);
                importe.push((p.qty * price).toFixed(2));
                ubicacion.push("{{ $ubicacion_id }}");
            }

            let totalVentaVal = $('#modal_total_display').text().replace('S/ ', '');

            let dataToSend = {
                _token: "{{ csrf_token() }}",
                es_movil: 1,
                documento: $('#select_documento').val(),
                tipoDocumentoIdentidad: tipoDoc,
                numeroDocumento: numDoc,
                nombre_cliente: nomCli,
                direccion_cliente: dirCli,
                celular_cliente: telCli,
                correo_cliente: '',
                sectores: sectorId,
                forma_pago: $('#select_forma_pago').val(),
                tipo_venta: tipoVenta,
                total_venta: totalVentaVal,
                total_recibido: $('#input_recibido').val(),
                vuelto: $('#input_vuelto').val(),
                numero_operacion: $('#input_operacion').val(),
                banco_venta: $('#select_banco').val(),
                fecha_venta: "{{ date('Y-m-d') }}",
                vendedor: "{{ $vendedor->id }}",
                quanty: quanty,
                idproducto: idproducto,
                priceproducto: priceproducto,
                nameproducto: nameproducto,
                importe: importe,
                ubicacion: ubicacion
            };

            $('#btn_guardar_venta_final').prop('disabled', true).text('Guardando...');
            $(".loader").fadeIn("fast");

            $.ajax({
                url: "{{ route('vendedor.venta.guardar') }}",
                type: 'POST',
                data: dataToSend,
                success: function(response) {
                    $(".loader").fadeOut("fast");
                    if (response.respuesta === 'ok') {
                        $('#modalFinalizarVenta').modal('hide');
                        $('#link_ticket').attr('href', "{{ url('ventas/ticket') }}/" + response.id);
                        $('#modalVentaExitosa').modal('show');
                    } else {
                        $('#btn_guardar_venta_final').prop('disabled', false).text('Confirmar Venta');
                        alert('Error al guardar la venta: ' + (response.mensaje || response.error));
                    }
                },
                error: function(xhr) {
                    $(".loader").fadeOut("fast");
                    $('#btn_guardar_venta_final').prop('disabled', false).text('Confirmar Venta');
                    alert('Error en la comunicación con el servidor.');
                }
            });
        });
    });
</script>
@endsection
