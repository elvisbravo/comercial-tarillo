@extends('layouts.main')

@section('title')
    Reprogramación de Creditos
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
@if(App\Permisos::hasPermission('creditos-reprogramacion', 1))
    <div class="container-fluid">
                      <div class="row">
                                <div class="col-12">
                                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                        <h4 class="mb-sm-0 font-size-18">Reprogramación de Creditos</h4>

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
                                                <div class="col-lg-6 col-xs-12">

                                                     
                                                        @if(App\Permisos::hasPermission('creditos-reprogramacion', 2))
                                                        <button type="button" class="btn btn-success" id="guardar"><i class="fas fa-save"></i> Guardar</button>
                                                        @endif
                                                        <button type="button" class="btn btn-warning" id="cuotas" disabled><i class=" fas fa-cloud-download-alt"></i> Cuotas</button>
                                                        <button type="button" class="btn btn-info" id="contrato" disabled> <i class=" fas fa-cloud-download-alt"></i> Contrato</button>




                                                </div>
                                                <div class="col-lg-6 col-xs-12">
                                                    <label for="" style="color:#D35400;text-align: center;font-size: 20px;"> <strong>Información Historica - Deuda acomulada</strong></label>
                                                    <h5 style="text-align: center;font-size: 28px;color: #0E6655;"> <strong id="deudaacomulada">0.00</strong></h5>

                                                </div>

                                    </div>

                                    <p style="color:red">Todos los campos marcados con (*) son obligatorios</p>


                                </div>

                            </div>
                             <br>

                                    <div class="row">
                                       <div class="col-lg-12 col-xs-12">
                                          <div class="row">

                                                <div class="col-lg-6 col-xs-12">
                                                    <label for="">Buscar Cliente</label>
                                                    <form class="app-search d-lg-block">

                                                    <div class="position-relative">
                                                        <input type="text" class="form-control" placeholder="Search..." disabled id="nombresdata">
                                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target=".bs-example-modal-xl" type="button"><i class="bx bx-search-alt align-middle"></i></button>
                                                    </div>

                                                    </form>
                                                    <input type="hidden" id="id_persona_tempe">
                                                    <input type="hidden" id="id_credito">
                                                    <input type="hidden" id="periodo">

                                                </div>
                                                <div class="col-lg-6 col-xs-12">
                                                    <label for="">Documento de Identidad</label> <br><br>
                                                    <input type="text" disabled class="form-control" id="documento">

                                                </div>

                                                <div class="col-lg-4 col-xs-12">

                                                  <label for="">Saldo Credito</label>
                                                  <input type="text" class="form-control" id="saldo_credito" disabled>

                                                </div>
                                                <div class="col-lg-4 col-xs-12">

                                               
                                                   <label for="">Vencimiento 1era cuota <strong style="color:red">(*)</strong></label>
                                                  <input type="date" id="fecha_inicio" class="form-control obligatorio">


                                                </div>
                                                <div class="col-lg-4 col-xs-12">
                                                    <label for="">Concepto de Credito <strong style="color:red">(*)</strong></label>
                                                    <select name="" id="conceto_id" class="form-control">
                                                        <option value="">--Seleccionar Concepto---</option>
                                                        @foreach($conceptos as $c)
                                                        <option value="{{$c->id}}" selected>{{$c->name}}</option>
                                                        @endforeach

                                                    </select>

                                                </div>

                                               <!-- <div class="col-lg-4 col-xs-12">

                                                <label for="">Tipo de vencimiento <strong style="color:red">(*)</strong></label>
                                                    <select name="" id="id_periodo" class="form-control obligatorio">
                                                        <option value="">--Seleccionar--</option>
                                                        <option value="1">Diario</option>
                                                        <option value="2">Semanal</option>
                                                        <option value="3">Quincenal</option>
                                                        <option value="4">Mensual</option>
                                                    </select>

                                                </div>-->

                                                <div class="col-lg-4 col-xs-12">

                                                    <label for="">Número de cuotas </label>
                                                    <input type="number" id="num_cuota" value="0" class="form-control">

                                                </div>
                                                <div class="col-lg-4 col-xs-12">
                                                       <label for="">Tasa Interes</label>
                                                       <input type="number" id="interes" value="0" class="form-control">
                                                 </div>

                                                 <div class="col-lg-4 col-xs-12">

                                                    <label for="">Observación</label>
                                                    <textarea name="" id="observacion" class="form-control" cols="5" rows="5"></textarea>
                                                </div>

                                                <div class="col-lg-12 col-xs-12">

                                                    <br>
                                                    <button type="button" class="btn btn-warning" id="calcuar_cuota">Generar Cuotas</button>

                                                </div>

                                                <div class="col-lg-12 col-xs-12">
                                                    <br>

                                                <h6>Cuotas Pendiente Reprogramdas</h6>
                                              

                                                <div id="spinner" style="display:none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align:center;">
                                                        <i class="fa fa-spinner fa-spin" style=" font-size: 28"></i>
                                                  </div>

                                                <div class="table-responsive">

                                                <table id="" class="table">
                                                                        <thead>
                                                                                <tr>
                                                                                    <th># Cuota</th>
                                                                                  
                                                                                    <th>Fecha Vencimiento</th>
                                                                                    <th>Interes</th>
                                                                                    <th>Monto</th>
                                                                                    <th>Estado</th>
                                                                                </tr>
                                                                        </thead>


                                                                            <tbody id="cuotas_activas">

                                                                            </tbody>
                                                                </table>

                                                         
                                                </div>

                                                </div>



                                                <div class="col-lg-12 col-xs-12">
                                                   <i data-feather="star"></i>
                                                        <div class="table-responsive">

                                                                <table id="" class="table">
                                                                        <thead>
                                                                                <tr>
                                                                                    <th># Cuota</th>
                                                                                    <th>Fecha Vencimiento</th>
                                                                                    <th>Periodo</th>
                                                                                    <th>Interes</th>
                                                                                    <th>Monto</th>
                                                                                </tr>
                                                                        </thead>


                                                                            <tbody id="reprogramaconcuotas">

                                                                            </tbody>
                                                                </table>


                                                        </div>



                                                </div>

                                                <div class="col-lg-4 col-xs-12">
                                                    <label for="">Total Capital</label>
                                                    <input type="number" id="capital" disabled class="form-control">
                                                 </div>

                                                 <div class="col-lg-4 col-xs-12">
                                                        <label for="">Total Interes</label>
                                                        <input type="text" id="interesporcentaje" disabled class="form-control">
                                                   </div>

                                                   <div class="col-lg-4 col-xs-12">
                                                    <label for="">Total A Pagar</label>
                                                    <input type="text" id="total_pagar" disabled class="form-control">
                                                  </div>
                                                


                                           </div>
                                        </div>
                                    </div>


                                    </div>
                                </div>
                               </div>
                            </div>



    </div>
