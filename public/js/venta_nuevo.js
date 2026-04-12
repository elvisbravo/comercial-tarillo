window.onload = () => {
    validar_caja();
}

const urlgeneral = document.getElementById('url_raiz_proyecto').value;

const tipo_venta = document.getElementById('tipo_venta');
const buscar_producto = document.getElementById('buscar_producto');
const render = document.getElementById('render_productos');
const details = document.getElementById('content_detalle');

const form = document.getElementById('form_venta');

const tipo_documento = document.getElementById('documento_identidad');
const numero_documento = document.getElementById('numero_documento');
const btn_consultar = document.getElementById('btn_consultar');

const total_recibido = document.getElementById('total_recibido');
const vuelto = document.getElementById('vuelto');

const descuento_total = document.getElementById('descuentos');

mostrar_productos_tipo_venta();

function mostrar_productos_tipo_venta(){

    render.innerHTML = `<img src="${urlgeneral}/public/img/loader-meta.gif" alt="PRODUCTOS" />`;

    fetch(urlgeneral+"/render-productos-tipo-venta")
    .then(res => res.json())
    .then(data => {
        view_render_productos(data);
    })

}

function search_productos_tipo_venta(search){
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const formData = new FormData();
    formData.append('buscar',search);
    formData.append('_token', csrf);

    fetch(urlgeneral+"/search-productos-tipo-venta",{
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        view_render_productos(data);
    })
}

function view_render_productos(data){
    let precio_venta;
    let descuento;

    let html = "";

    data.forEach(prod => {
        if (tipo_venta.value == 1) {
            precio_venta = prod.precio_contado;
            descuento = prod.descuento_contado;
        } else {
            precio_venta = prod.precio_credito;
            descuento = prod.descuento_credito;
        }

        html += `
        <div class="col-xl-3 col-6 col-sm-4 col-lg-4" onclick="modalProducto(${prod.producto_id},'${prod.nombre_producto}',${precio_venta},${prod.stock},${descuento})">

            <div class="card shadow rounded card-producto">
                <img class="card-img-top img-fluid imagen-producto" src="http://localhost/es_restaurante/public/img/default.jpg" alt="Card image cap">
                <div class="card-body p-1">
                    <h4 class="card-title text-center">${prod.nombre_producto}</h4>
                    <p class="text-warning mb-1 ml-3">S/ ${precio_venta} <strong class="text-right">${prod.stock}</strong> </p>
                </div>
            </div>

        </div>
        `;

    });

    render.innerHTML = html;
}

function modalProducto(id,name,precio,stock,descuento){
    $("#modal_producto").modal('show');
    const titleModal = document.getElementById('titleModal');
    const price_producto = document.getElementById('price-producto');
    const idproducto = document.getElementById('idProducto');
    const stock_input = document.getElementById('stock_producto');
    const modal_descuento = document.getElementById('modal_descuento');

    titleModal.textContent = name;
    price_producto.value = precio;
    idproducto.value = id;
    stock_input.textContent = stock;
    modal_descuento.value = descuento;

    $("#cantidad_producto").TouchSpin({
        initval: 1
    });

    $("#cantidad_producto").val(1);
}

function agregar_detalle(){
    const precio = document.getElementById('price-producto');
    const name = document.getElementById('titleModal');
    const quanty = document.getElementById('cantidad_producto');
    const idp = document.getElementById('idProducto');
    const stock = document.getElementById('stock_producto');
    const desc = document.getElementById('modal_descuento');

    const cantidad_producto = parseInt(quanty.value);
    const stock_producto = parseInt(stock.textContent);

    if (cantidad_producto > stock_producto) {
        alert('La cantidad no debe superar al stock del producto');
        return false;
    }

    const price = parseFloat(precio.value);

    const importe = price * parseInt(quanty.value);

    let html = `
        <tr>
            <input type="hidden" name="idproducto[]" value="${idp.value}">
            <input type="hidden" name="cantidad[]" value="${quanty.value}">
            <input type="hidden" name="precio[]" value="${price.toFixed(2)}">
            <input type="hidden" class="descuento" name="descuento" value="${desc.value}">
            
            <td>${quanty.value}</td>
            <td>${name.textContent}</td>
            <td>${price.toFixed(2)}</td>
            <td class="importe">${importe.toFixed(2)}</td>
            <td><i class="mdi mdi-delete text-danger fs-4"></i></td>
        </tr>
    `;

    $("#content_detalle").prepend(html);

    $("#modal_producto").modal('hide');

    suma_total();

}

function suma_total(){
    const importes = document.getElementsByClassName('importe');
    const total_text = document.getElementById('total_text');
    const total_num = document.getElementById('total_num');
    const descuento = document.getElementsByClassName('descuento');
    const total_d = document.getElementById('total_descuentos');
    const subtotal = document.getElementById('subtotal');
    const subtotal_text = document.getElementById('subtotal_text');

    const descuentos = document.getElementById('descuentos');

    let suma = 0;
    let descu = 0;
    
    importes.forEach(imp => {
        let importe = parseFloat(imp.textContent);

        suma += importe;
    });

    document.getElementById('totales').value = suma.toFixed(2);
    subtotal.value = suma.toFixed(2);
    subtotal_text.textContent = suma.toFixed(2);

    let suma_total = parseFloat(suma) - parseFloat(descuentos.value);

    descuento.forEach(desc => {
        let sum_desc = parseFloat(desc.value);

        descu += sum_desc;
    });

    total_text.textContent = suma_total.toFixed(2);
    total_num.value = suma_total.toFixed(2);
    total_recibido.value = suma_total.toFixed(2);
    total_d.value = descu.toFixed(2);

    if (tipo_venta.value == 2) {
        traer_candado(suma_total);
    }
}

