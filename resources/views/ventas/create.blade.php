@extends('layouts.main')

@section('title')
Ventas
@endsection

@section('css')

<link href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}" rel="stylesheet" type="text/css" />

<!-- Sweet Alert-->
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

<style>
    .imagen-producto {
        min-height: 100px;
        max-height: 100px;
    }

    .card-producto {
        min-height: 170px;
        max-height: 170px;
        cursor: pointer;
    }

    .datos_credito {
        display: none;
    }

</style>

@endsection

@section('contenido')

<nav class="page-breadcrumb">
    <ol class="breadcrumb p-0">
        <li class="breadcrumb-item">
            <h4>Crear Nueva Venta</h4>
        </li>
    </ol>
</nav>

<form id="form_venta" method="POST">

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label" for="formrow-email-input">Tipo de venta</label>
                <select class="form-select" id="tipo_venta" name="tipo_venta">
                    <option value="1">Venta al Contado</option>
                    <option value="2">Venta al Crédito</option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label" for="formrow-email-input">Comprobante</label>
                <select class="form-select" name="tipo_comprobante" id="tipo_comprobante">
                    <option value="1">Boleta de venta electronica</option>
                    <option value="2">Factura electronica</option>
                    <option value="5"></option>
                </select>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="mb-3">
                <label class="form-label" for="formrow-email-input">Tipo Doc. Ident</label>
                <select class="form-select" name="documento_identidad" id="documento_identidad">
                    @foreach ($tipo_documento as $documento)
                    @if ($documento->id == 1)
                    <option value="{{ $documento->id }}" selected="true">{{ $documento->nombre }}</option>
                    @else
                    <option value="{{ $documento->id }}">{{ $documento->nombre }}</option>
                    @endif
                    @endforeach

                </select>
            </div>
        </div>

        <div class="col-md-3">
            <div class="mb-3">
                <label class="form-label" for="formrow-email-input">N° de Documento de Identidad</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="numero_documento" placeholder="Número de documento aqui!" name="numero_documento" required="true">
                    <button class="btn btn-primary" type="button" id="btn_consultar"><i class="bx bx-search-alt align-middle"></i></button>
                </div>
            </div>
        </div>

        <div id="autocomplete-list"></div>

        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label" for="formrow-email-input">Nombre del cliente</label>
                <input type="text" class="form-control" placeholder="Nombre o Razón Social aqui!" name="cliente" id="name_cliente" required="true">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="mb-3">
                <label class="form-label" for="formrow-email-input">Dirección</label>
                <input type="text" class="form-control" placeholder="Escribe la dirección completa aqui!" name="direccion" id="street" required="true">
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label class="form-label" for="formrow-email-input">Correo</label>
                <input type="text" class="form-control" placeholder="Correo Electrónico aqui!" name="correo">
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="formrow-email-input">Num. Celular</label>
            <input type="text" class="form-control" placeholder="Escribe el número del celular" name="numero_celular">
        </div>
        <div class="col-md-3">
            <label class="form-label" for="formrow-email-input">Almacen</label>
           <select name="" id="almacen_id" class="form-control">
                       <option value="">Yurimaguas</option>
           </select>
        </div>
    </div>

    <div class="row">

        <div class="col-md-7">
            <div class="card">

                <div class="card-body">

                    <div class="input-group">
                        <div class="input-group-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </div>
                        <input type="text" class="form-control" id="buscar_producto" placeholder="Buscar Producto">
                    </div>

                    <div class="row mt-2" id="render_productos">

                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-body p-0">
                    <div class="datos-detalle">
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>CANT.</th>
                                    <th>PRODUCTO</th>
                                    <th>P.U</th>
                                    <th>IMPORTE</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="content_detalle">
                                <tr>
                                    <input type="hidden" id="subtotal">
                                    <th colspan="3" class="text-end">SUBTOTAL:</th>
                                    <td>S/ <span id="subtotal_text">0.00</span></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <input type="hidden" id="total_descuentos">
                                    <th colspan="3" class="text-end">DESCUENTO:</th>
                                    <td><input type="text" class="form-control" id="descuentos" name="descuentos" value="0.00" style="width: 100px;"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <input type="hidden" name="total_num" id="totales">
                                    <input type="hidden" name="total_num" id="total_num">
                                    <th colspan="3" class="text-end"><h3>TOTAL:</h3></th>
                                    <td>
                                        <h3>S/ <span id="total_text">0.00</span></h3>
                                    </td>
                                    <td></td>
                                </tr>

                                <tr class="datos_credito">
                                    <th colspan="3" class="text-end">MONTO INICIAL:</th>
                                    <td>S/ <span id="monto_inicial">0.00</span></td>
                                    <td></td>
                                </tr>
                                <tr class="datos_credito">
                                    <th colspan="3" class="text-end">MESES:</th>
                                    <td><span id="meses_credito">0</span></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>

                    </div>

                    <div class="row px-3">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="formrow-email-input">Forma de Pago</label>
                                <select class="form-select" id="forma_pago" name="forma_pago">
                                    <option value="1">Efectivo</option>
                                    <option value="2">visa</option>
                                    <option value="3">Yape</option>
                                    <option value="4">Plin</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="formrow-email-input">Total Recibido S/</label>
                                <input type="text" class="form-control" name="total_recibido" id="total_recibido">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="formrow-email-input">Vuelto</label>
                                <input type="text" class="form-control" readonly="true" name="vuelto" id="vuelto" value="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 text-center">
                            <a href="#" class="btn btn-danger">CANCELAR</a>
                            <button type="submit" class="btn btn-primary">GENERAR VENTA</button>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>

</form>

<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="modal_producto">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleModal"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="idProducto">
                <input type="hidden" id="price-producto">
                <input type="hidden" id="modal_descuento">

                <div class="mx-2">
                    <img class="img-fluid rounded-circle" src="http://localhost/es_restaurante/public/img/default.jpg" alt="" style="height: 200px">
                </div>

                <div class="row mt-3">
                    <div class="col-sm-4 col-4">
                        <p>STOCK:</p>
                    </div>
                    <div class="col-sm-8 col-8">
                        <p id="stock_producto">10</p>
                    </div>
                </div>

                <div class="row">
                    <label for="cantidad" class="col-sm-3 col-3 col-form-label">CANT: </label>
                    <div class="col-sm-9 col-9">
                        <input class="form-control" type="text" id="cantidad_producto">
                    </div>
                </div>

                <div class="d-grid mt-3">
                    <button type="button" class="btn btn-primary" onclick="agregar_detalle()">Agregar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_enviar" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h4 class="text-center">Generando venta...</h4>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-sm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="modal_caja">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleModal">Alerta de Caja</h5>
            </div>
            <div class="modal-body text-center">
                <h4 id="mensaje_caja" class="mb-4"></h4>
                <a class="btn btn-danger" id="ir_caja">Ir a caja</a>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')

<!-- Sweet Alerts js -->
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<script src="{{ asset('assets/js/pages/jquery.bootstrap-touchspin.js') }}"></script>

<script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>

<script src="{{ asset('js/venta_nuevo.js') }}">
</script>

@endsection