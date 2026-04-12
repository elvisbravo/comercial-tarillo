const urlgeneral = document.getElementById('url_raiz_proyecto').value;
const btn_add = document.getElementById('btnAddTraslado');
const guia = document.getElementById('form_guia');
const crear_factura=document.getElementById('crear_factura');


window.addEventListener("load", function (event) {
    $(".loader").fadeOut("slow");
  });



  traer_ubigeo();
  traer_productos();
  traer_clientes();
  var cont=0;

  btn_add.addEventListener('click', () => {

    

        const cantidad = document.getElementById('cantidad_trasladar');
        const stock = document.getElementById('stock_p');
        const id_p = document.getElementById('search_producto');
        const text_p = id_p.options[id_p.selectedIndex].text;

       // alert(cantidad.value+' '+stock.value)

       

          
             
                let html = `
                <tr id="fila${cont}">
                    <input type="hidden" class="id_producto" name="id_producto[]" value="${id_p.value}" />
                    <input type="hidden" class="cantidadx" name="cantidad[]" value="${cantidad.value}" />
                    <td>${text_p}</td>
                    <td>UNIDADES</td>
                    <td>${cantidad.value}</td>
                    <td onclick="eliminar(${cont})">
                        <i class="bx bx-trash fs-4 align-middle text-danger" style="cursor: pointer"></i>
                    </td>
                </tr>
            `;

            cont++;

            $("#contentTraslado").append(html);


        

     
   
        cantidad.value = "";
        //stock.value = "";

        
    

   

});

function eliminar(index){


  $("#fila" +index).remove();
 
}


  function traer_ubigeo(){
    fetch(urlgeneral+"/traslado/traer_ubigeo/"+0)
    .then(res => res.json())
    .then(data => {
        const partida = document.querySelector('#ubigeo_partida');
        const ubigeo_partida = new Choices(partida, {
            placeholderValue: "This is a placeholder set in the config",
            searchPlaceholderValue: "Busque aquí el ubigeo",
            choices: data
        });

        const llegada = document.querySelector('#ubigeo_llegada');
        const ubigeo_llegada = new Choices(llegada, {
            placeholderValue: "This is a placeholder set in the config",
            searchPlaceholderValue: "Busque aquí el ubigeo",
            choices: data
        });
    })
}

//FUNCION PARA CARGAR LOS PRODUCTOS
function traer_productos() {

  fetch(urlgeneral+"/traslado/traer_productos")
  .then(res => res.json())
  .then(data => {
      const prod = document.querySelector("#search_producto");
      const productos = new Choices(prod, {
          placeholderValue: "This is a placeholder set in the config",
          searchPlaceholderValue: "Busque aquí el producto",
          choices: data
      });

      prod.addEventListener(
          'addItem',
          function(event) {
              //console.log(event.detail.value);
              //render_producto(event.detail.value)
          }
      );
  })
}

//metodo para traer los clientes
function traer_clientes(){
  fetch(urlgeneral+"/traslado/traer_clientes")
  .then(res => res.json())
  .then(data => {
      const llegada = document.querySelector('#change_cliente');
      const ubigeo_llegada = new Choices(llegada, {
          placeholderValue: "This is a placeholder set in the config",
          searchPlaceholderValue: "Busque aquí el cliente",
          choices: data
      });
  })
}

//metodo para treaer los datos de los conductores
const buscarconductores=async(document)=>{

  const url=urlgeneral+'/traslado/lista_conductor/'+document;

      fetch(url)
      .then(response => response.json())
      .then(data => {
      // aquí puedes trabajar con los datos recibidos
      console.log(data);
      $("#nombre_conductor").val(data.nombre);
      $("#conductor_id").val(data.id);

      })
      .catch(error => {
      // manejo del error
      });


}

buscarconductor.addEventListener('click',function(){

  let documento=$("#numero_conductor").val();
  buscarconductores(documento);
});


todo=[];

guia.addEventListener('submit', (e) => {
  e.preventDefault();

  if (datosobligatorio() == true) {


   guia.disabled = false;

  const csrf = document.querySelector('meta[name="csrf-token"]').content;
  //DATOS DEL TIPO DE TRASLADO
  let selectdoc_traslado=document.getElementById("tipo_traslado_id");
  let tipo_traslado=selectdoc_traslado.options[selectdoc_traslado.selectedIndex].value;
  //NUMERO DE LA GUIA
  let numero_guia=$("#numero_guia").val();
  //fecha de emición de la guia
  let fecha_emision=$("#fecha_emision").val();
  //DATOS DEL TIPO DE COMPROBANTE
  let selecttipo_documento_id=document.getElementById("tipo_documento_id");
  let tipo_documento_id=selecttipo_documento_id.options[selecttipo_documento_id.selectedIndex].value;

  //NUMERO DEL COMPROBANTE
  let numero_referencia=$("#numero_referencia").val();

  //DATOS DE LOS PROVEEDORES
  let selecttipoproveedor_id=document.getElementById("proveedor_id");
  let proveedor_id=selecttipoproveedor_id.options[selecttipoproveedor_id.selectedIndex].value;

   //DATOS DEL CLIENTE
   let selectcliente_id=document.getElementById("change_cliente");
   let cliente_id=selectcliente_id.options[selectcliente_id.selectedIndex].value;
   
   //motivo de translado
   let selectmotivo=document.getElementById("motivo");
   let motivo=selectmotivo.options[selectmotivo.selectedIndex].value;
   //modalidad de traslado
   let selectmodalidad=document.getElementById("modalidad_traslado");
   let modalidad_traslado=selectmodalidad.options[selectmodalidad.selectedIndex].value;

    //fecha de translado
    let fecha=$("#fecha_traslado").val();
    //peso bruto
    let peso_bruto=$("#peso_bruto").val();
    //bultos
    let bultos=$("#bultos").val();

     //TREAR LA DATA EL ID DEL CONDUTOR
   let conductor_id=$("#conductor_id").val();

      //DATOS DEL VEHICULO
   let selectvehiculo_id=document.getElementById("vehiculo_id");
   let vehiculo_id=selectvehiculo_id.options[selectvehiculo_id.selectedIndex].value;

    //DIRECCIONES punto de partida
    let direccion_partida=$("#direccion_partida").val();
    let selectubigeo_partida=document.getElementById("ubigeo_partida");
    let ubigeo_partida=selectubigeo_partida.options[selectubigeo_partida.selectedIndex].value;

    //LAS SEDE
   

    if(ubigeo_partida==="Seleccione"){

        alert("El ubigeo de Partida es obligatorio");
        return false;
    }
    //DIRECCIONES PUNTO DE LLEGADA
    let direccion_llegada=$("#direccion_llegada").val();
    let selectubigeo_llegada=document.getElementById("ubigeo_llegada");
    let ubigeo_llegada=selectubigeo_llegada.options[selectubigeo_llegada.selectedIndex].value;

    if(ubigeo_llegada==="Seleccione"){

        alert("El ubigeo de Llegada es obligatorio");
        return false;
    }

     //almacenes 
     let selectalmacen_origen=document.getElementById("almacen_origen");
     let almacen_origen=selectalmacen_origen.options[selectalmacen_origen.selectedIndex].value;
 
     if(almacen_origen==""){
 
         alert("El Almacen de Origin obligatorio");
         return false;
     }

     //Agregar el transportista
     let selecttransporte_id=document.getElementById("transporte_id");
     let transporte_id=selecttransporte_id.options[selecttransporte_id.selectedIndex].value;
 
     if(almacen_origen==""){
 
         alert("El Almacen de Origin obligatorio");
         return false;
     }


      //CODIGO DEL DOCUMENTO ELECTRONICO
      
      let cabeza=new cabezeratranslado(tipo_traslado,numero_guia,fecha_emision,tipo_documento_id,numero_referencia,proveedor_id,cliente_id,motivo,modalidad_traslado,fecha,peso_bruto,bultos,conductor_id,vehiculo_id,direccion_partida,ubigeo_partida,direccion_llegada,ubigeo_llegada,almacen_origen,transporte_id);
      todo.push(cabeza);
     
      //AGREGAR EL DETALLE DE LOS PRODUCTOS

    const producto_id=document.getElementsByClassName('id_producto');
    const cantidad=document.getElementsByClassName('cantidadx');


    for (let i = 0; i < producto_id.length; i++) {

      let detalle=new detalletraslado(producto_id[i].value,cantidad[i].value);
      todo.push(detalle);
  }

    console.log(todo);

    showSpinner();

    $.ajax({
      type : "POST",
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      contentType:"application/json",
      dataType:'json',
      data:JSON.stringify(todo),
      processData:false,
      cache:false,
      url :urlgeneral+ "/guias/guardar",
      success : function (result) {

          console.log(result);
          hideSpinner();

          if(result.respuesta=="error"){

              Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: result.mensaje,
                  footer: 'Es muy importante que agregue datos al detalle de la Guia'
                });

                todo=[];

          }else{

            $("#codigo_guia_id").val(result);

            crear_factura.disabled = false;
            guia.disabled = false;

              Swal.fire({
                  icon: 'success',
                  title: 'Oops...',
                  text: result.mensaje,
                  footer: ''
              })

          }

        

          //location.href =urlgeneral+"/traslados";


     },



      error : function(xhr,errmsg,err) {

          Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'los Campos marcados con (*) son obligatorios!',
              footer: 'Por favor recargue la página para poder empezar de nuevo'
            })

                      console.log(xhr.status + ": " + xhr.responseText);
                      }

      });


    }


});



//FUNCION PARA PODER CREAR TODA LA FACTURA

$("#crear_factura").on("click",function(){

  alert('hola');

});

function cabezeratranslado(tipo_traslado,numero_guia,fecha_emision,tipo_documento_id,numero_referencia,proveedor_id,cliente_id,motivo,modalidad_traslado,fecha,peso_bruto,bultos,conductor_id,vehiculo_id,direccion_partida,ubigeo_partida,direccion_llegada,ubigeo_llegada,almacen_origen,transporte_id){

  this.tipo_traslado=tipo_traslado;
  this.numero_guia=numero_guia;
  this.fecha_emision=fecha_emision;
  this.tipo_documento_id=tipo_documento_id;
  this.numero_referencia=numero_referencia;
  this.proveedor_id=proveedor_id;
  this.cliente_id=cliente_id;
  this.motivo=motivo;
  this.modalidad_traslado=modalidad_traslado;
  this.fecha=fecha;
  this.peso_bruto=peso_bruto;
  this.bultos=bultos;
  this.conductor_id=conductor_id;
  this.vehiculo_id=vehiculo_id;
  this.direccion_partida=direccion_partida;
  this.ubigeo_partida=ubigeo_partida;
  this.direccion_llegada=direccion_llegada;
  this.ubigeo_llegada=ubigeo_llegada;
  this.almacen_origen=almacen_origen;
  this.transporte_id=transporte_id;


}















//DETALLE DEL TRANSLADO
function detalletraslado(producto_id,cantidad){

  this.producto_id=producto_id;
  this.cantidad=cantidad;

}




//METDO PARA VALIDAR SI LOS DATOS SON OBLIGATORIOS

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
              text: 'Todos los campos Marcados con (*) son Obligatorios !',
              footer: 'Y Es muy importante que agregue datos al detalle de compras'
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



 //validar los impust que sean obligatorios

 function showSpinner() {
  $('.loader').show();
  $('#spinner').show();
}

function hideSpinner() {
  $('.loader').hide();
  $('#spinner').hide();
}
