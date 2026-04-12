const urlgeneral = document.getElementById('url_raiz_proyecto').value;


window.addEventListener("load", function (event) {

 
    $(".loader").fadeOut("slow");
    $("#dataTableExample").DataTable();
    lista();
 
  });


  const CargarGuias=async()=>{
      
    try {
        const respuesta = await fetch(urlgeneral+'/recepcion/listadoguiasrecepcion');
        const datos = await respuesta.json();

        return datos;
      } catch (error) {
        console.error(error);
      }

  }

   //cargar los datos a la tabla
   function lista(){

    CargarGuias().then(datos => {
           
        var tabla = $("#dataTableExample").DataTable();
        for (var i = 0; i < datos.length; i++) {

            var botones = "<div class='dropdown'>" +
            "<button class='btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle' type='button' data-bs-toggle='dropdown' aria-expanded='false'>" +
            "<i class='bx bx-list-ul'></i>" +
            "</button>" +
            "<ul class='dropdown-menu dropdown-menu-end' style=''>" +
            "<li><a class='dropdown-item' href='/recepcion-mercaderia/" + datos[i].id + "' id='detalle-" + datos[i].id + "' target='_blank'>Detalle</a></li>" +
            "<li><a class='dropdown-item' href='/traslado/generar_guia/" +  datos[i].id+ "' target='_blank'>PDF</a></li>" +
            "<li><a class='dropdown-item' href='#' id='enviar-" + datos[i].id + "'>Anular Guia</a></li>" +
            //"<li><a class='dropdown-item' href='#' id='descargar-" +  datos[i].id + "'>Descargar XML y PDF</a></li>" +
            "</ul>" +
            "</div>";

            var estado='';
            if(datos[i].estado==0){
                estado='<div class="badge badge-soft-success font-size-12">GUIA RECEPCIONADA</div>';
            }else if(datos[i].estado==1){
                estado='<div class="badge badge-soft-danger font-size-12">PENDIENTE DE RECEPCION</div>';

            }else if(datos[i].estado==2){

                estado='<div class="badge badge-soft-warning font-size-12">ATENDIDO PARCIAL</div>'
            }else{

                estado='<div class="badge badge-soft-dark font-size-12">ANULADO</div>'

            }
            var documento= datos[i].serie+'-'+datos[i].correlativo;


            tabla.row.add([i+1,
                datos[i].fecha,
                documento,
                datos[i].razon_social,
                datos[i].motivo,
                estado,
                botones
              
              ]).draw(false);


        }
          
    });

  }
