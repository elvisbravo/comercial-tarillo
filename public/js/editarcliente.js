urlgeneral = $("#url_raiz_proyecto").val();
const tipo_documento = document.getElementById('documento_identidad');
const numero_documento = document.getElementById('numero_documento');
const btn_consultar = document.getElementById('btn_consultar');
const id_cliente = $("#id_cliente").val();
const ubigeo_idtem = $("#ubigeo_idtem").val();
const urimagen = urlgeneral + '/img/truck.png';

window.addEventListener("load", function (event) {
    $(".loader").fadeOut("slow");
    direcciones();
});

btn_consultar.addEventListener('click', (e) => {
    const t_documento = tipo_documento.value;
    const numero = numero_documento.value;

    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    const formData = new FormData();

    formData.append('tipo_documento', t_documento);
    formData.append('num_doc', numero);
    formData.append('_token', csrf);

    fetch(urlgeneral + "/consultar_dni_ruc", {
        method: 'POST',
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            const nombre = document.getElementById('razon_social');
            const street = document.getElementById('street');
            const nomb_per = document.getElementById('nomb_per');
            const pate_per = document.getElementById('pate_per');
            const mate_per = document.getElementById('mate_per');

            if (data.original.respuesta == "ok") {
                if (t_documento == 1) {
                    nombre.value = data.original.data.name + " " + data.original.data.paternal_surname + " " + data.original.data.maternal_surname;
                    nomb_per.value = data.original.data.name;
                    pate_per.value = data.original.data.paternal_surname;
                    mate_per.value = data.original.data.maternal_surname;
                }

                if (t_documento == 6) {
                    nombre.value = data.original.data.name;
                    street.value = data.original.data.street;
                }

            } else {
                alert('no fue encontrado');
            }

        })

});

traer_ubigeo();
traer_ubigeodos();

function traer_ubigeo() {

    fetch(urlgeneral + "/traslado/traer_ubigeo/" + ubigeo_idtem)
        .then(res => res.json())
        .then(data => {
            const partida = document.querySelector('.select_ubigeo_partida');
            const partidaUbigeo = new Choices(partida);

            partidaUbigeo.setChoices(data);


        })
}

function traer_ubigeodos() {

    fetch(urlgeneral + "/traslado/traer_ubigeo/" + ubigeo_idtem)
        .then(res => res.json())
        .then(data => {
            const partida = document.querySelector('.select_ubigeo_partidados');
            const partidaUbigeo = new Choices(partida);

            partidaUbigeo.setChoices(data);
        })
}

function agregar() {

    $("#staticBackdrop").modal('show');
    $("#actualizar").text("Guardar");
    $("#remover").hide();

    $("#valor").val(0);
    limpiarcajasunidas();
    sectores(0);
}

function direcciones() {
    $.get(urlgeneral + "/clientes/listadirecciones/" + id_cliente, function (data) {

        let contenido = "";
        for (var i = 0; i < data.length; i++) {

            contenido += `
            <div class="col-md-3 col-xs-12"> 
                <div class="card" style="width: 18rem;">
                    <div class="card-body">
                        <img class="" height="50px" width="50px" src="${urimagen}" alt="Card image cap">
                        <h5 class="card-title">Dirección Entraga/Contacto</h5> 
                        <p class="card-text">${data[i].direccion}</p>
                        <h5 class="card-title">Telefono</h5> 
                        <p class="card-text">${data[i].telefono}</p>
                        <button type="button" class="btn btn-primary" onclick="modificar(${data[i].id});">Editar</button>
                        <button type="button" class="btn btn-warning mx-3" onclick="ver_imagen_direccion(${data[i].id}, '${data[i].direccion}');">Ver Imagen</button> 
                    </div>  
                </div> 
            </div>
            `;
        }

        document.getElementById("direccionespe").innerHTML = contenido;
    });
}

function sectores(id) {
    $.get(urlgeneral + "/clientes/sector", function (data) {
        var contenido = '';
        contenido += ' <option value="">--Seleccionar Sector--</option>';
        for (var i = 0; i < data.length; i++) {
            if (id == data[i].id) {
                contenido += ' <option value="' + data[i].id + '" selected>' + data[i].nomb_sec + '</option>';
            } else {
                contenido += ' <option value="' + data[i].id + '">' + data[i].nomb_sec + '</option>';
            }
        }

        document.getElementById("id_sector").innerHTML = contenido;
    });
}

function modificar(id) {
    $("#staticBackdrop").modal('show');
    $("#actualizar").text("Actualizar");
    $("#remover").show();
    $("#fileImages").val("");

    $.get(urlgeneral + "/clientes/direccion/" + id, function (data) {
        $("#nombre_contacto").val(data.nombre_contacto);
        $("#direccion").val(data.direccion);
        $("#telefo").val(data.telefono);
        $("#correo").val(data.correo);
        $("#referencia").val(data.referencia);
        $("#valor").val(data.id);
        sectores(data.id_sector);
    });
}

const formDirecciones = document.getElementById('form-registrar-direccion');

