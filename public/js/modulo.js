const urlGeneral = $("#url_raiz_proyecto").val();
$("#actualizar").hide();
let dataParentModules;
const elementSelectFunctions = document.getElementById("idsFunctions");
let arrayIdsFunctions = [];
let choicesInstance;

window.addEventListener("load", function (event) {
    getParentModuleList(0);
    getListModule();
    $(".loader").fadeOut("slow");
    choicesInstance = new Choices("#idsFunctions", { removeItemButton: true });
});

elementSelectFunctions.addEventListener("addItem", function (event) {
    arrayIdsFunctions.push(event.detail.value);
});

elementSelectFunctions.addEventListener("removeItem", function (event) {
    let indexToRemove = arrayIdsFunctions.indexOf(event.detail.value);
    if (indexToRemove !== -1) {
        arrayIdsFunctions.splice(indexToRemove, 1);
    }
});

function getListModule() {
    $.get(urlGeneral + "/modulo/getListModule", function (data) {
        buildDataTable(data);
    });
}

function buildDataTable(data) {
    let contenidoHtml = "";
    for (let indice = 0; indice < data.length; indice++) {
        contenidoHtml += "<tr>";
        contenidoHtml += "<td>" + parseInt(indice + 1, 10) + "</td>";
        contenidoHtml += "<td> " + data[indice].name + "</td>";
        contenidoHtml += "<td> " + data[indice].modulo_padre.name + " </td>";
        contenidoHtml += "<td> " + data[indice].url + "</td>";
        contenidoHtml += "<td> " + data[indice].order + "</td>";
        contenidoHtml += "<td class='icons-flex'>";
        contenidoHtml +=
            ' <button type="button" onclick="abrimodal(' +
            data[indice].id +
            ')" class="btn btn-info waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fas fa-edit"></i> Editar</button>';
        contenidoHtml +=
            '<button type="button" onclick="deleteModule(' +
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

        $.get(urlGeneral + "/modulo/getModuleById/" + id, function (data) {
            $("#valor").val(id);
            document.getElementById("name").value = data["name"];
            document.getElementById("url").value = data["url"];
            document.getElementById("order").value = data["order"];
            buildSelectParentModules(data["idmodulo_padre"]);
            data.modulo_funcion.forEach((element) => {
                choicesInstance.setChoiceByValue(`${element.idfuncion}`);
            });
        });
    }
}

function getParentModuleList(valor) {
    $.get(urlGeneral + "/modulo_padre/getListParentModule", function (data) {
        dataParentModules = data;
        buildSelectParentModules(valor);
    });
}

function buildSelectParentModules(valor) {
    contenidoHtml = "";
    contenidoHtml += '<option value="">--Seleccionar--</option>';
    for (let index = 0; index < dataParentModules.length; index++) {
        if (valor == dataParentModules[index].id) {
            contenidoHtml +=
                "<option value='" +
                dataParentModules[index].id +
                "' selected>" +
                dataParentModules[index].name +
                "</option >";
        } else {
            contenidoHtml +=
                "<option value='" +
                dataParentModules[index].id +
                "' >" +
                dataParentModules[index].name +
                "</option >";
        }
    }
    document.getElementById("idmodulo_padre").innerHTML = contenidoHtml;
}

$("#guardar").on("click", function () {
    if (datosObligatorios() == true) {
        let payload = new FormData();
        let csrfToken = document.querySelector(
            'meta[name="csrf-token"]'
        ).content;
        let name = $("#name").val();
        let url = $("#url").val();
        let order = $("#order").val();
        let selectidmodulo_padre = document.getElementById("idmodulo_padre");
        let idmodulo_padre =
            selectidmodulo_padre.options[selectidmodulo_padre.selectedIndex]
                .value;
        payload.append("name", name);
        payload.append("url", url);
        payload.append("order", order);
        payload.append("idmodulo_padre", idmodulo_padre);
        payload.append("idsFunctions", arrayIdsFunctions);
        payload.append("_token", csrfToken);
        $.ajax({
            type: "POST",
            url: urlGeneral + "/modulo/create",
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
                getListModule();
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
        let url = $("#url").val();
        let order = $("#order").val();
        let selectidmodulo_padre = document.getElementById("idmodulo_padre");
        let idmodulo_padre =
            selectidmodulo_padre.options[selectidmodulo_padre.selectedIndex]
                .value;
        let id = $("#valor").val();
        payload.append("id", id);
        payload.append("name", name);
        payload.append("url", url);
        payload.append("order", order);
        payload.append("idmodulo_padre", idmodulo_padre);
        payload.append("idsFunctions", arrayIdsFunctions);
        payload.append("_token", csrfToken);
        $.ajax({
            type: "POST",
            url: urlGeneral + "/modulo/edit",
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
                getListModule();
                $("#staticBackdrop").modal("hide");
            },
        });
    }
});

function deleteModule(id) {
    const table = document.getElementById("datatable");
    table.addEventListener("click", (e) => {
        if (
            e.target.classList.contains("eliminar") ||
            e.target.classList.contains("bx")
        ) {
            Swal.fire({
                title: "¿Desea eliminar el módulo?",
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
                        url: "modulo/delete/" + id,
                        data: { _method: "delete", _token: csrfToken },
                        success: function (data) {
                            getListModule();
                            Swal.fire(
                                "Eliminado!",
                                "Módulo eliminado Correctamente.",
                                "success"
                            );
                        },
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
    if (arrayIdsFunctions.length === 0) {
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
    let selectedValues = choicesInstance.getValue();
    if (selectedValues.length > 0) {
        choicesInstance.removeActiveItems();
        arrayIdsFunctions = [];
    }
    for (let indice = 0; indice < ncontroles; indice++) {
        controles[indice].value = "";
    }
}
