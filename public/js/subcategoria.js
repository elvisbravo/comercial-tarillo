urlgeeneral = $("#url_raiz_proyecto").val();
$("#actualizar").hide();

//FUNCION LOAD
window.addEventListener("load", function (event) {
    //listadomarca();
    $(".loader").fadeOut("slow");
    categorias(0);
    listadosubcategorias();
});

function categorias(valor) {
    contenido = "";
    contenido += '<option value="">--Seleccionar--</option>';

    $.get(urlgeeneral + "/subcategorias/listadocategorias", function (data) {
        for (let index = 0; index < data.length; index++) {
            if (valor == data[index].id) {
                contenido +=
                    "<option value='" +
                    data[index].id +
                    "' selected>" +
                    data[index].categoria +
                    "</option >";
            } else {
                contenido +=
                    "<option value='" +
                    data[index].id +
                    "' >" +
                    data[index].categoria +
                    "</option >";
            }
        }

        document.getElementById("categoria_id").innerHTML = contenido;
    });
}

//LISTAR LAS SUBCATEOGRIAS
function listadosubcategorias() {
    $.get(urlgeeneral + "/subcategorias/listado", function (data) {
        console.log(data);

        llenarsubcategorias(data);
    });
}

function llenarsubcategorias(data) {
    let contenido = "";
    for (var i = 0; i < data.length; i++) {
        contenido += "<tr>";
        contenido +=
            "<td style='padding:1px;text-align:center'>" +
            parseInt(i + 1, 10) +
            "</td>";
        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            data[i].categoria +
            "</td>";
        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            data[i].subcategoria +
            "</td>";
        if (data[i].estado == 0) {
            contenido +=
                "<td style='padding:1px;text-align:center'> Inactivo</td>";
        } else {
            contenido +=
                "<td style='padding:1px;text-align:center'> Activo</td>";
        }

        contenido += "<td style='padding:1px;text-align:center'>";

        if (typeof canEdit !== "undefined" && canEdit) {
            contenido +=
                ' <button type="button" onclick="abrimodal(' +
                data[i].id +
                ')" class="btn btn-info waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fas fa-edit"></i></button>';
        }

        if (typeof canDelete !== "undefined" && canDelete) {
            if (data[i].estado == 0) {
                contenido +=
                    ' <button type="button" onclick="activar(' +
                    data[i].id +
                    ')" class="btn btn-warning "><i class="fas fa-sync activar"></i> </button>';
            } else {
                contenido +=
                    ' <button type="button" onclick="eliminar(' +
                    data[i].id +
                    ')" class="btn btn-danger "><i class="fas fa-trash-alt eliminar"></i> </button>';
            }
        }

        contenido += "</td>";
        contenido += "</tr>";
    }

    document.getElementById("listadosubcategorias").innerHTML = contenido;
    initDataTable("#datatable");
}

//metdo para guardar info

//GUARDAR LOS DATOS DE SECTOR
$("#guardar").on("click", function () {
    if (datosobligatorio() == true) {
        var frm = new FormData();
        var csrf = document.querySelector('meta[name="csrf-token"]').content;
        var subcategoria = $("#subcategoria").val();
        var selectcategoria_id =
            document.getElementById("categoria_id"); /*Obtener el SELECT */
        var categoria_id =
            selectcategoria_id.options[selectcategoria_id.selectedIndex].value;

        frm.append("categoria_id", categoria_id);
        frm.append("subcategoria", subcategoria);
        frm.append("_token", csrf);

        $.ajax({
            type: "POST",
            url: urlgeeneral + "/subcategorias/crear",
            data: frm,
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

                listadosubcategorias();
                $("#staticBackdrop").modal("hide");
            },
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

        $.get(urlgeeneral + "/subcategorias/editar/" + id, function (data) {
            //console.log(data["Codigo"]);
            $("#valor").val(id);
            document.getElementById("subcategoria").value =
                data["subcategoria"];
            categorias(data["categoria_id"]);
        });
    }
}

//METODO MODIFICAR LOS DATOS

$("#actualizar").on("click", function () {
    if (datosobligatorio() == true) {
        var frm = new FormData();
        var csrf = document.querySelector('meta[name="csrf-token"]').content;
        var subcategoria = $("#subcategoria").val();
        var selectcategoria_id =
            document.getElementById("categoria_id"); /*Obtener el SELECT */
        var categoria_id =
            selectcategoria_id.options[selectcategoria_id.selectedIndex].value;
        var id = $("#valor").val();

        frm.append("id", id);
        frm.append("categoria_id", categoria_id);
        frm.append("subcategoria", subcategoria);
        frm.append("_token", csrf);

        $.ajax({
            type: "POST",
            url: urlgeeneral + "/subcategorias/modificar",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: frm,
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
                listadosubcategorias();
                $("#staticBackdrop").modal("hide");

                //console.log(data);
            },
        });
    }
});

function eliminar(id) {
    const tabla = document.getElementById("datatable");

    tabla.addEventListener("click", (e) => {
        if (
            e.target.classList.contains("eliminar") ||
            e.target.classList.contains("bx")
        ) {
            Swal.fire({
                title: "¿Desea anular la subcategoria?",
                text: "No podrás revertir esto!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, Desactivar!",
                cancelButtonText: "Cancelar",
            }).then((result) => {
                if (result.isConfirmed) {
                    //Metodo para eleminar
                    var csrf = document.querySelector(
                        'meta[name="csrf-token"]',
                    ).content;
                    $.ajax({
                        type: "POST",
                        url: "subcategorias/eliminar/" + id,
                        data: { _method: "delete", _token: csrf },

                        success: function (data) {
                            listadosubcategorias();

                            Swal.fire(
                                "Anulado!",
                                "La subcategoria Anulada Correctamente.",
                                "success",
                            );
                        },
                    });
                }
            });
        }
    });
}

//ACTIVAR PROVEEDOR

function activar(id) {
    const tabla = document.getElementById("datatable");

    tabla.addEventListener("click", (e) => {
        if (
            e.target.classList.contains("activar") ||
            e.target.classList.contains("bx")
        ) {
            Swal.fire({
                title: "¿Desea Activar la SubCategoria?",
                text: "No podrás revertir esto!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, Activar!",
                cancelButtonText: "Cancelar",
            }).then((result) => {
                if (result.isConfirmed) {
                    //Metodo para eleminar
                    var csrf = document.querySelector(
                        'meta[name="csrf-token"]',
                    ).content;
                    $.ajax({
                        type: "POST",
                        url: "subcategorias/activar/" + id,
                        data: { _method: "delete", _token: csrf },

                        success: function (data) {
                            listadosubcategorias();

                            Swal.fire(
                                "Eliminado!",
                                "Activado Correctamente.",
                                "success",
                            );
                        },
                    });
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
            //alert("vacios");
            //obligarotio[i].parentNode.classList.add("form-control error");
            //swal("Here's a message!")
            //swal("Error!", "Los Campos Son Obligatorios!", "error")
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Los Campos categoria y subcategoria son Obligatorio!",
                footer: "",
            });
            //alert("Campos Obligatorios");
            //swal("Error!", "Los Campos Marcados de Rojo son requeridos!", "error")
            //alert("Los datos son Obliatorios");
        } else {
            obligarotio[i].parentNode.classList.remove("error");
        }
    }
    return bien;
}

//FUNCION LIMPIAR CAJAS DE INPUT
function limpiarcajasunidas() {
    var controles = document.getElementsByClassName("limpiar");
    var ncontroles = controles.length;
    //alert(ncontroles);
    for (var i = 0; i < ncontroles; i++) {
        controles[i].value = "";
    }
}
