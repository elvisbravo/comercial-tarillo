window.onload = () => {
    //validar_caja();
}

const urlgeneral = document.getElementById('url_raiz_proyecto').value;

const tipo_venta = document.getElementById('tipo_venta');
const csrf = document.querySelector('meta[name="csrf-token"]').content;
const buscar = document.getElementById('buscar_producto');

const listaProductos = document.getElementById('listProducts');
const listCategories =document.getElementById('listCategories');

const numeroDocumento = document.getElementById('numeroDocumento');
const btnConsulta = document.getElementById('btnConsulta');
const tipoDocumentoIdentidad = document.getElementById('tipoDocumentoIdentidad');
const documento = document.getElementById('documento');
const nameCustomer = document.getElementById('nombre_cliente');
const addressCustomer = document.getElementById('direccion_cliente');
const phoneCustomer = document.getElementById('celular_cliente');
const emailCustomer = document.getElementById('email_cliente');

const forma_pago = document.getElementById('forma_pago');
const banco_venta = document.getElementById('banco_venta');
const addParticionado = document.getElementById('addParticionado');
const forma_pago_particionado = document.getElementById('forma_pago_particionado');
const listPagoParticionado = document.getElementById('listPagoParticionado');
const total_recibido = document.getElementById('total_recibido');

const guardarVenta = document.getElementById('btnGuardarVenta');

const select_almacen = document.getElementById('almacen');
const select_ubicacion = document.getElementById('ubicacion');

const modifPrecio = document.getElementById('modificarPrecio');

//para eliminar el footer del document
const footer = document.getElementsByClassName('footer');
footer[0].remove();

const miSpinner = document.getElementById('mi-spinner');

//para quitar el paddin bottom del content a 0px
const pageContent = document.getElementsByClassName('page-content');
pageContent[0].style.paddingBottom = '0';

function open_modal_cobrar() {
    const tbody = document.getElementById('contentCarrito');
    const rows = tbody.querySelectorAll('tr');

    if (rows.length == 0) {
        alert('No tiene agregado ningún producto');
        return false;
    }

    $("#view_modal_cobrar").modal('show');
}


renderCategorias();
traerComprobantesVenta();
tipo_documento_identidad();
forma_pago_venta();
bancos_venta();
formaPagoParticionado();

render_productos();

function input_spinner(id, stock) {
    $("#sumRes"+id).TouchSpin({
        min: 1,
        max: stock,
        boostat: 5
    }).on("touchspin.on.startspin", function() {
        const quanty = document.getElementById('sumRes'+id);
        addQuanty(quanty.value,id);
    })
}

function addQuanty(quanty, id) {

    const namesPro = document.querySelectorAll('[name="idproducto[]"]');

    let contarsi = 0;

    namesPro.forEach(pro => {
        if (pro.value == id) {
            contarsi = 1;
        }
    });

    if (contarsi == 0) {
        const name = document.getElementById('nameProduct'+id);
        const price = document.getElementById('priceProduct'+id);
        const ubicacion = document.getElementById('ubicacion'+id);
        const cant = document.getElementById('sumRes'+id);
        addCarrito(id, cant.value,name.value, price.value, ubicacion.value);
        return false;
    }
    
    const cant = document.getElementById('quanty'+id);
    const inputCant = document.getElementById('quantyPro'+id);
    const priceBtn = document.getElementById('pricePro'+id);
    const inputPrice = document.getElementById('priceproducto'+id);
    const importe = document.getElementById('importPro'+id);
    const inputImporte = document.getElementById('importe'+id);

    cant.textContent = quanty;
    inputCant.value = quanty;

    const imp = parseFloat(quanty)*parseFloat(inputPrice.value);

    importe.textContent = imp.toFixed(2);
    inputImporte.value = imp.toFixed(2);

    sumaTotal();
}

function glide_categorias() {
    //para las categorias
    new Glide('#glide3', {
        type: 'carousel',
        startAt: 0,
        //autoplay: 3000,
        hoverpause: true,
        gap: 20,
        // animationTimingFunc: ease,
        perView: 5,
        breakpoints: {
            800: {
                perView: 4
            },
            600: {
                perView: 2
            }
        }
    }).mount();
}

