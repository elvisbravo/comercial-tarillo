urlgeeneral=$("#url_raiz_proyecto").val();
$("#actualizar").hide();


window.addEventListener("load", function (event) {

    listarprecios();
    sedes();
    listadoproductos();
   $(".loader").fadeOut("slow");


 });


 //funcion para listar sedes

 function sedes(){

    $.get(urlgeeneral+"/precios/sedes", function (data) {
       // console.log(data);

        contenido="";
        contenido +='<option value="">--Seleccionar--</option>';
        for (let index = 0; index < data.length; index++) {

          contenido += "<option value='" + data[index].id + "' >" + data[index].nombre + "</option >";


          }
            document.getElementById("sede_id").innerHTML=contenido;

      });

 }

 function listarprecios(){

    $.get(urlgeeneral+"/lista-precios/listaprecios", function (data) {
      console.log(data);

      contenido="";
      contenido +='<option value="">--Seleccionar--</option>';
      for (let index = 0; index < data.length; index++) {

        contenido += "<option value='" + data[index].id + "' >" + data[index].descripcion + "</option >";


        }
          document.getElementById("lista_id").innerHTML=contenido;

    });

}

//METODO PARA CARGAR LOS PRODUCTOS

function listadoproductos(){


    $.get(urlgeeneral+"/productos/listarproductos",function(data){


        var contenido = "";

        for (var i = 0; i < data.length; i++) {

            contenido += "<tr>";
            contenido += "<td style='padding:1px;text-align:center;'>" +  data[i].id+ "</td>";
            contenido += "<td style='padding:1px;text-align:center' id='nombres"+data[i].id+"'>" + data[i].nomb_pro + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>" + data[i].categoria + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>" + data[i].subcategoria + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>" + data[i].unidad + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>" + data[i].marca + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>";
            //contenido +='<div class="dropdown">';
            //contenido +=' <button class="btn  dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"> Action</button>';
            //contenido +=' <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">';
            //contenido +=' <li><a class="dropdown-item" href="#">Action</a></li>';
            //contenido +=' <li><a class="dropdown-item" href="#">Action</a></li>';
            //contenido +=' <li><a class="dropdown-item" href="#">Action</a></li>';
            //contenido +=' </ul>';
            //contenido +='</div>';


            contenido +='<a href="#" onclick="seleccionar('+data[i].id+')" type="button" class="btn btn-success "><i class="fas fa-check-square"></i> </a>';



            contenido +="</td>";
            contenido += "</tr>";


        }

            document.getElementById("listaprediosxs").innerHTML = contenido;
            $("#datatable").DataTable();



    });

  }



  //FUNCION SELECCIONAR EL PRODUCTO

function seleccionar(id){

    var producto=$("#nombres"+id).text();
    var prec_compra=$("#prec_compra"+id).text();
    $("#texto_product").val(producto);
    $("#producto_id").val(id);
    $("#staticBackdrop").modal("hide");



}

//METODO PARA GUARDAR LOS DATOS


$("#guardar").on("click",function(){

    if (datosobligatorio() == true) {



        var frm = new FormData();
        var csrf = document.querySelector('meta[name="csrf-token"]').content;
        var producto_id=$("#producto_id").val();
        var selectsede_id= document.getElementById("sede_id"); /*Obtener el SELECT */
        var sede_id = selectsede_id.options[selectsede_id.selectedIndex].value;

        var selectlista_id= document.getElementById("lista_id"); /*Obtener el SELECT */
        var lista_id = selectlista_id.options[selectlista_id.selectedIndex].value;

        var precio_contado=$("#precio_contado").val();
        var descuento_contado=$("#descuento_contado").val();
        var precio_credito=$("#precio_credito").val();
        //var descuento_credito=$("#descuento_credito").val();
        //var prec_compra=$("#prec_compra").val();



        $.get(urlgeeneral+"/precios/validar_producto/"+lista_id+"/"+producto_id+"/"+sede_id,function(data){


              if(data==0){


                  frm.append("articulo_id", producto_id);
                  frm.append("sucursal_id", sede_id);
                  frm.append("lista_id", lista_id);
                  frm.append("precio_contado", precio_contado);
                  frm.append("descuento_contado", descuento_contado);
                  frm.append("precio_credito", precio_credito);
                  //frm.append("descuento_credito", descuento_credito);
                  //frm.append("prec_compra",prec_compra);
                  frm.append("_token", csrf);

                  $.ajax({
                      type: "POST",
                      url: urlgeeneral+"/precios/crear",
                      data: frm,
                      dataType: 'json',
                      contentType: false,
                      processData: false,
                      success: function (data) {


                              Swal.fire({
                                  icon: 'success',
                                  title: 'Oops...',
                                  text: 'Creado Correctamente',
                                  footer: ''
                              })

                                //listadocategorias();
                                  //$('#staticBackdrop').modal('hide');
                                  limpiarcajasunidas();
                                  location.href =urlgeeneral+"/precios";


                         }
                    });





              }else{

                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Lo siento no podemos guardar el producto porque el sistema detecto que ya fue asignado sus precios de venta al contado y al credito para esta sede!',
                    footer: ''
                  })







              }

        });





       /* */




    }


});



//FUNCION PARA VALIDAR LOS DATOS



















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
                text: 'Los Campos marcados con (*) son Obligatorio!',
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


      //FUNCION LIMPIAR CAJAS DE INPUT
      function limpiarcajasunidas() {

        var controles = document.getElementsByClassName("limpiar");
        var ncontroles = controles.length;
        //alert(ncontroles);
        for (var i = 0; i < ncontroles; i++) {
            controles[i].value = "";
        }

    }
