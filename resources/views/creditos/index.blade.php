@extends('layouts.main')

@section('title')
    Generación de Creditos
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



@if(App\Permisos::hasPermission('creditos', 1))
    <div class="container-fluid">

                          <div class="row">
                                <div class="col-12">
                                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                        <h4 class="mb-sm-0 font-size-18">Generar Credito</h4>

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

                                                        @if(App\Permisos::hasPermission('creditos', 2))
                                                        <button type="button" class="btn btn-primary" id="factura"><i class="btn-icon-prepend" data-feather="plus"></i> Nuevo</button>
                                                        <button type="button" class="btn btn-success" id="guardar"><i class="fas fa-save"></i> Guardar</button>
                                                        @endif
                                                        <button type="button" class="btn btn-warning" id="cuotas" disabled><i class=" fas fa-cloud-download-alt"></i> Cuotas</button>
                                                        <button type="button" class="btn btn-info" id="contrato" disabled> <i class=" fas fa-cloud-download-alt"></i> Contrato</button>
                                                       <!-- <button type="button" class="btn btn-danger" id="garante"> <i class=" fa fa-cloud-search"></i> Garante</button> -->




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

                                 <div class="col-lg-4 col-xs-12">

                                       <label for="">Documento <strong style="color:red">(*)</strong></label>
                                       <input type="number" id="documento_cliente" disabled class="form-control">

                                        <label for="">Cliente <strong style="color:red">(*)</strong></label>
                                        <input type="hidden" id="id_cliente">
                                        <input type="hidden" id="id_venta">
                                         <input type="text" id="razon_social" disabled class="form-control ">

                                 </div>
                                 <div class="col-lg-4 col-xs-12">
                                     <label for="">N° Comprobante <strong style="color:red">(*)</strong></label>
                                     <input type="text"  disabled class="form-control " id="comprobante">

                                    <label for="">Monto Credito <strong style="color:red">(*)</strong></label>
                                    <input type="number"  id="monto" class="form-control obligatorio">


                                 </div>
                                 <div class="col-lg-4 col-xs-12">

                                 <label for="">Fecha Factura</label>

                                     <input type="date" disabled id="fecha_credito" class="form-control">

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


                                 <div class="col-lg-4 col-xs-12">
                                    
                                    <label for="">Tipo de vencimiento <strong style="color:red">(*)</strong></label>
                                      <select name="" id="id_periodo" class="form-control obligatorio">
                                           <option value="">--Seleccionar--</option>
                                           <option value="1">Diario</option>
                                           <option value="2">Semanal</option>
                                           <option value="3">Quincenal</option>
                                           <option value="4">Mensual</option>
                                      </select>

                                </div>
                                 <div class="col-lg-4 col-xs-12">
                                    <label for="">Tasa Interes</label>
                                    <input type="number" id="interes" value="0" class="form-control">

                                </div>
                                <div class="col-lg-4 col-xs-12">
                                    <label for="">Cuota Inicial <strong style="color:red">(*)</strong></label>
                                    <input type="text" class="form-control obligatorio" id="cuotainical" value="0">


                                </div>




                                <div class="col-lg-4 col-xs-12">

                                <label for="" style="color:#D35400">Desea desbloquear para agregar el número de cuotas?</label><br>
                                 <input type="checkbox" id="switch1" switch="none"  />
                                 <label for="switch1" data-on-label="On" data-off-label="Off"></label><br>


                                </div>
                               <!-- <div class="col-lg-4 col-xs-12">
                                    <label for="">Regla de calculo de cuotas</label>
                                      <select name="" id="form_calculo" class="form-control">
                                          <option value="">--Seleccionar--</option>
                                          <option value="1">Calculo directo</option>
                                      </select>

                                </div> -->
                                <div class="col-lg-4 col-xs-12">

                                    <label for="">Número de cuotas <strong>N° Máximo: <strong id="cuotainformativa"></strong> </strong></label>
                                    <input type="number" disabled id="num_cuota" class="form-control">
                                    <input type="hidden" id="meses_temporal">
                                    <input type="hidden" id="temporal_total_cuota">


                                </div>
                                <div class="col-lg-4 col-xs-12">

                                     <label for="">Observación</label>
                                    <textarea name="" id="observacion" class="form-control" cols="5" rows="5"></textarea>
                                </div>

                                <div class="col-lg-12 col-xs-12">

                                     <br>
                                    <button type="button" class="btn btn-warning" id="calcuar_cuota">Generar Cuotas</button>

                                </div>

                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-xs-12">
                                  <i data-feather="star"></i>
                                     <div class="table-responsive">

                                                <table id="datatable" class="table  dt-responsive  nowrap w-100">
                                                        <thead>
                                                        <tr>
                                                            <th># Cuota</th>
                                                            <th>Fecha Vencimiento</th>
                                                            <th>Interes</th>
                                                            <th>Monto</th>



                                                        </tr>
                                                        </thead>


                                                        <tbody id="calcuotalist">

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




  <!-- Static Backdrop Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                        <h5 class="modal-title" id="staticBackdropLabel">Datos de la Factura</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                            <div class="modal-body">

                                <input type="hidden" name="name" id="valor" value="0" />
                                <div class="form-group">

                                <div class="table-responsive">
                                                    <table id="datatable" class="table dt-responsive  nowrap w-100">
                                                        <thead>
                                                        <tr>
                                                            <th>Documento</th>
                                                            <th>Cliente</th>
                                                            <th>N° Comprobante</th>
                                                            <th>Fecha Venta</th>
                                                            <th>Monto</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                        </thead>


                                                        <tbody id="listaventas">

                                                        </tbody>
                                                    </table>

                                                    </div>


                                </div>




                            </div>

                            <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>

                    </div>
    </div>

        <div class="modal fade" id="staticBackdropdos" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                 <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content" >
                             <div class="modal-header">
                                    <h5 class="modal-title" id="staticBackdropLabel">Guardando el Credito</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                             </div>
                        <div class="modal-body">

                        <div class="row">


                                                      <div class="col-lg-2 col-xs-12"></div>
                                                      <div class="col-lg-8 col-xs-12">

                                                      <img src="{{asset('img/loader-meta.gif')}}" style=""  alt="" class="" width="100%" >
                                                            <h4 style="text-align: center;color:#BA4A00">Espere mintras se guarda el credito...</h4>
                                                            <p style="text-align: center;color:#BA4A00">Gracias<i class=" fas fa-coffee"></i></p>

                                                      </div>
                                                      <div class="col-lg-2 col-xs-12"></div>

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
@endif








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

    <script src="{{ asset('js/generar-creditos.js') }}">
    </script>

@endsection
