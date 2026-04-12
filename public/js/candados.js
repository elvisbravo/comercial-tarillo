
  urlgeeneral=$("#url_raiz_proyecto").val();
  $("#actualizar").hide();

  //FUNCION LOAD
window.addEventListener("load", function (event) {

     listarcolores();
     $(".loader").fadeOut("slow");
  });


  function listarcolores(){
    $.get(urlgeeneral+"/candados-creditos/listacandados", function (data) {
      //console.log(data);
        llenarcolores(data);
    });
}

function llenarcolores(data){

  let contenido="";
  for (var i = 0; i < data.length; i++) {
    contenido += "<tr>";
    contenido += "<td style='padding:1px;text-align:center'>" +  parseInt(i+1,10) + "</td>";
    contenido += "<td style='padding:1px;text-align:center'> " + data[i].rango_minimo + "</td>";
    contenido += "<td style='padding:1px;text-align:center'> " + data[i].rango_maximo + "</td>";
    contenido += "<td style='padding:1px;text-align:center'> " + data[i].monto_inicial + "</td>";
    contenido += "<td style='padding:1px;text-align:center'> " + data[i].nmeses + "</td>";
    contenido += "<td style='padding:1px;text-align:center'>";
    //contenido +='<i class="fas fa-edit"></i>';
    contenido +=' <button type="button" onclick="abrimodal('+ data[i].id +')" class="btn btn-info " data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fas fa-edit"></i></button>'
    contenido +='<button type="button" onclick="eliminarsector('+ data[i].id +')" class="btn btn-danger  eliminar"><i class="fas fa-trash-alt"></i></button>'
    contenido +="</td>";
    contenido += "</tr>";


  }

  document.getElementById("listadecolores").innerHTML = contenido;
  $("#datatable").dataTable();


}



  $("#guardar").on("click",function(){

    if (datosobligatorio() == true) {

        var frm = new FormData();
        var csrf = document.querySelector('meta[name="csrf-token"]').content;
        var rango_minimo=$("#rango_minimo").val();
        var rango_maximo=$("#rango_maximo").val();
        var monto_inicial=$("#monto_inicial").val();
        var nmeses=$("#nmeses").val();


        frm.append("rango_minimo", rango_minimo);
        frm.append("rango_maximo", rango_maximo);
        frm.append("monto_inicial", monto_inicial);
        frm.append("nmeses", nmeses);
        frm.append("_token", csrf);


        $.ajax({
            type: "POST",
            url: urlgeeneral+"/candados-creditos/crear",
            data: frm,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function (data) {


                    Swal.fire({
                        icon: 'success',
                        title: 'Ok...',
                        text: 'Creado Correctamente',
                        footer: ''
                    })

                        listarcolores();
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

    $.get(urlgeeneral+"/candados-creditos/edit/" + id, function (data) {
      //console.log(data["Codigo"]);
      $("#valor").val(id);
      document.getElementById("rango_minimo").value = data["rango_minimo"];
      document.getElementById("rango_maximo").value = data["rango_maximo"];
      document.getElementById("monto_inicial").value = data["monto_inicial"];
      document.getElementById("nmeses").value = data["nmeses"];


  });
  }


}

//METODO PARA MODIFICAR EL color:

$("#actualizar").on("click",function(){

  if (datosobligatorio() == true) {

            var frm = new FormData();
            var csrf = document.querySelector('meta[name="csrf-token"]').content;
            var id=$("#valor").val();
            var rango_minimo=$("#rango_minimo").val();
            var rango_maximo=$("#rango_maximo").val();
            var monto_inicial=$("#monto_inicial").val();
            var nmeses=$("#nmeses").val();

            frm.append("id", id);
            frm.append("rango_minimo", rango_minimo);
            frm.append("rango_maximo", rango_maximo);
            frm.append("monto_inicial", monto_inicial);
            frm.append("nmeses", nmeses);
            frm.append("_token", csrf);



            $.ajax({
              type: "POST",
              url: urlgeeneral+"/candados-creditos/modificar",
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

                listarcolores();
                $('#staticBackdrop').modal('hide');
                //console.log(data);

                }
            });




  }

});



//metodo para anular el color:


function eliminarsector(id){

  const tabla = document.getElementById('datatable');

  tabla.addEventListener('click', (e) => {
    if (e.target.classList.contains('eliminar') || e.target.classList.contains('bx')) {
        Swal.fire({
            title: '¿Desea eliminar el color?',
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
                url: urlgeeneral+"/candados-creditos/eliminar/"+id,
                data: {"_method": "delete",'_token': csrf},

                success: function (data) {

                  listarcolores();

                  Swal.fire(
                    'Eliminado!',
                    'Ha sido eliminado.',
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
          //alert("vacios");
          //obligarotio[i].parentNode.classList.add("form-control error");
          //swal("Here's a message!")
          //swal("Error!", "Los Campos Son Obligatorios!", "error")
          Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Ambos Campos son obligatorios!',
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
