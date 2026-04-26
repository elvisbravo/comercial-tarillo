
  urlgeeneral=$("#url_raiz_proyecto").val();
  $("#actualizar").hide();

  //FUNCION LOAD
window.addEventListener("load", function (event) {

     listadoimpuesto();
     empresas(0);
    $(".loader").fadeOut("slow"); 

  
  });


  function listadoimpuesto(){

    $.get(urlgeeneral+"/impuestos/listadoimpuesto", function (data) {
      console.log(data);

        llenarimpuesto(data);
        
    });

}

function empresas(valor){

  contenido="";
      contenido +='<option value="">--Seleccionar--</option>';


  $.get(urlgeeneral+"/impuestos/listadoempresas",function(data){
    console.log(data);

      for (let index = 0; index < data.length; index++) {

          if(valor==data[index].id){

              contenido += "<option value='" + data[index].id + "' selected>" + data[index].nombre_comercial + "</option >";

          }else{

              contenido += "<option value='" + data[index].id + "' >" + data[index].nombre_comercial + "</option >";

          }
      }

      document.getElementById("empresa_id").innerHTML=contenido;

      

      });


}

function llenarimpuesto(data) {
    if ($.fn.DataTable.isDataTable('#datatable')) {
        $('#datatable').DataTable().destroy();
    }

    let contenido = "";
    for (var i = 0; i < data.length; i++) {
        contenido += "<tr>";
        contenido += "<td style='padding:1px;text-align:center'>" + parseInt(i + 1, 10) + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].impuesto + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].tipo_impuesto + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].etiqueta_factura + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].nombre_comercial + "</td>";
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

    $('#listadoimpuestos').empty().html(contenido);
    initDataTable("#datatable");
}


//Para mandar crear

  $("#guardar").on("click",function(){

    if (datosobligatorio() == true) {

        var frm = new FormData();
        var csrf = document.querySelector('meta[name="csrf-token"]').content;
        var impuesto=$("#impuesto").val();
        var tipo_impuesto=$("#tipo_impuesto").val();
        var etiqueta_factura=$("#etiqueta_factura").val();
        var selectempresa_id = document.getElementById("empresa_id");
        var empresa_id = selectempresa_id.options[selectempresa_id.selectedIndex].value;
        var estado=1;

        frm.append("impuesto", impuesto);
        frm.append("tipo_impuesto", tipo_impuesto);
        frm.append("etiqueta_factura", etiqueta_factura);
        frm.append("empresa_id", empresa_id);
        frm.append("estado", estado);
        frm.append("_token", csrf);

        //alert(empresa);
        $.ajax({
            type: "POST",
            url: urlgeeneral+"/impuestos/crear",
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
        
                    listadoimpuesto();
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

    $.get(urlgeeneral+"/impuestos/editarimpuesto/" + id, function (data) {
      //console.log(data);
      $("#valor").val(id);
      document.getElementById("impuesto").value = data["impuesto"];
      document.getElementById("tipo_impuesto").value = data["tipo_impuesto"];
      document.getElementById("etiqueta_factura").value = data["etiqueta_factura"];
      empresas( data["empresa_id"]);
         
     
    
  });
  }


}

//METODO PARA MODIFICAR EL impuesto: 

$("#actualizar").on("click",function(){

  if (datosobligatorio() == true) {

    var frm = new FormData();
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var impuesto=$("#impuesto").val();
    var tipo_impuesto=$("#tipo_impuesto").val();
    var etiqueta_factura=$("#etiqueta_factura").val();
    var selectempresa_id = document.getElementById("empresa_id");
    var empresa_id = selectempresa_id.options[selectempresa_id.selectedIndex].value;
    var id=$("#valor").val();

            frm.append("id", id);
            frm.append("impuesto", impuesto);
            frm.append("tipo_impuesto", tipo_impuesto);
            frm.append("etiqueta_factura", etiqueta_factura);
            frm.append("empresa_id", empresa_id);
            frm.append("_token", csrf);

            $.ajax({
              type: "POST",
              url: urlgeeneral+"/impuestos/modificar",
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
                listadoimpuesto();
                
                //console.log(data);

                }
            });




  }

});



//metodo para eliminar un impuesto: 


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
                url: urlgeeneral+"/impuestos/eliminar/"+id,
                data: {"_method": "delete",'_token': csrf},
                
                success: function (data) {
                    listadoimpuesto();
                  
                  Swal.fire(
                    'Eliminado!',
                    'El impuesto ha sido eliminado.',
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
  