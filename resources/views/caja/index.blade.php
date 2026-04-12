@extends('layouts.main')

@section('title')
Caja
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
            <h4>Caja</h4>
        </li>
    </ol>
</nav>

<div class="row">
    <div class="col-8">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-8">
                        <button type="button" class="btn btn-warning w-lg">Arqueo de Caja</button>
                        @if($estado_caja == 0)
                        <button type="button" class="btn btn-success w-lg" id="abrir_cerrar_caja">Abrir Caja</button>
                        @else
                        <button type="button" class="btn btn-danger w-lg" id="abrir_cerrar_caja">Cerrar Caja</button>
                        @endif

                        <input type="hidden" id="estado_caja" value="{{ $estado_caja }}">
                        <input type="hidden" id="idcaja" value="{{ $idcaja }}">
                    </div>
                    <div class="col-md-2"></div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header border-bottom text-uppercase bg-info text-dark fw-bold">

                                Caja Fisica Al Día: {{$fecha}} <br>
                                <label for="">Estado Caja:
                                    
                                </label>
                                
                            </div>
                            <div class="card-body">
                                
                                <h2 class="text-center" style="font-size: 58px;">S/ <strong class="total">{{ $total_fisico }}</strong> </h2>

                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="card-text ingress">Ingresos: S/. <span>{{ $total_ingresos_fisico }}</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="card-text">Egresos: S/ <span>{{ $total_egresos_fisico}}</span></p>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer bg-info border-top text-dark fw-bold Transacciones">

                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header border-bottom text-uppercase bg-success text-dark fw-bold">

                                Caja Virtual Al Día: {{$fecha}} <br>
                                <label for="">Estado Caja:
                                    
                                </label>
                            </div>
                            <div class="card-body">
                                
                                <h2 class="text-center" style="font-size: 58px;">S/ <strong class="total">{{ $total_virtual }}</strong> </h2>


                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="card-text ingress">Ingresos: S/. <span>{{ $total_ingresos_virtual }}</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="card-text">Egresos: S/ <span>{{ $total_egresos_virtual }}</span></p>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer bg-success border-top text-dark fw-bold Transacciones">

                            </div>
                        </div>
                    </div>


                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>FORMAS DE PAGO</th>
                                        <th>TRANSACCIONES</th>
                                        <th>INGRESOS(0)</th>
                                        <th>EGRESOS(0)</th>
                                        <th>TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>EFECTIVO</td>
                                        <td>0</td>
                                        <td>0</td>
                                        <td>0</td>
                                        <td>0</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-4">

        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Reporte de caja fisica diaria</h4>
            </div>
            <div class="card-body">
                <div id="line-chart" data-colors='["#2ab57d", "#ccc"]' class="e-charts"></div>
            </div>
        </div>

    </div>
</div>

<div id="modal-aperturar-caja" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Abrir caja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="aperturar_caja">
                <div class="modal-body">
                    <div class="mb-3">
                    <label for="example-text-input" class="form-label">Fecha</label>
                    <input type="date" class="form-control" id="fecha_apertura" name="fecha_apertura">

                    </div>

                    <div class="mb-3">
                        <label for="example-text-input" class="form-label">Monto Fisico</label>
                        <input class="form-control obligatorio" type="number" id="monto_aperturar" name="monto_aperturar" value="0">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger waves-effect" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light" id="guardar">Guardar</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>


@endsection

@section('js')
<!-- Sweet Alerts js -->
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Required datatable js -->
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

<script src="{{asset('assets/libs/echarts/echarts.min.js')}}"></script>

<script src="{{asset('js/caja.js')}}"></script>
@endsection