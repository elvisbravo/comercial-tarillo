  
  urlgeeneral=$("#url_raiz_proyecto").val();

  //FUNCION LOAD
window.addEventListener("load", function (event) {

      listadopermisos();
    $(".loader").fadeOut("slow"); 

  
  });

  function listadopermisos(){

    $.get(urlgeeneral+"/permisos/listapermisos", function (data) {
      //console.log(data);

        llenarpermisos(data);
        
    });

}

//abril modal
function abrimodal(){

}

function llenarpermisos(data){

      let contenido="";
      for (var i = 0; i < data.length; i++) {
        contenido += "<tr>";
        contenido += "<td style='padding:1px;text-align:center'>" +  parseInt(i+1,10) + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>" + data[i].name + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>" + data[i].guard_name + "</td>";
        contenido += "</tr>";


      }

      document.getElementById("listapermisos").innerHTML = contenido;
      $("#datatable").dataTable();


}

    $("#guardar").on("click",function(){

        if (datosobligatorio() == true) {

            var frm = new FormData();
            var csrf = document.querySelector('meta[name="csrf-token"]').content;
            var name=$("#permiso").val();
            var guard_name=$("#guard_name").val();

            frm.append("name", name);
            frm.append("guard_name", guard_name);
            frm.append("_token", csrf);


            $.ajax({
                type: "POST",
                url: urlgeeneral+"/permisos/crear",
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
            
                            listadopermisos();
                            $('#staticBackdrop').modal('hide');
    
    
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
                    text: 'El campo Permiso es Obligatorio!',
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