function render_productos() {
    const detalleCredito = document.getElementById('detalleCredito');
    const resumenVenta = document.getElementById('resumenVenta');

    const typeVenta = document.getElementById('typeVenta');
    typeVenta.value = tipo_venta.value;

    if (tipo_venta.value == 1) {
        detalleCredito.classList.add('d-none');
        resumenVenta.classList.remove('col-md-6');
        resumenVenta.classList.add('col-md-12');
    } else {
        detalleCredito.classList.remove('d-none');
        resumenVenta.classList.add('col-md-6');
        resumenVenta.classList.remove('col-md-12');
    }

    //miSpinner.style.display = 'block';

    const formData = new FormData();
    formData.append('buscar_producto', buscar.value);
    formData.append('ubicacion', select_ubicacion.value);
    
    formData.append('_token', csrf);
    fetch(urlgeneral+"/render-productos-tipo-venta",{
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        //miSpinner.style.display = 'none';

        let html = "";
        let precio;

        data.forEach(pro => {

            if (tipo_venta.value == 1) {
                precio = pro.precio_contado;
            } else {
                precio = pro.precio_credito;
            }
            html += `
            <div class="col-md-3 col-6">
                <div class="card">
                    <img class="card-img-top img-fluid" src="img/productos/${pro.img}" alt="Card image cap" style="height: 105px">
                    <div class="card-body p-1">
                        <p class="text-center mb-1 mt-1 fw-bold">${pro.nomb_pro}</p>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-12">
                                <input type="hidden" id="ubicacion${pro.id}" value="${pro.ubicacion_id}">
                                <input type="hidden" id="stockPro${pro.id}" value="${pro.stock}">
                                <input type="hidden" id="nameProduct${pro.id}" value="${pro.nomb_pro}">
                                <input type="hidden" id="priceProduct${pro.id}" value="${precio}">
                                <h5 class="card-title text-center">Stock: ${pro.stock}</h5>
                                <h4 class="card-title text-center text-primary">S/${precio}</h4>
                            </div>

                        </div>

                        <div class="d-grid" id="viewBtnSpinner${pro.id}">
                            <button type="button" class="btn btn-primary agregarCarrito" data-id="${pro.id}">AGREGAR</button>
                        </div>

                        <!--<input type="text" class="form-control quanty-product" value="1">-->

                    </div>
                </div>
            </div>
            `;    
        });

        listaProductos.innerHTML = html;

    })
}

function renderCategorias() {
    fetch(urlgeneral+"/render-categorias-productos")
    .then(res => res.json())
    .then(data => {
        let html = "";

        data.forEach(category => {
            html += `
            <li class="glide__slide categorias">
                <!--<a href="/">
                    <img src="img/categories/cocina.png" alt="">
                </a>-->
                <a href="#">${category.categoria}</a>
            </li>
            `;
        });

        listCategories.innerHTML = html;

        glide_categorias();
    })
}

buscar.addEventListener('keyup', render_productos);
tipo_venta.addEventListener('change', (e) => {
    document.getElementById('contentCarrito').innerHTML = "";
    render_productos();
    sumaTotal();
});

listaProductos.addEventListener('click', (e) => {
    if (e.target.classList.contains('agregarCarrito')) {
        const idp = e.target.getAttribute('data-id');
        
        const stock = document.getElementById('stockPro'+idp);

        if (parseInt(stock.value) == 0) {
            alert('No cuenta con stock');
            return false;
        }

        const viewSpinner = document.getElementById('viewBtnSpinner'+idp);

        viewSpinner.innerHTML = `<input type="text" class="form-control quanty-product" id="sumRes${idp}" value="1">`;

        input_spinner(idp,stock.value);

        const nameProduct = document.getElementById('nameProduct'+idp);
        const priceProduct = document.getElementById('priceProduct'+idp);
        const ubicacionId = document.getElementById('ubicacion'+idp);

        if (document.getElementById("quantyPro"+idp)) {
            const valor = document.getElementById("quantyPro"+idp);
            const inputCan = document.getElementById('sumRes'+idp);

            inputCan.value = valor.value;

        } else {
            addCarrito(idp, 1,nameProduct.value, priceProduct.value, ubicacionId.value);
        }

    }
})

