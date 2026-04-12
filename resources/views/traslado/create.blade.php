@extends('layouts.main')

@section('title')
    traslado | nuevo
@endsection

@section('css')

    <link href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Sweet Alert-->
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- DataTables -->
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
                <h4>CREAR TRASLADO</h4>
            </li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <form id="form_guia">
                        <div class="row">
                            <div>
                                <h5 class="font-size-14 mb-4" style="color:red"><i class="mdi mdi-arrow-right text-primary me-1" style=" font-size:18px"></i> CLIENTE</h5>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label" for="formrow-email-input">Selecciona Tipo de Traslado  <strong style="color:red">*</strong></label>
                                            <select class="form-control" name="doc_traslado" id="tipo_traslado">
                                                <option value="">Seleccione</option>
                                                <option value="7">GUIA INTERNA</option>
                                                <option value="6">GUIA DE REMISION</option>
                                            </select>
                                        </div>
                                    </div>                                                            
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label" for="formrow-email-input">Selecciona un Cliente  <strong style="color:red">*</strong></label>
                                            <input type="hidden" id="id_persona_tempe">

                                            <div class="input-group">
                                                                    <button href="#" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target=".bs-example-modal-xl" onclick="traer_clientes();" >
                                                                       
                                                                        <i data-feather="search"></i>
                                                                       
                                                                    </button>
                                                                        <input type="text" class="form-control" disabled="true"  id="nombresdata" placeholder="Search here...">
                                            </div>

                                           <!-- <select class="form-control" name="cliente" id="change_cliente">
                                                <option value="">Seleccione un cliente</option>
                                            </select>!-->

                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label" for="">Email(opcional)</label>
                                            <input type="email" class="form-control" name="email" id="email">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <hr style="background-color: blue;height: 5px;border-radius: 10px;">

                        <div class="row">
                            <div>
                                <h5 class="font-size-14 mb-4" style="color:red"><i class="mdi mdi-arrow-right text-primary me-1" style=" font-size:18px"></i> DATOS DEL TRASLADO</h5>

                                <div class="row">                                                            
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label" for="formrow-email-input">Motivo del Traslado  <strong style="color:red">*</strong></label>
                                            <select class="form-select" id="motivo">
                                                <option value="VENTA">VENTA</option>
                                                <option value="COMPRA">COMPRA</option>
                                                <option value="TRASLADO ENTRE ESTABLECIMIENTOS DE LA EMPRESA" selected="true">TRASLADO ENTRE ESTABLECIMIENTOS DE LA EMPRESA</option>
                                                <option value="IMPORTACION">IMPORTACION</option>
                                                <option value="EXPORTACION">EXPORTACION</option>
                                                <option value="VENTA SUJETA A CONFIRMACION DEL COMPRADOR">VENTA SUJETA A CONFIRMACION DEL COMPRADOR</option>
                                                <option value="TRASLADO EMISOR ITINERANTE CP">TRASLADO EMISOR ITINERANTE CP</option>
                                                <option value="TRASLADO A ZONA PRIMARIA">TRASLADO A ZONA PRIMARIA</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label" for="formrow-password-input">Modalidad de Traslado  <strong style="color:red">*</strong></label>
                                            <select class="form-select" id="modalidad_traslado">
                                                <option value="TRANSPORTE PRIVADO">TRANSPORTE PRIVADO</option>
                                                <option value="TRANSPORTE PUBLICO">TRANSPORTE PUBLICO</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label" for="formrow-password-input">Fecha Inicial de Traslado <strong style="color:red">*</strong></label>
                                            <input type="date" class="form-control obligatorio" name="fecha_traslado" id="fecha_traslado">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label" for="formrow-password-input">Peso bruto (KGM)</label>
                                            <input type="text" class="form-control" id="peso_bruto" name="peso_bruto">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label" for="formrow-password-input">Número de bultos</label>
                                            <input type="text" class="form-control" id="bultos" name="bultos">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <hr style="background-color: blue;height: 5px;border-radius: 10px;">

                        <div class="row">
                            <div>
                                <h5 class="font-size-14 mb-4" style="color:red"><i class="mdi mdi-arrow-right text-primary me-1" style=" font-size:18px"></i> DATOS DEL TRANSPORTE</h5>

                                <div class="row">                                                            
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label class="form-label" for="formrow-email-input">Tipo Doc.Ident.</label>
                                            <select class="form-select" name="tipo_documento" id="tipo_documento">
                                                <option value="1">D.N.I</option>
                                                <option value="4">CARNET DE EXTRANJERIA</option>
                                                <option value="6">R.U.C</option>
                                                <option value="7">PASAPORTE</option>
                                                <option value="0">OTRO DOCUMENTO</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group mb-3">
                                            <label class="form-label d-block w-100" for="formrow-password-input">N° Doc Conductor  <strong style="color:red">*</strong></label>
                                            <input type="text" class="form-control " aria-describedby="basic-addon2" placeholder="Número de documento Aquí!!!" name="numero_conductor" id="numero_conductor">
                                            <input type="hidden" id="conductor_id">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="button" id="buscarconductor"><i class="bx bx-search-alt align-middle"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label" for="formrow-password-input">Nombre Conductor <strong style="color:red">*</strong></label>
                                            <input type="text" class="form-control obligatorio" id="nombre_conductor" name="nombre_conductor" placeholder="Nombre o Razón Social Aquí">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label" for="formrow-password-input">N° Placa Vehículo:</label>
                                            <input type="text" class="form-control" id="placa" name="placa" placeholder="Número de Placa Transporte">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <hr style="background-color: blue;height: 5px;border-radius: 10px;">

                        <div class="row">
                            <div>
                                <h5 class="font-size-14 mb-4" style="color:red"><i class="mdi mdi-arrow-right text-primary me-1" style=" font-size:18px"></i> PUNTO DE PARTIDA</h5>
    
                                <div class="row">                                                            
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="formrow-email-input">Dirección <strong style="color:red">*</strong></label>
                                            <input type="text" class="form-control obligatorio" id="direccion_partida" name="direccion_partida">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="formrow-password-input">Ubigeo  <strong style="color:red">*</strong></label>
                                            <select class="form-select" id="ubigeo_partida" name="ubigeo_partida">
                                                <option>Seleccione</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
    
                            </div>
                        </div>
                        <hr style="background-color: blue;height: 5px;border-radius: 10px;">

                        <div class="row">
                            <div>
                                <h5 class="font-size-14 mb-4" style="color:red"><i class="mdi mdi-arrow-right text-primary me-1" style=" font-size:18px"></i> PUNTO DE LLEGADA</h5>
    
                                <div class="row">                                                            
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="formrow-email-input">Dirección <strong style="color:red">*</strong> </label>
                                            <input type="text" class="form-control obligatorio" id="direccion_llegada" name="direccion_llegada">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="formrow-password-input">Ubigeo  <strong style="color:red">*</strong></label>
                                            <select class="form-select" id="ubigeo_llegada" name="ubigeo_llegada">
                                                <option>Seleccione una opción</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
    
                            </div>
                        </div>


                        <hr style="background-color: blue;height: 5px;border-radius: 10px;">

                        <div class="row">
                            <div>
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true"> <h5 class="font-size-14 mb-4" style="color:red"><i class="mdi mdi-arrow-right text-primary me-1" style=" font-size:18px"></i> DETALLE DE LA GUIA</h5></button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false"> <h5 class="font-size-14 mb-4" style="color:red"><i class="mdi mdi-arrow-right text-primary me-1" style=" font-size:18px"></i> DOCUMENTO DE REFERENCIA</h5></button>
                                        </li>
                                      
                             </ul>
                                    <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                         <div class="row">

                                            <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="formrow-email-input">Almacen Origen <strong style="color:red">(*)</strong></label>
                                                        <select class="form-select" name="almacen_origen" id="almacen_origen">
                                                            <option value="">--Seleccionar--</option>
                                                            @foreach($origen as $ub)
                                                            <option value="{{$ub->id}}">{{$ub->abreviatura}}/{{$ub->ubicacion}}</option>
                                                            @endforeach
                                                            
                                                        </select>
                                                    </div>
                                            </div>

                                           
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="formrow-email-input">Almacen Destino <strong style="color:red">(*)</strong></label>
                                                    <select class="form-select" name="almacen_destino" id="almacen_destino">
                                                    <option value="">--Seleccionar--</option>
                                                         @foreach($destinos as $ub)
                                                            <option value="{{$ub->id}}">{{$ub->abreviatura}}/{{$ub->ubicacion}}</option>
                                                            @endforeach
                                                       
                                                        
                                                    </select>
                                                </div>
                                             </div>




                                         </div>

                                         <div class="row">

                                         
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="">Producto</label>
                                            <select class="form-select" id="search_producto">
                                                <option>Seleccione un producto</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group mb-3">
                                            <label class="form-label d-block w-100" for="f">Cantidad</label>
                                            <input type="text" class="form-control" aria-describedby="basic-addon2" id="cantidad_trasladar" placeholder="cantidad a trasladar">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="button" id="btnAddTraslado"><i class="bx bx-plus align-middle"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="input-group mb-3">
                                            <label class="form-label d-block w-100" for="">Stock</label>
                                            <input type="text" class="form-control" id="stock_p" aria-describedby="basic-addon2" readonly>
                                        </div>
                                    </div>

                                         </div>

                                    </div>
                                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

                                    <div class="row">                                                            
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label" for="formrow-email-input">Tipo de Documento</label>
                                            <input type="hidden" id="id_documento_electronico">
                                            <select class="form-select" name="documento_referencia" id="documento_referencia">
                                                <option value="2">Factura</option>
                                                <option value="1">Boleta</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label" for="formrow-password-input">Serie</label>
                                            <input type="text" class="form-control" id="serie_referencia" name="serie_referencia" placeholder="XXXX">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label" for="formrow-password-input">Número</label>
                                            <input type="text" class="form-control" id="numero_referencia" name="numero_referencia" placeholder="########">
                                        </div>
                                    </div>
                                </div>


                                    
                                    </div>
                                    
                                    </div>
                            </div>

                        </div>

                        <div class="row">
                            <div>
                               
    
                              

                                <div class="row">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Descripción</th>
                                                    <th>Unid/Medida</th>
                                                    <th>Cantidad</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="contentTraslado">
                                                <!--<tr>
                                                    <td>MUEBLE PARA COCINA</td>
                                                    <td>UNIDADES</td>
                                                    <td>10</td>
                                                    <td>
                                                        <i class="bx bx-trash fs-4 align-middle text-danger" style="cursor: pointer"></i>
                                                    </td>
                                                </tr>-->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
    
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-center">
                                <a href="{{ route('traslados.index') }}" class="btn btn-danger">CANCELAR</a>
                                <button type="submit" class="btn btn-primary">GUARDAR</button>
                            </div>
                        </div>

                    </form>

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

                                                                            <table id="datatablesx" class="table table-bordered dt-responsive">
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

@endsection

@section('js')

    <!-- Sweet Alerts js -->
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Required datatable js -->
      <!-- Required datatable js -->
      <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>

    <script src="{{ asset('js/crear_guia.js') }}">
    </script>

@endsection
