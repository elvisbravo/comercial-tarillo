@extends('layouts.main')
@section('title')
  Roles de Usuarios
@endsection

@section('css')
<!-- Sweet Alert-->
<link href="{{asset('assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />

<!-- DataTables -->
<link href="{{asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('contenido')


<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Roles</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Roles</a></li>
                            <li class="breadcrumb-item active">Listado</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <a href="{{ route('roles.create') }}" class="btn btn-success waves-effect btn-label waves-light" id="btnadd"><i class="bx bx-plus label-icon"></i> Agregar</a>
                    </div>
                    <div class="card-body">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="table-responsive">

                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>

                                <th width="280px">Action</th>
                            </tr>
                            </thead>


                            <tbody id="serviciosdatos">
                            @foreach ($roles as $key => $role)
                            <tr>
                             <td>{{ ++$i }}</td>
                             <td>{{ $role->name }}</td>
                             <td>
                                <a class="btn btn-info" href="{{ route('roles.show',$role->id) }}">Show</a>

                                        <a class="btn btn-primary" href="{{ route('roles.edit',$role->id) }}">Edit</a>


                                        {!! Form::open(['method' => 'DELETE','route' => ['roles.destroy', $role->id],'style'=>'display:inline']) !!}
                                            {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                                        {!! Form::close() !!}

                           </td>

                            </tr>
                            @endforeach


                            </tbody>
                        </table>

                        {!! $roles->render() !!}
                   </div>



                    </div>
                </div>
            </div> <!-- end col -->
        </div>



    </div>
</div>









@endsection
