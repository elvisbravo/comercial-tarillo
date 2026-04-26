@extends('layouts.main')

@section('title')
Reporte de Inventario
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

<div class="loader" style="position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url('{{asset('img/loader-meta.gif')}}') 50% 50% no-repeat rgb(249,249,249);
        opacity: .8;">

    <div class="col-md-12" id="myDIV">
        <div class="panel panel-default">
            <div class="panel-heading"></div>
            <div class="panel-body loader-demo" style="margin-top:200px;">
                <h1 style="color: #186A3B;font-family: 'Jomhuria', cursive;text-align:center"></h1>
                <div class="ball-pulse">
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
        </div>
    </div>
</div>

<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <h4>Libro Electrónico de Inventario</h4>
        </li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <form class="row g-3" id="form_datetime">
                    <h6>Selecciona un rango de fechas</h6>
                    <p>Selecciona el rango de fechas de emisión de los comprobantes electrónico,
                        luego has click en Generar para poder visualizar el reporte.
                    </p>

                    <div class="col">
                        <label for=""> Seleccionar Ubicación Interna</label>
                        <input type="hidden" name="name" id="id_almacen" value="0" />
                        <select class="form-control w-25" id="id" class="obligatorio">
                            <option value=""><---Seleccionar---></option>
                            @foreach($ubicacion as $ubicaciones)
                            <option value="{{ $ubicaciones->id}}">{{ $ubicaciones->name}}</option>
                            @endforeach

                        </select>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button class="btn btn-info" type="button" onclick="generar_reporte()">GENERAR REPORTE</button>
                        @if(App\Permisos::hasPermission('reporteinventarios', 7))
                        <button class="btn btn-success" type="button" id="exportarexcel">EXPORTAR EN EXCEL</button>
                        @endif
                    </div>


                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6>FORMATO DEL LIBRO ELECTRÓNICO DE INVENTARIO</h6>

                <!-- Static Backdrop modal Button -->
                <i data-feather="star"></i>

                <div class="table-responsive">

                    <table id="datatabledos" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>CÓDIGO PRODUCTO</th>
                                <th>UBICACIÓN</th>
                                <th>UB.INTERNA</th>
                                <th>NOMBRE PRODUCTO</th>
                                <th>MARCA</th>
                                <th>CATEGORÍA</th>
                                <th>SUB_CATEGORÍA</th>
                                <th>UNIDAD DE MEDIDA</th>
                                <th>CANTIDAD INVENTARIADA</th>
                                <th>PROODUCTO COSTO</th>

                            </tr>
                        </thead>


                        <tbody id="listainventario">

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

<!-- Required datatable js -->
<!-- Sweet Alerts js -->
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Required datatable js -->
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

<script src="{{ asset('js/reporteinventarios.js') }}">
</script>

@endsection