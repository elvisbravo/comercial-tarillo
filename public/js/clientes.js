urlgeneral=$("#url_raiz_proyecto").val();

const tipo_documento = document.getElementById('documento_identidad');
const numero_documento = document.getElementById('numero_documento');
const btn_consultar = document.getElementById('btn_consultar');

window.addEventListener("load", function (event) {

    //listadocategorias();
    $(".loader").fadeOut("slow");
     $("#datatable").dataTable();



  });


function anular(id){

   // alert(id);
    const tabla = document.getElementById('datatable');

    tabla.addEventListener('click', (e) => {
      if (e.target.classList.contains('eliminar') || e.target.classList.contains('bx')) {
          Swal.fire({
              title: '¿Desea anular el Cliente?',
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
                  url: "clientes/eliminar/"+id,
                  data: {"_method": "delete",'_token': csrf},

                  success: function (data) {

                    location.href =urlgeneral+"/clientes";

                    Swal.fire(
                      'Anulado!',
                      'El cliente fue Anulado Correctamente.',
                      'success'
                    )


                  }

              });





              }
            })
      }
  })


}

function activar(id){


     const tabla = document.getElementById('datatable');

     tabla.addEventListener('click', (e) => {
       if (e.target.classList.contains('activar') || e.target.classList.contains('bx')) {
           Swal.fire({
               title: '¿Desea Activar el Cliente?',
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
                   url: "clientes/activar/"+id,
                   data: {"_method": "delete",'_token': csrf},

                   success: function (data) {

                     location.href =urlgeneral+"/clientes";

                     Swal.fire(
                       'Activado!',
                       'El cliente fue Activado Correctamente.',
                       'success'
                     )


                   }

               });





               }
             })
       }
   })


 }
