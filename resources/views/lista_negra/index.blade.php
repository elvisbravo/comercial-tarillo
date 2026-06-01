@extends('layouts.main')

@section('title', 'Lista Negra de Clientes')

@section('css')
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<!-- DataTables -->
<link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    div.dataTables_wrapper div.dataTables_paginate {
        display: flex !important;
        justify-content: flex-end !important;
    }
    .pagination { justify-content: flex-end !important; }
    .dataTables_filter input { border-radius: 8px !important; }
    .ln-container { font-family: 'Outfit', sans-serif; }
    .ln-card { border-radius: 14px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.05); background:#fff; }
    .ln-section-title { font-weight: 700; color: #1f2937; }
    .ln-table thead th {
        background: #1e293b; color: white; font-weight: 600;
        text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px;
        border: none; padding: 12px 14px;
    }
    .ln-table tbody td { padding: 12px 14px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
    .ln-table tbody tr:last-child td { border-bottom: none; }
    .ln-table tbody tr:hover { background: #f8fafc; }
    .badge-vencidas { background:#fee2e2; color:#b91c1c; border-radius:20px; padding:3px 10px; font-size:11px; font-weight:700; }
    .badge-atraso { background:#fef3c7; color:#92400e; border-radius:20px; padding:3px 10px; font-size:11px; font-weight:700; }
    .badge-deuda { background:#dbeafe; color:#1e40af; border-radius:20px; padding:3px 10px; font-size:11px; font-weight:700; }
    .badge-lista-negra { background:#7f1d1d; color:#fff; border-radius:20px; padding:3px 12px; font-size:11px; font-weight:700; }
    .severity-high { border-left: 4px solid #dc2626; }
    .severity-mid  { border-left: 4px solid #f59e0b; }
    .severity-low  { border-left: 4px solid #3b82f6; }
    .search-result { cursor: pointer; padding: 10px 14px; border-bottom: 1px solid #f1f5f9; }
    .search-result:hover { background: #f8fafc; }
    .search-result:last-child { border-bottom: none; }
    .empty-state { text-align:center; padding: 40px 20px; color:#94a3b8; }
    .empty-state i { font-size: 48px; display:block; margin-bottom: 10px; }
    .page-title-box h4 { font-weight: 700; color: #1f2937; letter-spacing: -0.5px; }
</style>
@endsection

@section('contenido')
<div class="container-fluid py-3 ln-container">

    {{-- Título --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-20 text-primary font-weight-bold">
                    <i class="mdi mdi-account-cancel-outline me-2"></i> Lista Negra de Clientes
                </h4>
            </div>
        </div>
    </div>

    {{-- Alertas --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius:10px;">
            <strong>¡Éxito!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius:10px;">
            <strong>¡Atención!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ===================== AGREGAR MANUALMENTE ===================== --}}
    <div class="card ln-card mb-4">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
            <h5 class="ln-section-title mb-0">
                <i class="mdi mdi-account-plus-outline me-1"></i> Agregar cliente manualmente
            </h5>
            <p class="text-muted font-size-12 mb-0">Busca por nombre, razón social o documento.</p>
        </div>
        <div class="card-body p-4">
            <div class="row g-2 align-items-end">
                <div class="col-md-10">
                    <label class="form-label font-weight-bold text-dark mb-1" style="font-size:12px;">Cliente</label>
                    <input type="text" id="buscar_cliente" class="form-control" placeholder="Escribe nombre, razón social o documento..." style="border-radius:8px;">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-light w-100" id="btn_limpiar_busqueda" title="Limpiar">
                        <i class="mdi mdi-refresh"></i>
                    </button>
                </div>
            </div>
            <div id="resultados_busqueda" class="mt-3" style="display:none; max-height: 300px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 10px;"></div>
        </div>
    </div>

    {{-- ===================== CLIENTES EN LISTA NEGRA ===================== --}}
    <div class="card ln-card mb-4">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="ln-section-title mb-0">
                    <i class="mdi mdi-account-cancel me-1 text-danger"></i> En lista negra
                </h5>
                <p class="text-muted font-size-12 mb-0">Clientes bloqueados actualmente para ventas a crédito.</p>
            </div>
            <span class="badge bg-soft-danger text-danger font-size-12 px-3 py-2">
                {{ count($enListaNegra) }} bloqueado(s)
            </span>
        </div>
        <div class="card-body p-4">
            @if(count($enListaNegra) > 0)
            <div class="table-responsive">
                <table class="table ln-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Documento</th>
                            <th>Celular</th>
                            <th>Motivo</th>
                            <th>Agregado por</th>
                            <th>Fecha</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($enListaNegra as $i => $ln)
                        <tr>
                            <td class="text-muted" style="font-size:12px;">{{ $i + 1 }}</td>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $ln->razon_social ?: trim(($ln->nomb_per ?? '').' '.($ln->pate_per ?? '').' '.($ln->mate_per ?? '')) }}</div>
                            </td>
                            <td>{{ $ln->documento ?: '—' }}</td>
                            <td>{{ $ln->telefono ?: '—' }}</td>
                            <td style="max-width: 260px;">
                                <div class="text-dark font-size-13">{{ \Illuminate\Support\Str::limit($ln->motivo, 80) }}</div>
                            </td>
                            <td>
                                <small class="text-muted">{{ $ln->agregado_por_nombre ?: '—' }}</small>
                            </td>
                            <td>
                                <small class="text-muted">{{ $ln->agregado_en ? \Carbon\Carbon::parse($ln->agregado_en)->format('d/m/Y H:i') : '—' }}</small>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-success btn-sm btn-quitar-ln"
                                        data-registro="{{ $ln->registro_id }}"
                                        data-cliente="{{ $ln->razon_social ?: trim(($ln->nomb_per ?? '').' '.($ln->pate_per ?? '').' '.($ln->mate_per ?? '')) }}"
                                        style="border-radius:8px; font-weight:600;">
                                    <i class="mdi mdi-account-check-outline me-1"></i> Quitar
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <i class="mdi mdi-account-check-outline"></i>
                <p>No hay clientes en la lista negra actualmente.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ===================== SUGERIDOS ===================== --}}
    <div class="card ln-card mb-4">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="ln-section-title mb-0">
                    <i class="mdi mdi-alert-decagram-outline me-1 text-warning"></i> Sugeridos para lista negra
                </h5>
                <p class="text-muted font-size-12 mb-0">Clientes con créditos activos que tienen cuotas vencidas.</p>
            </div>
            <span class="badge bg-soft-warning text-warning font-size-12 px-3 py-2">
                {{ count($sugeridos) }} candidato(s)
            </span>
        </div>
        <div class="card-body p-4">
            @if(count($sugeridos) > 0)
            <div class="table-responsive">
                <table id="tabla_sugeridos" class="table ln-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Documento</th>
                            <th class="text-center">Créditos activos</th>
                            <th class="text-center">Cuotas vencidas</th>
                            <th class="text-center">Máx. atraso (días)</th>
                            <th class="text-end">Deuda total</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sugeridos as $i => $s)
                            @php
                                $nombre = $s->razon_social ?: trim(($s->nomb_per ?? '').' '.($s->pate_per ?? '').' '.($s->mate_per ?? ''));
                                $severidad = $s->max_dias_atraso >= 60 || $s->cuotas_vencidas >= 4 ? 'severity-high' : ($s->max_dias_atraso >= 30 || $s->cuotas_vencidas >= 2 ? 'severity-mid' : 'severity-low');
                            @endphp
                            <tr class="{{ $severidad }}">
                                <td class="text-muted" style="font-size:12px;">{{ $i + 1 }}</td>
                                <td>
                                    <div class="font-weight-bold text-dark">{{ $nombre }}</div>
                                    @if($s->ultimo_vendedor)
                                        <small class="text-muted">Último vendedor: {{ $s->ultimo_vendedor }}</small>
                                    @endif
                                </td>
                                <td>{{ $s->documento ?: '—' }}</td>
                                <td class="text-center" data-order="{{ (int) $s->creditos_activos }}"><span class="badge-deuda">{{ $s->creditos_activos }}</span></td>
                                <td class="text-center" data-order="{{ (int) $s->cuotas_vencidas }}"><span class="badge-vencidas">{{ $s->cuotas_vencidas }}</span></td>
                                <td class="text-center" data-order="{{ (int) $s->max_dias_atraso }}"><span class="badge-atraso">{{ $s->max_dias_atraso }} d</span></td>
                                <td class="text-end font-weight-bold text-dark" data-order="{{ (float) $s->deuda_total }}">S/ {{ number_format($s->deuda_total, 2) }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm btn-agregar-sugerido"
                                            data-cliente-id="{{ $s->cliente_id }}"
                                            data-cliente="{{ $nombre }}"
                                            style="border-radius:8px; font-weight:600;">
                                        <i class="mdi mdi-account-cancel-outline me-1"></i> Bloquear
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <i class="mdi mdi-check-decagram-outline"></i>
                <p>No hay clientes sugeridos. Todos los créditos activos están al día.</p>
            </div>
            @endif
        </div>
    </div>

</div>

{{-- ===================== MODAL AGREGAR ===================== --}}
<div class="modal fade" id="modalAgregarLN" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #7f1d1d, #dc2626); border-radius: 16px 16px 0 0; padding: 20px 24px;">
                <div class="d-flex align-items-center gap-3 text-white">
                    <div style="width:40px; height:40px; background: rgba(255,255,255,0.15); border-radius: 10px; display:flex; align-items:center; justify-content:center;">
                        <i class="mdi mdi-account-cancel-outline" style="font-size:22px;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Agregar a lista negra</h5>
                        <small style="opacity:0.75;">El cliente no podrá recibir ventas a crédito</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <form id="form_agregar_ln" method="POST" action="{{ route('clientes.lista_negra.agregar') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-warning border-0 rounded-3 mb-3" style="background: #fef3c7;">
                        <i class="mdi mdi-information-outline me-2 text-warning"></i>
                        Vas a bloquear al cliente: <strong id="ln_cliente_nombre">—</strong>
                    </div>
                    <input type="hidden" name="cliente_id" id="ln_cliente_id">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Motivo <span class="text-danger">*</span></label>
                        <textarea name="motivo" id="ln_motivo" class="form-control" rows="3" required maxlength="500" placeholder="Describe el motivo por el cual se bloquea al cliente..." style="border-radius:8px;"></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label font-weight-bold">Notas adicionales</label>
                        <textarea name="notas" class="form-control" rows="2" maxlength="500" placeholder="Opcional: contexto, evidencia, etc." style="border-radius:8px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 d-flex gap-2 justify-content-end">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger" style="border-radius: 10px; font-weight: 600;">
                        <i class="mdi mdi-account-cancel-outline me-1"></i> Confirmar bloqueo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== MODAL QUITAR ===================== --}}
<div class="modal fade" id="modalQuitarLN" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #065f46, #10b981); border-radius: 16px 16px 0 0; padding: 20px 24px;">
                <div class="d-flex align-items-center gap-3 text-white">
                    <div style="width:40px; height:40px; background: rgba(255,255,255,0.15); border-radius: 10px; display:flex; align-items:center; justify-content:center;">
                        <i class="mdi mdi-account-check-outline" style="font-size:22px;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Quitar de lista negra</h5>
                        <small style="opacity:0.75;">El cliente podrá recibir ventas a crédito nuevamente</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <form id="form_quitar_ln" method="POST" action="">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-success border-0 rounded-3 mb-3" style="background:#d1fae5;">
                        <i class="mdi mdi-information-outline me-2 text-success"></i>
                        Vas a desbloquear al cliente: <strong id="ln_quitar_nombre">—</strong>
                    </div>
                    <div class="mb-2">
                        <label class="form-label font-weight-bold">Motivo de salida <span class="text-danger">*</span></label>
                        <textarea name="motivo_salida" class="form-control" rows="3" required maxlength="500" placeholder="¿Por qué se quita de la lista negra? (acuerdo de pago, error, etc.)" style="border-radius:8px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 d-flex gap-2 justify-content-end">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success" style="border-radius: 10px; font-weight: 600;">
                        <i class="mdi mdi-account-check-outline me-1"></i> Confirmar desbloqueo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Required datatable js -->
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

<script>
$(function () {
    $(".loader").fadeOut("slow");

    // Inicializar DataTable en la tabla de Sugeridos
    if ($('#tabla_sugeridos').length) {
        $('#tabla_sugeridos').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
            },
            order: [[4, 'desc']],     // Orden inicial: más cuotas vencidas arriba
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
            columnDefs: [
                { orderable: false, targets: [0, 7] }   // # y Acción no se ordenan
            ],
            stateSave: false
        });
    }

    const baseUrl = "{{ url('/') }}";
    const csrf = "{{ csrf_token() }}";

    // ============== BÚSQUEDA MANUAL ==============
    let searchTimer = null;
    $('#buscar_cliente').on('input', function () {
        clearTimeout(searchTimer);
        const term = $(this).val().trim();
        if (term.length < 2) {
            $('#resultados_busqueda').hide().html('');
            return;
        }
        searchTimer = setTimeout(function () {
            $.ajax({
                url: "{{ route('clientes.lista_negra.buscar') }}",
                type: 'GET',
                data: { q: term },
                success: function (data) {
                    if (!data || data.length === 0) {
                        $('#resultados_busqueda').html('<div class="search-result text-center text-muted">Sin resultados</div>').show();
                        return;
                    }
                    let html = '';
                    data.forEach(c => {
                        const nombre = c.razon_social || ((c.nomb_per || '') + ' ' + (c.pate_per || '') + ' ' + (c.mate_per || '')).trim() || '—';
                        html += `
                            <div class="search-result d-flex justify-content-between align-items-center" data-id="${c.id}" data-nombre="${nombre.replace(/"/g, '&quot;')}">
                                <div>
                                    <div class="font-weight-bold text-dark">${nombre}</div>
                                    <small class="text-muted">Doc: ${c.documento || '—'} · Tel: ${c.telefono || '—'}</small>
                                </div>
                                <button class="btn btn-sm btn-danger" style="border-radius:8px;">
                                    <i class="mdi mdi-account-cancel-outline me-1"></i> Bloquear
                                </button>
                            </div>`;
                    });
                    $('#resultados_busqueda').html(html).show();
                },
                error: function () {
                    Swal.fire({ icon:'error', title:'Error', text:'No se pudo realizar la búsqueda.' });
                }
            });
        }, 300);
    });

    // Click en resultado de búsqueda
    $(document).on('click', '.search-result', function () {
        const id = $(this).data('id');
        const nombre = $(this).data('nombre');
        $('#ln_cliente_id').val(id);
        $('#ln_cliente_nombre').text(nombre);
        $('#ln_motivo').val('');
        $('textarea[name="notas"]').val('');
        $('#resultados_busqueda').hide();
        $('#buscar_cliente').val('');
        $('#modalAgregarLN').modal('show');
    });

    $('#btn_limpiar_busqueda').on('click', function () {
        $('#buscar_cliente').val('');
        $('#resultados_busqueda').hide().html('');
    });

    // ============== AGREGAR DESDE SUGERIDOS ==============
    $(document).on('click', '.btn-agregar-sugerido', function () {
        const id = $(this).data('cliente-id');
        const nombre = $(this).data('cliente');
        $('#ln_cliente_id').val(id);
        $('#ln_cliente_nombre').text(nombre);
        $('#ln_motivo').val('Cliente con ' + $(this).closest('tr').find('.badge-vencidas').text() + ' cuota(s) vencida(s) en créditos activos.');
        $('textarea[name="notas"]').val('');
        $('#modalAgregarLN').modal('show');
    });

    // ============== QUITAR ==============
    $(document).on('click', '.btn-quitar-ln', function () {
        const id = $(this).data('registro');
        const nombre = $(this).data('cliente');
        $('#ln_quitar_nombre').text(nombre);
        $('#form_quitar_ln').attr('action', baseUrl + '/clientes/lista-negra/quitar/' + id);
        $('textarea[name="motivo_salida"]', '#form_quitar_ln').val('');
        $('#modalQuitarLN').modal('show');
    });
});
</script>
@endsection
