
  urlgeeneral=$("#url_raiz_proyecto").val();
  $("#actualizar").hide();

  //FUNCION LOAD
window.addEventListener("load", function (event) {

    listadocategorias();
    $(".loader").fadeOut("slow");


  });


  function listadocategorias(){

    $.get(urlgeeneral+"/categorias/listado", function (data) {
      //console.log(data);

        llenacategorias(data);

    });

}

function llenacategorias(data){

  let contenido="";
  for (var i = 0; i < data.length; i++) {
    contenido += "<tr>";
    contenido += "<td style='padding:1px;text-align:center'>" +  parseInt(i+1,10) + "</td>";
    contenido += "<td style='padding:1px;text-align:center'> " + data[i].categoria + "</td>";
    if(data[i].estado==0){
        contenido += "<td style='padding:1px;text-align:center'> <i class='fas fa-sync'></i> Inactivo</td>";

      }else{

        contenido += "<td style='padding:1px;text-align:center'> Activo</td>";

      }
    contenido += "<td style='padding:1px;text-align:center'>";
    
    if (typeof canEdit !== 'undefined' && canEdit) {
      contenido +=' <button type="button" onclick="abrimodal('+ data[i].id +')" class="btn btn-info " data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fas fa-edit"></i></button>';
    }

    if (typeof canDelete !== 'undefined' && canDelete) {
        if(data[i].estado==0){
            contenido +=' <button type="button" onclick="activar('+ data[i].id +')" class="btn btn-warning activar"><i class="fas fa-sync activar"></i> </button>';
        }else{
            contenido +=' <button type="button" onclick="eliminarsector('+ data[i].id +')" class="btn btn-danger eliminar"><i class="fas fa-trash-alt eliminar"></i> </button>';
        }
    }

    contenido +="</td>";
    contenido += "</tr>";


  }

  document.getElementById("listadocategorias").innerHTML = contenido;
  initDataTable("#datatable");


}



  $("#guardar").on("click",function(){

    if (datosobligatorio() == true) {

        var frm = new FormData();
        var csrf = document.querySelector('meta[name="csrf-token"]').content;
        var categoria=$("#categoria").val();


        frm.append("categoria", categoria);
        frm.append("_token", csrf);


        $.ajax({
            type: "POST",
            url: urlgeeneral+"/categorias/crear",
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

                      listadocategorias();
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

    $.get(urlgeeneral+"/categorias/editar/" + id, function (data) {
      //console.log(data["Codigo"]);
      $("#valor").val(id);
      document.getElementById("categoria").value = data["categoria"];


  });
  }


}

//METODO PARA MODIFICAR EL color:

$("#actualizar").on("click",function(){

  if (datosobligatorio() == true) {

    var frm = new FormData();
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var categoria=$("#categoria").val();
    var id=$("#valor").val();

            frm.append("id", id);
            frm.append("categoria", categoria);
            frm.append("_token", csrf);

            $.ajax({
              type: "POST",
              url: urlgeeneral+"/categorias/modificar",
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

                listadocategorias();
                $('#staticBackdrop').modal('hide');
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
            title: '¿Desea anular la Categoria?',
            text: "",
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
                url: "categorias/eliminar/"+id,
                data: {"_method": "delete",'_token': csrf},

                success: function (data) {

                    listadocategorias();

                  Swal.fire(
                    'Eliminado!',
                    'La categoria ha sido anulado.',
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
  //ACTIVAR PROVEEDOR

function activar(id){

    const tabla = document.getElementById('datatable');

    tabla.addEventListener('click', (e) => {
        if (e.target.classList.contains('activar') || e.target.classList.contains('bx')) {
            Swal.fire({
                title: '¿Desea Activar la Categoria?',
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
                    url: "categorias/activar/"+id,
                    data: {"_method": "delete",'_token': csrf},

                    success: function (data) {

                        listadocategorias();

                      Swal.fire(
                        'Eliminado!',
                        'Activado Correctamente.',
                        'success'
                      )


                    }

                });





                }
              })
        }
    })

}
