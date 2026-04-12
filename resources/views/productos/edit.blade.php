@extends('layouts.main')

@section('title')
    Editar Producto
@endsection

@section('style')
   <!-- Sweet Alert-->
<link href="{{asset('js/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />

<!-- DataTables -->
<link rel="stylesheet" href="{{asset('assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css')}}">
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
            <a  href="{{url('productos')}}" class="btn btn-primary " > <i class="dripicons-reply-all"></i> Atras</a>
                <h6 class="card-title">Editar Producto</h6>

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

                    {!!Form::open(array('url'=>'productos/modificar','method'=>'PUT','autocomplete'=>'off','files'=>'true'))!!}
                                                                  {{Form::token()}}
                    <div class="row">
                              <p style="color:red">Todos los campos marcados con (*) son obligatorios</p>

                              <div class="col-lg-6">

                                    <div>
                                                <div class="mb-3">
                                                        <label for="example-text-input" class="form-label">Número del Producto<strong style="color:red">(*)</strong></label>
                                                        <input class="form-control obligatorio limpiar" type="text"   placeholder="Número del Producto" value="{{$productos->nomb_pro}}" name="nomb_pro"  id="nomb_pro">

                                                </div>

                                                <input type="hidden" id="id" name="id" value="{{$productos->id}}">
                                                <input type="hidden" id="producto_id" name="producto_id" value="{{$productos->idproducto}}">
                                                <input type="hidden" id="almacen_id" name="almacen_id" value="{{$productos->idproducto}}">
                                                <input type="hidden" id="categoria_idg"  value="{{$productos->categoria_id}}">
                                                <input type="hidden" id="subcategoria_idg"  value="{{$productos->subcategoria_id}}">
                                                <input type="hidden" id="idcolor"  value="{{$productos->color_id}}">
                                                <input type="hidden" id="idmarca"  value="{{$productos->marca_id}}">



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
                                                                    @if($unidad->id== $productos->unidad_medida_id)
                                                                       <option value="{{$unidad->id}}" selected>{{$unidad->descripcion}}</option>
                                                                    @else

                                                                     <option value="{{$unidad->id}}" >{{$unidad->descripcion}}</option>
                                                                    @endif

                                                                    @endforeach



                                                            </select>

                                                    </div>

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









                                            </div>


                               </div>



                                    <div class="col-lg-6">

                                            <div>




                                          

                                                <div class="mb-3" >
                                                    <label for="example-text-input" class="form-label">Stock Minimo: <strong style="color:red">(*)</strong></label>
                                                    <input class="form-control obligatorio limpiar" type="number"   placeholder="Stock Minimo" name="stock_minimo" id="stock_minimo" value="{{$productos->stock_minimo}}">
                                                    <strong id="texto-dni"  style="color:red"></strong>

                                                    </div>

                                                <div class="mb-3">
                                                    <input type="hidden" id="impuesto_temporal" value="{{$productos->impuesto_id}}">
                                                   <label for="">Impuestos de Cliente</label>

                                                    <select name="impuesto_id" id="impuesto_id" class="form-control">
                                                        
                                                    </select>


                                                </div>

                                                    <div class="mb-3">
                                                    <label for="example-text-input" class="form-label">Costo: </label>
                                                    <input class="form-control obligatorio limpiar" type="number" disabled    placeholder="Cuenta Debe" name="costo"  id="costo" value="{{$productos->costo}}">
                                                    <strong id="texto-dni"  style="color:red"></strong>

                                                    </div>


                
                                                    <div class="mb-3">
                                                    <label for="example-text-input" class="form-label">Peso: </label>
                                                    <input class="form-control obligatorio limpiar" type="number"    placeholder="Cuenta Debe" name="volumen"  id="volumen">
                                                    <strong id="texto-dni"  style="color:red"></strong>

                                                    </div>

                                                    <div class="mb-3">
                                                    <label for="example-text-input" class="form-label">Volumen: </label>
                                                    <input class="form-control obligatorio limpiar" type="number"   value="" placeholder="peso" name="peso" id="peso">
                                                    <strong id="texto-dni"  style="color:red"></strong>

                                                    </div>
                                                    


                                                    <div class="mb-3" >
                                                    <label for="example-text-input" class="form-label">Codigo Barras: </label>
                                                    <input class="form-control obligatorio limpiar" type="text"   placeholder="Codigo Barras" name="codigo_barras"  id="codigo_barras" value="{{$productos->codigo_barras}}">


                                                    <img id="codigo"/>
                                                    </div>

                                                    <div class="mb-3">
                                                    <label for="example-text-input" class="form-label">Imagen: </label>
                                                    <input class="form-control obligatorio limpiar" type="file"   placeholder="Stock Minimo" name="img"  id="img">
                                                    <strong id="texto-dni"  style="color:red"></strong>
                                                    @if($productos->img!="")

                                                    <img src="{{asset('img/productos/'.$productos->img)}}" alt="" class="img-responsive" width="50%">

                                                    @endif

                                                    </div>


                                             </div>
                                     </div>
                                    <div class="col-lg-9">

                                    </div>
                                    <div class="col-lg-3">
                                             <button type="submit" class="btn btn-primary submit">Modificar</button>
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


















@endsection

@section('js')

    <!-- Sweet Alerts js -->

    <!-- Required datatable js -->
    <!-- Sweet Alerts js -->
    <script src="{{asset('js/sweetalert2.min.js')}}"></script>
    <!-- Required datatable js -->
    <script src="{{asset('assets/vendors/datatables.net/jquery.dataTables.js')}}"></script>
    <script src="{{asset('assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>


    <script src="{{ asset('js/editarproducto.js') }}">
    </script>

@endsection
