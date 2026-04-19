@extends('layouts.main')

@section('title')
Crear Amortizaciones
@endsection

@section('css')
<!-- Sweet Alert-->
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<!-- DataTables -->
<link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('contenido')

<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Amortizacion Cuotas</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">

                    </ol>
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-xs-12">
            <div class="card">

                <div class="card-body">

                    <i data-feather="star"></i>
                    <div class="row">


                        <div class="col-lg-6 col-xs-12">
                            <label for="">Clientes</label>
                            <form class="search-form">
                                <div class="input-group">

                                    <input type="text" disabled class="form-control" id="navbarForm" value="{{$credito->razon_social}}" placeholder="Cliente...">
                                    <input type="hidden" id="cliente_id" value="{{$credito->codigo}}">
                                    <input type="hidden" id="credito_id" value="{{$credito->id}}">
                                </div>
                            </form>

                        </div>
                        <div class="col-lg-6 col-xs-12">
                            <label for="">Documento</label>
                            <input type="text" class="form-control" id="documento" disabled value="{{$credito->documento}}">
                        </div>

                        <div class="col-lg-6 col-xs-12">
                            <label for="">Dirección</label>
                            <input type="text" disabled class="form-control" id="dire_per" value="{{$credito->dire_per}}">
                        </div>
                        <div class="col-lg-6 col-xs-12">
                            <label for="">Fecha Amortización</label>
                            <input type="date" class="form-control" id="fecha">
                        </div>
                        <div class="col-lg-6 col-xs-12">
                            <label for="">Monto Credito</label>
                            <input type="text" class="form-control" disabled id="impo_cre" value="{{$credito->impo_cre}}">
                        </div>
                        <div class="col-lg-6 col-xs-12">
                            <label for="">Forma de Pago</label>
                            <input type="text" disabled class="form-control" id="periodo_pago" value="{{$credito->periodo_pago}}">
                        </div>
                        <div class="col-lg-6 col-xs-12">
                            <label for="">Producto</label>
                            @if($credito->obse_cre !="")
                            <input type="text" disabled class="form-control" id="periodo_pago" value="{{$credito->obse_cre}}">
                            @else

                            <table class="table dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <td>Descripción</td>
                                        <td>Cantidad</td>
                                        <td>Total</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($detalle as $dt)
                                    <tr>
                                        <td>{{$dt->nomb_pro}}</td>
                                        <td>{{$dt->cantidad}}</td>
                                        <td>{{$dt->subtotal}}</td>

                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            @endif

                        </div>

                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-xs-12">
                            <br>
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#cuotas_tab" role="tab" aria-selected="true">
                                        <span class="d-block d-sm-none"><i class="fas fa-list"></i></span>
                                        <span class="d-none d-sm-block">Lista de Cuotas</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#amortizaciones_tab" role="tab" aria-selected="false">
                                        <span class="d-block d-sm-none"><i class="fas fa-money-check-alt"></i></span>
                                        <span class="d-none d-sm-block">Amortizaciones</span>
                                    </a>
                                </li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content p-3 text-muted">
                                <!-- TAB CUOTAS -->
                                <div class="tab-pane active" id="cuotas_tab" role="tabpanel">
                                    <div class="table-responsive">

                                        <table id="datatable" class="table  dt-responsive  nowrap w-100">
                                            <thead>
                                                <tr style="background-color: #616A6B ;">
                                                    <th>Opciones</th>
                                                    <th style="color:#17202A"># Credito</th>
                                                    <th style="color:#17202A"># Cuota</th>
                                                    <th style="color:#17202A">Cuota</th>
                                                    <th style="color:#17202A">Interes</th>
                                                    <th style="color:#17202A">Saldo Cuota</th>
                                                    <th style="color:#17202A">Vencimiento</th>
                                                    <th style="color:#17202A">Estado</th>

                                                </tr>
                                            </thead>

                                            <?php $suma = 0;
                                            $saldo = 0;
                                            $total = 0;

                                            ?>

                                            <tbody id="listadocuotas">

                                                @foreach($cuotas as $c)

                                                <tr>
                                                    <?php

                                                    if ($c->fven_cuo <= $fecha && $c->esta_cuo == 'PENDIENTE') {
                                                        $suma = $suma + $c->saldo_cuo;
                                                    }

                                                    $saldo = $saldo + $c->saldo_cuo;
                                                    $total = $total + $c->mont_cuo;


                                                    ?>
                                                    @if($c->esta_cuo=='COBRADA')
                                                    <td><input type="checkbox" disabled="true" id="checkbox{{$c->credito_id}}"></td>
                                                    @else

                                                    <td><input type="checkbox" id="checkbox{{$c->credito_id}}"></td>

                                                    @endif

                                                    <td>{{$c->credito_id}}</td>
                                                    <td>{{$c->numero_cuo}}</td>
                                                    <td>{{$c->mont_cuo}}</td>
                                                    <td>0.00</td>
                                                    <td>{{$c->saldo_cuo}}</td>
                                                    <td>{{$c->fven_cuo}}</td>
                                                    @if($c->esta_cuo=='COBRADA')
                                                    <td> <span class="badge bg-success">{{$c->esta_cuo}}</span></td>

                                                    @else

                                                    <td> <span class="badge bg-danger">{{$c->esta_cuo}}</span></td>

                                                    @endif

                                                </tr>

                                                @endforeach

                                            </tbody>
                                            <tfoot>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>0.00</td>
                                                <td><strong>Saldo:</strong> <strong id="saldo_real">{{$saldo}}</strong> </td>
                                            </tfoot>
                                        </table>


                                    </div>
                                </div>

                                <!-- TAB AMORTIZACIONES -->
                                <div class="tab-pane" id="amortizaciones_tab" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table dt-responsive nowrap w-100">
                                            <thead>
                                                <tr style="background-color: #616A6B ;">
                                                    <th style="color:#17202A">Fecha Amortización</th>
                                                    <th style="color:#17202A">Monto Pagado</th>
                                                    <th style="color:#17202A">Forma de Pago</th>
                                                    <th style="color:#17202A">N° Recibo</th>
                                                    <th style="color:#17202A">Acciones</th>
                                                </tr>
                                            </thead>
                                            <?php $total_amortizaciones = 0; ?>
                                            <tbody>
                                                @foreach($amortizaciones_realizadas as $amo)
                                                <?php $total_amortizaciones += $amo->monto; ?>
                                                <tr>
                                                    <td>{{ date('d-m-Y h:i a', strtotime($amo->created_at)) }}</td>
                                                    <td>{{ number_format($amo->monto, 2, '.', '') }}</td>
                                                    <td>{{ $amo->forma_pago }}</td>
                                                    <td>{{ $amo->num_recibo }}</td>
                                                    <td>
                                                        @if($loop->first)
                                                        <a href="javascript:void(0);" class="btn btn-danger btn-sm" title="Eliminar Amortización" data-recibo="{{ $amo->num_recibo }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr style="background-color: #F8F9F9;">
                                                    <td style="text-align: right;"><strong>Total de Amortizaciones:</strong></td>
                                                    <td><strong style="font-size: 1.1em; color: green;">{{ number_format($total_amortizaciones, 2, '.', '') }}</strong></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                    <hr>
                    <hr>
                    <div class="row">

                        <div class="col-lg-4 col-xs-12">
                            <label for="">Vendedores <strong style="color:red">(*)</strong> </label>
                            <select name="vendedor_id" class="form-control" id="vendedor_id">
                                <option value="">--Seleccionar Vendedor--</option>
                                @foreach($vendedores as $v)
                                <option value="{{$v->id}}">{{$v->nombre}}</option>
                                @endforeach
                            </select>

                        </div>

                        <div class="col-lg-4 col-xs-12">

                            <label for="">Forma de Pago <strong style="color:red">(*)</strong> </label>
                            <select name="fpag_rec" id="fpag_rec" class="form-control obligatorio">
                                <option value="">--Seleccionar--</option>
                                @foreach($formapago as $for)

                                <option value="{{$for->id}}">{{$for->descripcion}}</option>

                                @endforeach
                            </select>


                        </div>

                        <div class="col-lg-4 col-xs-12">
                            <label for="">Monto de la Deuda</label>
                            <input type="text" disabled class="form-control" value="{{$suma}}">

                        </div>

                        <div class="col-lg-4 col-xs-12">
                            <label for="">Importe Seleccionado</label>
                            <input type="text" disabled class="form-control">

                        </div>
                        <div class="col-lg-4 col-xs-12">
                            <label for="">Importe Cobro <strong style="color:red">(*)</strong></label>
                            <input type="number" class="form-control obligatorio" id="importe">

                        </div>
                        <div class="col-lg-4 col-xs-12">
                            <label for="">Documento de Referencia</label>
                            <input type="text" class="form-control" id="referencia">

                        </div>
                        <div class="col-lg-4 col-xs-12">
                            <label for="">Glosa</label>
                            <textarea name="obse_rec" class="form-control" id="obse_rec" cols="5" rows="5">

                                                         </textarea>

                        </div>

                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-8 col-xs-12">

                        </div>
                        <div class="col-lg-4 col-xs-12">

                            @if($credito->esta_cre==2)
                            <button class="btn btn-success" disabled="true" id="amortizar"> <i class="fas fa-save"></i> Amortizar</button>
                            <button class="btn btn-info" disabled id="imprimir"> <i class=" fas fa-cloud-download-alt"></i> Imprimir Recibo</button>
                            @else

                            <button class="btn btn-success" id="amortizar"> <i class="fas fa-save"></i> Amortizar</button>
                            <button class="btn btn-info" id="imprimir"> <i class=" fas fa-cloud-download-alt"></i> Imprimir Recibo</button>


                            @endif

                        </div>

                    </div>



                </div>


            </div>
        </div>
    </div>



