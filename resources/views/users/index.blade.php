@extends('layouts.main')


@section('title')
Usuarios
@endsection

@section('css')
<!-- Sweet Alert-->
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

<!-- DataTables -->
<link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

<style>
    div.dataTables_wrapper div.dataTables_paginate {
        display: flex !important;
        justify-content: flex-end !important;
    }

    .pagination {
        justify-content: flex-end !important;
    }
</style>
@endsection
@section('contenido')




<div class="container-fluid">
    <!-- start page title -->
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Usuario</a></li>

        </ol>
    </nav>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    @if(App\Permisos::hasPermission('users', 2))
                    <a href="{{ route('users.create') }}" class="btn btn-success" id="btnadd"><i class="bx bx-plus label-icon"></i> Agregar</a>
                    @else
                    <div></div>
                    @endif

                </div>
                <div class="card-body">

                    <div class="table-responsive">

                        <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Roles</th>
                                    <th>Img</th>
                                    <th width="280px">Action</th>
                                </tr>
                            </thead>


                            <tbody id="serviciosdatos">
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div> <!-- end col -->
    </div>

    <!-- modal -->


</div>












@endsection

@section('js')

<!-- Sweet Alerts js -->
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/utils.js') }}"></script>

<script>
    const urlGeneral = $("#url_raiz_proyecto").val();
    const canEdit = {{ App\Permisos::hasPermission('users', 3) ? 'true' : 'false' }};
    const canDelete = {{ App\Permisos::hasPermission('users', 4) ? 'true' : 'false' }};
</script>

<script src="{{asset('js/usuarios.js')}}"></script>

<script>
    function eliminarUsuario(id) {
        Swal.fire({
            title: '¿Está seguro?',
            text: "El usuario será dado de baja del sistema.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, dar de baja',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                let csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                $.ajax({
                    type: "POST",
                    url: urlGeneral + "/users/" + id,
                    data: { _method: "delete", _token: csrfToken },
                    success: function (data) {
                        getUsersList();
                        Swal.fire(
                            "Eliminado!",
                            "Usuario eliminado correctamente.",
                            "success"
                        );
                    }
                });
            }
        });
    }
</script>

@endsection