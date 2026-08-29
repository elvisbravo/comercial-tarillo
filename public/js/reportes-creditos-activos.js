urlgeeneral=$("#url_raiz_proyecto").val();
window.addEventListener("load", function (event) {
    $(".loader").fadeOut("slow");
    document.getElementById("estado").disabled=true;

    //fechaactual();
   // listar();
});

//METODO PARA BUSCAR CLIENTES POR NOMBRE O DNI (select2 con ajax)
$('#cliente_select').select2({
    placeholder: 'Buscar cliente por nombre o DNI...',
    minimumInputLength: 2,
    ajax: {
        url: urlgeeneral + '/creditos-pendientes/buscar-clientes',
        dataType: 'json',
        delay: 300,
        data: function (params) {
            return { q: params.term };
        },
        processResults: function (data) {
            return {
                results: data.map(function (c) {
                    return { id: c.id, text: c.razon_social + ' - ' + c.documento, documento: c.documento };
                })
            };
        },
        cache: true
    },
    language: {
        inputTooShort: function () { return 'Escribe al menos 2 caracteres'; },
        searching: function () { return 'Buscando...'; },
        noResults: function () { return 'No se encontraron clientes'; }
    }
});

$('#cliente_select').on('select2:select', function (e) {
    var data = e.params.data;
    $("#id_persona_tempe").val(data.id);
});

//METODO PARA BUSCAR LA DATA
$("#buscardata").on('click',function(){

    let codigo=$("#id_persona_tempe").val();
    let selectestado_id=document.getElementById('estado_id');
    var estado_id=selectestado_id.options[selectestado_id.selectedIndex].value;

    //alert(estado_id);

    $.get(urlgeeneral + "/creditos-pendientes/creditos/" + codigo + '/' + estado_id, function (data) {
        if ($.fn.DataTable.isDataTable('#datatable')) {
            $('#datatable').DataTable().destroy();
        }

        if (data.length > 0) {
            if (typeof canPrint !== 'undefined' && canPrint) {
                document.getElementById("estado").disabled = false;
            }
            let contenido = "";
            for (var i = 0; i < data.length; i++) {
                contenido += "<tr>";
                contenido += "<td style='padding:1px;text-align:center'>" + data[i].id + "</td>";
                contenido += "<td style='padding:1px;text-align:center'> " + data[i].documento + "</td>";
                contenido += "<td style='padding:1px;text-align:center'> " + data[i].razon_social + "</td>";
                contenido += "<td style='padding:1px;text-align:center'> " + data[i].fpag_cre + "</td>";
                contenido += "<td style='padding:1px;text-align:center'> " + data[i].peri_cre + "</td>";
                contenido += "<td style='padding:1px;text-align:center'> " + data[i].periodo_pago + "</td>";
                contenido += "<td style='padding:1px;text-align:center'> " + data[i].impo_cre + "</td>";
                contenido += "<td style='padding:1px;text-align:center'> " + data[i].saldo + "</td>";

                if (data[i].esta_cre == 1) {
                    contenido += "<td style='padding:1px;text-align:center'> <span class='badge  bg-success'>Activo </span></td>";
                } else if (data[i].esta_cre == 2) {
                    contenido += "<td style='padding:1px;text-align:center'> <span class='badge  bg-info'>PAGADO </span></td>";
                } else {
                    contenido += "<td style='padding:1px;text-align:center'> <span class='badge bg-warning'>ANULADO </span></td>";
                }

                contenido += "<td style='padding:1px;text-align:center'>";
                contenido += '<a href="#" onclick="abrimodal(' + data[i].id + ')" type="button" class="btn btn-info waves-effect waves-light" title="Ver Detalle"><i class="fas fa-check"></i> </a>';
                contenido += "</td>";
                contenido += "</tr>";
            }

            $('#lisatadocredtios').empty().html(contenido);
            initDataTable("#datatable");
        } else {
            document.getElementById("estado").disabled = true;
            $('#lisatadocredtios').empty();

            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Lo Siento no se encontraron datos para el cliente seleccionado!',
                footer: ''
              })


        }




     });



});

function abrimodal(id){

    $(".bs-example-modal-xl-y").modal("show");




    $.get(urlgeeneral + "/creditos-pendientes/cuotas/" + id, function (data) {
        if ($.fn.DataTable.isDataTable('#datatableg')) {
            $('#datatableg').DataTable().destroy();
        }

        $("#documentos").val(data[0].documento);
        $("#cliented").val(data[0].razon_social);
        $("#impo_cred").val(data[0].impo_cre);
        $("#periodo_pago").val(data[0].periodo_pago);
        $("#id_credito").val(data[0].credito_id);

        let contenido = "";
        for (var i = 0; i < data.length; i++) {
            contenido += "<tr>";
            contenido += "<td style='padding:1px;text-align:center'>" + data[i].credito_id + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>" + data[i].numero_cuo + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>" + data[i].mont_cuo + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>0.00</td>";
            contenido += "<td style='padding:1px;text-align:center'>" + data[i].saldo_cuo + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>" + data[i].fven_cuo + "</td>";
            if (data[i].esta_cuo == 'COBRADA')
                contenido += "<td style='padding:1px;text-align:center'> <span class='badge bg-success'>" + data[i].esta_cuo + " </span></td>";
            else {
                contenido += "<td style='padding:1px;text-align:center'> <span class='badge bg-danger'>" + data[i].esta_cuo + " </span></td>";
            }

            if (!data[i].condicion) {
                contenido += "<td style='padding:1px;text-align:center'></td>";
            } else if (data[i].condicion == 'PUNTUAL') {
                contenido += "<td style='padding:1px;text-align:center'> <span class='badge bg-success'>" + data[i].condicion + "</span></td>";
            } else if (data[i].condicion == 'ATRASADO' || data[i].condicion == 'VENCIDA') {
                contenido += "<td style='padding:1px;text-align:center'> <span class='badge bg-danger'>" + data[i].condicion + "</span></td>";
            } else {
                contenido += "<td style='padding:1px;text-align:center'> <span class='badge bg-secondary'>" + data[i].condicion + "</span></td>";
            }

            contenido += "</tr>";
        }

        $('#listaprediosxs').empty().html(contenido);
        initDataTable("#datatableg");
    });

    cargarHistorialPagos(id);
    cargarAmortizaciones(id);
}

