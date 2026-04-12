@extends('layouts.main')

@section('title')
    traslado Detalle
@endsection

@section('contenido')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Traslado Detalle</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Traslado</a></li>
                    <li class="breadcrumb-item active">Traslado detalle</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="invoice-title">
                    <div class="d-flex align-items-start">
                        <div class="flex-grow-1">
                            <div class="mb-4">
                                <img src="{{ asset('assets/images/logo-sm.svg') }}" alt="" height="24"><span class="logo-txt">GUIA DE REMISIÓN: T001-12</span>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="mb-4">
                                <h4 class="float-end font-size-16">Traslado de Entrada # 12345</h4>
                            </div>
                        </div>
                    </div>
                    

                    <!--<p class="mb-1">1874 County Line Road City, FL 33566</p>
                    <p class="mb-1"><i class="mdi mdi-email align-middle mr-1"></i> abc@123.com</p>
                    <p><i class="mdi mdi-phone align-middle mr-1"></i> 012-345-6789</p>-->
                </div>
                <hr class="my-4">
                <div class="row">
                    <div class="col-sm-3">
                        <div>
                            <div>
                                <h5 class="font-size-15">Cliente:</h5>
                                <p>Sede Yurimaguas</p>
                            </div>
                            
                            <div class="mt-4">
                                <h5 class="font-size-15">Motivo Traslado:</h5>
                                <p class="mb-1">TRASLADO ENTRE ESTABLECIMIENTO DE LA EMPRESA</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div>
                            <div>
                                <h5 class="font-size-15">Modalidad de traslado:</h5>
                                <p>TRANSPORTE PRIVADO</p>
                            </div>
                            
                            <div class="mt-4">
                                <h5 class="font-size-15">Fecha de traslado:</h5>
                                <p class="mb-1">21 de Julio de 2022</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div>
                            <div>
                                <h5 class="font-size-15">Peso bruto:</h5>
                                <p>-</p>
                            </div>
                            
                            <div class="mt-4">
                                <h5 class="font-size-15">Número de bultos:</h5>
                                <p class="mb-1">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div>
                            <div>
                                <h5 class="font-size-15">Conductor:</h5>
                                <p>78973423 - conductor number 1</p>
                            </div>
                            
                            <div class="mt-4">
                                <h5 class="font-size-15">Placa del vehículo:</h5>
                                <p class="mb-1">NRO. PLACA: X20-SDAS</p>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row">

                    <div class="col-sm-3">
                        <div>
                            <div>
                                <h5 class="font-size-15">Dirección de Partida:</h5>
                                <p>calle Alfonso Ugarte 567</p>
                            </div>
                            
                            <div class="mt-4">
                                <h5 class="font-size-15">Ubigeo de Partida:</h5>
                                <p class="mb-1">Loreto - Alto Amazonas - Yurimaguas</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div>
                            <div>
                                <h5 class="font-size-15">Dirección de LLegada:</h5>
                                <p>jr fernando belaunde terry km 45</p>
                            </div>
                            
                            <div class="mt-4">
                                <h5 class="font-size-15">Ubigeo de LLegada:</h5>
                                <p class="mb-1">Loreto - Alto Amazonas - Yurimaguas</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div>
                            <div>
                                <h5 class="font-size-15">Almacen de Origen:</h5>
                                <p>Sede Yurimaguas</p>
                            </div>
                            
                            <div class="mt-4">
                                <h5 class="font-size-15">Sede de Destino:</h5>
                                <p class="mb-1">Sede de Pampa Hermosa</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div>
                            <div>
                                <h5 class="font-size-15">Documento de Referencia:</h5>
                                <p>-</p>
                            </div>
                            
                        </div>
                    </div>

                </div>

                <hr class="my-4">

                <div class="py-2 mt-3">
                    <h5 class="font-size-15">Resumen del Traslado</h5>
                </div>
                <div class="p-4 border rounded">
                    <div class="table-responsive">
                        <table class="table table-nowrap align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 70px;">No.</th>
                                    <th>Producto</th>
                                    <th>Unidad de Medida</th>
                                    <th>Cantidad</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">01</th>
                                    <td>
                                        <h5 class="font-size-15 mb-1">Silla</h5>
                                        <p class="font-size-13 text-muted mb-0">marca: , modelo: </p>
                                    </td>
                                    <td>UND</td>
                                    <th>10</th>
                                    <th>
                                        <div class="form-check font-size-16">
                                            <input type="checkbox" name="check" class="form-check-input" id="checkAll">
                                            <label class="form-check-label" for="checkAll"></label>
                                        </div>
                                    </th>
                                </tr>
                                
                                <tr>
                                    <th scope="row">01</th>
                                    <td>
                                        <h5 class="font-size-15 mb-1">Silla</h5>
                                        <p class="font-size-13 text-muted mb-0">marca: , modelo: </p>
                                    </td>
                                    <td>UND</td>
                                    <th>10</th>
                                    <th>
                                        <div class="form-check font-size-16">
                                            <input type="checkbox" name="check" class="form-check-input" id="checkAll">
                                            <label class="form-check-label" for="checkAll"></label>
                                        </div>
                                    </th>
                                </tr>

                                
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-print-none mt-3">
                    <div class="float-end">
                        <a href="#" class="btn btn-primary w-md waves-effect waves-light">Aceptar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection