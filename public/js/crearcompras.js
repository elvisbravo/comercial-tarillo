urlgeeneral=$("#url_raiz_proyecto").val();
$("#actualizar").hide();


window.addEventListener("load", function (event) {


   $(".loader").fadeOut("slow");

   var m = moment().format("YYYY-MM-DD");
   $("#fecha_compra").val(m);
   selecttipo(0);
   todosproveedores();
   todasunidades();
   listaproductos();




 });
 ///==================crear proveedores=================
 function todosproveedores(){
 $.get(urlgeeneral+"/proveedores/listadomarca",function(data){

        let contenido="";
        contenido += "<option value='' >--Seleccionar--</option >";
        for (var i = 0; i < data.length; i++) {


          contenido += "<option value='"+data[i].id+"' >"+ data[i].nombre_comercial+"</option >";

        }

        document.getElementById("proveedor_id").innerHTML=contenido;

      });
 }


 function selecttipo(razon_social){

        if(razon_social==0){

          contenido="";
          contenido += "<option value='' >--Seleccionar--</option >";
          contenido += "<option value='Natutal' >Natutal</option >";
          contenido += "<option value='Juridica' >Juridica</option >";


        document.getElementById("razon_social").innerHTML=contenido;


      }

 }
 function abrimodal(id){


  if(id == "0"){

      limpiarcajasunidas();

      $("#guardar").show();
      $("#actualizar").hide();

    }

}

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


      frm.append("ruc", ruc);
      frm.append("razon_social", razon_social);
      frm.append("nombre_comercial", nombre_comercial);
      frm.append("telefono", telefono);
      frm.append("direccion", direccion);
      frm.append("email", email);
      frm.append("web_sitie", web_sitie);
      frm.append("contacto", contacto);
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
                  title: 'Oops...',
                  text: 'Creado Correctamente',
                  footer: ''
                })

                $('#staticBackdrop').modal('hide');

            }
        });




  }


});

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

  for (var i = 0; i < ncontroles; i++) {
      controles[i].value = "";
  }

}

//=================================================FUNCIONES PARA LOS INPUT CHECK======================

$("#igv").on("change",function(){



  if ($(this).is(':checked')) {

        console.log($(this).val() + ' is now checked');

        $("#igv").val('18.00');
        
        const subtotal_input = $("#subtotal_input").val();
        const igvtodo=document.getElementById('igvtodo');

        const totales = document.getElementById('total_compratemporal');
        //const total_input = document.getElementById('total_compra');





        let totaligv=0;
        let total_importe=0;
        totaligv=parseFloat(subtotal_input*0.18);
        total_importe=parseFloat(subtotal_input)+parseFloat(totaligv);
        igvtodo.textContent = totaligv.toFixed(2);


        console.log(total_importe);

        totales.textContent = total_importe.toFixed(2);
        $("#total_compra").val(total_importe.toFixed(2));








    } else {

        console.log($(this).val() + ' is now unchecked');
         $("#igv").val('0.00');

         const subtotal_input = $("#subtotal_input").val();

         $("#igvtodo").text("0.00");

         $("#total_compratemporal").text(subtotal_input);
        $("#total_compra").val(subtotal_input);

    }

    //alert("hola");


});

$("#persecciontem").on("change",function(){



  if ($(this).is(':checked')) {
        console.log($(this).val() + ' is now checked');

      document.getElementById("perseccion").disabled=false;

    } else {
        console.log($(this).val() + ' is now unchecked');
        document.getElementById("perseccion").disabled=true;

    }

    //alert("hola");


});

$("#icbpertem").on("change",function(){



  if ($(this).is(':checked')) {
        console.log($(this).val() + ' is now checked');

      document.getElementById("icbper").disabled=false;

    } else {
        console.log($(this).val() + ' is now unchecked');
        document.getElementById("icbper").disabled=true;

    }

    //alert("hola");


});
//===========================MOSTRAR LOS PRODUCTOS =======================================
function listaproductos(){

  $.get(urlgeeneral+"/productos/listaproduct",function(data){

    var contenido = "";

    for (var i = 0; i < data.length; i++) {
 

      contenido +='<div class="col-lg-4 col-xs-6" >';
      contenido +='<div class="card" style="">';
      contenido +=' <a  href="#" class="" onclick="modal(\''+data[i].id+'\')">';
     if(data[i].img==null){
        contenido +=' <img style="width: 100px;" src="'+urlgeeneral+'/img/productos/product.png" class="card-img-top" alt="..." >';
      }else{

       contenido +=' <img style="width: 100px;" src="'+urlgeeneral+'/img/productos/'+data[i].img+'" class="card-img-top" alt="..." >';
     }
      
      contenido +='<div class="card-body">';
      contenido +='<p class="card-text" style="font-size:12px; text-align: center;">'+data[i].nomb_pro+" / "+data[i].categoria+" / "+data[i].subcategoria+" / "+data[i].marca+" / "+data[i].color+'  <input id="product'+data[i].id+'" type="hidden" value="'+data[i].nomb_pro+'"></p>';
      contenido +='<p class="card-text" style="text-align: center;">S/' +data[i].prec_compra+ ' <input id="costo'+data[i].id+'" type="hidden" value="'+data[i].prec_compra+'"></p>';
      contenido +='</div>';
      contenido +=' </a>';
      contenido +='</div>';
      contenido +='</div>';

    }

    document.getElementById("inyecciondos").innerHTML = contenido;


});

}


