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
        return false;
    }

    const formData = new FormData();

    formData.append('idsede',idsede.value);
    formData.append('comprobante',comprobante.value);
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
                    <input type="text" class="form-control form-control-sm mb-2" name="serie_prueba[]" placeholder="SERIE" required="">
                    <input type="text" class="form-control form-control-sm mb-2" name="correlativo_prueba[]" placeholder="CORRELATIVO" required="">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm mb-2" name="serie_produccion[]" placeholder="SERIE" required="">
                    <input type="text" class="form-control form-control-sm mb-2" name="correlativo_produccion[]" placeholder="CORRELATIVO" required="">
                </td>
                <td>
                    <a href="javascript:;"><i data-feather="trash" class="icon-sm me-2 text-danger"></i></a>
                </td>
            </tr>
            `;

            $("#bodyComprobantes").append(html);
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

    fetch(urlgeneral+"/sedes/correlativos/"+idsede)
    .then(res => res.json())
    .then(data => {
        console.log(data);
    })
}

function render_correlativos(idsede){
    fetch(urlgeneral+"/sedes/correlativos/"+idsede)
    .then(res => res.json())
    .then(data => {
        let html = `
            <tr>
                <input type="hidden" class="comprobantes" name="tipocomprobantetraido[]" value="${data.tipo_comprobante_id}" />
                <td>
                    <input type="text" class="form-control form-control-sm" value="${data.descripcion}" readonly="true">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm mb-2" name="serie_prueba_traido[]" placeholder="SERIE" value="" required="">
                    <input type="text" class="form-control form-control-sm mb-2" name="correlativo_prueba_traido[]" placeholder="CORRELATIVO" required="">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm mb-2" name="serie_produccion_traido[]" placeholder="SERIE" required="">
                    <input type="text" class="form-control form-control-sm mb-2" name="correlativo_produccion_traido[]" placeholder="CORRELATIVO" required="">
                </td>
                <td>
                    <a href="javascript:;"><i data-feather="trash" class="icon-sm me-2 text-danger"></i></a>
                </td>
            </tr>
            `;

        $("#bodyComprobantes").html(html);
    })
}

function ingresar_sede(e){
    const idsede = e.target.getAttribute('data-id');

    window.location.href = urlgeneral+"/home";
}
