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

    <div class="row">
        <!-- Formulario de Asignación -->
        <div class="col-md-4 mb-4">
            <div class="card asignar-card">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="font-weight-bold text-dark mb-0">Nueva Asignación de Ruta</h5>
                </div>
                <div class="card-body px-4">
                    <form id="form-asignar-ruta" action="{{ route('admin.asignar.guardar') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Vendedor</label>
                            <select class="form-select" name="vendedor_id" required style="border-radius: 8px;">
                                <option value="">-- Seleccionar Vendedor --</option>
                                @foreach($vendedores as $v)
                                    <option value="{{ $v->usuario_id }}">{{ $v->nombre }}</option>
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
                    <div id="table-history-container">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle font-size-13" id="datatable_rutas">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Vendedor</th>
                                        <th>Sectores Asignados</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($historial->isEmpty())
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">No se han registrado asignaciones de ruta aún.</td>
                                        </tr>
                                    @else
                                        @foreach($historial as $key => $items)
                                            @php
                                                $first = $items->first();
                                            @endphp
                                            <tr>
                                                <td class="font-weight-bold">{{ date('d-m-Y', strtotime($first->fecha)) }}</td>
                                                <td>{{ $first->vendedor->nombre ?? '---' }}</td>
                                                <td>
                                                    @foreach($items as $item)
                                                        <span class="badge bg-soft-success text-success p-2 font-size-12 me-2 mb-1 d-inline-block">
                                                            {{ $item->sector->nomb_sec ?? '---' }}
                                                            <form class="form-eliminar-ruta" action="{{ route('admin.asignar.eliminar', $item->id) }}" method="POST" style="display:inline-block; margin-left: 5px;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" style="border: none; background: transparent; padding: 0; color: #f46a6a; font-weight: bold; line-height: 1;" title="Eliminar asignación">
                                                                    <i class="mdi mdi-close-circle font-size-13"></i>
                                                                </button>
                                                            </form>
                                                        </span>
                                                    @endforeach
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
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        $(".loader").fadeOut("slow");

        let dataTableInstance = null;

        function initDataTable() {
            if (typeof $.fn.DataTable === 'undefined') return;
            if ($.fn.DataTable.isDataTable('#datatable_rutas')) {
                $('#datatable_rutas').DataTable().destroy();
            }
            var rows = $('#datatable_rutas tbody tr');
            var hasRealRows = rows.length > 1 || (
                rows.length === 1 &&
                !rows.find('td').hasClass('dataTables_empty') &&
                !rows.find('td').hasClass('text-muted')
            );
            if (hasRealRows) {
                dataTableInstance = $('#datatable_rutas').DataTable({
                    "language": { "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json" },
                    "order": [[0, "desc"]]
                });
            }
        }

        initDataTable();

        async function reloadTable() {
            try {
                const response = await fetch(window.location.href);
                if (!response.ok) throw new Error('Error al cargar la tabla.');
                const htmlText = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(htmlText, 'text/html');
                const newTableHtml = doc.querySelector('#table-history-container').innerHTML;
                if (typeof $.fn.DataTable !== 'undefined' && $.fn.DataTable.isDataTable('#datatable_rutas')) {
                    $('#datatable_rutas').DataTable().destroy();
                }
                $('#table-history-container').html(newTableHtml);
                initDataTable();
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'No se pudo refrescar la tabla de historial.', 'error');
            }
        }

        // Asignar ruta via AJAX
        $(document).on('submit', '#form-asignar-ruta', async function (e) {
            e.preventDefault();
            e.stopPropagation();
            const form = this;
            const formData = new FormData(form);
            const actionUrl = form.action;

            try {
                const response = await fetch(actionUrl, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();

                if (response.ok) {
                    $(form).find('select').val('');
                    await reloadTable();
                    Swal.fire({
                        icon: 'success',
                        title: '¡Ruta Asignada!',
                        text: data.message || 'La ruta fue asignada exitosamente.',
                        timer: 2500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    Swal.fire('¡Atención!', data.message || 'Error al asignar la ruta.', 'warning');
                }
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'Error en la comunicación con el servidor.', 'error');
            }
        });

        // Eliminar asignación via AJAX con confirmación SweetAlert
        $(document).on('submit', '.form-eliminar-ruta', async function (e) {
            e.preventDefault();
            e.stopPropagation();
            const form = this;

            const confirmResult = await Swal.fire({
                title: '¿Eliminar asignación?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f46a6a',
                cancelButtonColor: '#74788d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });

            if (!confirmResult.isConfirmed) return;

            try {
                const formData = new FormData(form);
                const actionUrl = form.action;
                const response = await fetch(actionUrl, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();

                if (response.ok) {
                    await reloadTable();
                    Swal.fire({
                        icon: 'success',
                        title: '¡Eliminado!',
                        text: data.message || 'Asignación eliminada correctamente.',
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    Swal.fire('¡Atención!', data.message || 'Error al eliminar la asignación.', 'warning');
                }
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'Error en la comunicación con el servidor.', 'error');
            }
        });
    });
</script>
@endsection
