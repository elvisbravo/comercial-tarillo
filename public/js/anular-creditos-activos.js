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
        contenido += "<td style='padding:1px;text-align:center' id='nombre"+data[i].id+"'>" + data[i].nomb_per + "</td>";
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


     console.log("hola "+ $("#id_persona_tempe").val(id));

     $(".bs-example-modal-xl").modal('hide');
     let codigo=1;

     $.get(urlgeeneral+"/creditos-pendientes/creditos/"+id+'/'+codigo,function(data){

         if(data.length>0){

            let contenido="";
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
                contenido += "<td style='padding:1px;text-align:center'> <span class='badge bg-success'>Activo </span></td>";


                contenido += "<td style='padding:1px;text-align:center'>";
                //contenido +='<i class="fas fa-edit"></i>';
                contenido +='<a href="#" onclick="abrimodal('+ data[i].id +')" type="button" class="btn btn-danger " ><i class="fas fa-trash-alt"></i></a>';
                contenido +="</td>";
                contenido += "</tr>";


              }

              document.getElementById("lisatadocredtios").innerHTML = contenido;
              $("#datatable").dataTable();


         }else{

            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Lo siento no hemos encontrado Ningun credito asociado para este cliente!',
                footer: ''
              })


         }




     });




}

function abrimodal(id){

    $(".bs-example-modal-xl-y").modal("show");
     $("#codigo_credito").val(id);

   /* $.get(urlgeeneral+"/creditos-pendientes/cuotas/"+id,function(data){

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
}


//METODO PARA ANULAR CREDITO

$("#guardar").on("click",function(){

    var texto = document.getElementById('observacion').value;
    let codigo=$("#codigo_credito").val();

    var frm = new FormData();
    var csrf = document.querySelector('meta[name="csrf-token"]').content;


    if(texto.length==0){

        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'El campo Observación es Obligatorio!',
            footer: ''
          })

    }else{


        $.get(urlgeeneral+"/anular-credito/verificardorcuota/"+codigo,function(data){

             //console.log(data);
             if(data==1){

                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Lo siento no podemos Anular el credito porque hemos Detectado que ya tiene cuotas Amortizadas!',
                    footer: 'Para Anular el credito primero anule las Amortizaciones realizadas para este credito.'
                  })


             }else{

                frm.append("_token", csrf);
                frm.append("obse_cre", texto);
                frm.append("id", codigo);

                $.ajax({
                    type: "POST",
                    url: urlgeeneral+"/anular-credito/anular",
                    data: frm,
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    success : function (result) {

                        console.log(result);
                        document.getElementById('guardar').disabled=true;

                        $(".bs-example-modal-xl-y").modal("hide");

                        Swal.fire({
                            icon: 'success',
                            title: 'Ok...',
                            text: 'Anulado Correctamente',
                            footer: ''
                        })

                        //location.href =urlgeeneral+"/compras";


                   },



                    error : function(xhr,errmsg,err) {

                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Ocurrio un error al intentar anlar el credito!',
                            footer: 'Por favor recargue la página para poder empezar de nuevo'
                          })

                                    console.log(xhr.status + ": " + xhr.responseText);
                                    }

            });






             }

        });


    }

});

//METODO PARA IMPRMIR LOS ESTADO GENERALES DEL CREDITO
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
                icon: 'error',
                title: 'Oops...',
                text: 'El campo Color es Obligatorio!',
                footer: ''
              })
            //alert("Campos Obligatorios");
            //swal("Error!", "Los Campos Marcados de Rojo son requeridos!", "error")
            //alert("Los datos son Obliatorios");


        } else {
            obligarotio[i].parentNode.classList.remove("error")
        }
    }
    return bien;

    }
