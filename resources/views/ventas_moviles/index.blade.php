@extends('layouts.main')

@section('title', 'Ventas Móviles - Listado')

@section('css')
<style>
    .mobile-sales-card {
        transition: all 0.3s ease;
        border: none;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }
    .mobile-sales-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    .seller-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        margin-bottom: 1.5rem;
        box-shadow: 0 5px 15px rgba(118, 75, 162, 0.3);
    }
    .status-badge {
        padding: 5px 15px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .status-active { background: #e3f9e5; color: #1f9d55; }
    .status-idle { background: #fef3c7; color: #d97706; }
    
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }
    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 15px;
        display: flex;
        align-items: center;
        gap: 1rem;
        border: 1px solid #f0f0f0;
    }
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .stat-purple { background: #f3f0ff; color: #7c3aed; }
    .stat-green { background: #ecfdf5; color: #10b981; }
    .stat-orange { background: #fff7ed; color: #f59e0b; }

    .btn-assign {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-assign:hover {
        opacity: 0.9;
        transform: scale(1.05);
        color: white;
    }
</style>
@endsection

@section('contenido')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Unidades de Venta Móvil</h3>
            <p class="text-muted">Gestiona la asignación de mercancía a tus vendedores en ruta.</p>
        </div>
        <a href="{{ url('ventas-moviles/asignar') }}" class="btn btn-assign shadow-sm">
            <i class="fas fa-plus-circle me-2"></i> Nueva Asignación
        </a>
    </div>

    <!-- Stats Dashboard -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon stat-purple">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <p class="text-muted mb-0 small text-uppercase fw-bold">Vendedores</p>
                <h4 class="mb-0 fw-bold">12</h4>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-green">
                <i class="fas fa-truck-moving"></i>
            </div>
            <div>
                <p class="text-muted mb-0 small text-uppercase fw-bold">En Ruta</p>
                <h4 class="mb-0 fw-bold">8</h4>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-orange">
                <i class="fas fa-boxes"></i>
            </div>
            <div>
                <p class="text-muted mb-0 small text-uppercase fw-bold">Items Cargados</p>
                <h4 class="mb-0 fw-bold">450</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Vendedor Card 1 -->
        <div class="col-md-4 mb-4">
            <div class="card mobile-sales-card">
                <div class="card-body p-4 text-center">
                    <div class="d-flex justify-content-end mb-2">
                        <span class="status-badge status-active">En Ruta</span>
                    </div>
                    <div class="d-flex justify-content-center">
                        <div class="seller-avatar">JB</div>
                    </div>
                    <h5 class="fw-bold mb-1">Juan Bravo</h5>
                    <p class="text-muted small mb-4">Ruta Norte - Zona A</p>
                    
                    <div class="bg-light p-3 rounded-3 mb-4 d-flex justify-content-around">
                        <div class="text-center">
                            <p class="text-muted small mb-1">Productos</p>
                            <p class="fw-bold mb-0">15</p>
                        </div>
                        <div class="text-center border-start ps-3">
                            <p class="text-muted small mb-1">Valor Carga</p>
                            <p class="fw-bold mb-0">S/ 2,450.00</p>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary rounded-pill">
                            <i class="fas fa-edit me-2"></i> Ajustar Carga
                        </button>
                        <button class="btn btn-link text-muted small">Ver Historial de Salidas</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendedor Card 2 -->
        <div class="col-md-4 mb-4">
            <div class="card mobile-sales-card">
                <div class="card-body p-4 text-center">
                    <div class="d-flex justify-content-end mb-2">
                        <span class="status-badge status-idle">En Almacén</span>
                    </div>
                    <div class="d-flex justify-content-center">
                        <div class="seller-avatar" style="background: linear-gradient(135deg, #FF9A8B 0%, #FF6A88 55%, #FF99AC 100%);">MT</div>
                    </div>
                    <h5 class="fw-bold mb-1">María Torres</h5>
                    <p class="text-muted small mb-4">Ruta Sur - Zona B</p>
                    
                    <div class="bg-light p-3 rounded-3 mb-4 d-flex justify-content-around">
                        <div class="text-center">
                            <p class="text-muted small mb-1">Productos</p>
                            <p class="fw-bold mb-0">0</p>
                        </div>
                        <div class="text-center border-start ps-3">
                            <p class="text-muted small mb-1">Valor Carga</p>
                            <p class="fw-bold mb-0">S/ 0.00</p>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-assign rounded-pill">
                            <i class="fas fa-box-open me-2"></i> Asignar Mercancía
                        </button>
                        <button class="btn btn-link text-muted small">Ver Historial de Salidas</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendedor Card 3 -->
        <div class="col-md-4 mb-4">
            <div class="card mobile-sales-card">
                <div class="card-body p-4 text-center">
                    <div class="d-flex justify-content-end mb-2">
                        <span class="status-badge status-active">En Ruta</span>
                    </div>
                    <div class="d-flex justify-content-center">
                        <div class="seller-avatar" style="background: linear-gradient(135deg, #00dbde 0%, #fc00ff 100%);">RG</div>
                    </div>
                    <h5 class="fw-bold mb-1">Roberto Gómez</h5>
                    <p class="text-muted small mb-4">Ruta Centro</p>
                    
                    <div class="bg-light p-3 rounded-3 mb-4 d-flex justify-content-around">
                        <div class="text-center">
                            <p class="text-muted small mb-1">Productos</p>
                            <p class="fw-bold mb-0">42</p>
                        </div>
                        <div class="text-center border-start ps-3">
                            <p class="text-muted small mb-1">Valor Carga</p>
                            <p class="fw-bold mb-0">S/ 5,120.00</p>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary rounded-pill">
                            <i class="fas fa-edit me-2"></i> Ajustar Carga
                        </button>
                        <button class="btn btn-link text-muted small">Ver Historial de Salidas</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="{{ asset('js/ventas_moviles.js') }}"></script>
@endsection
