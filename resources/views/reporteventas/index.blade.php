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
                                    <div class="dropdown">
                                        <button class="form-select text-start" type="button" id="dropdownComprobantes" data-bs-toggle="dropdown" aria-expanded="false" style="display: flex; justify-content: space-between; align-items: center; background-color: #fff;">
                                            Cargando...
                                        </button>
                                        <ul class="dropdown-menu w-100 p-2" aria-labelledby="dropdownComprobantes" id="tipo_comprobante_id" style="max-height: 250px; overflow-y: auto;">
                                            
                                        </ul>
                                    </div>
                                </div>

                                <div class="col">
                                    <label for=""> Sedes</label>
                                    <div class="dropdown">
                                        <button class="form-select text-start" type="button" id="dropdownSedes" data-bs-toggle="dropdown" aria-expanded="false" style="display: flex; justify-content: space-between; align-items: center; background-color: #fff;">
                                            TODOS
                                        </button>
                                        <ul class="dropdown-menu w-100 p-2" aria-labelledby="dropdownSedes" id="sede_id_list" style="max-height: 250px; overflow-y: auto;">
                                            @foreach($sedes as $sede)
                                            <li>
                                                <div class="form-check dropdown-item px-4 py-1">
                                                    <input class="form-check-input check-sede" type="checkbox" name="sede_id[]" value="{{$sede->id}}" id="sede_{{$sede->id}}" {{ $sede->id == 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label w-100" style="cursor: pointer;" for="sede_{{$sede->id}}">{{$sede->nombre}}</label>
                                                </div>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
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
        const canPrint = {{ App\Permisos::hasPermission('reporteventas', 7) ? 'true' : 'false' }};
    </script>

    <script src="{{ asset('js/reporteventas.js') }}">
    </script>

@endsection