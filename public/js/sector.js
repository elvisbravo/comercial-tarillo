
  urlgeeneral=$("#url_raiz_proyecto").val();
  $("#actualizar").hide();

  //FUNCION LOAD
window.addEventListener("load", function (event) {

     listadomarca();
    $(".loader").fadeOut("slow");


  });


  function listadomarca(){

    $.get(urlgeeneral+"/sectores/listado", function (data) {
      //console.log(data);

        llenarmarca(data);

    });

}

function llenarmarca(data){

  let contenido="";
  for (var i = 0; i < data.length; i++) {
    contenido += "<tr>";
    contenido += "<td style='padding:1px;text-align:center'>" +  parseInt(i+1,10) + "</td>";
    contenido += "<td style='padding:1px;text-align:center'> " + data[i].nomb_sec + "</td>";
    contenido += "<td style='padding:1px;text-align:center'>";
    //contenido +='<i class="fas fa-edit"></i>';
    contenido +=' <button type="button" onclick="abrimodal('+ data[i].id +')" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fas fa-edit"></i></button>'
    contenido +='<button type="button" onclick="eliminarsector('+ data[i].id +')" class="btn btn-danger eliminar"><i class="fas fa-trash-alt"></i></button>'
    contenido +="</td>";
    contenido += "</tr>";


  }

  document.getElementById("listadecolores").innerHTML = contenido;
  $("#datatable").dataTable();


}



  $("#guardar").on("click",function(){

    if (datosobligatorio() == true) {

        var frm = new FormData();
        var csrf = document.querySelector('meta[name="csrf-token"]').content;
        var nomb_sec=$("#nomb_sec").val();


        frm.append("nomb_sec", nomb_sec);
        frm.append("_token", csrf);


        $.ajax({
            type: "POST",
            url: urlgeeneral+"/sectores/crear",
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

                    listadomarca();
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

    $.get(urlgeeneral+"/sectores/edit/" + id, function (data) {
      //console.log(data["Codigo"]);
      $("#valor").val(id);
      document.getElementById("nomb_sec").value = data["nomb_sec"];


  });
  }


}

//METODO PARA MODIFICAR EL color:

$("#actualizar").on("click",function(){

  if (datosobligatorio() == true) {

    var frm = new FormData();
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var nomb_sec=$("#nomb_sec").val();
    var id=$("#valor").val();

            frm.append("id", id);
            frm.append("nomb_sec", nomb_sec);
            frm.append("_token", csrf);

            $.ajax({
              type: "POST",
              url: urlgeeneral+"/sectores/modificar",
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
                });

                $('#staticBackdrop').modal('hide');
                listadomarca();

                //console.log(data);

                }
            });




  }

});



//metodo para anular el color:


function eliminarsector(id){

  const tabla = document.getElementById('datatable');

  tabla.addEventListener('click', (e) => {
    if (e.target.classList.contains('eliminar') || e.target.classList.contains('bx')) {
        Swal.fire({
            title: '¿Desea eliminar el color?',
            text: "No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, eliminar!',
            cancelButtonText: 'Cancelar'
          }).then((result) => {
            if (result.isConfirmed) {
            //Metodo para eleminar
            var csrf = document.querySelector('meta[name="csrf-token"]').content;
              $.ajax({
                type: "POST",
                url: "sectores/eliminar/"+id,
                data: {"_method": "delete",'_token': csrf},

                success: function (data) {
                    listadomarca();

                  Swal.fire(
                    'Eliminado!',
                    'El color ha sido eliminado.',
                    'success'
                  )


                }

            });





            }
          })
    }
})

}


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
              text: 'El campo Sector es Obligatorio!',
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
