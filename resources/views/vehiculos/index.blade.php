@extends('layouts.main')

@section('title')
    Vehiculos
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
                                            <h4 class="mb-sm-0 font-size-18">Listado Vehiculos</h4>

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

                                            <button type="button" class="btn btn-primary"  onclick="abrimodal(0)" data-bs-toggle="modal" data-bs-target="#staticBackdrop"> <i class="btn-icon-prepend" data-feather="plus"></i></button>

                                            </div>

                                            <div class="card-body">

                                            <!-- Static Backdrop modal Button -->
                                            <i data-feather="star"></i>

                                            <div class="table-responsive">
                                                <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                                    <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Placa</th>
                                                        <th>Tipo de Vehículo</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                    </thead>


                                                    <tbody id="listadovehiculos">

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
                             <input type="hidden" name="name" id="valor" value="0" />

                             <div class="form-group">
                                <label for="" >Placa <strong style="color:red">*</strong></label>
                                <input type="text" class="form-control obligatorio limpiar" placeholder="Placa" id="placa" class="obligatorio">

                            </div>
                        
                            <div class="form-group">
                                <label for="">Tipo de Vehículo <strong style="color:red">*</strong></label>
                                <select name="" id="tipo_vehiculo_id" class="form-control" class="obligatorio">

                                </select>

                            </div>
                            <div class="form-group">
                                <label for="" >Numero Soat </label>
                                <input type="text" class="form-control obligatorio limpiar" placeholder="num_soat" id="num_soat">

                            </div>
                            <div class="form-group">
                                <label for="" >Color </label>
                                <input type="text" class="form-control obligatorio limpiar" placeholder="color" id="color">

                            </div>
                            <div class="form-group">
                                <label for="" >Marca </label>
                                <input type="text" class="form-control obligatorio limpiar" placeholder="marca" id="marca">

                            </div>
                            <div class="form-group">
                                <label for="" >Marca </label>
                                <input type="text" class="form-control obligatorio limpiar" placeholder="modelo" id="modelo">

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

    <!-- Required datatable js -->
    <!-- Sweet Alerts js -->
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('js/vehiculos.js') }}">
    </script>

@endsection