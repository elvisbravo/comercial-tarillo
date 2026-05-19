@extends('layouts.main')

@section('title', 'Registrar Cobranzas')

@section('css')
<style>
    .mobile-container {
        max-width: 600px;
        margin: 0 auto;
        padding-bottom: 80px;
    }
    .credit-list-item {
        border-radius: 12px;
        border: 1px solid #eef2f3;
        padding: 15px;
        background: #ffffff;
        margin-bottom: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        transition: all 0.2s ease;
    }
    .credit-list-item:active {
        background: #f8f9fa;
        transform: scale(0.99);
    }
    .search-box {
        border-radius: 10px;
        border: 1px solid #ced4da;
        padding: 10px 15px;
    }
    .btn-blue-grad {
        background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 600;
    }
    .btn-blue-grad:hover, .btn-blue-grad:focus {
        color: white;
    }
</style>
@endsection

@section('contenido')
<div class="mobile-container py-3">
    <!-- Encabezado / Regresar -->
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('vendedor.dashboard') }}" class="btn btn-light btn-rounded me-2">
            <i class="mdi mdi-arrow-left"></i>
        </a>
        <h4 class="mb-0 font-weight-bold" style="color: #2c3e50;">Registrar Cobranza</h4>
    </div>

    <!-- Buscador de Créditos -->
    <div class="mb-3">
        <input type="text" class="form-control search-box" id="buscar_credito" placeholder="🔍 Buscar cliente por nombre o RUC/DNI...">
    </div>

    <!-- Lista de Créditos Activos en Ruta -->
    <div id="creditos_lista_wrapper">
        @if($creditos->isEmpty())
            <div class="text-center py-5 bg-white shadow-sm rounded p-3">
                <i class="mdi mdi-cash-remove text-muted" style="font-size: 48px;"></i>
                <p class="text-muted mt-2 mb-0">No se encontraron créditos activos para su ruta de hoy.</p>
            </div>
        @else
            @foreach($creditos as $c)
                <div class="credit-list-item" 
                     data-id="{{ $c->id }}" 
                     data-cliente-id="{{ $c->cliente_id }}"
                     data-nombre="{{ $c->razon_social }}" 
                     data-documento="{{ $c->documento }}"
                     data-saldo="{{ $c->saldo_pendiente }}"
                     data-total-credito="{{ $c->impo_cre }}">
                    
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="font-weight-bold d-block text-dark" style="font-size: 15px;">{{ $c->razon_social }}</span>
                            <small class="text-muted d-block mt-1">DNI/RUC: {{ $c->documento }}</small>
                            <span class="badge bg-soft-primary text-primary mt-2">Crédito N° {{ $c->id }}</span>
                        </div>
                        <div class="text-end">
                            <span class="text-muted font-size-11 d-block">Saldo Pendiente</span>
                            <span class="font-weight-bold text-danger d-block" style="font-size: 18px;">S/ {{ number_format($c->saldo_pendiente, 2) }}</span>
                            <small class="text-muted d-block font-size-11">Total: S/ {{ number_format($c->impo_cre, 2) }}</small>
                        </div>
                    </div>
                    
                    <div class="d-grid mt-3">
                        <button class="btn btn-blue-grad btn-sm py-2 btn-cobrar-credito" type="button">
                            <i class="mdi mdi-cash-plus me-1"></i> Cobrar Amortización
                        </button>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

<!-- Modal para Registrar Cobro -->
<div class="modal fade" id="modalCobrarAmortizacion" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title font-weight-bold text-dark">Registrar Amortización</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_cobrar_amortizacion">
                <div class="modal-body p-4">
                    <div class="mb-3 p-3 bg-soft-info rounded">
                        <h6 class="font-weight-bold mb-1 text-info" id="modal_cliente_nombre">---</h6>
                        <span class="text-muted font-size-12" id="modal_cliente_doc">---</span>
                        <div class="d-flex justify-content-between mt-2 pt-2 border-top border-info-subtle">
                            <span class="text-dark font-size-12">Saldo Máximo a Cobrar:</span>
                            <span class="font-weight-bold text-danger" id="modal_saldo_maximo">S/ 0.00</span>
                        </div>
                    </div>

                    <!-- Campos Requeridos por el backend -->
                    <input type="hidden" name="cliente_id" id="input_cliente_id">
                    <input type="hidden" name="credito_id" id="input_credito_id">
                    <input type="hidden" name="vendedor_id" value="{{ $vendedor->id }}">
                    <input type="hidden" name="fech_rec" value="{{ date('Y-m-d') }}">

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Monto a Cobrar (S/)</label>
                        <input type="number" step="0.01" class="form-control form-control-lg font-weight-bold text-success" name="mont_rec" id="input_monto_rec" placeholder="0.00" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Forma de Pago</label>
                        <select class="form-select" name="fpag_rec" id="input_fpag_rec" required>
                            <option value="1" selected>EFECTIVO</option>
                            <option value="2">DEPOSITO / TRANSFERENCIA</option>
                            <option value="3">YAPE</option>
                            <option value="4">PLIN</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Documento de Referencia / N° Op.</label>
                        <input type="text" class="form-control" name="docu_ref" id="input_docu_ref" placeholder="N° de boleta, recibo o transferencia">
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Observación / Comentario</label>
                        <textarea class="form-control" name="obse_rec" id="input_obse_rec" rows="2" placeholder="Escriba algún comentario opcional..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Atrás</button>
                    <button type="submit" class="btn btn-success px-4" id="btn_guardar_cobro">
                        Confirmar Cobro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Éxito / Ticket Cobro -->
