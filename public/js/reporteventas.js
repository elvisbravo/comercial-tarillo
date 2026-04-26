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
        contenido += `
            <li>
                <div class="form-check dropdown-item px-4 py-1">
                    <input class="form-check-input check-comprobante" type="checkbox" name="tipo_comprobante[]" value="0" id="comp_todos" checked>
                    <label class="form-check-label w-100" style="cursor: pointer;" for="comp_todos">TODOS</label>
                </div>
            </li>
        `;
        for (var i = 0; i < data.length; i++) {
            contenido += `
            <li>
                <div class="form-check dropdown-item px-4 py-1">
                    <input class="form-check-input check-comprobante" type="checkbox" name="tipo_comprobante[]" value="${data[i].id}" id="comp_${data[i].id}">
                    <label class="form-check-label w-100" style="cursor: pointer;" for="comp_${data[i].id}">${data[i].descripcion}</label>
                </div>
            </li>
            `;
        }

        document.getElementById("tipo_comprobante_id").innerHTML = contenido;

        // Evitar que el menú se cierre al hacer clic en los checkboxes
        $('#tipo_comprobante_id').on('click', function (e) {
            e.stopPropagation();
        });

        // Lógica de los checks (Si marca 'TODOS', desmarca los demás y viceversa)
        $('.check-comprobante').on('change', function() {
            if ($(this).val() == "0" && $(this).is(':checked')) {
                $('.check-comprobante').not(this).prop('checked', false);
            } else if ($(this).val() != "0" && $(this).is(':checked')) {
                $('#comp_todos').prop('checked', false);
            }
            
            // Si ninguno queda seleccionado, marcamos TODOS por defecto
            if ($('.check-comprobante:checked').length === 0) {
                 $('#comp_todos').prop('checked', true);
            }
            
            // Actualizar texto del botón desplegable
            let selectedTxt = [];
            $('.check-comprobante:checked').each(function() {
                selectedTxt.push($(this).next('label').text());
            });
            $('#dropdownComprobantes').text(selectedTxt.join(', '));
        });

        $('#dropdownComprobantes').text('TODOS');
    });
}

// Lógica para Sedes (se carga directamente porque Blade la dibuja desde el servidor)
window.addEventListener("load", function () {
    // Evitar que se cierre el dropdown al hacer click en las opciones
    $('#sede_id_list').on('click', function (e) {
        e.stopPropagation();
    });

    // Lógica principal de los Sede Checkboxes
    $('.check-sede').on('change', function() {
        if ($(this).val() == "1" && $(this).is(':checked')) { // "1" es TODOS
            $('.check-sede').not(this).prop('checked', false);
        } else if ($(this).val() != "1" && $(this).is(':checked')) {
            $('#sede_1').prop('checked', false);
        }
        
        // Si no queda nada seleccionado, marcamos TODOS por defecto
        if ($('.check-sede:checked').length === 0) {
             $('#sede_1').prop('checked', true);
        }
        
        // Actualizamos el boton principal
        let selectedTxt = [];
        $('.check-sede:checked').each(function() {
            selectedTxt.push($(this).next('label').text());
        });
        $('#dropdownSedes').text(selectedTxt.join(', '));
    });
});

const form = document.getElementById('formReporteVentas');

const csrf = document.querySelector('meta[name="csrf-token"]').content;

const btnGenerar = document.getElementById('generar_reporte');

form.addEventListener('submit', (e) => {
    e.preventDefault();

    btnGenerar.disabled = true;
    btnGenerar.textContent = 'Generando reporte...';

    const formData = new FormData(form);
    formData.append('_token', csrf);

    fetch(urlgeeneral+"/reporteventas/consulta", {
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
