urlgeeneral=$("#url_raiz_proyecto").val();
$("#actualizar").hide();

//FUNCION LOAD
window.addEventListener("load", function (event) {

  listadoproductos();
  $(".loader").fadeOut("slow"); 


});

//metodo para llenar la tabla de productos
function listadoproductos(){


  $.get(urlgeeneral+"/productos-maestro/listadoproductosmaestro",function(data){

       
         llenalistado(data);

  });

}


function llenalistado(data){

  var contenido = "";

    for (var i = 0; i < data.length; i++) {
      contenido += "<tr>";
      contenido += "<td style='padding:1px;text-align:center;'>" +  parseInt(i+1,10) + "</td>";
      contenido += "<td style='padding:1px;text-align:center'>" + data[i].nomb_pro + "</td>";
      contenido += "<td style='padding:1px;text-align:center'>" + data[i].categoria + "</td>";
      contenido += "<td style='padding:1px;text-align:center'>" + data[i].subcategoria + "</td>";
      contenido += "<td style='padding:1px;text-align:center'>" + data[i].unidad + "</td>";
      contenido += "<td style='padding:1px;text-align:center'>" + data[i].marca + "</td>";
      contenido += "<td style='padding:1px;text-align:center'>" + data[i].prec_compra + "</td>";
      contenido += "<td style='padding:1px;text-align:center'>";
      //contenido +='<div class="dropdown">';
      //contenido +=' <button class="btn  dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"> Action</button>';
      //contenido +=' <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">';
      //contenido +=' <li><a class="dropdown-item" href="#">Action</a></li>';
      //contenido +=' <li><a class="dropdown-item" href="#">Action</a></li>';
      //contenido +=' <li><a class="dropdown-item" href="#">Action</a></li>';
      //contenido +=' </ul>';
      //contenido +='</div>';
        

      contenido +='<a href="productos-maestro/'+data[i].id+'" type="button" class="btn btn-success "><i class="fab fa-searchengin"></i> </a>';
      contenido +='<a href="productos-maestro/'+data[i].id+'/edit" type="button" class="btn btn-info " ><i class="fas fa-edit"></i> </a>';
      contenido +='<button type="button" onclick="eliminarsector(\''+ data[i].id +'\')" class="btn btn-danger  eliminar"><i class="fa fa-trash eliminar" aria-hidden="true"></i> </button>';
      
      
      contenido +="</td>";
      contenido += "</tr>";


    }

    document.getElementById("listadoproductos").innerHTML = contenido;
      $("#datatable").DataTable();


}