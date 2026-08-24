@extends('layouts.main')

@section('title')
Zonas
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

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Listado Zonas</h4>

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
                    @if(App\Permisos::hasPermission('zonas', 2))
                    <button type="button" class="btn btn-primary" onclick="abrimodal(0)" data-bs-toggle="modal" data-bs-target="#staticBackdrop"> <i class="btn-icon-prepend" data-feather="plus"></i> crear Nueva Zona</button>
                    @endif
                </div>

                <div class="card-body">

                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Zona</th>
                                    <th>Sede</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody id="listadezonas">

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
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Formulario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="valor" value="0" />
                <div class="form-group">
                    <label for="">Nombre de la Zona</label>
                    <input type="text" class="form-control obligatorio limpiar" placeholder="Nombre de la zona" id="nomb_zona">

                </div>
                <div class="form-group">
                    <label for="">Sede</label>
                    <select class="form-control obligatorio limpiar" id="sede_id">
                        <option value="">--Seleccionar--</option>
                        @foreach($sedes as $sede)
                        <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardar">Guardar</button>
                <button type="button" class="btn btn-primary" id="actualizar">Actualizar</button>

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

<script>
    const canEdit = {{ App\Permisos::hasPermission('zonas', 3) ? 'true' : 'false' }};
    const canDelete = {{ App\Permisos::hasPermission('zonas', 4) ? 'true' : 'false' }};
</script>

<script src="{{ asset('js/zona.js') }}"></script>

@endsection
