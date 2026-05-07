urlgeeneral = $("#url_raiz_proyecto").val();
const canDelete = $("#can_delete").val() == "1";
const canViewDetail = $("#can_view_detail").val() == "1";

window.addEventListener("load", function (event) {
    listadocompras();
    $(".loader").fadeOut("slow");
});

//FUNCION LISTADO

function listadocompras() {
    $.ajax({
        url: urlgeeneral + "/compras/listacompras",
        type: "GET",
        cache: false,
        success: function (data) {
            llenardata(data);
        },
    });
}

function llenardata(data) {
    const estados = {
        0: '<div class="badge badge-soft-danger font-size-12">ANULADO</div>',
        1: '<div class="badge badge-soft-success font-size-12">ACTIVA</div>',
    };

    let contenido = "";
    for (var i = 0; i < data.length; i++) {
        const estdo_guia = estados[data[i].estado] || "ESTADO DESCONOCIDO";

        contenido += "<tr>";
        contenido +=
            "<td style='padding:1px;text-align:center'>" +
            parseInt(i + 1, 10) +
            "</td>";
        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            data[i].nombre_comercial +
            "</td>";
        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            data[i].serie_comprobante +
            "-" +
            data[i].correlativo_comprobante +
            "</td>";
        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            data[i].total_compra +
            "</td>";
        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            data[i].descripcion +
            "</td>";
        let fecha = data[i].fecha_compra.split("-");
        let fecha_formateada = fecha[2] + "-" + fecha[1] + "-" + fecha[0];

        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            fecha_formateada +
            "</td>";
        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            estdo_guia +
            "</td>";
        contenido += "<td style='padding:1px;text-align:center'>";

        if (typeof canViewDetail !== "undefined" && canViewDetail) {
            let num_comprobante =
                data[i].serie_comprobante +
                "-" +
                data[i].correlativo_comprobante;
            contenido +=
                ' <button type="button" onclick="abrimodal(' +
                data[i].id +
                ", '" +
                num_comprobante +
                "'" +
                ')" class="btn btn-warning waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#staticBackdrop" title="Ver Detalle"><i class="fas fa-eye"></i> </button>';
        }

        if (
            data[i].estado != 0 &&
            typeof canDelete !== "undefined" &&
            canDelete
        ) {
            contenido +=
                ' <button type="button" onclick="eliminarcompra(' +
                data[i].id +
                ')" class="btn btn-danger waves-effect waves-light eliminar" title="Anular"><i class="fas fa-trash-alt"></i> </button>';
        }

        contenido += "</td>";
        contenido += "</tr>";
    }

    // 1. Destruimos la instancia de DataTable si ya existe para evitar conflictos en el refresco
    if ($.fn.DataTable.isDataTable("#datatable")) {
        $("#datatable").DataTable().destroy();
    }

    // 2. Actualizamos el HTML
    $("#listadocompras").html(contenido);

    // 3. Reinicializamos la tabla
    initDataTable("#datatable");
}

function abrimodal(id, comprobante) {
    if (id == "0") {
        if (typeof limpiarcajasunidas === "function") {
            limpiarcajasunidas();
        }

        $("#staticBackdropLabel").html("Nueva Compra");
        $("#guardar").show();
        $("#actualizar").hide();
    } else {
        // Actualizamos el título del modal con el número de comprobante
        $("#staticBackdropLabel").html(
            "Detalle de la Compra " + (comprobante || ""),
        );

        // Mostramos el loader y ocultamos el contenido para una carga limpia
        $("#loader_modal").show();
        $("#content_modal").hide();

        // 1. Destruimos la instancia de DataTable si ya existe para liberar la memoria y el DOM
        if ($.fn.DataTable.isDataTable("#datatabledos")) {
            $("#datatabledos").DataTable().destroy();
        }

        // 2. Limpiamos el contenido previo inmediatamente
        $("#detalle").empty();

        // 3. Usamos $.ajax con cache:false para obligar al navegador a traer datos nuevos
        $.ajax({
            url: urlgeeneral + "/compras/ver/" + id,
            type: "GET",
            cache: false,
            success: function (data) {
                let contenido = "";
                if (data.length > 0) {
                    for (var i = 0; i < data.length; i++) {
                        contenido += "<tr>";
                        contenido +=
                            "<td style='padding:1px;text-align:center'>" +
                            parseInt(i + 1, 10) +
                            "</td>";
                        contenido +=
                            "<td style='padding:1px;'> " +
                            data[i].nomb_pro +
                            "</td>";
                        contenido +=
                            "<td style='padding:1px;text-align:center'> " +
                            data[i].cantidad +
                            "</td>";
                        contenido +=
                            "<td style='padding:1px;text-align:center'> " +
                            data[i].descripcion +
                            "</td>";
                        contenido +=
                            "<td style='padding:1px;text-align:center'> " +
                            data[i].precio +
                            "</td>";
                        contenido +=
                            "<td style='padding:1px;text-align:center'> " +
                            data[i].subtotal +
                            "</td>";

                        contenido += "</tr>";
                    }
                } else {
                    contenido =
                        '<tr><td colspan="6" class="text-center">No hay productos registrados</td></tr>';
                }

                // 4. Insertamos los nuevos datos
                $("#detalle").html(contenido);

                // 5. Inicializamos DataTables con un pequeño retraso para asegurar el renderizado
                setTimeout(function () {
                    initDataTable("#datatabledos");
                    // Ocultamos el loader y mostramos el contenido
                    $("#loader_modal").hide();
                    $("#content_modal").fadeIn();
                }, 200);
            },
            error: function () {
                $("#loader_modal").hide();
                $("#content_modal").show();
                $("#detalle").html(
                    '<tr><td colspan="6" class="text-center text-danger">Error al cargar los detalles de la compra</td></tr>',
                );
            },
        });
    }
}

function eliminarcompra(id) {
    Swal.fire({
        title: "¿Desea Anular la Compra?",
        text: "No podrás revertir esto!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si, Anular!",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            var csrf = document.querySelector('meta[name="csrf-token"]').content;
            $.ajax({
                type: "POST",
                url: urlgeeneral + "/compras/eliminar/" + id,
                data: { _method: "delete", _token: csrf },
                success: function (data) {
                    if (data.respuesta === "error") {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: data.mensaje,
                        });
                    } else {
                        Swal.fire({
                            icon: "success",
                            title: "Anulado",
                            text: data.mensaje,
                        }).then(() => {
                            listadocompras();
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Ocurrió un error al intentar anular la compra",
                    });
                },
            });
        }
    });
}
