@extends('layouts.main')

@section('title', 'Dashboard Vendedor')

@section('css')
<style>
    .mobile-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        background: #ffffff;
    }
    .mobile-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }
    .gradient-green {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: #ffffff;
    }
    .gradient-blue {
        background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);
        color: #ffffff;
    }
    .gradient-purple {
        background: linear-gradient(135deg, #8a2be2 0%, #4a00e0 100%);
        color: #ffffff;
    }
    .gradient-orange {
        background: linear-gradient(135deg, #f12711 0%, #f5af19 100%);
        color: #ffffff;
    }
    .icon-wrapper {
        width: 55px;
        height: 55px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 15px;
    }
    .quick-action-btn {
        border-radius: 12px;
        padding: 15px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.2s ease;
        border: none;
        width: 100%;
        margin-bottom: 12px;
    }
    .quick-action-btn:active {
        transform: scale(0.97);
    }
    .section-title {
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 20px;
        position: relative;
        padding-left: 15px;
    }
    .section-title::before {
        content: '';
        position: absolute;
        left: 0;
        top: 5px;
        height: 18px;
        width: 4px;
        background-color: #11998e;
        border-radius: 2px;
    }
    .route-item {
        padding: 12px 15px;
        border-radius: 10px;
        background: #f8f9fa;
        margin-bottom: 10px;
        border-left: 4px solid #117A65;
        font-weight: 500;
    }
</style>
@endsection

@section('contenido')
<div class="container-fluid py-3">
    <!-- Mensaje de bienvenida -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="mobile-card p-4 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #1f4068 0%, #162447 100%); color: white;">
                <div>
                    <span style="font-size: 14px; opacity: 0.8; text-transform: uppercase; font-weight: 600;">Ruta de Hoy</span>
                    <h2 class="mb-0 font-weight-bold text-white" style="color: #ffffff; letter-spacing: -0.5px;">¡Hola, {{ $vendedor->nombre }}!</h2>
                    <p class="mb-0 mt-1" style="font-size: 13px; opacity: 0.9;">
                        <i class="mdi mdi-calendar-range me-1"></i> {{ date('d-m-Y') }}
                    </p>
                </div>
                <div class="d-none d-sm-block">
                    <span class="badge bg-soft-light text-white p-2" style="font-size: 12px;">Vendedor Movil</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas si no hay ruta asignada -->
    @if($sectoresAsignados->isEmpty())
        <div class="alert alert-warning border-0 shadow-sm" role="alert" style="border-radius: 12px;">
            <i class="mdi mdi-alert-circle-outline me-2" style="font-size: 18px; vertical-align: middle;"></i>
            <strong>¡Atención!</strong> No tienes sectores asignados para hoy. Solicita tu ruta al administrador.
        </div>
    @endif

    <!-- Estadísticas del día -->
    <h5 class="section-title">Estadísticas Acumuladas (Hoy)</h5>
    <div class="row">
        <!-- Tarjeta Ventas -->
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <div class="card mobile-card h-100">
                <div class="card-body">
                    <div class="icon-wrapper bg-soft-success text-success">
                        <i class="mdi mdi-cart-arrow-right"></i>
                    </div>
                    <span class="text-muted d-block font-size-13 text-truncate">Ventas</span>
                    <h3 class="mb-0 mt-1 font-weight-bold">S/ {{ number_format($totalVentas, 2) }}</h3>
                    <small class="text-success font-size-11">{{ $cantVentas }} oper. registradas</small>
                </div>
            </div>
        </div>
        <!-- Tarjeta Cobros -->
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <div class="card mobile-card h-100">
                <div class="card-body">
                    <div class="icon-wrapper bg-soft-info text-info">
                        <i class="mdi mdi-cash-register"></i>
                    </div>
                    <span class="text-muted d-block font-size-13 text-truncate">Cobranzas</span>
                    <h3 class="mb-0 mt-1 font-weight-bold">S/ {{ number_format($totalCobranzas, 2) }}</h3>
                    <small class="text-info font-size-11">{{ $cantCobranzas }} oper. registradas</small>
                </div>
            </div>
        </div>
        <!-- Tarjeta Por Cobrar en Ruta -->
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <div class="card mobile-card h-100">
                <div class="card-body">
                    <div class="icon-wrapper" style="background-color: rgba(231, 76, 60, 0.15); color: #c0392b; width: 55px; height: 55px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 15px;">
                        <i class="mdi mdi-cash-multiple"></i>
                    </div>
                    <span class="text-muted d-block font-size-13 text-truncate">Por Cobrar (Ruta)</span>
                    <h3 class="mb-0 mt-1 font-weight-bold" style="color: #c0392b;">S/ {{ number_format($totalPorCobrar, 2) }}</h3>
                    <small class="text-danger font-size-11">En sectores de hoy</small>
                </div>
            </div>
        </div>
        <!-- Tarjeta Stock en Furgoneta -->
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <div class="card mobile-card h-100">
                <div class="card-body">
                    <div class="icon-wrapper" style="background-color: rgba(52, 152, 219, 0.15); color: #2980b9; width: 55px; height: 55px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 15px;">
                        <i class="mdi mdi-cube-outline"></i>
                    </div>
                    <span class="text-muted d-block font-size-13 text-truncate">Mi Stock (Furgoneta)</span>
                    <h3 class="mb-0 mt-1 font-weight-bold" style="color: #2980b9;">{{ number_format($totalStockUnits, 0) }} uds.</h3>
                    <small class="text-primary font-size-11">{{ $totalStockItems }} prod. activos</small>
                </div>
            </div>
        </div>
        <!-- Tarjeta Sectores -->
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <div class="card mobile-card h-100">
                <div class="card-body">
                    <div class="icon-wrapper bg-soft-warning text-warning">
                        <i class="mdi mdi-map-marker-radius"></i>
                    </div>
                    <span class="text-muted d-block font-size-13 text-truncate">Sectores</span>
                    <h3 class="mb-0 mt-1 font-weight-bold">{{ count($sectoresAsignados) }}</h3>
                    <small class="text-muted font-size-11">Ruta diaria activa</small>
                </div>
            </div>
        </div>
        <!-- Tarjeta Furgoneta -->
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <div class="card mobile-card h-100">
                <div class="card-body">
                    <div class="icon-wrapper bg-soft-purple text-purple">
                        <i class="mdi mdi-truck-delivery"></i>
                    </div>
                    <span class="text-muted d-block font-size-13 text-truncate">Mi Unidad</span>
                    <h4 class="mb-0 mt-1 text-truncate font-weight-bold" style="font-size: 15px;">
                        {{ $vendedor->stockLocation->name ?? 'No Asignado' }}
                    </h4>
                    <small class="text-muted font-size-11">Ubicación de Stock</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas (Mobile First) -->
    <h5 class="section-title mt-2">Acciones de Ruta</h5>
    <div class="row mb-4">
        <div class="col-12 col-sm-6">
            <a href="{{ route('vendedor.venta') }}" class="btn quick-action-btn gradient-green text-white">
                <i class="mdi mdi-plus-circle-outline font-size-18"></i> Registrar Nueva Venta
            </a>
        </div>
        <div class="col-12 col-sm-6">
            <a href="{{ route('vendedor.cobros') }}" class="btn quick-action-btn gradient-blue text-white">
                <i class="mdi mdi-cash-plus font-size-18"></i> Registrar Cobranza (Amortización)
            </a>
        </div>
        <div class="col-12 col-sm-6">
            <a href="{{ route('vendedor.stock') }}" class="btn quick-action-btn gradient-purple text-white">
                <i class="mdi mdi-file-table-box-multiple-outline font-size-18"></i> Consultar Mi Stock / Inventario
            </a>
        </div>
    </div>

    <!-- Lista de Ruta para hoy -->
    <div class="row">
        <div class="col-12">
            <div class="card mobile-card">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="card-title font-weight-bold mb-0">Mi Ruta de Sectores de Hoy</h5>
                </div>
                <div class="card-body px-4">
                    @if($sectoresAsignados->isEmpty())
                        <p class="text-muted">Ningún sector asignado para hoy.</p>
                    @else
                        @foreach($sectoresAsignados as $asig)
                            <div class="route-item d-flex justify-content-between align-items-center">
                                <span>{{ $asig->sector->nomb_sec }}</span>
                                <span class="badge bg-soft-success text-success p-2">Ruta Activa</span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    window.addEventListener("load", function(event) {
        $(".loader").fadeOut("slow");
    });
</script>
@endsection
