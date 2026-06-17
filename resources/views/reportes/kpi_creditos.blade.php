@extends('layouts.main')

@section('title')
    Calificación Crediticia
@endsection

@section('css')
    <style>
        body {
            background-color: #f4f5f7;
        }

        .kpi-gauge-container {
            max-width: 760px;
            margin: 0 auto;
        }

        .gauge-banner {
            background: linear-gradient(135deg, #6a1b9a 0%, #4a148c 100%);
            color: #ffffff;
            padding: 18px 28px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(74, 20, 140, 0.25);
        }

        .gauge-banner h1 {
            font-size: 26px;
            font-weight: 700;
            margin: 0;
            letter-spacing: 0.2px;
            color: #ffffff !important;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.35);
        }

        .bcp-logo {
            width: 54px;
            height: 54px;
            background: #ffffff;
            color: #1a3a8a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 22px;
            transform: rotate(45deg);
            border-radius: 6px;
        }

        .bcp-logo span {
            transform: rotate(-45deg);
            letter-spacing: -1px;
        }

        .gauge-wrapper {
            position: relative;
            width: 100%;
            height: 360px;
            background: #ffffff;
            border-radius: 10px;
            margin-top: 24px;
            padding: 10px 20px 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }

        .categories-title {
            text-align: center;
            color: #1a73e8;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 1.5px;
            margin: 30px 0 22px;
        }

        .divider-dashed {
            border-top: 1px dashed #c9ccd1;
            margin: 0;
        }

        .consumo-marker {
            color: #1a73e8;
            font-weight: 700;
            font-size: 14px;
            margin: 18px 0 14px;
        }

        .consumo-marker::before {
            content: "▸ ";
            color: #1a73e8;
        }

        .categoria-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 14px;
            font-size: 14px;
            color: #2c3e50;
            line-height: 1.4;
        }

        .categoria-dot {
            width: 16px;
            height: 16px;
            min-width: 16px;
            border-radius: 50%;
            margin-right: 12px;
            margin-top: 3px;
        }

        .categoria-text strong {
            color: #1a73e8;
            display: block;
            margin-bottom: 2px;
        }

        .summary-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
            margin-top: 20px;
        }

        .summary-pill {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 30px;
            background: #ffffff;
            color: #34495e;
            font-size: 13px;
            font-weight: 600;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
        }

        .summary-pill:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.12);
        }

        .summary-pill.active {
            outline: 3px solid #6a1b9a;
            outline-offset: 1px;
        }

        .summary-pill .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 6px;
        }

        .summary-pill.pill-all {
            background: #f1f3f5;
            color: #495057;
        }

        .summary-bar .label {
            margin-right: 6px;
            color: #7a7a7a;
            font-weight: 500;
        }

        /* Selector de sede */
        .sede-filter {
            background: #ffffff;
            border-radius: 10px;
            padding: 14px 18px;
            margin-top: 16px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .sede-filter-label {
            font-weight: 600;
            color: #34495e;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .sede-filter-select {
            flex: 0 0 auto;
            min-width: 240px;
            padding: 8px 12px;
            border: 1px solid #d6dbe1;
            border-radius: 8px;
            font-size: 14px;
            color: #2c3e50;
            background: #ffffff;
            cursor: pointer;
            transition: border-color 0.2s ease;
        }

        .sede-filter-select:focus {
            outline: none;
            border-color: #6a1b9a;
            box-shadow: 0 0 0 3px rgba(106, 27, 154, 0.12);
        }

        .sede-filter-hint {
            font-size: 12px;
            color: #7a7a7a;
            font-style: italic;
        }

        /* Desglose por Sedes */
        .sedes-section {
            margin-top: 36px;
        }

        .sedes-section-title {
            color: #1a73e8;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sede-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
            height: 100%;
            transition: all 0.2s ease;
        }

        .sede-card:hover {
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .sede-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .sede-card-name {
            font-weight: 700;
            color: #1e293b;
            font-size: 15px;
            flex: 1;
            margin-right: 8px;
            word-break: break-word;
        }

        .sede-card-count {
            background: #eef2ff;
            color: #4f46e5;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .sede-card-total {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 14px;
        }

        .progress-stacked {
            height: 12px;
            display: flex;
            border-radius: 6px;
            overflow: hidden;
            background-color: #f1f5f9;
            margin-bottom: 12px;
        }

        .progress-segment {
            height: 100%;
            transition: width 0.6s ease;
        }

        .segment-normal     { background-color: #28a745; }
        .segment-potencial  { background-color: #8bc34a; }
        .segment-deficiente { background-color: #ffc107; }
        .segment-dudoso     { background-color: #ff9800; }
        .segment-perdida    { background-color: #dc3545; }

        .legend-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            font-size: 11px;
        }

        .legend-pill {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: 600;
            color: #ffffff;
        }

        .legend-pill .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.7);
            margin-right: 5px;
        }

        .sedes-empty {
            text-align: center;
            padding: 30px;
            color: #94a3b8;
            font-style: italic;
            font-size: 14px;
        }

        /* Listado de créditos filtrable */
        .credits-section {
            margin-top: 36px;
        }

        .credits-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 14px;
        }

        .credits-section-title {
            color: #1a73e8;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .credits-filter-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 20px;
            background: #eef2ff;
            color: #4f46e5;
            font-size: 12px;
            font-weight: 700;
        }

        .credits-table-wrapper {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .credits-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .credits-table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            text-align: left;
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .credits-table tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }

        .credits-table tbody tr:last-child td {
            border-bottom: none;
        }

        .credits-table tbody tr:hover {
            background: #f8fafc;
        }

        .credits-table .num {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        .credits-cat-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .credits-empty {
            text-align: center;
            padding: 30px;
            color: #94a3b8;
            font-style: italic;
            font-size: 14px;
        }
    </style>
@endsection

@section('contenido')
    <div class="container py-4">
        <div class="kpi-gauge-container">

            {{-- Banner morado con título y logo --}}
            <div class="gauge-banner">
                <h1>¿Cuál es tu calificación crediticia?</h1>
                <div class="bcp-logo">
                    <span>S/</span>
                </div>
            </div>

            {{-- Selector de sede (filtrado por tipo_envio del usuario) --}}
            @if(count($sedes) > 0)
                <div class="sede-filter">
                    <label for="filter-sede" class="sede-filter-label">
                        <i class="bx bx-buildings"></i> Sede:
                    </label>
                    <select id="filter-sede" class="sede-filter-select">
                        <option value="all">— Todas las sedes —</option>
                        @foreach($sedes as $s)
                            <option value="{{ $s->id }}" {{ (session('key') && !$isAdmin && session('key')->sede_id == $s->id) ? 'selected' : '' }}>
                                {{ $s->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <span class="sede-filter-hint" id="sede-filter-hint"></span>
                </div>
            @endif

            {{-- Gauge semicircular con ECharts --}}
            <div class="gauge-wrapper">
                <div id="kpi-gauge-chart" style="width: 100%; height: 100%;"></div>
            </div>

            {{-- Resumen numérico por categoría (siempre visible) --}}
            <div class="summary-bar" id="summary-bar">
                <span class="label">Mostrar:</span>
                <span class="summary-pill pill-all active" data-filter="all">
                    <span class="dot" style="background:#6a1b9a;"></span> Todas
                </span>
                <span class="summary-pill" data-filter="normal"><span class="dot" style="background:#28a745;"></span> Normal: <span id="sum-normal">0</span></span>
                <span class="summary-pill" data-filter="potencial"><span class="dot" style="background:#8bc34a;"></span> Problemas potenciales: <span id="sum-potencial">0</span></span>
                <span class="summary-pill" data-filter="deficiente"><span class="dot" style="background:#ffc107;"></span> Deficiente: <span id="sum-deficiente">0</span></span>
                <span class="summary-pill" data-filter="dudoso"><span class="dot" style="background:#ff9800;"></span> Dudoso: <span id="sum-dudoso">0</span></span>
                <span class="summary-pill" data-filter="perdida"><span class="dot" style="background:#dc3545;"></span> Pérdida: <span id="sum-perdida">0</span></span>
            </div>

            {{-- Título de categorías --}}
            <h2 class="categories-title">CATEGORÍAS DE CLASIFICACIÓN</h2>

            <hr class="divider-dashed">

            {{-- Listado de categorías con descripciones --}}
            <div class="consumo-marker">EN CRÉDITOS DE CONSUMO</div>

            <div class="row">
                <div class="col-md-6">
                    <div class="categoria-item">
                        <div class="categoria-dot" style="background:#28a745;"></div>
                        <div class="categoria-text">
                            <strong>Normal</strong>
                            Pago puntual o atraso máximo de 8 días
                        </div>
                    </div>
                    <div class="categoria-item">
                        <div class="categoria-dot" style="background:#8bc34a;"></div>
                        <div class="categoria-text">
                            <strong>Problemas potenciales</strong>
                            Atrasos en el pago máximo entre 9 a 30 días
                        </div>
                    </div>
                    <div class="categoria-item">
                        <div class="categoria-dot" style="background:#ffc107;"></div>
                        <div class="categoria-text">
                            <strong>Deficiente</strong>
                            Atrasos en el pago máximo entre 31 a 60 días
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="categoria-item">
                        <div class="categoria-dot" style="background:#ff9800;"></div>
                        <div class="categoria-text">
                            <strong>Dudoso</strong>
                            Atrasos en el pago máximo entre 61 a 120 días
                        </div>
                    </div>
                    <div class="categoria-item">
                        <div class="categoria-dot" style="background:#dc3545;"></div>
                        <div class="categoria-text">
                            <strong>Pérdida</strong>
                            Atrasos en el pago de más de 120 días
                        </div>
                    </div>
                </div>
            </div>

            {{-- Desglose por Sedes --}}
            <div class="sedes-section">
                <h3 class="sedes-section-title">
                    <i class="bx bx-buildings"></i> Desglose por Sedes
                </h3>
                <div class="row" id="sedes-cards-row">
                    <!-- Cards insertados dinámicamente -->
                </div>
                <div id="sedes-empty" class="sedes-empty" style="display: none;">
                    <i class="bx bx-info-circle"></i> No hay datos de sedes para mostrar.
                </div>
            </div>

            {{-- Listado de créditos filtrable por categoría --}}
            <div class="credits-section">
                <div class="credits-section-header">
                    <h3 class="credits-section-title">
                        <i class="bx bx-list-ul"></i> Listado de Créditos
                    </h3>
                    <span class="credits-filter-badge" id="credits-filter-badge">
                        <i class="bx bx-filter-alt"></i> Todas las categorías
                    </span>
                </div>
                <div class="credits-table-wrapper">
                    <table class="credits-table" id="credits-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Documento</th>
                                <th>Sede</th>
                                <th class="num">Importe</th>
                                <th class="num">Saldo pendiente</th>
                                <th class="num">Días atraso</th>
                                <th>Categoría</th>
                            </tr>
                        </thead>
                        <tbody id="credits-tbody">
                        </tbody>
                    </table>
                </div>
                <div id="credits-empty" class="credits-empty" style="display: none;">
                    <i class="bx bx-info-circle"></i> No hay créditos para la categoría seleccionada.
                </div>
            </div>

        </div>
    </div>
@endsection

@section('js')
    {{-- ECharts (ya está en assets/libs del proyecto; lo cargamos desde CDN por simplicidad) --}}
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>

    <script>
        // Configuración del gauge con 5 zonas de color
        // Categorías: normal(0-20) | potencial(20-40) | deficiente(40-60) | dudoso(60-80) | perdida(80-100)
        const CATEGORIES = [
            { key: 'normal',     center: 10, color: '#28a745' },
            { key: 'potencial',  center: 30, color: '#8bc34a' },
            { key: 'deficiente', center: 50, color: '#ffc107' },
            { key: 'dudoso',     center: 70, color: '#ff9800' },
            { key: 'perdida',    center: 90, color: '#dc3545' }
        ];

        function dominantCategory(summary) {
            let dom = CATEGORIES[0];
            let max = -1;
            CATEGORIES.forEach(c => {
                const cant = (summary && summary[c.key] && summary[c.key].cantidad) || 0;
                if (cant > max) {
                    max = cant;
                    dom = c;
                }
            });
            if (max <= 0) return CATEGORIES[2];
            return dom;
        }

        function renderGauge(summary) {
            const dom = document.getElementById('kpi-gauge-chart');
            if (!dom) return;
            let myChart = echarts.getInstanceByDom(dom);
            if (!myChart) myChart = echarts.init(dom);
            const cat = dominantCategory(summary);

            const option = {
                series: [{
                    type: 'gauge',
                    startAngle: 180,
                    endAngle: 0,
                    min: 0,
                    max: 100,
                    splitNumber: 5,
                    center: ['50%', '78%'],
                    radius: '95%',
                    progress: { show: false },
                    axisLine: {
                        lineStyle: {
                            width: 30,
                            color: [
                                [0.2, '#28a745'],
                                [0.4, '#8bc34a'],
                                [0.6, '#ffc107'],
                                [0.8, '#ff9800'],
                                [1,   '#dc3545']
                            ]
                        }
                    },
                    pointer: {
                        icon: 'path://M2.9,0.7L2.9,0.7c1.4,0,2.6,1.2,2.6,2.6v115c0,1.4-1.2,2.6-2.6,2.6l0,0c-1.4,0-2.6-1.2-2.6-2.6V3.3C0.3,1.9,1.5,0.7,2.9,0.7z',
                        length: '60%',
                        width: 6,
                        offsetCenter: [0, '-5%'],
                        itemStyle: { color: '#4a4a4a' }
                    },
                    axisTick: {
                        distance: -25,
                        length: 8,
                        lineStyle: { color: '#ffffff', width: 2 }
                    },
                    splitLine: {
                        distance: -30,
                        length: 30,
                        lineStyle: { color: '#ffffff', width: 4 }
                    },
                    axisLabel: {
                        distance: -48,
                        color: '#7a7a7a',
                        fontSize: 12,
                        formatter: function () { return ''; }
                    },
                    title: {
                        offsetCenter: [0, '20%'],
                        fontSize: 16,
                        fontWeight: 600,
                        color: '#34495e',
                        show: false
                    },
                    detail: {
                        offsetCenter: [0, '40%'],
                        fontSize: 14,
                        color: '#7a7a7a',
                        formatter: ''
                    },
                    data: [{ value: cat.center, name: '' }]
                }]
            };

            myChart.setOption(option, true);
            window.addEventListener('resize', () => myChart && myChart.resize());
        }

        function fillSummary(summary) {
            const cats = ['normal', 'potencial', 'deficiente', 'dudoso', 'perdida'];
            cats.forEach(k => {
                const cant = (summary && summary[k] && summary[k].cantidad) || 0;
                $(`#sum-${k}`).text(cant);
            });
        }

        function renderCreditsList(credits, filter) {
            const tbody = $('#credits-tbody');
            const empty = $('#credits-empty');
            const badge = $('#credits-filter-badge');
            const table = $('#credits-table');

            tbody.empty();

            const list = (!filter || filter === 'all')
                ? (credits || [])
                : (credits || []).filter(c => c.cat_key === filter);

            const filterLabels = {
                all: 'Todas las categorías',
                normal: 'Normal',
                potencial: 'Problemas potenciales',
                deficiente: 'Deficiente',
                dudoso: 'Dudoso',
                perdida: 'Pérdida'
            };
            badge.html(`<i class="bx bx-filter-alt"></i> ${filterLabels[filter] || 'Todas las categorías'} (${list.length})`);

            if (!list || list.length === 0) {
                table.hide();
                empty.show();
                return;
            }
            table.show();
            empty.hide();

            list.forEach((c, idx) => {
                const dias = c.max_dias_atraso;
                const diasLabel = `${dias} día${dias === 1 ? '' : 's'}`;
                const row = `
                    <tr>
                        <td>${idx + 1}</td>
                        <td>${escapeHtml(c.cliente_nombre || '-')}</td>
                        <td>${escapeHtml(c.cliente_documento || '-')}</td>
                        <td>${escapeHtml(c.sede_nombre || '-')}</td>
                        <td class="num">${formatMoney(c.impo_cre)}</td>
                        <td class="num">${formatMoney(c.saldo_pendiente)}</td>
                        <td class="num">${diasLabel}</td>
                        <td><span class="credits-cat-badge" style="background:${c.color_hex};">${escapeHtml(c.categoria || '-')}</span></td>
                    </tr>
                `;
                tbody.append(row);
            });
        }

        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function setActivePill(filter) {
            $('#summary-bar .summary-pill').removeClass('active');
            $('#summary-bar .summary-pill[data-filter="' + (filter || 'all') + '"]').addClass('active');
        }

        function formatMoney(amount) {
            const n = parseFloat(amount) || 0;
            return 'S/ ' + n.toLocaleString('es-PE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function renderSedes(sedes) {
            const container = $('#sedes-cards-row');
            container.empty();

            if (!sedes || sedes.length === 0) {
                $('#sedes-empty').show();
                return;
            }
            $('#sedes-empty').hide();

            sedes.forEach(function(sede) {
                const total = sede.total_creditos || 1;
                const pct = (n) => total > 0 ? ((n / total) * 100).toFixed(0) : 0;

                const pctNormal     = pct(sede.normal.cantidad);
                const pctPotencial  = pct(sede.potencial.cantidad);
                const pctDeficiente = pct(sede.deficiente.cantidad);
                const pctDudoso     = pct(sede.dudoso.cantidad);
                const pctPerdida    = pct(sede.perdida.cantidad);

                const cardHtml = `
                    <div class="col-md-6 mb-3">
                        <div class="sede-card">
                            <div class="sede-card-header">
                                <div class="sede-card-name">${sede.sede_nombre}</div>
                                <span class="sede-card-count">${sede.total_creditos} crés.</span>
                            </div>
                            <div class="sede-card-total">${formatMoney(sede.total_saldo)}</div>
                            <div class="progress-stacked" title="Distribución por categoría">
                                <div class="progress-segment segment-normal"     style="width: ${pctNormal}%;"     title="Normal: ${pctNormal}%"></div>
                                <div class="progress-segment segment-potencial"  style="width: ${pctPotencial}%;"  title="Potencial: ${pctPotencial}%"></div>
                                <div class="progress-segment segment-deficiente" style="width: ${pctDeficiente}%;" title="Deficiente: ${pctDeficiente}%"></div>
                                <div class="progress-segment segment-dudoso"     style="width: ${pctDudoso}%;"     title="Dudoso: ${pctDudoso}%"></div>
                                <div class="progress-segment segment-perdida"    style="width: ${pctPerdida}%;"    title="Pérdida: ${pctPerdida}%"></div>
                            </div>
                            <div class="legend-pills">
                                <span class="legend-pill segment-normal">${sede.normal.cantidad} Normal</span>
                                <span class="legend-pill segment-potencial">${sede.potencial.cantidad} Potencial</span>
                                <span class="legend-pill segment-deficiente">${sede.deficiente.cantidad} Defic.</span>
                                <span class="legend-pill segment-dudoso">${sede.dudoso.cantidad} Dudoso</span>
                                <span class="legend-pill segment-perdida">${sede.perdida.cantidad} Pérdida</span>
                            </div>
                        </div>
                    </div>
                `;
                container.append(cardHtml);
            });
        }

        $(function () {
            const urlData = "{{ route('reportes.kpi_creditos.data') }}";

            // Estado en memoria del último fetch y del filtro actual
            let lastCredits = [];
            let currentFilter = 'all';

            // Ocultar el preloader global del layout (igual que en las demás vistas)
            $(".loader").fadeOut("slow");

            // Pintar el gauge con datos vacíos primero (evita quedarse en blanco si la API tarda/falla)
            renderGauge({});
            setActivePill(currentFilter);
            renderCreditsList([], currentFilter);

            function fetchData() {
                const sedeId = $('#filter-sede').val() || 'all';
                const hint = $('#sede-filter-hint');
                hint.text('Cargando...');

                $.ajax({
                    url: urlData,
                    method: 'GET',
                    data: { sede_id: sedeId },
                    timeout: 15000,
                    success: function (response) {
                        console.log('KPI response:', response);
                        const summary = (response && response.summary) || {};
                        const sedesSummary = (response && response.sedes_summary) || [];
                        lastCredits = (response && response.credits) || [];

                        renderGauge(summary);
                        fillSummary(summary);
                        renderSedes(sedesSummary);
                        renderCreditsList(lastCredits, currentFilter);

                        const total = (summary && summary.total_creditos) || 0;
                        const label = sedeId === 'all' ? 'Todas las sedes' : 'Sede seleccionada';
                        hint.text(`${label} — ${total} crédito(s)`);
                    },
                    error: function (xhr, status, err) {
                        console.error('Error al cargar KPI:', status, err, xhr && xhr.status, xhr && xhr.responseText);
                        const code = xhr && xhr.status ? ` (HTTP ${xhr.status})` : '';
                        hint.text(`Error al cargar${code}`);
                        lastCredits = [];
                        renderCreditsList([], currentFilter);
                    }
                });
            }

            // Click en pills de categoría: filtra el listado (toggle: click en la misma la desactiva)
            $('#summary-bar').on('click', '.summary-pill', function () {
                const filter = $(this).data('filter');
                if (!filter) return;
                if (currentFilter === filter && filter !== 'all') {
                    currentFilter = 'all';
                } else {
                    currentFilter = filter;
                }
                setActivePill(currentFilter);
                renderCreditsList(lastCredits, currentFilter);
            });

            // Carga inicial
            fetchData();

            // Re-cargar al cambiar de sede
            $('#filter-sede').on('change', fetchData);
        });
    </script>
@endsection
