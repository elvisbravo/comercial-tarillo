@extends('layouts.main')

@section('title', 'Registrar Venta - Ventas Móviles')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    * { font-family: 'Inter', sans-serif; }

    body { background: #f0f4f8; }

    /* === SELECT2 CUSTOM PREMIUM STYLING === */
    .select2-container--default .select2-selection--single {
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 12px !important;
        height: 46px !important;
        background: white !important;
        transition: all 0.2s !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 46px !important;
        padding-left: 14px !important;
        padding-right: 30px !important;
        color: #1e293b !important;
        font-size: 14px !important;
        font-family: 'Inter', sans-serif !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px !important;
        right: 12px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #6b7280 transparent transparent transparent !important;
        border-width: 5px 4px 0 4px !important;
    }
    .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
        border-color: transparent transparent #6b7280 transparent !important;
        border-width: 0 4px 5px 4px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.12) !important;
        outline: none !important;
    }
    .select2-dropdown {
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08) !important;
        overflow: hidden !important;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 8px !important;
        padding: 8px 12px !important;
        font-size: 13px !important;
        font-family: 'Inter', sans-serif !important;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        border-color: #3b82f6 !important;
        outline: none !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #3b82f6 !important;
        color: white !important;
    }
    .select2-container--default .select2-results__option {
        padding: 8px 14px !important;
        font-size: 13px !important;
        font-family: 'Inter', sans-serif !important;
    }

    .mobile-wrapper {
        max-width: 640px;
        margin: 0 auto;
        padding: 16px 16px 100px;
    }

    /* === HEADER === */
    .page-header {
        background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
        border-radius: 20px;
        padding: 20px;
        margin-bottom: 16px;
        color: white;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 8px 32px rgba(15,32,39,0.3);
    }
    .page-header .back-btn {
        width: 40px; height: 40px;
        background: rgba(255,255,255,0.15);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        color: white; text-decoration: none;
        transition: background 0.2s;
        flex-shrink: 0;
    }
    .page-header .back-btn:hover { background: rgba(255,255,255,0.25); color: white; }
    .page-header .vendor-info small { opacity: 0.7; font-size: 12px; }
    .page-header h4 { margin: 0; font-weight: 700; font-size: 17px; }

    /* === CARDS === */
    .section-card {
        background: white;
        border-radius: 16px;
        padding: 18px;
        margin-bottom: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 1px solid rgba(0,0,0,0.04);
    }
    .section-card .section-title {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #94a3b8;
        margin-bottom: 14px;
        display: flex; align-items: center; gap: 6px;
    }
    .section-card .section-title i { font-size: 14px; }

    /* === TIPO DE VENTA TOGGLE === */
    .venta-toggle {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-bottom: 14px;
    }
    .venta-toggle-btn {
        border: 2px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 12px;
        padding: 12px;
        cursor: pointer;
        text-align: center;
        transition: all 0.2s;
        font-weight: 600;
        font-size: 13px;
        color: #64748b;
        user-select: none;
    }
    .venta-toggle-btn.active-contado {
        border-color: #10b981;
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        color: #059669;
    }
    .venta-toggle-btn.active-credito {
        border-color: #f59e0b;
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
        color: #d97706;
    }
    .venta-toggle-btn i { display: block; font-size: 20px; margin-bottom: 4px; }

    /* === CLIENTE === */
    .cliente-select-wrapper { position: relative; }
    .form-select, .form-control {
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        padding: 10px 14px;
        font-size: 14px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-select:focus, .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
    }
    .form-label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    /* Nuevo cliente expandible */
    #form_nuevo_cliente {
        background: #f8faff;
        border-radius: 12px;
        padding: 14px;
        border: 1.5px dashed #93c5fd;
        margin-top: 12px;
    }
    #form_nuevo_cliente .nc-title {
        font-size: 13px;
        font-weight: 700;
        color: #3b82f6;
        margin-bottom: 12px;
        display: flex; align-items: center; gap: 6px;
    }
    .input-group-sm .btn {
        border-radius: 0 8px 8px 0;
        border: 1.5px solid #e2e8f0;
    }

    /* === SEARCH BOX === */
    .search-box-wrapper {
        position: relative;
        margin-bottom: 12px;
    }
    .search-box-wrapper i {
        position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
        color: #94a3b8; font-size: 16px; pointer-events: none;
    }
    .search-box-wrapper input {
        padding-left: 38px;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        font-size: 14px;
        width: 100%;
        padding-top: 11px; padding-bottom: 11px;
    }
    .search-box-wrapper input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
        outline: none;
    }

    /* === PRODUCT ITEMS === */
    .product-item {
        background: white;
        border-radius: 14px;
        padding: 14px;
        margin-bottom: 10px;
        border: 1.5px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.2s;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .product-item:hover { border-color: #3b82f6; box-shadow: 0 4px 16px rgba(59,130,246,0.1); }
    .product-item.in-cart {
        border-color: #10b981;
        background: linear-gradient(135deg, #f0fdf4, white);
    }
    .prod-icon {
        width: 44px; height: 44px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        color: white; font-size: 18px;
    }
    .prod-info { flex: 1; min-width: 0; }
    .prod-name {
        font-weight: 700; font-size: 13.5px; color: #1e293b;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .prod-prices { display: flex; gap: 8px; margin-top: 4px; flex-wrap: wrap; }
    .price-tag {
        font-size: 11px; font-weight: 600; padding: 2px 7px;
        border-radius: 6px;
    }
    .price-tag.contado { background: #ecfdf5; color: #059669; }
    .price-tag.credito { background: #fffbeb; color: #d97706; }
    .stock-tag {
        font-size: 10px; font-weight: 700; padding: 2px 7px;
        border-radius: 6px; background: #eff6ff; color: #3b82f6;
    }
    .qty-controls {
        display: flex; align-items: center; gap: 8px; flex-shrink: 0;
    }
    .qty-btn {
        width: 30px; height: 30px;
        border-radius: 8px; border: none;
        background: #f1f5f9; color: #475569;
        font-weight: 700; font-size: 15px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.15s; line-height: 1;
    }
    .qty-btn:hover { background: #e2e8f0; }
    .qty-btn.plus:hover { background: #dcfce7; color: #16a34a; }
    .qty-btn.minus:hover { background: #fee2e2; color: #dc2626; }
    .qty-display {
        font-weight: 800; font-size: 16px; color: #1e293b;
        min-width: 24px; text-align: center;
    }

    /* No stock */
    .empty-products {
        text-align: center; padding: 40px 20px;
        background: white; border-radius: 16px;
    }
    .empty-products i { font-size: 50px; color: #cbd5e1; }
    .empty-products p { color: #94a3b8; margin-top: 10px; font-size: 14px; }

    /* === BOTTOM BAR === */
    .cart-bar {
        position: fixed;
        bottom: 0; left: 0; right: 0;
        background: white;
        border-top: 1px solid #e2e8f0;
        padding: 12px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 1000;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
    }
    .cart-bar .total-section small { color: #94a3b8; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .cart-bar .total-section h3 { margin: 0; font-weight: 800; font-size: 24px; color: #1e293b; }
    .btn-checkout {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white; border: none;
        border-radius: 14px; padding: 13px 24px;
        font-weight: 700; font-size: 15px;
        display: flex; align-items: center; gap: 8px;
        transition: all 0.2s; box-shadow: 0 4px 15px rgba(17,153,142,0.3);
    }
    .btn-checkout:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(17,153,142,0.4); }
    .btn-checkout:disabled { background: #e2e8f0; color: #94a3b8; box-shadow: none; transform: none; }

    /* === MODAL === */
    .modal-content { border: none; border-radius: 24px !important; overflow: hidden; }
    .modal-header-grad {
        background: linear-gradient(135deg, #0f2027, #2c5364);
        padding: 20px 24px; border: none; color: white;
    }
    .modal-header-grad .modal-title { font-weight: 700; font-size: 16px; }
    .modal-body { padding: 24px; }
    .modal-footer-light { background: #f8fafc; padding: 16px 24px; border: none; }

    .total-badge {
        background: linear-gradient(135deg, #11998e, #38ef7d);
        color: white; border-radius: 16px; padding: 16px 20px;
        text-align: center; margin-bottom: 20px;
    }
    .total-badge small { opacity: 0.8; font-size: 12px; font-weight: 600; display: block; margin-bottom: 2px; }
    .total-badge h2 { margin: 0; font-weight: 800; font-size: 32px; }

    .form-group-modern { margin-bottom: 16px; }
    .input-modern {
        border: 1.5px solid #e2e8f0; border-radius: 12px;
        padding: 12px 14px; font-size: 14px; width: 100%;
        transition: all 0.2s;
    }
    .input-modern:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
        outline: none;
    }
    .select-modern {
        border: 1.5px solid #e2e8f0; border-radius: 12px;
        padding: 12px 14px; font-size: 14px; width: 100%;
        background: white; appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 12px center; background-repeat: no-repeat; background-size: 16px;
        cursor: pointer; transition: all 0.2s;
    }
    .select-modern:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
        outline: none;
    }

    .btn-confirm {
        background: linear-gradient(135deg, #11998e, #38ef7d);
        color: white; border: none; border-radius: 14px;
        padding: 14px 28px; font-weight: 700; font-size: 15px;
        width: 100%; transition: all 0.2s;
    }
    .btn-confirm:hover { transform: translateY(-1px); }
    .btn-confirm:disabled { background: #e2e8f0; color: #94a3b8; }

    /* === SUCCESS MODAL === */
    .success-icon { font-size: 80px; color: #10b981; animation: popIn 0.4s ease-out; }
    @keyframes popIn {
        0% { transform: scale(0); opacity: 0; }
        70% { transform: scale(1.1); }
        100% { transform: scale(1); opacity: 1; }
    }

    /* === VUELTO CALC === */
    .vuelto-display {
        background: #f0fdf4; border-radius: 10px; padding: 10px 14px;
        display: flex; justify-content: space-between; align-items: center;
        border: 1.5px solid #bbf7d0; margin-top: 10px;
    }
    .vuelto-display span { font-size: 12px; font-weight: 600; color: #166534; }
    .vuelto-display strong { font-size: 16px; font-weight: 800; color: #15803d; }

    .bank-wrapper { display: none; }

    .cart-count-badge {
        background: #ef4444; color: white; font-size: 10px; font-weight: 800;
        width: 18px; height: 18px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        margin-left: 4px;
    }
</style>
@endsection

@section('contenido')
<div class="mobile-wrapper">

    {{-- HEADER --}}
    <div class="page-header">
        <a href="{{ route('vendedor.dashboard') }}" class="back-btn">
            <i class="mdi mdi-arrow-left" style="font-size:18px;"></i>
        </a>
        <div class="vendor-info">
            <small><i class="mdi mdi-truck me-1"></i>Ventas Móviles</small>
            <h4>Registrar Venta</h4>
        </div>
    </div>

    {{-- TIPO DE VENTA --}}
    <div class="section-card">
        <div class="section-title"><i class="mdi mdi-cash-multiple"></i> Tipo de Venta</div>
        <div class="venta-toggle">
            <div class="venta-toggle-btn active-contado" id="btn_contado" onclick="setTipoVenta(1)">
                <i class="mdi mdi-cash"></i>
                Contado
            </div>
            <div class="venta-toggle-btn" id="btn_credito" onclick="setTipoVenta(2)">
                <i class="mdi mdi-credit-card-outline"></i>
                Crédito
            </div>
        </div>
        <input type="hidden" id="tipo_venta" value="1">
    </div>

    {{-- CLIENTE --}}
    <div class="section-card">
        <div class="section-title"><i class="mdi mdi-account-circle"></i> Cliente</div>
        
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label mb-0">Seleccionar Cliente en Ruta</label>
            <button type="button" class="btn btn-sm btn-outline-primary" id="btn_toggle_nuevo_cliente" style="border-radius: 8px; font-weight: 700; font-size: 11px;">
                <i class="mdi mdi-account-plus me-1"></i> NUEVO CLIENTE
            </button>
        </div>

        <div class="form-group-modern" id="wrapper_select_cliente">
            <select class="select-modern" id="select_cliente">
                <option value="">-- Seleccionar Cliente --</option>
                @foreach($clientes as $cli)
                    <option value="{{ $cli->id }}"
                            data-documento="{{ $cli->documento }}"
                            data-nombre="{{ $cli->razon_social }}"
                            data-direccion="{{ $cli->dire_per }}"
                            data-telefono="{{ $cli->telefono }}"
                            data-sector="{{ $cli->id_sector }}"
                            data-tipodoc="{{ $cli->tipo_doc }}">
                        {{ $cli->razon_social }} · {{ $cli->documento }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Formulario Nuevo Cliente --}}
        <div id="form_nuevo_cliente" class="d-none">
            <div class="nc-title"><i class="mdi mdi-account-plus"></i> Registro Rápido de Cliente</div>
            <div class="row g-2">
                <div class="col-5">
                    <label class="form-label">Tipo Doc.</label>
                    <select class="select-modern" id="tipo_doc_nuevo">
                        @foreach($tipo_documento as $td)
                            <option value="{{ $td->id }}">{{ $td->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-7">
                    <label class="form-label">Número Doc.</label>
                    <div class="input-group">
                        <input type="text" class="form-control input-modern" id="doc_nuevo" placeholder="Ej: 12345678" style="border-radius: 12px 0 0 12px;">
                        <button class="btn btn-primary btn-sm" type="button" id="btn_buscar_api" style="border-radius: 0 12px 12px 0; padding: 0 12px;">
                            <i class="mdi mdi-magnify"></i>
                        </button>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Nombre / Razón Social</label>
                    <input type="text" class="input-modern" id="nombre_nuevo" placeholder="Nombre completo">
                </div>
                <div class="col-12">
                    <label class="form-label">Dirección</label>
                    <input type="text" class="input-modern" id="direccion_nuevo" placeholder="Av. / Jr. / Calle...">
                </div>
                <div class="col-6">
                    <label class="form-label">Celular</label>
                    <input type="text" class="input-modern" id="celular_nuevo" placeholder="9XXXXXXXX">
                </div>
                <div class="col-6">
                    <label class="form-label">Sector</label>
                    <select class="select-modern" id="sector_nuevo">
                        @foreach($sectores as $sec)
                            <option value="{{ $sec->id }}">{{ $sec->nomb_sec }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- PRODUCTOS --}}
    <div class="section-title" style="padding: 0 4px; margin-bottom: 10px;">
        <i class="mdi mdi-package-variant-closed" style="font-size:16px; color:#64748b;"></i>
        <span style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:#64748b;">
            Productos en Ruta
        </span>
        <span id="cart_count_badge" class="cart-count-badge d-none">0</span>
    </div>

    <div class="search-box-wrapper">
        <i class="mdi mdi-magnify"></i>
        <input type="text" id="buscar_producto" placeholder="Buscar producto...">
    </div>

    <div id="productos_lista">
        @if($productos->isEmpty())
            <div class="empty-products">
                <i class="mdi mdi-inbox-remove-outline"></i>
                <p>No hay stock disponible en la unidad móvil.</p>
            </div>
        @else
            @foreach($productos as $prod)
                <div class="product-item"
                     data-id="{{ $prod->id }}"
                     data-nombre="{{ $prod->nomb_pro }}"
                     data-precio-contado="{{ $prod->precio_contado }}"
                     data-precio-credito="{{ $prod->precio_credito }}"
                     data-stock="{{ $prod->stock }}">
                    <div class="prod-icon">
                        <i class="mdi mdi-package"></i>
                    </div>
                    <div class="prod-info">
                        <div class="prod-name">{{ $prod->nomb_pro }}</div>
                        <div class="prod-prices">
                            <span class="price-tag contado">S/ {{ number_format($prod->precio_contado, 2) }}</span>
                            <span class="price-tag credito">S/ {{ number_format($prod->precio_credito, 2) }}</span>
                            <span class="stock-tag"><i class="mdi mdi-cube-outline"></i> {{ $prod->stock }}</span>
                        </div>
                    </div>
                    <div class="qty-controls">
                        <button class="qty-btn minus btn-minus" type="button">−</button>
                        <span class="qty-display">0</span>
                        <button class="qty-btn plus btn-plus" type="button">+</button>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

</div>

{{-- BARRA INFERIOR --}}
<div class="cart-bar">
    <div class="total-section">
        <small>Total a Cobrar</small>
        <h3 id="cart_total_display">S/ 0.00</h3>
    </div>
    <button class="btn-checkout" id="btn_continuar_venta" disabled>
        Continuar <i class="mdi mdi-arrow-right"></i>
    </button>
</div>

{{-- MODAL FINALIZAR VENTA --}}
<div class="modal fade" id="modalFinalizarVenta" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header-grad d-flex justify-content-between align-items-center">
                <div>
                    <div class="modal-title"><i class="mdi mdi-cash-register me-2"></i>Finalizar y Cobrar</div>
                    <small style="opacity:0.7; font-size:12px;">Confirme los datos antes de procesar</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form_finalizar_venta">
                <div class="modal-body">
                    <div class="total-badge">
                        <small>Monto Total</small>
                        <h2 id="modal_total_display">S/ 0.00</h2>
                    </div>

                    <div class="form-group-modern">
                        <label class="form-label">Comprobante</label>
                        <select class="select-modern" name="documento" id="select_documento" required>
                            @foreach($comprobantes as $comp)
                                <option value="{{ $comp->id }}" {{ $comp->id == 5 ? 'selected' : '' }}>{{ $comp->descripcion }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="forma_pago_wrapper">
                        <div class="form-group-modern">
                            <label class="form-label">Forma de Pago</label>
                            <select class="select-modern" name="forma_pago" id="select_forma_pago" required>
                                @foreach($forma_pagos as $fp)
                                    <option value="{{ $fp->id }}">{{ $fp->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div id="efectivo_calculo_wrapper">
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label">Monto Recibido (S/)</label>
                                <input type="number" step="0.01" class="input-modern" name="total_recibido" id="input_recibido" value="0">
                            </div>
                        </div>
                        <div class="vuelto-display">
                            <span><i class="mdi mdi-arrow-left-right me-1"></i>Vuelto / Cambio</span>
                            <strong id="vuelto_display">S/ 0.00</strong>
                        </div>
                        <input type="hidden" name="vuelto" id="input_vuelto" value="0">
                    </div>

                    {{-- SECCIÓN CRÉDITO - solo visible cuando tipo_venta == 2 --}}
                    <div id="credito_config_section" class="d-none mt-3">
                        <hr style="border-top: 1px solid rgba(255,255,255,0.1);">
                        <h6 class="text-white mb-3"><i class="mdi mdi-calendar-clock me-1"></i>Configuración de Crédito</h6>

                        <div class="form-group-modern">
                            <label class="form-label">Concepto de Crédito</label>
                            <select class="select-modern" name="concepto_credito_id" id="select_concepto_credito">
                                <option value="">-- Seleccionar --</option>
                            </select>
                        </div>

                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label">N° Cuotas</label>
                                <select class="select-modern" name="num_cuotas" id="select_num_cuotas">
                                    <option value="1">1 cuota</option>
                                    <option value="2">2 cuotas</option>
                                    <option value="3">3 cuotas</option>
                                    <option value="4">4 cuotas</option>
                                    <option value="6">6 cuotas</option>
                                    <option value="12">12 cuotas</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Fecha Primera Cuota</label>
                                <input type="date" class="input-modern" name="fecha_primera_cuota" id="input_fecha_primera_cuota">
                            </div>
                        </div>

                        <div class="row g-2 mt-2">
                            <div class="col-4">
                                <label class="form-label">Inicial (S/)</label>
                                <input type="number" step="0.01" class="input-modern" name="cuota_inicial" id="input_cuota_inicial" value="0" min="0">
                            </div>
                            <div class="col-4" id="wrapper_inicial_forma_pago" style="display:none;">
                                <label class="form-label">Forma Pago Inicial</label>
                                <select class="select-modern" name="inicial_forma_pago" id="select_inicial_forma_pago">
                                    @foreach($forma_pagos as $fp)
                                        <option value="{{ $fp->id }}">{{ $fp->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4" id="wrapper_inicial_operacion" style="display:none;">
                                <label class="form-label">N° Operación Inicial</label>
                                <input type="text" class="input-modern" name="inicial_numero_operacion" id="input_inicial_operacion">
                            </div>
                        </div>

                        <div id="cuotas_preview" class="mt-3">
                            {{-- Generado dinámicamente por JavaScript --}}
                        </div>
                        <input type="hidden" name="cuotas_data" id="input_cuotas_data">
                    </div>
                </div>
                <div class="modal-footer-light">
                    <button type="button" class="btn btn-outline-secondary w-100 mb-2" data-bs-dismiss="modal">Atrás</button>
                    <button type="submit" class="btn-confirm" id="btn_guardar_venta_final">
                        <i class="mdi mdi-check-circle me-2"></i> Confirmar Venta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL CRÉDITO ACTIVO --}}
<div class="modal fade" id="modalCreditoActivo" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header-grad d-flex justify-content-between align-items-center">
                <div>
                    <div class="modal-title"><i class="mdi mdi-alert-circle me-2"></i>Cliente con Crédito Activo</div>
                    <small style="opacity:0.7; font-size:12px;">Este cliente ya tiene un crédito en curso</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <div class="alert alert-warning">
                    <i class="mdi mdi-information me-1"></i>
                    <span id="credito_warning_msg"></span>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card" style="border-radius: 10px;">
                            <div class="card-body">
                                <h6 class="fw-bold text-dark"><i class="mdi mdi-file-document me-1"></i>Datos del Crédito</h6>
                                <table class="table table-sm mt-2" style="font-size: 12px;">
                                    <tr><td class="text-muted">Código:</td><td class="fw-bold" id="credito_codigo"></td></tr>
                                    <tr><td class="text-muted">Sede:</td><td id="credito_sede"></td></tr>
                                    <tr><td class="text-muted">Fecha:</td><td id="credito_fecha"></td></tr>
                                    <tr><td class="text-muted">Comprobante:</td><td id="credito_comprobante"></td></tr>
                                    <tr><td class="text-muted">Monto Total:</td><td class="fw-bold text-primary" id="credito_monto"></td></tr>
                                    <tr><td class="text-muted">Saldo Pendiente:</td><td class="fw-bold text-danger" id="credito_saldo"></td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card" style="border-radius: 10px;">
                            <div class="card-body">
                                <h6 class="fw-bold text-dark"><i class="mdi mdi-cash-multiple me-1"></i>Cuotas Pendientes</h6>
                                <table class="table table-sm mt-2" style="font-size: 12px;">
                                    <thead><tr><th>#</th><th>Monto</th><th>Vencimiento</th><th>Estado</th></tr></thead>
                                    <tbody id="credito_cuotas_table"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <h6 class="fw-bold text-dark"><i class="mdi mdi-package me-1"></i>Productos de la Venta Anterior</h6>
                    <table class="table table-sm" style="font-size: 12px;">
                        <thead><tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Importe</th></tr></thead>
                        <tbody id="credito_productos_table"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer-light">
                <button type="button" class="btn btn-outline-secondary w-100 mb-2" data-bs-dismiss="modal">Cancelar Venta</button>
                <button type="button" class="btn btn-warning w-100" id="btn_proceder_credito">
                    <i class="mdi mdi-check-circle me-2"></i> Proceder con Venta al Crédito de Todas Formas
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL ÉXITO --}}
<div class="modal fade" id="modalVentaExitosa" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="mb-3">
                    <i class="mdi mdi-check-circle success-icon"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">¡Venta Registrada!</h4>
                <p class="text-muted mb-4">La venta fue guardada y queda pendiente de liquidación.</p>
                <div class="d-grid gap-2">
                    <a href="#" id="link_ticket" target="_blank" class="btn btn-primary btn-lg" style="border-radius: 12px;">
                        <i class="mdi mdi-receipt me-2"></i> Ver / Imprimir Ticket
                    </a>
                    <a href="#" id="link_contrato" target="_blank" class="btn btn-info btn-lg d-none" style="border-radius: 12px;">
                        <i class="mdi mdi-file-document me-2"></i> Ver Contrato
                    </a>
                    <button type="button" class="btn btn-outline-success" style="border-radius: 12px;" onclick="window.location.reload();">
                        <i class="mdi mdi-plus me-1"></i> Registrar Otra Venta
                    </button>
                    <a href="{{ route('vendedor.dashboard') }}" class="btn btn-light" style="border-radius: 12px;">
                        Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $(".loader").fadeOut("slow");

    // Inicializar Select2 para Selección de Cliente en Ruta
    $('#select_cliente').select2({
        placeholder: "-- Seleccionar Cliente --",
        allowClear: true,
        width: '100%'
    });

    // ============================
    // TIPO DE VENTA
    // ============================
    function setTipoVenta(tipo) {
        $('#tipo_venta').val(tipo);
        if (tipo == 1) {
            $('#btn_contado').addClass('active-contado').removeClass('active-credito');
            $('#btn_credito').removeClass('active-contado active-credito');
            $('#credito_config_section').addClass('d-none');
            // Mostrar forma de pago y efectivo para contado
            $('#forma_pago_wrapper').show();
            $('#efectivo_calculo_wrapper').show();
            // Resetear forma de pago a efectivo
            $('#select_forma_pago').val('1');
        } else {
            $('#btn_credito').addClass('active-credito').removeClass('active-contado');
            $('#btn_contado').removeClass('active-contado active-credito');
            $('#credito_config_section').removeClass('d-none');
            // Ocultar forma de pago y efectivo para crédito
            $('#forma_pago_wrapper').hide();
            $('#efectivo_calculo_wrapper').hide();
            fetchCreditoParametros();
 }
        updateTotals();
    }
    window.setTipoVenta = setTipoVenta;
    // Mostrar/ocultar campos de pago inicial segun el valor
    $(document).on("input", "#input_cuota_inicial", function() {
        var inicial = parseFloat($(this).val()) || 0;
        if (inicial > 0) {
            $("#wrapper_inicial_forma_pago").show();
            $("#wrapper_inicial_operacion").show();
        } else {
            $("#wrapper_inicial_forma_pago").hide();
            $("#wrapper_inicial_operacion").hide();
        }
    });
    // ============================
    // CRÉDITO - Funciones
    // ============================
    function fetchCreditoParametros() {
        $.get("{{ route('vendedor.credito.parametros') }}", function(data) {
            // Poblar conceptos
            let opts = '<option value="">-- Seleccionar --</option>';
            data.conceptos.forEach(function(c) {
                opts += '<option value="' + c.id + '">' + c.name + '</option>';
            });
            $('#select_concepto_credito').html(opts);

            // Establecer fecha primera cuota por defecto (30 días)
            let primeraFecha = new Date();
            primeraFecha.setDate(primeraFecha.getDate() + 30);
            $('#input_fecha_primera_cuota').val(primeraFecha.toISOString().split('T')[0]);

            generateCuotasPreview();
        });
    }

    function generateCuotasPreview() {
        let numCuotas = parseInt($('#select_num_cuotas').val()) || 1;
        let totalVenta = parseFloat($('#modal_total_display').text().replace('S/ ', '').replace(',', '')) || 0;
        let inicial = parseFloat($('#input_cuota_inicial').val()) || 0;
        let capital = Math.max(0, totalVenta - inicial);
        let montoCuota = (capital / numCuotas).toFixed(2);
        let primeraFecha = $('#input_fecha_primera_cuota').val() || new Date().toISOString().split('T')[0];

        let html = '<div class="text-white mb-2" style="font-size:12px;"><i class="mdi mdi-format-list-numbered me-1"></i> Cronograma de Cuotas</div>';
        html += '<table class="table table-sm table-dark" style="font-size:11px;"><thead><tr><th>#</th><th>Monto</th><th>Fecha Vencimiento</th></tr></thead><tbody>';

        // Si hay inicial, mostrar como cuota 0
        if (inicial > 0) {
            let fechaHoy = new Date().toISOString().split('T')[0];
            html += '<tr>';
            html += '<td>Ini.</td>';
            html += '<td>S/ ' + inicial.toFixed(2) + '</td>';
            html += '<td><input type="date" class="input-modern" name="cuota_fecha_0" value="' + fechaHoy + '" style="padding: 2px 6px; font-size: 11px; width: 110px;"></td>';
            html += '</tr>';
        }

        let currentDate = new Date(primeraFecha);
        for (let i = 1; i <= numCuotas; i++) {
            let fechaFormateada = currentDate.toISOString().split('T')[0];
            html += '<tr>';
            html += '<td>Cuota ' + i + '</td>';
            html += '<td>S/ ' + montoCuota + '</td>';
            html += '<td><input type="date" class="input-modern" name="cuota_fecha_' + i + '" value="' + fechaFormateada + '" style="padding: 2px 6px; font-size: 11px; width: 110px;"></td>';
            html += '</tr>';
            currentDate.setMonth(currentDate.getMonth() + 1);
        }
        html += '</tbody></table>';

        $('#cuotas_preview').html(html);
    }

    // Eventos para regenerar preview de cuotas
    $(document).on('change', '#select_num_cuotas', generateCuotasPreview);
    $(document).on('change', '#input_fecha_primera_cuota', generateCuotasPreview);
    $(document).on('change', '#input_cuota_inicial', generateCuotasPreview);

    // Mostrar/ocultar campos de pago inicial según el valor
    $(document).on('input', '#input_cuota_inicial', function() {
        let inicial = parseFloat($(this).val()) || 0;
        if (inicial > 0) {
            $('#wrapper_inicial_forma_pago').show();
            $('#wrapper_inicial_operacion').show();
        } else {
            $('#wrapper_inicial_forma_pago').hide();
            $('#wrapper_inicial_operacion').hide();
        }
    });

    // Botón proceder con crédito activo
    $(document).on('click', '#btn_proceder_credito', function() {
        let originalData = $(this).data('original_data');
        // Asegurar que vendedor esté presente
        if (!originalData.vendedor && currentVendedorId) {
            originalData.vendedor = currentVendedorId;
        }
        originalData.confirmar_credito = true;

        $('#modalCreditoActivo').modal('hide');
        $('#btn_guardar_venta_final').prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-2"></i>Guardando...');

        $.ajax({
            url: "{{ route('vendedor.venta.guardar') }}",
            type: 'POST',
            data: originalData,
            success: function(response) {
                if (response.respuesta === 'ok') {
                    $('#link_ticket').attr('href', "{{ url('venta/ticket') }}/" + response.id);
                    if (response.credito_id) { $("#link_contrato").attr("href", "{{ url("creditos/contrato") }}/" + response.credito_id).removeClass("d-none"); } else { $("#link_contrato").addClass("d-none"); }
                    $('#modalVentaExitosa').modal('show');
                } else {
                    $('#btn_guardar_venta_final').prop('disabled', false).html('<i class="mdi mdi-check-circle me-2"></i>Confirmar Venta');
                    alert('Error: ' + (response.mensaje || response.error));
                }
            },
            error: function(xhr) {
                $('#btn_guardar_venta_final').prop('disabled', false).html('<i class="mdi mdi-check-circle me-2"></i>Confirmar Venta');
                let msg = 'Error en el servidor.';
                try { let r = xhr.responseJSON; msg = r.error || r.mensaje || msg; } catch(ex) {}
                alert(msg);
            }
        });
    });

    // Función para recolectar datos de cuotas
    function collectCuotasData() {
        let numCuotas = parseInt($('#select_num_cuotas').val()) || 1;
        let totalVenta = parseFloat($('#modal_total_display').text().replace('S/ ', '').replace(',', '')) || 0;
        let inicial = parseFloat($('#input_cuota_inicial').val()) || 0;
        let capital = Math.max(0, totalVenta - inicial);
        let montoCuota = (capital / numCuotas).toFixed(2);
        let cuotasData = [];

        // Si hay inicial, agregarla como cuota 0
        if (inicial > 0) {
            let fechaInicial = $('input[name="cuota_fecha_0"]').val() || new Date().toISOString().split('T')[0];
            cuotasData.push({
                numero: 0,
                monto: inicial.toFixed(2),
                fecha_vencimiento: fechaInicial
            });
        }

        for (let i = 1; i <= numCuotas; i++) {
            let fecha = $('input[name="cuota_fecha_' + i + '"]').val();
            cuotasData.push({
                numero: i,
                monto: montoCuota,
                fecha_vencimiento: fecha
            });
        }
        return JSON.stringify(cuotasData);
    }

    // ============================
    // CLIENTE
    // ============================
    let isNuevoClienteMode = false;

    $('#btn_toggle_nuevo_cliente').on('click', function() {
        isNuevoClienteMode = !isNuevoClienteMode;
        if (isNuevoClienteMode) {
            $(this).removeClass('btn-outline-primary').addClass('btn-primary').html('<i class="mdi mdi-close me-1"></i> CANCELAR');
            $('#form_nuevo_cliente').removeClass('d-none');
            $('#wrapper_select_cliente').addClass('d-none');
            $('#select_cliente').val('').trigger('change');
        } else {
            $(this).removeClass('btn-primary').addClass('btn-outline-primary').html('<i class="mdi mdi-account-plus me-1"></i> NUEVO CLIENTE');
            $('#form_nuevo_cliente').addClass('d-none');
            $('#wrapper_select_cliente').removeClass('d-none');
        }
    });

    // Buscar en API RUC/DNI
    $('#btn_buscar_api').on('click', function() {
        let doc = $('#doc_nuevo').val().trim();
        let tipo = $('#tipo_doc_nuevo').val();
        if (!doc) { alert('Ingrese un número de documento.'); return; }
        $(".loader").fadeIn("fast");
        $.ajax({
            url: "{{ url('ventas/consultar_dni_ruc') }}",
            type: 'POST',
            data: { _token: "{{ csrf_token() }}", tipo_documento: tipo, num_doc: doc },
            success: function(res) {
                $(".loader").fadeOut("fast");
                if (res.exception === 'existe_base_datos') {
                    alert('Cliente ya existe en la base de datos. Selecciónelo desde el listado.');
                    $('#btn_toggle_nuevo_cliente').click(); // Volver al listado
                    // Seleccionar automáticamente si existe en el select
                    $('#select_cliente option').each(function() {
                        if ($(this).data('documento') == doc) {
                            $('#select_cliente').val($(this).val()).trigger('change');
                        }
                    });
                } else if (res.nombres || (res.original && res.original.nombres)) {
                    let nombres = res.nombres || res.original.nombres;
                    let direccion = res.direccion || (res.original && res.original.direccion) || '';
                    $('#nombre_nuevo').val(nombres);
                    $('#direccion_nuevo').val(direccion);
                } else {
                    alert('No se encontraron resultados para este documento.');
                }
            },
            error: function() { $(".loader").fadeOut("fast"); alert('Error al consultar el documento.'); }
        });
    });

    // ============================
    // CARRITO
    // ============================
    let cart = {};
    // Variable global para el usuario vendedor actual
    var currentVendedorId = {{ $usuario->id ?? 'null' }};

    // Buscar productos
    $('#buscar_producto').on('input', function() {
        let value = $(this).val().toLowerCase();
        $('#productos_lista .product-item').each(function() {
            let nombre = $(this).attr('data-nombre').toLowerCase();
            $(this).toggle(nombre.includes(value));
        });
    });

    // Botón +
    $(document).on('click', '.btn-plus', function() {
        let item = $(this).closest('.product-item');
        let id = item.attr('data-id');
        let stock = parseInt(item.attr('data-stock'));
        let priceContado = parseFloat(item.attr('data-precio-contado')) || 0;
        let priceCredito = parseFloat(item.attr('data-precio-credito')) || 0;
        let nombre = item.attr('data-nombre');

        if (!cart[id]) {
            cart[id] = { id, nombre, qty: 0, stock, priceContado, priceCredito };
        }
        if (cart[id].qty < stock) {
            cart[id].qty++;
            item.find('.qty-display').text(cart[id].qty);
            item.addClass('in-cart');
            updateTotals();
        } else {
            // Shake animation
            item.css('border-color', '#ef4444');
            setTimeout(() => item.css('border-color', ''), 800);
        }
    });

    // Botón -
    $(document).on('click', '.btn-minus', function() {
        let item = $(this).closest('.product-item');
        let id = item.attr('data-id');
        if (cart[id] && cart[id].qty > 0) {
            cart[id].qty--;
            item.find('.qty-display').text(cart[id].qty);
            if (cart[id].qty === 0) {
                delete cart[id];
                item.removeClass('in-cart');
            }
            updateTotals();
        }
    });

    function updateTotals() {
        let tipoVenta = $('#tipo_venta').val();
        let total = 0;
        let count = 0;
        for (let id in cart) {
            let p = cart[id];
            let price = (tipoVenta === '1') ? p.priceContado : p.priceCredito;
            total += p.qty * price;
            count += p.qty;
        }
        $('#cart_total_display').text('S/ ' + total.toFixed(2));
        $('#modal_total_display').text('S/ ' + total.toFixed(2));
        $('#input_recibido').val(total.toFixed(2));
        recalcVuelto();

        if (count > 0) {
            $('#btn_continuar_venta').prop('disabled', false);
            $('#cart_count_badge').text(count).removeClass('d-none');
        } else {
            $('#btn_continuar_venta').prop('disabled', true);
            $('#cart_count_badge').addClass('d-none');
        }
    }

    // ============================
    // CONTINUAR
    // ============================
    $('#btn_continuar_venta').on('click', function() {
        if (!isNuevoClienteMode && !$('#select_cliente').val()) {
            alert('Debe seleccionar un cliente del listado o registrar uno nuevo.');
            return;
        }
        updateTotals();
        $('#modalFinalizarVenta').modal('show');
    });

    // ============================
    // FORMA DE PAGO
    // ============================
    $('#select_forma_pago').on('change', function() {
        let val = $(this).val();
        // Si es efectivo (id=1) mostrar monto recibido y vuelto, sino ocultar
        if (val === '1') {
            $('#efectivo_calculo_wrapper').show();
        } else {
            $('#efectivo_calculo_wrapper').hide();
        }
    });

    // Vuelto
    function recalcVuelto() {
        let recibido = parseFloat($('#input_recibido').val()) || 0;
        let totalText = $('#modal_total_display').text().replace('S/ ', '');
        let total = parseFloat(totalText) || 0;
        let vuelto = recibido - total;
        let vueltoVal = vuelto >= 0 ? vuelto : 0;
        $('#vuelto_display').text('S/ ' + vueltoVal.toFixed(2));
        $('#input_vuelto').val(vueltoVal.toFixed(2));
    }
    $('#input_recibido').on('input', recalcVuelto);

    // ============================
    // ENVIAR VENTA
    // ============================
    $('#form_finalizar_venta').on('submit', function(e) {
        e.preventDefault();

        let clienteVal = $('#select_cliente').val();
        let numDoc = '', nomCli = '', dirCli = '', telCli = '', tipoDoc = '1', sectorId = '', correo = '';

        if (isNuevoClienteMode) {
            numDoc = $('#doc_nuevo').val().trim();
            nomCli = $('#nombre_nuevo').val().trim();
            dirCli = $('#direccion_nuevo').val().trim();
            telCli = $('#celular_nuevo').val().trim();
            tipoDoc = $('#tipo_doc_nuevo').val();
            sectorId = $('#sector_nuevo').val();
            if (!numDoc || !nomCli) {
                alert('Debe completar el número de documento y nombre del nuevo cliente.');
                return;
            }
        } else {
            let opt = $('#select_cliente option:selected');
            if (!opt.val()) {
                alert('Debe seleccionar un cliente del listado o registrar uno nuevo.');
                return;
            }
            numDoc   = opt.attr('data-documento') || '';
            nomCli   = opt.attr('data-nombre') || '';
            dirCli   = opt.attr('data-direccion') || '';
            telCli   = opt.attr('data-telefono') || '';
            tipoDoc  = opt.attr('data-tipodoc') || '1';
            sectorId = opt.attr('data-sector') || '';
        }

        let tipoVenta = $('#tipo_venta').val();
        let quanty = [], idproducto = [], priceproducto = [], nameproducto = [], importe = [], ubicacion = [];

        for (let id in cart) {
            let p = cart[id];
            let price = (tipoVenta === '1') ? p.priceContado : p.priceCredito;
            quanty.push(p.qty);
            idproducto.push(p.id);
            priceproducto.push(price);
            nameproducto.push(p.nombre);
            importe.push((p.qty * price).toFixed(2));
            ubicacion.push("{{ $ubicacion_id }}");
        }

        if (quanty.length === 0) {
            alert('No hay productos en el carrito.'); return;
        }

        let totalVentaVal = $('#modal_total_display').text().replace('S/ ', '');

        let dataToSend = {
            _token: "{{ csrf_token() }}",
            es_movil: 1,
            documento: $('#select_documento').val(),
            tipoDocumentoIdentidad: tipoDoc,
            numeroDocumento: numDoc,
            nombre_cliente: nomCli,
            direccion_cliente: dirCli,
            celular_cliente: telCli,
            correo_cliente: correo,
            sectores: sectorId,
            forma_pago: $('#select_forma_pago').val(),
            tipo_venta: tipoVenta,
            total_venta: totalVentaVal,
            total_recibido: $('#input_recibido').val(),
            vuelto: $('#input_vuelto').val(),
            fecha_venta: "{{ date('Y-m-d') }}",
            vendedor: currentVendedorId,
            quanty: quanty,
            idproducto: idproducto,
            priceproducto: priceproducto,
            nameproducto: nameproducto,
            importe: importe,
            ubicacion: ubicacion,
            concepto_credito_id: tipoVenta == "2" ? $("#select_concepto_credito").val() : "",
            cuotas_data: tipoVenta == "2" ? collectCuotasData() : "[]",
            cuota_inicial: tipoVenta == "2" ? $('#input_cuota_inicial').val() || 0 : 0,
            inicial_forma_pago: tipoVenta == "2" ? $('#select_inicial_forma_pago').val() || '' : '',
            inicial_numero_operacion: tipoVenta == "2" ? $('#input_inicial_operacion').val() || '' : ''
        };

        $('#btn_guardar_venta_final').prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-2"></i>Guardando...');

        $.ajax({
            url: "{{ route('vendedor.venta.guardar') }}",
            type: 'POST',
            data: dataToSend,
            success: function(response) {
                if (response.respuesta === 'ok') {
                    $('#modalFinalizarVenta').modal('hide');
                    $('#link_ticket').attr('href', "{{ url('venta/ticket') }}/" + response.id);
                    if (response.credito_id) { $("#link_contrato").attr("href", "{{ url("creditos/contrato") }}/" + response.credito_id).removeClass("d-none"); } else { $("#link_contrato").addClass("d-none"); }
                    $('#modalVentaExitosa').modal('show');
                } else if (response.respuesta === 'warning' && response.credito_activo) {
                    // Mostrar modal de crédito activo
                    $('#btn_guardar_venta_final').prop('disabled', false).html('<i class="mdi mdi-check-circle me-2"></i>Confirmar Venta');
                    $('#modalFinalizarVenta').modal('hide');

                    let cred = response.credito;
                    $('#credito_warning_msg').text(response.mensaje);
                    $('#credito_codigo').text(cred.id);
                    $('#credito_sede').text(cred.sede);
                    $('#credito_fecha').text(cred.fecha_credito);
                    $('#credito_comprobante').text(cred.comprobante || '-');
                    $('#credito_monto').text('S/ ' + parseFloat(cred.monto_total).toFixed(2));
                    $('#credito_saldo').text('S/ ' + parseFloat(cred.saldo_pendiente).toFixed(2));

                    // Cuotas
                    let cuotasHtml = '';
                    cred.cuotas.forEach(function(c) {
                        let estadoClass = c.estado === 'COBRADA' ? 'bg-success' : 'bg-danger';
                        cuotasHtml += '<tr><td>' + (c.numero === 0 ? 'Ini.' : c.numero) + '</td><td>S/ ' + parseFloat(c.monto).toFixed(2) + '</td><td>' + c.fecha_vencimiento + '</td><td><span class="badge ' + estadoClass + '">' + c.estado + '</span></td></tr>';
                    });
                    $('#credito_cuotas_table').html(cuotasHtml);

                    // Productos
                    let prodsHtml = '';
                    cred.productos.forEach(function(p) {
                        prodsHtml += '<tr><td>' + p.nombre + '</td><td>' + p.cantidad + '</td><td>S/ ' + parseFloat(p.precio).toFixed(2) + '</td><td>S/ ' + parseFloat(p.importe).toFixed(2) + '</td></tr>';
                    });
                    $('#credito_productos_table').html(prodsHtml);

                    // Guardar data para proceder
                    $('#btn_proceder_credito').data('original_data', dataToSend);

                    $('#modalCreditoActivo').modal('show');
                } else {
                    $('#btn_guardar_venta_final').prop('disabled', false).html('<i class="mdi mdi-check-circle me-2"></i>Confirmar Venta');
                    alert('Error: ' + (response.mensaje || response.error));
                }
            },
            error: function(xhr) {
                $('#btn_guardar_venta_final').prop('disabled', false).html('<i class="mdi mdi-check-circle me-2"></i>Confirmar Venta');
                let msg = 'Error en el servidor.';
                try { let r = xhr.responseJSON; msg = r.error || r.mensaje || msg; } catch(ex) {}
                alert(msg);
            }
        });
    });
});
</script>
@endsection