function addCarrito(id, cantidad, name, price, ubicacion) {

    const importe = parseFloat(cantidad*price).toFixed(2);

    const rowCarrito = `
        <tr>
            <input type="hidden" name="ubicacion[]" id="ubicacionId${id}" value="${ubicacion}" />
            <input type="hidden" name="idproducto[]" id="idproducto${id}" value="${id}" />
            <input type="hidden" name="quanty[]" id="quantyPro${id}" value="${cantidad}" />
            <input type="hidden" name="nameproducto[]" id="nameproducto${id}" value="${name}" />
            <input type="hidden" name="priceproducto[]" id="priceproducto${id}" value="${price}" />
            <input type="hidden" name="importe[]" id="importe${id}" value="${importe}" />

            <td width="8%" class="cant" id="quanty${id}">${cantidad}</td>
            <td width="50%" class="namePro">${name}</td>
            <td width="15%">
                <button type="button" class="btn btn-outline-secondary pricePro" id="pricePro${id}" onclick="viewPrecios(${id})">${price}</button>
            </td>
            <td width="15%" class="importPro" id="importPro${id}">${importe}</td>
            <td width="10%">
                <button type="button" class="btn btn-outline-secondary btn-sm btn-delete-pedido" data-id="d">
                    <i class="bx bxs-trash-alt btn-delete-pedido" data-id="di"></i>
                </button>
            </td>
        </tr>
    `;

    $("#contentCarrito").append(rowCarrito);

    sumaTotal();
}

function viewPrecios(id) {
    $("#modalPrecios").modal('show');
    const formData = new FormData();
    formData.append('idproducto', id);
    formData.append('tipoVenta', tipo_venta.value);
    formData.append('_token', csrf);

    fetch(urlgeneral+'/traerPrecio', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        const dataPrecio = document.getElementById('data_precio');
        let precio;
        let title = "";
        if (tipo_venta.value == 1) {
            precio = data.precio_contado;
            title = "PRECIO AL CONTADO";
        } else {
            precio = data.precio_credito;
            title = "PRECIO AL CREDITO";
        }

        let html = `
        <thead>
            <input type="hidden" id="prop" value="${data.articulo_id}">
            <tr>
                <th>${title}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>${precio}</td>
                <td width="30%">
                    <input type="number" id="newPrecio" class="form-control" value="0.00">
                </td>
            </tr>
        </tbody>
        `;

        dataPrecio.innerHTML = html;
    })
}

modifPrecio.addEventListener('click', (e) => {
    $("#modalPrecios").modal('hide');
    const idproducto = document.getElementById('prop');
    const newPrecio = document.getElementById('newPrecio');

    const cantidad = document.getElementById('quantyPro'+idproducto.value);
    const price = document.getElementById('priceproducto'+idproducto.value);
    const importe = document.getElementById('importe'+idproducto.value);
    const textImport = document.getElementById('importPro'+idproducto.value);
    const textPrice = document.getElementById('pricePro'+idproducto.value);

    const newImporte = parseFloat(newPrecio.value)*parseFloat(cantidad.value);

    price.value = newPrecio.value;
    textPrice.textContent = newPrecio.value;
    importe.value = newImporte.toFixed(2);
    textImport.textContent = newImporte.toFixed(2);

    sumaTotal();
})

document.getElementById('contentCarrito').addEventListener('click', (e) => {
    if (e.target.classList.contains('btn-delete-pedido')) {
        const del = e.target.getAttribute('data-id');

        if (del == 'd') {
            e.target.parentElement.parentElement.remove();
        } else {
            e.target.parentElement.parentElement.parentElement.remove();
        }

        sumaTotal();
    }
})

