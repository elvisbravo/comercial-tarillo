@extends('layouts.main')

@section('title')
Historial de Cambios de Sede
@endsection

@section('css')
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
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

    .table-historial {
        margin-bottom: 0;
        font-size: 0.85rem;
    }

    .table-historial thead {
        background: var(--bs-primary);
        color: white;
        border-radius: 8px 8px 0 0;
    }

    .table-historial thead th {
        font-weight: 600;
        padding: 0.75rem 0.6rem;
        border: none;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table-historial tbody tr {
        border-bottom: 1px solid #F0F0F0;
        transition: background-color 0.15s ease;
    }

    .table-historial tbody tr:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.04);
    }

    .table-historial tbody td {
        padding: 0.75rem 0.6rem;
        vertical-align: middle;
        border: none;
        color: #444;
    }

    .badge-sede {
        font-size: 0.75rem;
        padding: 0.4rem 0.6rem;
        border-radius: 6px;
    }

    .badge-actual {
        background-color: rgba(25, 135, 84, 0.15);
        color: #198754;
    }

    .badge-anterior {
        background-color: rgba(108, 117, 125, 0.15);
        color: #6c757d;
    }

    .fecha-badge {
        font-size: 0.78rem;
        color: #666;
    }

    @media (max-width: 767.98px) {
        .table-historial {
            font-size: 0.75rem;
        }

        .table-historial thead th,
        .table-historial tbody td {
            padding: 0.4rem 0.3rem;
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
                    <i class="mdi mdi-map-marker-swap me-2 text-primary"></i>Historial de Cambios de Sede
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0"></ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="row">
        <div class="col-12">
            <div class="card compra-card">
                <div class="card-header d-flex align-items-center">
                    <i class="mdi mdi-history me-2"></i>
                    Clientes que han cambiado de sede
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table id="datatable-historial" class="table table-historial">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Documento</th>
                                    <th>Sede</th>
                                    <th>Fecha</th>
                                    <th>Condición</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-historial">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@section('js')

<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

<script>
    urlgeeneral = $("#url_raiz_proyecto").val();

    window.addEventListener("load", function (event) {
        $(".loader").fadeOut("slow");
        listarHistorial();
    });

    function listarHistorial() {
        $.get(urlgeeneral + "/amortizacion/historial-cambios", function (data) {
            llenarTabla(data);
        });
    }

    function llenarTabla(data) {
        if ($.fn.DataTable.isDataTable('#datatable-historial')) {
            $('#datatable-historial').DataTable().destroy();
        }

        let contenido = "";

        if (data.length === 0) {
            contenido = '<tr><td colspan="5" class="text-center text-muted py-4">No hay registros de cambios de sede</td></tr>';
        } else {
            for (let i = 0; i < data.length; i++) {
                var badgeClass = data[i].estado == 1 ? 'badge-actual' : 'badge-anterior';
                var badgeIcon = data[i].estado == 1 ? 'mdi-map-marker-check' : 'mdi-map-marker';
                var condicion = data[i].estado == 1 ? 'ACTUAL' : 'ANTERIOR';

                contenido += "<tr>";
                contenido += "<td><strong>" + data[i].cliente + "</strong></td>";
                contenido += "<td>" + (data[i].documento || '-') + "</td>";
                contenido += "<td><span class='badge " + badgeClass + "'><i class='mdi " + badgeIcon + " me-1'></i>" + data[i].sede + "</span></td>";
                contenido += "<td><span class='fecha-badge'>" + data[i].fecha + "</span></td>";
                contenido += "<td><span class='badge " + badgeClass + "'>" + condicion + "</span></td>";
                contenido += "</tr>";
            }
        }

        $("#tabla-historial").html(contenido);

        $('#datatable-historial').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            "order": [[0, "asc"], [3, "asc"]]
        });
    }
</script>

@endsection
