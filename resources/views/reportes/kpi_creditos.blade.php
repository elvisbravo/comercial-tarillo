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
            font-size: 24px;
            font-weight: 600;
            margin: 0;
            letter-spacing: 0.2px;
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
        }

        .summary-pill .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 6px;
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

            {{-- Gauge semicircular con ECharts --}}
            <div class="gauge-wrapper">
                <div id="kpi-gauge-chart" style="width: 100%; height: 100%;"></div>
            </div>

            {{-- Resumen numérico (oculto si no hay datos) --}}
            <div class="summary-bar" id="summary-bar" style="display: none;">
                <span class="summary-pill"><span class="dot" style="background:#28a745;"></span> Normal: <span id="sum-normal">0</span></span>
                <span class="summary-pill"><span class="dot" style="background:#8bc34a;"></span> Potencial: <span id="sum-potencial">0</span></span>
                <span class="summary-pill"><span class="dot" style="background:#ffc107;"></span> Deficiente: <span id="sum-deficiente">0</span></span>
                <span class="summary-pill"><span class="dot" style="background:#ff9800;"></span> Dudoso: <span id="sum-dudoso">0</span></span>
                <span class="summary-pill"><span class="dot" style="background:#dc3545;"></span> Pérdida: <span id="sum-perdida">0</span></span>
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

        </div>
    </div>
@endsection

@section('js')
    {{-- ECharts (ya está en assets/libs del proyecto; lo cargamos desde CDN por simplicidad) --}}
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>

    <script>
        $(document).ready(function () {
            const urlData = "{{ route('reportes.kpi_creditos.data') }}";

            $.ajax({
                url: urlData,
                method: 'GET',
                success: function (response) {
                    const summary = response.summary || {};
                    renderGauge(summary);
                    fillSummary(summary);
                },
                error: function (xhr) {
                    console.error('Error al cargar KPI:', xhr);
                    renderGauge({});
                }
            });

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
                    const cant = (summary[c.key] && summary[c.key].cantidad) || 0;
                    if (cant > max) {
                        max = cant;
                        dom = c;
                    }
                });
                // Si no hay datos, mantener el centro (deficiente)
                if (max <= 0) return CATEGORIES[2];
                return dom;
            }

            function renderGauge(summary) {
                const dom = document.getElementById('kpi-gauge-chart');
                const myChart = echarts.init(dom);
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
                        progress: {
                            show: false
                        },
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
                            itemStyle: {
                                color: '#4a4a4a'
                            }
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
                            formatter: function (val) {
                                // Etiquetas sólo en los puntos de quiebre de cada categoría
                                return '';
                            }
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

                myChart.setOption(option);
                window.addEventListener('resize', () => myChart.resize());
            }

            function fillSummary(summary) {
                const cats = ['normal', 'potencial', 'deficiente', 'dudoso', 'perdida'];
                const total = cats.reduce((acc, k) => acc + ((summary[k] && summary[k].cantidad) || 0), 0);
                if (total <= 0) {
                    $('#summary-bar').hide();
                    return;
                }
                $('#summary-bar').show();
                cats.forEach(k => {
                    const cant = (summary[k] && summary[k].cantidad) || 0;
                    $(`#sum-${k}`).text(cant);
                });
            }
        });
    </script>
@endsection
