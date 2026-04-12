urlgeeneral=$("#url_raiz_proyecto").val();


window.addEventListener("load", function (event) {

    //listadoproductos();

   $(".loader").fadeOut("slow");



 });


  //mostrar el listado de colores
 $.get(urlgeeneral+"/colores/listadocolores",function(data){

    var contenido="";
    contenido +=' <option value="">--Seleccionar--</option>';
    for(var i=0;i<data.length;i++){

     contenido += "<option value='" + data[i].id + "' >" + data[i].descripcion + "</option >";
    }

    document.getElementById("color_id").innerHTML=contenido;



});

//CARGAR EL IMPUESTO
$.get(urlgeeneral+"/productos/impuesto_cliente",function(data){

  var contenido="";
  contenido +=' <option value="">--Seleccionar--</option>';
  for(var i=0;i<data.length;i++){

   contenido += "<option value='" + data[i].id + "' >" + data[i].impuesto + "</option >";
  }

  document.getElementById("impuesto_id").innerHTML=contenido;



});


 //mostrar el listado de marcas

 $.get(urlgeeneral+"/marcas/listadomarca",function(data){

    var contenido="";
    contenido +=' <option value="">--Seleccionar--</option>';
    for(var i=0;i<data.length;i++){

    contenido += "<option value='" + data[i].id + "' >" + data[i].descripcion + "</option >";
    }

    document.getElementById("marca_id").innerHTML=contenido;



});



      //METODO PARA LISTAR LAS CATEGORIAS
      $.get(urlgeeneral+"/categorias/listado",function(data){

        var contenido="";
       contenido +=' <option value="">--Seleccionar--</option>';
       for(var i=0;i<data.length;i++){

       contenido += "<option value='"+data[i].id+"' >" + data[i].categoria + "</option >";
       }

       document.getElementById("categoria_id").innerHTML=contenido;

   });

   //FUNCION PARA LISTAR LAS SUBCATEOGIRAS
   function seleccionar(id){

       $.get(urlgeeneral+"/productos/subcategorias/"+id,function(data){

         var contenido="";
           contenido +=' <option value="">--Seleccionar--</option>';
           for(var i=0;i<data.length;i++){

           contenido += "<option value='" + data[i].id + "' >" + data[i].subcategoria + "</option >";
           }

           document.getElementById("subcategoria_id").innerHTML=contenido;

       });

   }


 /* $("#modales").on("click",function(){

    datostarifa=document.getElementById('almacen_id').value.split('_');


      if(datostarifa==""){

        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Es muy Importante que seleccione el Almacen antes de continuar!',
          footer: ''
        })


      }else{

        listadoproductos();

        $(".bs-example-modal-xl").modal("show");

      }



});
 //metodo para llenar la tabla de productos
function listadoproductos(){




  $.get(urlgeeneral+"/productos-maestro/listadoproductosmaestro",function(data){


         llenalistado(data);

  });

}


function llenalistado(data){

  //datostarifa=document.getElementById('almacen_id').value.split('_');
  //alert(datostarifa);

  var contenido = "";

    for (var i = 0; i < data.length; i++) {
      contenido += "<tr>";
      contenido += "<td style='padding:1px;text-align:center;' id='codi"+data[i].id+"'>" +   data[i].codigo  + "</td>";
      contenido += "<td style='padding:1px;text-align:center' id='name"+data[i].id+ "'>" + data[i].nomb_pro + "</td>";
      contenido += "<td style='padding:1px;text-align:center'>  <input type='hidden' id='cate"+data[i].id+"' value='"+data[i].idcategoria+"'>" + data[i].categoria + "</td>";
      contenido += "<td style='padding:1px;text-align:center'>  <input type='hidden' id='subca"+data[i].id+"' value='"+data[i].idsub+"'>" + data[i].subcategoria + "</td>";
      contenido += "<td style='padding:1px;text-align:center'>  <input type='hidden' id='iduni"+data[i].id+"' value='"+data[i].idunidad+"'>" + data[i].unidad + "</td>";
      contenido += "<td style='padding:1px;text-align:center'>  <input type='hidden' id='idmarca"+data[i].id+"' value='"+data[i].idmarca+"'>" + data[i].marca + "</td>";
      contenido += "<td style='padding:1px;text-align:center'>  <input type='hidden' id='idcolor"+data[i].id+"' value='"+data[i].idcolores+"'>" + data[i].color + "</td>";
      contenido += "<td style='padding:1px;text-align:center' > <input type='hidden' id='desc"+data[i].id+"' value='"+data[i].descuento_minimo+"'><strong id='comp"+data[i].id+"'>"+data[i].prec_compra +"</strong></td>";
      contenido += "<td style='padding:1px;text-align:center'>";
      //contenido +='<div class="dropdown">';
      //contenido +=' <button class="btn  dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"> Action</button>';
      //contenido +=' <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">';
      //contenido +=' <li><a class="dropdown-item" href="#">Action</a></li>';
      //contenido +=' <li><a class="dropdown-item" href="#">Action</a></li>';
      //contenido +=' <li><a class="dropdown-item" href="#">Action</a></li>';
      //contenido +=' </ul>';
      //contenido +='</div>';
      contenido +='<a href="#" onclick="seleccionar('+data[i].id+')" type="button" class="btn btn-success "><i class="fas fa-hand-pointer"></i> </a>';

      //contenido +='<a href="productos-maestro/'+data[i].id+'" type="button" class="btn btn-success "><i class="fab fa-searchengin"></i> </a>';
      //contenido +='<a href="productos-maestro/'+data[i].id+'/edit" type="button" class="btn btn-info " ><i class="fas fa-edit"></i> </a>';
      //contenido +='<button type="button" onclick="eliminarsector(\''+ data[i].id +'\')" class="btn btn-danger  eliminar"><i class="fa fa-trash eliminar" aria-hidden="true"></i> </button>';


      contenido +="</td>";
      contenido += "</tr>";


    }

    document.getElementById("listadeprodcutos").innerHTML = contenido;
      $("#datatableclientes").DataTable();


}

function seleccionar(id){

  datostarifa=document.getElementById('almacen_id').value.split('_');

    $.get(urlgeeneral+"/productos/consultacreacionalmacen/"+id+"/"+datostarifa,function(data){

         if(data.length>0){

          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Lo siento el sistema a detectado que el produco con el codigo seleccionado ya a sido registrado para este almacen!',
            footer: ''
          })


          $(".bs-example-modal-xl").modal("hide");


         }else{

          var cod=$("#codi"+id).text();
          var producto=$("#name"+id).text();
          var precio_compra=$("#comp"+id).text();
          var descuento_minimo=$("#desc"+id).val();
          $("#id_producto").val(id);

          $("#codigo").val(cod);
          $("#nomb_pro").val(producto);
          $("#prec_compra").val(precio_compra);
          $("#descuento_minimo").val(descuento_minimo);
          var idcolor=$("#idcolor"+id).val();
          var cate=$("#cate"+id).val();
          var iduni=$("#iduni"+id).val();
          var idmarca=$("#idmarca"+id).val();

          color(idcolor);
          categorias(cate);
          marca(idmarca);
          unidades(iduni);


           $(".bs-example-modal-xl").modal("hide");

            Swal.fire({
                icon: 'success',
                title: 'Oops...',
                text: 'Producto Cargado Correctamente',
                footer: ''
            })





         }

    });





}


//cargar las unidades de medida
function unidades(id){

  $.get(urlgeeneral+"/productos/unidades",function(data){

    var contenido="";
    contenido +=' <option value="">--Seleccionar--</option>';
    for(var i=0;i<data.length;i++){


      if( data[i].id==id){

        contenido += "<option value='" + data[i].id + "' selected>" + data[i].descripcion + "</option >";

      }else{

        contenido += "<option value='" + data[i].id + "' >" + data[i].descripcion + "</option >";
      }



    }

    document.getElementById("unidad_medida_id").innerHTML=contenido;



});




}


  //mostrar el listado de colores
  function color(idcolor){



    $.get(urlgeeneral+"/colores/listadocolores",function(data){

      var contenido="";
      contenido +=' <option value="">--Seleccionar--</option>';
      for(var i=0;i<data.length;i++){


        if( data[i].id==idcolor){

          contenido += "<option value='" + data[i].id + "' selected>" + data[i].descripcion + "</option >";

        }else{

          contenido += "<option value='" + data[i].id + "' >" + data[i].descripcion + "</option >";
        }



      }

      document.getElementById("color_id").innerHTML=contenido;



  });



}

//METODO PARA LISTAR LAS CATEGORIAS

function categorias(category){


    $.get(urlgeeneral+"/categorias/listado",function(data){

      subcategorias(category);

        var contenido="";
       contenido +=' <option value="">--Seleccionar--</option>';
       for(var i=0;i<data.length;i++){

        if(data[i].id==category){

          contenido += "<option value='"+data[i].id+"' selected>" + data[i].categoria + "</option >";

        }else{

            contenido += "<option value='"+data[i].id+"' >" + data[i].categoria + "</option >";


        }


       }

       document.getElementById("categoria_id").innerHTML=contenido;

    });



}

//FUNCION PARA LISTAR LAS SUBCATEOGIRAS
function subcategorias(id){


  $.get(urlgeeneral+"/productos/subcategorias/"+id,function(data){

       var contenido="";
      contenido +=' <option value="">--Seleccionar--</option>';

      for(var i=0;i<data.length;i++){

       if(data[i].id==id){

         contenido += "<option value='" + data[i].id + "' selected>" + data[i].subcategoria + "</option >";

       }else{

         contenido += "<option value='" + data[i].id + "' >" + data[i].subcategoria + "</option >";

       }



      }

      document.getElementById("subcategoria_id").innerHTML=contenido;

  });

}


//mostrar el listado de marcas
function marca(datos){


  $.get(urlgeeneral+"/marcas/listadomarca",function(data){

    var contenido="";
    contenido +=' <option value="">--Seleccionar--</option>';
    for(var i=0;i<data.length;i++){

      if(data[i].id==datos){

        contenido += "<option value='" + data[i].id + "' selected>" + data[i].descripcion + "</option >";


      }else{

        contenido += "<option value='" + data[i].id + "' >" + data[i].descripcion + "</option >";
      }




    }

    document.getElementById("marca_id").innerHTML=contenido;



});


}*/


  //METODO PARA CALCULAR EL PRECIO DEL PRODUCTO POR PORCENTAJE
  $("#porcentaje_ganancia").on('keyup',function(){

    var prec_compra=$("#prec_compra").val();
    var porcentaje_ganancia=$("#porcentaje_ganancia").val();


    if(prec_compra==""){

    }else{

      var porcentaje = (parseFloat(prec_compra)*parseFloat(porcentaje_ganancia))/100;
      total=(parseFloat(porcentaje)+parseFloat(prec_compra)).toFixed(2);

      $("#precio_venta_contado").val(total);


    }
  });

   //metodo para generar las ganancias precio al credito
   $("#porcentaje_venta_credito").on('keyup',function(){

    var prec_compra=$("#prec_compra").val();
    var porcentaje_venta_credito=$("#porcentaje_venta_credito").val();


    if(prec_compra==""){

    }else{

      var porcentaje = (parseFloat(prec_compra)*parseFloat(porcentaje_venta_credito))/100;
      total=(parseFloat(porcentaje)+parseFloat(prec_compra)).toFixed(2);

      $("#precio_venta_credito").val(total);


    }




  });
