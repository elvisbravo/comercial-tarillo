@extends('layouts.main')

@section('title')
Listado Compras
@endsection

@section('css')
<!-- Sweet Alert-->
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

<!-- DataTables -->
<link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

<style>
    div.dataTables_wrapper div.dataTables_paginate {
        display: flex !important;
        justify-content: flex-end !important;
    }

    .pagination {
        justify-content: flex-end !important;
    }
</style>
@endsection
@section('contenido')

<div class="container-fluid">
    <input type="hidden" id="can_delete" value="{{ App\Permisos::hasPermission('compras', 4) ? '1' : '0' }}">
    <input type="hidden" id="can_view_detail" value="{{ App\Permisos::hasPermission('compras', 6) ? '1' : '0' }}">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Listado Compras</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">

                    </ol>
                </div>

            </div>
        </div>
    </div>


    <!-- end page title -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    @if(App\Permisos::hasPermission('compras', 2))
                    <a href="{{url('compras/create')}}" type="button" class="btn btn-primary "> <i class="btn-icon-prepend" data-feather="plus"></i>Nuevo </a>
                    @endif
                </div>

                <div class="card-body">
                    <!-- Static Backdrop modal Button -->
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                    @endif

                    <!-- Static Backdrop modal Button -->


                    <div class="table-responsive">

                        <table id="datatable" class="table  dt-responsive  nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>PROVEEDOR</th>
                                    <th>COMPROBANTES</th>
                                    <th>MONTO</th>
                                    <th>TIPO</th>
                                    <th>FECHA</th>
                                    <th>ESTADO</th>
                                    <th>ACCIONES</th>

                                </tr>
                            </thead>


                            <tbody id="listadocompras">

                            </tbody>
                        </table>

                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

<!-- Static Backdrop Modal -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Detalle de la Compra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="loader_modal" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <h5 class="mt-2">Cargando datos...</h5>
                </div>

                <div id="content_modal" style="display: none;">
                    <div class="table-responsive">
                    <table id="datatabledos" class="table  dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>PRODUCTOS</th>
                                <th>CANTIDAD</th>
                                <th>UNIDADES</th>
                                <th>PRECIO</th>
                                <th>SUBTOTAL</th>


                            </tr>
                        </thead>


                        <tbody id="detalle">

                        </tbody>
                    </table>
                </div>
            </div> <!-- Fin content_modal -->
        </div> <!-- Fin modal-body -->
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>


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

<script src="{{ asset('js/compraslista.js') }}">
</script>

@endsection