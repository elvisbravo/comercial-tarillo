@extends('layouts.main')
@section('contenido')


    <div class="page-content">

    <div class="container-fluid">

                <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">Nuevo Usuario</h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="{{url('users')}}">Usuario</a></li>
                                            <li class="breadcrumb-item active">Crear</li>
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

                                <a href="{{url('roles')}}" type="button" class="btn btn-success waves-effect btn-label waves-light" ><i class="bx bx-plus label-icon"></i> Atras</a>

                                </p>
                            </div>

                            <div class="card-body">
                             <div class="row">

                                @if (count($errors) > 0)
                                        <div class="alert alert-danger">
                                            <strong>Whoops!</strong> Datos Obligatorios.<br><br>
                                            <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    {!! Form::model($role, ['method' => 'PATCH','route' => ['roles.update', $role->id]]) !!}

                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <strong>Name:</strong>
                                                {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <strong>Permission:</strong>
                                                <br/>
                                                @foreach($permission as $value)
                                                    <label>{{ Form::checkbox('permission[]', $value->id, in_array($value->id, $rolePermissions) ? true : false, array('class' => 'name')) }}
                                                    {{ $value->name }}</label>
                                                <br/>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div>



                                    {!! Form::close() !!}




                            </div>


                           </div>


                        </div>


                    </div>
                </div>





    </div>
    </div>









@endsection
