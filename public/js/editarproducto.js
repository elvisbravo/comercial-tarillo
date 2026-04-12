urlgeeneral=$("#url_raiz_proyecto").val();

window.addEventListener("load", function (event) {

   $(".loader").fadeOut("slow");
   var codigo_barras=$("#codigo_barras").val();
      //JsBarcode("#codigo", codigo_barras);

   codigoarras();

   var category=$("#categoria_idg").val();
   var idcolor=$("#idcolor").val();
   var idmarca=$("#idmarca").val();
   var impuesto_temporal=$("#impuesto_temporal").val();

   categorias(category);
   color(idcolor);
   marca(idmarca);
   impuestos(impuesto_temporal);

 });

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


}

   //METODO PARA LISTAR LAS CATEGORIAS
 function impuestos(impuesto){


    $.get(urlgeeneral+"/productos/impuesto_cliente",function(data){

      var contenido="";
      contenido +=' <option value="">--Seleccionar--</option>';
      for(var i=0;i<data.length;i++){

        
        if(data[i].id==impuesto){

         
          contenido += "<option value='" + data[i].id + "' selected>" + data[i].impuesto + "</option >";


        }else{

          contenido += "<option value='" + data[i].id + "' >" + data[i].impuesto + "</option >";
        }

    
     
      }
    
      document.getElementById("impuesto_id").innerHTML=contenido;
    
    
    
    });
  

 }

//METODO PARA LISTAR LAS CATEGORIAS

function categorias(category){


    $.get(urlgeeneral+"/categorias/listado",function(data){

      seleccionar(category);

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
function seleccionar(id){


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



//METODO PARA ANALIZAR EL CAMBIO DE perspectiveOrigin:
$("#prec_compra").on("keyup",function(){

  var prec_compra=$("#prec_compra").val();
  var porcentaje_ganancia=$("#porcentaje_ganancia").val();
  var porcentaje_venta_credito=$("#porcentaje_venta_credito").val();

  if(prec_compra==""){

  }else{

    var porcentaje = (parseFloat(prec_compra)*parseFloat(porcentaje_ganancia))/100;
    total=(parseFloat(porcentaje)+parseFloat(prec_compra)).toFixed(2);

    $("#prec_ven").val(total);


  }

  if(prec_compra==""){

  }else{

    var porcentaje = (parseFloat(prec_compra)*parseFloat(porcentaje_venta_credito))/100;
    total=(parseFloat(porcentaje)+parseFloat(prec_compra)).toFixed(2);

    $("#precio_venta_credito").val(total);


  }



});

  //METODO PARA CALCULAR EL PRECIO DEL PRODUCTO POR PORCENTAJE
  $("#porcentaje_ganancia").on('keyup',function(){

    var prec_compra=$("#prec_compra").val();
    var porcentaje_ganancia=$("#porcentaje_ganancia").val();


    if(prec_compra==""){

    }else{

      var porcentaje = (parseFloat(prec_compra)*parseFloat(porcentaje_ganancia))/100;
      total=(parseFloat(porcentaje)+parseFloat(prec_compra)).toFixed(2);

      $("#prec_ven").val(total);


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







   //generar el codigo de barras
   function codigoarras(){

    $("#codigo_barras").on("keyup",function(){

      var codigo_barras=$("#codigo_barras").val();
      JsBarcode("#codigo", codigo_barras);


    });


   }




//MODIFICAR EL ESTADO DEL CONTROL DE STOCK

$("#switch1").on("change",function(){



if ($(this).is(':checked')) {
      console.log($(this).val() + ' is now checked');
      $("#switch1").val('SI');


  } else {
      console.log($(this).val() + ' is now unchecked');
      $("#switch1").val('NO');
  }

  //alert("hola");


});
