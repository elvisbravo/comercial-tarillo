@extends('layouts.main')

@section('title')
    Editar Precios
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
                                            <h4 class="mb-sm-0 font-size-18">Editar Precio</h4>

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
                                            <a href="{{url('precios')}}" type="button" class="btn btn-primary waves-effect btn-label waves-light"  > <i class="dripicons-reply-all"></i> Atras</a>

                                            </div>

                                            <div class="card-body">

                                            <!-- Static Backdrop modal Button -->
                                              <div class="row">

                                              <div class="col-lg-6 col-xs-12">
                                              <p style="color:red">Todos los campos marcados con (*) son obligatorios</p>
                                                            <label for="">Producto  <!-- <button type="button" class="btn btn-primary"   data-bs-toggle="modal" data-bs-target="#staticBackdrop"> <i class="btn-icon-prepend" data-feather="plus"></i></button> --></label>
                                                         <input type="text" class="form-control obligatorio limpiar" disabled  id="texto_product" value="{{$productos->nomb_pro}}">
                                                         <input type="hidden" id="producto_id" class="obligatorio limpiar" value="{{$precios->articulo_id}}">
                                                         <input type="hidden" id="id" class="obligatorio limpiar" value="{{$precios->id}}">


                                                         <label for="">Seleccionar la Sede  <strong style="color:red">(*)</strong></label>
                                                         <input type="hidden" id="sede_temporal" value="{{$precios->sucursal_id}}">
                                                         <select name="" id="sede_id" class="form-control">
                                                    </select>


                                                         <label for="">Seleccionar Lista de Precios  <strong style="color:red">(*)</strong></label>
                                                          <input type="hidden" id="lista_temporal" class="obligatorio limpiar" value="{{$precios->lista_id}}">
                                                                    <select name="" id="lista_id" class="form-control">
                                                                     </select>






                                                   </div>
                                                   <div class="col-lg-6 col-xs-12">
                                                       <br>
                                                       <label for="">Precio Venta al contado <strong style="color:red">(*)</strong></label>
                                                    <input type="text" class="form-control obligatorio limpiar"  id="precio_contado" value="{{$precios->precio_contado}}">
                                                    <label for="">Descuento al contado <strong style="color:red">(*)</strong></label>
                                                    <input type="text" class="form-control obligatorio limpiar"  id="descuento_contado" value="{{$precios->descuento_contado}}">

                                                    <label for="">Precio Venta al Credito <strong style="color:red">(*)</strong></label>
                                                    <input type="text" class="form-control obligatorio limpiar" id="precio_credito" value="{{$precios->precio_credito}}">



                                                    <div class="modal-footer">
                                                                    <a href="{{url('precios')}}" type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</a>
                                                                    <button type="button" class="btn btn-primary" id="guardar">Guardar</button>


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
    <!-- Sweet Alerts js -->
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('js/editar-precio.js') }}">
    </script>

@endsection
