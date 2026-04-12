@extends('layouts.main')

@section('title')
Pos
@endsection

@section('css')
<link href="{{ asset('css/glide.core.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/glide.theme.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ asset('css/jquery.bootstrap-touchspin.css') }}" rel="stylesheet" type="text/css" />

<style>
    .categorias {
        background-color: #249a6a;
    }

    .glide__slide {
        text-align: center;
        border: 0.3px solid #229164;
        border-radius: 10px;
    }

    #glide3 .glide__slide {
        position: relative;
        height: 30px;
    }

    #glide3 a {
        display: block;
        width: 100%;
        position: absolute;
        bottom: 4px;
        color: white;
        font-size: 13px;
        font-weight: bold;
    }

    /*#glide3 img {
        margin: 0 auto;
        display: block;
        max-width: 90%;
        max-height: 60px;
    }*/

    #modal_cobrar {
        cursor: pointer;
    }

    #mi-spinner {
        display: none;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
</style>
@endsection

@section('contenido')

<div class="row">

</div>
<div class="row">

    <div id="mi-spinner" class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>

    <div class="col-lg-8">
        <div class="row">

            <div class="input-group mb-3">
                <div class="col-md-8">
                    <input type="text" class="form-control" placeholder="Buscar producto" id="buscar_producto">
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="tipo_venta">
                        <option value="1">VENTA AL CONTADO</option>
                        <option value="2">VENTA AL CREDITO</option>
                    </select>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="wrap">
                <div class="glide" id="glide3">
                    <div class="glide__track" data-glide-el="track">
                        <ul class="glide__slides" id="listCategories">
                            
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="listProducts" style="height: calc(100vh - 239.63px); overflow-y: auto">

        </div>
    </div>
    <div class="col-lg-4" style="position: relative; height: calc(100vh - 90px); padding: 0">
        <div class="row mb-2">
            <div class="col-md-6">
                <select class="form-select" id="almacen">
                    @foreach ($almacenes as $item)
                        <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <select class="form-select" id="ubicacion">
                    @foreach ($ubicaciones as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div style="overflow-y: auto; height: 340px">
            <form id="detalle_venta">
                <input type="hidden" name="tipo_venta" id="typeVenta" value="1">
                <table class="table-sm" id="tableCarrito">
                    <tbody id="contentCarrito">
                        
                    </tbody>
                </table>
            </form>
        </div>

        <div class="fixed-bottom" style="position: absolute; bottom: 0; right: 0;">
            <div class="row">
                <hr>
                <div class="col-md-6 d-none" id="detalleCredito">
                    <p style="margin-left: 10px;">Monto Inicial: S/<span id="monto_inicial">0.00</span></p>
                    <p style="margin-left: 10px;">Meses: <span id="meses">0</span></p>
                </div>
                <div class="col-md-6" id="resumenVenta">
                    <p class="text-end" style="margin-right: 10px;">Subtotal: S/<span id="subtotal">0.00</span></p>
                    <p class="text-end" style="margin-right: 10px;">Igv: S/0.00</p>
                    <p class="text-end" style="margin-right: 10px;">Icbper: S/0.00</p>
                </div>
                
            </div>
            <div style="height: 60px; background: blue" onclick="open_modal_cobrar()" id="modal_cobrar">
                <div class="row">
                    <div class="col-md-6 col-6">
                        <h4 class="text-white p-3"><i class="fas fa-angle-right"></i> PAGO </h4>
                    </div>
                    <div class="col-md-6 col-6">
                        <h4 class="text-white p-3 text-end">S/<span id="totalbtn">0.00</span> </h4>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- MODALES -->
<div class="modal fade bs-example-modal-center" id="modalPrecios" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">PRECIOS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table" id="data_precio">
                    
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary waves-effect waves-light" id="modificarPrecio">Guardar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true" id="view_modal_cobrar">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myExtraLargeModalLabel">COBRAR VENTA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="venta_info">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Documento: </label>
                                <select class="form-select" id="documento" name="documento">
                                    
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Tipo Doc. Iden: </label>
                                <select class="form-select" id="tipoDocumentoIdentidad" name="tipoDocumentoIdentidad">
                                    
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">N° de Documento de Identidad: </label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="numeroDocumento" name="numeroDocumento" placeholder="Número de documento Aquí!" required>
                                <button class="btn btn-outline-primary" type="button" id="btnConsulta">
                                    <i class="bx bx-search-alt-2"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Nombre Cliente: </label>
                                <input class="form-control" type="text" id="nombre_cliente" name="nombre_cliente" placeholder="Nombre o Razón Social Aquí!">
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label">Dirección: </label>
                                <input class="form-control" type="text" id="direccion_cliente" name="direccion_cliente" placeholder="Escriba la dirección completa Aquí!">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Num. Celular: </label>
                                <input class="form-control" type="text" name="celular_cliente" id="celular_cliente" placeholder="Escriba el número de celular">
                            </div>
                        </div>

                        <input class="form-control" type="hidden" id="correo_cliente" name="correo_cliente">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Sectores: </label>
                                <select name="sectores" id="sector" class="form-select">
                                    @foreach ($sectores as $item)
                                        <option value="{{ $item->id }}">{{ $item->nomb_sec }}</option>
                                    @endforeach
                                    
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Forma de Pago: </label>
                                        <select class="form-select" id="forma_pago" name="forma_pago">
                                            
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 efectivo">
                                    <input type="hidden" name="total_venta" id="montoVenta" value="0">
                                    <div class="mb-3">
                                        <label class="form-label">Total Recibido: </label>
                                        <input class="form-control" type="number" name="total_recibido" id="total_recibido" value="0">
                                    </div>
                                </div>
                                <div class="col-md-4 efectivo">
                                    <div class="mb-3">
                                        <label class="form-label">Vuelto: </label>
                                        <input class="form-control" type="number" name="vuelto" id="vuelto" value="0" readonly>
                                    </div>
                                </div>

                                <div class="col-md-4 d-none pago-aplicativo">
                                    <div class="mb-3">
                                        <label class="form-label">N° de Operación: </label>
                                        <input class="form-control" type="number" name="numero_operacion" id="numero_operacion">
                                    </div>
                                </div>
                                <div class="col-md-4 d-none pago-aplicativo bancos">
                                    <div class="mb-3">
                                        <label class="form-label">Banco: </label>
                                        <select class="form-select" name="banco_venta" id="banco_venta">
                                            
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2 d-none pago-particionado">
                                    <button type="button" class="btn btn-primary mt-4" id="addParticionado"><i class="bx bx-plus"></i></button>
                                </div>
                                <div class="col-md-5 d-none pago-particionado">
                                    <label class="form-label">Forma de Pago: </label>
                                    <select class="form-select" id="forma_pago_particionado">
                                        
                                    </select>
                                </div>

                            </div>

                            <div id="listPagoParticionado">
                                
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="observacion" class="form-label">Observación</label>
                                        <textarea class="form-control" id="observacion" name="observacion" rows="3" placeholder="Escriba aqui una descripción"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Fecha Venta: </label>
                                        <input type="date" class="form-control" name="fecha_venta" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Vendedor: </label>
                                        <select class="form-select" name="vendedor" id="vendedor">
                                            @foreach ($vendedores as $item)
                                                @if ($item->id == $userId)
                                                    <option value="{{ $item->id }}" selected="true">{{ $item->nombre }}</option>
                                                @else
                                                    <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                                @endif
                                                
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <table class="table">
                                <tr>
                                    <td>Resumen:</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Sub Total Ventas:</td>
                                    <td class="text-end">S/. <span id="subTotalVenta">0.00</span></td>
                                </tr>
                                <tr>
                                    <td>Exonerada:</td>
                                    <td class="text-end">S/. <span id="exonerada">0.00</span></td>
                                </tr>
                                <tr>
                                    <td>Igv: (18%)</td>
                                    <td class="text-end">S/. <span id="igvVenta">0.00</span></td>
                                </tr>
                                <tr>
                                    <td>Imp. ICBPER:</td>
                                    <td class="text-end">S/. <span id="icbper">0.00</span></td>
                                </tr>
                                <tr>
                                    <td>TOTAL:</td>
                                    <td class="text-end">S/. <span id="totalVenta">0.00</span></td>
                                </tr>
                            </table>
                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light" id="btnGuardarVenta">Guardar</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
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
<script src="{{ asset('assets/js/pages/bootstrap-input-spinner.js') }}"></script>
<script src="{{ asset('js/pluggins/glide.js') }}"></script>
<script src="{{ asset('js/pluggins/jquery.bootstrap-touchspin.js') }}"></script>
<script src="{{ asset('js/pos.js') }}?t={{ time() }}"></script>
@endsection