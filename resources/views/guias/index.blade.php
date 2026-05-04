@extends('layouts.main')

@section('title')
Guias
@endsection

@section('css')
<!-- Sweet Alert-->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css" />
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
            <h4>Listado de Guias</h4>
        </li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                @if(App\Permisos::hasPermission('guias', 2))
                <a href="{{ route('guias.create') }}" class="btn btn-primary mb-3">Recepción Guia</a>
                @endif

                <div class="table-responsive">
                    <table id="dataTableExample" class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>FECHA</th>
                                <th>COMPROBANTE</th>
                                <th>CLIENTE</th>
                                <th>TIPO TRASLADO</th>
                                <th>CONFORME</th>
                                <!-- <th>ACEPTADO SUNAT</th> -->
                                <th>ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody id="traslados">




                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->


@endsection

@section('js')

<!-- Sweet Alerts js -->
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Required datatable js -->
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>

<script>
    const canDelete = {{ App\Permisos::hasPermission('guias', 4) ? 'true' : 'false' }};
</script>

<script src="{{ asset('js/guia_remision.js') }}">
</script>

@endsection