<div class="modal fade" id="modalCobroExitoso" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <i class="mdi mdi-checkbox-marked-circle-outline text-success" style="font-size: 72px;"></i>
                </div>
                <h4 class="font-weight-bold text-dark mb-2">¡Cobranza Registrada!</h4>
                <p class="text-muted mb-4">La cobranza ha sido guardada exitosamente y queda pendiente de liquidación administrativa.</p>

                <div class="d-grid gap-2">
                    <a href="#" id="link_ticket_cobro" target="_blank" class="btn btn-primary btn-lg">
                        <i class="mdi mdi-receipt font-size-18 me-2"></i> Ver Recibo de Cobranza (PDF)
                    </a>
                    <a href="{{ route('vendedor.dashboard') }}" class="btn btn-outline-secondary">
                        Volver al Dashboard
                    </a>
                    <button type="button" class="btn btn-light" onclick="window.location.reload();">
                        Cobrar a Otro Cliente
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $(".loader").fadeOut("slow");

        // Filtrar créditos
        $('#buscar_credito').on('keyup', function() {
            let value = $(this).val().toLowerCase();
            $('#creditos_lista_wrapper .credit-list-item').filter(function() {
                let matchNombre = $(this).attr('data-nombre').toLowerCase().indexOf(value) > -1;
                let matchDoc = $(this).attr('data-documento').toLowerCase().indexOf(value) > -1;
                $(this).toggle(matchNombre || matchDoc);
            });
        });

        // Abrir modal de cobro
        $('.btn-cobrar-credito').on('click', function() {
            let item = $(this).closest('.credit-list-item');
            let idCredito = item.attr('data-id');
            let idCliente = item.attr('data-cliente-id');
            let nombre = item.attr('data-nombre');
            let documento = item.attr('data-documento');
            let saldo = parseFloat(item.attr('data-saldo'));
            
            $('#input_cliente_id').val(idCliente);
            $('#input_credito_id').val(idCredito);
            $('#modal_cliente_nombre').text(nombre);
            $('#modal_cliente_doc').text('DNI/RUC: ' + documento);
            $('#modal_saldo_maximo').text('S/ ' + saldo.toFixed(2));
            $('#input_monto_rec').val(saldo.toFixed(2)).attr('max', saldo);
            
            $('#modalCobrarAmortizacion').modal('show');
        });

        // Enviar Formulario de Cobro
        $('#form_cobrar_amortizacion').on('submit', function(e) {
            e.preventDefault();

            let monto = parseFloat($('#input_monto_rec').val()) || 0;
            let maxMonto = parseFloat($('#input_monto_rec').attr('max')) || 0;

            if (monto <= 0) {
                alert('Ingrese un monto válido mayor a cero.');
                return;
            }
            if (monto > maxMonto) {
                alert('El monto ingresado excede el saldo pendiente de S/ ' + maxMonto.toFixed(2));
                return;
            }

            let formData = new FormData(this);
            formData.append('_token', "{{ csrf_token() }}");
            formData.append('es_movil', 1);

            $('#btn_guardar_cobro').prop('disabled', true).text('Guardando...');
            $(".loader").fadeIn("fast");

            $.ajax({
                url: "{{ route('vendedor.cobros.guardar') }}",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(res) {
                    $(".loader").fadeOut("fast");
                    if (res.status === 'OK') {
                        $('#modalCobrarAmortizacion').modal('hide');
                        $('#link_ticket_cobro').attr('href', "{{ url('consulta-amortizaciones/recibo') }}/" + res.id);
                        $('#modalCobroExitoso').modal('show');
                    } else {
                        $('#btn_guardar_cobro').prop('disabled', false).text('Confirmar Cobro');
                        alert('Error al registrar cobro: ' + (res.message || 'Error desconocido'));
                    }
                },
                error: function(xhr) {
                    $(".loader").fadeOut("fast");
                    $('#btn_guardar_cobro').prop('disabled', false).text('Confirmar Cobro');
                    alert('Error al conectar con el servidor.');
                }
            });
        });
    });
</script>
@endsection