//METODO PARA BUSCAR LOS PRODUCTOS EN EL BUSCADOR
function buscar(){

     document.getElementById("inyecciondos").innerHTML = "";

     var searctxt=$("#navbarForm").val().toUpperCase();

     if(searctxt==""){

         listaproductos(); 
         
     }else{

            $.get(urlgeeneral+"/productos/searchproduct/"+searctxt,function(data){

              var contenido = "";
          
              for (var i = 0; i < data.length; i++) {
          
          
                contenido +='<div class="col-lg-4 col-xs-6" >';
                contenido +='<div class="card" style="">';
                contenido +=' <a  href="#" class="" onclick="modal(\''+data[i].id+'\')">';
              if(data[i].img==null){
                  contenido +=' <img style="width: 100px;" src="'+urlgeeneral+'/img/productos/product.png" class="card-img-top" alt="..." >';
                }else{
          
                contenido +=' <img style="width: 100px;" src="'+urlgeeneral+'/img/productos/'+data[i].img+'" class="card-img-top" alt="..." >';
              }
                
                contenido +='<div class="card-body">';
                contenido +='<p class="card-text" style="font-size:12px; text-align: center;">'+data[i].nomb_pro+" / "+data[i].categoria+" / "+data[i].subcategoria+" / "+data[i].marca+" / "+data[i].color+'  <input id="product'+data[i].id+'" type="hidden" value="'+data[i].nomb_pro+'"></p>';
                contenido +='<p class="card-text" style="text-align: center;">S/' +data[i].prec_compra+ ' <input id="costo'+data[i].id+'" type="hidden" value="'+data[i].prec_compra+'"></p>';
                contenido +='</div>';
                contenido +=' </a>';
                contenido +='</div>';
                contenido +='</div>';
          
              }
          
              document.getElementById("inyecciondos").innerHTML = contenido;
          
          
          });

              
     }

    // alert(searctxt);

}

//FUNCIONA ABRIR EL MODAL
 function modal(id){


    var costox=$("#costo"+id).val();
    var namex=$("#product"+id).val();

    $("#titleModal").text(namex);
    $("#idProducto").val(id);
    $("#price-producto").val(costox);

     $(".bs-example-modal-sm").modal("show");

 }


//AGREGAR LOS PRODUCTOS A LA tableLayout:
      var total=0;
      subtotal=[];
      var cont=0;
      const vector =[];

      function agregar_detalle(){

        $(".bs-example-modal-sm").modal("hide");

          document.getElementById('pagar').disabled=false;


           var costox=$("#price-producto").val();
           var namex=$("#titleModal").text();
           var id=$("#idProducto").val();
           var cantidadxy=$("#cantidad_producto").val();

           var selectunidades= document.getElementById("uniades_id"); /*Obtener el SELECT */
           var unidadesxy = selectunidades.options[selectunidades.selectedIndex].value;
           var combo = document.getElementById("uniades_id");
           var textx = combo.options[combo.selectedIndex].text;

           //alert(textx);



           if(repetido(id)){

                Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: 'Lo Siento pero el producto ya a sido agregado!',
                  footer: ''
              })


           }else{

                const importe = costox * parseInt(cantidadxy);
                subtotal[cont]=parseFloat(importe);
                total=parseFloat(total+subtotal[cont]);




                var fila='<tr class="selected" id="fila'+cont+'" ><td><button type="button" class="btn btn-warning" onclick="eliminar('+cont+');">X</button></td><td style="font-size: 12px;" ><input class="id_producto obligatorio" type="hidden"  name="id_producto[]"  value="'+id+'">'+namex+'</td><td><input class="id_unidad obligatorio" type="hidden"  name="id_unidad[]"  value="'+unidadesxy+'"> '+textx+'</td><td style="font-size: 12px;" > <input type="number" class="cantidadx obligatorio" onkeyup="calcular_importe('+cont+')" id="cantidadx'+cont+'" style=" width: 90px;" value="'+cantidadxy+'"> </td> </td> <td><input type="number" class="preciox obligatorio" id="costo'+cont+'" onkeyup="calcular_importe('+cont+')" style=" width: 90px;"    value="'+costox+'"><td><input class="fletex" id="fletex'+cont+'" value="0" style=" width: 90px;"    > </td>  </td>  <td id="importe'+cont+'" class="importe">'+importe.toFixed(2)+'</td> </tr>';
                cont++;

                $('#listadocompras').append(fila);
                //calcular_importe(id);

                $("#subtotal").text(total);
                $("#subtotal_input").val(total);
                $("#total_compratemporal").text(total);
                $("#total_compra").val(total);


                vector.push({
                  id:id
                });


           }

           //alert(namex);





      }

      //metodo para cargar las unidades de medida en la tabla

      function todasunidades(){
        $.get(urlgeeneral+"/compras/unidades",function(data){

               let contenido="";
               contenido += "<option value='' >--Seleccionar--</option >";
               for (var i = 0; i < data.length; i++) {


                 contenido += "<option value='"+data[i].id+"' >"+ data[i].descripcion+"</option >";

               }

               document.getElementById("uniades_id").innerHTML=contenido;

             });
        }


function eliminar(index){


  $("#fila" +index).remove();
  const importe = document.getElementsByClassName('importe');
  let total_importe = 0;

  for (var i = 0; i < importe.length; i++) {

        let subt = importe[i].textContent;
        total_importe -= parseFloat(subt);
  }

  const subtotal = document.getElementById('subtotal');
  const subtotal_input = document.getElementById('subtotal_input');
  const total = document.getElementById('total_compratemporal');
  const total_input = document.getElementById('total_compra');
 let temporan=parseFloat(-1*total_importe);

  subtotal.textContent = temporan.toFixed(2);
  subtotal_input.value = temporan.toFixed(2);


  total.textContent = temporan.toFixed(2);
  total_input.value = temporan.toFixed(2);

    vector.splice(index, 1);

      if( Object.keys(vector).length === 0){

          document.getElementById('pagar').disabled=true;
      }



}

//VALIDAR EXISTENCIA
function repetido(id){

  var sw=0;
    //console.log(id);
for(var i=0;i<vector.length;i++){

    if(vector[i].id==id){
        sw=true;

        //vector.splice(i, 1);
    }

}

return sw;


}


function calcular_importe(id){

  const cant = document.getElementById('cantidadx'+id);
  const price = document.getElementById('costo'+id);
  const importe = document.getElementById('importe'+id)


  const total_imp = cant.value * price.value;
  importe.textContent = total_imp.toFixed(2);


    total_compra();
}

function  total_compra(){

  const importe = document.getElementsByClassName('importe');
  let total_importe = 0;

  for (var i = 0; i < importe.length; i++) {

        let subt = importe[i].textContent;
        total_importe += parseFloat(subt);
  }

  const subtotal = document.getElementById('subtotal');
  const subtotal_input = document.getElementById('subtotal_input');
  const total = document.getElementById('total_compratemporal');


  subtotal.textContent = total_importe.toFixed(2);
  subtotal_input.value = total_importe.toFixed(2);


  total.textContent = total_importe.toFixed(2);
  $("#total_compra").val(total_importe.toFixed(2));


}
//FUNCION PARA OCULTAR LOS CUADROS DONDE SE PONEL EL CORRELATIVO DE LAS COMPRAS
const selectElement = document.querySelector('#tipo_comprobante_id');

selectElement.addEventListener('change', (event) => {
  const resultado = document.querySelector('#tipo_comprobante_id');
  //resultado.textContent = `Te gusta el sabor ${event.target.value}`;
  if(event.target.value==12){

    $("#document_ser").hide();
  }else{
    $("#document_ser").show();
  }
  console.log( `Te gusta el sabor ${event.target.value}`);
});

//FUNCION EJECULAR LA COMPRA
todo=[];
$("#pagar").on("click",function(){


    //if (datosobligatorio() == true) {

    var dato = new FormData();
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var selectproveedor_id = document.getElementById("proveedor_id"); /*Obtener el SELECT */
    var proveedor_id = selectproveedor_id.options[selectproveedor_id.selectedIndex].value;
    var selectalmacen_id = document.getElementById("almacen_id"); /*Obtener el SELECT */
    var almacen_id = selectalmacen_id.options[selectalmacen_id.selectedIndex].value;
    var selectmoneda_id = document.getElementById("moneda_id"); /*Obtener el SELECT */
    var moneda_id = selectmoneda_id.options[selectmoneda_id.selectedIndex].value;
   // var selectforma_pago_id= document.getElementById("forma_pago_id"); /*Obtener el SELECT */
    //var forma_pago_id = selectforma_pago_id.options[selectforma_pago_id.selectedIndex].value;
    var selecttipo_pago_id= document.getElementById("tipo_pago_id"); /*Obtener el SELECT */
    var tipo_pago_id = selecttipo_pago_id.options[selecttipo_pago_id.selectedIndex].value;
    var selecttipo_comprobante_id= document.getElementById("tipo_comprobante_id"); /*Obtener el SELECT */
    var tipo_comprobante_id = selecttipo_comprobante_id.options[selecttipo_comprobante_id.selectedIndex].value;
    var fecha_compra=$("#fecha_compra").val();
    var serie_comprobante=$("#serie_comprobante").val();
    var correlativo_comprobante=$("#correlativo_comprobante").val();
    var compra_venta=$("#subtotal").text();
    var total_igv=$("#igvtodo").text();
    var total_compras=$("#total_compratemporal").text();
    var cambio_monto=$("#cambio_monto").val();
    var porcentaje_igv=$("#igv").val();

    //LLENAR EL OBJETO
    var cabezera=new cabezeracompra(proveedor_id,almacen_id,moneda_id,tipo_pago_id,tipo_comprobante_id,fecha_compra,serie_comprobante,correlativo_comprobante,compra_venta,total_igv,total_compras,cambio_monto,porcentaje_igv);
    //console.log(cabezera);
    todo.push(cabezera);

    const id_producto=document.getElementsByClassName('id_producto');
    const cantidadx=document.getElementsByClassName('cantidadx');
    const preciox=document.getElementsByClassName('preciox');
    const importe = document.getElementsByClassName('importe');
    const unidad_medida_id =document.getElementsByClassName('id_unidad');
    const flete=document.getElementsByClassName('fletex');

    //alert(unidad_medida_id);


    for (var i = 0; i < importe.length; i++) {

       var detalle=new detallecompras(id_producto[i].value,unidad_medida_id[i].value,cantidadx[i].value,preciox[i].value,importe[i].textContent,flete[i].value);
       todo.push(detalle);

    }

      console.log(todo);


              $("#staticBackdropdos").modal("show");

              $.ajax({
                type : "POST",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                contentType:"application/json",
                dataType:'json',
                data:JSON.stringify(todo),
                processData:false,
                cache:false,
                url :urlgeeneral+ "/compras/crear",
                success : function (result) {

                    //console.log(result);

                    //$("#imprimir").unbind('click', false);
                    //document.getElementById('pagar').disabled=true;
                    $('#staticBackdropdos').css('display','none');

                    Swal.fire({
                        icon: 'success',
                        title: 'Oops...',
                        text: 'Compra Generada Correctamente',
                        footer: ''
                    })

                    location.href =urlgeeneral+"/compras";


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


    //}



});


//metodo para cacturar la cabezera de la compra
function cabezeracompra(proveedor_id,almacen_id,moneda_id,tipo_pago_id,tipo_comprobante_id,fecha_compra,serie_comprobante,correlativo_comprobante,compra_venta,total_igv,total_compras,cambio_monto,porcentaje_igv){


        this.proveedor_id = proveedor_id;
        this.almacen_id = almacen_id;
        this.moneda_id=moneda_id;
       
        this.tipo_pago_id=tipo_pago_id;
        this.tipo_comprobante_id=tipo_comprobante_id;
        this.fecha_compra=fecha_compra;
        this.serie_comprobante=serie_comprobante;
        this.correlativo_comprobante=correlativo_comprobante;
        this.compra_venta=compra_venta;
        this.total_igv=total_igv;
        this.total_compras=total_compras;
        this.cambio_monto=cambio_monto;
        this.porcentaje_igv=porcentaje_igv;
}

//constructor para capturar el detalle
function detallecompras(producto_id,unidad_medida_id,cantidad,precio,subtotal,flete){

      this.producto_id = producto_id;
      this.unidad_medida_id=unidad_medida_id;
      this.cantidad = cantidad;
      this.precio=precio;
      this.subtotal=subtotal;
      this.flete=flete;


}

//VALIDAR DATOS
function selectoption(){

    if ($('#options').val().trim() === '') {

        alert('Debe seleccionar una opción');

    }

}



//VALIDAR INPUT

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
