const urlgeneral = document.getElementById('url_raiz_proyecto').value;

const formNota = document.getElementById('form_nota_credito');
const csrf = document.querySelector('meta[name="csrf-token"]').content;

$("#dataTableExample").DataTable({
    paging: false
});

function enviar_comprobante(e, id) {
    e.preventDefault();

    $("#modalEnviar").modal('show');
    
    fetch('/enviar-comprobante/'+id)
    .then(res => res.json())
    .then(data => {
        $("#modalEnviar").modal('hide');

        if (data.respuesta == "ok") {
            Swal.fire({
                position: 'top-center',
                icon: 'success',
                title: data.msj_sunat,
                showConfirmButton: false,
                timer: 2500
            });

            setTimeout(() => {
                location.reload();
            }, 3000);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: data.mensaje
            })
        }
    })
}

function generarNotaCredito(e, id) {
    e.preventDefault();

    $("#modal_nota_credito").modal('show');

    const venta = document.getElementById('idsale');
    venta.value = id;
}

formNota.addEventListener('submit', (e) => {
    e.preventDefault();

    const formData = new FormData(formNota);
    formData.append('_token', csrf);

    $("#modal_nota_credito").modal('hide');
    $("#modal_enviar_nota").modal('show');

    fetch(urlgeneral+"/generar-nota-credito", {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        $("#modal_enviar_nota").modal('hide');

        if (data.respuesta == 'ok') {
            Swal.fire({
                position: 'top-center',
                icon: 'success',
                title: data.mensaje,
                showConfirmButton: false,
                timer: 2500
            });

            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            Swal.fire({
                position: 'top-center',
                icon: 'error',
                title: data.mensaje,
                showConfirmButton: false,
                //timer: 2500
            });
        }
    })

})

function generarNotaVenta(e, id) {
    e.preventDefault();
    Swal.fire({
        title: '¿Dese eliminar la nota de venta?',
        text: "No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!'
      }).then((result) => {
        if (result.isConfirmed) {
            $("#modalEnviar").modal('show');
            const mensaje = document.getElementById('mensajeConfirmacion');
            mensaje.textContent = 'Eliminando Nota de Venta';
            fetch(urlgeneral+"/delete-nota-venta/"+id)
            .then(res => res.json())
            .then(data => {
                $("#modalEnviar").modal('hide');
                if (data.respuesta == "ok") {
                    Swal.fire({
                        position: 'top-center',
                        icon: 'success',
                        title: data.mensaje,
                        showConfirmButton: false,
                        timer: 2500
                    });

                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                } else {
                    Swal.fire({
                        position: 'top-center',
                        icon: 'error',
                        title: "Intente de nuevo o comuniquese con el administrador del sistema",
                        showConfirmButton: false,
                        //timer: 2500
                    });
                }
            })

        }
    })
}