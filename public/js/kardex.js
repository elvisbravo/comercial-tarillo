window.addEventListener("load", function (event) {
    $(".loader").fadeOut("slow");
});

const urlgeneral = document.getElementById("url_raiz_proyecto").value;

const form = document.getElementById("form_kardex");

const tabla = document.getElementById("contentKardex");

$("#item_productos").select2({
    ajax: {
        url: urlgeneral + "/kardex/traer_productos",
        type: "post",
        dataType: "json",
        placeholder: "Buscar productos",
        maximumSelectionLength: 10,

        data: function (params) {
            return {
                q: params.term, // search term
                page: params.page,
                _token: document.querySelector('meta[name="csrf-token"]')
                    .content,
            };
        },
        processResults: function (data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            return {
                results: data.results,
            };
        },
        cache: true,
        // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
    },
    escapeMarkup: function (markup) {
        return markup;
    }, // let our custom formatter work
    //templateResult: formatRepo, // omitted for brevity, see the source of this page
    //templateSelection: formatRepoSelection, // omitted for brevity, see the source of this page
    language: {
        noResults: function () {
            return "No hay resultado";
        },
        searching: function () {
            return "Buscando..";
        },
    },
});

const almacenSelect = document.getElementById("almacen");
const ubicacionSelect = document.getElementById("ubicacion");

almacenSelect.addEventListener("change", function () {
    const almacen_id = this.value;

    if (almacen_id === "") {
        ubicacionSelect.innerHTML =
            '<option value="">Seleccione un almacén primero</option>';
        ubicacionSelect.disabled = true;
        return;
    }

    ubicacionSelect.innerHTML = '<option value="">Cargando...</option>';
    ubicacionSelect.disabled = true;

    fetch(`${urlgeneral}/kardex/traer_ubicaciones/${almacen_id}`)
        .then((res) => res.json())
        .then((data) => {
            let options =
                '<option value="todas">Todas las ubicaciones</option>';
            data.forEach((ubicacion) => {
                options += `<option value="${ubicacion.id}">${ubicacion.name}</option>`;
            });
            ubicacionSelect.innerHTML = options;
            ubicacionSelect.disabled = false;
        })
        .catch((err) => {
            console.error(err);
            ubicacionSelect.innerHTML =
                '<option value="">Error al cargar ubicaciones</option>';
        });
});

form.addEventListener("submit", (e) => {
    e.preventDefault();

    const btnGenerar = document.getElementById("btnGenerarKardex");
    const originalText = btnGenerar.innerHTML;

    // Deshabilitar botón y cambiar texto
    btnGenerar.disabled = true;
    btnGenerar.innerHTML =
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Consultando kardex...';

    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    const formData = new FormData(form);

    formData.append("_token", csrf);

    fetch(urlgeneral + "/kardex/guardar", {
        method: "POST",
        body: formData,
    })
        .then((res) => res.json())
        .then((data) => {
            let html = "";

            data.forEach((kardex, index) => {
                let cantidad_entrada = 0;
                let cantidad_salida = 0;
                let descripcion = "";

                if (kardex.tipo == 1) {
                    cantidad_entrada = kardex.cantidad_unitaria;
                    descripcion = `COMPRA ${kardex.serie_comprobante}-${kardex.correlativo_comprobante}`;
                } else {
                    cantidad_salida = kardex.cantidad_unitaria;
                    descripcion = `VENTA ${kardex.serie_comprobante}-${kardex.correlativo_comprobante}`;
                }

                html += `
            <tr>
                <td>${data.length - index}</td>
                <td>${kardex.fecha}</td>
                <td>
                    <span class="badge bg-info">${kardex.nombre_ubicacion || ""}</span>
                </td>
                <td>
                    ${descripcion}
                </td>
                <td>
                    ${cantidad_entrada}
                </td>
                <td>
                    ${cantidad_salida}
                </td>
                <td>
                    ${kardex.cantidad_total}
                </td>
            </tr>
            `;
            });

            tabla.innerHTML = html;
        })
        .finally(() => {
            // Restaurar botón al finalizar
            btnGenerar.disabled = false;
            btnGenerar.innerHTML = originalText;
        });
});