</div>





<div class="modal fade" id="staticBackdropdos" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Generando la Amortización</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="row">


                    <div class="col-lg-2 col-xs-12"></div>
                    <div class="col-lg-8 col-xs-12">

                        <img src="{{asset('img/loader-meta.gif')}}" style="" alt="" class="" width="100%">
                        <h4 style="text-align: center;color:#BA4A00">Espere mintras se guarda la Amortización...</h4>
                        <p style="text-align: center;color:#BA4A00">Gracias<i class=" fas fa-coffee"></i></p>

                    </div>
                    <div class="col-lg-2 col-xs-12"></div>

                </div>

            </div>

        </div>
    </div>
</div>

<!--<div class="modal fade bs-example-modal-sm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="modal_caja">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleModal">Alerta de Caja</h5>
            </div>
            <div class="modal-body text-center">
                <h4 id="mensaje_caja" class="mb-4"></h4>
                <a class="btn btn-danger" id="ir_caja">Ir a caja</a>
            </div>
        </div>
    </div>
</div> -->




@endsection

@section('js')

<!-- Sweet Alerts js -->
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

<script src="{{ asset('js/amortizacion.js') }}"></script>

<script>
    $(document).on('click', '.btn-danger[title="Eliminar Amortización"]', function(e) {
        e.preventDefault();
        let num_recibo = $(this).data('recibo');

        Swal.fire({
            title: '¿Confirmar Anulación?',
            html: `
                <p>Está a punto de anular por completo el recibo <b>${num_recibo}</b> y restituir el saldo de la deuda.</p>
                <div class="form-check text-start p-3 bg-light rounded border mt-3" style="font-size: 14px;">
                    <input class="form-check-input" type="checkbox" id="eliminar_movimiento" style="margin-left: 0; margin-right: 10px;" checked>
                    <label class="form-check-label" for="eliminar_movimiento" style="margin-left: 20px; cursor: pointer;">
                        Anular también el Movimiento en Caja asociado.
                    </label>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '<i class="fas fa-trash-alt"></i> Sí, anular',
            cancelButtonText: 'Cancelar',
            preConfirm: () => {
                return document.getElementById('eliminar_movimiento').checked;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let eliminar_movimiento = result.value ? 1 : 0;

                $.ajax({
                    url: '{{ url("amortizacion/anular_recibo_amort") }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        num_recibo: num_recibo,
                        eliminar_movimiento: eliminar_movimiento
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('¡Anulado!', 'La amortización fue anulada correctamente.', 'success')
                                .then(() => {
                                    window.location.reload();
                                });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Ocurrió un error en el servidor, intente nuevamente', 'error');
                    }
                });
            }
        });
    });
</script>

@endsection