//METODO PARA CARGAR EL HISTORIAL DE PAGOS DE UN CREDITO EN LA PESTAÑA "PAGOS REALIZADOS" (incluye pagados y anulados)
function cargarHistorialPagos(id) {

    $.get(urlgeeneral + "/creditos-pendientes/historial/" + id, function (data) {
        if ($.fn.DataTable.isDataTable('#datatablehistorial')) {
            $('#datatablehistorial').DataTable().destroy();
        }

        var credito = data.credito;

        $("#historial_cuotas").val(credito.total_cuotas);

        var estadoHtml = "";
        if (credito.esta_cre == 1) {
            estadoHtml = "<span class='badge bg-success'>Activo</span>";
        } else if (credito.esta_cre == 2) {
            estadoHtml = "<span class='badge bg-info'>PAGADO</span>";
        } else {
            estadoHtml = "<span class='badge bg-warning'>ANULADO</span>";
            if (credito.f_anulacion) {
                estadoHtml += " <small>(anulado el " + credito.f_anulacion + ")</small>";
            }
        }
        $("#historial_estado").html(estadoHtml);

        var contenido = "";
        var pagos = data.pagos;
        for (var i = 0; i < pagos.length; i++) {
            contenido += "<tr>";
            contenido += "<td data-order='" + pagos[i].fecha_orden + "' style='padding:1px;text-align:center'>" + pagos[i].fecha_pago + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>" + pagos[i].numero_cuo + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>" + pagos[i].mont_amo + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>" + pagos[i].tipo_amo + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>" + (pagos[i].forma_pago || '-') + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>" + (pagos[i].num_recibo || '-') + "</td>";
            if (pagos[i].esta_rec == 'ANULADO') {
                contenido += "<td style='padding:1px;text-align:center'> <span class='badge bg-danger'>ANULADO</span></td>";
            } else {
                contenido += "<td style='padding:1px;text-align:center'> <span class='badge bg-success'>EMITIDO</span></td>";
            }
            contenido += "</tr>";
        }

        $('#listahistorialpagos').empty().html(contenido);
        initDataTable("#datatablehistorial", { order: [[0, 'asc'], [1, 'asc']] });
    });
}

//METODO PARA CARGAR LAS AMORTIZACIONES (RESUMEN POR RECIBO) DEL CREDITO EN LA PESTAÑA "AMORTIZACIONES"
function cargarAmortizaciones(id) {

    $.get(urlgeeneral + "/creditos-pendientes/amortizaciones/" + id, function (data) {
        if ($.fn.DataTable.isDataTable('#datatableamortizaciones')) {
            $('#datatableamortizaciones').DataTable().destroy();
        }

        var contenido = "";
        for (var i = 0; i < data.length; i++) {
            contenido += "<tr>";
            contenido += "<td data-order='" + data[i].fecha_orden + "' style='padding:1px;text-align:center'>" + data[i].fecha_amo + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>" + data[i].monto + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>" + (data[i].forma_pago || '-') + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>" + (data[i].num_recibo || '-') + "</td>";
            if (data[i].esta_rec == 'ANULADO') {
                contenido += "<td style='padding:1px;text-align:center'> <span class='badge bg-danger'>ANULADO</span></td>";
            } else {
                contenido += "<td style='padding:1px;text-align:center'> <span class='badge bg-success'>EMITIDO</span></td>";
            }
            contenido += "</tr>";
        }

        $('#listaamortizaciones').empty().html(contenido);
        initDataTable("#datatableamortizaciones", { order: [[0, 'asc']] });
    });
}


//METODO PARA IMPRMIR LOS ESTADO GENERALES DEL CREDITO
$("#estado").on("click",function(){


      let codigo=$("#id_persona_tempe").val();
      let selectestado_id=document.getElementById('estado_id');
      var estado_id=selectestado_id.options[selectestado_id.selectedIndex].value;
      
      window.open(urlgeeneral+"/creditos-pendientes/estado_cuenta/"+codigo+'/'+estado_id, '_blank');

})

//METODO APRA IMPRIMIR EL CONTRATO

$("#imprimir_contrato").on("click",function(){

    let codigo=  $("#id_credito").val();

    window.open(urlgeeneral+"/creditos/contrato/"+codigo, '_blank');

})

//metodo para imprimir las cuotas

$("#imprimir_cuotas").on("click",function(){

    let codigo=  $("#id_credito").val();

    window.open(urlgeeneral+"/creditos-pendientes/cuota/"+codigo, '_blank');

});
