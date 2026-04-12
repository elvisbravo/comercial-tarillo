const urlgeneral = document.getElementById('url_raiz_proyecto').value;


window.addEventListener("load", function (event) {

 
    $(".loader").fadeOut("slow");
    $("#dataTableExample").DataTable();
    var id=$("#id_guia").val();
    detalle(id);
  
  });

  //FUNCION PARA PODER CARGAR LOS DATOS DEL PRODUCTO

  const cargarDetalleProdutos=async(codigo,id_guia)=>{
      
    try {
      const respuesta = await fetch(urlgeneral+'/articulo_demandado/'+codigo+"/"+id_guia);
      const datos = await respuesta.json();

      return datos;
    } catch (error) {
      console.error(error);
    }


  }

  function abrimodal(id){

    let id_guia=$("#id_guia").val();

    showSpinner();
    $("#cantidad_recibido").val("");
    cargarDetalleProdutos(id,id_guia).then(datos=>{
      
      hideSpinner();
      let selectubicacion=document.getElementById("ubicaciones_id");
      let ubicacion_id=selectubicacion.options[selectubicacion.selectedIndex].value;
      $("#ubicaciontable").text(selectubicacion.options[selectubicacion.selectedIndex].text);
         //console.log(datos);
         $("#valor").val(datos.id);
         $("#id_producto").val(id);
         $("#product_name").text(datos.nomb_pro);
         $("#cantidad_demanda").text(datos.cantidad);
         $("#cantidad_hecha").text(datos.cantidad_recibido);
         $("#cantidad_recibido_echa").val(datos.cantidad_recibido);
         $("#datafierencia").text(datos.diferencia);

    });

      
  }

  
   //CARGAR EL DETALLE DA LA DATA
   const CargarDetalle=async(id)=>{

    try {
      const respuesta = await fetch(urlgeneral+'/detalle/'+id);
      const datos = await respuesta.json();

      return datos;

    } catch (error) {
      console.error(error);
    }


  }

  function detalle(id){

    CargarDetalle(id).then(datos=>{

          let contenido='';
          var tabla = $("#dataTableExample").DataTable();

          for (let i = 0; i < datos.length; i++) {

                var botones='<button class="btn btn-primary" onclick="abrimodal('+datos[i].producto_id+')" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fa fa-bars" aria-hidden="true"></i></button>';

                tabla.row.add([datos[i].producto_id,
                  datos[i].nomb_pro,
                  datos[i].descripcion,
                  datos[i].cantidad,
                  datos[i].cantidad_recibido,
                  botones
                
                ]).draw(false);
            
          }
    });


  }

 
 
  //METODO PARA PODER GUARDAR LA DATA
 
  $("#guardar").on("click",function(){

    var frm = new FormData();
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    let id=$("#valor").val();
    let cantidad_demanda=$("#cantidad_demanda").text();
    let selectubicacion=document.getElementById("ubicaciones_id");
    let ubicacion_id=selectubicacion.options[selectubicacion.selectedIndex].value;
    let cantidad_recibido=$("#cantidad_recibido").val();
    let id_almacen_origen=$("#id_almacen_origen").val();
    let id_producto= $("#id_producto").val();
    let almacenorigen=$("#almacenorigen").val();
    let almacendestino=$("#almacendestino").val();
    let serie=$("#serie").val();
    let correlativo=$("#correlativo").val();
    let tipo_traslado_id=$("#tipo_traslado_id").val();
    let fecha=$("#fecha").val();
    
    frm.append("id", id);
    frm.append("cantidad_demanda", cantidad_demanda);
    frm.append("ubicacion_id", ubicacion_id);
    frm.append("cantidad_recibido",cantidad_recibido);
    frm.append("id_almacen_origen",id_almacen_origen);
    frm.append("id_producto",id_producto);
    frm.append("almacenorigen",almacenorigen);
    frm.append("almacendestino",almacendestino);
    frm.append("serie",serie);
    frm.append("correlativo",correlativo);
    frm.append("tipo_traslado_id",tipo_traslado_id);
    frm.append("fecha",fecha);
    frm.append("_token", csrf);

    
  
    showSpinner() ;

    $.ajax({
      type: "POST",
      url: urlgeneral+"/recepcion-mercaderia/crear",
      data: frm,
      dataType: 'json',
      contentType: false,
      processData: false,
      success: function (data) {

         hideSpinner();
         console.log(data);
 
        
          if(data.respuesta=='error'){

            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: data.mensaje,
              footer: ''
            })

          }else if(data.respuesta=="ok"){

            $('#staticBackdrop').modal('hide');

            var tabla = $("#dataTableExample").DataTable();
            tabla.clear().draw();

               detalle(data.mensaje);
               console.log(data.mensaje);

  

      
          }


         }
    });



  });


  //METODO VALIDAR Y GUARDAR LA INFORMACIÓN
  $("#todo").on('click',function(){

       let id_guia=$("#id_guia").val();
       let selectubicaciones_id=document.getElementById('ubicaciones_id');
       let ubicaciones_id=selectubicaciones_id.options[selectubicaciones_id.selectedIndex].value;

       if(ubicaciones_id==""){

        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Es muy importante que seleccione el almacen Destino!',
          footer: ''
        })

        return;
       }

       


       showSpinner();
        $.get(urlgeneral+"/guardar/"+id_guia+'/'+ubicaciones_id,function(data){
          hideSpinner()
                    if(data=='OK'){

                      location.href =urlgeneral+"/recepcion-mercaderia";

                    }
        });
  })


  function showSpinner() {
    $('.loader').show();
    $('#spinner').show();
  }
  
  function hideSpinner() {
    $('.loader').hide();
    $('#spinner').hide();
  }
  