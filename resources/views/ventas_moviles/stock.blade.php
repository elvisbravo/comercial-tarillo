@extends('layouts.main')

@section('title', 'Consultar Stock')

@section('css')
<style>
    .mobile-container {
        max-width: 600px;
        margin: 0 auto;
        padding-bottom: 40px;
    }
    .stock-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        background: #ffffff;
    }
    .product-row {
        border-bottom: 1px solid #f1f4f6;
        padding: 12px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .product-row:last-child {
        border-bottom: none;
    }
    .badge-stock {
        font-size: 14px;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 700;
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
        <h4 class="mb-0 font-weight-bold" style="color: #2c3e50;">Mi Stock / Furgoneta</h4>
    </div>

    <!-- Buscador -->
    <div class="mb-3">
        <input type="text" class="form-control" id="buscar_stock" placeholder="🔍 Buscar producto en inventario..." style="border-radius: 10px; padding: 10px 15px;">
    </div>

    <!-- Tarjeta de Inventario -->
    <div class="card stock-card">
        <div class="card-header bg-transparent border-0 pt-4 px-3 pb-0">
            <h5 class="font-weight-bold mb-0 text-dark">Listado de Artículos Disponibles</h5>
            <small class="text-muted">Unidad: {{ $vendedor->stockLocation->name ?? 'Furgoneta' }}</small>
        </div>
        <div class="card-body p-0 mt-3" id="stock_list_wrapper">
            @if($stock->isEmpty())
                <div class="text-center py-5">
                    <i class="mdi mdi-package-variant-remove text-muted" style="font-size: 48px;"></i>
                    <p class="text-muted mt-2 mb-0">No hay productos con stock registrado en su unidad móvil.</p>
                </div>
            @else
                @foreach($stock as $item)
                    <div class="product-row" data-nombre="{{ $item->nomb_pro }}">
                        <div style="flex: 1; padding-right: 15px;">
                            <span class="font-weight-bold d-block text-dark font-size-14 text-truncate" style="max-width: 320px;">
                                {{ $item->nomb_pro }}
                            </span>
                        </div>
                        <div>
                            @if($item->stock > 10)
                                <span class="badge bg-soft-success text-success badge-stock">{{ $item->stock }} unid.</span>
                            @elseif($item->stock > 0)
                                <span class="badge bg-soft-warning text-warning badge-stock">{{ $item->stock }} unid.</span>
                            @else
                                <span class="badge bg-soft-danger text-danger badge-stock">Agotado</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $(".loader").fadeOut("slow");

        // Filtrar tabla de stock
        $('#buscar_stock').on('keyup', function() {
            let value = $(this).val().toLowerCase();
            $('#stock_list_wrapper .product-row').filter(function() {
                $(this).toggle($(this).attr('data-nombre').toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
@endsection
