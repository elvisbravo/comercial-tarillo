@extends('layouts.main')

@section('title', 'Asignar Mercancía - Ventas Móviles')

@section('css')
<style>
    .assign-container {
        display: flex;
        gap: 2rem;
        height: calc(100vh - 200px);
    }
    .product-selection {
        flex: 2;
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
    }
    .assignment-bag {
        flex: 1;
        background: rgba(118, 75, 162, 0.05);
        border: 2px dashed rgba(118, 75, 162, 0.2);
        border-radius: 20px;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
    }
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 1rem;
        overflow-y: auto;
        padding: 0.5rem;
    }
    .product-item {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 1rem;
        text-align: center;
        transition: all 0.2s ease;
        cursor: pointer;
        border: 2px solid transparent;
    }
    .product-item:hover {
        background: white;
        border-color: #764ba2;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    .product-img {
        width: 100%;
        height: 100px;
        background: #eee;
        border-radius: 10px;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
    }
    .bag-item {
        background: white;
        padding: 10px;
        border-radius: 12px;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }
    .btn-finish {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 15px;
        border-radius: 12px;
        font-weight: 600;
        margin-top: auto;
    }
    .qty-input {
        width: 60px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
</style>
@endsection

@section('contenido')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('ventas-moviles') }}">Ventas Móviles</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Nueva Asignación</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-1">Nueva Asignación de Mercancía</h3>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label fw-bold">Seleccionar Vendedor</label>
                <select class="form-select form-select-lg rounded-pill border-2">
                    <option selected>Selecciona un vendedor...</option>
                    <option>Juan Bravo (Ruta Norte)</option>
                    <option>María Torres (Ruta Sur)</option>
                    <option>Roberto Gómez (Ruta Centro)</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label fw-bold">Almacén de Origen</label>
                <select class="form-select form-select-lg rounded-pill border-2">
                    <option selected>Almacén Central</option>
                    <option>Sede Norte</option>
                </select>
            </div>
        </div>
    </div>

    <div class="assign-container">
        <!-- Product Selection -->
        <div class="product-selection">
            <div class="input-group mb-4 shadow-sm rounded-pill overflow-hidden">
                <span class="input-group-text bg-white border-0 ps-4"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control border-0 py-3" placeholder="Buscar productos por nombre, código o categoría...">
            </div>

            <div class="product-grid">
                <!-- Mock Products -->
                @for ($i = 1; $i <= 12; $i++)
                <div class="product-item">
                    <div class="product-img">
                        <i class="fas fa-image fa-2x"></i>
                    </div>
                    <p class="mb-1 small fw-bold">Producto Demo #{{ $i }}</p>
                    <p class="text-primary small mb-2">S/ 45.00</p>
                    <button class="btn btn-sm btn-outline-primary w-100 rounded-pill">
                        <i class="fas fa-plus"></i> Añadir
                    </button>
                </div>
                @endfor
            </div>
        </div>

        <!-- Assignment Bag -->
        <div class="assignment-bag">
            <h5 class="fw-bold mb-4 d-flex justify-content-between">
                Items para Asignar
                <span class="badge bg-primary rounded-pill">3</span>
            </h5>
            
            <div class="bag-items-list overflow-auto">
                <div class="bag-item">
                    <div class="bg-light rounded p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-box text-muted"></i>
                    </div>
                    <div class="flex-grow-1">
                        <p class="mb-0 small fw-bold">Aceite Primor 1L</p>
                        <p class="mb-0 text-muted smaller">SKU: 001234</p>
                    </div>
                    <input type="number" class="qty-input" value="12">
                    <button class="btn btn-link text-danger p-0"><i class="fas fa-times"></i></button>
                </div>

                <div class="bag-item">
                    <div class="bg-light rounded p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-box text-muted"></i>
                    </div>
                    <div class="flex-grow-1">
                        <p class="mb-0 small fw-bold">Arroz Costeño 5kg</p>
                        <p class="mb-0 text-muted smaller">SKU: 009876</p>
                    </div>
                    <input type="number" class="qty-input" value="5">
                    <button class="btn btn-link text-danger p-0"><i class="fas fa-times"></i></button>
                </div>

                <div class="bag-item">
                    <div class="bg-light rounded p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-box text-muted"></i>
                    </div>
                    <div class="flex-grow-1">
                        <p class="mb-0 small fw-bold">Azúcar Rubia 1kg</p>
                        <p class="mb-0 text-muted smaller">SKU: 005544</p>
                    </div>
                    <input type="number" class="qty-input" value="20">
                    <button class="btn btn-link text-danger p-0"><i class="fas fa-times"></i></button>
                </div>
            </div>

            <div class="mt-4 border-top pt-3">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total Unidades:</span>
                    <span class="fw-bold">37</span>
                </div>
                <div class="d-flex justify-content-between mb-4">
                    <span class="text-muted">Valor Estimado:</span>
                    <span class="fw-bold text-primary">S/ 1,240.00</span>
                </div>
                <button class="btn btn-finish w-100 shadow">
                    <i class="fas fa-check-circle me-2"></i> Confirmar y Generar Guía
                </button>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="{{ asset('js/ventas_moviles.js') }}"></script>
@endsection
