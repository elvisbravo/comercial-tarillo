const urlGeneral = $("#url_raiz_proyecto").val();

$(document).ready(function() {
    getList();
});

function getList() {
    $.get(urlGeneral + "/acciones/getList", function(data) {
        buildDataTable(data);
    });
}

function buildDataTable(data) {
    let contenidoHtml = "";
    for (let i = 0; i < data.length; i++) {
        contenidoHtml += "<tr>";
        contenidoHtml += "<td>" + (i + 1) + "</td>";
        contenidoHtml += "<td>" + data[i].nombre + "</td>";
        contenidoHtml += "<td>";
        contenidoHtml += '<button type="button" onclick="abrimodal(' + data[i].id + ')" class="btn btn-info btn-sm waves-effect waves-light me-1" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fas fa-edit"></i> Editar</button>';
        contenidoHtml += '<button type="button" onclick="eliminar(' + data[i].id + ')" class="btn btn-danger btn-sm waves-effect waves-light"><i class="fas fa-trash-alt"></i> Eliminar</button>';
        contenidoHtml += "</td>";
        contenidoHtml += "</tr>";
    }

    if ($.fn.DataTable.isDataTable('#datatable')) {
        $('#datatable').DataTable().destroy();
    }
    
    $("#listado").html(contenidoHtml);
    $("#datatable").DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
        }
    });
}

function abrimodal(id) {
    limpiar();
    if (id == 0) {
        $("#id").val(0);
        $("#guardar").show();
        $("#actualizar").hide();
    } else {
        $("#guardar").hide();
        $("#actualizar").show();
        $.get(urlGeneral + "/acciones/getById/" + id, function(data) {
            $("#id").val(data.id);
            $("#nombre").val(data.nombre);
        });
    }
}

$("#guardar").on("click", function() {
    if (validar() == true) {
        $(this).prop('disabled', true);
        let payload = {
            nombre: $("#nombre").val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        $.post(urlGeneral + "/acciones", payload, function(data) {
            Swal.fire("Éxito", "Creado correctamente", "success");
            getList();
            $("#staticBackdrop").modal("hide");
        }).always(function() {
            $("#guardar").prop('disabled', false);
        });
    }
});

$("#actualizar").on("click", function() {
    if (validar() == true) {
        $(this).prop('disabled', true);
        let payload = {
            id: $("#id").val(),
            nombre: $("#nombre").val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        $.post(urlGeneral + "/acciones/update", payload, function(data) {
            Swal.fire("Éxito", "Actualizado correctamente", "success");
            getList();
            $("#staticBackdrop").modal("hide");
        }).always(function() {
            $("#actualizar").prop('disabled', false);
        });
    }
});

function eliminar(id) {
    Swal.fire({
        title: "¿Desea eliminar esta acción?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "DELETE",
                url: urlGeneral + "/acciones/" + id,
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function(data) {
                    Swal.fire("Eliminado", "La acción ha sido eliminada", "success");
                    getList();
                }
            });
        }
    });
}

function validar() {
    let isValid = true;
    $(".obligatorio").each(function() {
        if ($(this).val() == "") {
            $(this).addClass("is-invalid");
            isValid = false;
        } else {
            $(this).removeClass("is-invalid");
        }
    });

    if (!isValid) {
        Swal.fire("Error", "Complete los campos obligatorios", "error");
    }
    return isValid;
}

function limpiar() {
    $(".limpiar").val("");
    $(".is-invalid").removeClass("is-invalid");
}
