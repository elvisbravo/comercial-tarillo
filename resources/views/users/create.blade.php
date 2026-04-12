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
                            <div class="card-header">

                            <a href="{{ url('users') }}" class="btn btn-success waves-effect btn-label waves-light" id="btnadd"><i class="dripicons-reply-all"></i> Atras</a>
                         
                                </p>
                            </div>

                            <div class="card-body">
                             <div class="row">

                                @if (count($errors) > 0)
                                        <div class="alert alert-danger">
                                            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                            <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    {!! Form::open(array('route' => 'users.store','method'=>'POST','autocomplete'=>'off','files'=>'true')) !!}
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Nombre:</strong>
                                            {!! Form::text('name', null, array('placeholder' => 'Nombre','class' => 'form-control')) !!}
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Email:</strong>
                                            {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control')) !!}
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Password:</strong>
                                            {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control')) !!}
                                        </div>
                                     </div>

                                     <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Confirm Password:</strong>
                                            {!! Form::password('confirm-password', array('placeholder' => 'Confirm Password','class' => 'form-control')) !!}
                                        </div>
                                     </div>

                                     <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Role:</strong>
                                            {!! Form::select('roles[]', $roles,[], array('class' => 'form-control','multiple')) !!}
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Seleccione la Sede</strong>
                                            <select name="sede_id" id="" class="form-control">
                                                <option value="">--Seleccionar--</option>
                                                @foreach( $sedes as $sede)
                                                 <option value="{{$sede->id}}">{{$sede->nombre}}</option>
                                                 @endforeach
                                            </select>

                                        </div>

                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Foto:</strong>
                                    <input type="file" class="form-control" name='img' id='img'>
                                </div>

                                
                            </div>
                                    <br>
                                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>




                                    {!! Form::close() !!}



                             
                            </div>
                            
                        
                           </div>

                        
                        </div>


                    </div>
                </div>













    </div>










@endsection