formDirecciones.addEventListener('submit', (e) => {
    e.preventDefault();

    const btnForm = document.getElementById('actualizar');

    btnForm.disabled = true;

    const formData = new FormData(formDirecciones);

    fetch(urlgeneral + "/clientes/modificardir", {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    })
        .then(res => res.json())
        .then(data => {
            btnForm.disabled = false;

            direcciones();

            Swal.fire({
                icon: 'success',
                title: 'Oops...',
                text: 'Modificado Correctamente',
                footer: ''
            })

            $('#staticBackdrop').modal('hide');
        })
})

function datosobligatorio() {
    var bien = true;

    var obligarotio = document.getElementsByClassName("obligatorio");
    var ncontroles = obligarotio.length;

    for (var i = 0; i < ncontroles; i++) {
        if (obligarotio[i].value == "") {
            bien = false;
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Los Campos con (*) son Obligatorio!',
                footer: ''
            })
        } else {
            obligarotio[i].parentNode.classList.remove("error")
        }
    }
    return bien;
}

function datosobligatoriodos() {
    var bien = true;

    var obligarotio = document.getElementsByClassName("obligatoriodos");
    var ncontroles = obligarotio.length;

    for (var i = 0; i < ncontroles; i++) {
        if (obligarotio[i].value == "") {
            bien = false;

            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Los Campos con (*) son Obligatorio!',
                footer: ''
            })

        } else {
            obligarotio[i].parentNode.classList.remove("error")
        }
    }
    return bien;

}

function limpiarcajasunidas() {
    var controles = document.getElementsByClassName("limpiar");
    var ncontroles = controles.length;
    //alert(ncontroles);
    for (var i = 0; i < ncontroles; i++) {
        controles[i].value = "";
    }
}

const contenedorImagenes = document.getElementById('contenedor-imagenes');

function ver_imagen_direccion(id, direccion) {
    $("#modal_imagen_direccion").modal('show');

    contenedorImagenes.innerHTML = '';

    const titleModal = document.getElementById('modalImagenDireccion');
    titleModal.textContent = direccion;

    getImagenesDirecciones(id);
}

function getImagenesDirecciones(id) {
    fetch(urlgeneral + "/getImagenesDireccion/" + id)
        .then(res => res.json())
        .then(data => {

            if(data.length > 0) {
                mostrarImagenes(data)
            } else {
                let html = `<h3 class="text-center">No hay Imágenes subidas</h3>`;
                contenedorImagenes.innerHTML = html;
            }

            
        })
}

function mostrarImagenes(datos) {

    let html = "";

    datos.forEach(direccion => {
        let address = urlgeneral + "/" + direccion.path_image;

        html += `
        <div class="col-lg-3 col-sm-6">
            <div class="mt-4">
                <a href="${address}" class="image-popup-desc" data-title="Imagen dirección ${direccion.id}">
                    <img src="${address}" class="img-fluid" alt="work-thumbnail-${direccion.id}">
                </a>

                <span class="remove-icon">
                    <a href="javascript: void(0);" onclick="deleteImageDirection(${direccion.id}, ${direccion.dire_id})"><i class="fas fa-times text-danger"></i></a>
                </span>
            </div>
        </div>
        `;
    });

    contenedorImagenes.innerHTML = html;

    const lightboxDesc = GLightbox({selector:".image-popup-desc"})
}

function deleteImageDirection(id, dire_id) {
    Swal.fire({
        title: "¿Está seguro?",
        text: "¡No podrá revertir esto!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si, bórralo!",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(urlgeneral+'/clientes/imagenDireccionEliminar/'+id)
            .then(res => res.json())
            .then(data => {
                getImagenesDirecciones(dire_id);

                Swal.fire({
                    position: "top-center",
                    icon: "success",
                    title: "Se elimino correctamente la Imagen",
                    showConfirmButton: false,
                    timer: 2500
                  });
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    });
}

const imagen_referencia = document.getElementById('foto_referencia');
const vista_previa = document.getElementById('view_preview_img');
const form_cliente_update = document.getElementById('form_cliente_update');

imagen_referencia.addEventListener('change', function() {

    vista_previa.innerHTML = `
    <a href="" class="image-popup-desc" data-title="Imagen dirección" id="idImage">
        <img id="preview" class="mt-1 image-popup-desc" src="" alt="Vista previa" style="width: 200px;height: 200px;">
    </a>
    `;

    const previewImg = document.getElementById('preview');
    const idImage = document.getElementById('idImage');

    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.addEventListener('load', function() {
        previewImg.src = reader.result;
      });
      reader.readAsDataURL(file);
    }
});

form_cliente_update.addEventListener('submit', (e) => {
    e.preventDefault();

    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const guardardatos = document.getElementById('guardardatos');

    guardardatos.disabled = true;

    const formData = new FormData(form_cliente_update);
    formData.append("_token", csrf);

    fetch(urlgeneral + "/clientes/modificardatos",{
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        guardardatos.disabled = false;
        location.href = urlgeneral + "/clientes";
    })

})

if(document.getElementById('preview')) {
    const lightboxDesc = GLightbox({selector:".image-popup-desc"})
}