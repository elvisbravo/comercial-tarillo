@extends('layouts.main')

@section('title')
    Productos
@endsection

@section('css')
   <!-- Sweet Alert-->
<link href="{{asset('js/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />

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


    <div class="container-fluid">

<!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0 font-size-18">Listado Productos</h4>

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

                            @if(App\Permisos::hasPermission('productos', 2))
                            <a href="{{url('productos/create')}}" type="button" class="btn btn-primary"  > <i class="btn-icon-prepend" data-feather="plus"></i>Nuevo</a>
                            @endif

                            </div>

                            <div class="card-body">

                                <div class="row">

                                     <!-- <div class="col-lg-2 col-xs-12">
                                          <label for="">Seleccionar Almacen disponible</label>
                                          <select name="" id="" class="form-control" onchange="listadoproductos(this.value);">
                                              <option value="">--Seleccionar--</option>
                                              <option value="0" selected>TODO</option>
                                               @foreach ($origen as $item)
                                                    <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                                @endforeach
                                          </select>


                                      </div> -->

                                </div><br>
                                   <!-- Static Backdrop modal Button -->
                            @if ($message = Session::get('success'))
                                <div class="alert alert-success">
                                    <p>{{ $message }}</p>
                                </div>
                            @endif

                            <!-- Static Backdrop modal Button -->






                             <div class="table-responsive">
                                <table id="datatable" class="table  dt-responsive  nowrap w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Producto</th>
                                        <th>Categoria</th>
                                        <th>Sub Categoria</th>
                                        <th>Unidad Medida</th>
                                        <th>Marca</th>

                                        <th>Precio Venta al Contado</th>
                                        <th>Precio Venta al Credito</th>
                                        <th>Stock</th>
                                        <th>Acciones</th>
                                    </tr>
                                    </thead>


                                    <tbody id="listadoproductos">

                                    </tbody>
                                </table>
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
    <script src="{{asset('js/sweetalert2.min.js')}}"></script>
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <script>
        const canEdit = {{ App\Permisos::hasPermission('productos', 3) ? 'true' : 'false' }};
        const canDelete = {{ App\Permisos::hasPermission('productos', 4) ? 'true' : 'false' }};
    </script>

    <script src="{{ asset('js/productos.js') }} ">
    </script>

@endsection
