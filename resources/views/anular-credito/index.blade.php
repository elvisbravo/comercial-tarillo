@extends('layouts.main')

@section('title')
Anular Credito
@endsection

@section('css')
<!-- Sweet Alert-->
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

<!-- DataTables -->
<link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

<!-- Select2 -->
<link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />

<style>
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

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Anulación Credito</h4>

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
                                <div class="col-lg-6 col-xs-12">
                                    <label for="cliente_select">Buscar Cliente</label>
                                    <select id="cliente_select" class="form-select" style="width:100%"></select>
                                    <input type="hidden" id="id_persona_tempe">

                                </div>
                                <div class="col-lg-6 col-xs-12">
                                    <label for="documento">Documento de Identidad</label>
                                    <input type="text" disabled class="form-control" id="documento">

                                </div>


                                <div class="col-lg-12 col-xs-12 mt-3">
                                    <div class="table-responsive">

                                        <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>N° Credito</th>
                                                    <th>Documento</th>
                                                    <th>Cliente</th>
                                                    <th>Fecha Registro Credito</th>
                                                    <th>Número de Cuotas</th>
                                                    <th>Tipo Vencimiento</th>
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



<div class="modal fade bs-example-modal-xl-y" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myExtraLargeModalLabel">Detalle de Credito</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="codigo_credito">

                <label for="observacion">Ingrese el porque se esta anulado el Credito? <strong style="color:red">Campo obligatorio</strong></label>

                <textarea name="" id="observacion" cols="5" rows="5" class="form-control obligatorio">
                                                            </textarea>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardar">Guardar</button>


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

<script src="{{ asset('js/anular-creditos-activos.js') }}">
</script>

@endsection