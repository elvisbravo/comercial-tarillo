const urlgeeneral = $("#url_raiz_proyecto").val();
$("#actualizar").hide();

window.addEventListener("load", function (event) {
    getParentModuleList();
    $(".loader").fadeOut("slow");
});

function getParentModuleList() {
    $.get(urlgeeneral + "/modulo_padre/getListParentModule", function (data) {
        buildDataTable(data);
    });
}

function buildDataTable(data) {
    let contenidoHtml = "";
    for (let indice = 0; indice < data.length; indice++) {
        contenidoHtml += "<tr>";
        contenidoHtml += "<td>" + parseInt(indice + 1, 10) + "</td>";
        contenidoHtml += "<td> " + data[indice].name + "</td>";
        contenidoHtml += `<td> <i data-feather='${data[indice].icon}'></i></td>`;
        contenidoHtml += "<td> " + data[indice].order + "</td>";
        contenidoHtml += "<td class='icons-flex'>";
        contenidoHtml +=
            ' <button type="button" onclick="abrimodal(' +
            data[indice].id +
            ')" class="btn btn-info waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fas fa-edit"></i> Editar</button>';
        contenidoHtml +=
            '<button type="button" onclick="deleteParentModule(' +
            data[indice].id +
            ')" class="btn btn-danger waves-effect waves-light eliminar"><i class="fas fa-trash-alt"></i> Eliminar</button>';
        contenidoHtml += "</td>";
        contenidoHtml += "</tr>";
    }
    document.getElementById("listadecolores").innerHTML = contenidoHtml;
    $("#datatable").dataTable();
}

function abrimodal(id) {
    if (id == "0") {
        limpiarCajasunidas();
        $("#guardar").show();
        $("#actualizar").hide();
    } else {
        limpiarCajasunidas();
        $("#guardar").hide();
        $("#actualizar").show();
        $.get(
            urlgeeneral + "/modulo_padre/getParentModuleById/" + id,
            function (data) {
                $("#valor").val(id);
                document.getElementById("name").value = data["name"];
                document.getElementById("icon").value = data["icon"];
                document.getElementById("order").value = data["order"];
            }
        );
    }
}

$("#guardar").on("click", function () {
    if (datosObligatorios() == true) {
        let payload = new FormData();
        let csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let name = $("#name").val();
        let icon = $("#icon").val();
        let order = $("#order").val();
        payload.append("name", name);
        payload.append("icon", icon);
        payload.append("order", order);
        payload.append("_token", csrfToken);
        $.ajax({
            type: "POST",
            url: urlgeeneral + "/modulo_padre/create",
            data: payload,
            dataType: "json",
            contentType: false,
            processData: false,
            success: function (data) {
                Swal.fire({
                    icon: "success",
                    title: "Oops...",
                    text: "Creado Correctamente",
                    footer: "",
                });
                getParentModuleList();
                $("#staticBackdrop").modal("hide");
            },
        });
    }
});

$("#actualizar").on("click", function () {
    if (datosObligatorios() == true) {
        let payload = new FormData();
        let csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let name = $("#name").val();
        let icon = $("#icon").val();
        let order = $("#order").val();
        let id = $("#valor").val();
        payload.append("id", id);
        payload.append("name", name);
        payload.append("icon", icon);
        payload.append("order", order);
        payload.append("_token", csrfToken);
        $.ajax({
            type: "POST",
            url: urlgeeneral + "/modulo_padre/edit",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: payload,
            dataType: "json",
            contentType: false,
            processData: false,
            success: function (data) {
                Swal.fire({
                    icon: "success",
                    title: "Oops...",
                    text: "Modificado Correctamente",
                    footer: "",
                });
                getParentModuleList();
                $("#staticBackdrop").modal("hide");
            },
        });
    }
});

function deleteParentModule(id) {
    const tabla = document.getElementById("datatable");
    tabla.addEventListener("click", (e) => {
        if (
            e.target.classList.contains("eliminar") ||
            e.target.classList.contains("bx")
        ) {
            Swal.fire({
                title: "¿Desea eliminar el módulo padre?",
                text: "No podrás revertir esto!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, eliminar!",
                cancelButtonText: "Cancelar",
            }).then((result) => {
                if (result.isConfirmed) {
                    let csrfToken = document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content;
                    $.ajax({
                        type: "POST",
                        url: "modulo_padre/delete/" + id,
                        data: { _method: "delete", _token: csrfToken },
                        success: function (data) {
                            getParentModuleList();
                            Swal.fire(
                                "Eliminado",
                                "Registro eliminado correctamente.",
                                "success"
                            );
                        },
                        error: function (xhr, status, error) {
                            if (xhr.status === 500) {
                                Swal.fire(
                                    "Error",
                                    xhr.responseJSON.error,
                                    "error"
                                );
                            } else if (xhr.status === 404) {
                                Swal.fire(
                                    "Error",
                                    xhr.responseJSON.error,
                                    "error"
                                );
                            } else {
                                Swal.fire(
                                    "Error",
                                    "Ha ocurrido un error inesperado. Por favor, inténtalo de nuevo más tarde.",
                                    "error"
                                );
                            }
                        }
                    });
                }
            });
        }
    });
}

function datosObligatorios() {
    let isValidRequiredFields = true;
    let obligarotio = document.getElementsByClassName("obligatorio");
    let ncontroles = obligarotio.length;
    for (let indice = 0; indice < ncontroles; indice++) {
        if (obligarotio[indice].value == "") {
            isValidRequiredFields = false;
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Los campos marcados con (*) son obligatorios",
                footer: "",
            });
        } else {
            obligarotio[indice].parentNode.classList.remove("error");
        }
    }
    return isValidRequiredFields;
}

function limpiarCajasunidas() {
    let controles = document.getElementsByClassName("limpiar");
    let ncontroles = controles.length;
    for (let indice = 0; indice < ncontroles; indice++) {
        controles[indice].value = "";
    }
}
