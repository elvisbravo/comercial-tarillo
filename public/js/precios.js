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

function llenarpermisos(data) {
    if ($.fn.DataTable.isDataTable('#datatable')) {
        $('#datatable').DataTable().destroy();
    }

    let contenido = "";
    for (var i = 0; i < data.length; i++) {
        contenido += "<tr>";
        contenido += "<td style='padding:1px;text-align:center'>" + data[i].descripcion + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>" + data[i].codigo + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>" + data[i].nomb_pro + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>" + data[i].precio_contado + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>" + data[i].precio_credito + "</td>";
        contenido += "<td style='padding:1px;text-align:center'><span class='badge badge-soft-success font-size-12'><i class='fas fa-check-circle me-1'></i> Activo</span></td>";
        contenido += "<td style='padding:1px;text-align:center'>";

        if (typeof canEdit !== 'undefined' && canEdit) {
            contenido += '<a href="precios/' + data[i].id + '/edit" type="button" class="btn btn-info waves-effect waves-light" title="Editar"><i class="fas fa-edit"></i> </a>';
        }

        if (typeof canDelete !== 'undefined' && canDelete) {
            contenido += ' <button type="button" onclick="eliminarsector(' + data[i].id + ')" class="btn btn-danger waves-effect waves-light eliminar" title="Eliminar"><i class="fas fa-trash-alt"></i> </button>';
        }

        contenido += "</td>";
        contenido += "</tr>";
    }

    $('#listaprcios').empty().html(contenido);
    initDataTable("#datatable");
}
