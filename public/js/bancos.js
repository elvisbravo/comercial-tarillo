urlgeeneral=$("#url_raiz_proyecto").val();
$("#actualizar").hide();

//FUNCION LOAD
window.addEventListener("load", function (event) {
    listadobancos();
  $(".loader").fadeOut("slow"); 


});

function listadobancos(){

  $.get(urlgeeneral+"/bancos/listadobancos", function (data) {
    console.log(data);

      llenarbancos(data);
      
  });

}
function llenarbancos(data){

  let contenido="";
   for (var i = 0; i < data.length; i++) {
     contenido += "<tr>";
     contenido += "<td style='padding:1px;text-align:center'>" +  parseInt(i+1,10) + "</td>";
     contenido += "<td style='padding:1px;text-align:center'> " + data[i].nombre + "</td>";
     contenido += "<td style='padding:1px;text-align:center'> " + data[i].abreviatura + "</td>";
     contenido += "<td style='padding:1px;text-align:center'>";
     //contenido +='<i class="fas fa-edit"></i>';
     contenido +=' <button type="button" onclick="abrimodal('+ data[i].id +')" class="btn btn-info " data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fas fa-edit"></i> </button>'
     contenido +='<button type="button" onclick="eliminarsector('+ data[i].id +')" class="btn btn-danger eliminar"><i class="fas fa-trash-alt"></i></button>'
     contenido +="</td>";
     contenido += "</tr>";
 
 
   }
 
   document.getElementById("listado_bancos").innerHTML = contenido;
   $("#datatable").dataTable();
  
 
 }
 

 $("#guardar").on("click",function(){

  if (datosobligatorio() == true) {

      var frm = new FormData();
      var csrf = document.querySelector('meta[name="csrf-token"]').content;
      var nombre=$("#nombre").val();
      var abreviatura=$("#abreviatura").val();
      var estado= 1;

      frm.append("nombre", nombre);
      frm.append("abreviatura", abreviatura);
      frm.append("estado", estado);
      
      frm.append("_token", csrf);

      //alert(empresa);
      $.ajax({
          type: "POST",
          url: urlgeeneral+"/bancos/crear",
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
      
                  listadobancos();
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

    $.get(urlgeeneral+"/bancos/editarbanco/" + id, function (data) {
      //console.log(data);
      $("#valor").val(id);
      document.getElementById("nombre").value = data["nombre"];
      document.getElementById("abreviatura").value = data["abreviatura"];
      
    
  });
  }


}

//METODO PARA MODIFICAR: 

$("#actualizar").on("click",function(){

  if (datosobligatorio() == true) {

    var frm = new FormData();
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var nombre=$("#nombre").val();
    var abreviatura=$("#abreviatura").val();
    var id=$("#valor").val();

            frm.append("id", id);
            frm.append("nombre", nombre);
            frm.append("abreviatura", abreviatura);
            frm.append("_token", csrf);

            $.ajax({
              type: "POST",
              url: urlgeeneral+"/bancos/modificar",
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
                listadobancos();
                
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
            title: '¿Desea eliminar el impuesto?',
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
                url: urlgeeneral+"/bancos/eliminar/"+id,
                data: {"_method": "delete",'_token': csrf},
                
                success: function (data) {
                    listadobancos();
                  
                  Swal.fire(
                    'Eliminado!',
                    'El banco ha sido eliminado.',
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