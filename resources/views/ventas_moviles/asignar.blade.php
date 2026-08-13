@extends('layouts.main')

@section('title', 'Asignación de Rutas y Sectores')

@section('css')
<!-- DataTables -->
<link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    .asignar-card {
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: none;
    }

    .modal .modal-header .modal-title {
        color: #fff !important;
        opacity: 1 !important;
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
                    <a href="{{ route('admin.cargar_stock.historial') }}" class="btn {{ Request::routeIs('admin.cargar_stock.historial') ? 'btn-warning' : 'btn-light' }} btn-action">
                        <i class="mdi mdi-history me-1"></i> Historial de Cargas
                    </a>
                    <a href="{{ route('admin.recojo') }}" class="btn {{ Request::routeIs('admin.recojo') ? 'btn-danger' : 'btn-light' }} btn-action">
                        <i class="mdi mdi-package-variant-remove me-1"></i> Recojo de Mercadería
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
                            <label class="form-label font-weight-bold">Zona</label>
                            <select class="form-select" id="filtro_zona_asignar" required style="border-radius: 8px;">
                                <option value="">-- Seleccionar Zona --</option>
                                @foreach($zonas as $z)
                                    <option value="{{ $z->id }}">{{ $z->nomb_zona }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Sectores de la Zona</label>
                            <div id="sectores_de_zona" class="border rounded p-2" style="max-height: 180px; overflow-y: auto; border-radius: 8px;">
                                <p class="text-muted font-size-13 mb-0" id="sectores_de_zona_vacio">Selecciona una zona para ver sus sectores.</p>
                                @foreach($sectores as $s)
                                    <div class="form-check sector-checkbox-item" data-zona-id="{{ $s->zona_id }}" style="display:none;">
                                        <input class="form-check-input" type="checkbox" name="sectores_ids[]" value="{{ $s->id }}" id="sector_chk_{{ $s->id }}">
                                        <label class="form-check-label" for="sector_chk_{{ $s->id }}">{{ $s->nomb_sec }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Fecha Programada</label>
                            <input type="date" class="form-control" name="fecha" value="{{ date('Y-m-d') }}" required style="border-radius: 8px;">
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Tipo de Asignación</label>
                            <select class="form-select" name="tipo" required style="border-radius: 8px;">
                                <option value="AMBOS" selected>Ambos (Venta y Cobranza)</option>
                                <option value="VENTA">Solo Venta</option>
                                <option value="COBRANZA">Solo Cobranza</option>
                            </select>
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
                    <div class="row g-2 align-items-end mb-3">
                        <div class="col-auto">
                            <label class="form-label font-weight-bold mb-1 font-size-12">Desde</label>
                            <input type="date" id="filtro_fecha_desde" value="{{ $fechaDesde }}" class="form-control form-control-sm" style="border-radius: 8px;">
                        </div>
                        <div class="col-auto">
                            <label class="form-label font-weight-bold mb-1 font-size-12">Hasta</label>
                            <input type="date" id="filtro_fecha_hasta" value="{{ $fechaHasta }}" class="form-control form-control-sm" style="border-radius: 8px;">
                        </div>
                    </div>

                    <div id="table-history-container">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle font-size-13" id="datatable_rutas">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Vendedor</th>
                                        <th>Zonas Asignadas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($historial as $key => $items)
                                        @php
                                            $first = $items->first();
                                            $porZona = $items->groupBy(function ($item) {
                                                return optional(optional($item->sector)->zona)->nomb_zona ?? 'Sin Zona';
                                            });
                                        @endphp
                                        <tr>
                                            <td class="font-weight-bold" data-order="{{ $first->fecha }}">{{ date('d-m-Y', strtotime($first->fecha)) }}</td>
                                            <td>{{ $first->vendedor->nombre ?? '---' }}</td>
                                            <td>
                                                @foreach($porZona as $nombreZona => $itemsZona)
                                                    @php
                                                        $detalleZona = $itemsZona->map(function ($item) {
                                                            $tipoInfo = [
                                                                'VENTA' => ['label' => 'Venta', 'class' => 'bg-soft-primary text-primary'],
                                                                'COBRANZA' => ['label' => 'Cobranza', 'class' => 'bg-soft-warning text-warning'],
                                                                'AMBOS' => ['label' => 'Ambos', 'class' => 'bg-soft-info text-info'],
                                                            ][$item->tipo ?? 'AMBOS'] ?? ['label' => 'Ambos', 'class' => 'bg-soft-info text-info'];
                                                            return [
                                                                'id' => $item->id,
                                                                'sector' => $item->sector->nomb_sec ?? '---',
                                                                'tipo_label' => $tipoInfo['label'],
                                                                'tipo_class' => $tipoInfo['class'],
                                                            ];
                                                        });
                                                    @endphp
                                                    <span class="badge bg-soft-success text-success p-2 font-size-12 me-2 mb-1 d-inline-block btn-ver-zona-detalle"
                                                          style="cursor: pointer;"
                                                          data-zona="{{ $nombreZona }}"
                                                          data-vendedor="{{ $first->vendedor->nombre ?? '---' }}"
                                                          data-fecha="{{ date('d-m-Y', strtotime($first->fecha)) }}"
                                                          data-detalle='{{ $detalleZona->toJson() }}'>
                                                        {{ $nombreZona }} ({{ $itemsZona->count() }})
                                                        <i class="mdi mdi-eye-outline ms-1"></i>
                                                    </span>
                                                @endforeach
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
    </div>
</div>

<!-- Modal Detalle de Zona Asignada -->
<div class="modal fade" id="modalDetalleZonaAsignada" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 15px 40px rgba(0,0,0,0.15);">
            <div class="modal-header bg-dark text-white p-4 border-0">
                <div>
                    <h5 class="modal-title font-weight-bold mb-1" id="modalZonaTitulo">Zona</h5>
                    <small class="text-white-50" id="modalZonaSubtitulo">---</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle font-size-13 mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Sector</th>
                                <th class="text-center">Tipo</th>
                                <th class="text-center" style="width: 60px;">Quitar</th>
                            </tr>
                        </thead>
                        <tbody id="modalZonaDetalleBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light p-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/utils.js') }}"></script>
<script>
    const csrfToken = '{{ csrf_token() }}';
    const urlAsignarDatos = "{{ route('admin.asignar.datos') }}";
    const urlAsignarEliminarBase = "{{ url('ventas-moviles/asignar-ruta/eliminar') }}";

    $(document).ready(function () {
        $(".loader").fadeOut("slow");

        initDataTable('#datatable_rutas', {
            order: [[0, 'desc']],
            columnDefs: [{ orderable: false, targets: 2 }]
        });

        // Refresca solo la tabla de historial (fetch POST, sin tocar la URL) con las
        // fechas actualmente seleccionadas en el filtro.
        async function refrescarHistorial() {
            try {
                const fechaDesde = $('#filtro_fecha_desde').val();
                const fechaHasta = $('#filtro_fecha_hasta').val();
                const response = await fetch(urlAsignarDatos, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams({ fecha_desde: fechaDesde, fecha_hasta: fechaHasta })
                });
                if (!response.ok) throw new Error('Error al cargar la tabla.');
                const htmlText = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(htmlText, 'text/html');
                const newTableHtml = doc.querySelector('#table-history-container').innerHTML;
                $('#table-history-container').html(newTableHtml);
                initDataTable('#datatable_rutas', {
                    order: [[0, 'desc']],
                    columnDefs: [{ orderable: false, targets: 2 }]
                });
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'No se pudo refrescar la tabla de historial.', 'error');
            }
        }

        $('#filtro_fecha_desde, #filtro_fecha_hasta').on('change', refrescarHistorial);

        // Al elegir una zona en el formulario de asignación, mostrar sus sectores
        // (todos tildados por defecto, se pueden destildar antes de guardar).
        function actualizarSectoresDeZona() {
            const zonaId = $('#filtro_zona_asignar').val();
            const $items = $('.sector-checkbox-item');

            $items.hide().find('input[type="checkbox"]').prop('checked', false);

            if (!zonaId) {
                $('#sectores_de_zona_vacio').text('Selecciona una zona para ver sus sectores.').show();
                return;
            }

            const $deLaZona = $items.filter('[data-zona-id="' + zonaId + '"]');
            if ($deLaZona.length === 0) {
                $('#sectores_de_zona_vacio').text('Esta zona no tiene sectores registrados.').show();
                return;
            }

            $('#sectores_de_zona_vacio').hide();
            $deLaZona.show().find('input[type="checkbox"]').prop('checked', true);
        }

        $('#filtro_zona_asignar').on('change', actualizarSectoresDeZona);

        // Ver detalle de una zona asignada (sectores incluidos, con su tipo y opción de quitar)
        $(document).on('click', '.btn-ver-zona-detalle', function () {
            const zona = $(this).data('zona');
            const vendedor = $(this).data('vendedor');
            const fecha = $(this).data('fecha');
            const detalle = $(this).data('detalle') || [];

            $('#modalZonaTitulo').text(zona);
            $('#modalZonaSubtitulo').text(vendedor + ' · ' + fecha);

            let html = '';
            detalle.forEach(function (d) {
                html += '<tr>';
                html += '<td>' + d.sector + '</td>';
                html += '<td class="text-center"><span class="badge ' + d.tipo_class + '">' + d.tipo_label + '</span></td>';
                html += '<td class="text-center">';
                html += '<form class="form-eliminar-ruta" action="' + urlAsignarEliminarBase + '/' + d.id + '" method="POST" style="display:inline-block;">';
                html += '<input type="hidden" name="_token" value="' + csrfToken + '">';
                html += '<input type="hidden" name="_method" value="DELETE">';
                html += '<button type="submit" style="border: none; background: transparent; padding: 0; color: #f46a6a; font-weight: bold; line-height: 1;" title="Quitar sector"><i class="mdi mdi-close-circle font-size-16"></i></button>';
                html += '</form>';
                html += '</td>';
                html += '</tr>';
            });

            $('#modalZonaDetalleBody').html(html);
            $('#modalDetalleZonaAsignada').modal('show');
        });

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
                    actualizarSectoresDeZona();
                    await refrescarHistorial();
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
                    $('#modalDetalleZonaAsignada').modal('hide');
                    await refrescarHistorial();
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
