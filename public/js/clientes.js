urlgeneral=$("#url_raiz_proyecto").val();

const tipo_documento = document.getElementById('documento_identidad');
const numero_documento = document.getElementById('numero_documento');
const btn_consultar = document.getElementById('btn_consultar');

window.addEventListener("load", function (event) {
    listadoclientes(); // Load active by default
    $(".loader").fadeOut("slow");
});

// Filter change
$("#estado_filter").on("change", function() {
    listadoclientes();
});

function listadoclientes() {
    const estado = $("#estado_filter").val();
    
    initDataTable("#datatable", {
        processing: true,
        serverSide: true,
        ajax: {
            url: urlgeneral + "/clientes/listado/" + estado,
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
            { data: "razon_social", className: "text-center" },
            { data: "tipo_doc", className: "text-center" },
            { data: "documento", className: "text-center" },
            { data: "dire_per", className: "text-center" },
            { data: "telefono", className: "text-center" },
            { 
                data: null, 
                orderable: false,
                searchable: false,
                className: "text-center",
                render: function (data, type, row) {
                    let btns = "";
                    
                    if (typeof canEdit !== 'undefined' && canEdit) {
                        btns += `<a type="button" href="${urlgeneral}/clientes/${row.id}/edit" class="btn btn-info waves-effect waves-light" title="Editar"><i class="fas fa-edit"></i> </a> `;
                    }

                    if (typeof canDelete !== 'undefined' && canDelete) {
                        if (row.estado_per == 1) {
                            btns += `<button type="button" onclick="anular(${row.id});" class="btn btn-danger waves-effect waves-light eliminar" title="Anular"><i class="fas fa-trash-alt eliminar"></i> </button>`;
                        } else {
                            btns += `<button type="button" onclick="activar(${row.id});" class="btn btn-warning waves-effect waves-light activar" title="Activar"><i class="fas fa-sync activar"></i> </button>`;
                        }
                    }
                    
                    return btns;
                }
            }
        ],
        order: [[1, 'asc']] // Sort by Razon Social by default
    });
}

function anular(id) {
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
            var csrf = document.querySelector('meta[name="csrf-token"]').content;
            $.ajax({
                type: "POST",
                url: urlgeneral + "/clientes/eliminar/" + id,
                data: { "_method": "delete", '_token': csrf },
                success: function (data) {
                    if (data.status === 'error') {
                        Swal.fire('Atención', data.mensaje, 'error');
                    } else {
                        Swal.fire(
                            'Anulado!',
                            'El cliente fue Anulado Correctamente.',
                            'success'
                        );
                        // Reload table without full page refresh
                        $('#datatable').DataTable().ajax.reload(null, false);
                    }
                }
            });
        }
    });
}

function activar(id) {
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
            var csrf = document.querySelector('meta[name="csrf-token"]').content;
            $.ajax({
                type: "POST",
                url: urlgeneral + "/clientes/activar/" + id,
                data: { "_method": "delete", '_token': csrf },
                success: function (data) {
                    Swal.fire(
                        'Activado!',
                        'El cliente fue Activado Correctamente.',
                        'success'
                    );
                    // Reload table without full page refresh
                    $('#datatable').DataTable().ajax.reload(null, false);
                }
            });
        }
    });
}
