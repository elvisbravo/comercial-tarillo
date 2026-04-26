@extends('layouts.main')

@section('title')
    Reporte de Compras
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
                <h4>Libro Electrónico de Compras</h4>
            </li>
        </ol>
</nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <form class="row g-3" id="form_datetime">
                        <h6>Selecciona un rango de fechas</h6>
                        <p>Selecciona el rango de fechas de emisión de los comprobantes electrónico,
                            luego has click en Generar para poder visualizar el reporte.
                        </p>
                                <div class="col">
                                    <label for="">Desde:</label>
                                    <input type="date" class="form-control" class="obligatorio" id="desde">
                                </div>
                                <div class="col">
                                    <label for="">Hasta:</label>
                                    <input type="date" class="form-control" class="obligatorio" id="hasta">
                                </div>
                                <div class="col">
                                    <label for=""> Tipo de Comprobante</label>
                                    <input type="hidden" name="name" id="id_comprobante" value="0" />
                                    <select class="form-control" id="tipo_comprobante_id" class="obligatorio">
                                       
                                    </select>
                                </div>
                                <div class="col">
                                    <label for=""> Seleccionar Sucursal</label>
                                    <input type="hidden" name="name" id="id_sucursal" value="0" />
                                    <select class="form-control"  id="sede_id" class="obligatorio">
                                        
                                    </select>
                                </div>
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button class="btn btn-info" type="button" onclick="generar_reporte()">GENERAR REPORTE</button>
                                    @if(App\Permisos::hasPermission('reportecompras', 7))
                                    <button class="btn btn-success" type="button" ID="exportarexcel">EXPORTAR EN EXCEL</button>
                                    @endif
                                </div>
                        
                    
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6>FORMATO DEL LIBRO ELECTRÓNICO DE COMPRAS</h6>
                    
                    <!-- Static Backdrop modal Button -->
                    <i data-feather="star"></i>

                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>CÓDIGO COMPRA</th>
                                    <th>PROVEEDOR</th>
                                    <th>UBICACIÓN</th>
                                    <th>MONEDA</th>
                                    <th>USUARIO</th>
                                    <th>FORMA PAGO</th>
                                    <th>TIPO PAGO</th>
                                    <th>T.COMPROBANTE</th>
                                    <th>FECHA INGRESO</th>
                                    <th>SERIE</th>
                                    <th>CORRELATIVO</th>
                                    <th>MONTO TOTAL</th>
                                    <th>TOTAL IGV</th>
                                    <th>TOTAL COMPRA</th>
                                    <th>%IGV</th>
                                    <th>SEDE</th>
                                    <th>FLETE</th>
                                                            
                                </tr>
                            </thead>


                            <tbody id="listado_reporte_compras">

                            </tbody>
                        </table>
                    </div>
                </div>

                
            </div>
        </div>
    </div>
                                
    
    


    <!-- Modal -->
    






@endsection

@section('js')

    <!-- Sweet Alerts js -->

    <!-- Required datatable js -->
    <!-- Sweet Alerts js -->
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('js/reportecompras.js') }}">
    </script>

@endsection