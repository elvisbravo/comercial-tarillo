@extends('layouts.main')

@section('title')
Clientes
@endsection

@section('css')
<!-- Sweet Alert-->
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

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

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Listado Clientes</h4>

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


                    <a href="{{url('clientes/create')}}" class="btn btn-primary "> <i class="btn-icon-prepend" data-feather="plus"></i> Nuevo</a>

                </div>

                <div class="card-body">

                    <!-- Static Backdrop modal Button -->
                    <i data-feather="star"></i>

                    <div class="table-responsive">
                        <form method="GET" action="{{ route('clientes.index') }}">
                            <div class="row mb-2">
                                <div class="col-md-6 ms-auto d-flex">
                                    <input type="text" name="buscar" class="form-control me-2"
                                        placeholder="Buscar cliente..."
                                        value="{{ $buscar }}">
                                    <button class="btn btn-primary">Buscar</button>
                                </div>
                            </div>
                        </form>
                        <table id="datatables" class="table table-bordered dt-responsive  nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Razon Social</th>
                                    <th>Tipo Documento</th>
                                    <th>N° Documento</th>
                                    <th>Dirección Fiscal</th>
                                    <th>Telefono</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>


                            <tbody>
                                <?php $i = 1;   ?>
                                @foreach($clientes as $c)
                                <tr>
                                    <td style='padding:1px;text-align:center'><?php echo $i++   ?></td>
                                    <td style='padding:1px;text-align:center'>{{$c->razon_social}}</td>
                                    <td style='padding:1px;text-align:center'>{{$c->tipo_doc}}</td>
                                    <td style='padding:1px;text-align:center'>{{$c->documento}}</td>
                                    <td style='padding:1px;text-align:center'>{{$c->dire_per}}</td>
                                    <td style='padding:1px;text-align:center'>{{$c->telefono}}</td>
                                    @if($c->estado_per==1)
                                    <td style='padding:1px;text-align:center'>Activo</td>
                                    @else
                                    <td style='padding:1px;text-align:center'>Inactivo</td>
                                    @endif
                                    <td style='padding:1px;text-align:center'>


                                        <a type="button" href="{{ route('clientes.edit',$c->id) }}" class="btn btn-info"><i class="fas fa-edit"></i> </a>
                                        @if($c->estado_per==1)
                                        <button type="button" onclick="anular('{{$c->id}}');" class="btn btn-danger eliminar"><i class="fas fa-trash-alt  eliminar"></i> <input type="hidden" value="'{{$c->id}}'"> </button>
                                        @else
                                        <button type="button" onclick="activar('{{$c->id}}');" class="btn btn-warning activar"><i class="fas fa-sync activar"></i> </button>
                                        @endif

                                    </td>

                                </tr>
                                @endforeach

                            </tbody>
                        </table>

                        <div class="mt-3">
                            {{ $clientes->appends(['buscar' => $buscar])->links() }}
                        </div>

                    </div>
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
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Required datatable js -->
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

<script src="{{ asset('js/clientes.js') }}">
</script>

@endsection