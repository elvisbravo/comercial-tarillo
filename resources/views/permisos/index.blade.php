@extends('layouts.main')

@section('title')
Gestión de Permisos
@endsection

@section('css')
<style>
    .role-item {
        cursor: pointer;
        transition: all 0.3s;
        padding: 12px 20px;
        border-bottom: 1px solid #f1f1f1;
    }

    .role-item:hover {
        background-color: #f8f9fa;
    }

    .role-item.active {
        background-color: #3b5de7;
        color: white;
        border-bottom-color: #3b5de7;
    }

    .module-card {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        margin-bottom: 20px;
        overflow: hidden;
    }

    .module-header {
        background-color: #f8f9fa;
        padding: 10px 15px;
        border-bottom: 1px solid #e9ecef;
    }

    .submodule-row {
        padding: 10px 15px;
        border-bottom: 1px solid #f1f1f1;
        display: flex;
        align-items: start;
        flex-direction: column;
    }

    .actions-container {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 8px;
        padding-left: 25px;
    }

    .perm-check {
        margin-bottom: 0;
        cursor: pointer;
    }

    #panel-permisos {
        display: none;
    }

    .ver-check-container {
        margin-bottom: 5px;
        font-weight: 600;
        border-bottom: 1px dashed #ddd;
        width: 100%;
        padding-bottom: 5px;
    }