function sumaTotal() {
    const cantidades = document.getElementsByClassName('cant');
    const precios = document.getElementsByClassName('pricePro');

    const totalbtn = document.getElementById('totalbtn');
    const subtotal = document.getElementById('subtotal');

    const subtotalventa = document.getElementById('subTotalVenta');
    const exonerada = document.getElementById('exonerada');
    const totalVenta = document.getElementById('totalVenta');
    const montoVenta = document.getElementById('montoVenta');

    let total = 0;

    for (let i = 0; i < cantidades.length; i++) {
        total += parseFloat(cantidades[i].textContent)*parseFloat(precios[i].textContent);
        
    }

    subtotal.textContent = total.toFixed(2);
    totalbtn.textContent = total.toFixed(2);

    subtotalventa.textContent = total.toFixed(2);
    exonerada.textContent = total.toFixed(2);
    totalVenta.textContent = total.toFixed(2);
    total_recibido.value = total.toFixed(2);
    montoVenta.value = total.toFixed(2);

    if (tipo_venta.value == 2) {
        const monto_inicial = document.getElementById('monto_inicial');
        const meses = document.getElementById('meses');

        fetch(urlgeneral+"/traer_candado/"+total)
        .then(res => res.json())
        .then(data => {
            console.log(data);
            monto_inicial.textContent = data.monto_inicial;
            meses.textContent = data.nmeses;
        })
    }
}

function traerComprobantesVenta() {
    fetch(urlgeneral+"/tipo-comprobantes-venta")
    .then(res => res.json())
    .then(data => {
        let html = "";
        let select;

        data.forEach(doc => {
            if (doc.id == 5) {
                select = "selected";
            } else {
                select = "";
            }
            html += `
                <option value="${doc.id}" ${select}>${doc.descripcion}</option>
            `;
        });

        documento.innerHTML = html;
    })
}

function tipo_documento_identidad() {
    fetch(urlgeneral+"/tipo-documento-identidad")
    .then(res => res.json())
    .then(data => {
        let html = "";

        data.forEach(doc_ident => {
            let selected;
            if (doc_ident.id == 1) {
                selected = 'selected';
            } else {
                selected = "";
            }

            html += `
                <option value="${doc_ident.id}" ${selected}>${doc_ident.nombre}</option>
            `;
        });

        tipoDocumentoIdentidad.innerHTML = html;
    })
}

function soloNumeros(e){
    var key = e.which || e.keyCode;

    // Permite ingresar solo números
    if (key < 48 || key > 57) {
        e.preventDefault();
    }
}

numeroDocumento.addEventListener('keypress', soloNumeros);

btnConsulta.addEventListener('click', (e) => {
    const numero_documento = numeroDocumento.value;
    const identidad = tipoDocumentoIdentidad.value;

    if (identidad == 1) {
        getDataDni(numero_documento);
    } else {
        if (identidad == 6) {
            getDataRuc(numero_documento);
        } else {
            alert('No se puede consultar ')
        }
    }
})

documento.addEventListener('change', (e) => {
    const valor = e.target.value;
    console.log(valor);

    if (valor === '2') {
        
        tipoDocumentoIdentidad.value = "6";
    } else {
        tipoDocumentoIdentidad.value = "1";
    }
}); 

function getDataDni(numero) {
    if (numero.length != 8) {
        return alert('Ingrese un dni con 8 dígitos');
    }

    const formData = new FormData();
    formData.append('num_doc', numero);
    formData.append('tipo_documento', 1);
    formData.append('_token', csrf);

    fetch(urlgeneral+"/consultar_dni_ruc",{
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.exception == "existe_base_datos") {
            nameCustomer.value = data.original.nombres;
            addressCustomer.value = data.original.direccion;
            phoneCustomer.value = data.original.celular;
        } else {
            nameCustomer.value = data.original.nombres+" "+data.original.apellidoPaterno+" "+data.original.apellidoMaterno;
        }
        
    })
}

function getDataRuc(numero) {
    if (numero.length != 11) {
        return alert('Ingrese un ruc con 11 dígitos');
    }

    const formData = new FormData();
    formData.append('num_doc', numero);
    formData.append('tipo_documento', 6);
    formData.append('_token', csrf);

    fetch(urlgeneral+"/consultar_dni_ruc",{
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.exception == "existe_base_datos") {
            nameCustomer.value = data.original.nombres;
            addressCustomer.value = data.original.direccion;
            phoneCustomer.value = data.original.celular;
        } else {
            nameCustomer.value = data.original.razonSocial;
            addressCustomer.value = data.original.direccion;
        }
        
    })
}

