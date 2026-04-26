const urlgeneral = document.getElementById('url_raiz_proyecto').value;
const formNota = document.getElementById('form_nota_credito');
const csrf = document.querySelector('meta[name="csrf-token"]').content;

$(document).ready(function() {
    listadoventas();
});

function listadoventas() {
    initDataTable("#datatable", {
        processing: true,
        serverSide: true,
        ajax: {
            url: urlgeneral + "/ventas/listado",
            type: "GET"
        },
        columns: [
            { 
                data: null, 
                orderable: false, 
                searchable: false,
                className: "text-center align-middle",
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                } 
            },
            { 
                data: "fecha", 
                className: "text-left align-middle",
                render: function(data, type, row) {
                    return `${row.fecha_formateada} / ${row.hora}`;
                }
            },
            { 
                data: "serie_comprobante", 
                className: "text-left align-middle",
                render: function(data, type, row) {
                    let tipo = "";
                    if (row.tipo_comprobante_id == 1) tipo = "BOLETA DE VENTA";
                    else if (row.tipo_comprobante_id == 2) tipo = "FACTURA";
                    else if (row.tipo_comprobante_id == 3) tipo = "NOTA DE CREDITO";
                    else if (row.tipo_comprobante_id == 4) tipo = "NOTA DE DEBITO";
                    else if (row.tipo_comprobante_id == 5) tipo = "NOTA DE VENTA";
                    else tipo = "COTIZACION";

                    let html = `${tipo}: ${row.serie_comprobante || ''}-${row.numero_comprobante || ''}`;
                    
                    if (row.tipo_comprobante_id == 3) {
                        html += `<span class="d-block font-size-14"><i class="bx bx-down-arrow-circle text-info"></i> Modifica: ${row.serie_nota_credito || ''}-${row.numero_nota_credito || ''}</span>`;
                    }
                    
                    return html;
                }
            },
            { 
                data: "documento", 
                className: "text-left align-middle",
                render: function(data, type, row) {
                    let nombre = (row.nomb_per || '') + ' ' + (row.pate_per || '') + ' ' + (row.mate_per || '');
                    return `<p class="mb-1 mt-1">${row.documento || ''}</p>
                            <p class="mb-1">${nombre.trim()}</p>`;
                }
            },
            { data: "monto", className: "text-left align-middle", defaultContent: '' },
            { 
                data: "aceptado_sunat", 
                className: "text-left align-middle",
                render: function(data, type, row) {
                    let html = "";
                    if (row.aceptado_sunat == 1) {
                        html += '<div class="badge badge-soft-success font-size-12">SI</div> ';
                    } else {
                        html += '<div class="badge badge-soft-danger font-size-12">NO</div> ';
                    }

                    if (row.tipo_comprobante_id == 5 && row.estado_nota == 2) {
                        html += '<div class="badge badge-soft-danger font-size-12">ELIMINADO</div>';
                    }
                    return html;
                }
            },
            { 
                data: null, 
                orderable: false, 
                searchable: false,
                className: "text-left align-middle",
                render: function(data, type, row) {
                    let actions = `
                        <div class="dropdown">
                            <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-list-ul"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="./venta/ticket/${row.id}" target="_blank">Imprimir Comprobante</a></li>
                                <li><a class="dropdown-item" href="#">Detalle</a></li>
                                <li><a class="dropdown-item" href="#">Cambiar comprobante</a></li>`;
                    
                    if (row.tipo_comprobante_id == 1 || row.tipo_comprobante_id == 2) {
                        if (row.aceptado_sunat != 1) {
                            actions += `<li><a class="dropdown-item" href="#" onclick="enviar_comprobante(event, ${row.id})">Enviar Sunat</a></li>`;
                        }
                        if (row.aceptado_sunat == 1) {
                            actions += `<li><a class="dropdown-item" href="#" onclick="generarNotaCredito(event, ${row.id})">Crear nota de crédito</a></li>`;
                        }
                    } else {
                        if (row.estado_nota == 1) {
                            if (typeof canDelete !== 'undefined' && canDelete) {
                                actions += `<li><a class="dropdown-item" href="#" onclick="generarNotaVenta(event, ${row.id})">Eliminar Nota de Venta</a></li>`;
                            }
                        }
                    }

                    actions += `</ul></div>`;
                    return actions;
                }
            }
        ],
        order: [[0, 'desc']]
    });
}

function enviar_comprobante(e, id) {
    e.preventDefault();
    $("#modalEnviar").modal('show');
    
    fetch(urlgeneral + '/enviar-comprobante/'+id)
    .then(res => res.json())
    .then(data => {
        $("#modalEnviar").modal('hide');
        if (data.respuesta == "ok") {
            Swal.fire({
                position: 'top-center',
                icon: 'success',
                title: data.msj_sunat,
                showConfirmButton: false,
                timer: 2500
            });
            $('#datatable').DataTable().ajax.reload(null, false);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: data.mensaje
            })
        }
    })
}

function generarNotaCredito(e, id) {
    e.preventDefault();
    $("#modal_nota_credito").modal('show');
    document.getElementById('idsale').value = id;
}

if (formNota) {
    formNota.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(formNota);
        formData.append('_token', csrf);

        $("#modal_nota_credito").modal('hide');
        $("#modal_enviar_nota").modal('show');

        fetch(urlgeneral+"/generar-nota-credito", {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            $("#modal_enviar_nota").modal('hide');
            if (data.respuesta == 'ok') {
                Swal.fire({
                    position: 'top-center',
                    icon: 'success',
                    title: data.mensaje,
                    showConfirmButton: false,
                    timer: 2500
                });
                $('#datatable').DataTable().ajax.reload(null, false);
            } else {
                Swal.fire({
                    position: 'top-center',
                    icon: 'error',
                    title: data.mensaje,
                    showConfirmButton: false
                });
            }
        })
    });
}

function generarNotaVenta(e, id) {
    e.preventDefault();
    Swal.fire({
        title: '¿Dese eliminar la nota de venta?',
        text: "No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!'
      }).then((result) => {
        if (result.isConfirmed) {
            $("#modalEnviar").modal('show');
            document.getElementById('mensajeConfirmacion').textContent = 'Eliminando Nota de Venta';
            fetch(urlgeneral+"/delete-nota-venta/"+id)
            .then(res => res.json())
            .then(data => {
                $("#modalEnviar").modal('hide');
                if (data.respuesta == "ok") {
                    Swal.fire({
                        position: 'top-center',
                        icon: 'success',
                        title: data.mensaje,
                        showConfirmButton: false,
                        timer: 2500
                    });
                    $('#datatable').DataTable().ajax.reload(null, false);
                } else {
                    Swal.fire({
                        position: 'top-center',
                        icon: 'error',
                        title: "Intente de nuevo o comuniquese con el administrador del sistema",
                        showConfirmButton: false
                    });
                }
            })
        }
    })
}