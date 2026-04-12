urlgeeneral=$("#url_raiz_proyecto").val();
$("#actualizar").hide();


window.addEventListener("load", function (event) {

     precios();
    //sedes();
   $(".loader").fadeOut("slow");


 });


 //funcion para listar sedes

 function precios(){

    $.get(urlgeeneral+"/precios/lista_precios", function (data) {
      //console.log(data);

        llenarpermisos(data);

    });

}

function llenarpermisos(data){

    let contenido="";
    for (var i = 0; i < data.length; i++) {
      contenido += "<tr>";
      contenido += "<td style='padding:1px;text-align:center'>" + data[i].descripcion  + "</td>";
      contenido += "<td style='padding:1px;text-align:center'>" + data[i].codigo + "</td>";
      contenido += "<td style='padding:1px;text-align:center'>" + data[i].nomb_pro + "</td>";
      //contenido += "<td style='padding:1px;text-align:center'>" + data[i].prec_compra + "</td>";
      contenido += "<td style='padding:1px;text-align:center'>" + data[i].precio_contado + "</td>";
      contenido += "<td style='padding:1px;text-align:center'>" + data[i].precio_credito + "</td>";
      contenido += "<td style='padding:1px;text-align:center'><span class='label label-success'>Activo</span></td>";
      contenido += "<td style='padding:1px;text-align:center'>";
    //contenido +='<i class="fas fa-edit"></i>';
    contenido +='<a href="precios/'+data[i].id+'/edit" type="button" class="btn btn-info " ><i class="fas fa-edit"></i> </a>';
    contenido +='<button type="button" onclick="eliminarsector('+ data[i].id +')" class="btn btn-danger  eliminar"><i class="fas fa-trash-alt"></i> </button>'
    contenido +="</td>";

      contenido += "</tr>";


    }

    document.getElementById("listaprcios").innerHTML = contenido;
    $("#datatable").dataTable();


}
