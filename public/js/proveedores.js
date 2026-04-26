urlgeeneral=$("#url_raiz_proyecto").val();
const btn_consultar = document.getElementById('btn_consultar');
const tipo_documento = document.getElementById('documento_identidad');
const numero_documento = document.getElementById('ruc');
$("#actualizar").hide();

  //FUNCION LOAD
  window.addEventListener("load", function (event) {

    todosproveedores();
   $(".loader").fadeOut("slow");

   selecttipo(0);


 });


 function todosproveedores(){

      $.get(urlgeeneral+"/proveedores/listadomarca",function(data){

           llenardatos(data);
      });
 }

 function llenardatos(data) {
    if ($.fn.DataTable.isDataTable('#datatable')) {
        $('#datatable').DataTable().destroy();
    }

    let contenido = "";
    for (var i = 0; i < data.length; i++) {
        contenido += "<tr>";
        contenido += "<td style='padding:1px;text-align:center'>" + parseInt(i + 1, 10) + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].razon_social + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].ruc + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].nombre_comercial + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + (data[i].telefono || '') + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + (data[i].direccion || '') + "</td>";
        
        if (data[i].estado == 0) {
            contenido += "<td style='padding:1px;text-align:center'><span class='badge badge-soft-danger font-size-12'><i class='fas fa-times-circle me-1'></i> Inactivo</span></td>";
        } else {
            contenido += "<td style='padding:1px;text-align:center'><span class='badge badge-soft-success font-size-12'><i class='fas fa-check-circle me-1'></i> Activo</span></td>";
        }

        contenido += "<td style='padding:1px;text-align:center'>";
        
        if (typeof canViewDetail !== 'undefined' && canViewDetail) {
            contenido += '<a href="proveedores/' + data[i].id + '" type="button" class="btn btn-success waves-effect waves-light" title="Ver Detalle"><i class="fas fa-eye"></i> </a>';
        }

        if (typeof canEdit !== 'undefined' && canEdit) {
            contenido += ' <button type="button" onclick="abrimodal(' + data[i].id + ')" class="btn btn-info waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#staticBackdrop" title="Editar"><i class="fas fa-edit"></i> </button>';
        }

        if (data[i].estado == 0) {
            if (typeof canDelete !== 'undefined' && canDelete) {
                contenido += ' <button type="button" onclick="activar(' + data[i].id + ')" class="btn btn-warning waves-effect waves-light" title="Activar"><i class="fas fa-sync activar"></i> </button>';
            }
        } else {
            if (typeof canDelete !== 'undefined' && canDelete) {
                contenido += ' <button type="button" onclick="eliminar(' + data[i].id + ')" class="btn btn-danger waves-effect waves-light" title="Desactivar"><i class="fas fa-trash-alt eliminar"></i> </button>';
            }
        }

        contenido += "</td>";
        contenido += "</tr>";
    }

    $('#listadoproveedores').empty().html(contenido);
    initDataTable("#datatable");
}

 //METODO PARA BUSCAR EL DOCUMENTO DEL PROVEEDOR

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

        const nombre = document.getElementById('nombre_comercial');
        const direccion=document.getElementById('direccion');

        if (data.original) {
          
            if (t_documento == 1) {
                nombre.value = data.original.nombres + " " + data.original.apellidoPaterno + " " + data.original.apellidoMaterno;
                //direccion.value = data.original.data.street;
            }

            if (t_documento == 6) {
                nombre.value = data.original.razonSocial;
                direccion.value = data.original.direccion;
          
            }

        } else {
            alert('no fue encontrado');
        }

    })

});



 //metodo para guardar
 $("#guardar").on("click",function(){

    if (datosobligatorio() == true) {
        var frm = new FormData();
        var csrf = document.querySelector('meta[name="csrf-token"]').content;

        var selectrazon_social=document.getElementById("razon_social");
        var razon_social=selectrazon_social.options[selectrazon_social.selectedIndex].value;
        var ruc=$("#ruc").val();
        var nombre_comercial=$("#nombre_comercial").val();
        var telefono=$("#telefono").val();
        var direccion=$("#direccion").val();
        var email=$("#email").val();
        var web_sitie=$("#web_sitie").val();
        var contacto=$("#contacto").val();
        var selectdocumento_identidad=document.getElementById("documento_identidad");
        var documento_identidad=selectdocumento_identidad.options[selectdocumento_identidad.selectedIndex].value;


        frm.append("ruc", ruc);
        frm.append("razon_social", razon_social);
        frm.append("nombre_comercial", nombre_comercial);
        frm.append("telefono", telefono);
        frm.append("direccion", direccion);
        frm.append("email", email);
        frm.append("web_sitie", web_sitie);
        frm.append("contacto", contacto);
        frm.append("documento_identidad", documento_identidad);
        frm.append("_token", csrf);

        $.ajax({
            type: "POST",
            url: urlgeeneral+"/proveedores/crear",
            data: frm,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function (data) {

                //console.log(data);

                 todosproveedores();

                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Creado Correctamente',
                    footer: ''
                  })

                  $('#staticBackdrop').modal('hide');

              },
              error: function (xhr) {
                if (xhr.status === 422) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Validación',
                        text: 'El RUC/DNI ya se encuentra registrado.',
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error inesperado al procesar la solicitud.',
                    });
                }
              }
          });




    }


});


