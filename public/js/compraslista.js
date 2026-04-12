urlgeeneral=$("#url_raiz_proyecto").val();

window.addEventListener("load", function (event) {

    listadocompras();
   $(".loader").fadeOut("slow");


 });

 //FUNCION LISTADO

 function listadocompras(){

        $.get(urlgeeneral+"/compras/listacompras",function(data){

            llenardata(data);
        });
 }

 function llenardata(data){

  const estados={

        0:'<div class="badge badge-soft-danger font-size-12">ANULADO</div>',
        1: '<div class="badge badge-soft-success font-size-12">ACTIVA</div>'
  }

    let contenido="";
    for (var i = 0; i < data.length; i++) {

      
      /*var botones = "<div class='dropdown'>" +
      "<button class='btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle' type='button' data-bs-toggle='dropdown' aria-expanded='false'>" +
      "<i class='bx bx-list-ul'></i>" +
      "</button>" +
      "<ul class='dropdown-menu dropdown-menu-end' style=''>" +
      '<li><a class="dropdown-item"  href="#" onclick="abrimodal('+ data[i].id +')" class="btn btn-warning " data-bs-toggle="modal" data-bs-target="#staticBackdrop">Ver</a></li>' +
      "<li><a class='dropdown-item' href='/compras/generar/" +  data[i].id+ "' target='_blank'>PDF</a></li>" +
      '<li><a class="dropdown-item"  href="#" onclick="eliminarsector('+ data[i].id +')" >Anular</a></li>' +
      //"<li><a class='dropdown-item' href='#' id='descargar-" +  datos[i].id + "'>Descargar XML y PDF</a></li>" +
      "</ul>" +
      "</div>";*/

      const estdo_guia = estados[data[i].estado] || 'ESTADO DESCONOCIDO';


        contenido += "<tr>";
        contenido += "<td style='padding:1px;text-align:center'>" +  parseInt(i+1,10) + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].nombre_comercial + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].serie_comprobante +"-"+ data[i].correlativo_comprobante+ "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].total_compra + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].descripcion + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].fecha_compra + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + estdo_guia + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>";
        contenido +=' <button type="button" onclick="abrimodal('+ data[i].id +')" class="btn btn-warning " data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fas fa-eye"></i> </button>';
        contenido +=' <button type="button" onclick="eliminarcompra('+ data[i].id +')" class="btn btn-danger eliminar"><i class="fas fa-trash-alt"></i> </button>'

        contenido +="</td>";

        contenido += "</tr>";
    }

    document.getElementById("listadocompras").innerHTML = contenido;
    $("#datatable").dataTable();


 }

 function abrimodal(id){

    if(id == "0"){

      limpiarcajasunidas();

      $("#guardar").show();
      $("#actualizar").hide();

    }else{



      $.get(urlgeeneral+"/compras/ver/"+id, function (data) {


        let contenido="";
        for (var i = 0; i < data.length; i++) {
            contenido += "<tr>";
            contenido += "<td style='padding:1px;text-align:center'>" +  parseInt(i+1,10) + "</td>";
            contenido += "<td style='padding:1px;text-align:center'> " + data[i].nomb_pro + "</td>";
            contenido += "<td style='padding:1px;text-align:center'> " + data[i].cantidad + "</td>";
            contenido += "<td style='padding:1px;text-align:center'> " + data[i].descripcion + "</td>";
            contenido += "<td style='padding:1px;text-align:center'> " + data[i].precio + "</td>";
            contenido += "<td style='padding:1px;text-align:center'> " + data[i].subtotal + "</td>";


            contenido += "</tr>";
        }

        document.getElementById("detalle").innerHTML = contenido;
        $("#datatable").dataTable();




      });


    }


  }

  //ANULAR LA COMPRA
  
function eliminarcompra(id){



 const tabla = document.getElementById('datatable');


   tabla.addEventListener('click', (e) => {

 

    if (e.target.classList.contains('eliminar') || e.target.classList.contains('bx')) {
        Swal.fire({
            title: '¿Desea Anular la Compra?',
            text: "No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, Anular!',
            cancelButtonText: 'Cancelar'
          }).then((result) => {
            if (result.isConfirmed) {
            //Metodo para eleminar
            
            var csrf = document.querySelector('meta[name="csrf-token"]').content;

            //alert(id);

              $.ajax({
                type: "POST",
                url: "compras/eliminar/"+id,
                data: {"_method": "delete",'_token': csrf},

                success: function (data) {

                  listadocompras();

                  if(data.respuesta==="error"){
                    
                      Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.mensaje,
                        footer: ''
                      })

                  }else if(data.respuesta==="ok"){

                      Swal.fire({
                        icon: 'success',
                        title: 'Oops...',
                        text: data.mensaje,
                        footer: ''
                    })


                  }
                 

                     console.log(data);


                }

            });





            }
          })
    }

});



}
