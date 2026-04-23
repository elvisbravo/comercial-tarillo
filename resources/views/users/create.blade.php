@extends('layouts.main')
@section('contenido')

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Nuevo Usuario</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">

                    </ol>
                </div>

            </div>
        </div>
    </div>

    <!-- end page title -->
    <div class="row">
        <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center bg-transparent border-bottom">
                                <h5 class="card-title mb-0">Información del Nuevo Usuario</h5>
                                <a href="{{ url('users') }}" class="btn btn-secondary btn-sm waves-effect btn-label waves-light">
                                    <i class="bx bx-arrow-back label-icon"></i> Atras
                                </a>
                            </div>

                            <div class="card-body">
                                @if (count($errors) > 0)
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong>¡Ups!</strong> Hubo algunos problemas con su entrada.<br><br>
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                {!! Form::open(array('route' => 'users.store','method'=>'POST','autocomplete'=>'off','files'=>'true', 'class' => 'needs-validation', 'novalidate')) !!}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Nombre Completos <span class="text-danger">*</span></label>
                                            {!! Form::text('name', null, array('placeholder' => 'Ej: Juan Pérez','class' => 'form-control', 'required')) !!}
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Correo Electrónico <span class="text-danger">*</span></label>
                                            {!! Form::email('email', null, array('placeholder' => 'ejemplo@correo.com','class' => 'form-control', 'required')) !!}
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Contraseña <span class="text-danger">*</span></label>
                                            {!! Form::password('password', array('placeholder' => 'Ingrese contraseña segura','class' => 'form-control', 'required')) !!}
                                        </div>
                                     </div>

                                     <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Confirmar Contraseña <span class="text-danger">*</span></label>
                                            {!! Form::password('confirm-password', array('placeholder' => 'Vuelva a escribir la contraseña','class' => 'form-control', 'required')) !!}
                                        </div>
                                     </div>

                                     <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Rol del Usuario <span class="text-danger">*</span></label>
                                            {!! Form::select('roles[]', $roles, null, array('class' => 'form-select', 'id' => 'roles_select', 'required', 'placeholder' => '--Seleccionar Rol--')) !!}
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Sede Asignada <span class="text-danger">*</span></label>
                                            <select name="sede_id" id="sede_id" class="form-select select2" required>
                                                <option value="" disabled selected>--Seleccionar Sede--</option>
                                                @foreach( $sedes as $sede)
                                                 <option value="{{$sede->id}}">{{$sede->nombre}}</option>
                                                 @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Foto de Perfil (Opcional)</label>
                                            <input type="file" class="form-control" name="img" id="img" accept="image/*">
                                        </div>
                                    </div>
                                </div>
                                
                                <hr class="mt-4 mb-4">
                                
                                <div class="text-end">
                                    <button type="reset" class="btn btn-light waves-effect me-2">Limpiar Campos</button>
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                                        <i class="bx bx-save font-size-16 align-middle me-2"></i> Guardar Usuario
                                    </button>
                                </div>
                                {!! Form::close() !!}
                            </div>
                           </div>


            </div>
    </div>













</div>










<script>
document.addEventListener('DOMContentLoaded', function () {
    const rolSelect   = document.getElementById('roles_select');
    const sedeSelect  = document.getElementById('sede_id');

    function filtrarSedePorRol() {
        const rolElegido = rolSelect.value.toLowerCase();
        const esAdmin    = rolElegido === 'administrador';

        Array.from(sedeSelect.options).forEach(function (opt) {
            if (opt.text.trim().toUpperCase() === 'TODOS') {
                opt.hidden   = !esAdmin;
                opt.disabled = !esAdmin;
                // Si estaba seleccionada y ya no está disponible, limpiar
                if (!esAdmin && opt.selected) {
                    sedeSelect.value = '';
                }
            }
        });
    }

    // Ejecutar al cargar para el estado inicial
    filtrarSedePorRol();

    // Ejecutar cada vez que cambia el rol
    rolSelect.addEventListener('change', filtrarSedePorRol);
});
</script>
@endsection