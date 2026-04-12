@extends('layouts.main')

@section('title')
    Kardex
@endsection

@section('css')

    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Sweet Alert-->
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('contenido')

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <h4>Kardex de Producto</h4>
            </li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <form id="form_kardex">

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
									<label class="form-label">Seleccionar Producto</label>
									<select class="js-example-basic-single form-select" id="item_productos" name="producto" data-width="100%">
										<option value="">Seleccione</option>
									</select>
								</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="almacen_destino" class="form-label">Almacen</label>
                                    <select class="form-select" id="almacen" name="almacen" required>
                                        <option value="">Seleccione</option>
                                        @foreach ($origen as $item)
                                        <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                        @endforeach
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label">Fecha Inicio</label>
                                    <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio">
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label">Fecha final</label>
                                    <input type="date" class="form-control" name="fecha_final" id="fecha_final">
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-2">
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary mt-4">Generar Kardex</button>
                                </div>
                            </div><!-- Col -->
                        </div>

                    </form>
                    
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Fecha</th>
                                    <th>Detalle</th>
                                    <th>ENTRADAS</th>
                                    <th>SALIDAS</th>
                                    <th>STOCK</th>
                                </tr>
                            </thead>
                            <tbody id="contentKardex">
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <!-- Sweet Alerts js -->
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('js/kardex.js') }}">
    </script>

@endsection
