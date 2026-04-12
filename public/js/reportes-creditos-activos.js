urlgeeneral=$("#url_raiz_proyecto").val();
window.addEventListener("load", function (event) {
    $(".loader").fadeOut("slow");
    document.getElementById("estado").disabled=true;

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
        contenido +='<a href="#" onclick="seleccionar(\''+data[i].id+'\')" type="button" class="btn btn-success"><i class="fas fa-check"></i> </a>'
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
     $("#id_persona_tempe").val(id)


     console.log("hola "+ id);

     $(".bs-example-modal-xl").modal('hide');

    




}

//METODO PARA BUSCAR LA DATA
$("#buscardata").on('click',function(){

    let codigo=$("#id_persona_tempe").val();
    let selectestado_id=document.getElementById('estado_id');
    var estado_id=selectestado_id.options[selectestado_id.selectedIndex].value;

    //alert(estado_id);

    $.get(urlgeeneral+"/creditos-pendientes/creditos/"+codigo+'/'+estado_id,function(data){

        if(data.length>0){

            document.getElementById("estado").disabled=false;
            let contenido="";
            document.getElementById("lisatadocredtios").innerHTML = contenido;
            for (var i = 0; i < data.length; i++) {
                contenido += "<tr>";
                contenido += "<td style='padding:1px;text-align:center'>" +  data[i].id  + "</td>";
                contenido += "<td style='padding:1px;text-align:center'> " + data[i].documento + "</td>";
                contenido += "<td style='padding:1px;text-align:center'> " + data[i].razon_social + "</td>";
                contenido += "<td style='padding:1px;text-align:center'> " + data[i].fpag_cre + "</td>";
                contenido += "<td style='padding:1px;text-align:center'> " + data[i].peri_cre + "</td>";
                contenido += "<td style='padding:1px;text-align:center'> " + data[i].periodo_pago + "</td>";
                contenido += "<td style='padding:1px;text-align:center'> " + data[i].impo_cre + "</td>";


                contenido += "<td style='padding:1px;text-align:center'> " + data[i].saldo + "</td>";

                if(data[i].esta_cre==1){
                    contenido += "<td style='padding:1px;text-align:center'> <span class='badge  bg-success'>Activo </span></td>";

                }else if(data[i].esta_cre==2){
                    contenido += "<td style='padding:1px;text-align:center'> <span class='badge  bg-info'>PAGADO </span></td>";

                }else{
                    contenido += "<td style='padding:1px;text-align:center'> <span class='badge bg-warning'>ANULADO </span></td>";

                }
                


                contenido += "<td style='padding:1px;text-align:center'>";
                //contenido +='<i class="fas fa-edit"></i>';
                contenido +='<a href="#" onclick="abrimodal('+ data[i].id +')" type="button" class="btn btn-info " ><i class="fas fa-check"></i> </a>';
                contenido +="</td>";
                contenido += "</tr>";


              }

              document.getElementById("lisatadocredtios").innerHTML = contenido;
              $("#datatable").dataTable();


        }else{

            document.getElementById("estado").disabled=true;
            document.getElementById("lisatadocredtios").innerHTML = '';

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




    $.get(urlgeeneral+"/creditos-pendientes/cuotas/"+id,function(data){

        $("#documentos").val(data[0].documento);
        $("#cliented").val(data[0].razon_social);
        $("#impo_cred").val(data[0].impo_cre);
        $("#periodo_pago").val(data[0].periodo_pago);
        $("#id_credito").val(data[0].credito_id );
        console.log(data[0].documento);



        let contenido="";
        document.getElementById("listaprediosxs").innerHTML = contenido;
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
