urlgeeneral=$("#url_raiz_proyecto").val();
$("#actualizar").hide();

//FUNCION LOAD
window.addEventListener("load", function (event) {

   listadoproductos();
  $(".loader").fadeOut("slow");


});


//metodo para llenar la tabla de productos
function listadoproductos(){


   $.get(urlgeeneral+"/productos/listarproductos",function(data){


          llenalistado(data);

   });

}

function llenalistado(data) {
    if ($.fn.DataTable.isDataTable('#datatable')) {
        $('#datatable').DataTable().destroy();
    }

    var contenido = "";
    for (var i = 0; i < data.length; i++) {
        contenido += "<tr>";
        contenido += "<td style='padding:1px;text-align:center;'>" + parseInt(i + 1, 10) + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>" + data[i].nomb_pro + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>" + data[i].categoria + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>" + data[i].subcategoria + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>" + data[i].unidad + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>" + data[i].marca + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>" + data[i].precio_contado + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>" + data[i].precio_credito + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> <strong>" + data[i].stock + "</strong> </td>";
        contenido += "<td style='padding:1px;text-align:center'>";

        if (typeof canEdit !== 'undefined' && canEdit) {
            contenido += '<a href="productos/' + data[i].id + '/edit" type="button" class="btn btn-info waves-effect waves-light" title="Editar"><i class="fas fa-edit"></i> </a> ';
        }

        if (typeof canDelete !== 'undefined' && canDelete) {
            contenido += '<button type="button" onclick="eliminarsector(\'' + data[i].id + '\')" class="btn btn-danger waves-effect waves-light eliminar" title="Anular"><i class="fa fa-trash"></i> </button>';
        }

        contenido += "</td>";
        contenido += "</tr>";
    }

    document.getElementById("listadoproductos").innerHTML = contenido;
    initDataTable("#datatable");
}


function eliminarsector(id) {
    Swal.fire({
        title: '¿Desea anular el Producto?',
        text: "No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            var csrf = document.querySelector('meta[name="csrf-token"]').content;
            $.ajax({
                type: "POST",
                url: urlgeeneral + "/productos/eliminar/" + id,
                data: { "_method": "delete", '_token': csrf },
                success: function (data) {
                    if (data == "ERROR") {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Lo siento no podemos anular el producto porque el sistema a detectado que el producto tiene stock disponible!',
                            footer: ''
                        })
                    } else {
                        Swal.fire(
                            'Eliminado!',
                            'El producto ha sido anulado.',
                            'success'
                        );
                        location.reload();
                    }
                }
            });
        }
    })
}
