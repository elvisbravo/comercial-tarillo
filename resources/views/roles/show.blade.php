@extends('layouts.main')


@section('title')
  Show
@endsection

@section('style')
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
                    <h4 class="mb-sm-0 font-size-18">Detalle permisos Usuario</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Usuarios</a></li>
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
                    <div class="card-header">
                        <a href="{{ url('roles') }}" class="btn btn-success waves-effect btn-label waves-light" id="btnadd"><i class="bx bx-plus label-icon"></i> Atras</a>
                    </div>
                    <div class="card-body">
                    <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Name:</strong>
                                    {{ $role->name }}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Permissions:</strong>
                                    @if(!empty($rolePermissions))
                                        @foreach($rolePermissions as $v)
                                            <label class="label label-success">{{ $v->name }},</label>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

      </div>
</div>











@endsection
