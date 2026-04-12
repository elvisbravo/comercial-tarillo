@extends('layouts.main')

@section('title')
    Crear Producto
@endsection

@section('css')
   <!-- Sweet Alert-->
<link href="{{asset('js/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />

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

    <div class="col-md-12 stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Crear Nuevo Producto</h6>

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                            <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                            </ul>
                        </div>
                    @endif
                    <!--<form>-->
                    {!! Form::open(array('route' => 'productos-maestro.store','method'=>'POST','autocomplete'=>'off','files'=>'true')) !!}

                    <div class="row">
                              <p style="color:red">Todos los campos marcados con (*) son obligatorios</p>

                              <div class="col-lg-4">

                                    <div>
                                    <div class="mb-3">
                                                        <label for="example-text-input" class="form-label">Codigo Producto<strong style="color:red">(*) 6 valores como maximo</strong></label>
                                                        <input class="form-control obligatorio limpiar" type="text"  placeholder="Codigo de Producto" name="codigo"  id="codigo">
                                                        <strong id="texto-dni"  style="color:red"></strong>
                                                </div>

                                                <div class="mb-3">
                                                        <label for="example-text-input" class="form-label">Número del Producto <strong style="color:red">(*)</strong></label>
                                                        <input class="form-control obligatorio limpiar" type="text"  placeholder="Número del Producto" name="nomb_pro"    id="nomb_pro">
                                                        <strong id="texto-dni"  style="color:red"></strong>
                                                </div>




                                                    <div class="mb-3">

                                                        <label for="example-text-input" class="form-label">Categoria: </label>

                                                           <select name="categoria_id" id="categoria_id" class="form-control obligatorio selector" onchange="seleccionar(this.value)">



                                                            </select>

                                                    </div>

                                                    <div class="mb-3">

                                                        <label for="example-text-input" class="form-label">SubCategoria: </label>
                                                        <input type="hidden" value="" id="tipobloque">
                                                           <select name="subcategoria_id" id="subcategoria_id" class="form-control obligatorio selector">
                                                                    <option value="">--Seleccionar--</option>



                                                            </select>

                                                    </div>

                                                    <div class="mb-3">

                                                        <label for="example-text-input" class="form-label">Marca: </label>
                                                        <input type="hidden" value="" id="tipobloque">
                                                           <select name="marca_id" id="marca_id" class="form-control obligatorio selector">




                                                            </select>

                                                    </div>

                                                    <div class="mb-3">

                                                        <label for="example-text-input" class="form-label">Color: </label>
                                                        <input type="hidden" value="" id="tipobloque">
                                                           <select name="color_id" id="color_id" class="form-control obligatorio selector">
                                                                    <option value="">--Seleccionar--</option>



                                                            </select>

                                                    </div>

                                                    <div class="mb-3">

                                                        <label for="example-text-input" class="form-label">Unidad Medida: </label>
                                                        <input type="hidden" value="" id="tipobloque">
                                                        <select name="unidad_medida_id" id="unidad_medida_id" class="form-control obligatorio selector " >
                                                                <option value="">--Seleccionar--</option>
                                                                 @foreach($unidadmedida as $unidad)
                                                                    <option value="{{$unidad->id}}" >{{$unidad->descripcion}}</option>

                                                                    @endforeach


                                                            </select>

                                                    </div>











                                            </div>


                               </div>

                               <div class="col-lg-4">

                                    <div>





                                                    <div class="mb-3">
                                                    <label for="example-text-input" class="form-label">Precio Compra: <strong style="color:red">(*)</strong></label>
                                                    <input class="form-control obligatorio limpiar" type="number" placeholder="Precio Compra" name="prec_compra"   id="prec_compra">
                                                    <strong id="texto-dni"  style="color:red"></strong>

                                                    </div>





                                                    <div class="mb-3">
                                                    <label for="example-text-input" class="form-label">Porcentaje Ganancia al contado (0-100): <strong style="color:red">(*)</strong></label>
                                                    <input class="form-control obligatorio limpiar" type="number"  placeholder="Porcentaje Ganancia" name="porcentaje_ganancia_venta" id="porcentaje_ganancia">
                                                    <strong id="texto-dni"  style="color:red"></strong>

                                                    </div>

                                                    <div class="mb-3">
                                                    <label for="example-text-input" class="form-label">Precio Venta al contado: <strong style="color:red">(*)</strong></label>
                                                    <input class="form-control obligatorio limpiar" type="text"    placeholder="Precio Venta" name="precio_venta_contado" id="precio_venta_contado">
                                                    <strong id="texto-dni"  style="color:red"></strong>

                                                    </div>
                                                    <div class="mb-3">
                                                    <label for="example-text-input" class="form-label">Descuento Minimo para la Venta al contado: <strong style="color:red">(*)</strong></label>
                                                    <input class="form-control obligatorio limpiar" type="number" placeholder="Descuento Minimo para la Venta"  name="descuento_minimo_venta_cont"   id="descuento_minimo_venta_cont">
                                                    <strong id="texto-dni"  style="color:red"></strong>

                                                    </div>

                                                    <div class="mb-3">
                                                    <label for="example-text-input" class="form-label">Porcentaje Ganancia al Credito (0-100): <strong style="color:red">(*)</strong></label>
                                                    <input class="form-control obligatorio limpiar" type="number"  placeholder="Porcentaje Ganancia" name="porcentaje_venta_credito" id="porcentaje_venta_credito">
                                                    <strong id="texto-dni"  style="color:red"></strong>

                                                    </div>

                                                    <div class="mb-3">
                                                    <label for="example-text-input" class="form-label">Precio Venta al Credito: <strong style="color:red">(*)</strong></label>
                                                    <input class="form-control obligatorio limpiar" type="text"    placeholder="Precio Venta" name="precio_venta_credito" id="precio_venta_credito">
                                                    <strong id="texto-dni"  style="color:red"></strong>

                                                    </div>
                                                    <div class="mb-3">
                                                    <label for="example-text-input" class="form-label">Descuento Minimo para la Venta al credito: <strong style="color:red">(*)</strong></label>
                                                    <input class="form-control obligatorio limpiar" type="number" placeholder="Descuento Minimo para la Venta" name="descuento_mini_venta_cre"    id="descuento_mini_venta_cre">
                                                    <strong id="texto-dni"  style="color:red"></strong>

                                                    </div>










                                            </div>



                                    </div>

                                    <div class="col-lg-4">

                                            <div>

                                            <div class="mt-3">
                                                    <label for="example-text-input" class="form-label">Control de Stock: </label>
                                                                <div class="col">
                                                                    <input type="hidden" id="controlstock">
                                                                    <div class="d-inline-block me-1">No</div>
                                                                    <div class="form-check form-switch d-inline-block">

                                                                       <input type="checkbox" class="form-check-input tipo_envio" id="switch1" style="cursor: pointer;" name="controlstock" value="SI"  checked />

                                                                        <label for="" class="form-check-label">Si</label>
                                                                    </div>
                                                                </div>
                                                    </div>




                                                <div class="mb-3" >
                                                    <label for="example-text-input" class="form-label">Stock Minimo: <strong style="color:red">(*)</strong></label>
                                                    <input class="form-control obligatorio limpiar" type="number"  value="" placeholder="Stock Minimo" name="stock_minimo" id="stock_minimo">
                                                    <strong id="texto-dni"  style="color:red"></strong>

                                                    </div>


                                            <div class="mb-3">
                                                    <label for="example-text-input" class="form-label">Cuenta Debe: </label>
                                                    <input class="form-control obligatorio limpiar" type="number"    placeholder="Cuenta Debe" name="cuenta_debe"  id="cuenta_debe">
                                                    <strong id="texto-dni"  style="color:red"></strong>

                                                    </div>

                                                    <div class="mb-3">
                                                    <label for="example-text-input" class="form-label">Cuenta Haber: </label>
                                                    <input class="form-control obligatorio limpiar" type="number"   value="" placeholder="Cuenta Haber" name="cuenta_haber" id="cuenta_haber">
                                                    <strong id="texto-dni"  style="color:red"></strong>

                                                    </div>
                                                    <!--<div class="mb-3">
                                                    <label for="example-text-input" class="form-label">Codigo Barras: </label>
                                                    <input class="form-control obligatorio limpiar" type="text"   placeholder="Codigo Barras" name="codigo_barras"  id="codigo_barras">

                                                    <img id="codigo"/>
                                                    </div> !-->

                                                    <div class="mb-3">
                                                    <label for="example-text-input" class="form-label">Imagen: </label>
                                                    <input class="form-control obligatorio limpiar" type="file"   placeholder="Stock Minimo" name="img"  id="img">
                                                    <strong id="texto-dni"  style="color:red"></strong>

                                                    </div>

                                                    <div class="mb-3">
                                                       <label for="example-text-input" class="form-label" style="color:blue">Activar producto para las sedes: </label><br>
                                                       @foreach($sedes as $value)
                                                        <label>{{ Form::checkbox('sedes[]', $value->id, false, array('class' => 'name')) }}
                                                        {{ $value->nombre }}</label>
                                                    <br/>
                                                    @endforeach


                                                    </div>


                                             </div>
                                     </div>
                                    <div class="col-lg-9">

                                    </div>
                                    <div class="col-lg-3">
                                             <button type="submit" class="btn btn-primary submit">CREAR</button>
                                            <a href="{{ route('productos.index') }}" class="btn btn-danger">CANCELAR</a>
                                    </div>




                    </div>

                    {!! Form::close() !!}
                    <!--</form>-->
            </div>
        </div>
    </div>

