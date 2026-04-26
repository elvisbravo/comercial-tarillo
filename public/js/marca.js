 
  urlgeeneral=$("#url_raiz_proyecto").val();
  $("#actualizar").hide();

  //FUNCION LOAD
window.addEventListener("load", function (event) {

     listadomarca();
    $(".loader").fadeOut("slow"); 

  
  });


  function listadomarca(){

    $.get(urlgeeneral+"/marcas/listadomarca", function (data) {
      console.log(data);

        llenarmarca(data);
        
    });

}

function llenarmarca(data){

  let contenido="";
  for (var i = 0; i < data.length; i++) {
    contenido += "<tr>";
    contenido += "<td style='padding:1px;text-align:center'>" +  parseInt(i+1,10) + "</td>";
    contenido += "<td style='padding:1px;text-align:center'> " + data[i].descripcion + "</td>";
    contenido += "<td style='padding:1px;text-align:center'>";
    
    if (typeof canEdit !== 'undefined' && canEdit) {
      contenido += ' <button type="button" onclick="abrimodal(' + data[i].id + ')" class="btn btn-info " data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fas fa-edit"></i> </button>';
    }

    if (typeof canDelete !== 'undefined' && canDelete) {
      contenido += ' <button type="button" onclick="eliminarsector(' + data[i].id + ')" class="btn btn-danger eliminar"><i class="fas fa-trash-alt eliminar"></i></button>';
    }

    contenido += "</td>";
    contenido += "</tr>";


  }

  document.getElementById("listadecolores").innerHTML = contenido;
  initDataTable("#datatable");


}


  
  $("#guardar").on("click",function(){

    if (datosobligatorio() == true) {

        var frm = new FormData();
        var csrf = document.querySelector('meta[name="csrf-token"]').content;
        var descripcion=$("#color").val();


        frm.append("descripcion", descripcion);
        frm.append("_token", csrf);


        $.ajax({
            type: "POST",
            url: urlgeeneral+"/marcas/crear",
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

    $.get(urlgeeneral+"/marcas/editarmarca/" + id, function (data) {
      //console.log(data["Codigo"]);
      $("#valor").val(id);
      document.getElementById("color").value = data["descripcion"];
     
    
  });
  }


}

//METODO PARA MODIFICAR EL color: 

$("#actualizar").on("click",function(){

  if (datosobligatorio() == true) {

    var frm = new FormData();
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var descripcion=$("#color").val();
    var id=$("#valor").val();

            frm.append("id", id);
            frm.append("descripcion", descripcion);
            frm.append("_token", csrf);

            $.ajax({
              type: "POST",
              url: urlgeeneral+"/marcas/modificar",
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
                url: "marcas/eliminar/"+id,
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
  