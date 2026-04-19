@extends('layouts.main')


@section('title')
  Usuarios
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
                        <a href="{{ route('users.create') }}" class="btn btn-success" id="btnadd"><i class="bx bx-plus label-icon"></i> Agregar</a>
                        <form action="{{ route('users.index') }}" method="GET" class="d-flex" autocomplete="off" style="max-width: 300px; width: 100%;">
                            <div class="input-group">
                                <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre..." value="{{ $buscar ?? '' }}">
                                <button type="submit" class="btn btn-primary"><i class="bx bx-search-alt"></i></button>
                            </div>
                        </form>
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
                            @foreach ($datos as $key => $user)
                                <tr>
                                  <td>{{ ++$i }}</td>
                                  <td>{{ $user->name }}</td>
                                  <td>{{ $user->email }}</td>
                                  <td>
                                    @if(!empty($user->getRoleNames()))
                                        @foreach($user->getRoleNames() as $v)
                                        <label class="btn btn-success">{{ $v }}</label>
                                        @endforeach
                                    @endif
                                    </td>
                                    <td>

                                      <img src="{{asset('perfil/'.$user->img)}}" alt="" class="img-thumbnail" width='50px'>
                                    </td>

                                    <td>
                                        <a class="btn btn-info" href="{{ route('users.show',$user->id) }}"><i class="bx bxs-show label-icon"></i> Ver</a>
                                        <a class="btn btn-primary" href="{{ route('users.edit',$user->id) }}"><i class="bx bx-pencil label-icon"></i> Editar</a>
                                            {!! Form::open(['method' => 'DELETE','route' => ['users.destroy', $user->id],'style'=>'display:inline', 'class' => 'form-eliminar-usuario']) !!}
                                                {!! Form::submit('Eliminar', ['class' => 'btn btn-danger','']) !!}
                                            {!! Form::close() !!}
                                    </td>


                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                   </div>

                   <div class="d-flex justify-content-end mt-3">
                       {!! $datos->render() !!}
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

<!-- Required datatable js -->
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

<script src="{{asset('js/usuarios.js')}}"></script>

<script>
    $('.form-eliminar-usuario').submit(function(e){
        e.preventDefault();
        
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
                this.submit();
            }
        });
    });
</script>

@endsection
