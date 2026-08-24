urlgeeneral = $("#url_raiz_proyecto").val();
$("#actualizar").hide();

window.addEventListener("load", function (event) {
    listadoZonas();
    $(".loader").fadeOut("slow");
});

function listadoZonas() {
    $.get(urlgeeneral + "/zonas/listado", function (data) {
        llenarZonas(data);
    });
}

function llenarZonas(data) {
    if ($.fn.DataTable.isDataTable('#datatable')) {
        $('#datatable').DataTable().destroy();
    }

    let contenido = "";
    for (var i = 0; i < data.length; i++) {
        const esActivo = data[i].estado === 'ACTIVO';
        const badge = esActivo
            ? '<span class="badge bg-success">Activo</span>'
            : '<span class="badge bg-danger">Inactivo</span>';

        const nombreSede = data[i].sede ? data[i].sede.nombre : '-';

        contenido += "<tr>";
        contenido += "<td style='padding:1px;text-align:center'>" + parseInt(i + 1, 10) + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].nomb_zona + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + nombreSede + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>" + badge + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>";

        if (typeof canEdit !== 'undefined' && canEdit) {
            contenido += ' <button type="button" onclick="abrimodal(' + data[i].id + ')" class="btn btn-info waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fas fa-edit"></i></button>';
        }

        if (typeof canDelete !== 'undefined' && canDelete) {
            contenido += ' <button type="button" onclick="eliminarZona(' + data[i].id + ')" class="btn btn-danger waves-effect waves-light"><i class="fas fa-trash-alt"></i></button>';
        }

        contenido += "</td>";
        contenido += "</tr>";
    }

    $('#listadezonas').empty().html(contenido);
    initDataTable("#datatable");
}

$("#guardar").on("click", function () {
    if (datosobligatorio() == true) {
        var frm = new FormData();
        var csrf = document.querySelector('meta[name="csrf-token"]').content;
        var nomb_zona = $("#nomb_zona").val();
        var sede_id = $("#sede_id").val();

        frm.append("nomb_zona", nomb_zona);
        frm.append("sede_id", sede_id);
        frm.append("_token", csrf);

        $.ajax({
            type: "POST",
            url: urlgeeneral + "/zonas/crear",
            data: frm,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function (data) {
                Swal.fire({
                    icon: 'success',
                    title: 'Listo',
                    text: 'Zona creada correctamente',
                });

                listadoZonas();
                $('#staticBackdrop').modal('hide');
            }
        });
    }
});

function abrimodal(id) {
    if (id == "0") {
        limpiarcajasunidas();

        $("#guardar").show();
        $("#actualizar").hide();
    } else {
        $("#guardar").hide();
        $("#actualizar").show();

        $.get(urlgeeneral + "/zonas/edit/" + id, function (data) {
            $("#valor").val(id);
            document.getElementById("nomb_zona").value = data["nomb_zona"];
            document.getElementById("sede_id").value = data["sede_id"] ?? "";
        });
    }
}

$("#actualizar").on("click", function () {
    if (datosobligatorio() == true) {
        var frm = new FormData();
        var csrf = document.querySelector('meta[name="csrf-token"]').content;
        var nomb_zona = $("#nomb_zona").val();
        var sede_id = $("#sede_id").val();
        var id = $("#valor").val();

        frm.append("id", id);
        frm.append("nomb_zona", nomb_zona);
        frm.append("sede_id", sede_id);
        frm.append("_token", csrf);

        $.ajax({
            type: "POST",
            url: urlgeeneral + "/zonas/modificar",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: frm,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function (data) {
                Swal.fire({
                    icon: 'success',
                    title: 'Listo',
                    text: 'Zona modificada correctamente',
                });

                $('#staticBackdrop').modal('hide');
                listadoZonas();
            }
        });
    }
});

function eliminarZona(id) {
    Swal.fire({
        title: '¿Desea eliminar la zona?',
        text: "No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            var csrf = document.querySelector('meta[name="csrf-token"]').content;
            $.ajax({
                type: "POST",
                url: urlgeeneral + "/zonas/eliminar/" + id,
                data: { "_method": "delete", '_token': csrf },
                success: function (data) {
                    listadoZonas();

                    Swal.fire(
                        'Eliminada!',
                        'La zona ha sido eliminada.',
                        'success'
                    );
                }
            });
        }
    });
}

function datosobligatorio() {
    var bien = true;

    var obligarotio = document.getElementsByClassName("obligatorio");
    var ncontroles = obligarotio.length;

    for (var i = 0; i < ncontroles; i++) {
        if (obligarotio[i].value == "") {
            bien = false;
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'El campo Zona es Obligatorio!',
            });
        } else {
            obligarotio[i].parentNode.classList.remove("error");
        }
    }
    return bien;
}

function limpiarcajasunidas() {
    var controles = document.getElementsByClassName("limpiar");
    var ncontroles = controles.length;
    for (var i = 0; i < ncontroles; i++) {
        controles[i].value = "";
    }
}
