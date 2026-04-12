@extends('layouts.main')


@section('title')
  Usuarios
@endsection

@section('css')
<!-- Sweet Alert-->


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
                    <div class="card-header">
                        <a href="{{ route('users.create') }}" class="btn btn-success" id="btnadd"><i class="bx bx-plus label-icon"></i> Agregar</a>
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
                                            {!! Form::open(['method' => 'DELETE','route' => ['users.destroy', $user->id],'style'=>'display:inline']) !!}
                                                {!! Form::submit('Eliminar', ['class' => 'btn btn-danger','']) !!}
                                            {!! Form::close() !!}
                                    </td>


                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                   </div>
                   {!! $datos->render() !!}


                    </div>
                </div>
            </div> <!-- end col -->
        </div>

        <!-- modal -->


    </div>












@endsection

@section('js')

<!-- Sweet Alerts js -->

<!-- Required datatable js -->
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

<script src="{{asset('js/usuarios.js')}}"></script>

@endsection
