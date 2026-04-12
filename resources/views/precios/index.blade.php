@extends('layouts.main')

@section('title')
    Lista de Precios
@endsection

@section('css')
   <!-- Sweet Alert-->
   <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
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






                <div class="container-fluid">

                <!-- start page title -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                            <h4 class="mb-sm-0 font-size-18">Listado de Precios</h4>

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

                                            <!--<button type="button" class="btn btn-primary waves-effect btn-label waves-light"  onclick="abrimodal(0)" data-bs-toggle="modal" data-bs-target="#staticBackdrop"> <i class="btn-icon-prepend" data-feather="plus"></i> Crear Precios</button>-->
                                            <a href="{{url('precios/create')}}" type="button" class="btn btn-primary"  > <i class="btn-icon-prepend" data-feather="plus"></i>Nuevo</a>

                                            </div>

                                            <div class="card-body">

                                            <!-- Static Backdrop modal Button -->
                                            <i data-feather="star"></i>

                                            <div class="table-responsive">
                                                <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                                    <thead>
                                                    <tr>

                                                        <th>Lista Precios</th>
                                                        <th>Codigo Producto</th>
                                                        <th>Producto</th>
                                                        <th>Precio al contado</th>
                                                        <th>Precio al credito</th>
                                                        <th>Estado</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                    </thead>


                                                    <tbody id="listaprcios">

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

    <script src="{{ asset('js/precios.js') }}">
    </script>

@endsection
