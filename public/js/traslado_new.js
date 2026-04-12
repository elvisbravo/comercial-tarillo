const urlgeneral = document.getElementById('url_raiz_proyecto').value;

const stock = document.getElementById('stock_producto');
const idpro = document.getElementById('idproducto');
const nombre = document.getElementById('nombre_producto');
const cantidad = document.getElementById('cantidad');

const btnAdd = document.getElementById('agregar_detalle_traslado');
const detalleTraslado = document.getElementById('detalleTraslado');

const form = document.getElementById('form_traslado');

if ($(".js-example-basic-single").length) {
    $("#item_productos").select2({
        ajax: {
            url: urlgeneral + "/traslado/traer_productos",
            type: "post",
            dataType: "json",
            placeholder: "Buscar productos",
            maximumSelectionLength: 10,
      
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page,
                    id: $("#almacen_origen").val(),
                    _token: document.querySelector('meta[name="csrf-token"]').content
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
    }).on('change', function(e) {
        data_producto(this.value);
    });
}

btnAdd.addEventListener('click', (e) => {
    let html = `
    <tr>
        <input type="hidden" name="producto_id[]" value="${idpro.value}">
        <input type="hidden" name="cantidad[]" value="${cantidad.value}">
        <td>${nombre.value}</td>
        <td>${cantidad.value}</td>
        <td>
            <button type="button" class="btn btn-danger btn-sm btndelete">Eliminar</button>
        </td>
    </tr>
    `;

    $("#detalleTraslado").append(html);

    idpro.value = "";
    nombre.value = "";
    stock.value = "";
    cantidad.value = "";

    $("#item_productos").val('').trigger('change')
});

detalleTraslado.addEventListener('click', (e) => {
    
    if (e.target.classList.contains('btndelete')) {
        e.target.parentElement.parentElement.remove();
    }

});

form.addEventListener('submit', (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    formData.append('_token', csrf);

    fetch(urlgeneral+"/traslado/guardar",{
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.respuesta == "ok") {
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: data.mensaje,
                showConfirmButton: false,
                timer: 1500
            })

            setTimeout( function(){
                window.location.href = urlgeneral+"/traslados";
            }, 1500)

        } else {
            Swal.fire(
                'Advertencia!',
                data.mensaje,
                'error'
            )
        }
    })

})

function data_producto(id){

    const idalmacen = document.getElementById('almacen_origen');
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    const formData = new FormData();

    formData.append('idproducto',id);
    formData.append('idalmacen',idalmacen.value);
    formData.append('_token', csrf);

    fetch(urlgeneral+"/traslado/data_producto",{
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        
        if (data == "") {
            idpro.value = "";
            stock.value = "";
            nombre.value = "";
            cantidad.value = "";

            return false;
        }

        stock.value = data.stock;
        idpro.value = data.idpro;
        nombre.value = data.nomb_pro;

    })
}
