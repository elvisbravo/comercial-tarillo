@extends('layouts.main')

@section('title')
        Proveedores
@endsection

@section('css')
    <!-- Sweet Alert-->
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

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
                                            <h4 class="mb-sm-0 font-size-18">Listado Proveedores</h4>

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
                                            @if(App\Permisos::hasPermission('proveedores', 2))
                                            <button type="button" class="btn btn-primary"  onclick="abrimodal(0)" data-bs-toggle="modal" data-bs-target="#staticBackdrop"> <i class="btn-icon-prepend" data-feather="plus"></i> Nuevo Proveedor</button>
                                            @endif
                                            </div>

                                            <div class="card-body">

                                            <!-- Static Backdrop modal Button -->
                                            <i data-feather="star"></i>

                                            <div class="table-responsive">
                                                <table id="datatable" class="table  dt-responsive  nowrap w-100">
                                                        <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Razon Social</th>
                                                            <th>Ruc</th>
                                                            <th>Nombre Comercial</th>
                                                            <th>Telefono</th>
                                                            <th>Direccion</th>
                                                            <th>Estado</th>
                                                            <th>Acciones</th>

                                                        </tr>
                                                        </thead>


                                                        <tbody id="listadoproveedores">

                                                        </tbody>
                                                    </table>
                                                </div>



                                            </div>
                                        </div>
                                    </div>
                                </div>

                </div>









     <!-- Static Backdrop Modal -->
     <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                    <h5 class="modal-title" id="staticBackdropLabel">Formulario de Proveedor</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        <div class="modal-body">
                            <p class="text-muted mb-4">Todos los campos marcados con <span class="text-danger">(*)</span> son obligatorios</p>
                             <input type="hidden" name="name" id="valor" value="0" />
                             
                             <div class="row">
                                 <div class="col-md-6 mb-3">
                                     <label for="razon_social" class="form-label">Persona <strong class="text-danger">(*)</strong></label>
                                     <select name="razon_social" id="razon_social" class="form-select obligatorio">
                                     </select>
                                 </div>
                                 <div class="col-md-6 mb-3">
                                     <label class="form-label" for="documento_identidad">Tipo Doc. Ident <strong class="text-danger">(*)</strong></label>
                                     <select class="form-select obligatorio" name="documento_identidad" id="documento_identidad">
                                         @foreach ($tipo_documento as $documento)
                                             <option value="{{ $documento->id }}" {{ $documento->id == 1 ? 'selected' : '' }}>{{ $documento->nombre }}</option>
                                         @endforeach
                                     </select>
                                 </div>
                             </div>

                             <div class="row">
                                 <div class="col-md-6 mb-3">
                                     <label for="ruc" class="form-label">Número Documento <strong class="text-danger">(*)</strong></label>
                                     <div class="input-group">
                                         <input type="number" class="form-control obligatorio" id="ruc" placeholder="Número de documento" name="ruc">
                                         <button class="btn btn-primary" type="button" id="btn_consultar"><i class="bx bx-search-alt align-middle"></i></button>
                                     </div>
                                 </div>
                                 <div class="col-md-6 mb-3">
                                     <label for="nombre_comercial" class="form-label">Nombre / Razón Social <strong class="text-danger">(*)</strong></label>
                                     <input type="text" class="form-control obligatorio limpiar" placeholder="Nombre Comercial" id="nombre_comercial">
                                 </div>
                             </div>

                             <div class="row">
                                 <div class="col-md-6 mb-3">
                                     <label for="direccion" class="form-label">Dirección</label>
                                     <input type="text" class="form-control limpiar" placeholder="Dirección" id="direccion">
                                 </div>
                                 <div class="col-md-6 mb-3">
                                     <label for="telefono" class="form-label">Teléfono</label>
                                     <input type="text" class="form-control limpiar" placeholder="Teléfono" id="telefono">
                                 </div>
                             </div>

                             <div class="row">
                                 <div class="col-md-6 mb-3">
                                     <label for="email" class="form-label">Email</label>
                                     <input type="email" class="form-control limpiar" placeholder="Email" id="email">
                                 </div>
                                 <div class="col-md-6 mb-3">
                                     <label for="web_sitie" class="form-label">Sitio Web</label>
                                     <input type="text" class="form-control limpiar" placeholder="URL Sitio Web" id="web_sitie">
                                 </div>
                             </div>

                             <div class="row">
                                 <div class="col-md-12 mb-3">
                                     <label for="contacto" class="form-label">Contacto / Representante</label>
                                     <input type="text" class="form-control limpiar" placeholder="Nombre del contacto" id="contacto">
                                 </div>
                             </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-primary waves-effect waves-light" id="guardar">Guardar</button>
                            <button type="button" class="btn btn-primary waves-effect waves-light" id="actualizar">Actualizar</button>
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

    <script>
        const canEdit = {{ App\Permisos::hasPermission('proveedores', 3) ? 'true' : 'false' }};
        const canDelete = {{ App\Permisos::hasPermission('proveedores', 4) ? 'true' : 'false' }};
        const canViewDetail = {{ App\Permisos::hasPermission('proveedores', 6) ? 'true' : 'false' }};
    </script>

    <script src="{{ asset('js/proveedores.js') }}">
    </script>

@endsection
