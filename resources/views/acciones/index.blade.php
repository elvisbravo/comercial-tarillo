@extends('layouts.main')

@section('title')
Acciones
@endsection

@section('css')
<link href="{{asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />

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
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Listado de Acciones</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    @if(App\Permisos::hasPermission('acciones', 2))
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="abrimodal(0)" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                        <i class="fas fa-plus"></i> Crear Acción
                    </button>
                    @endif
                </div>
                <div class="card-body">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th width="10%">#</th>
                                <th width="70%">Nombre</th>
                                <th width="20%">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="listado">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Formulario de Acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="id" value="0">
                <div class="form-group mb-3">
                    <label>Nombre <strong class="text-danger">(*)</strong></label>
                    <input type="text" class="form-control obligatorio limpiar" id="nombre" placeholder="Nombre de la acción">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardar">Guardar</button>
                <button type="button" class="btn btn-primary" id="actualizar" style="display: none;">Actualizar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<script>
    const canEdit = {{ App\Permisos::hasPermission('acciones', 3) ? 'true' : 'false' }};
    const canDelete = {{ App\Permisos::hasPermission('acciones', 4) ? 'true' : 'false' }};
</script>

<script src="{{ asset('js/acciones.js') }}"></script>
@endsection
