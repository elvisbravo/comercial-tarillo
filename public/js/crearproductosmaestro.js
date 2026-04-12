urlgeeneral=$("#url_raiz_proyecto").val();


window.addEventListener("load", function (event) {

 
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

      $("#codigo_barras").on("keyup",function(){

        var codigo_barras=$("#codigo_barras").val();
        console.log(codigo_barras);
        JsBarcode("#codigo", codigo_barras);


      });

      
 