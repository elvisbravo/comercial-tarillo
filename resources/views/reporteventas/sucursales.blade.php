@extends('layouts.main')

@section('title')
    Reporte de Ventas
@endsection

@section('css')
   <!-- Sweet Alert-->
   <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

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
                <h4>Libro Electrónico de Ventas</h4>
            </li>
        </ol>
</nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <form class="row g-3" id="formReporteVentas">
                        <h6>Selecciona un rango de fechas</h6>
                        <p>Selecciona el rango de fechas de emisión de los comprobantes electrónico,
                            luego has click en Generar para poder visualizar el reporte.
                        </p>
                                <div class="col">
                                    <label for="">Desde:</label>
                                    <input type="date" class="form-control" class="obligatorio" id="desde" name="desde" required>
                                </div>
                                <div class="col">
                                    <label for="">Hasta:</label>
                                    <input type="date" class="form-control" class="obligatorio" id="hasta" name="hasta" required>
                                </div>
                                <div class="col">
                                    <label for=""> Tipo de Comprobante</label>
                                    <select class="form-select" id="tipo_comprobante_id" aria-label="Default select example" name="tipo_comprobante">
                                        
                                    </select>
                                </div>
                                <div class="col">
                                    <label for=""> Sucursal</label>
                                    <select class="form-select" id="sucursal" aria-label="Default select example" name="sucursal">
                                        @foreach ($sedes as $sede)
                                            <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button class="btn btn-info" type="submit" id="generar_reporte">GENERAR REPORTE</button>
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
                    <h6>FORMATO DEL LIBRO ELECTRÓNICO DE VENTAS</h6>

                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                            <thead>
                                <tr>
                                    <th>F.EMISIÓN</th>
                                    <th>F.VENCIMIENTO</th>
                                    <th>TIPO DOC</th>
                                    <th>SERIE</th>
                                    <th>NÚMERO</th>
                                    <th>DNI/RUC</th>
                                    <th>RAZÓN SOCIAL</th>
                                    <th>OP.GRAVADA</th>
                                    <th>OP.EXONERADA</th>
                                    <th>OP.INAFECTA</th>
                                    <th>IGV</th>
                                    <th>ISC</th>
                                    <th>ICBPER</th>
                                    <th>OTRO.TRIBUTOS</th>
                                    <th>TOTAL</th>
                                    <th>MONEDA</th>
                                    <th>FEC.COMP.MODIF</th>
                                    <th>TIPO.DOC.MODIF</th>
                                    <th>SERIE.DOC.MODIF</th>
                                    <th>NUM.DOC.MODIF</th>
                                    <th>ESTADO</th>
                                    
                                </tr>
                            </thead>


                            <tbody id="listadoVentas">

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

    <!-- Buttons examples -->
    <script src="{{ asset('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pdfmake/build/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pdfmake/build/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>

    <script>
        const canPrint = {{ App\Permisos::hasPermission('reporteallventas', 7) ? 'true' : 'false' }};
    </script>

    <script src="{{ asset('js/reporteallventas.js') }}">
    </script>

@endsection