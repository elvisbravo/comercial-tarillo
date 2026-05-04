@extends('layouts.main')

@section('title')
Clientes
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
                <h4 class="mb-sm-0 font-size-18">Listado Clientes</h4>

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
                    @if(App\Permisos::hasPermission('clientes', 2))
                    <a href="{{url('clientes/create')}}" class="btn btn-primary "> <i class="btn-icon-prepend" data-feather="plus"></i> Nuevo</a>
                    @endif
                </div>

                <div class="card-body">

                    <!-- Static Backdrop modal Button -->
                    <i data-feather="star"></i>

                    <div class="table-responsive">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="estado_filter">Filtrar por Estado:</label>
                                <select id="estado_filter" class="form-select">
                                    <option value="1">Activos</option>
                                    <option value="0">Inactivos</option>
                                </select>
                            </div>
                        </div>

                        <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Razon Social</th>
                                    <th>Tipo Documento</th>
                                    <th>N° Documento</th>
                                    <th>Dirección Fiscal</th>
                                    <th>Telefono</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="listadoclientes">
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

<!-- Sweet Alerts js -->

<!-- Required datatable js -->
<!-- Sweet Alerts js -->
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Required datatable js -->
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

<input type="hidden" id="permiso_editar" value="{{ App\Permisos::hasPermission('clientes', 3) }}">
<input type="hidden" id="permiso_eliminar" value="{{ App\Permisos::hasPermission('clientes', 4) }}">

<script src="{{ asset('js/clientes.js') }}">
</script>

@endsection