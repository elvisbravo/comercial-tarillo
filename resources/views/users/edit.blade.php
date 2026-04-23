@extends('layouts.main')


@section('title')
  Editar Usuario
@endsection

@section('css')
<!-- Sweet Alert-->
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

<!-- DataTables -->
<link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('contenido')


    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Editar Usuario</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                           
                            <li class="breadcrumb-item active">Detalle</li>
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
                        <h5 class="card-title mb-0">Información del Usuario a Editar</h5>
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

                    {!! Form::model($user, ['method' => 'PATCH','route' => ['users.update', $user->id],'autocomplete'=>'off','files'=>'true', 'class' => 'needs-validation', 'novalidate']) !!}
                    <input type="hidden" value="{{$user->id}}" name="id">

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
                                <label class="form-label fw-semibold">Nueva Contraseña (Opcional)</label>
                                {!! Form::password('password', array('placeholder' => 'Dejar en blanco para mantener la actual','class' => 'form-control')) !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Confirmar Nueva Contraseña</label>
                                {!! Form::password('confirm-password', array('placeholder' => 'Vuelva a escribir la nueva contraseña','class' => 'form-control')) !!}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Rol del Usuario <span class="text-danger">*</span></label>
                                {!! Form::select('roles[]', $roles,$userRole, array('class' => 'form-select', 'id' => 'roles_select', 'required', 'placeholder' => '--Seleccionar Rol--')) !!}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Sede Asignada <span class="text-danger">*</span></label>
                                <select name="sede_id" id="sede_id" class="form-select" required>
                                    <option value="" disabled>--Seleccionar Sede--</option>
                                    @foreach( $sedes as $sede)
                                        @if($sede->id==$user->sede_id)
                                            <option value="{{$sede->id}}" selected>{{$sede->nombre}}</option>
                                        @else
                                            <option value="{{$sede->id}}">{{$sede->nombre}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Foto de Perfil</label>
                                <input type="file" class="form-control" name="img" id="img" accept="image/*">
                            </div>
                            @if($user->img)
                            <div class="mt-2">
                                <p class="text-muted mb-1 font-size-13">Foto actual:</p>
                                <img src="{{asset('perfil/'.$user->img)}}" alt="Foto de perfil" class="img-thumbnail shadow-sm rounded" style="max-width: 150px; height: auto;">
                            </div>
                            @endif
                        </div>
                    </div>

                    <hr class="mt-4 mb-4">
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                            <i class="bx bx-save font-size-16 align-middle me-2"></i> Guardar Cambios
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
    const rolSelect  = document.getElementById('roles_select');
    const sedeSelect = document.getElementById('sede_id');

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

    // Ejecutar al cargar para respetar el rol actual del usuario
    filtrarSedePorRol();

    // Ejecutar cada vez que cambia el rol
    rolSelect.addEventListener('change', filtrarSedePorRol);
});
</script>
@endsection