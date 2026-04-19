urlgeeneral = $("#url_raiz_proyecto").val();
$("#actualizar").hide();

//FUNCION LOAD
window.addEventListener("load", function (event) {
    $(".loader").fadeOut("slow");
    $("#datatabledos").dataTable();
});

//mandar como parametros, para listar reportes de acuerdo a ellos

function generar_reporte() {
    if (datosobligatorio() == true) {
        let selecAlmacen = document.getElementById("id");
        let stock_id = selecAlmacen.options[selecAlmacen.selectedIndex].value;

        $.get(
            urlgeeneral + "/reporte/listarinventario/" + stock_id,
            function (data) {
                console.log(data);
                $("#datatabledos").dataTable().fnDestroy();
                llenarinventario(data);
            },
        );
    }
}

function llenarinventario(data) {
    let contenido = "";
    for (var i = 0; i < data.length; i++) {
        contenido += "<tr>";

        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            data[i].id +
            "</td>";
        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            data[i].ubicacion +
            "</td>";
        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            data[i].nombrestock +
            "</td>";
        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            data[i].nomb_pro +
            "</td>";
        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            data[i].marca +
            "</td>";
        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            data[i].categoria +
            "</td>";
        let subcategoria_text = data[i].subcategoria !== null ? data[i].subcategoria : "";
        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            subcategoria_text +
            "</td>";
        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            data[i].unidad +
            "</td>";
        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            data[i].stock +
            "</td>";
        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            data[i].costo +
            "</td>";

        contenido += "<td style='padding:1px;text-align:center'>";
        //contenido +='<i class="fas fa-edit"></i>';
        contenido += "</td>";
        contenido += "</tr>";
    }

    document.getElementById("listainventario").innerHTML = contenido;

    $("#datatabledos").dataTable();
}

$("#exportarexcel").on("click", function () {
    //window.open("http://127.0.0.1:8000/facturacion/resumen/35" , "ventana1" , "width=420,height=600,scrollbars=NO")
    let selecAlmacen = document.getElementById("id");
    let stock_id = selecAlmacen.options[selecAlmacen.selectedIndex].value;

    window.location.href = "exportar/" + stock_id;
});

function datosobligatorio() {
    var bien = true;

    var obligarotio = document.getElementsByClassName("obligatorio");
    var ncontroles = obligarotio.length;

    for (var i = 0; i < ncontroles; i++) {
        if (obligarotio[i].value == "") {
            bien = false;
            /* alert("vacios");
            obligarotio[i].parentNode.classList.add("form-control error");
            swal("Here's a message!")
            swal("Error!", "Los Campos Son Obligatorios!", "error") */
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Todos los campos con * son Obligatorios!",
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