@else
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger text-center">No tienes permiso para ver este módulo.</div>
            </div>
        </div>
    </div>
@endif/div>

    <!--  Extra Large modal example -->
<div class="modal fade bs-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="myExtraLargeModalLabel">Litado de Clientes</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                         <div class="row">
                                                             <div class="col-lg-12">

                                                             <div class="table-responsive">

                                                                            <table id="datatables" class="table table-bordered dt-responsive">
                                                                                    <thead>
                                                                                    <tr>

                                                                                        <th>Dni</th>
                                                                                        <th>Cliente</th>
                                                                                        <th>Dirección</th>
                                                                                        <th   width="20" >Acciones</th>
                                                                                    </tr>
                                                                                    </thead >


                                                                                    <tbody id="listaclientes">


                                                                                    </tbody>
                                                                            </table>


                                                                </div>



                                                             </div>

                                                         </div>





                                                        </div>
                                                    </div><!-- /.modal-content -->
                                                </div><!-- /.modal-dialog -->
                                            </div><!-- /.modal -->


        <!-- agregar los creditos activos que tiene el cliente   -->

        <div class="modal fade bs-example-modal-xl-y" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
                                                <div class="modal-dialog  modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="myExtraLargeModalLabel">Detalle de Credito</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">


                                                 

                                                           <hr>
                                                           <h6>Seleccionar Credito que desea Reprogramación</h6>


                                                        <div class="table-responsive">

                                                                            <table id="datatableg" class="table table-bordered dt-responsive  nowrap w-100">
                                                                                    <thead>
                                                                                    <tr>
                                                                                        <th>N° Credito</th>
                                                                                        <th>Documento</th>
                                                                                        <th>Cliente</th>
                                                                                        <th>Fecha de Registro Credito</th>
                                                                                        <th>Numero de Cuotas</th>
                                                                                        <th>Tipo Vencimiento</th>
                                                                                        <th>Monto Credito Inicial</th>
                                                                                        <th>Saldo Pendiente</th>
                                                                                        <th>Estado</th>
                                                                                        <th>Aacciones</th>
                                                                                    </tr>
                                                                                    </thead>


                                                                                    <tbody id="listacreditos_pendientes">

                                                                                    </tbody>

                                                                            </table>

                                                         </div>

                                                    







                                                        </div>
                                                    </div><!-- /.modal-content -->
                                                </div><!-- /.modal-dialog -->
                                            </div><!-- /.modal -->


























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

    <script src="{{ asset('js/generar-reprogramacion-credito.js') }}">
    </script>

@endsection
