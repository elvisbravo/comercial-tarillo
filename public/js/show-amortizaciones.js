urlgeeneral=$("#url_raiz_proyecto").val();
window.addEventListener("load", function (event) {
    $(".loader").fadeOut("slow");
    //fechaactual();
   // listar();
});

//funcion para traer los datos del credito

function creditos(id){

    $(".bs-example-modal-lg").modal("show");

    $.get(urlgeeneral+"/creditos-pendientes/cuotas/"+id,function(data){

        console.log(data);

        $("#id_cre").text(data[0].credito_id);
        $("#monto_cre").text(data[0].impo_cre);
        $("#numcuotas").text(data[0].numero_cuo);
        $("#importe").text(data[0].impo_cre);
        $("#fecha").text(data[0].fpag_cre);

        $("#documento").text(data[0].documento);
        $("#cliente").text(data[0].razon_social);
        $("#ruc").text(data[0].documento);
        $("#id_credito").val(id);


        let contenido="";
        for (var i = 0; i < data.length; i++) {

            contenido += "<tr>";
            contenido += "<td style='padding:1px;text-align:center'>" +  data[i].numero_cuo  + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>" +  data[i].mont_cuo  + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>0.00</td>";
            contenido += "<td style='padding:1px;text-align:center'>" +  data[i].mont_cuo  + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>" +  data[i].fven_cuo  + "</td>";
            if( data[i].esta_cuo=='COBRADA')
            contenido += "<td style='padding:1px;text-align:center'> <span class='badge bg-success'>" +  data[i].esta_cuo  + " </span></td>";
            else{
                contenido += "<td style='padding:1px;text-align:center'> <span class='badge bg-danger'>" +  data[i].esta_cuo  + " </span></td>";

            }
            contenido += "<td style='padding:1px;text-align:center'>" +  data[i].saldo_cuo  + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>" +  data[i].saldo_cuo  + "</td>";

            contenido += "<td style='padding:1px;text-align:center'>" +  data[i].saldo_cuo  + "</td>";





            contenido += "</tr>";


        }

        document.getElementById("cuota").innerHTML = contenido;
          $("#datatableg").dataTable();



    });


}


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
