const urlgeneral = document.getElementById('url_raiz_proyecto').value

const add = document.getElementById('btnadd');
const form = document.getElementById('form_concepto');

const tipo_m = document.getElementById('tipo_movimiento');
const concepto = document.getElementById('descripcion');

initDataTable("#dataTableExample", {
    processing: true,
    serverSide: true,
    ajax: {
        url: urlgeneral + "/conceptos/listado",
        type: "GET"
    },
    columns: [
        { 
            data: null, 
            orderable: false,
            searchable: false,
            className: "text-center",
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            } 
        },
        { data: "descripcion", className: "text-center" },
        { data: "tipo_movimiento", className: "text-center" },
        { 
            data: null, 
            orderable: false,
            searchable: false,
            className: "text-center",
            render: function (data, type, row) {
                let btns = "";
                
                if (typeof canEdit !== 'undefined' && canEdit) {
                    btns += `<button type="button" class="btn btn-info waves-effect waves-light" onclick="editar(${row.id},'${row.descripcion}','${row.tipo_movimiento}')" title="Editar"><i class="fa fa-edit"></i></button> `;
                }
                
                return btns;
            }
        }
    ],
    order: [[1, 'asc']],
    responsive: true,
    autoWidth: false
});

function render() {
    $('#dataTableExample').DataTable().ajax.reload(null, false);
}

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

    fetch(urlgeneral + "/conceptos/guardar", {
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
                icon: 'error',
                title: 'Ocurrió un error',
                showConfirmButton: false,
                timer: 2500
            })
        }
    })
})

function editar(id,descripcion,tipo_movimiento) {
    $("#modal_concepto").modal('show');

    tipo_m.value = tipo_movimiento;
    concepto.value = descripcion;

    document.getElementById('idconcepto').value = id;
}