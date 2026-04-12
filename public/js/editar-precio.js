urlgeeneral=$("#url_raiz_proyecto").val();
$("#actualizar").hide();


window.addEventListener("load", function (event) {

    let sede_temporal=$("#sede_temporal").val();
    let lista_temporal=$("#lista_temporal").val();

    listarprecios(lista_temporal);
    sedes(sede_temporal);
    $(".loader").fadeOut("slow");




 });


 //funcion para listar sedes

 function sedes(sede_temporal){

    $.get(urlgeeneral+"/precios/sedes", function (data) {
       // console.log(data);

        contenido="";
        contenido +='<option value="">--Seleccionar--</option>';
        for (let index = 0; index < data.length; index++) {

          if(sede_temporal==data[index].id)
          {

            contenido += "<option value='" + data[index].id + "' selected>" + data[index].nombre + "</option >";

          }else{

            contenido += "<option value='" + data[index].id + "' >" + data[index].nombre + "</option >";

          }




          }
            document.getElementById("sede_id").innerHTML=contenido;

      });

 }

 function listarprecios(lista_temporal){

    $.get(urlgeeneral+"/lista-precios/listaprecios", function (data) {
      console.log(data);

      contenido="";
      contenido +='<option value="">--Seleccionar--</option>';
      for (let index = 0; index < data.length; index++) {

      if(lista_temporal==data[index].id){

        contenido += "<option value='" + data[index].id + "' selected>" + data[index].descripcion + "</option >";

      }else{

        contenido += "<option value='" + data[index].id + "' >" + data[index].descripcion + "</option >";


      }




        }
          document.getElementById("lista_id").innerHTML=contenido;

    });

}

//METODO PARA CARGAR LOS PRODUCTOS

/*function listadoproductos(){


    $.get(urlgeeneral+"/productos-maestro/listadoproductosmaestro",function(data){


        var contenido = "";

        for (var i = 0; i < data.length; i++) {

            contenido += "<tr>";
            contenido += "<td style='padding:1px;text-align:center;'>" +  data[i].codigo+ "</td>";
            contenido += "<td style='padding:1px;text-align:center' id='nombres"+data[i].id+"'>" + data[i].nomb_pro + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>" + data[i].categoria + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>" + data[i].subcategoria + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>" + data[i].unidad + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>" + data[i].marca + "</td>";

            contenido += "<td style='padding:1px;text-align:center'>";
            contenido +='<a href="#" onclick="seleccionar('+data[i].id+')" type="button" class="btn btn-success "><i class="fas fa-check-square"></i> </a>';



            contenido +="</td>";
            contenido += "</tr>";


        }

            document.getElementById("listaprediosxs").innerHTML = contenido;
            $("#datatable").DataTable();



    });

  }*/



  //FUNCION SELECCIONAR EL PRODUCTO

function seleccionar(id){

    var producto=$("#nombres"+id).text();
    $("#texto_product").val(producto);
    $("#producto_id").val(id);
    $("#staticBackdrop").modal("hide");



}

//METODO PARA GUARDAR LOS DATOS


$("#guardar").on("click",function(){

    if (datosobligatorio() == true) {



        var frm = new FormData();
        var csrf = document.querySelector('meta[name="csrf-token"]').content;
        var id=$("#id").val();
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



                  frm.append("id", id);
                  frm.append("articulo_id", producto_id);
                  frm.append("sucursal_id", sede_id);
                  frm.append("lista_id", lista_id);
                  frm.append("precio_contado", precio_contado);
                  frm.append("descuento_contado", descuento_contado);
                  frm.append("precio_credito", precio_credito);
                  //frm.append("descuento_credito", descuento_credito);
                  //frm.append("prec_compra", prec_compra);
                  frm.append("_token", csrf);

                  $.ajax({
                    type: "POST",
                    url: urlgeeneral+"/precios/modificar",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
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
                                  //limpiarcajasunidas();
                                  location.href =urlgeeneral+"/precios";


                         }
                    });












       /* */




    }


});



//FUNCION PARA VALIDAR LOS DATOS

//METO PARA COMPRBAR SI EL PRODUCTO ESTA EN LA SUCURSAL.


















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


      //FUNCION LIMPIAR CAJAS DE INPUT
      function limpiarcajasunidas() {

        var controles = document.getElementsByClassName("limpiar");
        var ncontroles = controles.length;
        //alert(ncontroles);
        for (var i = 0; i < ncontroles; i++) {
            controles[i].value = "";
        }

    }