</div>

<!-- modal -->


</div>


<!--  Extra Large modal example
<div class="modal fade bs-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-xl">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="myExtraLargeModalLabel">Litado de Productos Mestros</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                         <div class="row">
                                                             <div class="col-lg-12">

                                                             <div class="table-responsive">

                                                                            <table id="datatableclientes" class="table table-bordered dt-responsive">
                                                                                    <thead>
                                                                                    <tr>

                                                                                        <th>Codigo</th>
                                                                                        <th>Producto</th>
                                                                                        <th>Categoria</th>
                                                                                        <th>Sub Categoria</th>
                                                                                        <th>Unidad Medida</th>
                                                                                        <th>Marca</th>
                                                                                        <th>Color</th>
                                                                                        <th>Precio Compra</th>
                                                                                        <th>Acciones</th>

                                                                                    </tr>
                                                                                    </thead >


                                                                                    <tbody id="listadeprodcutos">

                                                                                    </tbody>
                                                                            </table>


                                                                </div>



                                                             </div>

                                                         </div>




                                                        </div>
                                                    </div>
                                                </div>
                                            </div> /.modal -->







@endsection

@section('js')

    <!-- Sweet Alerts js -->

    <!-- Required datatable js -->
    <!-- Sweet Alerts js -->
    <script src="{{asset('js/sweetalert2.min.js')}}"></script>
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>


    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>


    <script src="{{ asset('js/crearproductos.js') }}">
    </script>

@endsection
