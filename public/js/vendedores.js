urlgeeneral=$("#url_raiz_proyecto").val();
$("#actualizar").hide();

//FUNCION LOAD
window.addEventListener("load", function (event) {
    
  listadovendedores();
  listadousuarios();
  $(".loader").fadeOut("slow"); 


});

function listadovendedores(){
  $.get(urlgeeneral+"/vendedores/listadovendedores", function (data) {
    console.log(data);

      llenarvendedores(data);
      
  });
}

function llenarvendedores(data) {
    if ($.fn.DataTable.isDataTable('#datatable')) {
        $('#datatable').DataTable().destroy();
    }

    let contenido = "";
    for (var i = 0; i < data.length; i++) {
        contenido += "<tr>";
        contenido += "<td style='padding:1px;text-align:center'>" + parseInt(i + 1, 10) + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].nombre + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].documento + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].direccion + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>";

        if (typeof canEdit !== 'undefined' && canEdit) {
            contenido += ' <button type="button" onclick="abrimodal(' + data[i].id + ')" class="btn btn-info waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#staticBackdrop" title="Editar"><i class="fas fa-edit"></i> </button>';
        }

        if (typeof canDelete !== 'undefined' && canDelete) {
            contenido += ' <button type="button" onclick="eliminarsector(' + data[i].id + ')" class="btn btn-danger waves-effect waves-light eliminar" title="Eliminar"><i class="fas fa-trash-alt"></i></button>';
        }

        contenido += "</td>";
        contenido += "</tr>";
    }

    $('#listado_vendedores').empty().html(contenido);
    initDataTable("#datatable");
}
 //listado usuarios

 function listadousuarios(valor){

  contenido="";
      contenido +='<option value="">--Seleccionar--</option>';


  $.get(urlgeeneral+"/vendedores/listadousuarios",function(data){
    //console.log(data);

      for (let index = 0; index < data.length; index++) {

          if(valor==data[index].id){

              contenido += "<option value='" + data[index].id + "' selected>" + data[index].name + "</option >";

          }else{

              contenido += "<option value='" + data[index].id + "' >" + data[index].name + "</option >";

          }
      }

      document.getElementById("usuario_id").innerHTML=contenido;

      

      });


}

 //Crear un nuevo vendedor

 $("#guardar").on("click",function(){

  if (datosobligatorio() == true) {

      var frm = new FormData();
      var csrf = document.querySelector('meta[name="csrf-token"]').content;
      var selectusuario_id = document.getElementById("usuario_id");
      var nombre = selectusuario_id.options[selectusuario_id.selectedIndex].text;
      var documento=$("#documento").val();
      var selectusuario_id = document.getElementById("usuario_id");
      var usuario_id = selectusuario_id.options[selectusuario_id.selectedIndex].value;
      var estado=1;
      var direccion=$("#direccion").val();

      frm.append("nombre", nombre);
      frm.append("documento", documento);
      frm.append("direccion", direccion);
      frm.append("usuario_id", usuario_id);
      frm.append("estado", estado);
      frm.append("_token", csrf);

      //alert(usuario_id);
      $.ajax({
          type: "POST",
          url: urlgeeneral+"/vendedores/crear",
          data: frm,
          dataType: 'json',
          contentType: false,
          processData: false,
          success: function (data) {
            //alert(data.success);
            if (data.success == true) {
              Swal.fire({
                icon: 'success',
                title: 'Oops...',
                text: 'Creado Correctamente',
                footer: ''
              })

              listadovendedores();
                  $('#staticBackdrop').modal('hide');
            }else{
              Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'El vendedor ya se encuentra registrado!',
                footer: ''
              })
            }

            
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

    $.get(urlgeeneral+"/vendedores/editarvendedor/" + id, function (data) {
      //console.log(data);
      $("#valor").val(id);
      listadousuarios( data["usuario_id"]);
      document.getElementById("documento").value = data["documento"];
      document.getElementById("direccion").value = data["direccion"];
      
    
  });
  }


}

//METODO PARA MODIFICAR: 

$("#actualizar").on("click",function(){

  if (datosobligatorio() == true) {

    var frm = new FormData();
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var selectusuario_id = document.getElementById("usuario_id");
    var nombre = selectusuario_id.options[selectusuario_id.selectedIndex].text;
    var documento=$("#documento").val();
    var direccion=$("#direccion").val();
    var selectusuario_id = document.getElementById("usuario_id");
    var usuario_id = selectusuario_id.options[selectusuario_id.selectedIndex].value;
    var id=$("#valor").val();

            frm.append("id", id);
            frm.append("nombre", nombre);
            frm.append("documento", documento);
            frm.append("direccion", direccion);
            frm.append("usuario_id", usuario_id);
            frm.append("_token", csrf);

            $.ajax({
              type: "POST",
              url: urlgeeneral+"/vendedores/modificar",
              headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
              data: frm,
              dataType: 'json',
              contentType: false,
              processData: false,
              success: function (data) {
                //alert (data.success);
                if (data.success == true) {
                  Swal.fire({
                    icon: 'success',
                    title: 'Oops...',
                    text: 'Modificado Correctamente',
                    footer: ''
                  })
    
                  listadovendedores();
                      $('#staticBackdrop').modal('hide');
                }else{
                  Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'El vendedor ya se encuentra registrado!',
                    footer: ''
                  })
                }
    
                //console.log(data);

                }
            });




  }

});

//metodo para eliminar: 
function eliminarsector(id){

  const tabla = document.getElementById('datatable');

  tabla.addEventListener('click', (e) => {
    if (e.target.classList.contains('eliminar') || e.target.classList.contains('bx')) {

        Swal.fire({
            title: '¿Desea eliminar el vendedor?',
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
                url: urlgeeneral+"/vendedores/eliminar/"+id,
                data: {"_method": "delete",'_token': csrf},
                
                success: function (data) {
                    listadovendedores();
                  
                  Swal.fire(
                    'Eliminado!',
                    'El vendedor ha sido eliminado.',
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
         /* alert("vacios");
          obligarotio[i].parentNode.classList.add("form-control error");
          swal("Here's a message!")
          swal("Error!", "Los Campos Son Obligatorios!", "error") */
          Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Todos los campos con * son Obligatorios!',
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
 