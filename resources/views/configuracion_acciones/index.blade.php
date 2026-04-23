@extends('layouts.main')

@section('title')
Configuración de Acciones
@endsection

@section('css')
<style>
    .module-item { cursor: pointer; transition: background 0.3s; }
    .module-item:hover { background-color: #f8f9fa; }
    .submodule-list { padding-left: 2rem; border-left: 2px solid #e9ecef; margin-left: 1rem; }
    .submodule-item { padding: 8px; border-bottom: 1px solid #f1f1f1; display: flex; justify-content: space-between; align-items: center; }
    .action-badge { margin-right: 5px; }
</style>
<link href="{{asset('assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('contenido')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Configurar Acciones por Módulo</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="accordion" id="accordionModules">
                        @foreach($parents as $parent)
                        <div class="accordion-item mb-2 border">
                            <h2 class="accordion-header" id="heading{{ $parent->id }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $parent->id }}" aria-expanded="false" aria-controls="collapse{{ $parent->id }}">
                                    <i class="{{ $parent->icon }} me-2 text-primary"></i> 
                                    <strong>{{ $parent->name }}</strong>
                                </button>
                            </h2>
                            <div id="collapse{{ $parent->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $parent->id }}" data-bs-parent="#accordionModules">
                                <div class="accordion-body bg-light bg-opacity-10">
                                    <div class="submodule-list">
                                        @php $hasSub = false; @endphp
                                        @foreach($submodules as $sub)
                                            @if($sub->padre_id == $parent->id)
                                                @php $hasSub = true; @endphp
                                                <div class="submodule-item">
                                                    <div>
                                                        <i class="{{ $sub->icon }} me-1 text-secondary small"></i>
                                                        {{ $sub->name }}
                                                    </div>
                                                    <button class="btn btn-outline-primary btn-sm" onclick="configurarAcciones({{ $sub->id }}, '{{ $sub->name }}')">
                                                        <i class="fas fa-cog"></i> Configurar
                                                    </button>
                                                </div>
                                            @endif
                                        @endforeach
                                        @if(!$hasSub)
                                            <p class="text-muted mb-0 small">No hay submódulos</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Configuración -->
<div class="modal fade" id="modalAcciones" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Acciones para: <span id="nombreSubmodulo" class="text-primary"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modulo_id">
                <p class="text-muted small mb-3">Selecciona las acciones permitidas para este módulo:</p>
                <div class="row">
                    @foreach($acciones as $accion)
                    <div class="col-md-6 mb-2">
                        <div class="form-check form-switch px-4 py-2 border rounded">
                            <input class="form-check-input check-accion" type="checkbox" id="accion_{{ $accion->id }}" value="{{ $accion->id }}">
                            <label class="form-check-label ms-2" for="accion_{{ $accion->id }}">
                                {{ $accion->nombre }}
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardar" onclick="guardarConfiguracion()">
                    <i class="fas fa-save me-1"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
const urlGeneral = $("#url_raiz_proyecto").val();

function configurarAcciones(moduloId, nombre) {
    $("#modulo_id").val(moduloId);
    $("#nombreSubmodulo").text(nombre);
    $(".check-accion").prop('checked', false);
    
    // Cargar asignaciones actuales
    $.get(urlGeneral + "/configuracion-acciones/getAssignments/" + moduloId, function(data) {
        data.forEach(accionId => {
            $("#accion_" + accionId).prop('checked', true);
        });
        $("#modalAcciones").modal("show");
    });
}

function guardarConfiguracion() {
    let moduloId = $("#modulo_id").val();
    let acciones = [];
    
    $(".check-accion:checked").each(function() {
        acciones.push($(this).val());
    });

    $("#btnGuardar").prop('disabled', true);
    
    $.post(urlGeneral + "/configuracion-acciones/save", {
        modulo_id: moduloId,
        acciones: acciones,
        _token: $('meta[name="csrf-token"]').attr('content')
    }, function(data) {
        Swal.fire("Éxito", "Configuración actualizada correctamente", "success");
        $("#modalAcciones").modal("hide");
    }).always(function() {
        $("#btnGuardar").prop('disabled', false);
    });
}
</script>
@endsection
