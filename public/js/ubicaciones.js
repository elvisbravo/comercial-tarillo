urlgeeneral=$("#url_raiz_proyecto").val();
$("#actualizar").hide();

window.addEventListener("load", function (event) {

    //listarcolores();
   $(".loader").fadeOut("slow");
   tipo(0);
   almacenes(0);


 });


function almacenes(id){

    $.get(urlgeeneral+"/stock-location/almacenes",function(data){

        console.log(data);
        let  opciones = '';
         opciones +='<option value="">--Seleccionar Supervisor--</option>';

        for (var i = 0; i < data.length; i++) {

            if(id== data[i].id){

                opciones += '<option value="' + data[i].id + '" selected>' + data[i].nombre+ '</option>';

            }else{

                opciones += '<option value="' + data[i].id + '">' + data[i].nombre+ '</option>';
            }

        }

        document.getElementById("almacen_id").innerHTML = opciones;
   });


}

function tipo(id){

    $.get(urlgeeneral+"/stock-location/tipohubicacion",function(data){

        console.log(data);
        let  opciones = '';
         opciones +='<option value="">--Seleccionar Supervisor--</option>';

        for (var i = 0; i < data.length; i++) {

            if(id==data[i].id){

                opciones += '<option value="'+data[i].id +'" selected>'+ data[i].name+'</option>';
                
            }else{
                opciones += '<option value="'+data[i].id +'">'+ data[i].name+'</option>';
            }

            

        }

        document.getElementById("tipo_ubicacion_id").innerHTML = opciones;
   });


}

//METODO PARA CARGAR LA INFORMACIÓN

//METODO PARA GAURDAR LOS DATOS

$("#guardar").on("click",function(){

    if (datosobligatorio() == true) {

        var frm = new FormData();
        var csrf = document.querySelector('meta[name="csrf-token"]').content;
        var name=$("#name").val();

        var selectalmacen_id=document.getElementById("almacen_id");
        var almacen_id=selectalmacen_id.options[selectalmacen_id.selectedIndex].value;

        var selecttipo_ubicacion_id=document.getElementById("tipo_ubicacion_id");
        var type_location=selecttipo_ubicacion_id.options[selecttipo_ubicacion_id.selectedIndex].value;

        //RESPOSABLE
        var responsable=$("#responsable").val();

        //METODO PARA CAPTURAR LOS DATOS
        var checkBox = document.getElementById("es_chatarra");
        var checkBoxdos = document.getElementById("devolucion");

        //METODO PARA CAPTAR SI ES CHATARRA

        if (checkBox.checked == true){

            es_chatarra=true;
        }else{
            es_chatarra=false;
        }

        //METODO PARA DEVOLUCIÓN
        if (checkBoxdos.checked == true){

            devolucion=true;

            alert(devolucion);

        }else{

            devolucion=false;
        }


        


        frm.append("name", name);
        frm.append("almacen_id", almacen_id);
        frm.append("type_location", type_location);
        frm.append("responsable", responsable);
        frm.append("es_chatarra", es_chatarra);
        frm.append("devolucion", devolucion);

        frm.append("_token", csrf);

        


        $.ajax({
            type: "POST",
            url: urlgeeneral+"/stock-location/crear",
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

                        //listarcolores();
                        $('#staticBackdrop').modal('hide');
                        location.reload();

                        


               }
          });




    }


});



//METODO PARA GUARDAR LA INFORMACIÓN
$("#actualizar").on("click",function(){

    if (datosobligatorio() == true) {

        var frm = new FormData();
        var csrf = document.querySelector('meta[name="csrf-token"]').content;
        var name=$("#name").val();
        var id=$("#valor").val();

        var selectalmacen_id=document.getElementById("almacen_id");
        var almacen_id=selectalmacen_id.options[selectalmacen_id.selectedIndex].value;

        var selecttipo_ubicacion_id=document.getElementById("tipo_ubicacion_id");
        var type_location=selecttipo_ubicacion_id.options[selecttipo_ubicacion_id.selectedIndex].value;

        //RESPOSABLE
        var responsable=$("#responsable").val();

        //METODO PARA CAPTURAR LOS DATOS
        var checkBox = document.getElementById("es_chatarra");
        var checkBoxdos = document.getElementById("devolucion");

        //METODO PARA CAPTAR SI ES CHATARRA

        if (checkBox.checked == true){

            es_chatarra=true;
        }else{
            es_chatarra=false;
        }

        //METODO PARA DEVOLUCIÓN
        if (checkBoxdos.checked == true){

            devolucion=true;

            alert(devolucion);

        }else{

            devolucion=false;
        }


        

        frm.append("id", id);
        frm.append("name", name);
        frm.append("almacen_id", almacen_id);
        frm.append("type_location", type_location);
        frm.append("responsable", responsable);
        frm.append("es_chatarra", es_chatarra);
        frm.append("devolucion", devolucion);

        frm.append("_token", csrf);

       


        $.ajax({
            type: "POST",
            url: urlgeeneral+"/stock-location/modificar",
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
              //listadomarca();
              
              //console.log(data);
              location.reload();


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

                //alert(id);
            
                $.get(urlgeeneral+"/stock-location/editar/" + id, function (data) {
                    //console.log(data["Codigo"]);
                    $("#valor").val(id);
                    document.getElementById("name").value = data["name"];
                    almacenes(data["almacen_id"]);
                    tipo(data["type_location"]);

                    document.getElementById("responsable").value = data["responsable"];
            
            
                });
        }
    
  
  }


  function eliminar(id){

    Swal.fire({
        title: '¿Desea eliminar el Almacén Interno?',
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
                            url: urlgeeneral+"/stock-location/eliminar/"+id,
                            data: {"_method": "delete",'_token': csrf},
                            
                            success: function (data) {
                               // listadomarca();
                            
                         
                            Swal.fire(
                                'Eliminado!',
                                'La Ubicación Interna fue Eliminada Correctamente.',
                                'success'
                            )

                            location.reload();

                                
                            
                            }

                        });


        


        }

        
      })
  
  }

  function activar(id){

    Swal.fire({
        title: '¿Desea eliminar el Almacén Interno?',
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
                            url: urlgeeneral+"/stock-location/activar/"+id,
                            data: {"_method": "delete",'_token': csrf},
                            
                            success: function (data) {
                               // listadomarca();
                            
                            Swal.fire(
                                'Activada!',
                                'La Ubicación Interna fue Activada Correctamente.',
                                'success'
                            )

                            location.reload();
                                
                            
                            }

                        });


        


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
                text: 'Los campos Marcados de Color Rojo son Obligatorios!',
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
  