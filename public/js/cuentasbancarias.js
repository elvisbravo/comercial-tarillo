urlgeeneral=$("#url_raiz_proyecto").val();
$("#actualizar").hide();

//FUNCION LOAD
window.addEventListener("load", function (event) {

    listadoCuentasBancarias();
    listadoBancos();
  $(".loader").fadeOut("slow"); 


});

function listadoCuentasBancarias(){

  $.get(urlgeeneral+"/cuentasbancarias/listadoCuentasBancarias", function (data) {
    console.log(data);

      llenarCuentasBancarias(data);
      
  });

}

//listado cuentas bancarias

function llenarCuentasBancarias(data) {
    if ($.fn.DataTable.isDataTable('#datatable')) {
        $('#datatable').DataTable().destroy();
    }

    let contenido = "";
    for (var i = 0; i < data.length; i++) {
        contenido += "<tr>";
        contenido += "<td style='padding:1px;text-align:center'>" + parseInt(i + 1, 10) + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].cuenta_corriente + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].cuenta_cci + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].nombre + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>";

        if (typeof canEdit !== 'undefined' && canEdit) {
            contenido += ' <button type="button" onclick="abrimodal(' + data[i].id + ')" class="btn btn-info waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fas fa-edit"></i> </button>';
        }

        if (typeof canDelete !== 'undefined' && canDelete) {
            contenido += ' <button type="button" onclick="eliminarsector(' + data[i].id + ')" class="btn btn-danger waves-effect waves-light eliminar"><i class="fas fa-trash-alt"></i></button>';
        }

        contenido += "</td>";
        contenido += "</tr>";
    }

    $('#listado_cuentas_bancarias').empty().html(contenido);
    initDataTable("#datatable");
}

 //listado bancos

 function listadoBancos(valor){

  contenido="";
      contenido +='<option value="">--Seleccionar--</option>';


  $.get(urlgeeneral+"/cuentasbancarias/listadoBancos",function(data){
    //console.log(data);

      for (let index = 0; index < data.length; index++) {

          if(valor==data[index].id){

              contenido += "<option value='" + data[index].id + "' selected>" + data[index].nombre + "</option >";

          }else{

              contenido += "<option value='" + data[index].id + "' >" + data[index].nombre + "</option >";

          }
      }

      document.getElementById("banco_id").innerHTML=contenido;

      

      });


}


//abrir modal

function abrimodal(id){

  if(id == "0"){

    limpiarcajasunidas();

    $("#guardar").show();
    $("#actualizar").hide();

  }else{

    $("#guardar").hide();
    $("#actualizar").show();

    $.get(urlgeeneral+"/cuentasbancarias/editarCuentasBancarias/" + id, function (data) {
      //console.log(data);
      $("#valor").val(id);
      document.getElementById("cuenta_corriente").value = data["cuenta_corriente"];
      document.getElementById("cuenta_cci").value = data["cuenta_cci"];
      listadoBancos( data["banco_id"]);
         
     
    
  });
  }
}

//Para mandar crear

$("#guardar").on("click",function(){

  if (datosobligatorio() == true) {

      var frm = new FormData();
      var csrf = document.querySelector('meta[name="csrf-token"]').content;
      var cuenta_corriente=$("#cuenta_corriente").val();
      var cuenta_cci=$("#cuenta_cci").val();
      var selectBanco_id = document.getElementById("banco_id");
      var banco_id = selectBanco_id.options[selectBanco_id.selectedIndex].value;
      var estado=1;

      frm.append("cuenta_corriente", cuenta_corriente);
      frm.append("cuenta_cci", cuenta_cci);
      frm.append("banco_id", banco_id);
      frm.append("estado", estado);
      frm.append("_token", csrf);

      //alert(empresa);
      $.ajax({
          type: "POST",
          url: urlgeeneral+"/cuentasbancarias/crear",
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
      
                  listadoCuentasBancarias();
                      $('#staticBackdrop').modal('hide');


             }
        });

        




  }


});

//METODO MODIFICAR LOS DATOS 

$("#actualizar").on("click",function(){

  if (datosobligatorio() == true) {

    var frm = new FormData();
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var cuenta_corriente=$("#cuenta_corriente").val();
    var cuenta_cii=$("#cuenta_cci").val();
    var selectBanco_id = document.getElementById("banco_id"); /*Obtener el SELECT */
    var banco_id = selectBanco_id.options[selectBanco_id.selectedIndex].value;
    var id=$("#valor").val();

            frm.append("id", id);
            frm.append("cuenta_corriente", cuenta_corriente);
            frm.append("cuenta_cci", cuenta_cii);
            frm.append("banco_id", banco_id);
            frm.append("_token", csrf);

            $.ajax({
              type: "POST",
              url: urlgeeneral+"/cuentasbancarias/modificar",
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
                listadoCuentasBancarias();
              $('#staticBackdrop').modal('hide');
                
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
            title: '¿Desea eliminar la cuenta bancaria?',
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
                url: urlgeeneral+"/cuentasbancarias/eliminar/"+id,
                data: {"_method": "delete",'_token': csrf},
                
                success: function (data) {
                    listadoCuentasBancarias();
                  
                  Swal.fire(
                    'Eliminado!',
                    'La cuenta bancaria ha sido eliminada.',
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