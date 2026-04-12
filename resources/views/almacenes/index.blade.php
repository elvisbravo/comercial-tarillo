@extends('layouts.main')

@section('title')
    Almacenes
@endsection

@section('css')
    <!-- Sweet Alert-->
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('contenido')

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <h4>Listado de Almacenes</h4>
            </li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <button type="button" class="btn btn-primary mb-3" id="btnadd">Nuevo Almacen</button>

                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Abreviatura</th>
                                    <th>Dirección</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="contentAlmacen">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modal_almacen" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="titleModal">Agregar Almacen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>

                <form id="form_almacen">
                    <input type="hidden" name="idalmacen" id="idalmacen" value="0">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombre_almacen" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre_almacen" name="nombre_almacen" autocomplete="off" placeholder="Nombre">
                        </div>
                        <div class="mb-3">
                            <label for="direccion_almacen" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion_almacen" name="direccion_almacen" autocomplete="off" placeholder="Dirección">
                        </div>
                        <div class="mb-3">
                            <label for="direccion_almacen" class="form-label">Nombre corto</label>
                            <input type="text" class="form-control" id="abreviatura" name="abreviatura" autocomplete="off" placeholder="Nombre corto">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" id="btnform">Guardar</button>
                    </div>
                </form>
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

    <script src="{{ asset('js/almacenes.js') }}">
    </script>

@endsection