//METODO PARA ACTUALIZAR LOS DATOS
$("#actualizar").on("click",function(){

    if (datosobligatorio() == true) {
        var frm = new FormData();
        var csrf = document.querySelector('meta[name="csrf-token"]').content;

        var selectrazon_social=document.getElementById("razon_social");
        var razon_social=selectrazon_social.options[selectrazon_social.selectedIndex].value;
        var ruc=$("#ruc").val();
        var nombre_comercial=$("#nombre_comercial").val();
        var telefono=$("#telefono").val();
        var direccion=$("#direccion").val();
        var email=$("#email").val();
        var web_sitie=$("#web_sitie").val();
        var contacto=$("#contacto").val();
        var id=$("#valor").val();


        frm.append("ruc", ruc);
        frm.append("razon_social", razon_social);
        frm.append("nombre_comercial", nombre_comercial);
        frm.append("telefono", telefono);
        frm.append("direccion", direccion);
        frm.append("email", email);
        frm.append("web_sitie", web_sitie);
        frm.append("contacto", contacto);
        frm.append("id", id);
        frm.append("_token", csrf);

        $.ajax({
            type: "POST",
            url: urlgeeneral+"/proveedores/modificar",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: frm,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function (data) {

                todosproveedores();
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Proveedor Modificado Correctamente',
                    footer: ''
                })

                $('#staticBackdrop').modal('hide');


            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Validación',
                        text: 'El RUC/DNI ya se encuentra registrado.',
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error inesperado al procesar la solicitud.',
                    });
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

          $.get(urlgeeneral+"/proveedores/editar/" + id, function (data) {
            //console.log(data["Codigo"]);
            $("#valor").val(id);
            document.getElementById("ruc").value = data["ruc"];
            document.getElementById("nombre_comercial").value = data["nombre_comercial"];
            document.getElementById("telefono").value = data["telefono"];
            document.getElementById("direccion").value = data["direccion"];
            document.getElementById("email").value = data["email"];
            document.getElementById("web_sitie").value = data["web_sitie"];
            document.getElementById("ruc").value = data["ruc"];
            document.getElementById("ruc").value = data["ruc"];
            document.getElementById("contacto").value = data["contacto"];

            selecttipo(data["razon_social"]);
            console.log(data);



        });


      }





}

function eliminar(id){

    const tabla = document.getElementById('datatable');

    tabla.addEventListener('click', (e) => {
        if (e.target.classList.contains('eliminar') || e.target.classList.contains('bx')) {
            Swal.fire({
                title: '¿Desea Desactivar el Proveedor?',
                text: "No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, Anular!',
                cancelButtonText: 'Cancelar'
              }).then((result) => {
                if (result.isConfirmed) {
                //Metodo para eleminar
              var csrf = document.querySelector('meta[name="csrf-token"]').content;
                  $.ajax({
                    type: "POST",
                    url: "proveedores/eliminar/"+id,
                    data: {"_method": "delete",'_token': csrf},

                    success: function (data) {

                        todosproveedores();

                      Swal.fire(
                        'Eliminado!',
                        'Desactivado Correctamente',
                        'success'
                      )


                    }

                });





                }
              })
        }
    })


}

//ACTIVAR PROVEEDOR

function activar(id){

    const tabla = document.getElementById('datatable');

    tabla.addEventListener('click', (e) => {
        if (e.target.classList.contains('activar') || e.target.classList.contains('bx')) {
            Swal.fire({
                title: '¿Desea Activar el Proveedor?',
                text: "No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, Activar!',
                cancelButtonText: 'Cancelar'
              }).then((result) => {
                if (result.isConfirmed) {
                //Metodo para eleminar
              var csrf = document.querySelector('meta[name="csrf-token"]').content;
                  $.ajax({
                    type: "POST",
                    url: "proveedores/activar/"+id,
                    data: {"_method": "delete",'_token': csrf},

                    success: function (data) {

                        todosproveedores();

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

 //VALIDAR DATOS OBLIGARORIOS

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
                text: 'Los Campos Marcados con (*) son Obligatorios!',
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
//funcion para llenar el select tipo documento
function selecttipo(razon_social){

    if(razon_social==0){

        contenido="";
        contenido += "<option value='' >--Seleccionar--</option >";
        contenido += "<option value='Natutal' >Natutal</option >";
        contenido += "<option value='Juridica' >Juridica</option >";


       document.getElementById("razon_social").innerHTML=contenido;


    }else{

        contenido="";
        contenido += "<option value='' >--Seleccionar--</option >";
        if(razon_social=="Natutal"){

            contenido += "<option value='Natutal' selected>Natutal</option >";
            contenido += "<option value='Juridica' >Juridica</option >";

        }else if(razon_social=="Juridica"){

            contenido += "<option value='Natutal' >Natutal</option >";
            contenido += "<option value='Juridica' selected>Juridica</option >";

        }



       document.getElementById("razon_social").innerHTML=contenido;



    }




}
