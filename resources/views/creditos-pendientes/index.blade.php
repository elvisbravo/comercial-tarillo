@extends('layouts.main')

@section('title')
Creditos Pendientes
@endsection

@section('css')
<!-- Sweet Alert-->
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

<!-- DataTables -->
<link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

<!-- Select2 -->
<link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />

<style>
    div.dataTables_wrapper div.dataTables_paginate {
        display: flex !important;
        justify-content: flex-end !important;
    }

    .pagination {
        justify-content: flex-end !important;
    }

    /* Corregir el alto del Select2 para que coincida con los inputs de Bootstrap */
    .select2-container .select2-selection--single {
        height: 38px !important;
        border: 1px solid #ced4da !important;
        border-radius: 0.25rem !important;
        padding: 5px 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
        right: 8px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 26px !important;
        padding-left: 0 !important;
        color: #495057 !important;
    }
</style>
@endsection
@section('contenido')


@if(App\Permisos::hasPermission('creditos-pendientes', 1))
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Consulta de Creditos</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">

                    </ol>
                </div>

            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="row">
                        <div class="col-lg-12 col-xs-12">
                            <div class="row">
                                <div class="col-lg-4 col-xs-12">
                                    <label for="">Buscar Cliente</label>
                                    <select id="cliente_select" class="form-select" style="width:100%"></select>
                                    <input type="hidden" id="id_persona_tempe">

                                </div>

                                <div class="col-lg-4 col-xs-12">
                                    <label for="">Estado</label>
                                    <select name="" id="estado_id" class="form-control">
                                        <option value="">--Seleccionar--</option>
                                        <option value="3" selected>TODOS</option>
                                        <option value="1">ACTIVOS</option>
                                        <option value="2">PAGADOS</option>
                                        <option value="0">ANULADOS</option>
                                    </select>

                                </div>
                                <div class="col-lg-2 col-xs-12">
                                    <label>&nbsp;</label>
                                    <button class="btn btn-primary d-block w-100" id="buscardata">Buscar</button>
                                </div>



                                <div class="col-lg-4 col-xs-12">
                                    <br>
                                    @if(App\Permisos::hasPermission('creditos-pendientes', 7))
                                    <button class="btn btn-primary" id="estado">Imprimir Estado de cuenta</button>
                                    @endif

                                </div>

                                <div class="col-lg-12 col-xs-12 mt-3">
                                    <div class="table-responsive">

                                        <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>N°</th>
                                                    <th>Documento</th>
                                                    <th>Cliente</th>
                                                    <th>Fecha Registro</th>
                                                    <th>Cuotas</th>
                                                    <th>Vencimiento</th>
                                                    <th>Monto Credito</th>

                                                    <th>Saldo Pendiente</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>


                                            <tbody id="lisatadocredtios">

                                            </tbody>
                                        </table>


                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>



</div>
@else
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="alert alert-danger text-center">No tienes permiso para ver este módulo.</div>
        </div>
    </div>
</div>
@endif



<div class="modal fade bs-example-modal-xl-y" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myExtraLargeModalLabel">Detalle de Credito</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-lg-6 col-xs-12">
                        <label for="">Documento</label>
                        <input type="text" class="form-control" id="documentos" disabled>
                    </div>
                    <div class="col-lg-6 col-xs-12">
                        <label for="">Cliente</label>
                        <input type="text" disabled class="form-control" id="cliented">
                        <input type="hidden" disabled class="form-control" id="id_credito">
                    </div>

                    <div class="col-lg-4 col-xs-12">
                        <label for="">Monto Credito</label>
                        <input type="text" class="form-control" disabled id="impo_cred">
                    </div>
                    <div class="col-lg-4 col-xs-12">
                        <label for="">Forma de Pago</label>
                        <input type="text" disabled class="form-control" id="periodo_pago">
                    </div>
                    <div class="col-lg-2 col-xs-12">
                        <label for="">N° de Cuotas</label>
                        <input type="text" disabled class="form-control" id="historial_cuotas">
                    </div>
                    <div class="col-lg-2 col-xs-12">
                        <label for="">Estado</label> <br><br>
                        <span id="historial_estado"></span>
                    </div>

                </div>
                <hr>

                <ul class="nav nav-tabs" id="detalleCreditoTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="cuotas-tab" data-bs-toggle="tab" data-bs-target="#cuotas-pane" type="button" role="tab" aria-controls="cuotas-pane" aria-selected="true">Detalle de las Cuotas</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pagos-tab" data-bs-toggle="tab" data-bs-target="#pagos-pane" type="button" role="tab" aria-controls="pagos-pane" aria-selected="false">Pagos Realizados</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="amortizaciones-tab" data-bs-toggle="tab" data-bs-target="#amortizaciones-pane" type="button" role="tab" aria-controls="amortizaciones-pane" aria-selected="false">Amortizaciones</button>
                    </li>
                </ul>
                <div class="tab-content" id="detalleCreditoTabContent">
                    <div class="tab-pane fade show active" id="cuotas-pane" role="tabpanel" aria-labelledby="cuotas-tab">

                        <div class="table-responsive mt-3">

                            <table id="datatableg" class="table table-bordered dt-responsive  nowrap w-100">
                                <thead>
                                    <tr>
                                        <th># Credito</th>
                                        <th># Cuota</th>
                                        <th>Cuota</th>
                                        <th>Interes </th>
                                        <th>Saldo Cuota</th>
                                        <th>Vencimiento</th>
                                        <th>Estado</th>
                                        <th>Condición</th>
                                    </tr>
                                </thead>


                                <tbody id="listaprediosxs">

                                </tbody>

                            </table>

                        </div>

                        @if(App\Permisos::hasPermission('creditos-pendientes', 7))
                        <button class="btn btn-primary" id="imprimir_contrato">Imprimir Contrato</button>
                        <button class="btn btn-info" id="imprimir_cuotas">Imprimir Cuotas</button>
                        @endif

                    </div>
                    <div class="tab-pane fade" id="pagos-pane" role="tabpanel" aria-labelledby="pagos-tab">

                        <div class="table-responsive mt-3">

                            <table id="datatablehistorial" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th># Cuota</th>
                                        <th>Monto Pagado</th>
                                        <th>Tipo</th>
                                        <th>Forma de Pago</th>
                                        <th>N° Recibo</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>

                                <tbody id="listahistorialpagos">

                                </tbody>

                            </table>

                        </div>

                    </div>
                    <div class="tab-pane fade" id="amortizaciones-pane" role="tabpanel" aria-labelledby="amortizaciones-tab">

                        <div class="table-responsive mt-3">

                            <table id="datatableamortizaciones" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Monto Amortizado</th>
                                        <th>Forma de Pago</th>
                                        <th>N° Recibo</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>

                                <tbody id="listaamortizaciones">

                                </tbody>

                            </table>

                        </div>

                    </div>
                </div>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


@endsection

@section('js')

<!-- Sweet Alerts js -->

<!-- Required datatable js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.3/moment.min.js"></script>
<!-- Sweet Alerts js -->
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Required datatable js -->
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>

<script>
    const canPrint = {{ App\Permisos::hasPermission('creditos-pendientes', 7) ? 'true' : 'false' }};
</script>

<script src="{{ asset('js/reportes-creditos-activos.js') }}">
</script>

@endsection