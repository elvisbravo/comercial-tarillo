urlgeeneral = $("#url_raiz_proyecto").val();

window.addEventListener("load", function (event) {
    $(".loader").fadeOut("slow");
    //fechaactual();
    listar();
});

function listar() {
    $.get(urlgeeneral + "/amortizacion/creditos", function (data) {
        llenar(data);
    });
}
function llenar(data) {
    if ($.fn.DataTable.isDataTable('#datatable')) {
        $('#datatable').DataTable().destroy();
    }

    let contenido = "";
    for (var i = 0; i < data.length; i++) {
        contenido += "<tr>";
        contenido += "<td style='padding:1px;text-align:center'>" + data[i].id + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].documento + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].razon_social + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].productos + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].fpag_cre + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].peri_cre + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].periodo_pago + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].impo_cre + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].saldo_pendiente + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].esta_cre + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>";

        if (typeof canViewDetail !== 'undefined' && canViewDetail) {
            contenido += ' <button type="button" onclick="abrimodal(' + data[i].id + ')" class="btn btn-warning waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#staticBackdrop" title="Ver Detalle"><i class="fas fa-eye"></i> </button>';
        }

        if (typeof canCreate !== 'undefined' && canCreate) {
            contenido += ' <a href="amortizacion/' + data[i].id + '/edit" type="button" class="btn btn-info waves-effect waves-light" title="Amortizar"><i class="fas fa-check"></i> </a>';
        }

        contenido += ' <button type="button" onclick="cambiarSedeCredito(' + data[i].id + ')" class="btn btn-secondary waves-effect waves-light" title="Cambiar Sede"><i class="mdi mdi-map-marker"></i> </button>';

        contenido += "</td>";
        contenido += "</tr>";
    }

    $('#lisatadocredtios').empty().html(contenido);
    initDataTable("#datatable");
}

function abrimodal(params) {
    $.get(
        urlgeeneral + "/amortizacion/detalle_product/" + params,
        function (data) {
            let contenido = "";
            for (var i = 0; i < data.length; i++) {
                contenido += "<tr>";
                contenido +=
                    "<td style='padding:1px;text-align:center'> " +
                    data[i].descripcion +
                    "</td>";
                contenido +=
                    "<td style='padding:1px;text-align:center'> " +
                    data[i].cantidad +
                    "</td>";
                contenido +=
                    "<td style='padding:1px;text-align:center'> " +
                    data[i].subtotal +
                    "</td>";
                contenido += "</tr>";
            }

            document.getElementById("detalle").innerHTML = contenido;
            initDataTable("#datatabledos");
        },
    );
}

function cambiarSedeCredito(creditoId) {
    // Primero cargar las sedes disponibles
    $.get(urlgeeneral + "/sedes/listado", function(data) {
        if (data.length === 0) {
            Swal.fire('Error', 'No hay sedes disponibles', 'error');
            return;
        }

        let opciones = '<option value="">-- Seleccionar Sede --</option>';
        for (let i = 0; i < data.length; i++) {
            opciones += '<option value="' + data[i].id + '">' + data[i].nombre + '</option>';
        }

        Swal.fire({
            title: 'Cambiar Sede del Crédito',
            text: '¿Está seguro de cambiar la sede de este crédito?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'var(--bs-primary)',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar',
            html: '<select id="sede-select" class="form-select">' + opciones + '</select>',
            preConfirm: () => {
                return document.getElementById('sede-select').value;
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                $.post(urlgeeneral + "/amortizacion/cambiar-sede", {
                    _token: document.querySelector('meta[name="csrf-token"]').content,
                    credito_id: creditoId,
                    sede_id: result.value
                }, function(data) {
                    if (data.success) {
                        Swal.fire('Éxito', 'Sede cambiada correctamente', 'success');
                        listar();
                    } else {
                        Swal.fire('Error', data.message || 'No se pudo cambiar la sede', 'error');
                    }
                }).fail(function() {
                    Swal.fire('Error', 'No se pudo cambiar la sede', 'error');
                });
            }
        });
    });
}
