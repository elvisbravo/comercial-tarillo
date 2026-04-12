@extends('layouts.main')

@section('title')
    Ubicaiones
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
                                            <h4 class="mb-sm-0 font-size-18">Listado Ubicaciones</h4>

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
                                           @foreach($almacenes as $al)
                                             <div class="accordion" id="accordionExample{{$al->id}}">
                                                <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingOne{{$al->id}}">
                                                            <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne{{$al->id}}" aria-expanded="true" aria-controls="collapseOne{{$al->id}}">
                                                              {{$al->nombre}}
                                                            </button>
                                                        </h2>
                                                            <div id="collapseOne{{$al->id}}" class="accordion-collapse collapse show" aria-labelledby="headingOne{{$al->id}}" data-bs-parent="#accordionExample{{$al->id}}">
                                                                <div class="accordion-body">
                                                                    <div class="text-muted">
                                                                         @foreach($ubicaciones as $ub)
                                                                            <ul>
                                                                                @if($ub->almacen_id==$al->id)
                                                                                   ==> {{ $ub->name}}  <a href="#" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><button class="btn btn-info" onclick="abrimodal({{$ub->id}});"><i class="fas fa-edit"></i></button>  </a>

                                                                                   @if($ub->estado=='1')
                                                                                   <button type="button"  onclick="eliminar({{$ub->id}})" class="btn btn-danger "><i class="fas fa-trash-alt"></i></button>
                                                                                   @else
                                                                                   <button type="button"  onclick="activar({{$ub->id}})" class="btn btn-warning "><i class="fas fa-sync activar"></i></button>

                                                                                   @endif
                                                                         


                                                                         
                                                                                @endif
                                                                            </ul>
                                                                           @endforeach

                                  
                                                                    </div>
                                                                </div>
                                                            </div>
                                                      </div>
                                                </div>

                                           @endforeach

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
                                <label for="">Ubicación <strong style="color:red">(*)</strong></label>
                                <input type="text" class="form-control obligatorio limpiar" placeholder="Nombre de Ubicación" id="name">

                            </div>
                            <div class="form-group">
                                <label for="">Almacen Padre <strong style="color:red">(*)</strong></label>
                                <select  id="almacen_id" class="form-control">
                                    <option value="">--Seleccionar Almacen--</option>
                                </select>

                            </div>

                            <div class="form-group">
                                <label for="">Tipo de Hubicación <strong style="color:red">(*)</strong></label>
                                <select  id="tipo_ubicacion_id" class="form-control">
                                    <option value="">--Tipo Hubicación--</option>

                                </select>

                            </div>
                           
                            <div class="form-group">
                                <label for="">Responsable</label>
                                <input type="text" class="form-control limpiar" placeholder="Nombre del Responsable" id="responsable">

                            </div>
                            <br>

                            <div class="form-group">

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="es_chatarra">
                                        <label class="form-check-label" for="flexCheckDefault">
                                        ¿Es una ubicación de chatarra?
                                        </label>
                                    </div>

                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                        <input class="form-check-input" type="checkbox"  id="devolucion" >
                                        <label class="form-check-label" for="flexCheckChecked">
                                           ¿Es una ubicación de devolución?
                                        </label>
                                </div>

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

    <script src="{{ asset('js/ubicaciones.js') }}">
    </script>

@endsection
