urlgeneral=$("#url_raiz_proyecto").val();

const tipo_documento = document.getElementById('documento_identidad');
const numero_documento = document.getElementById('numero_documento');
const btn_consultar = document.getElementById('btn_consultar');

window.addEventListener("load", function (event) {

    //listadocategorias();
    $(".loader").fadeOut("slow");
     $("#datatable").dataTable();



  });


function anular(id){
    Swal.fire({
        title: '¿Desea anular el Cliente?',
        text: "¡No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, Anular!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            //Metodo para eleminar
            var csrf = document.querySelector('meta[name="csrf-token"]').content;
            $.ajax({
                type: "POST",
                url: urlgeneral+"/clientes/eliminar/"+id,
                data: {"_method": "delete",'_token': csrf},
                success: function (data) {
                    if(data.status === 'error'){
                        Swal.fire('Atención', data.mensaje, 'error');
                    } else {
                        Swal.fire(
                            'Anulado!',
                            'El cliente fue Anulado Correctamente.',
                            'success'
                        ).then(function() {
                            location.href = urlgeneral+"/clientes";
                        });
                    }
                }
            });
        }
    });
}

function activar(id){
    Swal.fire({
        title: '¿Desea Activar el Cliente?',
        text: "¡No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, Activar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            //Metodo para eleminar
            var csrf = document.querySelector('meta[name="csrf-token"]').content;
            $.ajax({
                type: "POST",
                url: urlgeneral+"/clientes/activar/"+id,
                data: {"_method": "delete",'_token': csrf},
                success: function (data) {
                    Swal.fire(
                        'Activado!',
                        'El cliente fue Activado Correctamente.',
                        'success'
                    ).then(function() {
                        location.href = urlgeneral+"/clientes";
                    });
                }
            });
        }
    });
}
