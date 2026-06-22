@extends('layouts.main')

@section('title')
Listado Compras
@endsection

@section('css')
<!-- Sweet Alert-->
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

<!-- DataTables -->
<link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

<style>
    .compra-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    }

    .compra-card .card-header {
        background: white;
        color: #2C3E50;
        border-bottom: 2px solid var(--bs-primary);
        border-radius: 12px 12px 0 0;
        padding: 1rem 1.25rem;
        font-weight: 700;
    }

    .compra-card .card-body {
        padding: 1.25rem;
    }

    .form-label {
        font-weight: 600;
        color: #34495E;
        font-size: 0.85rem;
        margin-bottom: 0.35rem;
    }

    .form-control, .form-select {
        border-radius: 8px;
        border: 1.5px solid #E5E8E8;
        padding: 0.6rem 0.85rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 3px rgba(var(--bs-primary-rgb), 0.12);
    }

    .btn-primary {
        background: var(--bs-primary);
        border: none;
        border-radius: 8px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
    }

    .btn-primary:hover {
        background: var(--bs-primary);
        filter: brightness(0.9);
    }

    .btn-danger {
        border-radius: 8px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
    }

    .btn-success {
        border-radius: 8px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
    }

    .productos-section {
        background: #F8F9FA;
        border-radius: 12px;
        border: 1.5px solid #E5E8E8;
        overflow: hidden;
    }

    .productos-section .header {
        background: var(--bs-primary);
        color: white;
        padding: 0.75rem 1rem;
        font-weight: 600;
    }

    .search-box {
        border-radius: 10px;
        border: 1.5px solid #E5E8E8;
        padding: 0.75rem 1rem 0.75rem 2.75rem;
        background: white;
    }

    .search-box:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 3px rgba(var(--bs-primary-rgb), 0.12);
    }

    .search-wrapper {
        position: relative;
    }

    .search-wrapper .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #7F8C8D;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 0.75rem;
        max-height: 55vh;
        overflow-y: auto;
        padding: 0.75rem;
    }

    .product-item {
        background: white;
        border: 1.5px solid #E5E8E8;
        border-radius: 10px;
        padding: 0.85rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .product-item:hover {
        border-color: var(--bs-primary);
        box-shadow: 0 3px 12px rgba(var(--bs-primary-rgb), 0.15);
        transform: translateY(-2px);
    }

    .product-item .nombre {
        font-weight: 600;
        color: #2C3E50;
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }

    .product-item .precio {
        color: var(--bs-primary);
        font-weight: 700;
        font-size: 1rem;
    }

    .product-item .stock {
        font-size: 0.75rem;
        color: #7F8C8D;
    }

    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }

    .table-compras {
        margin-bottom: 0;
        font-size: 0.85rem;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-compras thead {
        background: var(--bs-primary);
        color: white;
        border-radius: 8px 8px 0 0;
    }

    .table-compras thead th {
        font-weight: 600;
        padding: 0.75rem 0.6rem;
        border: none;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .table-compras thead th:first-child {
        border-radius: 8px 0 0 0;
    }

    .table-compras thead th:last-child {
        border-radius: 0 8px 0 0;
    }

    .table-compras tbody tr {
        border-bottom: 1px solid #F0F0F0;
        transition: background-color 0.15s ease;
    }

    .table-compras tbody tr:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.04);
    }

    .table-compras tbody td {
        padding: 0.6rem 0.5rem;
        vertical-align: middle;
        border: none;
        font-size: 0.85rem;
        color: #444;
    }

    .table-compras .col-num {
        width: 40px;
        text-align: center;
        color: #888;
        font-size: 0.78rem;
        font-weight: 600;
    }

    .table-compras .col-producto {
        font-weight: 600;
        color: #2C3E50;
        min-width: 180px;
    }

    .table-compras .col-und {
        font-size: 0.8rem;
        color: #666;
        text-align: center;
    }

    .table-compras .col-cant,
    .table-compras .col-precio,
    .table-compras .col-flete {
        text-align: center;
        width: 90px;
    }

    .table-compras .col-subtotal {
        text-align: right;
        font-weight: 700;
        color: var(--bs-primary);
        width: 100px;
    }

    .table-compras tbody tr .btn-remove {
        width: 28px;
        height: 28px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 0.75rem;
        line-height: 1;
    }

    .table-compras .input-sm-table {
        width: 80px;
        padding: 0.35rem 0.5rem;
        font-size: 0.82rem;
        border-radius: 6px;
        text-align: center;
        border: 1.5px solid #E5E8E8;
    }

    .table-compras .input-sm-table:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 2px rgba(var(--bs-primary-rgb), 0.1);
        outline: none;
    }

    .table-compras tbody tr.selected {
        background-color: rgba(var(--bs-primary-rgb), 0.04);
    }

    /* Empty state */
    .table-compras .empty-message td {
        text-align: center;
        color: #aaa;
        font-size: 0.9rem;
        padding: 2.5rem 1rem;
    }

    /* Responsive */
    @media (max-width: 767.98px) {
        .table-compras {
            font-size: 0.75rem;
        }

        .table-compras thead th,
        .table-compras tbody td {
            padding: 0.4rem 0.3rem;
        }

        .table-compras .col-producto {
            min-width: 120px;
        }

        .table-compras .input-sm-table {
            width: 65px;
        }
    }

    .totales-section {
        background: linear-gradient(135deg, #F8F9FA 0%, #EBEDEF 100%);
        border-radius: 10px;
        padding: 1rem;
        border: 1.5px solid #E5E8E8;
    }

    .totales-section .fila-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.35rem 0;
        font-size: 0.9rem;
    }

    .totales-section .fila-total.total-final {
        border-top: 2px solid var(--bs-primary);
        padding-top: 0.6rem;
        margin-top: 0.3rem;
        font-size: 1.1rem;
    }

    .totales-section .label {
        color: #5D6D7E;
        font-weight: 500;
    }

    .totales-section .valor {
        font-weight: 700;
        color: #2C3E50;
    }

    .totales-section .valor.total {
        color: var(--bs-primary);
        font-size: 1.3rem;
    }

    .checkbox-switch {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .checkbox-switch .form-check-input {
        width: 1.3rem;
        height: 1.3rem;
        cursor: pointer;
    }

    .checkbox-switch .form-check-label {
        font-weight: 500;
        color: #5D6D7E;
        font-size: 0.85rem;
        cursor: pointer;
    }

    .input-disabled:disabled {
        background-color: #F8F9FA;
        border-color: #E5E8E8;
        color: #7F8C8D;
    }

    @media (max-width: 991.98px) {
        .product-grid {
            max-height: 35vh;
        }
    }

    @media (max-width: 767.98px) {
        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            max-height: 30vh;
        }

        .table-compras {
            font-size: 0.75rem;
        }

        .table-compras thead th,
        .table-compras tbody td {
            padding: 0.4rem 0.3rem;
        }
    }

    .btn-modal-add {
        border-radius: 8px;
        padding: 0.35rem 0.75rem;
        font-size: 0.8rem;
    }

    .product-list-container {
        max-height: 50vh;
        overflow-y: auto;
    }

    /* ========== PRODUCT CARDS (inyecciondos) ========== */
    .product-card {
        background: white;
        border: 1.5px solid #E5E8E8;
        border-radius: 10px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.25s ease;
        display: flex;
        flex-direction: column;
    }

    .product-card:hover {
        border-color: var(--bs-primary);
        box-shadow: 0 4px 16px rgba(var(--bs-primary-rgb), 0.18);
        transform: translateY(-3px);
    }

    .product-card-img {
        width: 100%;
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #F8F9FA;
        padding: 0.5rem;
        overflow: hidden;
    }

    .product-card-img img {
        max-height: 90px;
        max-width: 100%;
        object-fit: contain;
    }

    .product-card-body {
        padding: 0.75rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }

    .product-name {
        font-weight: 700;
        color: #2C3E50;
        font-size: 0.82rem;
        line-height: 1.3;
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem;
    }

    .meta-tag {
        background: #EBEDEF;
        color: #5D6D7E;
        font-size: 0.68rem;
        padding: 0.15rem 0.4rem;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 0.2rem;
        white-space: nowrap;
    }

    .meta-tag i {
        font-size: 0.7rem;
    }

    .product-price {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--bs-primary);
        color: white;
        padding: 0.4rem 0.6rem;
        border-radius: 6px;
        margin-top: auto;
    }

    .price-label {
        font-size: 0.68rem;
        font-weight: 600;
        text-transform: uppercase;
        opacity: 0.85;
    }

    .price-value {
        font-size: 0.9rem;
        font-weight: 700;
    }

    /* Responsive product grid */
    @media (max-width: 575.98px) {
        .product-card-img {
            height: 80px;
        }

        .product-name {
            font-size: 0.75rem;
        }
    }
