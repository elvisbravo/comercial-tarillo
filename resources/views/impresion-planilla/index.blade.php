@extends('layouts.main')

@section('title')
  Impresión Masiva de Cuotas Vencidas
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
    <div class="container-fluid">
                      <div class="row">
                                <div class="col-12">
                                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                        <h4 class="mb-sm-0 font-size-18">Impresión Masiva de Cuotas Vencidas</h4>

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
                                                <div class="col-lg-4 col-xs-12">

                                                <label for="">Selecciona el sector <strong style="color:red">( Campo obligatorio )</strong></label>
                                                <select name="" id="sector_id" class="form-control">
                                                    <option value="">--Seleccionar--</option>
                                                    <option value="0">TODOS</option>
                                                    @foreach($sector as $sd)
                                                      <option value="{{$sd->id}}">{{$sd->nomb_sec}}</option>
                                                    @endforeach

                                                </select>

                                                </div>
                                                <div class="col-lg-4 col-xs-12">

                                                    <label for="">Fecha <strong style="color:red">( Campo obligatorio )</strong></label>
                                                    <input type="date" class="form-control" id="fecha">

                                                </div>
                                                 <div class="col-lg-4 col-xs-12">
                                                     <br>

                                                   <button class="btn btn-primary" id="buscar">Buscar</button>
                                                   @if(App\Permisos::hasPermission('impresion-planilla', 7))
                                                   <button class="btn btn-info" id="imprimir">Imprimir</button>
                                                   @endif

                                                </div>



                                                <div class="col-lg-12 col-xs-12">
                                                   <i data-feather="star"></i>
                                                    <div class="table-responsive">

                                                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                                        <thead>
                                                        <tr>
                                                            <th>Documento</th>
                                                            <th>Cliente</th>
                                                            <th>Sector</th>
                                                            <th>Monto Total Deuda Vencida</th>
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


    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.14.1/moment.min.js"></script>

    <script src="{{ asset('js/impresion-masiva.js')}}">
    </script>

@endsection
