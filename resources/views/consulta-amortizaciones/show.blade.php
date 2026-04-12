@extends('layouts.main')

@section('title')
    Detalle Amortizaciones
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
                      <div class="row">
                                <div class="col-12">
                                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                        <h4 class="mb-sm-0 font-size-18">Detalle de Amortizaciones</h4>

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


              <div class="col-md-12 col-sm-12  ">
                 <div class="x_panel">
                            <div class="x_title" style="background:#0B5345;">

                                    <ul class="nav navbar-right panel_toolbox">



                                    </li>
                                    </ul>
                                    <div class="clearfix"></div>
                            </div>



                            <div class="x_content">
                                <div class="table-responsive">

                                    <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                                            <thead>
                                                <tr>
                                                <th>N° Credito</th>
                                                <th>Credito</th>
                                                <th>Importe Cuota</th>
                                                <th>Amortización</th>
                                                <th>Capital</th>
                                                <th>Interes</th>
                                                <th>Saldo</th>
                                                <th>Opciones</th>


                                                </tr>
                                            </thead>

                                            <tbody>
                                            @foreach($creditos as $a)

                                                <tr onclick="creditos('{{$a->id}}');">
                                                <a href="">
                                                        <th>{{$a->id}}</th>
                                                        <th>Venta de Articulos</th>
                                                        <th>{{$a->mont_cuo}}</th>
                                                        <th>{{$a->tipo_amo}}</th>
                                                        <th>{{$a->capi_cuo}}</th>
                                                        <th>0.00</th>
                                                        <th>{{$a->sald_cap}}</th>
                                                        <th>
                                                            <a href="#">ver</a>
                                                        </th>
                                                    </a>
                                                </tr>


                                            @endforeach

                                            </tbody>
                                        </table>

                                        <input type="hidden" id="montorestante">

                            </div>
                        </div>


              </div>




                                       </div>

                                    </div>
                                </div>
                               </div>
                        </div>
    </div>



    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">

                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel" style="text-align:center">Información del Credito</h4>

                        </div>
                        <div class="modal-body">
                            <div class="row">
                            <input type="hidden" disabled class="form-control" id="id_credito" >

                                <div class="col-md-12 col-xs-12">
                                    <h6 style="text-align:center">INFORMACIÓN DEL CREDITO</h6>
                                    <h4 style="color:#D35400;text-align:center">INFORMACIÓN DEL CREDITO</h4>
                                    <p  style="text-align:center">Credito N°:  <strong id="id_cre"> </strong>     Concepto: <strong id="concepto">POR LA COMPRA DE MERCADERIA</strong></p>
                                    <p  style="text-align:center">Capital N°:  <strong id="monto_cre">  </strong>     Fecha: <strong id="fecha"></strong>  Doc. Ref: <strong id="documento"></strong></p>
                                    <p  style="text-align:center">N° Cuota:  <strong id="numcuotas">  </strong>     Observacion: <strong id="observaciones"> </strong></p>
                                    <p  style="text-align:center">Importe a Pagar:  <strong id="importe">  </strong>  </p>

                                    <h4 style="color:#F1C40F;text-align:center ">Datos del Cliente</h4>
                                    <p  style="text-align:center">Nombres:  <Strong id="cliente"></Strong></p>
                                    <p  style="text-align:center">Sector: <strong></strong>  Documento: <strong id="ruc"></strong></p>
                                </div>

                                <div class="col-md-12 col-xs-12">
                                <div class="table-responsive">
                            <table id="datatables" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                <th>N° Cuota</th>
                                <th>Capital</th>
                                <th>Interes</th>
                                <th>Importe Cuota</th>
                                <th>Vencimiento</th>
                                <th>Estado</th>
                                <th>Saldo</th>
                                <th>S. saldo Capital</th>
                                <th>Capital</th>

                                </tr>
                            </thead>


                            <tbody id="cuota">



                            </tbody>
                            </table>

                        </div>

                        <button class="btn btn-primary"  id="imprimir_contrato">Imprimir Contrato</button>
                        <button class="btn btn-info"  id="imprimir_cuotas">Imprimir Cuotas</button>

                                </div>


                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

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

    <script src="{{ asset('js/show-amortizaciones.js') }}">
    </script>

@endsection
