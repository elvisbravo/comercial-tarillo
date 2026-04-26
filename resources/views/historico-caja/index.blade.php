@extends('layouts.main')

@section('title')
    Historial de Caja
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

<nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <h4>Historial de Caja</h4>
            </li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>CAJERO</th>
                                    <th>MONTO APERTURA FISICO</th>
                                    <th>MONTO APERTURA VIRTUAL</th>
                                    <th>MONTO CIERRE FISICO</th>
                                    <th>MONTO CIERRE VIRTUAL</th>
                                    <th>FECHA APERTURA</th>
                                    <th>HORA APERTURA</th>
                                    <th>FECHA CIERRE</th>
                                    <th>HORA CIERRE</th>
                                    <th>ESTADO DE CAJA</th>
                                    <th>ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody id="contentAlmacen">
                                @foreach ($cajas as $key => $caja)
                                    <tr>
                                        <td></td>
                                        <td>{{ $caja->name }}</td>
                                        <td>{{ $caja->monto_apertura }}</td>
                                        <td></td>
                                        <td>{{ $caja->monto_cierre_fisico }}</td>
                                        <td>{{ $caja->monto_cierre_virtual }}</td>
                                        <td>
                                            {{ $caja->fecha_apertura }}
                                        </td>
                                        <td>{{ $caja->hora_apertura }}</td>
                                        <td>{{ $caja->fecha_cierre }}</td>
                                        <td>{{ $caja->hora_cierre }}</td>
                                        @if( $caja->estado == 0)
                                            <td>CERRADO</td>
                                        @else
                                            <td>ABIERTO</td>
                                        @endif
                                        <td>
                                            @if(App\Permisos::hasPermission('historico-caja', 6))
                                            <div class="dropdown">
                                                <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bx bx-list-ul"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="./historico-caja/pdfcaja/{{ $caja->id }}" target="_blank">Imprimir Pdf</a> <a class="dropdown-item" href="./resumen-caja/resumenCaja/{{$caja->id}}/{{$caja->fecha_apertura}}/{{$caja->fecha_cierre}}" target="_blank">Resumen Caja</a></li>
                                                    
                                                </ul>
                                               
                                            </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
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

    <!-- Required datatable js -->
    <!-- Sweet Alerts js -->
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('js/HistoricoCaja.js') }}">
    </script>

@endsection