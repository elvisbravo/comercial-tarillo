const urlgeneral = document.getElementById('url_raiz_proyecto').value

const add = document.getElementById('btnadd');
const form = document.getElementById('form_concepto');

const tipo_m = document.getElementById('tipo_movimiento');
const concepto = document.getElementById('descripcion');

$("#dataTableExample").DataTable();

render();

add.addEventListener('click', (e) => {
    $("#modal_concepto").modal('show');
    document.getElementById('idconcepto').value = 0;
    tipo_m.value = "";
    concepto.value = "";
});

form.addEventListener('submit', (e) => {
    e.preventDefault();

    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    const formData = new FormData(form);

    formData.append('_token', csrf);

    fetch(urlgeneral+"/conceptos/guardar", {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.respuesta == "ok") {
            Swal.fire({
                position: 'top-center',
                icon: 'success',
                title: data.mensaje,
                showConfirmButton: false,
                timer: 2500
            });

            $("#modal_concepto").modal('hide');

            render();

        } else {
            Swal.fire({
                position: 'top-center',
                icon: 'danger',
                title: 'Ocurrio un error',
                showConfirmButton: false,
                timer: 2500
            })
        }
    })
})

function render() {
    fetch(urlgeneral+"/conceptos/listado")
    .then(res => res.json())
    .then(data => {

        const lista = document.getElementById('renderConceptos');

        let html = "";

        data.forEach((concepto,index) => {
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${concepto.descripcion}</td>
                    <td>${concepto.tipo_movimiento}</td>
                    <td>
                        <button type="button" class="btn btn-info" onclick="editar(${concepto.id},'${concepto.descripcion}','${concepto.tipo_movimiento}')"><i class="fa fa-edit"></i></button>
                    </td>
                </tr>
            `;
        });

        $("#dataTableExample").DataTable().destroy();        
        lista.innerHTML = html;
        $("#dataTableExample").DataTable();
    })
}

function editar(id,descripcion,tipo_movimiento) {
    $("#modal_concepto").modal('show');

    tipo_m.value = tipo_movimiento;
    concepto.value = descripcion;

    document.getElementById('idconcepto').value = id;
}