function forma_pago_venta() {
    fetch(urlgeneral+"/forma-pago")
    .then(res => res.json())
    .then(data => {
        let html = "";

        data.forEach(forma => {
            html += `
                <option value="${forma.id}">${forma.descripcion.toUpperCase()}</option>
            `;
        });

        forma_pago.innerHTML = html;
    })
}

forma_pago.addEventListener('change', (e) => {
    const forma = e.target.value;

    const efectivo = document.querySelectorAll('.efectivo');
    const pago_aplicativo = document.querySelectorAll('.pago-aplicativo');
    const pago_par = document.querySelectorAll('.pago-particionado');
    const classBancos = document.querySelector('.bancos');

    if (forma == 1) {
        for (let i = 0; i < efectivo.length; i++) {
            efectivo[i].classList.remove('d-none');
        }

        for (let j = 0; j < pago_aplicativo.length; j++) {
            pago_aplicativo[j].classList.add('d-none');
            
        }

        for (let k = 0; k < pago_par.length; k++) {
            pago_par[k].classList.add('d-none');
            
        }
    } else {
        if (forma == 2 || forma == 3 || forma == 4 || forma == 8) {
            for (let i = 0; i < efectivo.length; i++) {
                efectivo[i].classList.add('d-none');
            }
    
            for (let j = 0; j < pago_aplicativo.length; j++) {
                pago_aplicativo[j].classList.remove('d-none');
                
            }
    
            for (let k = 0; k < pago_par.length; k++) {
                pago_par[k].classList.add('d-none');
                
            }

            classBancos.classList.add('d-none');

        } else {
            if (forma == 5) {
                for (let i = 0; i < efectivo.length; i++) {
                    efectivo[i].classList.add('d-none');
                }
        
                for (let j = 0; j < pago_aplicativo.length; j++) {
                    pago_aplicativo[j].classList.remove('d-none');
                    
                }
        
                for (let k = 0; k < pago_par.length; k++) {
                    pago_par[k].classList.add('d-none');
                    
                }
            } else {
                for (let i = 0; i < efectivo.length; i++) {
                    efectivo[i].classList.add('d-none');
                }
        
                for (let j = 0; j < pago_aplicativo.length; j++) {
                    pago_aplicativo[j].classList.add('d-none');
                    
                }
        
                for (let k = 0; k < pago_par.length; k++) {
                    pago_par[k].classList.remove('d-none');
                    
                }
            }
        }
    }
});

function bancos_venta() {
    fetch(urlgeneral+"/bancos-venta")
    .then(res => res.json())
    .then(data => {
        let html = `<option value="">Seleccionar...</option>`;

        data.forEach(banco => {
            html += `<option value="${banco.id}">${banco.cuenta_corriente}-${banco.abreviatura}</option>`
        });

        banco_venta.innerHTML = html;
    })
}

function formaPagoParticionado() {
    fetch(urlgeneral+"/forma-pago")
    .then(res => res.json())
    .then(data => {
        let html = "";

        data.forEach(forma => {
            let disabled = "";

            if (forma.id == 9) {
                disabled = "disabled"
            }

            html += `
                <option value="${forma.id}" ${disabled}>${forma.descripcion.toUpperCase()}</option>
            `;
        });

        forma_pago_particionado.innerHTML = html;
    })
}

function banco_particionado(id) {
    fetch(urlgeneral+"/bancos-venta")
    .then(res => res.json())
    .then(data => {
        const banco_particionado_ = document.getElementById('banco_particionado'+id)
        let html = `<option value="">Seleccionar...</option>`;

        data.forEach(banco => {
            html += `<option value="${banco.id}">${banco.cuenta_corriente}-${banco.abreviatura}</option>`
        });

        banco_particionado_.innerHTML = html;
    })
}