</style>
<link href="{{asset('assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('contenido')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Asignación de Permisos por Rol</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Panel Izquierdo: Roles -->
        <div class="col-md-3">
            <div class="card overflow-hidden">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Seleccionar Rol</h5>
                </div>
                <div class="card-body p-0">
                    <div id="roles-list">
                        @foreach($roles as $role)
                        <div class="role-item" data-id="{{ $role->id }}" onclick="cargarPermisos({{ $role->id }}, '{{ $role->name }}', this)">
                            <i class="fas fa-user-tag me-2"></i> {{ $role->name }}
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Derecho: Módulos y Acciones -->
        <div class="col-md-9">
            <div id="placeholder-permisos" class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-shield-alt fa-4x text-light mb-3"></i>
                    <h5 class="text-muted">Selecciona un rol de la izquierda para configurar sus accesos</h5>
                </div>
            </div>

            <div id="panel-permisos">
                <div class="card mb-3 border-primary border-start border-4">
                    <div class="card-body py-2 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Permisos para: <span id="nombre-rol-seleccionado" class="text-primary font-weight-bold"></span></h5>
                        @if(App\Permisos::hasPermission('permisos', 2))
                        <button class="btn btn-primary" onclick="guardarPermisos()">
                            <i class="fas fa-save me-1"></i> Guardar Todo
                        </button>
                        @endif
                    </div>
                </div>

                <input type="hidden" id="rol_id_actual">

                @foreach($parents as $parent)
                <div class="module-card">
                    <div class="module-header d-flex justify-content-between align-items-center">
                        <div><i class="{{ $parent->icon }} me-1 text-primary"></i> <strong>{{ $parent->name }}</strong></div>
                    </div>
                    <div class="module-body bg-white">
                        @php $hasSubs = false; @endphp
                        @foreach($submodules as $sub)
                        @if($sub->padre_id == $parent->id)
                        @php $hasSubs = true; @endphp
                        <div class="submodule-row p-3">
                            <div class="d-flex align-items-center w-100 mb-2">
                                <div class="form-check perm-check me-3" style="min-width: 200px;">
                                    <input class="form-check-input check-permiso" type="checkbox"
                                        id="perm_v_{{ $sub->id }}"
                                        data-modulo="{{ $sub->id }}"
                                        data-accion="{{ $accion_ver->id }}"
                                        {{ !App\Permisos::hasPermission('permisos', 2) ? 'disabled' : '' }}>
                                    <label class="form-check-label text-dark fw-bold" for="perm_v_{{ $sub->id }}">
                                        <i class="{{ $sub->icon }} me-1 text-secondary small"></i>
                                        {{ $sub->name }} (Ver)
                                    </label>
                                </div>

                                <div class="actions-container d-flex gap-3 flex-wrap border-start ps-3">
                                    @foreach($sub->acciones_configuradas as $acc)
                                    {{-- Evitar mostrar "Ver" si ya está configurado como acción para no duplicar --}}
                                    @if(strtolower($acc->nombre) != 'ver')
                                    <div class="form-check perm-check">
                                        <input class="form-check-input check-permiso" type="checkbox"
                                            id="perm_{{ $sub->id }}_{{ $acc->id }}"
                                            data-modulo="{{ $sub->id }}"
                                            data-accion="{{ $acc->id }}" disabled>
                                        <label class="form-check-label small" for="perm_{{ $sub->id }}_{{ $acc->id }}">
                                            {{ $acc->nombre }}
                                        </label>
                                    </div>
                                    @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach

                        @if(!$hasSubs)
                        <div class="p-3 text-center text-muted small">Módulo sin submódulos configurados</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
    const urlGeneral = $("#url_raiz_proyecto").val();
    const idAccionVer = {{ $accion_ver->id }};
    const canCreate = {{ App\Permisos::hasPermission('permisos', 2) ? 'true' : 'false' }};

    $(document).on("change", `.check-permiso[data-accion='${idAccionVer}']`, function() {
        let moduloId = $(this).data('modulo');
        let isChecked = $(this).is(':checked');

        // Buscar todos los otros checks del mismo módulo
        let otherChecks = $(`.check-permiso[data-modulo="${moduloId}"][data-accion!="${idAccionVer}"]`);

        if (isChecked) {
            otherChecks.prop('disabled', false);
        } else {
            otherChecks.prop('disabled', true).prop('checked', false);
        }
    });

    function cargarPermisos(rolId, nombreRol, elemento) {
        // UI feedback
        $(".role-item").removeClass("active");
        $(elemento).addClass("active");
        $("#nombre-rol-seleccionado").text(nombreRol);
        $("#rol_id_actual").val(rolId);

        // Reset checks and disable secondary actions
        $(".check-permiso").prop('checked', false);
        $(`.check-permiso[data-accion!='${idAccionVer}']`).prop('disabled', true);

        // Fetch permissions
        $("#placeholder-permisos").hide();
        $("#panel-permisos").fadeIn();

        $.get(urlGeneral + "/permisos/getByRole/" + rolId, function(data) {
            data.forEach(p => {
                let selector = `.check-permiso[data-modulo="${p.modulo_id}"][data-accion="${p.accion_id}"]`;
                $(selector).prop('checked', true);

                // Si es el permiso de "Ver", habilitar los demás del mismo módulo
                if (p.accion_id == idAccionVer && canCreate) {
                    $(`.check-permiso[data-modulo="${p.modulo_id}"][data-accion!="${idAccionVer}"]`).prop('disabled', false);
                }
            });

            // Si no tiene permiso de crear, forzar que el Ver también se quede deshabilitado
            if (!canCreate) {
                $(".check-permiso").prop('disabled', true);
            }
        });
    }

    function guardarPermisos() {
        let rolId = $("#rol_id_actual").val();
        if (!rolId) return;

        let permisos = [];
        $(".check-permiso:checked").each(function() {
            permisos.push({
                modulo_id: $(this).data('modulo'),
                accion_id: $(this).data('accion')
            });
        });

        Swal.fire({
            title: 'Guardando permisos...',
            didOpen: () => {
                Swal.showLoading()
            },
            allowOutsideClick: false
        });

        $.post(urlGeneral + "/permisos/save", {
            rol_id: rolId,
            permisos: permisos,
            _token: $('meta[name="csrf-token"]').attr('content')
        }, function(data) {
            Swal.fire("Éxito", "Los permisos han sido actualizados correctamente", "success");
        });
    }
</script>
@endsection