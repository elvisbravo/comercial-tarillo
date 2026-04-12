@extends('layouts.main')

@section('title')
    Carga de Inventario
@endsection

@section('css')
   <!-- Sweet Alert-->
   <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('contenido')

<nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <h4>Carga de Inventario</h4>
            </li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                @if ( $errors->any() )

                    <div class="alert alert-danger">
                        @foreach( $errors->all() as $error )<li>{{ $error }}</li>@endforeach
                    </div>
                    @endif

                    @if(isset($numRows))
                    <div class="badge badge-soft-success font-size-12">
                       <h4><strong>Se importaron {{$numRows}} registros.</strong></h4> 
                    </div>
                @endif
                  
             
                <form action="/cargainventario/import" method="POST" enctype="multipart/form-data">
                      @csrf
                    <div class="mb-3">
                        <label for="formFileMultiple" class="form-label">Seleccionar archivo para la carga de inventario</label>
                        <input type="file" name="file" class="form-control">
                    </div>
                    <input class="btn btn-primary" type="submit" value="Enviar">

                    </form>
                   
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    






@endsection

@section('js')

    <!-- Sweet Alerts js -->

    <!-- Required datatable js -->
    <!-- Sweet Alerts js -->
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('js/cargainventario.js') }}">
    </script>

@endsection