</style>
@endsection

@section('contenido')

<div class="container-fluid py-3">

    <!-- Page Title -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">
                    <i class="mdi mdi-clipboard-text-outline me-2 text-success"></i>Registro de Compras
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0"></ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Required Fields Notice -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert" style="border-radius: 10px;">
                <i class="mdi mdi-alert-circle-outline me-2" style="font-size: 1.2rem;"></i>
                <span>Todos los campos marcados con <strong>(*)</strong> son obligatorios</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-3">

        <!-- LEFT COLUMN: Form -->
        <div class="col-lg-7">

            <!-- Datos Generales Card -->
            <div class="card compra-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <i class="mdi mdi-information-outline me-2"></i>
                    Datos Generales
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-4 col-12">
                            <label class="form-label">Fecha Compra <strong style="color:red">(*)</strong></label>
                            <input type="date" class="form-control" id="fecha_compra" name="fecha_compra">
                        </div>
                        <div class="col-sm-4 col-12">
                            <label class="form-label">Tipo Moneda <strong style="color:red">(*)</strong></label>
                            <select name="moneda_id" id="moneda_id" class="form-select options">
                                <option value="">--Seleccionar--</option>
                                @foreach($monedas as $mon)
                                    @if($mon->id==1)
                                        <option value="{{$mon->id}}" selected>{{$mon->descripcion}}</option>
                                    @else
                                        <option value="{{$mon->id}}">{{$mon->descripcion}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4 col-12">
                            <label class="form-label">Tipo Cambio</label>
                            <input type="number" class="form-control" value="0.00" name="cambio_monto" id="cambio_monto">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Productos Card -->
            <div class="card compra-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <i class="mdi mdi-cart-outline me-2"></i>
                    Productos Seleccionados
                    <input type="hidden" id="controlstock">
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive product-list-container">
                        <table class="table table-compras">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 40px;">#</th>
                                    <th>PRODUCTO</th>
                                    <th class="text-center" style="width: 80px;">UND</th>
                                    <th class="text-center" style="width: 90px;">CANT.</th>
                                    <th class="text-center" style="width: 90px;">PRECIO</th>
                                    <th class="text-center" style="width: 70px;">FLETE</th>
                                    <th class="text-center" style="width: 100px;">SUBTOTAL</th>
                                    <th class="text-center" style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="listadocompras">
                                <tr class="empty-message">
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="mdi mdi-cart-outline" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; opacity: 0.4;"></i>
                                        No hay productos agregados
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Totales Card -->
            <div class="card compra-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <i class="mdi mdi-calculator me-2"></i>
                    Resumen de Totales
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Left: Checkboxes -->
                        <div class="col-md-5">
                            <div class="totales-section h-100 d-flex flex-column justify-content-center">
                                <div class="checkbox-switch mb-2">
                                    <input type="checkbox" class="form-check-input tipo_envio" id="igv" value="0.00" style="cursor: pointer;" name="controlstock" value="NO" />
                                    <label for="igv" class="form-check-label">Incluir IGV</label>
                                </div>
                                <div class="checkbox-switch mb-2">
                                    <input type="checkbox" class="form-check-input tipo_envio" id="persecciontem" style="cursor: pointer;" name="controlstock" value="NO" />
                                    <label for="persecciontem" class="form-check-label">Percepción</label>
                                </div>
                                <div class="checkbox-switch">
                                    <input type="checkbox" class="form-check-input tipo_envio" id="icbpertem" style="cursor: pointer;" name="controlstock" value="NO" />
                                    <label for="icbpertem" class="form-check-label">ICBPER</label>
                                </div>
                            </div>
                        </div>
                        <!-- Right: Values -->
                        <div class="col-md-7">
                            <div class="totales-section">
                                <div class="fila-total">
                                    <span class="label">Sub Total</span>
                                    <span class="valor">S/. <span id="subtotal">0.00</span></span>
                                </div>
                                <input type="hidden" id="subtotal_input">
                                <div class="fila-total">
                                    <span class="label">IGV</span>
                                    <span class="valor">S/. <span id="igvtodo">0.00</span></span>
                                </div>
                                <div class="fila-total">
                                    <span class="label">Percepción</span>
                                    <div><input type="text" disabled class="form-control input-disabled d-inline-block w-auto" name="perseccion" id="perseccion"></div>
                                </div>
                                <div class="fila-total">
                                    <span class="label">ICBPER</span>
                                    <div><input type="text" disabled class="form-control input-disabled d-inline-block w-auto" name="icbper" id="icbper"></div>
                                </div>
                                <div class="fila-total total-final">
                                    <span class="label fw-bold">TOTAL</span>
                                    <span class="valor total">S/. <span id="total_compratemporal">0.00</span></span>
                                </div>
                                <input type="hidden" name="total_compra" id="total_compra">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Datos de Compra Card -->
            <div class="card compra-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <i class="mdi mdi-file-document-outline me-2"></i>
                    Datos de la Compra
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <!-- Proveedor -->
                        <div class="col-md-6">
                            <label class="form-label">Proveedor <strong style="color:red">(*)</strong></label>
                            <div class="input-group">
                                <select name="proveedor_id" id="proveedor_id" class="form-select">
                                    <option value="">--Seleccionar--</option>
                                </select>
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#staticBackdrop" onclick="abrimodal(0)">
                                    <i class="mdi mdi-plus"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Almacen -->
                        <div class="col-md-6">
                            <label class="form-label">Almacén <strong style="color:red">(*)</strong></label>
                            <select name="almacen_id" id="almacen_id" class="form-select">
                                <option value="">--Seleccionar--</option>
                                @foreach($almacenes as $al)
                                    <option value="{{$al->id}}" {{ str_contains($al->abreviatura, 'Stock') || str_contains($al->ubicacion, 'Stock') ? 'selected' : '' }}>{{$al->abreviatura}} / {{$al->ubicacion}}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tipo Pago -->
                        <div class="col-md-6">
                            <label class="form-label">Tipo Pago <strong style="color:red">(*)</strong></label>
                            <select name="tipo_pago_id" id="tipo_pago_id" class="form-select">
                                <option value="">--Seleccionar--</option>
                                @foreach($tipopago as $tipo)
                                    <option value="{{$tipo->id}}">{{$tipo->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tipo Comprobante -->
                        <div class="col-md-6">
                            <label class="form-label">Tipo Comprobante <strong style="color:red">(*)</strong></label>
                            <select name="tipo_comprobante_id" id="tipo_comprobante_id" class="form-select">
                                <option value="">--Seleccionar--</option>
                                @foreach($comprobante as $com)
                                    <option value="{{$com->id}}" {{ $com->id == 12 ? 'selected' : '' }}>{{$com->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Serie y Número -->
                        <div id="document_ser" class="row w-100 mx-0 px-0">
                            <div class="col-md-3 col-6">
                                <label class="form-label">Serie <strong style="color:red">(*)</strong></label>
                                <input type="text" class="form-control" name="serie_comprobante" id="serie_comprobante">
                            </div>
                            <div class="col-md-3 col-6">
                                <label class="form-label">Número <strong style="color:red">(*)</strong></label>
                                <input type="text" class="form-control" name="correlativo_comprobante" id="correlativo_comprobante">
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('compras.index') }}" class="btn btn-danger px-4">
                    <i class="mdi mdi-close-circle-outline me-1"></i>CANCELAR
                </a>
                <button type="button" class="btn btn-primary px-4" id="pagar">
                    <i class="mdi mdi-check-circle-outline me-1"></i>CREAR COMPRA
                </button>
            </div>

        </div>

        <!-- RIGHT COLUMN: Product Search -->
        <div class="col-lg-5">

            <div class="card compra-card sticky-top" style="top: 1rem;">
                <div class="card-header d-flex align-items-center">
                    <i class="mdi mdi-magnify me-2"></i>
                    Buscar Productos
                </div>
                <div class="card-body p-0">
                    <div class="p-3 border-bottom">
                        <div class="search-wrapper">
                            <i data-feather="search" class="search-icon" style="width: 18px; height: 18px;"></i>
                            <input type="text" class="form-control search-box w-100" onkeyup="buscar();" id="navbarForm" placeholder="Buscar por nombre de producto...">
                        </div>
                    </div>
                    <div class="product-grid" id="inyecciondos">
                        <!-- Products injected by JS -->
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Static Backdrop Modal - Proveedor -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px;">
            <div class="modal-header" style="background: var(--bs-primary); color: white; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title" id="staticBackdropLabel">
                    <i class="mdi mdi-account-plus-outline me-2"></i>Nuevo Proveedor
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p style="color:#E74C3C; font-size: 0.85rem;"><i class="mdi mdi-alert-circle-outline me-1"></i>Todos los campos marcados con (*) son obligatorios</p>
                <input type="hidden" name="name" id="valor" value="0" />

                <div class="mb-3">
                    <label class="form-label">Razón Social <strong style="color:red">(*)</strong></label>
                    <select name="" id="razon_social" class="form-select"></select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Número Documento <strong style="color:red">(*)</strong></label>
                    <input type="number" class="form-control obligatorio limpiar" placeholder="Número de documento" id="ruc">
                </div>

                <div class="mb-3">
                    <label class="form-label">Nombre Comercial <strong style="color:red">(*)</strong></label>
                    <input type="text" class="form-control obligatorio limpiar" placeholder="Nombre comercial" id="nombre_comercial">
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label">Teléfono</label>
                        <input type="text" class="form-control limpiar" placeholder="Teléfono" id="telefono">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Email</label>
                        <input type="text" class="form-control limpiar" placeholder="Email" id="email">
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label class="form-label">Dirección</label>
                    <input type="text" class="form-control limpiar" placeholder="Dirección" id="direccion">
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label">Sitio Web</label>
                        <input type="text" class="form-control limpiar" placeholder="Sitio web" id="web_sitie">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Contacto</label>
                        <input type="text" class="form-control limpiar" placeholder="Contacto" id="contacto">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardar">
                    <i class="mdi mdi-content-save-outline me-1"></i>Guardar
                </button>
                <button type="button" class="btn btn-warning" id="actualizar">
                    <i class="mdi mdi-pencil-outline me-1"></i>Actualizar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA AGREGAR LA CANTIDAD DEL PRODUCTO -->
<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="modal_producto">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px;">
            <div class="modal-header" style="background: var(--bs-primary); color: white; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title text-white" id="titleModal">
                    <i class="mdi mdi-cart-plus me-2"></i>Agregar Producto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="idProducto">
                <input type="hidden" id="price-producto">

                <div class="mb-3">
                    <label class="form-label">Cantidad</label>
                    <input class="form-control" type="number" id="cantidad_producto" placeholder="Ingrese cantidad">
                </div>

                <div class="mb-3">
                    <label class="form-label">Unidad de Medida</label>
                    <select name="" id="uniades_id" class="form-select"></select>
                </div>

                <div class="d-grid mt-4">
                    <button type="button" class="btn btn-primary" onclick="agregar_detalle()">
                        <i class="mdi mdi-cart-plus me-1"></i>Agregar a la Lista
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- GENERANDO COMPRA -->
<div class="modal fade" id="staticBackdropdos" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px;">
            <div class="modal-header" style="background: var(--bs-primary); color: white; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title text-white" id="staticBackdropLabel">
                    <i class="mdi mdi-spin mdi-loading me-2"></i>Guardando Compra
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <img src="{{asset('img/loader-meta.gif')}}" alt="Cargando..." class="mb-3" style="max-width: 150px;">
                <h5 style="color:#186A3C;">Espere mientras se guarda la compra...</h5>
                <p style="color:#7F8C8D;" class="mb-0">Gracias por su paciencia <i class="mdi mdi-coffee"></i></p>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')

<!-- Sweet Alerts js -->
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Required datatable js -->
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.14.1/moment.min.js"></script>

<script src="{{ asset('js/crearcompras.js') }}"></script>

@endsection
