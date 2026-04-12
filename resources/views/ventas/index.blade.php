@extends('layouts.main')

@section('title')
    Ventas
@endsection

@section('css')
    <!-- Sweet Alert-->
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('contenido')

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <h4>Lista de ventas</h4>
            </li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <a href="/pos" class="btn btn-primary mb-3" id="btnadd">Nueva venta</a>

                    <div class="table-responsive">
                        <table id="dataTableExample" class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>FECHA</th>
                                    <th>COMPROBANTE</th>
                                    <th>CLIENTE</th>
                                    <th>MONTO</th>
                                    <th>ACEPTADO SUNAT</th>
                                    <th>ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody id="contentAlmacen">
                                @foreach ($ventas as $key => $venta)
                                    <tr>
                                        <td class="text-left align-middle">{{ ++$i }}</td>
                                        <td class="text-left align-middle">{{ $venta->fecha }} / {{ $venta->hora }}</td>

                                        @if ($venta->tipo_comprobante_id == 1)
                                        <td class="text-left align-middle">
                                            BOLETA DE VENTA: {{ $venta->serie_comprobante }}-{{ $venta->numero_comprobante }}
                                            @if ($venta->estado_nota == 2)
                                            <span class="d-block font-size-14"><i class="bx bx-down-arrow-circle text-info"></i> Modificado por Nota Crédito: {{ $venta->serie_nota_credito }}-{{ $venta->numero_nota_credito }}</span>
                                            @endif
                                        </td>
                                        @elseif($venta->tipo_comprobante_id == 2)
                                        <td class="text-left align-middle">
                                            FACTURA: {{ $venta->serie_comprobante }}-{{ $venta->numero_comprobante }}
                                            @if ($venta->estado_nota == 2)
                                            <span class="d-block font-size-14"><i class="bx bx-down-arrow-circle text-info"></i> Modificado por Nota Crédito: {{ $venta->serie_nota_credito }}-{{ $venta->numero_nota_credito }}</span>
                                            @endif
                                            
                                        </td>
                                        @elseif($venta->tipo_comprobante_id == 3)
                                        <td class="text-left align-middle">
                                            NOTA DE CREDITO: {{ $venta->serie_comprobante }}-{{ $venta->numero_comprobante }}
                                            <span class="d-block font-size-14"><i class="bx bx-down-arrow-circle text-info"></i> Modifica: {{ $venta->serie_nota_credito }}-{{ $venta->numero_nota_credito }}</span>
                                        </td>
                                        @elseif($venta->tipo_comprobante_id == 4)
                                        <td class="text-left align-middle">NOTA DE DEBITO: {{ $venta->serie_comprobante }}-{{ $venta->numero_comprobante }}</td>
                                        @elseif($venta->tipo_comprobante_id == 5)
                                        <td class="text-left align-middle">NOTA DE VENTA: {{ $venta->serie_comprobante }}-{{ $venta->numero_comprobante }}</td>
                                        @else
                                        <td class="text-left align-middle">COTIZACION{{ $venta->serie_comprobante }}-{{ $venta->numero_comprobante }}</td>
                                        @endif
    
                                        <td class="text-left align-middle">
                                            <p class="mb-1 mt-1">{{ $venta->documento }}</p>
                                            <p class="mb-1">{{ $venta->nomb_per }} {{ $venta->pate_per }} {{ $venta->mate_per }}</p>
                                        </td>
                                        <td class="text-left align-middle">{{ $venta->monto }}</td>
                                        <td class="text-left align-middle">
                                            @if ($venta->aceptado_sunat == 1)
                                            <div class="badge badge-soft-success font-size-12">SI</div>
                                            @else
                                            <div class="badge badge-soft-danger font-size-12">NO</div>
                                            @endif

                                            @if($venta->tipo_comprobante_id == 5)
                                                @if ($venta->estado_nota == 2)
                                                <div class="badge badge-soft-danger font-size-12">ELIMINADO</div>
                                                @endif
                                            @endif
                                            
                                        </td>
                                        <td class="text-left align-middle">
                                            <div class="dropdown">
                                                <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bx bx-list-ul"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="./venta/ticket/{{ $venta->id }}" target="_blank">Imprimir Comprobante</a></li>
                                                    <li><a class="dropdown-item" href="#">Detalle</a></li>
                                                    
                                                    
                                                    <li><a class="dropdown-item" href="#">Cambiar comprobante</a></li>
                                                    <!--<li><a class="dropdown-item" href="#">Cambiar tipo pago</a></li>-->
                                                    @if ($venta->tipo_comprobante_id == 1 || $venta->tipo_comprobante_id == 2)
                                                        @if ($venta->aceptado_sunat != 1)
                                                        <li><a class="dropdown-item" href="#" onclick="enviar_comprobante(event, {{ $venta->id }})">Enviar Sunat</a></li>
                                                        @endif

                                                        @if ($venta->aceptado_sunat == 1)
                                                        <li><a class="dropdown-item" href="#" onclick="generarNotaCredito(event, {{ $venta->id }})">Crear nota de crédito</a></li>
                                                        @endif
                                                    @else 
                                                        
                                                        @if ($venta->estado_nota == 1)
                                                        <li><a class="dropdown-item" href="#" onclick="generarNotaVenta(event, {{ $venta->id }})">Eliminar Nota de Venta</a></li>
                                                        @endif
                                                    
                                                    @endif
                                                    
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        {{ $ventas->links() }}

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade bs-example-modal-center" id="modalEnviar" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                
                <div class="modal-body">
                    <h5 class="text-center py-3" id="mensajeConfirmacion">Enviando comprobante...</h5>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <div id="modal_nota_credito" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Generar Nota de Crédito</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form_nota_credito">
                    <input type="hidden" name="idventa" id="idsale" value="0">

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Motivo</label>
                                    <select class="form-select" name="motivo">
                                        <option value="1">Anulación de la operación</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger waves-effect" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light" id="btnNota">Generar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_enviar_nota" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <h4 class="text-center">Enviando Nota de Crédito...</h4>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

    <!-- Sweet Alerts js -->
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('js/venta.js') }}?t={{ time() }}">
    </script>

@endsection
