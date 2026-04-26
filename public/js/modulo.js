const urlGeneral = $("#url_raiz_proyecto").val();
$("#actualizar").hide();
let dataParentModules;

window.addEventListener("load", function (event) {
    getParentModuleList(0);
    getListModule();
    $(".loader").fadeOut("slow");
});

function getListModule() {
    $.get(urlGeneral + "/modulo/getListModule", function (data) {
        buildDataTable(data);
    });
}

function buildDataTable(data) {
    let contenidoHtml = "";
    for (let indice = 0; indice < data.length; indice++) {
        let parentName = "N/A";
        if (data[indice].padre_id == 0) {
            parentName = '<span class="badge bg-success">ES PADRE</span>';
        } else {
            // Buscar el nombre del padre en la lista de módulos raíz
            let parentObj = dataParentModules ? dataParentModules.find(m => m.id == data[indice].padre_id) : null;
            parentName = parentObj ? parentObj.name : "ID: " + data[indice].padre_id;
        }
        contenidoHtml += "<tr>";
        contenidoHtml += "<td>" + parseInt(indice + 1, 10) + "</td>";
        contenidoHtml += "<td> " + data[indice].name + "</td>";
        contenidoHtml += "<td> <i class='" + data[indice].icon + "'></i> " + data[indice].icon + "</td>";
        contenidoHtml += "<td> " + parentName + " </td>";
        contenidoHtml += "<td> " + data[indice].url + "</td>";
        contenidoHtml += "<td> " + data[indice].order + "</td>";
        contenidoHtml += "<td class='icons-flex'>";
        if (typeof canEdit !== 'undefined' && canEdit) {
            contenidoHtml +=
                ' <button type="button" onclick="abrimodal(' +
                data[indice].id +
                ')" class="btn btn-info waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fas fa-edit"></i> Editar</button>';
        }

        if (typeof canDelete !== 'undefined' && canDelete) {
            contenidoHtml +=
                '<button type="button" onclick="deleteModule(' +
                data[indice].id +
                ')" class="btn btn-danger waves-effect waves-light"><i class="fas fa-trash-alt"></i> Eliminar</button>';
        }
        contenidoHtml += "</td>";
        contenidoHtml += "</tr>";
    }
    if ($.fn.DataTable.isDataTable('#datatable')) {
        $('#datatable').DataTable().destroy();
    }
    document.getElementById("listadecolores").innerHTML = contenidoHtml;
    initDataTable("#datatable");
}

function abrimodal(id) {
    limpiarCajasUnidas();
    if (id == "0") {
        $("#guardar").show();
        $("#actualizar").hide();
        buildSelectParentModules(0);
    } else {
        $("#guardar").hide();
        $("#actualizar").show();

        $.get(urlGeneral + "/modulo/getModuleById/" + id, function (data) {
            $("#valor").val(id);
            document.getElementById("name").value = data["name"];
            document.getElementById("icon").value = data["icon"];
            document.getElementById("url").value = data["url"];
            document.getElementById("order").value = data["order"];
            buildSelectParentModules(data["padre_id"]);
        });
    }
}

function getParentModuleList(valor) {
    $.get(urlGeneral + "/modulo/getParentModules", function (data) {
        dataParentModules = data;
        buildSelectParentModules(valor);
        getListModule(); // Mover aquí para asegurar que dataParentModules esté listo para la tabla
    });
}

function buildSelectParentModules(valor) {
    let contenidoHtml = "";
    if (valor == 0) {
        contenidoHtml += '<option value="0" selected>-- ES MÓDULO PADRE --</option>';
    } else {
        contenidoHtml += '<option value="0">-- ES MÓDULO PADRE --</option>';
    }

    if (dataParentModules) {
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
    }
    document.getElementById("idmodulo_padre").innerHTML = contenidoHtml;
}

$("#guardar").on("click", function () {
    if (datosObligatorios() == true) {
        $(this).prop('disabled', true);
        let payload = new FormData();
        let csrfToken = document.querySelector(
            'meta[name="csrf-token"]'
        ).content;
        let name = $("#name").val();
        let icon = $("#icon").val();
        let url = $("#url").val();
        let order = $("#order").val();
        let selectpadre_id = document.getElementById("idmodulo_padre");
        let padre_id =
            selectpadre_id.options[selectpadre_id.selectedIndex]
                .value;
        payload.append("name", name);
        payload.append("icon", icon);
        payload.append("url", url);
        payload.append("order", order);
        payload.append("padre_id", padre_id);
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
                    title: "Exito",
                    text: "Creado Correctamente"
                });
                getParentModuleList(0);
                $("#staticBackdrop").modal("hide");
            },
            complete: function() {
                $("#guardar").prop('disabled', false);
            }
        });
    }
});

$("#actualizar").on("click", function () {
    if (datosObligatorios() == true) {
        $(this).prop('disabled', true);
        let payload = new FormData();
        let csrfToken = document.querySelector(
            'meta[name="csrf-token"]'
        ).content;
        let name = $("#name").val();
        let icon = $("#icon").val();
        let url = $("#url").val();
        let order = $("#order").val();
        let selectpadre_id = document.getElementById("idmodulo_padre");
        let padre_id =
            selectpadre_id.options[selectpadre_id.selectedIndex]
                .value;
        let id = $("#valor").val();
        payload.append("id", id);
        payload.append("name", name);
        payload.append("icon", icon);
        payload.append("url", url);
        payload.append("order", order);
        payload.append("padre_id", padre_id);
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
                    title: "Exito",
                    text: "Modificado Correctamente"
                });
                getParentModuleList(0);
                $("#staticBackdrop").modal("hide");
            },
            complete: function() {
                $("#actualizar").prop('disabled', false);
            }
        });
    }
});

function deleteModule(id) {
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
                url: urlGeneral + "/modulo/delete/" + id,
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
        }
    }
    return isValidRequiredFields;
}

function limpiarCajasUnidas() {
    let controles = document.getElementsByClassName("limpiar");
    let ncontroles = controles.length;
    for (let indice = 0; indice < ncontroles; indice++) {
        controles[indice].value = "";
    }
}
