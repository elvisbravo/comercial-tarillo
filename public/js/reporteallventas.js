urlgeeneral = $("#url_raiz_proyecto").val();
$("#actualizar").hide();

//FUNCION LOAD
window.addEventListener("load", function (event) {
    tipo_comprobante();
    $(".loader").fadeOut("slow");
});

//mostrar tipo comprobante
function tipo_comprobante() {
    $.get(urlgeeneral + "/reporteventas/tipocomprobantes", function (data) {
        let contenido = "";
        //contenido += "<option value='' >--Seleccionar--</option >";
        contenido += "<option value='0' id='todos'>TODOS</option >";
        for (var i = 0; i < data.length; i++) {
            contenido +=
                "<option value='" +
                data[i].id +
                "' >" +
                data[i].descripcion +
                "</option >";
        }

        document.getElementById("tipo_comprobante_id").innerHTML = contenido;
    });
}

const form = document.getElementById('formReporteVentas');

const csrf = document.querySelector('meta[name="csrf-token"]').content;

const btnGenerar = document.getElementById('generar_reporte');

form.addEventListener('submit', (e) => {
    e.preventDefault();

    btnGenerar.disabled = true;
    btnGenerar.textContent = 'Generando reporte...';

    const formData = new FormData(form);
    formData.append('_token', csrf);

    fetch(urlgeeneral+"/reporteallventas/consulta", {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        btnGenerar.disabled = false;
        btnGenerar.textContent = 'GENERAR REPORTE';

        const lista = document.getElementById('listadoVentas');

        let ventas = "";

        $("#datatable").DataTable();

        data.forEach(venta => {
            let estado = "";
            let fecha_eliminacion = "";
            let serie_nota = "";
            let numero_nota = "";

            if (venta.aceptado_sunat == 1) {
                estado = "ACEPTADO";
            } else {
                estado = "PENDIENTE";
            }

            if (venta.tipo_comprobante_id == 3) {
                if (venta.fecha_eliminacion != null) {
                    fecha_eliminacion = venta.fecha_eliminacion;
                }
    
                if (venta.serie_nota_credito != null) {
                    serie_nota = venta.serie_nota_credito;
                }
    
                if (venta.numero_nota_credito != null) {
                    numero_nota = venta.numero_nota_credito;
                }
            }

            ventas += `
                <tr>
                    <td>${venta.fecha}</td>
                    <td>${venta.fecha}</td>
                    <td>${venta.comprobante}</td>
                    <td>${venta.serie_comprobante}</td>
                    <td>${venta.numero_comprobante}</td>
                    <td>${venta.documento}</td>
                    <td>${venta.razon_social}</td>
                    <td>0.00</td>
                    <td>${venta.monto}</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>${venta.monto}</td>
                    <td>S/</td>
                    <td>${fecha_eliminacion}</td>
                    <td></td>
                    <td>${serie_nota}</td>
                    <td>${numero_nota}</td>
                    <td>${estado}</td>
                </tr>
            `;
        });

        $("#datatable").DataTable().destroy();

        lista.innerHTML = ventas;

        let dtButtons = [];
        if (typeof canPrint !== 'undefined' && canPrint) {
            dtButtons.push({
                extend: 'excelHtml5',
                text: 'Exportar a Excel',
                className: 'btn btn-success'
            });
        }

        $("#datatable").DataTable({
            order: [],
            dom: 'Bfrtip',
            buttons: dtButtons,
            language: {
                "decimal": "",
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
                "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
                "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Entradas",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Ultimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            }
        });
    })
})
