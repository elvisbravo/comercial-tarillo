const urlgeeneral = $("#url_raiz_proyecto").val();
$("#actualizar").hide();
let dataParentModules;
const btnRadioSi = document.getElementById("btnradio_si");
const btnRadioNo = document.getElementById("btnradio_no");

window.addEventListener("load", function (event) {
    getListFunctions();
    $(".loader").fadeOut("slow");
});

function getListFunctions() {
    $.get(urlgeeneral + "/funcion/getListFunction", function (data) {
        buildDataTable(data);
    });
}

function buildDataTable(data) {
    let contenidoHtml = "";
    for (let indice = 0; indice < data.length; indice++) {
        contenidoHtml += "<tr>";
        contenidoHtml += "<td>" + parseInt(indice + 1, 10) + "</td>";
        contenidoHtml += "<td> " + data[indice].name + "</td>";
        contenidoHtml +=
            "<td> " + (data[indice].icon ? data[indice].icon : "") + "</td>";
        contenidoHtml +=
            "<td> " + (data[indice].class ? data[indice].class : "") + "</td>";
        contenidoHtml +=
            "<td> " + (data[indice].order ? data[indice].order : "") + "</td>";
        contenidoHtml +=
            "<td> " + (data[indice].button ? "Si" : "No") + "</td>";
        contenidoHtml += "<td class='icons-flex'>";
        contenidoHtml +=
            ' <button type="button" onclick="abrimodal(' +
            data[indice].id +
            ')" class="btn btn-info waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fas fa-edit"></i> Editar</button>';
        contenidoHtml +=
            '<button type="button" onclick="deleteFunction(' +
            data[indice].id +
            ')" class="btn btn-danger waves-effect waves-light eliminar"><i class="fas fa-trash-alt"></i> Eliminar</button>';
        contenidoHtml += "</td>";
        contenidoHtml += "</tr>";
    }
    document.getElementById("listadecolores").innerHTML = contenidoHtml;
    $("#datatable").dataTable();
}

function abrimodal(id) {
    limpiarCajasUnidas();
    if (id == "0") {
        $("#guardar").show();
        $("#actualizar").hide();
    } else {
        $("#guardar").hide();
        $("#actualizar").show();
        $.get(urlgeeneral + "/funcion/getFunctionById/" + id, function (data) {
            setChecked(data["button"]);
            $("#valor").val(id);
            document.getElementById("name").value = data["name"];
            document.getElementById("icon").value = data["icon"];
            document.getElementById("class").value = data["class"];
            document.getElementById("order").value = data["order"];
        });
    }
}

$("#guardar").on("click", function () {
    if (datosObligatorios() == true) {
        let payload = new FormData();
        let csrfToken = document.querySelector(
            'meta[name="csrf-token"]'
        ).content;
        let name = $("#name").val();
        let icon = $("#icon").val();
        let className = $("#class").val();
        let order = $("#order").val();
        let button = btnRadioSi.checked ? true : false;
        payload.append("name", name);
        payload.append("icon", icon);
        payload.append("class", className);
        payload.append("order", order);
        payload.append("button", button);
        payload.append("_token", csrfToken);
        $.ajax({
            type: "POST",
            url: urlgeeneral + "/funcion/create",
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
                getListFunctions();
                $("#staticBackdrop").modal("hide");
            },
        });
    }
});

$("#actualizar").on("click", function () {
    if (datosObligatorios() == true) {
        let payload = new FormData();
        let csrfToken = document.querySelector(
            'meta[name="csrf-token"]'
        ).content;
        let name = $("#name").val();
        let icon = $("#icon").val();
        let className = $("#class").val();
        let order = $("#order").val();
        let button = btnRadioSi.checked ? true : false;
        let id = $("#valor").val();
        payload.append("id", id);
        payload.append("name", name);
        payload.append("icon", icon);
        payload.append("class", className);
        payload.append("order", order);
        payload.append("button", button);
        payload.append("_token", csrfToken);
        $.ajax({
            type: "POST",
            url: urlgeeneral + "/funcion/edit",
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
                getListFunctions();
                $("#staticBackdrop").modal("hide");
            },
        });
    }
});

function setChecked(value) {
    if (value) {
        btnRadioSi.checked = value;
        btnRadioNo.checked = false;
    } else {
        btnRadioSi.checked = false;
        btnRadioNo.checked = true;
    }
}

function deleteFunction(id) {
    const tabla = document.getElementById("datatable");
    tabla.addEventListener("click", (e) => {
        if (
            e.target.classList.contains("eliminar") ||
            e.target.classList.contains("bx")
        ) {
            Swal.fire({
                title: "¿Desea eliminar la función?",
                text: "No podrás revertir esto!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, eliminar!",
                cancelButtonText: "Cancelar",
            }).then((result) => {
                if (result.isConfirmed) {
                    let csrf = document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content;
                    $.ajax({
                        type: "POST",
                        url: "funcion/delete/" + id,
                        data: { _method: "delete", _token: csrf },
                        success: function (data) {
                            getListFunctions();
                            Swal.fire(
                                "Eliminado!",
                                "Función eliminada Correctamente.",
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
            return isValidRequiredFields;
        } else {
            obligarotio[indice].parentNode.classList.remove("error");
        }
    }
    if (btnRadioSi.checked === false && btnRadioNo.checked === false) {
        isValidRequiredFields = false;
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Los campos marcados con (*) son obligatorios",
            footer: "",
        });
        return isValidRequiredFields;
    }
    return isValidRequiredFields;
}

function limpiarCajasUnidas() {
    let controles = document.getElementsByClassName("limpiar");
    let ncontroles = controles.length;
    btnRadioSi.checked = false;
    btnRadioNo.checked = false;
    for (let indice = 0; indice < ncontroles; indice++) {
        controles[indice].value = "";
    }
}
