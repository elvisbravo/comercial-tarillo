const urlgeneral = document.getElementById('url_raiz_proyecto').value;
const sedes = document.getElementById('list_sedes');
const agregar_comprobante = document.getElementById('agregar_comprobante');
const form = document.getElementById('form_correlativos');

sedes.addEventListener('click', (e) => {
    if (e.target.classList.contains('tipo_envio')) {

        update_tipo_envio(e);

    }

    if (e.target.classList.contains('estado')) {

        update_estado(e);

    }

    if (e.target.classList.contains('ingresar_sede')) {

        ingresar_sede(e);

    }

});

agregar_comprobante.addEventListener('click', (e) => {
    const idsede = document.getElementById('idsede');
    const comprobante = document.getElementById('comprobante');
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    // Obtener tipo_envio de la sede actual
    const checkbox = document.querySelector('.tipo_envio[value="' + idsede.value + '"]');
    const tipoEnvio = checkbox && checkbox.checked ? 1 : 0;

    const comprobantes = document.querySelectorAll('.comprobantes');
    let veri = 0;

    if (comprobantes.length != 0) {
        comprobantes.forEach(comp => {
            if (comp.value == comprobante.value) {
                veri = veri + 1;
            }
        });
    }

    if (veri != 0) {
        alert('Este comprobante ya está en la lista');
        return false;
    }

    const formData = new FormData();

    formData.append('idsede',idsede.value);
    formData.append('comprobante',comprobante.value);
    formData.append('tipo_envio', tipoEnvio);
    formData.append("_token", csrf);

    fetch(urlgeneral+"/sedes/select_comprobante",{
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        if (data.respuesta === "ok") {
            let html = `
            <tr>
                <input type="hidden" class="comprobantes" name="tipocomprobante[]" value="${data.idcomprobante}" />
                <td>
                    <input type="text" class="form-control form-control-sm" value="${data.comprobante}" readonly="true">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" name="serie_traido[]" placeholder="SERIE" required="">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" name="correlativo_traido[]" placeholder="CORRELATIVO" required="">
                </td>
                <td>
                    <a href="javascript:;"><i data-feather="trash" class="icon-sm me-2 text-danger"></i></a>
                </td>
            </tr>
            `;

            $("#bodyComprobantes").append(html);
        } else if (data.respuesta === "existe") {
            alert(data.mensaje);
        }

    })

});

form.addEventListener('submit', (e) => {
    e.preventDefault();
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    const formData = new FormData(form);

    formData.append('_token', csrf);

    fetch(urlgeneral+"/sedes/guardar_correlativos",{
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Se guardo correctamente',
            showConfirmButton: false,
            timer: 1500
        })
    })
})

function update_tipo_envio(e){
    const idsede = e.target.value;
    let envio;

    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    if (e.target.checked) {
        envio = 1;
    } else {
        envio = 0;
    }

    const formData = new FormData();
    formData.append('idsede',idsede);
    formData.append('envio',envio);
    formData.append("_token", csrf);

    fetch(urlgeneral+"/sedes/update_tipo_envio",{
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Se cambio el tipo de envio correctamente',
            showConfirmButton: false,
            timer: 1500
        })
    })
}

function update_estado(e){
    const idsede = e.target.value;
    let estado;

    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    if (e.target.checked) {
        estado = 1;
    } else {
        estado = 0;
    }

    const formData = new FormData();
    formData.append('idsede',idsede);
    formData.append('estado',estado);
    formData.append("_token", csrf);

    fetch(urlgeneral+"/sedes/update_estado",{
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Se cambio el estado correctamente',
            showConfirmButton: false,
            timer: 1500
        })
    })
}

function correlativos(idsede){
    $("#modal_correlativos").modal('show');

    const ids = document.getElementById('idsede');
    ids.value = idsede;

    // Obtener el valor del checkbox tipo_envio (0 = prueba, 1 = produccion)
    const checkbox = document.querySelector('.tipo_envio[value="' + idsede + '"]');
    const tipoEnvio = checkbox && checkbox.checked ? 1 : 0;

    fetch(urlgeneral+"/sedes/correlativos/"+idsede+"?tipo_envio="+tipoEnvio)
    .then(res => res.json())
    .then(data => {
        console.log(data);
        render_correlativos(data);
    })
}

function render_correlativos(data){
    let html = '';

    if(data.length === 0){
        html = '<tr><td colspan="3" class="text-center">No hay correlativos registrados</td></tr>';
    } else {
        data.forEach(function(item) {
            html += `
            <tr>
                <input type="hidden" name="correlativo_id[]" value="${item.id}" />
                <input type="hidden" name="tipocomprobantetraido[]" value="${item.tipo_comprobante_id}" />
                <td>
                    <input type="text" class="form-control form-control-sm" value="${item.descripcion}" readonly="true">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" name="serie_traido[]" value="${item.serie}" readonly="true">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" name="correlativo_traido[]" value="${item.correlativo}" readonly="true">
                </td>
                <td>
                    <a href="javascript:;" onclick="eliminar_correlativo(${item.id})"><i data-feather="trash" class="icon-sm me-2 text-danger"></i></a>
                </td>
            </tr>
            `;
        });
    }

    $("#bodyComprobantes").html(html);
}

function eliminar_correlativo(id){
    // Función para eliminar un correlativo
    if(confirm('¿Está seguro de eliminar este correlativo?')) {
        fetch(urlgeneral+"/sedes/eliminar_correlativo/"+id)
        .then(res => res.json())
        .then(data => {
            if(data.respuesta === 'ok') {
                // Recargar la lista
                const idsede = document.getElementById('idsede').value;
                correlativos(idsede);
            } else {
                alert('Error al eliminar');
            }
        });
    }
}

function ingresar_sede(e){
    const idsede = e.target.getAttribute('data-id');
    const csrf   = document.querySelector('meta[name="csrf-token"]').content;

    const formData = new FormData();
    formData.append('sede_id', idsede);
    formData.append('_token', csrf);

    fetch(urlgeneral + '/sedes/seleccionar_sede', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.ok) {
            window.location.href = urlgeneral + '/home';
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.error || 'No se pudo seleccionar la sede.'
            });
        }
    })
    .catch(() => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error de conexión al seleccionar la sede.'
        });
    });
}
