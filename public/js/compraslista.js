urlgeeneral=$("#url_raiz_proyecto").val();

window.addEventListener("load", function (event) {

    listadocompras();
   $(".loader").fadeOut("slow");


 });

 //FUNCION LISTADO

 function listadocompras(){

        $.get(urlgeeneral+"/compras/listacompras",function(data){

            llenardata(data);
        });
 }

 function llenardata(data) {
    const estados = {
        0: '<div class="badge badge-soft-danger font-size-12">ANULADO</div>',
        1: '<div class="badge badge-soft-success font-size-12">ACTIVA</div>'
    }

    let contenido = "";
    for (var i = 0; i < data.length; i++) {
        const estdo_guia = estados[data[i].estado] || 'ESTADO DESCONOCIDO';

        contenido += "<tr>";
        contenido += "<td style='padding:1px;text-align:center'>" + parseInt(i + 1, 10) + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].nombre_comercial + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].serie_comprobante + "-" + data[i].correlativo_comprobante + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].total_compra + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].descripcion + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + data[i].fecha_compra + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> " + estdo_guia + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>";
        
        if (typeof canViewDetail !== 'undefined' && canViewDetail) {
            contenido += ' <button type="button" onclick="abrimodal(' + data[i].id + ')" class="btn btn-warning waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#staticBackdrop" title="Ver Detalle"><i class="fas fa-eye"></i> </button>';
        }

        if (data[i].estado != 0 && typeof canDelete !== 'undefined' && canDelete) {
            contenido += ' <button type="button" onclick="eliminarcompra(' + data[i].id + ')" class="btn btn-danger waves-effect waves-light eliminar" title="Anular"><i class="fas fa-trash-alt"></i> </button>';
        }

        contenido += "</td>";
        contenido += "</tr>";
    }

    document.getElementById("listadocompras").innerHTML = contenido;
    initDataTable("#datatable");
}

function abrimodal(id){

    if(id == "0"){

      limpiarcajasunidas();

      $("#guardar").show();
      $("#actualizar").hide();

    }else{



      $.get(urlgeeneral+"/compras/ver/"+id, function (data) {


        let contenido="";
        for (var i = 0; i < data.length; i++) {
            contenido += "<tr>";
            contenido += "<td style='padding:1px;text-align:center'>" +  parseInt(i+1,10) + "</td>";
            contenido += "<td style='padding:1px;text-align:center'> " + data[i].nomb_pro + "</td>";
            contenido += "<td style='padding:1px;text-align:center'> " + data[i].cantidad + "</td>";
            contenido += "<td style='padding:1px;text-align:center'> " + data[i].descripcion + "</td>";
            contenido += "<td style='padding:1px;text-align:center'> " + data[i].precio + "</td>";
            contenido += "<td style='padding:1px;text-align:center'> " + data[i].subtotal + "</td>";


            contenido += "</tr>";
        }

        document.getElementById("detalle").innerHTML = contenido;
        initDataTable("#datatabledos");




      });


    }


  }

function eliminarcompra(id) {
    Swal.fire({
        title: '¿Desea Anular la Compra?',
        text: "No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, Anular!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            var csrf = document.querySelector('meta[name="csrf-token"]').content;
            $.ajax({
                type: "POST",
                url: "compras/eliminar/" + id,
                data: { "_method": "delete", '_token': csrf },
                success: function (data) {
                    listadocompras();
                    if (data.respuesta === "error") {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.mensaje
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Anulado',
                            text: data.mensaje
                        });
                    }
                }
            });
        }
    });
}
