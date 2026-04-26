const urlgeneral = document.getElementById('url_raiz_proyecto').value;



window.addEventListener("load", function (event) {
    $(".loader").fadeOut("slow");
    lista();
});


  const CargarGuias=async()=>{
      
    try {

        const respuesta = await fetch(urlgeneral+'/traslado/listadoguias');
        const datos = await respuesta.json();

        return datos;
      } catch (error) {
        console.error(error);
      }

  }

  //cargar los datos a la tabla
  function lista() {
      CargarGuias().then(datos => {
          if ($.fn.DataTable.isDataTable('#dataTableExample')) {
              $('#dataTableExample').DataTable().destroy();
          }

          const estados = {
              0: '<div class="badge badge-soft-success font-size-12">GUIA ATENDIDO</div>',
              1: '<div class="badge badge-soft-danger font-size-12">PENDIENTE DE ACEPTACIÓN</div>',
              2: '<div class="badge badge-soft-warning font-size-12">ATENDIDO PARCIAL</div>',
              3: '<div class="badge badge-soft-danger font-size-12">ANULADO</div>'
          };

          let contenido = "";

          for (var i = 0; i < datos.length; i++) {
              var botones = "<div class='dropdown'>" +
                  "<button class='btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle' type='button' data-bs-toggle='dropdown' aria-expanded='false'>" +
                  "<i class='bx bx-list-ul'></i>" +
                  "</button>" +
                  "<ul class='dropdown-menu dropdown-menu-end' style=''>" +
                  "<li><a class='dropdown-item' href='/traslados/show/" + datos[i].id + "' id='detalle-" + datos[i].id + "'>Detalle</a></li>" +
                  "<li><a class='dropdown-item' href='/traslado/generar_guia/" + datos[i].id + "' target='_blank'>PDF</a></li>";

              if (typeof canDelete !== 'undefined' && canDelete) {
                  botones += "<li><a class='dropdown-item eliminar' type='button'  href='#' onclick='eliminarsector(" + datos[i].id + ")'>Anular Guia</a></li>";
              }

              botones += "</ul></div>";

              const estdo_guia = estados[datos[i].estado] || 'ESTADO DESCONOCIDO';
              var documento = datos[i].serie + '-' + datos[i].correlativo;

              contenido += "<tr>";
              contenido += "<td>" + (i + 1) + "</td>";
              contenido += "<td>" + datos[i].fecha + "</td>";
              contenido += "<td>" + documento + "</td>";
              contenido += "<td>" + datos[i].razon_social + "</td>";
              contenido += "<td>" + datos[i].motivo + "</td>";
              contenido += "<td>" + estdo_guia + "</td>";
              contenido += "<td>" + botones + "</td>";
              contenido += "</tr>";
          }

          document.getElementById("traslados").innerHTML = contenido;
          initDataTable("#dataTableExample");
      });
  }

//ANULAR LA GUIA

function eliminarsector(id) {
    Swal.fire({
        title: '¿Desea anular la Guía?',
        text: "No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, anular!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            var csrf = document.querySelector('meta[name="csrf-token"]').content;
            $.ajax({
                type: "POST",
                url: "traslados/eliminar/" + id,
                data: { "_method": "delete", '_token': csrf },
                success: function (data) {
                    if (data.respuesta == 'ok') {
                        lista();
                        Swal.fire(
                            'Anulado!',
                            '' + data.mensaje,
                            'success'
                        )
                    } else {
                        lista();
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: '' + data.mensaje,
                            footer: ''
                        });
                    }
                }
            });
        }
    })
}
