@extends('layouts.main')

@section('title')
    Anular Credito
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
                                        <h4 class="mb-sm-0 font-size-18">Anulación Credito</h4>

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
                                                    <label for="">Buscar Cliente</label>
                                                    <form class="app-search d-lg-block">

                                                    <div class="position-relative">
                                                        <input type="text" class="form-control" placeholder="Search..." disabled id="nombresdata">
                                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target=".bs-example-modal-xl" type="button"><i class="bx bx-search-alt align-middle"></i></button>
                                                    </div>

                                                    </form>
                                                    <input type="hidden" id="id_persona_tempe">

                                                </div>
                                                <div class="col-lg-6 col-xs-12">
                                                    <label for="">Documento de Identidad</label> <br><br>
                                                    <input type="text" disabled class="form-control" id="documento">

                                                </div>


                                                <div class="col-lg-12 col-xs-12">
                                                   <i data-feather="star"></i>
                                                    <div class="table-responsive">

                                                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                                        <thead>
                                                        <tr>
                                                            <th>N° Credito</th>
                                                            <th>Documento</th>
                                                            <th>Cliente</th>
                                                            <th>Fecha Registro Credito</th>
                                                            <th>Número de Cuotas</th>
                                                            <th>Tipo Vencimiento</th>
                                                            <th>Monto Credito</th>

                                                            <th>Saldo Pendiente</th>
                                                            <th>Estado</th>
                                                            <th>Acciones</th>
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


                                            <div class="modal fade bs-example-modal-xl-y" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
                                                <div class="modal-dialog  modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="myExtraLargeModalLabel">Detalle de Credito</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" id="codigo_credito">

                                                        <label for="">Ingrese el porque se esta anulado el Credito? <strong style="color:red">Campo obligatorio</strong></label>

                                                            <textarea name="" id="observacion" cols="5" rows="5" class="form-control obligatorio">
                                                            </textarea>


                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                                            <button type="button" class="btn btn-primary" id="guardar">Guardar</button>


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

    <script src="{{ asset('js/anular-creditos-activos.js') }}">
    </script>

@endsection
