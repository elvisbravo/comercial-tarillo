urlgeeneral=$("#url_raiz_proyecto").val();
const btn_consultar = document.getElementById('btn_consultar');
const tipo_documento = document.getElementById('documento_identidad');
const numero_documento = document.getElementById('ruc');

$("#actualizar").hide();

//FUNCION LOAD
window.addEventListener("load", function (event) {

    todostransportistas();
  $(".loader").fadeOut("slow"); 
  selecttipo(0);

});

function todostransportistas(){
    $.get(urlgeeneral+"/transportistas/listatransportistas",function(data){

        llenardatos(data);
   });
}

//mostrar los transportistas en la vista

function llenardatos(data){
    let contenido="";
    for (var i = 0; i < data.length; i++) {
      contenido += "<tr>";
      contenido += "<td style='padding:1px;text-align:center'>" +  parseInt(i+1,10) + "</td>";
      contenido += "<td style='padding:1px;text-align:center'> " + data[i].razon_social + "</td>";
      contenido += "<td style='padding:1px;text-align:center'> " + data[i].ruc + "</td>";
      contenido += "<td style='padding:1px;text-align:center'> " + data[i].nombre_comercial + "</td>";
      contenido += "<td style='padding:1px;text-align:center'> " + data[i].telefono + "</td>";
      contenido += "<td style='padding:1px;text-align:center'> " + data[i].direccion + "</td>";
     /*  if(data[i].estado==0){
        contenido += "<td style='padding:1px;text-align:center'> <i class='fas fa-sync'></i> Inactivo</td>";

      }else{

        contenido += "<td style='padding:1px;text-align:center'> Activo</td>";

      } */
      contenido += "<td style='padding:1px;text-align:center'>";
      //contenido +='<i class="fas fa-edit"></i>';
     
      contenido +=' <button type="button" onclick="abrimodal('+ data[i].id +')" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fas fa-edit"></i> </button>'
      contenido +='<button type="button" onclick="eliminar('+ data[i].id +')" class="btn btn-danger "><i class="fas fa-trash-alt eliminar"></i> </button>';


      contenido +="</td>";
      contenido += "</tr>";

    }
    document.getElementById("listadotransportitas").innerHTML = contenido;
    $("#datatable").dataTable();
}

//funcion para llenar el select tipo documento
function selecttipo(razon_social){

    if(razon_social==0){

        contenido="";
        contenido += "<option value='' >--Seleccionar--</option >";
        contenido += "<option value='Natural' >Natural</option >";
        contenido += "<option value='Juridica' >Juridica</option >";


       document.getElementById("razon_social").innerHTML=contenido;


    }else{

        contenido="";
        contenido += "<option value='' >--Seleccionar--</option >";
        if(razon_social=="Natural"){

            contenido += "<option value='Natural' selected>Narutal</option >";
            contenido += "<option value='Juridica' >Juridica</option >";

        }else if(razon_social=="Juridica"){

            contenido += "<option value='Natural' >Natural</option >";
            contenido += "<option value='Juridica' selected>Juridica</option >";

        }



       document.getElementById("razon_social").innerHTML=contenido;



    }




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
        var selectdocumento_identidad=document.getElementById("documento_identidad");
        var documento_identidad=selectdocumento_identidad.options[selectdocumento_identidad.selectedIndex].value;


        frm.append("ruc", ruc);
        frm.append("razon_social", razon_social);
        frm.append("nombre_comercial", nombre_comercial);
        frm.append("telefono", telefono);
        frm.append("direccion", direccion);
        frm.append("documento_identidad", documento_identidad);
        frm.append("_token", csrf);

        $.ajax({
            type: "POST",
            url: urlgeeneral+"/transportistas/crear",
            data: frm,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function (data) {

                //console.log(data);

                 todostransportistas();

                Swal.fire({
                    icon: 'success',
                    title: 'Oops...',
                    text: 'Creado Correctamente',
                    footer: ''
                  })

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

          $.get(urlgeeneral+"/transportistas/editar/" + id, function (data) {
            //console.log(data["Codigo"]);
            $("#valor").val(id);
            document.getElementById("ruc").value = data["ruc"];
            document.getElementById("nombre_comercial").value = data["nombre_comercial"];
            document.getElementById("telefono").value = data["telefono"];
            document.getElementById("direccion").value = data["direccion"];
            selecttipo(data["razon_social"]);
           // console.log(data);



        });


      }





}

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
        var id=$("#valor").val();


        frm.append("ruc", ruc);
        frm.append("razon_social", razon_social);
        frm.append("nombre_comercial", nombre_comercial);
        frm.append("telefono", telefono);
        frm.append("direccion", direccion);
        frm.append("id", id);
        frm.append("_token", csrf);

        $.ajax({
            type: "POST",
            url: urlgeeneral+"/transportistas/modificar",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: frm,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function (data) {

                todostransportistas();
                Swal.fire({
                    icon: 'success',
                    title: 'Oops...',
                    text: 'Transportista Modificado Correctamente',
                    footer: ''
                })

                $('#staticBackdrop').modal('hide');


            }
        });




    }


});

function eliminar(id){

    const tabla = document.getElementById('datatable');

    tabla.addEventListener('click', (e) => {
        if (e.target.classList.contains('eliminar') || e.target.classList.contains('bx')) {
            Swal.fire({
                title: '¿Desea Desactivar el Transportistas?',
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
                    url: "transportistas/eliminar/"+id,
                    data: {"_method": "delete",'_token': csrf},

                    success: function (data) {

                        todostransportistas();

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