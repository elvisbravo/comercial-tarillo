@extends('layouts.main')

@section('contenido')

            <div class="container-fluid">

             <!-- start page title -->
             <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h3 class="mb-sm-0 font-size-18" style="color:#117A65 ">{{ $sedeDelUsuario->nombre}}   </h3>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                            <li class="breadcrumb-item active">Dashboard</li>
                                        </ol>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-xl-3 col-md-6">
                                <!-- card -->
                                <div class="card card-h-100">
                                    <!-- card body -->
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Ventas Contado</span>
                                                <h4 class="mb-3">
                                                    S/ <span class="counter-value" data-target="{{ $totalVentasContado }}">0</span>
                                                </h4>
                                            </div>

                                            <div class="col-6">
                                                <div id="mini-chart1" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
                                            </div>
                                        </div>
                                        <div class="text-nowrap">
                                            <span class="badge bg-soft-success text-success">+S {{ $totalVentasContado }}</span>
                                            <span class="ms-1 text-muted font-size-13">Total Ventas Contado</span>
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->
                            </div><!-- end col -->

                            <div class="col-xl-3 col-md-6">
                                <!-- card -->
                                <div class="card card-h-100">
                                    <!-- card body -->
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Ventas Credito</span>
                                                <h4 class="mb-3">
                                                    S/ <span class="counter-value" data-target="{{ $totalVentasCredito }}">0</span>
                                                </h4>
                                            </div>
                                            <div class="col-6">
                                                <div id="mini-chart2" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
                                            </div>
                                        </div>
                                        <div class="text-nowrap">
                                            <span class="badge bg-soft-danger text-danger">{{ $totalVentasCredito }}</span>
                                            <span class="ms-1 text-muted font-size-13">Total Ventas Credito</span>
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->
                            </div><!-- end col-->

                            <div class="col-xl-3 col-md-6">
                                <!-- card -->
                                <div class="card card-h-100">
                                    <!-- card body -->
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-muted mb-3 lh-1 d-block text-truncate">Monto Total Credito Activo</span>
                                                <h4 class="mb-3">
                                                    S/ <span class="counter-value" data-target="{{ $totalCreditoActivo }}">0</span>
                                                </h4>
                                            </div>
                                            <div class="col-6">
                                                <div id="mini-chart3" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
                                            </div>
                                        </div>
                                        <div class="text-nowrap">
                                            <span class="badge bg-soft-success text-success">+ {{ $totalCreditoActivo }}</span>
                                            <span class="ms-1 text-muted font-size-13">Monto Total Credito Activo</span>
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->
                            </div><!-- end col -->

                            <div class="col-xl-3 col-md-6">
                                <!-- card -->
                                <div class="card card-h-100">
                                    <!-- card body -->
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-muted mb-3 lh-1 d-block text-truncate">Monto total Amortizado</span>
                                                <h4 class="mb-3">
                                                    S/   <span class="counter-value" data-target="{{ $totalamortizado }}">0</span>
                                                </h4>
                                            </div>
                                            <div class="col-6">
                                                <div id="mini-chart4" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
                                            </div>
                                        </div>
                                        <div class="text-nowrap">
                                            <span class="badge bg-soft-success text-success">+{{ $totalamortizado }}</span>
                                            <span class="ms-1 text-muted font-size-13">Monto total Amortizado</span>
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->
                            </div><!-- end col -->
                        </div><!-- end row-->

                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">Echarts</h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Charts</a></li>
                                            <li class="breadcrumb-item active">Echarts</li>
                                        </ol>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title mb-0">Total de Ventas - Año {{ $anioActual }}</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="line-chart" data-colors='["#2ab57d", "#ccc"]' class="e-charts"></div>
                                    </div>
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->
                            <div class="col-xl-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title mb-0">Total Ventas Credito y Contado - Año {{ $anioActual }}</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="mix-line-bar" data-colors='["#2ab57d", "#5156be", "#fd625e"]' class="e-charts"></div>
                                    </div>
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->
                        </div>

                        <div class="row">
                            <div class="col-xl-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title mb-0">Top 5 de los Productos Más Vendidos</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="doughnut-chart" data-colors='["#5156be", "#ffbf53", "#fd625e", "#4ba6ef", "#2ab57d"]' class="e-charts"></div>
                                    </div>
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->
                            <div class="col-xl-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title mb-0">Top 5 de los Productos Más Vendidos</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="pie-chart" data-colors='["#fd625e", "#2ab57d", "#4ba6ef", "#ffbf53", "#5156be"]' class="e-charts"></div>
                                    </div>
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->
                        </div>

                        <div class="row">

                            <div class="col-xl-6">
                                <div class="card">
                                    <div class="card-header align-items-center d-flex">
                                        <h4 class="card-title mb-0 flex-grow-1">Transaciones</h4>
                                        <div class="flex-shrink-0">
                                            <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs" role="tablist">

                                                <li class="nav-item">
                                                    <a class="nav-link active" data-bs-toggle="tab" href="#transactions-all-tab" role="tab">
                                                        Top 10 Clientes Más Deudores
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-bs-toggle="tab" href="#transactions-buy-tab" role="tab">
                                                        Top 10 de los Mejores Clientes
                                                    </a>
                                                </li>

                                            </ul>
                                            <!-- end nav tabs -->
                                        </div>
                                    </div><!-- end card header -->

                                    <div class="card-body px-0">
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="transactions-all-tab" role="tabpanel">
                                                <div class="table-responsive px-3" data-simplebar style="max-height: 352px;">

                                                    <table class="table align-middle table-nowrap table-borderless">
                                                        <tbody id="clientes_morosos">



                                                        </tbody>
                                                    </table>



                                                </div>
                                            </div>

                                            <div class="tab-pane" id="transactions-buy-tab" role="tabpanel">
                                                <div class="table-responsive px-3" data-simplebar style="max-height: 352px;">
                                                    <table class="table align-middle table-nowrap table-borderless">
                                                        <tbody id="clientes_buen_pagadores">

                                                        </tbody>
                                                    </table>


                                                </div>
                                            </div>


                                        </div>
                                    </div>















                                </div>

                            </div>

                            <div class="col-xl-6">

                                <div class="card">
                                    <div class="card-header align-items-center d-flex">
                                        <h4 class="card-title mb-0 flex-grow-1">Lista de los Mejores Cobradores</h4>
                                    </div>

                                    <div class="card-body px-0">
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="transactions-all-tab" role="tabpanel">
                                                <div class="table-responsive px-3" data-simplebar style="max-height: 352px;">

                                                    <table class="table align-middle table-nowrap table-borderless">
                                                        <tbody id="vendedores">



                                                        </tbody>
                                                    </table>



                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>



                        </div>





            </div>

@endsection

@section('js')

<script src="{{ asset('assets/libs/echarts/echarts.min.js') }}"></script>

<script src="{{ asset('assets/js/pages/echarts.init.js') }}"></script>


@endsection
