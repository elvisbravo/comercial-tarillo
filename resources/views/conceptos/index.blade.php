@extends('layouts.main')

@section('title')
Conceptos
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

<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <h4>Lista de Conceptos</h4>
        </li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">

                @if(App\Permisos::hasPermission('conceptos', 2))
                <button type="button" class="btn btn-primary mb-4" id="btnadd">Nuevo Concepto</button>
                @endif

                <div class="table-responsive">
                    <table id="dataTableExample" class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>CONCEPTO</th>
                                <th>TIPO MOVIMIENTO</th>
                                <th>ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody id="renderConceptos">

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_concepto" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Agregar Concepto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="form_concepto">
                <input type="hidden" name="idconcepto" id="idconcepto" value="0">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">Tipo Movimiento</label>
                        <select class="form-select mb-3" id="tipo_movimiento" name="tipo_movimiento">
                            <option value="">Seleccione el Tipo de Movimiento</option>
                            <option value="INGRESO">INGRESO</option>
                            <option value="EGRESO">EGRESO</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">Concepto</label>
                        <input type="text" class="form-control" id="descripcion" name="descripcion">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
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
    const canEdit = {{ App\Permisos::hasPermission('conceptos', 3) ? 'true' : 'false' }};
    const canDelete = {{ App\Permisos::hasPermission('conceptos', 4) ? 'true' : 'false' }};
</script>

<script src="{{ asset('js/conceptos.js') }}"></script>

@endsection