function obtenerElementosPorNombre(nombre) {
    return Array.from(document.getElementsByName(nombre));
}

addParticionado.addEventListener('click', (e) => {
    const forma = forma_pago_particionado.value;
    const textForma = forma_pago_particionado.options[forma_pago_particionado.selectedIndex].text;

    var elementos = obtenerElementosPorNombre("forma_pago_particionado[]");

    let data_forma = [];

    elementos.forEach(element => {
        data_forma.push(element.value);
    });

    if (data_forma.includes(forma)) {
        return alert('Ya existe esta forma de pago');
    }

    let html = `
    <div class="row">
        <div class="col-md-3">
            <label class="form-label">Forma de Pago: </label>
            <input type="hidden" name="forma_pago_particionado[]" value="${forma}">
            <input type="text" class="form-control" value="${textForma}" name="formaPagoParticionado[]" readonly>
        </div>
        <div class="col-md-2">
            <div class="mb-3">
                <label class="form-label">Monto: </label>
                <input class="form-control" type="number" id="" name="montoParticionado[]" value="">
            </div>
        </div>
        <div class="col-md-3" id="number_operation${forma}">
            <div class="mb-3">
                <label class="form-label">N° de Operación: </label>
                <input class="form-control" type="number" id="" name="numeroOperacionParticionado[]">
            </div>
        </div>
        <div class="col-md-3" id="select_banco${forma}">
            <div class="mb-3">
                <label class="form-label">Banco: </label>
                <select class="form-select" id="banco_particionado${forma}" name="bancoParticionado[]">
                    
                </select>
            </div>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-outline-secondary btn-sm mt-4 delete-forma" data-id="d"><i class="bx bxs-trash-alt delete-forma" data-id="di"></i></button>
        </div>
    </div>
    `;

    $("#listPagoParticionado").append(html);

    const number_operation = document.querySelector('#number_operation'+forma);
    const select_banco = document.querySelector('#select_banco'+forma);

    if (forma == 1) {
        number_operation.classList.add('d-none');
        select_banco.classList.add('d-none');
    } else {
        if (forma == 2 || forma == 3 || forma == 4 || forma == 8) {
            number_operation.classList.remove('d-none');
            select_banco.classList.add('d-none');
        } else {
            number_operation.classList.remove('d-none');
            select_banco.classList.remove('d-none');
        }
    }

    banco_particionado(forma);
})

listPagoParticionado.addEventListener('click', (e) => {
    
    if (e.target.classList.contains('delete-forma')) {
        const data_id = e.target.getAttribute('data-id');
        if (data_id == 'd') {
            e.target.parentElement.parentElement.remove();
        } else {
            e.target.parentElement.parentElement.parentElement.remove();
        }
    }
    
});

total_recibido.addEventListener('keyup', (e) => {
    const montoVenta = parseFloat(document.getElementById('montoVenta').value);
    const inputVuelto = document.getElementById('vuelto');

    const monto = parseFloat(e.target.value);

    const vuelto = monto - montoVenta;

    inputVuelto.value = vuelto.toFixed(2);
})

guardarVenta.addEventListener('click', (e) => {
    e.target.disabled = true;

    if (documento.value === '2' && tipoDocumentoIdentidad.value != "6") {
        alert('Para Facturas es necesario ingresar un ruc');
        return false;
    }

    let formData1 = new FormData(document.getElementById("detalle_venta"));
    let formData2 = new FormData(document.getElementById("venta_info"));

    for (const [key, value] of formData2.entries()) {
      formData1.append(key, value);
    }

    formData1.append('_token', csrf);

    fetch(urlgeneral+'/generar_venta', {
      method: 'POST',
      body: formData1
    })
    .then(response => response.json())
    .then(data => {
        if (data.respuesta == "ok") {
            $("#view_modal_cobrar").modal('hide');
            window.location.href = urlgeneral+"/pos";
            window.open(urlgeneral+"/venta/ticket/"+data.id);
        } else {
            alert(data.mensaje);
            e.target.disabled = false;
        }
    });
})

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

select_ubicacion.addEventListener('change', render_productos);