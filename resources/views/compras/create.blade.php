@extends('layouts.main')

@section('title')
    Listado Compras
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
                                        <h4 class="mb-sm-0 font-size-18">Registro de Compras</h4>

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


                            <div class="card-body">

                                 <div class="row">
                                 <p style="color:red">Todos los campos marcados con (*) son obligatorios</p>
                                     <div class="col-lg-6 col-xs-12">

                                         <div class="row">
                                             <div class="col-lg-4 col-xs-12">
                                                 <label for="">Fecha Compra <strong style="color:red">(*)</strong> </label>
                                                 <input type="date" class="form-control" id="fecha_compra" name="fecha_compra">
                                             </div>
                                             <div class="col-lg-4 col-xs-12">
                                                 <label for="">Tipo Moneda <strong style="color:red">(*)</strong></label>
                                                 <select name="moneda_id" id="moneda_id" class="form-control options">
                                                     <option value="">--Seleccionar--</option>
                                                     @foreach($monedas as $mon)
                                                      
                                                     @if($mon->id==1)
                                                     <option value="{{$mon->id}}" selected >{{$mon->descripcion}}</option>
                                                     @else
                                                     <option value="{{$mon->id}}">{{$mon->descripcion}}</option>

                                                     @endif

                                                     @endforeach
                                                 </select>
                                             </div>

                                             
                                             <div class="col-lg-4 col-xs-12">
                                                <label for="">Tipo Cambio</label>
                                                <input type="number" class="form-control" value="0.00" name="cambio_monto" id="cambio_monto">
                                             </div>

                                         </div><br>

                                         <div class="row" style="">
                                             <div class="col-lg-12 col-xs-12" style=" height: 400px;overflow:auto;overflow-x:hidden">
                                                    <div class="table-responsive">

                                                            <table id="datatable" class="table  dt-responsive  nowrap w-100">
                                                                    <thead >
                                                                        <tr style="background-color: #616A6B ;">
                                                                        <th style="color:#17202A">#</th>
                                                                            <th style="color:#17202A">PRODUCTO</th>
                                                                            <th style="color:#17202A">Unidades</th>
                                                                            <th style="color:#17202A">CANT.</th>
                                                                            <th style="color:#17202A">PRECIO</th>
                                                                            <th style="color:#17202A">FLETE</th>
                                                                            <th style="color:#17202A">SUBTOTAL</th>

                                                                        </tr>
                                                                    </thead>


                                                                    <tbody id="listadocompras">

                                                                    </tbody>
                                                                </table>

                                                    </div>

                                             </div>

                                         </div>

                                         <div class="row">
                                             <div class="col-lg-4 col-xs-12"></div>
                                                    <div class="col-lg-4 col-xs-12">
                                                        <p>Sub Total</p>


                                                        <input type="hidden" id="controlstock">

                                                                <div class="form-check form-switch d-inline-block">

                                                                    <input type="checkbox" class="form-check-input tipo_envio" id="igv" value="0.00" style="cursor: pointer;" name="controlstock" value="NO"   />

                                                                <label for="" class="form-check-label">Igv</label>
                                                            </div><br><br>
                                                            <input type="hidden" id="controlstock">

                                                                        <div class="form-check form-switch d-inline-block">

                                                                            <input type="checkbox" class="form-check-input tipo_envio" id="persecciontem" style="cursor: pointer;" name="controlstock" value="NO"   />

                                                                        <label for="" class="form-check-label">Percepción</label>
                                                                    </div><br><br>

                                                                    <input type="hidden" id="controlstock">

                                                                        <div class="form-check form-switch d-inline-block">

                                                                            <input type="checkbox" class="form-check-input tipo_envio" id="icbpertem" style="cursor: pointer;" name="controlstock" value="NO"   />

                                                                        <label for="" class="form-check-label">Icbper</label>
                                                                    </div>

                                                                    <p style="font-size: 16px;margin-top:5px">Total</p>



                                             </div>
                                                    <div class="col-lg-4 col-xs-12">
                                                        <p>S/. <strong id="subtotal">0.00</strong> <input type="hidden" id="subtotal_input"> </p>
                                                        <p>S/. <strong id="igvtodo">0.00</strong> </p>
                                                        <input type="text" disabled class="form-control" name="perseccion" id="perseccion"><br>
                                                        <input type="text" disabled class="form-control" name="icbper" id="icbper">
                                                          <p style="font-size: 16px;margin-top:5px">S/ <strong id="total_compratemporal"><input type="hidden" name="total_compra" id="total_compra"> 0.00</strong> </p>

                                                    </div>

                                         </div>

                                         <div class="row">
                                             <div class="col-lg-10 col-xs-12">
                                                 <label for="">Proveedor <strong style="color:red">(*)</strong></label>
                                                 <select name="proveedor_id" id="proveedor_id" class="form-control">


                                                 </select>

                                             </div>

                                             <div class="col-lg-2 col-xs-6">

                                                      <button class="btn btn-primary" style="margin-top:20px" data-bs-toggle="modal" data-bs-target="#staticBackdrop" onclick="abrimodal(0)">+</button>
                                             </div>
                                             
                                             <div class="col-lg-6 col-xs-12">
                                                 <label for="">Almacen <strong style="color:red">(*)</strong></label>
                                                 <select name="almacen_id" id="almacen_id" class="form-control">
                                                     <option value="">--Seleccionar--</option>
                                                     @foreach($almacenes as $al)
                                                      <option value="{{$al->id}}" >{{$al->abreviatura}}/{{$al->ubicacion}}</option>

                                                     @endforeach
                                                 </select>

                                             </div>

                                             <div class="col-lg-6 col-xs-12">
                                                 <label for="">Tipo Pago <strong style="color:red">(*)</strong></label>
                                                 <select name="tipo_pago_id" id="tipo_pago_id" class="form-control">
                                                     <option value="">--Seleccionar--</option>
                                                     @foreach($tipopago as $tipo)
                                                       <option value="{{$tipo->id}}">{{$tipo->descripcion}}</option>
                                                     @endforeach
                                                 </select>

                                             </div>
                                             <div class="col-lg-6 col-xs-12">
                                                 <label for="">Tipo Comprobante <strong style="color:red">(*)</strong></label>
                                                 <select name="tipo_comprobante_id" id="tipo_comprobante_id" class="form-control" >
                                                     <option value="">--Seleccionar--</option>
                                                     @foreach($comprobante as $com)
                                                     <option value="{{$com->id}}">{{$com->descripcion}}</option>

                                                     @endforeach
                                                 </select>

                                             </div>

                                             <div id="document_ser" class="row">

                                             <div class="col-lg-3 col-xs-12">
                                                 <label for="">Serie <strong style="color:red">(*)</strong></label>
                                                 <input type="text" class="form-control" name="serie_comprobante" id="serie_comprobante">

                                             </div>
                                             <div class="col-lg-3 col-xs-12">
                                                 <label for="">Número <strong style="color:red">(*)</strong></label>
                                                 <input type="text" class="form-control" name="correlativo_comprobante" id="correlativo_comprobante">

                                             </div>

                                             </div>



                                            <!-- <div class="col-lg-6 col-xs-12">
                                                 <label for="">Forma de Pago <strong style="color:red">(*)</strong></label>
                                                 <select name="forma_pago_id" id="forma_pago_id" class="form-control">
                                                     <option value="">--Seleccionar--</option>
                                                     @foreach($formapago as $for)

                                                      <option value="{{$for->id}}">{{$for->descripcion}}</option>

                                                     @endforeach
                                                 </select>

                                             </div> -->
                                            <!-- <div class="col-lg-6 col-xs-12">
                                                 <label for="">Número de Pago</label>
                                                 <input type="text" class="form-control" name="numero_pago" id="numero_pago">

                                             </div> -->
                                             <div class="col-lg-6 col-xs-12"></div>
                                             <div class="col-lg-6 col-xs-12"><br>
                                             <button type="button" class="btn btn-primary" id="pagar">CREAR</button>
                                             <a href="{{ route('compras.index') }}" class="btn btn-danger">CANCELAR</a>
                                             </div>



                                         </div>


                                     </div>
                                     <div class="col-lg-6 col-xs-12">
                                        <div class="row">

                                                <div class="col-lg-12 col-xs-12">
                                                    <br>
                                                       
                                                                <div class="input-group">
                                                                    <div class="input-group-text">
                                                                        <i data-feather="search"></i>
                                                                    </div>
                                                                        <input type="text" class="form-control" onkeyup="buscar();" id="navbarForm" placeholder="Search here...">
                                                                </div>
                                                       

                                                </div>

                                        </div><br>

                                       <div class="row" style="height:600px;overflow:auto;overflow-x:hidden" id="inyecciondos"></div>


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
                                    <h5 class="modal-title" id="staticBackdropLabel">Formulario</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        <div class="modal-body">
                           <p style="color:red">Todos los campos marcados con (*) son obligatorios</p>
                             <input type="hidden" name="name" id="valor" value="0" />
                             <div class="form-group">
                              <label for="">Razon Social <strong style="color:red">(*)</strong></label>
                              <select name="" id="razon_social" class="form-control">


                              </select>


                             </div>
                            <div class="form-group">
                                <label for="">Número Documento <strong style="color:red">(*)</strong></label>
                                <input type="number" class="form-control obligatorio limpiar" placeholder="Número Documento" id="ruc">

                            </div>
                            <div class="form-group">
                                <label for="">Nombre Comercial <strong style="color:red">(*)</strong></label>
                                <input type="text" class="form-control obligatorio limpiar" placeholder="Nombre Comercial" id="nombre_comercial">

                            </div>

                            <div class="form-group">
                                <label for="">Telefono</label>
                                <input type="text" class="form-control  limpiar" placeholder="Telefono" id="telefono">

                            </div>
                            <div class="form-group">
                                <label for="">Dirección</label>
                                <input type="text" class="form-control  limpiar" placeholder="Dirección" id="direccion">

                            </div>

                            <div class="form-group">
                                <label for="">Email</label>
                                <input type="text" class="form-control  limpiar" placeholder="Email" id="email">

                            </div>

                            <div class="form-group">
                                <label for="">Sitio Web</label>
                                <input type="text" class="form-control  limpiar" placeholder="web_sitie" id="web_sitie">

                            </div>

                            <div class="form-group">
                                <label for="">Contacto</label>
                                <input type="text" class="form-control  limpiar" placeholder="Contacto" id="contacto">

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

     <!-- MODAL PARA AGREGAR LA CANTIDAD DEL PRODUCTO -->
    <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="modal_producto">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="titleModal"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="idProducto">
                    <input type="hidden" id="price-producto">
                    <div class="row mt-3">
                        <div class="col-sm-4 col-4">
                            <p>Cantidad:</p>
                        </div>

                            <input class="form-control" type="number" id="cantidad_producto">

                    </div>

                    <div class="row">
                    <div class="col-sm-4 col-4">
                            <p>Unid.Medida:</p>
                        </div>

                           <select name="" id="uniades_id" class="form-control"></select>

                    </div>

                    <div class="d-grid mt-3">
                        <button type="button" class="btn btn-primary" onclick="agregar_detalle()">Agregar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

      <!-- GENERANDO COMPRA -->


    <div class="modal fade" id="staticBackdropdos" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                 <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content" >
                             <div class="modal-header">
                                    <h5 class="modal-title" id="staticBackdropLabel">Guardando la compra</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                             </div>
                        <div class="modal-body">

                        <div class="row">


                                                      <div class="col-lg-2 col-xs-12"></div>
                                                      <div class="col-lg-8 col-xs-12">

                                                      <img src="{{asset('img/loader-meta.gif')}}" style=""  alt="" class="" width="100%" >
                                                            <h4 style="text-align: center;color:#BA4A00">Espere mintras se guarda la compra...</h4>
                                                            <p style="text-align: center;color:#BA4A00">Gracias<i class=" fas fa-coffee"></i></p>

                                                      </div>
                                                      <div class="col-lg-2 col-xs-12"></div>

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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.14.1/moment.min.js"></script>

    <script src="{{ asset('js/crearcompras.js') }}">
    </script>

@endsection
