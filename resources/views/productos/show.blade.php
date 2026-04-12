@extends('layouts.main')

@section('title')
    Detalle Productos
@endsection

@section('css')
   <!-- Sweet Alert-->
<link href="{{asset('js/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />

<!-- DataTables -->
<link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('contenido')

<div class="loader" style="position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url('{{asset('img/loader-meta.gif')}}') 50% 50% no-repeat rgb(249,249,249);
        opacity: .8;">

        <div class="col-md-12" id="myDIV">
            <div class="panel panel-default">
                <div class="panel-heading"></div>
                <div class="panel-body loader-demo" style="margin-top:200px;">
                    <h1 style="color: #186A3B;font-family: 'Jomhuria', cursive;text-align:center"></h1>
                    <div class="ball-pulse">
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="container-fluid">

                <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0 font-size-18">Detalle Productos</h4>

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

                            <a href="{{url('productos')}}" type="button" class="btn btn-primary waves-effect btn-label waves-light"  ><i class="dripicons-reply-all"></i> Atras</a>
                            
                            </div>

                            <div class="card-body">
                         
                            
                            
                       

                            </div>
                        </div>
                    </div>
                </div>



    
    </div>








@endsection

@section('js')

    <!-- Sweet Alerts js -->

    <!-- Required datatable js -->
    <!-- Sweet Alerts js -->
    <script src="{{asset('js/sweetalert2.min.js')}}"></script>
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('js/productos.js') }}">
    </script>

@endsection
