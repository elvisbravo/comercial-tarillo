@extends('layouts.main')

@section('title', 'Asignación de Rutas y Sectores')

@section('css')
<style>
    .asignar-card {
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: none;
    }
</style>
@endsection

@section('contenido')
<div class="container-fluid py-3">
    <!-- Navegación del Módulo de Ventas Móviles -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; background: #ffffff;">
                <div class="card-body p-2 d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.asignar') }}" class="btn {{ Request::routeIs('admin.asignar') ? 'btn-primary' : 'btn-light' }} btn-action">
                        <i class="mdi mdi-map-marker-path me-1"></i> 1. Asignar Rutas
                    </a>
                    <a href="{{ route('admin.cargar_stock') }}" class="btn {{ Request::routeIs('admin.cargar_stock') ? 'btn-primary' : 'btn-light' }} btn-action">
                        <i class="mdi mdi-truck-load me-1"></i> 2. Cargar Furgonetas
                    </a>
                    <a href="{{ route('admin.liquidar') }}" class="btn {{ Request::routeIs('admin.liquidar') ? 'btn-primary' : 'btn-light' }} btn-action">
                        <i class="mdi mdi-cash-multiple me-1"></i> 3. Liquidar Caja
                    </a>
                    <a href="{{ route('admin.retorno') }}" class="btn {{ Request::routeIs('admin.retorno') ? 'btn-primary' : 'btn-light' }} btn-action">
                        <i class="mdi mdi-swap-horizontal-bold me-1"></i> 4. Retorno de Stock
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Título de Página -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18 text-primary font-weight-bold">
                    <i class="mdi mdi-map-marker-path me-2"></i> Asignación y Programación de Rutas Diarias
                </h4>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 10px;">
            <strong>¡Éxito!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 10px;">
            <strong>¡Atención!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Formulario de Asignación -->
        <div class="col-md-4 mb-4">
            <div class="card asignar-card">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="font-weight-bold text-dark mb-0">Nueva Asignación de Ruta</h5>
                </div>
                <div class="card-body px-4">
                    <form action="{{ route('admin.asignar.guardar') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Vendedor</label>
                            <select class="form-select" name="vendedor_id" required style="border-radius: 8px;">
                                <option value="">-- Seleccionar Vendedor --</option>
                                @foreach($vendedores as $v)
                                    <option value="{{ $v->id }}">{{ $v->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Sector / Ruta</label>
                            <select class="form-select" name="sector_id" required style="border-radius: 8px;">
                                <option value="">-- Seleccionar Sector --</option>
                                @foreach($sectores as $s)
                                    <option value="{{ $s->id }}">{{ $s->nomb_sec }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Fecha Programada</label>
                            <input type="date" class="form-control" name="fecha" value="{{ date('Y-m-d') }}" required style="border-radius: 8px;">
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg py-2 font-weight-bold" style="border-radius: 10px;">
                                <i class="mdi mdi-plus-circle-outline me-1"></i> Asignar Ruta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Historial y Registro de Rutas -->
        <div class="col-md-8">
            <div class="card asignar-card">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="font-weight-bold text-dark mb-0">Historial y Programación de Rutas</h5>
                </div>
                <div class="card-body px-4">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle font-size-13" id="datatable_rutas">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Vendedor</th>
                                    <th>Sector Asignado</th>
                                    <th class="text-center" style="width: 100px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($historial->isEmpty())
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No se han registrado asignaciones de ruta aún.</td>
                                    </tr>
                                @else
                                    @foreach($historial as $h)
                                        <tr>
                                            <td class="font-weight-bold">{{ date('d-m-Y', strtotime($h->fecha)) }}</td>
                                            <td>{{ $h->vendedor->nombre ?? '---' }}</td>
                                            <td>
                                                <span class="badge bg-soft-success text-success p-2 font-size-12">
                                                    {{ $h->sector->nomb_sec ?? '---' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <form action="{{ route('admin.asignar.eliminar', $h->id) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar esta asignación de ruta?');" title="Eliminar asignación">
                                                        <i class="mdi mdi-trash-can-outline font-size-15"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $(".loader").fadeOut("slow");
        
        // Inicializar DataTable si es necesario
        if ($('#datatable_rutas tbody tr').length > 1) {
            $('#datatable_rutas').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
                },
                "order": [[0, "desc"]]
            });
        }
    });
</script>
@endsection
