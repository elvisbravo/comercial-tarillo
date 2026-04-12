urlgeeneral=$("#url_raiz_proyecto").val();
$("#actualizar").hide();
const btn_consultar = document.getElementById('btn_consultar');
const tipo_documento = document.getElementById('documento_identidad');

window.addEventListener("load", function (event) {

    listadoconductores();
   $(".loader").fadeOut("slow");


 });

 function listadoconductores(){

    $.get(urlgeeneral+'/conductor/listadoconductores',function(data){

        llenarcolores(data);

    });
 }

//CARGAR LOS DATOS DE VENTAS
btn_consultar.addEventListener('click', (e) => {

  const t_documento = tipo_documento.value;
  const numero = numero_documento.value;

 
  

  const csrf = document.querySelector('meta[name="csrf-token"]').content;

  const formData = new FormData();

  formData.append('tipo_documento',t_documento);
  formData.append('num_doc',numero);
  formData.append('_token', csrf);

  fetch(urlgeeneral+"/consultar_dni_ruc",{
      method: 'POST',
      body: formData
  })
  .then(res => res.json())
  .then(data => {

      const nombre = document.getElementById('nombre');
      //const direccion=document.getElementById('direccion');
      console.log(data.original);

      if (data.original) {
        
          if (t_documento == 1) {
              nombre.value = data.original.nombres + " " + data.original.apellidoPaterno + " " + data.original.apellidoMaterno;

            
              //direccion.value = data.original.data.street;
          }

          if (t_documento == 6) {
              nombre.value = data.original.razonSocial;
              //direccion.value = data.original.direccion;
        
          }

      } else {
          alert('no fue encontrado');
      }

  })

});



 function llenarcolores(data){

    let contenido="";
    for (var i = 0; i < data.length; i++) {
      contenido += "<tr>";
      contenido += "<td style='padding:1px;text-align:center'>" +  parseInt(i+1,10) + "</td>";
      contenido += "<td style='padding:1px;text-align:center'> " + data[i].nombre + "</td>";
      contenido += "<td style='padding:1px;text-align:center'> " + data[i].numero_documento + "</td>";
      contenido += "<td style='padding:1px;text-align:center'> " + data[i].categoria_licencia + "</td>";
      contenido += "<td style='padding:1px;text-align:center'> " + data[i].num_licencia + "</td>";
      contenido += "<td style='padding:1px;text-align:center'>";
      //contenido +='<i class="fas fa-edit"></i>';
      contenido +=' <button type="button" onclick="abrimodal('+ data[i].id +')" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fas fa-edit"></i> </button>'
      contenido +='<button type="button" onclick="eliminarsector('+ data[i].id +')" class="btn btn-danger  eliminar"><i class="fas fa-trash-alt"></i> </button>'
      contenido +="</td>";
      contenido += "</tr>";
  
  
    }
  
    document.getElementById("listadoconductores").innerHTML = contenido;
    $("#datatable").dataTable();
  
  
  }
  

  $("#guardar").on("click",function(){

    if (datosobligatorio() == true) {

        var frm = new FormData();
        var csrf = document.querySelector('meta[name="csrf-token"]').content;
        var numero_documento=$("#numero_documento").val();
        var nombre=$("#nombre").val();
        var categoria_licencia=$("#categoria_licencia").val();
        var num_licencia=$("#num_licencia").val();

        frm.append("numero_documento", numero_documento);
        frm.append("nombre", nombre);
        frm.append("categoria_licencia", categoria_licencia);
        frm.append("num_licencia", num_licencia);
        frm.append("_token", csrf);


        $.ajax({
            type: "POST",
            url: urlgeeneral+"/conductor/crear",
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

                      listadoconductores();
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

      showSpinner();
  
      $.get(urlgeeneral+"/conductor/editar/" + id, function (data) {

        hideSpinner();
        //console.log(data["Codigo"]);
        $("#valor").val(id);
        document.getElementById("nombre").value = data["nombre"];
        document.getElementById("numero_documento").value = data["numero_documento"];
        document.getElementById("categoria_licencia").value = data["categoria_licencia"];
        document.getElementById("num_licencia").value = data["num_licencia"];
  
  
    });
    }
  
  
  }



  $("#actualizar").on("click",function(){

    if (datosobligatorio() == true) {
  
      var frm = new FormData();
      var csrf = document.querySelector('meta[name="csrf-token"]').content;
      var numero_documento=$("#numero_documento").val();
      var nombre=$("#nombre").val();
      var categoria_licencia=$("#categoria_licencia").val();
      var num_licencia=$("#num_licencia").val();

      var id=$("#valor").val();
  
        frm.append("id", id);
        frm.append("numero_documento", numero_documento);
        frm.append("nombre", nombre);
        frm.append("categoria_licencia", categoria_licencia);
        frm.append("num_licencia", num_licencia);
        frm.append("_token", csrf);

        //alert(id);

        showSpinner();
  
              $.ajax({
                type: "POST",
                url: urlgeeneral+"/conductor/modificar",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: frm,
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function (data) {
  
                    hideSpinner();

                  Swal.fire({
                    icon: 'success',
                    title: 'Oops...',
                    text: 'Modificado Correctamente',
                    footer: ''
                  })
  
                  listadoconductores();
                  $('#staticBackdrop').modal('hide');
                 // console.log(data);
  
                  }
              });
  
  
  
  
    }
  
  });

  


  
function eliminarsector(id){

    const tabla = document.getElementById('datatable');
  
    tabla.addEventListener('click', (e) => {
      if (e.target.classList.contains('eliminar') || e.target.classList.contains('bx')) {
          Swal.fire({
              title: '¿Desea eliminar el Conductor?',
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
                  url: urlgeeneral+"/conductor/eliminar/"+id,
                  data: {"_method": "delete",'_token': csrf},
  
                  success: function (data) {
  
                    listadoconductores();
  
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




  
function showSpinner() {
    $('.loader').show();
    $('#spinner').show();
  }
  
  function hideSpinner() {
    $('.loader').hide();
    $('#spinner').hide();
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
  