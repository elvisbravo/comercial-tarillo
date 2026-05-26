@extends('layouts.main')

@section('title')
    Reporte KPI de Créditos
@endsection

@section('css')
    <!-- Sweet Alert-->
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
        /* CSS Premium para Dashboard KPI */
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

        .kpi-container {
            font-family: 'Outfit', sans-serif;
            color: #495057;
        }

        .page-title-box h4 {
            font-weight: 700;
            color: #1f2937;
            letter-spacing: -0.5px;
        }

        /* Tarjetas de Resumen KPI con gradientes premium */
        .kpi-card {
            border: none;
            border-radius: 16px;
            color: #ffffff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            cursor: pointer;
        }

        .kpi-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: rotate(45deg);
            pointer-events: none;
            transition: all 0.5s ease;
        }

        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        .kpi-card:hover::before {
            right: -10%;
            top: -40%;
            background: rgba(255, 255, 255, 0.15);
        }

        .kpi-card.active-filter {
            ring: 3px solid #ffffff;
            outline: 3px solid rgba(0, 0, 0, 0.2);
            transform: scale(0.98);
        }

        /* Gradientes específicos para las 5 categorías */
        .bg-gradient-normal {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .bg-gradient-potencial {
            background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%);
            /* Ajustado a un color dorado/amarillo premium */
            background: linear-gradient(135deg, #f1a90a 0%, #f7ca18 100%);
        }
        .bg-gradient-deficiente {
            background: linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%);
        }
        .bg-gradient-dudoso {
            background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);
        }
        .bg-gradient-perdida {
            background: linear-gradient(135deg, #1f2937 0%, #4b5563 100%);
        }
        .bg-gradient-total {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }

        .kpi-value {
            font-size: 28px;
            font-weight: 800;
            line-height: 1.2;
            margin-top: 5px;
        }

        .kpi-label {
            font-size: 13px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.9;
        }

        .kpi-icon {
            font-size: 32px;
            opacity: 0.8;
        }

        /* Tarjeta de desglose por Sede */
        .sede-card {
            border: 1px solid rgba(226, 232, 240, 0.8);
            background: #ffffff;
            border-radius: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            cursor: pointer;
        }

        .sede-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.1);
        }

        .sede-card.active-filter {
            border-color: #3b82f6;
            background-color: rgba(59, 130, 246, 0.02);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .sede-title {
            font-weight: 700;
            color: #1e293b;
            font-size: 16px;
        }

        /* Stacked progress bar premium */
        .progress-stacked {
            height: 12px;
            display: flex;
            border-radius: 6px;
            overflow: hidden;
            background-color: #f1f5f9;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .progress-segment {
            height: 100%;
            transition: width 0.6s ease;
        }

        .segment-normal { background-color: #2ec4b6; }
        .segment-potencial { background-color: #ffbf00; }
        .segment-deficiente { background-color: #ff7f50; }
        .segment-dudoso { background-color: #e71d36; }
        .segment-perdida { background-color: #4b5563; }

        /* Leyenda de píldoras */
        .legend-pill {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-right: 5px;
            margin-bottom: 5px;
            color: #fff;
        }

        /* Tabla premium */
        .card-table-wrapper {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .table-premium {
            margin-bottom: 0;
        }

        .table-premium thead th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            padding: 14px 16px;
            border-bottom: 2px solid #e2e8f0;
        }

        .table-premium tbody td {
            padding: 14px 16px;
            vertical-align: middle;
            font-size: 14px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        .table-premium tbody tr {
            transition: all 0.2s ease;
        }

        .table-premium tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Semáforo Status Badges */
        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
            box-shadow: 0 0 8px currentColor;
        }

        .badge-semaforo {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-normal {
            background-color: rgba(46, 196, 182, 0.12);
            color: #11998e;
        }
        .badge-potencial {
            background-color: rgba(255, 191, 0, 0.12);
            color: #d97706;
        }
        .badge-deficiente {
            background-color: rgba(255, 127, 80, 0.12);
            color: #ea580c;
        }
        .badge-dudoso {
            background-color: rgba(231, 29, 54, 0.12);
            color: #dc2626;
        }
        .badge-perdida {
            background-color: rgba(75, 85, 99, 0.12);
            color: #4b5563;
        }

        /* Shimmer loading effect */
        .shimmer {
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 200% 100%;
            animation: loading-shimmer 1.5s infinite;
        }

        @keyframes loading-shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Animaciones */
        .fade-in-up {
            animation: fadeInUp 0.4s ease forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        @keyframes pulse-dot {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.2);
                opacity: 0.7;
            }
        }
        .animate-pulse {
            animation: pulse-dot 1.5s infinite ease-in-out;
        }
    </style>
@endsection

@section('contenido')
    <div class="container-fluid kpi-container py-3">
        <!-- Título -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-1 font-size-18">KPI de Cartera de Créditos</h4>
                        <p class="text-muted mb-0 font-size-13">Monitoreo de riesgo con medidas de semáforo por sede física.</p>
                    </div>
                    <div class="page-title-right">
                        <button class="btn btn-outline-primary waves-effect waves-light" id="btn-refresh">
                            <i class="mdi mdi-refresh me-1"></i> Actualizar Reporte
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros Rápidos -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-body p-3">
                        <div class="row align-items-center">
                            <div class="col-md-4 mb-2 mb-md-0">
                                <label class="form-label font-size-13 fw-semibold mb-1">Filtrar por Sede</label>
                                <select id="filter-sede" class="form-select" style="border-radius: 8px;">
                                    <option value="all">Todas las Sedes</option>
                                    @foreach($sedes as $s)
                                        <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-2 mb-md-0">
                                <label class="form-label font-size-13 fw-semibold mb-1">Buscar Cliente</label>
                                <div class="position-relative">
                                    <input type="text" id="filter-search" class="form-control" placeholder="Buscar por nombre o documento..." style="border-radius: 8px; padding-left: 35px;">
                                    <i class="bx bx-search position-absolute text-muted" style="left: 12px; top: 12px; font-size: 16px;"></i>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end pt-3">
                                <span class="badge bg-soft-info text-info font-size-12 px-3 py-2" id="filter-active-indicator" style="border-radius: 20px; display: none;">
                                    <i class="mdi mdi-filter me-1"></i> Filtro Activo: <span id="active-filter-text">Ninguno</span>
                                    <span class="ms-2 cursor-pointer text-danger" id="btn-clear-filters" style="font-weight: 700;">&times;</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fila de KPI Cards -->
        <div class="row mb-4" id="kpi-cards-row">
            <!-- Total Card -->
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card kpi-card bg-gradient-total" data-filter="all">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="kpi-label">TOTAL CARTERA</span>
                                <div class="kpi-value" id="kpi-total-val">0</div>
                            </div>
                            <i class="bx bx-wallet kpi-icon"></i>
                        </div>
                        <div class="mt-3 font-size-13 text-white-50" id="kpi-total-saldo">S/ 0.00</div>
                    </div>
                </div>
            </div>
            <!-- Normal (Green) -->
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card kpi-card bg-gradient-normal" data-filter="normal">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="kpi-label">NORMAL (0-8d)</span>
                                <div class="kpi-value" id="kpi-normal-val">0</div>
                            </div>
                            <i class="bx bx-check-circle kpi-icon"></i>
                        </div>
                        <div class="mt-3 font-size-13 text-white-50" id="kpi-normal-saldo">S/ 0.00</div>
                    </div>
                </div>
            </div>
            <!-- Potencial (Yellow) -->
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card kpi-card bg-gradient-potencial" data-filter="potencial">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="kpi-label" style="color: #451a03;">POTENCIAL (9-30d)</span>
                                <div class="kpi-value" id="kpi-potencial-val" style="color: #451a03;">0</div>
                            </div>
                            <i class="bx bx-info-circle kpi-icon" style="color: #451a03;"></i>
                        </div>
                        <div class="mt-3 font-size-13" style="color: rgba(69, 26, 3, 0.7);" id="kpi-potencial-saldo">S/ 0.00</div>
                    </div>
                </div>
            </div>
            <!-- Deficiente (Orange) -->
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card kpi-card bg-gradient-deficiente" data-filter="deficiente">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="kpi-label">DEFICIENTE (31-60d)</span>
                                <div class="kpi-value" id="kpi-deficiente-val">0</div>
                            </div>
                            <i class="bx bx-error kpi-icon"></i>
                        </div>
                        <div class="mt-3 font-size-13 text-white-50" id="kpi-deficiente-saldo">S/ 0.00</div>
                    </div>
                </div>
            </div>
            <!-- Dudoso (Red) -->
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card kpi-card bg-gradient-dudoso" data-filter="dudoso">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="kpi-label">DUDOSO (61-120d)</span>
                                <div class="kpi-value" id="kpi-dudoso-val">0</div>
                            </div>
                            <i class="bx bx-error-alt kpi-icon"></i>
                        </div>
                        <div class="mt-3 font-size-13 text-white-50" id="kpi-dudoso-saldo">S/ 0.00</div>
                    </div>
                </div>
            </div>
            <!-- Pérdida (Gray) -->
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card kpi-card bg-gradient-perdida" data-filter="perdida">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="kpi-label">PÉRDIDA (>120d)</span>
                                <div class="kpi-value" id="kpi-perdida-val">0</div>
                            </div>
                            <i class="bx bx-block kpi-icon"></i>
                        </div>
                        <div class="mt-3 font-size-13 text-white-50" id="kpi-perdida-saldo">S/ 0.00</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secciones de Sede (Visible para Administradores o quienes ven múltiples) -->
        <h5 class="mb-3 font-size-15 fw-bold text-dark mt-4" id="title-sedes-section">Desglose por Sedes</h5>
        <div class="row mb-4" id="sedes-cards-row">
            <!-- Sede cards inserted dynamically -->
        </div>

        <!-- Tabla de Créditos Detallados -->
        <div class="row">
            <div class="col-12">
                <div class="card-table-wrapper">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap" style="background-color: #f8fafc;">
                        <h5 class="card-title mb-0 font-size-15 fw-bold" id="table-credits-title">Listado de Créditos</h5>
                        <div class="font-size-13 text-muted">
                            Mostrando <span id="table-count-showing">0</span> de <span id="table-count-total">0</span> créditos
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-premium align-middle table-nowrap">
                            <thead>
                                <tr>
                                    <th>Cód. Crédito</th>
                                    <th>Sede</th>
                                    <th>Cliente</th>
                                    <th>F. Registro</th>
                                    <th class="text-end">Monto Original</th>
                                    <th class="text-end">Saldo Pendiente</th>
                                    <th class="text-center">Máx. Retraso</th>
                                    <th>Estado / Semáforo</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="credits-table-body">
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                        <p class="mt-2 text-muted">Cargando información del portafolio...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap" style="background-color: #f8fafc;" id="pagination-wrapper">
                        <!-- Botones de paginación insertados dinámicamente -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detalle de Cuotas -->
    <div class="modal fade" id="modal-cuotas-detalle" tabindex="-1" role="dialog" aria-labelledby="modalCuotasTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-0" style="border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title font-size-16 fw-bold" id="modalCuotasTitle">
                        <i class="mdi mdi-format-list-bulleted me-1"></i> Detalle de Cuotas del Crédito
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="background-color: #f8fafc;">
                    <!-- Cabecera de información rápida -->
                    <div class="row mb-3 bg-white p-3 border rounded shadow-sm mx-0" style="border-radius: 12px !important;">
                        <div class="col-md-6 mb-2">
                            <span class="text-muted font-size-12 d-block">CLIENTE</span>
                            <span class="fw-bold font-size-14 text-dark" id="modal-client-name">-</span>
                            <span class="text-muted font-size-12 d-block mt-1" id="modal-client-doc">Doc: -</span>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <span class="text-muted font-size-12 d-block">MONTO ORIGINAL</span>
                            <span class="fw-bold font-size-14 text-primary" id="modal-amount-orig">S/ 0.00</span>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <span class="text-muted font-size-12 d-block">SALDO PENDIENTE</span>
                            <span class="fw-bold font-size-14 text-danger" id="modal-amount-pend">S/ 0.00</span>
                        </div>
                    </div>

                    <!-- Tabla de Cuotas -->
                    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3 text-uppercase font-size-11 fw-semibold text-muted"># Cuota</th>
                                        <th class="text-uppercase font-size-11 fw-semibold text-muted text-end">Monto Cuota</th>
                                        <th class="text-uppercase font-size-11 fw-semibold text-muted text-end">Saldo Cuota</th>
                                        <th class="text-uppercase font-size-11 fw-semibold text-muted text-center">Vencimiento</th>
                                        <th class="text-uppercase font-size-11 fw-semibold text-muted text-center">Días de Atraso</th>
                                        <th class="text-uppercase font-size-11 fw-semibold text-muted pe-3">Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="modal-cuotas-tbody">
                                    <!-- Cuotas cargadas vía AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-white py-3">
                    <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal" style="border-radius: 8px;">Cerrar</button>
                    <a href="#" id="modal-btn-go-to-credit" class="btn btn-primary waves-effect waves-light" style="border-radius: 8px;">
                        Ver en Gestión de Créditos <i class="mdi mdi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- Sweet Alerts js -->
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Variables de estado del dashboard
            let allCredits = [];
            let allSedesSummary = [];
            let globalSummary = {};
            
            // Estado de los filtros
            let activeFilters = {
                sede: 'all',
                category: 'all',
                search: ''
            };

            // Paginación
            let currentPage = 1;
            const itemsPerPage = 20;

            // Al cargar la página, obtener la data
            fetchKpiData();

            // Refrescar reporte
            $('#btn-refresh').click(function() {
                fetchKpiData();
            });

            // Cambios de filtro sede
            $('#filter-sede').change(function() {
                activeFilters.sede = $(this).val();
                activeFilters.category = 'all'; // Reset category filter on changing Sede
                currentPage = 1;
                applyFilters();
            });

            // Cambios de búsqueda
            $('#filter-search').on('input', function() {
                activeFilters.search = $(this).val().toLowerCase().trim();
                currentPage = 1;
                applyFilters();
            });

            // Limpiar filtros rápidos
            $('#btn-clear-filters').click(function() {
                activeFilters.sede = 'all';
                activeFilters.category = 'all';
                activeFilters.search = '';
                $('#filter-sede').val('all');
                $('#filter-search').val('');
                currentPage = 1;
                applyFilters();
            });

            // Función para obtener la data vía AJAX
            function fetchKpiData() {
                // Loader en tabla
                $('#credits-table-body').html(`
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2 text-muted">Obteniendo análisis de cartera en tiempo real...</p>
                        </td>
                    </tr>
                `);

                $.ajax({
                    url: "{{ route('reportes.kpi_creditos.data') }}",
                    method: 'GET',
                    success: function(response) {
                        $(".loader").fadeOut("slow");
                        allCredits = response.credits;
                        allSedesSummary = response.sedes_summary;
                        globalSummary = response.summary;

                        renderKpis(globalSummary);
                        renderSedes(allSedesSummary);
                        applyFilters();
                    },
                    error: function(xhr) {
                        $(".loader").fadeOut("slow");
                        console.error(xhr);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al cargar reporte',
                            text: 'No se pudo obtener la información de KPI. Por favor intente de nuevo.',
                            confirmButtonColor: '#3b82f6'
                        });
                        $('#credits-table-body').html(`
                            <tr>
                                <td colspan="9" class="text-center py-5 text-danger">
                                    <i class="mdi mdi-alert-circle-outline font-size-24"></i>
                                    <p class="mt-2">Error al recuperar datos del servidor.</p>
                                </td>
                            </tr>
                        `);
                    }
                });
            }

            // Renderizar los KPI generales en las tarjetas superiores
            function renderKpis(summary) {
                $('#kpi-total-val').text(summary.total_creditos);
                $('#kpi-total-saldo').text(formatMoney(summary.total_saldo));

                $('#kpi-normal-val').text(summary.normal.cantidad);
                $('#kpi-normal-saldo').text(formatMoney(summary.normal.saldo));

                $('#kpi-potencial-val').text(summary.potencial.cantidad);
                $('#kpi-potencial-saldo').text(formatMoney(summary.potencial.saldo));

                $('#kpi-deficiente-val').text(summary.deficiente.cantidad);
                $('#kpi-deficiente-saldo').text(formatMoney(summary.deficiente.saldo));

                $('#kpi-dudoso-val').text(summary.dudoso.cantidad);
                $('#kpi-dudoso-saldo').text(formatMoney(summary.dudoso.saldo));

                $('#kpi-perdida-val').text(summary.perdida.cantidad);
                $('#kpi-perdida-saldo').text(formatMoney(summary.perdida.saldo));
            }

            // Renderizar las tarjetas de desglose por Sede
            function renderSedes(sedes) {
                const container = $('#sedes-cards-row');
                container.empty();

                if (sedes.length === 0) {
                    $('#title-sedes-section').hide();
                    return;
                }
                
                $('#title-sedes-section').show();

                sedes.forEach(function(sede) {
                    // Calcular porcentajes
                    const total = sede.total_creditos || 1;
                    const pctNormal = ((sede.normal.cantidad / total) * 100).toFixed(0);
                    const pctPotencial = ((sede.potencial.cantidad / total) * 100).toFixed(0);
                    const pctDeficiente = ((sede.deficiente.cantidad / total) * 100).toFixed(0);
                    const pctDudoso = ((sede.dudoso.cantidad / total) * 100).toFixed(0);
                    const pctPerdida = ((sede.perdida.cantidad / total) * 100).toFixed(0);

                    const isActive = activeFilters.sede == sede.sede_id ? 'active-filter' : '';

                    const cardHtml = `
                        <div class="col-xl-4 col-md-6 mb-4 fade-in-up">
                            <div class="card sede-card h-100 ${isActive}" data-sede-id="${sede.sede_id}">
                                <div class="card-body p-4 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="sede-title mb-0 text-truncate" style="max-width: 80%;">${sede.sede_nombre}</h6>
                                            <span class="badge bg-soft-primary text-primary" style="border-radius: 12px;">${sede.total_creditos} Creds.</span>
                                        </div>
                                        <div class="font-size-20 fw-bold text-dark mb-3">${formatMoney(sede.total_saldo)}</div>
                                    </div>
                                    
                                    <div>
                                        <!-- Stacked progress bar -->
                                        <div class="progress-stacked mb-3">
                                            <div class="progress-segment segment-normal" style="width: ${pctNormal}%;" title="Normal: ${pctNormal}%" data-bs-toggle="tooltip"></div>
                                            <div class="progress-segment segment-potencial" style="width: ${pctPotencial}%;" title="Potencial: ${pctPotencial}%" data-bs-toggle="tooltip"></div>
                                            <div class="progress-segment segment-deficiente" style="width: ${pctDeficiente}%;" title="Deficiente: ${pctDeficiente}%" data-bs-toggle="tooltip"></div>
                                            <div class="progress-segment segment-dudoso" style="width: ${pctDudoso}%;" title="Dudoso: ${pctDudoso}%" data-bs-toggle="tooltip"></div>
                                            <div class="progress-segment segment-perdida" style="width: ${pctPerdida}%;" title="Pérdida: ${pctPerdida}%" data-bs-toggle="tooltip"></div>
                                        </div>

                                        <!-- Leyenda -->
                                        <div class="d-flex flex-wrap font-size-11">
                                            <span class="legend-pill segment-normal" title="Normal: S/ ${formatMoney(sede.normal.saldo)}">${sede.normal.cantidad} N</span>
                                            <span class="legend-pill segment-potencial" title="Potencial: S/ ${formatMoney(sede.potencial.saldo)}" style="color: #451a03;">${sede.potencial.cantidad} P</span>
                                            <span class="legend-pill segment-deficiente" title="Deficiente: S/ ${formatMoney(sede.deficiente.saldo)}">${sede.deficiente.cantidad} D</span>
                                            <span class="legend-pill segment-dudoso" title="Dudoso: S/ ${formatMoney(sede.dudoso.saldo)}">${sede.dudoso.cantidad} Du</span>
                                            <span class="legend-pill segment-perdida" title="Pérdida: S/ ${formatMoney(sede.perdida.saldo)}">${sede.perdida.cantidad} Pe</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    container.append(cardHtml);
                });

                // Inicializar tooltips de Bootstrap
                $('[data-bs-toggle="tooltip"]').tooltip();

                // Click handler en tarjetas de Sede
                $('.sede-card').click(function() {
                    const sId = $(this).data('sede-id');
                    if (activeFilters.sede == sId) {
                        // Click en activa -> desactiva
                        activeFilters.sede = 'all';
                        $('#filter-sede').val('all');
                    } else {
                        activeFilters.sede = sId;
                        $('#filter-sede').val(sId);
                    }
                    activeFilters.category = 'all'; // reset
                    currentPage = 1;
                    applyFilters();
                });
            }

            // Click handler en tarjetas superiores de categoría
            $('.kpi-card').click(function() {
                const cat = $(this).data('filter');
                $('.kpi-card').removeClass('active-filter');
                
                if (activeFilters.category == cat) {
                    activeFilters.category = 'all';
                } else {
                    activeFilters.category = cat;
                    $(this).addClass('active-filter');
                }
                currentPage = 1;
                applyFilters();
            });

            // Aplicar filtros locales sobre los créditos y renderizar tabla
            function applyFilters() {
                // Sincronizar active-filter de kpi-cards
                $('.kpi-card').removeClass('active-filter');
                if (activeFilters.category !== 'all') {
                    $(`.kpi-card[data-filter="${activeFilters.category}"]`).addClass('active-filter');
                } else {
                    $(`.kpi-card[data-filter="all"]`).addClass('active-filter');
                }

                // Sincronizar active-filter de sede-cards
                $('.sede-card').removeClass('active-filter');
                if (activeFilters.sede !== 'all') {
                    $(`.sede-card[data-sede-id="${activeFilters.sede}"]`).addClass('active-filter');
                }

                // Filtrar lista
                let filtered = allCredits;

                // 1. Filtro Sede
                if (activeFilters.sede !== 'all') {
                    filtered = filtered.filter(c => c.sede_id == activeFilters.sede);
                }

                // 2. Filtro Categoría
                if (activeFilters.category !== 'all') {
                    filtered = filtered.filter(c => c.cat_key == activeFilters.category);
                }

                // 3. Búsqueda texto
                if (activeFilters.search !== '') {
                    filtered = filtered.filter(c => 
                        c.cliente_nombre.toLowerCase().includes(activeFilters.search) ||
                        c.cliente_documento.includes(activeFilters.search) ||
                        c.credito_id.toString().includes(activeFilters.search)
                    );
                }

                // Indicador de filtros activos
                updateFilterIndicator();

                // Renderizar tabla con datos filtrados
                renderTable(filtered);
            }

            function updateFilterIndicator() {
                const indicator = $('#filter-active-indicator');
                const text = $('#active-filter-text');
                
                let activeTexts = [];
                
                if (activeFilters.sede !== 'all') {
                    const sedeObj = allSedesSummary.find(s => s.sede_id == activeFilters.sede);
                    if (sedeObj) activeTexts.push(`Sede: ${sedeObj.sede_nombre}`);
                }
                
                if (activeFilters.category !== 'all') {
                    activeTexts.push(`Categoría: ${activeFilters.category.toUpperCase()}`);
                }

                if (activeFilters.search !== '') {
                    activeTexts.push(`Búsqueda: "${activeFilters.search}"`);
                }

                if (activeTexts.length > 0) {
                    text.text(activeTexts.join(' | '));
                    indicator.fadeIn(200);
                } else {
                    indicator.fadeOut(200);
                }
            }

            // Renderizar el listado en la tabla paginado
            function renderTable(credits) {
                $('#table-count-total').text(allCredits.length);
                $('#table-count-showing').text(credits.length);

                const tbody = $('#credits-table-body');
                tbody.empty();

                if (credits.length === 0) {
                    tbody.html(`
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="bx bx-info-circle text-warning" style="font-size: 24px;"></i>
                                <p class="mt-2 mb-0">No se encontraron créditos activos con los filtros seleccionados.</p>
                            </td>
                        </tr>
                    `);
                    $('#pagination-wrapper').empty();
                    return;
                }

                // Calcular índices de paginación
                const totalPages = Math.ceil(credits.length / itemsPerPage);
                if (currentPage > totalPages) currentPage = totalPages || 1;
                
                const startIndex = (currentPage - 1) * itemsPerPage;
                const endIndex = Math.min(startIndex + itemsPerPage, credits.length);

                const paginated = credits.slice(startIndex, endIndex);

                paginated.forEach(function(c) {
                    const statusClass = `badge-semaforo badge-${c.cat_key}`;
                    const borderStyle = `border-left: 4px solid ${c.color_hex};`;

                    const trHtml = `
                        <tr style="${borderStyle}" class="fade-in-up">
                            <td class="fw-bold">#${c.credito_id}</td>
                            <td><span class="text-muted font-size-13">${c.sede_nombre}</span></td>
                            <td>
                                <div class="fw-semibold text-dark">${c.cliente_nombre}</div>
                                <div class="text-muted font-size-11">Doc: ${c.cliente_documento}</div>
                            </td>
                            <td>${c.fech_cre}</td>
                            <td class="text-end fw-semibold">S/ ${formatMoney(c.impo_cre)}</td>
                            <td class="text-end fw-bold text-primary">S/ ${formatMoney(c.saldo_pendiente)}</td>
                            <td class="text-center">
                                <span class="fw-bold font-size-14 ${c.max_dias_atraso > 8 ? 'text-danger' : 'text-success'}">
                                    ${c.max_dias_atraso} días
                                </span>
                            </td>
                            <td>
                                <span class="${statusClass}">
                                    <span class="status-dot" style="color: ${c.color_hex}; background-color: ${c.color_hex};"></span>
                                    ${c.categoria}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-soft-primary btn-view-cuotas waves-effect" data-id="${c.credito_id}" data-client="${c.cliente_nombre}" data-doc="${c.cliente_documento}" data-original="${c.impo_cre}" data-pending="${c.saldo_pendiente}">
                                    <i class="mdi mdi-eye me-1"></i> Ver Cuotas
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.append(trHtml);
                });

                // Renderizar botones de paginación
                renderPagination(totalPages);

                // Asignar evento click a los botones de cuotas
                $('.btn-view-cuotas').click(function() {
                    const cId = $(this).data('id');
                    const client = $(this).data('client');
                    const doc = $(this).data('doc');
                    const original = $(this).data('original');
                    const pending = $(this).data('pending');
                    
                    showCuotasModal(cId, client, doc, original, pending);
                });
            }

            // Renderizar la barra de paginación
            function renderPagination(totalPages) {
                const wrapper = $('#pagination-wrapper');
                wrapper.empty();

                if (totalPages <= 1) return;

                let paginationHtml = `<ul class="pagination pagination-rounded mb-0">`;
                
                // Botón Anterior
                paginationHtml += `
                    <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                        <a href="javascript:void(0);" class="page-link page-prev"><i class="mdi mdi-chevron-left"></i></a>
                    </li>
                `;

                // Números de página
                for (let i = 1; i <= totalPages; i++) {
                    // Mostrar solo páginas cercanas a la actual para no colapsar la pantalla
                    if (totalPages > 6 && Math.abs(i - currentPage) > 2 && i !== 1 && i !== totalPages) {
                        if (i === 2 || i === totalPages - 1) {
                            paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                        }
                        continue;
                    }
                    paginationHtml += `
                        <li class="page-item ${currentPage === i ? 'active' : ''}">
                            <a href="javascript:void(0);" class="page-link page-num" data-page="${i}">${i}</a>
                        </li>
                    `;
                }

                // Botón Siguiente
                paginationHtml += `
                    <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                        <a href="javascript:void(0);" class="page-link page-next"><i class="mdi mdi-chevron-right"></i></a>
                    </li>
                `;

                paginationHtml += `</ul>`;
                wrapper.append(paginationHtml);

                // Eventos
                $('.page-num').click(function() {
                    currentPage = parseInt($(this).data('page'));
                    applyFilters();
                });

                $('.page-prev').click(function() {
                    if (currentPage > 1) {
                        currentPage--;
                        applyFilters();
                    }
                });

                $('.page-next').click(function() {
                    if (currentPage < totalPages) {
                        currentPage++;
                        applyFilters();
                    }
                });
            }

            // Función para formatear dinero en soles
            function formatMoney(amount) {
                return parseFloat(amount).toLocaleString('es-PE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            // Mostrar el modal de cuotas cargado vía AJAX con el endpoint del sistema
            function showCuotasModal(creditId, client, doc, original, pending) {
                // Rellenar cabecera modal
                $('#modal-client-name').text(client);
                $('#modal-client-doc').text(`Doc: ${doc}`);
                $('#modal-amount-orig').text(`S/ ${formatMoney(original)}`);
                $('#modal-amount-pend').text(`S/ ${formatMoney(pending)}`);
                
                // Configurar botón para ir al crédito
                $('#modal-btn-go-to-credit').attr('href', `{{ url('creditos-pendientes') }}?buscar=${doc}`);

                // Mostrar cargando
                $('#modal-cuotas-tbody').html(`
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="spinner-border text-primary spinner-border-sm" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <span class="ms-2 text-muted">Recuperando desglose de amortizaciones...</span>
                        </td>
                    </tr>
                `);

                // Abrir modal
                const myModal = new bootstrap.Modal(document.getElementById('modal-cuotas-detalle'));
                myModal.show();

                // Cargar cuotas con AJAX (usamos el endpoint de ReporteCreditos)
                $.ajax({
                    url: `{{ url('creditos-pendientes/cuotas') }}/${creditId}`,
                    method: 'GET',
                    success: function(response) {
                        const tbody = $('#modal-cuotas-tbody');
                        tbody.empty();

                        if (response.length === 0) {
                            tbody.html('<tr><td colspan="6" class="text-center py-3 text-muted">No se encontraron cuotas para este crédito.</td></tr>');
                            return;
                        }

                        // El endpoint retorna cuotas. Vamos a ordenarlas por número
                        response.forEach(function(cuota) {
                            const num = cuota.numero_cuo;
                            const importe = parseFloat(cuota.mont_cuo);
                            const saldo = parseFloat(cuota.saldo_cuo);
                            
                            // Formatear fecha de vencimiento
                            const rawDate = cuota.fven_cuo;
                            const parts = rawDate.split('-');
                            const formattedDate = `${parts[2]}/${parts[1]}/${parts[0]}`;
                            
                            // Calcular días de retraso
                            const today = new Date();
                            today.setHours(0,0,0,0);
                            const dueDate = new Date(parts[0], parts[1]-1, parts[2]);
                            dueDate.setHours(0,0,0,0);
                            
                            let delayDays = 0;
                            let delayText = '-';
                            let isOverdue = false;

                            if (cuota.esta_cuo === 'PENDIENTE') {
                                if (dueDate < today) {
                                    const diffTime = Math.abs(today - dueDate);
                                    delayDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                                    delayText = `<span class="text-danger fw-bold">${delayDays} días</span>`;
                                    isOverdue = true;
                                } else {
                                    delayText = `<span class="text-success">Al día</span>`;
                                }
                            } else {
                                delayText = `<span class="text-muted">Pagado</span>`;
                            }

                            // Badge de estado de cuota
                            let stateBadge = '';
                            if (cuota.esta_cuo === 'COBRADA' || cuota.esta_cuo === 'PAGADO') {
                                stateBadge = '<span class="badge bg-soft-success text-success">PAGADO</span>';
                            } else if (cuota.esta_cuo === 'PENDIENTE') {
                                stateBadge = isOverdue 
                                    ? '<span class="badge bg-soft-danger text-danger">VENCIDO</span>' 
                                    : '<span class="badge bg-soft-primary text-primary">PENDIENTE</span>';
                            } else if (cuota.esta_cuo === 'REPROGRAMADA') {
                                stateBadge = '<span class="badge bg-soft-warning text-warning">REPROGRAMADA</span>';
                            } else {
                                stateBadge = `<span class="badge bg-soft-secondary text-secondary">${cuota.esta_cuo}</span>`;
                            }

                            const rowHtml = `
                                <tr>
                                    <td class="ps-3 fw-medium">Cuota ${num}</td>
                                    <td class="text-end fw-semibold">S/ ${formatMoney(importe)}</td>
                                    <td class="text-end fw-bold text-dark">S/ ${formatMoney(saldo)}</td>
                                    <td class="text-center">${formattedDate}</td>
                                    <td class="text-center font-size-13">${delayText}</td>
                                    <td class="pe-3">${stateBadge}</td>
                                </tr>
                            `;
                            tbody.append(rowHtml);
                        });
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        $('#modal-cuotas-tbody').html(`
                            <tr>
                                <td colspan="6" class="text-center py-3 text-danger">
                                    <i class="mdi mdi-alert-circle-outline me-1"></i> Error al cargar cuotas.
                                </td>
                            </tr>
                        `);
                    }
                });
            }
        });
    </script>
@endsection
