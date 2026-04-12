urlgeeneral=$("#url_raiz_proyecto").val();
window.addEventListener("load", function (event) {
    $(".loader").fadeOut("slow");
    //fechaactual();
   // listar();
});

//METODO PARA CARGAR LOS DATOS DE LOS CLIENTES

$.get(urlgeeneral+"/creditos-pendientes/listadoclientes",function(data){

    var contenido = "";
    for (var i = 0; i < data.length; i++) {
        contenido += "<tr>";

        contenido += "<td style='padding:1px;text-align:center' id='documento"+data[i].id+"'>" + data[i].documento + "</td>";
        contenido += "<td style='padding:1px;text-align:center' id='nombre"+data[i].id+"'>" + data[i].razon_social + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>" + data[i].dire_per + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>";
        contenido +='<a href="#" onclick="seleccionar(\''+data[i].id+'\')" type="button" class="btn btn-success"><i class="fas fa-check"></i> </a>';

        contenido +="</td>";

        contenido += "</tr>";
    }

    document.getElementById("listaclientes").innerHTML = contenido;
    $("#datatables").DataTable();


});

//METODO PARA ASIGAR LOS DATOS
function seleccionar(id){

     $("#documento").val($("#documento"+id).text());
     $("#nombresdata").val($("#nombre"+id).text());
     $("#id_persona_tempe").val(id);


     $(".bs-example-modal-xl").modal('hide');





}

//METODO PARA BUSCAR LOS DATOS

$("#buscar").on("click",function(){

    let id=$("#id_persona_tempe").val();
    let fechauno=$("#fechauno").val();
    let fechados=$("#fechados").val();



    $.get(urlgeeneral+"/listamortizaciones/"+id+"/"+fechauno+"/"+fechados,function(data){

        //console.log(data[0].amortizaciones);

     let contenido="";
     for (var i = 0; i < data.length; i++) {
         contenido += "<tr>";
         contenido += "<td style='padding:1px;text-align:center'>" +  data[i].id  + "</td>";
         contenido += "<td style='padding:1px;text-align:center'> " + data[i].num_recibo + "</td>";
         contenido += "<td style='padding:1px;text-align:center'> " + data[i].mont_rec + "</td>";
         contenido += "<td style='padding:1px;text-align:center'> " + data[i].fech_rec + "</td>";
         contenido += "<td style='padding:1px;text-align:center'> " + data[i].esta_rec + "</td>";
         contenido += "<td style='padding:1px;text-align:center'> " + data[i].usuario + "</td>";
         contenido += "<td style='padding:1px;text-align:center'>";
         //contenido +='<i class="fas fa-edit"></i>';
         contenido +='<a href="'+urlgeeneral+'/consulta-amortizaciones/'+data[i].id+'" target="_blank" type="button" class="btn btn-info " ><i class="fas fa-check"></i> </a>';
         contenido +='<a href="#" onclick="imprimir(\''+data[i].id+'\')" type="button" class="btn btn-warning"><i class="fas fa-cloud-download-alt"></i> </a>';
         contenido +="</td>";
         contenido += "</tr>";
         //datosgeneral(data[i].amortizaciones);


       }

       document.getElementById("lisatadocredtios").innerHTML = contenido;
       $("#datatable").dataTable();



  });






});


//IMPRIMIR EL RECIBO
function imprimir(id){


    window.open(urlgeeneral+"/consulta-amortizaciones/recibo/"+id, '_blank');

}



/*function abrimodal(id){

    $(".bs-example-modal-xl-y").modal("show");



    console.log(datos);






    /*$.get(urlgeeneral+"/creditos-pendientes/cuotas/"+id,function(data){

        $("#documentos").val(data[0].documento);
        $("#cliented").val(data[0].razon_social);
        $("#impo_cred").val(data[0].impo_cre);
        $("#periodo_pago").val(data[0].periodo_pago);
        console.log(data[0].documento);

        let contenido="";
        for (var i = 0; i < data.length; i++) {

            contenido += "<tr>";
            contenido += "<td style='padding:1px;text-align:center'>" +  data[i].credito_id  + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>" +  data[i].numero_cuo  + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>" +  data[i].mont_cuo  + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>0.00</td>";
            contenido += "<td style='padding:1px;text-align:center'>" +  data[i].saldo_cuo  + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>" +  data[i].fven_cuo  + "</td>";
             if( data[i].esta_cuo=='COBRADA')
            contenido += "<td style='padding:1px;text-align:center'> <span class='badge bg-success'>" +  data[i].esta_cuo  + " </span></td>";
            else{
                contenido += "<td style='padding:1px;text-align:center'> <span class='badge bg-danger'>" +  data[i].esta_cuo  + " </span></td>";

            }



            contenido += "</tr>";


        }

        document.getElementById("listaprediosxs").innerHTML = contenido;
          $("#datatableg").dataTable();



    });*/
/*}*/



//METODO PARA IMPRMIR LOS ESTADO GENERALES DEL CREDITO
$("#estado").on("click",function(){

      let codigo=$("#id_persona_tempe").val();

      alert(codigo);
})
