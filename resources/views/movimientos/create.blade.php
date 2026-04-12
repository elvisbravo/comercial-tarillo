@extends('layouts.main')

@section('title')
Nuevo Movimiento
@endsection

@section('css')

<link href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}" rel="stylesheet" type="text/css" />

<!-- Sweet Alert-->
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

@endsection

@section('contenido')

<nav class="page-breadcrumb">
    <ol class="breadcrumb p-0">
        <li class="breadcrumb-item">
            <h4>Crear Nuevo Movimiento</h4>
        </li>
    </ol>
</nav>

<form id="form_movimiento" method="POST">
    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label" for="formrow-email-input">TIPO DE MOVIMIENTO</label>
                <select class="form-select" id="tipo_movimiento" name="tipo_movimiento" required>
                    <option value="">SELECCIONE</option>
                    <option value="INGRESO">INGRESO</option>
                    <option value="EGRESO">EGRESO</option>
                </select>
            </div>
        </div>

        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label" for="formrow-email-input">CONCEPTO</label>
                <select class="form-select" name="concepto" id="concepto" required>
                    <option value="">SELECCIONE</option>
                </select>
            </div>
        </div>

        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label" for="formrow-email-input">FORMA DE PAGO</label>
                <select class="form-select" name="forma_pago" required>
                    <option value="">SELECCIONE</option>
                    @foreach($forma_pagos as $forma)
                    <option value="{{ $forma->id }}">{{ $forma->descripcion }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label" for="formrow-email-input">MONTO</label>
                <input type="text" class="form-control" name="monto" required>
            </div>
        </div>

        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label" for="formrow-email-input">DESCRIPCION</label>
                <input type="text" class="form-control" name="descripcion" required>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="mb-3 text-center">
                <a href="{{route('movimientos.index')}}" class="btn btn-danger">CANCELAR</a>
                <button type="submit" class="btn btn-success">GUARDAR</button>
            </div>
        </div>
    </div>

</form>

<div class="modal fade bs-example-modal-sm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="modal_caja">
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
</div>

@endsection

@section('js')

<!-- Sweet Alerts js -->
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<script src="{{ asset('assets/js/pages/jquery.bootstrap-touchspin.js') }}"></script>

<script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>

<script src="{{ asset('js/new_movimiento.js') }}">
</script>

@endsection