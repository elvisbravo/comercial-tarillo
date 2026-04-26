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
    if ($.fn.DataTable.isDataTable('#datatable')) {
        $('#datatable').DataTable().destroy();
    }

    let contenido = "";
    for (var i = 0; i < data.length; i++) {
        contenido += "<tr>";
        contenido += "<td style='padding:1px;text-align:center'>" + data[i].id + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].documento + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].razon_social + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].productos + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].fpag_cre + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].peri_cre + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].periodo_pago + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].impo_cre + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].saldo_pendiente + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].esta_cre + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>";

        if (typeof canViewDetail !== 'undefined' && canViewDetail) {
            contenido += ' <button type="button" onclick="abrimodal(' + data[i].id + ')" class="btn btn-warning waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#staticBackdrop" title="Ver Detalle"><i class="fas fa-eye"></i> </button>';
        }

        if (typeof canCreate !== 'undefined' && canCreate) {
            contenido += ' <a href="amortizacion/' + data[i].id + '/edit" type="button" class="btn btn-info waves-effect waves-light" title="Amortizar"><i class="fas fa-check"></i> </a>';
        }

        contenido += "</td>";
        contenido += "</tr>";
    }

    $('#lisatadocredtios').empty().html(contenido);
    initDataTable("#datatable");
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
            initDataTable("#datatabledos");
        },
    );
}
