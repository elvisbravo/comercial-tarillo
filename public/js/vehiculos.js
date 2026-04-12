urlgeeneral=$("#url_raiz_proyecto").val();
$("#actualizar").hide();

//FUNCION LOAD
window.addEventListener("load", function (event) {

   listadovehiculo();
   listadotipovehiculo(0);
  $(".loader").fadeOut("slow"); 


});
function listadovehiculo(){

    $.get(urlgeeneral+"/vehiculos/listadovehiculo", function (data) {
      
        llenarvehiculo(data);
        
    });

}

function listadotipovehiculo(valor){

    contenido="";
        contenido +='<option value="">--Seleccionar--</option>';
  
  
    $.get(urlgeeneral+"/vehiculos/listadotipovehiculo",function(data){
      console.log(data);
  
        for (let index = 0; index < data.length; index++) {
  
            if(valor==data[index].id){
  
                contenido += "<option value='" + data[index].id + "' selected>" + data[index].name + "</option >";
  
            }else{
  
                contenido += "<option value='" + data[index].id + "' >" + data[index].name + "</option >";
  
            }
        }
  
        document.getElementById("tipo_vehiculo_id").innerHTML=contenido;
  
        
  
        });
  
  
  }

  function llenarvehiculo(data){

    let contenido="";
     for (var i = 0; i < data.length; i++) {
       contenido += "<tr>";
       contenido += "<td style='padding:1px;text-align:center'>" +  parseInt(i+1,10) + "</td>";
       contenido += "<td style='padding:1px;text-align:center'> " + data[i].placa + "</td>";
       contenido += "<td style='padding:1px;text-align:center'> " + data[i].name + "</td>";
       contenido += "<td style='padding:1px;text-align:center'>";
       //contenido +='<i class="fas fa-edit"></i>';
       contenido +=' <button type="button" onclick="abrimodal('+ data[i].id +')" class="btn btn-info " data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fas fa-edit"></i> </button>'
       contenido +='<button type="button" onclick="eliminarsector('+ data[i].id +')" class="btn btn-danger eliminar"><i class="fas fa-trash-alt"></i></button>'
       contenido +="</td>";
       contenido += "</tr>";
   
   
     }
   
     document.getElementById("listadovehiculos").innerHTML = contenido;
     $("#datatable").dataTable();
    
   
   }

   //Para crear vehiculo

  $("#guardar").on("click",function(){

    if (datosobligatorio() == true) {

        let frm = new FormData();
        let csrf = document.querySelector('meta[name="csrf-token"]').content;
        let placa=$("#placa").val();
        let num_soat=$("#num_soat").val();
        let color=$("#color").val();
        let marca=$("#marca").val();
        let modelo=$("#modelo").val();
        let selecttipovehiculo_id = document.getElementById("tipo_vehiculo_id");
        let tipo_vehiculo_id = selecttipovehiculo_id.options[selecttipovehiculo_id.selectedIndex].value;
        let estado=1;

        frm.append("placa", placa);
        frm.append("tipo_vehiculo_id", tipo_vehiculo_id);
        frm.append("estado", num_soat);
        frm.append("estado", color);
        frm.append("estado", marca);
        frm.append("estado", modelo);
        frm.append("estado", estado);
        frm.append("_token", csrf);

        //alert(empresa);
        $.ajax({
            type: "POST",
            url: urlgeeneral+"/vehiculos/crear",
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
        
                    listadovehiculo();
                        $('#staticBackdrop').modal('hide');


               }
          });

          




    }


});

//modal que nos muestra la información al momento de editar

function abrimodal(id){

    if(id == "0"){
  
      limpiarcajasunidas();
  
      $("#guardar").show();
      $("#actualizar").hide();
  
    }else{
  
      $("#guardar").hide();
      $("#actualizar").show();
  
      $.get(urlgeeneral+"/vehiculos/editarvehiculo/" + id, function (data) {
        //console.log(data);
        $("#valor").val(id);
        document.getElementById("placa").value = data["placa"];

        document.getElementById("num_soat").value = data["num_soat"];
        document.getElementById("color").value = data["color"];
        document.getElementById("marca").value = data["marca"];
        document.getElementById("modelo").value = data["modelo"];

        listadotipovehiculo( data["tipo_vehiculo_id"]);
           
       
      
    });
    }
  
  
  }

  //METODO PARA MODIFICAR EL impuesto: 

$("#actualizar").on("click",function(){

    if (datosobligatorio() == true) {
  
      var frm = new FormData();
      var csrf = document.querySelector('meta[name="csrf-token"]').content;
      var placa=$("#placa").val();
      let num_soat=$("#num_soat").val();
      let color=$("#color").val();
      let marca=$("#marca").val();
      let modelo=$("#modelo").val();
      var selecttipovehiculo_id = document.getElementById("tipo_vehiculo_id");
      var tipo_vehiculo_id = selecttipovehiculo_id.options[selecttipovehiculo_id.selectedIndex].value;
      var id=$("#valor").val();
  
              frm.append("id", id);
              frm.append("placa", placa);
              frm.append("estado", num_soat);
              frm.append("estado", color);
              frm.append("estado", marca);
              frm.append("estado", modelo);
              frm.append("tipo_vehiculo_id", tipo_vehiculo_id);
              frm.append("_token", csrf);
  
              $.ajax({
                type: "POST",
                url: urlgeeneral+"/vehiculos/modificar",
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
                  listadovehiculo();
                  
                  //console.log(data);
  
                  }
              });
  
  
  
  
    }
  
  });
  
//metodo para eliminar un vehiculo: 


function eliminarsector(id){

    const tabla = document.getElementById('datatable');
  
    tabla.addEventListener('click', (e) => {
      if (e.target.classList.contains('eliminar') || e.target.classList.contains('bx')) {
  
          Swal.fire({
              title: '¿Desea eliminar el vehiculo?',
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
                  url: urlgeeneral+"/vehiculos/eliminar/"+id,
                  data: {"_method": "delete",'_token': csrf},
                  
                  success: function (data) {
                      listadovehiculo();
                    
                    Swal.fire(
                      'Eliminado!',
                      'El vehiculo ha sido eliminado.',
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