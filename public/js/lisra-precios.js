urlgeeneral=$("#url_raiz_proyecto").val();
$("#actualizar").hide();

window.addEventListener("load", function (event) {

    listarprecios();
   $(".loader").fadeOut("slow");


 });

 function listarprecios(){

    $.get(urlgeeneral+"/lista-precios/listaprecios", function (data) {
      //console.log(data);

        llenarmarca(data);

    });

}


function llenarmarca(data){

    let contenido="";
    for (var i = 0; i < data.length; i++) {
      contenido += "<tr>";
      contenido += "<td style='padding:1px;text-align:center'>" +  parseInt(i+1,10) + "</td>";
      contenido += "<td style='padding:1px;text-align:center'> " + data[i].descripcion + "</td>";
      contenido += "<td style='padding:1px;text-align:center'> " + data[i].vigencia + "</td>";
         if(data[i].fecha_inicial==null){

            contenido += "<td style='padding:1px;text-align:center'> </td>";

         }else{
            contenido += "<td style='padding:1px;text-align:center'> " + data[i].fecha_inicial + "</td>";

         }

         if(data[i].fecha_final==null){
            contenido += "<td style='padding:1px;text-align:center'> </td>";

         }else{

            contenido += "<td style='padding:1px;text-align:center'> " + data[i].fecha_final + "</td>";

         }


      contenido += "<td style='padding:1px;text-align:center'>";
      //contenido +='<i class="fas fa-edit"></i>';
      contenido +=' <button type="button" onclick="abrimodal('+ data[i].id +')" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fas fa-edit"></i> Editar</button>'
      contenido +='<button type="button" onclick="eliminarsector('+ data[i].id +')" class="btn btn-danger eliminar"><i class="fas fa-trash-alt"></i> Eliminar</button>'
      contenido +="</td>";
      contenido += "</tr>";


    }

    document.getElementById("listadecolores").innerHTML = contenido;
    $("#datatable").dataTable();


  }




 //METODO PARA ACTIVAR Y DESACTIVAR LA CAJITA DE TEXTO
 $("#vigencia").on("change",function(){

    if ($(this).is(':checked')) {

        $("#vigencia").val("SI");

    } else {

        $("#vigencia").val("NO");
    }


});









//METODO PARA GUARDAR DATOS
 $("#guardar").on("click",function(){

    if (datosobligatorio() == true) {

        var frm = new FormData();
        var csrf = document.querySelector('meta[name="csrf-token"]').content;
        var descripcion=$("#descripcion").val();
        var vigencia=$("#vigencia").val();
        var fecha_inicial=$("#fecha_inicial").val();
        var fecha_final=$("#fecha_final").val();





        frm.append("descripcion", descripcion);
        frm.append("vigencia", vigencia);
        frm.append("fecha_inicial", fecha_inicial);
        frm.append("fecha_final", fecha_final);
        frm.append("_token", csrf);


        $.ajax({
            type: "POST",
            url: urlgeeneral+"/lista-precios/crear",
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

                      listarprecios();
                    $('#staticBackdrop').modal('hide');


               }
          });




    }


});


function abrimodal(id){

    if(id == "0"){

      limpiarcajasunidas();

      $("#guardar").show();
      $("#actualizar").hide();

    }else{

      $("#guardar").hide();
      $("#actualizar").show();

      $.get(urlgeeneral+"/lista-precios/editarlista/" + id, function (data) {
        //console.log(data["Codigo"]);
        $("#valor").val(id);
        document.getElementById("descripcion").value = data["descripcion"];
        document.getElementById("fecha_inicial").value=data["fecha_inicial"];
        document.getElementById("fecha_final").value=data["fecha_final"];
        document.getElementById("vigencia").value=data["vigencia"];

    });
    }


  }


  //METODO PARA MODIFICAR EL color:

$("#actualizar").on("click",function(){

    if (datosobligatorio() == true) {

      var frm = new FormData();
      var csrf = document.querySelector('meta[name="csrf-token"]').content;
      var id=$("#valor").val();
      var descripcion=$("#descripcion").val();
      var vigencia=$("#vigencia").val();
      var fecha_inicial=$("#fecha_inicial").val();
      var fecha_final=$("#fecha_final").val();

      frm.append("id", id);
      frm.append("descripcion", descripcion);
      frm.append("vigencia", vigencia);
      frm.append("fecha_inicial", fecha_inicial);
      frm.append("fecha_final", fecha_final);






              $.ajax({
                type: "POST",
                url: urlgeeneral+"/lista-precios/modificar",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: frm,
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function (data) {


                  Swal.fire({
                    icon: 'success',
                    title: 'Oops...',
                    text: 'Modificado Correctamente',
                    footer: ''
                  })
                  $('#staticBackdrop').modal('hide');
                  listarprecios();

                  //console.log(data);

                  }
              });




    }

  });



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
