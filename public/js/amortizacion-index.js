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
    let contenido = "";
    for (var i = 0; i < data.length; i++) {
        contenido += "<tr>";
        contenido +=
            "<td style='padding:1px;text-align:center'>" + data[i].id + "</td>";
        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            data[i].documento +
            "</td>";
        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            data[i].razon_social +
            "</td>";
        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            data[i].productos +
            "</td>";
        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            data[i].fpag_cre +
            "</td>";
        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            data[i].peri_cre +
            "</td>";
        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            data[i].periodo_pago +
            "</td>";
        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            data[i].impo_cre +
            "</td>";

        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            data[i].saldo_pendiente +
            "</td>";

        contenido +=
            "<td style='padding:1px;text-align:center'> " +
            data[i].esta_cre +
            "</td>";

        contenido += "<td style='padding:1px;text-align:center'>";
        //contenido +='<i class="fas fa-edit"></i>';
        contenido +=
            ' <button type="button" onclick="abrimodal(' +
            data[i].id +
            ')" class="btn btn-warning " data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fas fa-eye"></i> </button>';
        contenido +=
            '<a href="amortizacion/' +
            data[i].id +
            '/edit" type="button" class="btn btn-info " ><i class="fas fa-check"></i> </a>';

        contenido += "</td>";
        contenido += "</tr>";
    }

    document.getElementById("lisatadocredtios").innerHTML = contenido;
    $("#datatable").dataTable();
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
            $("#datatable").dataTable();
        },
    );
}
