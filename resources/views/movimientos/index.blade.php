@extends('layouts.main')

@section('title')
    Ventas
@endsection

@section('css')
    <!-- Sweet Alert-->
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
        .pagination {
            justify-content: flex-end !important;
        }
    </style>
@endsection
@section('contenido')

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <h4>Lista de Movimientos</h4>
            </li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    @if(App\Permisos::hasPermission('movimientos', 2))
                    <a href="{{route('movimientos.create')}}" class="btn btn-primary mb-3" id="btnadd">Nuevo Movimiento</a>
                    @endif

                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>FECHA</th>
                                    <th>TIPO MOVIMIENTO</th>
                                    <th>CAJA</th>
                                    <th>DESCRIPCION</th>
                                    <th>MONTO</th>
                                    <th>TIPO COMPROBANTE</th>
                                </tr>
                            </thead>
                            <tbody id="contentAlmacen">
                            @foreach ($mov as $key => $movi)
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $movi->fecha }} / {{ $movi->hora }}</td>
                                    <td>{{ $movi->tipo_movimiento }}</td>
                                    <td>{{ $movi->tipo_caja }}</td>
                                    <td>{{ $movi->desc_mov }}</td>
                                    <td>{{ $movi->monto }}</td>
                                    <td>{{ $movi->descripcion_comprobante }}</td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                        {{ $mov->links() }}

                    </div>
                </div>
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

    <script src="{{ asset('js/movimientos.js') }}"></script>

@endsection