tipo_venta.addEventListener('change', (e) => {

    mostrar_productos_tipo_venta();

    const datos_cred = document.querySelectorAll('.datos_credito');

    if (e.target.value == 1) {
        document.getElementById('tipo_comprobante').value = 1;
        datos_cred.forEach(cred => {
            cred.style.display = 'none';
        });
    } else {
        document.getElementById('tipo_comprobante').value = 5;
        datos_cred.forEach(cred => {
            cred.style.display = 'revert';
        });
    }

});

details.addEventListener('click', (e) => {
    if (e.target.classList.contains('mdi-delete')) {
        e.target.parentElement.parentElement.remove();
        suma_total();
    }
});

buscar_producto.addEventListener('keyup', (e) => {
    const search = e.target.value;

    search_productos_tipo_venta(search);
});

form.addEventListener('submit', (e) => {
    e.preventDefault();

    const descuentos = document.getElementById('descuentos');
    const total_descuentos = document.getElementById('total_descuentos');

    if (descuentos.value > total_descuentos.value) {
        alert('Solo puede descontar hasta '+total_descuentos.value);
        return false;
    }

    Swal.fire({
        title: 'Necesitamos de tu Confirmación',
        text: "Se creará el comprobante!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, Adelante!'
    }).then((result) => {
        if (result.isConfirmed) {
            $("#modal_enviar").modal('show');
            const csrf = document.querySelector('meta[name="csrf-token"]').content;

            const formData = new FormData(form);

            formData.append('_token', csrf);

            fetch(urlgeneral+"/generar_venta",{
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                $("#modal_enviar").modal('hide');
                if (data.respuesta == "ok") {
                    window.location.href = urlgeneral+"/ventas";
                    window.open(urlgeneral+"/venta/ticket/"+data.id);
                } else {
                    alert(data.mensaje);
                }
            })
        }
    })

});

btn_consultar.addEventListener('click', (e) => {
    const t_documento = tipo_documento.value;
    const numero = numero_documento.value;

    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    const formData = new FormData();

    formData.append('tipo_documento',t_documento);
    formData.append('num_doc',numero);
    formData.append('_token', csrf);

    fetch(urlgeneral+"/consultar_dni_ruc",{
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        const nombre = document.getElementById('name_cliente');
        const street = document.getElementById('street');

        if (data.original) {
            if (t_documento == 1) {
                nombre.value = data.original.nombres + " " + data.original.apellidoPaterno + " " + data.original.apellidoMaterno;
            }

            if (t_documento == 6) {
                nombre.value = data.original.razonSocial;
                street.value = data.original.direccion;
            }

        } else {
            alert('no fue encontrado');
        }

    })

});

total_recibido.addEventListener('keyup', (e) => {
    const total_num = document.getElementById('total_num');

    const valor = parseFloat(e.target.value);

    let vuelto_dar = valor - parseFloat(total_num.value);

    vuelto.value = vuelto_dar.toFixed(2);
});

descuento_total.addEventListener('keyup', (e) => {
    const valor = parseFloat(e.target.value);

    const totales = document.getElementById('totales');
    const total_n = document.getElementById('total_num');
    const total_text = document.getElementById('total_text');

    const total_venta = parseFloat(totales.value) - valor;

    total_text.textContent = total_venta.toFixed(2);
    total_n.value = total_venta.toFixed(2);
})

function traer_candado(monto) {
    fetch(urlgeneral+'/traer_candado/'+monto)
    .then(res => res.json())
    .then(data => {
        const monto_inicial = document.getElementById("monto_inicial");
        monto_inicial.textContent = data.monto_inicial;

        const meses = document.getElementById('meses_credito');
        meses.textContent = data.nmeses;
    })
}


function validar_caja() {
    fetch(urlgeneral+'/caja/validar')
    .then(res => res.json())
    .then(data => {
        if (data.status == 1) {
            $("#modal_caja").modal('show');
            document.getElementById('mensaje_caja').textContent = data.mensaje;
            document.getElementById('ir_caja').setAttribute('href',urlgeneral+'/caja');
        }
        
    })
}

const lista = ['opción 1', 'opción 2', 'opción 3', 'opción 4'];

// Obtener el elemento input y asignarle un event listener que se active al escribir en él
const filtrar = document.getElementById('autocomplete-list')
numero_documento.addEventListener('input', mostrarSugerencias);

function mostrarSugerencias() {
  // Obtener el valor ingresado en el input
  const val = numero_documento.value;

  // Filtrar la lista de opciones y obtener solo aquellas que coinciden con el valor ingresado
  const sugerencias = lista.filter(opcion => opcion.startsWith(val));

  filtrar.innerHTML = "";

  // Mostrar las sugerencias debajo del input
  sugerencias.forEach(opcion => {
    const div = document.createElement('div');
    div.textContent = opcion;
    div.addEventListener('click', () => {
      numero_documento.value = opcion;
      filtrar.remove();
    });
    filtrar.appendChild(div);
  });
}