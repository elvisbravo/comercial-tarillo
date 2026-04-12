@extends('layouts.main')

@section('title')
    traslados
@endsection

@section('css')
    <!-- Sweet Alert-->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css"/>
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

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <h4>Recepción de Guia  

                   => @if($guias->estado==1)  
                      <h1 style="color:red">GUIA PENDIENTE DE RECEPCION {{$guias->serie}}-{{$guias->correlativo}}</h1>
                      @elseif($guias->estado==2)
                      <h1 style="color:red">GUIA OBSERVADA {{$guias->serie}}-{{$guias->correlativo}}</h1>
                      @elseif($guias->estado==0)
                      <h1 style="color:green">GUIA RECEPCIONADA {{$guias->serie}}-{{$guias->correlativo}}</h1>
                     @endif
                     
                </h4>
            </li>
        </ol>
    </nav>

    
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                  <div class="row">
                  <div class="col-md-4">
                        <input type="hidden" value="{{$guias->serie}}" id="serie">
                        <input type="hidden" value="{{$guias->correlativo}}" id="correlativo">
                        <input type="hidden" value="{{$guias->tipo_traslado_id}}" id="tipo_traslado_id">
                        <input type="hidden" value="{{$id_guia}}" id="id_guia">
                        <input type="hidden" value="{{$guias->estado}}" id="estado">
                        <label for="">Fecha</label>
                        <input type="text" class="form-control" value="{{$guias->fecha}}" disabled id="fecha">

                    </div>

                    <div class="col-md-4">
                        <label for="">Cliente</label>
                        <input type="text" class="form-control" value="{{$guias->razon_social}}" disabled id="cliente">

                    </div>
                    <div class="col-md-4">
                        <label for="">Motivo de Traslado</label>
                        <input type="text" class="form-control" value="{{$guias->motivo}}"  disabled id="motivo">

                    </div>
                    <div class="col-md-4">
                        <label for="">Modalidad de Traslado</label>
                        <input type="text" class="form-control" value="{{$guias->modalidad_traslado}}"  disabled id="modalidad">

                    </div>
                    <div class="col-md-4">
                        <label for="">Peso bruto (KGM)</label>
                        <input type="text" class="form-control" value="{{$guias->peso_bruto}}" disabled id="peso">

                    </div>
                    <div class="col-md-4">
                        <label for="">Número de bultos</label>
                        <input type="text" class="form-control" value="{{$guias->bultos}}" disabled id="valumen">

                    </div>
                    <div class="col-md-4">
                        <label for="">Documento Conductor</label>
                        <input type="text" class="form-control" value="{{$guias->documento}}"  disabled id="documento">

                    </div>
                    <div class="col-md-4">
                        <label for="">Nombre del Conductor</label>
                        <input type="text" class="form-control" value="{{$guias->conductor}}" disabled id="documento">

                    </div>
                    <div class="col-md-4">
                        <label for="">Vehiculo</label>
                        <input type="text" class="form-control" value="{{$guias->placa}}" disabled id="vehiculo">

                    </div>
                    <div class="col-md-4">
                        <label for="">Dirección de Partida</label>
                        <input type="text" class="form-control" value="{{$guias->direccion_partida}}" disabled id="direccionpartidad">

                    </div>
                    <div class="col-md-4">
                        <label for="">Ubigeo de Partida</label>
                        <input type="text" class="form-control" value="{{$guias->departamento}} - {{$guias->provincia}} - {{$guias->distrito}}" disabled id="ubigeopartidad">

                    </div>
                    <div class="col-md-4">
                        <label for="">Dirección de LLegada</label>
                        <input type="text" class="form-control" value="{{$guias->direccion_llegada}}" disabled id="direccionllegada">

                    </div>
                    <div class="col-md-4">
                        <label for="">Ubigeo de Llegada</label>
                        <input type="text" class="form-control" value="{{$guias->dep}} - {{$guias->pro}} - {{$guias->dist}}" disabled id="ubigeollegada">

                    </div>
                    <div class="col-md-4">
                        <input type="hidden" value="{{$guias->id_almacen_origen}}" id="id_almacen_origen">
                        <label for="">Almacen Origin</label>
                        <input type="text" class="form-control" value="{{$guias->origen}}" disabled id="almacenorigen">

                    </div>
                    <div class="col-md-4">
                        <label for="">Almacen Destino</label>
                        <input type="text" class="form-control" value="{{$guias->destino}}" disabled id="almacendestino">

                    </div>
                    <div class="col-md-4">
                        <label for="">Ubicación destino <strong style="color:red">*</strong>  </label>
                        <select name="" id="ubicaciones_id" class="form-control">
                            <option value="">--Seleccionar Ubicación--</option>
                                                 @foreach($ubicaciones as $ub)
                                                    @if($guias->id_ubicacion_destino==$ub->id)

                                                     <option value="{{$ub->id}}" selected>{{$ub->abreviatura}}/{{$ub->ubicacion}}</option>

                                                     @else

                                                     <option value="{{$ub->id}}">{{$ub->abreviatura}}/{{$ub->ubicacion}}</option>

                                                     @endif

                                                 @endforeach
                            </select>

                    </div>


                  </div>

                    <div class="table-responsive">
                    <i data-feather="star"></i>
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Producto</th>
                                    <th>Unidad</th>
                                    <th>Cantidad Demandada</th>
                                    <th>Cantidad Realizada</th>
                                    
                                   <!-- <th>ACEPTADO SUNAT</th> -->
                                    <th>ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody id="detalledata">


                            </tbody>

                        </table>
                    </div>

                    
                      <a href="/recepcion-mercaderia" class="btn btn-danger">Cancelar</a>
                   
                        @if($guias->estado==1) 

                        <button class="btn btn-primary" id="todo">Guardar</button>

                      @elseif($guias->estado==2)

                      <button class="btn btn-primary" id="todo">Guardar</button>

                      @elseif($guias->estado==0)
                         <button class="btn btn-primary" disabled id="todo">Guardar</button>
                     @endif


                </div>
            </div>
        </div>
    </div>




    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                    <h5 class="modal-title" id="staticBackdropLabel">Operaciones detalladas</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        <div class="modal-body">
                             <input type="hidden" name="name" id="valor" value="0" />
                             <input type="hidden" id="id_producto">

                             <div class="form-group">
                              <label for="">Producto: </label>
                                <strong id="product_name"></strong>

                             </div>
                             <div class="form-group">
                              <label for="">Demanda:</label>
                                <strong id="cantidad_demanda"></strong>

                             </div>
                             <div class="form-group">
                              <label for="">Cantidad hecha:</label>
                                <strong id="cantidad_hecha">0.00</strong>
                             </div>

                             <div class="form-group">
                              <label for="">Diferencia:</label>
                                <strong id="datafierencia">0.00</strong>
                             </div>


                              <div class="row">
                             

                               <div class="table-responsive">
                               <i data-feather="star"></i>
                                                <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                                    <thead>
                                                    <tr>
                                                        <th>Para</th>
                                                        <th>Realizada</th>
                                                        <th>Unidades</th>
                                                       
                                                    </tr>
                                                    </thead>


                                                    <tbody id="listadecolores">

                                                        <tr>
                                                            <td>
                                                                  <strong id="ubicaciontable"></strong>
                                                            </td>
                                                            <td>
                                                            @if($guias->estado==0)
                                                                <input type="number" disabled class="form-control" id="cantidad_recibido_echa">
                                                            @else

                                                                <input type="number" class="form-control" id="cantidad_recibido">
                                                            @endif

                                                            </td>
                                                            <td>
                                                                Unidades
                                                            </td>
                                                           
                                                        </tr>



                                                    </tbody>

                                                </table>

                                </div>

                              </div>

                        </div>
                        <div class="modal-footer">

                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                        @if($guias->estado==1) 

                        <button type="button" class="btn btn-primary" id="guardar">Guardar</button>

                        @elseif($guias->estado==2)

                       <button type="button" class="btn btn-primary" id="guardar">Guardar</button>

                      @elseif($guias->estado==0)
                          <button type="button" class="btn btn-primary" disabled="true" id="guardar">Guardar</button>
                     @endif
                        
                       

                    </div>
                    </div>
                </div>
        </div>








    @endsection

@section('js')

    <!-- Sweet Alerts js -->
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.flash.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
    

    <script src="{{ asset('js/guia_remision-pendientes-show.js') }}">
    </script>

@endsection
