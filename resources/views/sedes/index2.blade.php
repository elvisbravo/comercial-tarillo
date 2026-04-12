@extends('layouts.main')

@section('title')
    Sedes
@endsection

@section('css')
    <!-- Sweet Alert-->
    <link href="{{asset('js/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
@endsection
@section('contenido')

    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <a href="{{ route('sedes.create') }}" class="btn btn-primary btn-icon-text mb-2 mb-md-0">
            <i class="btn-icon-prepend" data-feather="plus"></i>
            crear Nueva Sede
        </a>
    </div>

    <div class="container-fluid">
        <div class="row profile-body" id="list_sedes">
            @foreach( $sedes as $sede)
            <div class="d-md-block col-md-4 col-xl-4 left-wrapper">
                <div class="card rounded">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="card-title mb-0">{{ $sede->nombre }}</h6>
                            <div class="dropdown">
                                <button class="btn p-0" type="button" id="dropdownMenuButton"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item d-flex align-items-center" href="javascript:;">
                                        <i data-feather="edit-2" class="icon-sm me-2"></i> <span class="">Editar</span>
                                    </a>
                                    <a class="dropdown-item d-flex align-items-center" href="javascript:;" onclick="correlativos({{ $sede->id }})">
                                        <i data-feather="layers" class="icon-sm me-2"></i> <span class="">Correlativos</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="tx-11 fw-bolder mb-0 text-uppercase">Dirección:</label>
                            <p class="text-muted">{{ $sede->direccion }}</p>
                        </div>
                        <div class="mt-3">
                            <label class="tx-11 fw-bolder mb-0 text-uppercase">Teléfono:</label>
                            <p class="text-muted">{{ $sede->telefono }}</p>
                        </div>

                        <div class="mt-3">
                            <div class="col">
                                <div class="d-inline-block me-1">Prueba</div>
                                <div class="form-check form-switch d-inline-block">
                                    @if( $sede->tipo_envio == 0 )
                                    <input type="checkbox" class="form-check-input tipo_envio" style="cursor: pointer;" value="{{ $sede->id }}" />
                                    @else
                                    <input type="checkbox" class="form-check-input tipo_envio" style="cursor: pointer;" value="{{ $sede->id }}" checked />
                                    @endif
                                    <label for="" class="form-check-label">Producción</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="col">
                                <div class="d-inline-block me-1">Inactivo</div>
                                <div class="form-check form-switch d-inline-block">
                                    @if( $sede->estado == 2 )
                                    <input type="checkbox" class="form-check-input estado" style="cursor: pointer;" value="{{ $sede->id }}" />
                                    @else
                                    <input type="checkbox" class="form-check-input estado" style="cursor: pointer;" value="{{ $sede->id }}" checked />
                                    @endif
                                    <label for="" class="form-check-label">Activo</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 d-grid">
                            <button type="button" class="btn btn-primary btn-block">INGRESAR</button>
                        </div>

                    </div>
                </div>
            </div>
            @endforeach

        </div>

        <!-- modal -->

        <div class="modal fade bd-example-modal-lg" id="modal_correlativos" tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
              
                    <div class="modal-header">
                        <h5 class="modal-title h4" id="myLargeModalLabel">Correlativos</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                    </div>
                    <form id="form_correlativos">
                        <input type="hidden" name="idsede" id="idsede" value="0">
                        <div class="modal-body">
                        
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <select class="form-select" id="comprobante" name="comprobante">
                                            @foreach ($comprobantes as $comprobante)
                                            <option value="{{ $comprobante->id }}">{{ $comprobante->descripcion }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-primary" id="agregar_comprobante">Agregar</button>
                                    </div>
                                </div>
                            </div>

                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>COMPROBANTE</th>
                                        <th>PRUEBA</th>
                                        <th>PRODUCCION</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="bodyComprobantes">
                                </tbody>
                            </table>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success">Guardar</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

    </div>

@endsection

@section('js')

    <!-- Sweet Alerts js -->
    <script src="{{asset('js/sweetalert2.min.js')}}"></script>
    <!-- Required datatable js -->

    <script src="{{ asset('js/sedes.js') }}">
    </script>

@endsection
