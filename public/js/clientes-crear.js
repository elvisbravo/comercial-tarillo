urlgeneral = $("#url_raiz_proyecto").val();

const tipo_documento = document.getElementById('documento_identidad');
const numero_documento = document.getElementById('numero_documento');
const btn_consultar = document.getElementById('btn_consultar');

window.addEventListener("load", function (event) {
    $(".loader").fadeOut("slow");
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
            const street = document.getElementById('dire_per');
            const nomb_per = document.getElementById('nomb_per');
            const pate_per = document.getElementById('pate_per');
            const mate_per = document.getElementById('mate_per');
            console.log(data.original.direccion);
            //console.log(data.original.nombres);

            if (data.original) {
                if (t_documento == 1) {
                    nombre.value = data.original.nombres;
                    nomb_per.value = data.original.nombres;
                    pate_per.value = data.original.apellidoPaterno;
                    mate_per.value = data.original.apellidoMaterno;
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

traer_ubigeo();

function traer_ubigeo() {
    fetch(urlgeneral + "/traslado/traer_ubigeo/" + 0)
        .then(res => res.json())
        .then(data => {
            const partida = document.querySelector('.select_ubigeo_partida');
            const ubigeo_partida = new Choices(partida, {
                placeholderValue: "This is a placeholder set in the config",
                searchPlaceholderValue: "Busque aquí el ubigeo",
                choices: data
            });
        })
}

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
                text: 'Los Campos Marcados con (*) son obligatorios',
                footer: ''
            })
        } else {
            obligarotio[i].parentNode.classList.remove("error")
        }
    }
    return bien;
}

const imagen_referencia = document.getElementById('foto_referencia');
const vista_previa = document.getElementById('vista_previa');
const form_create_cliente = document.getElementById('form_create_cliente');

imagen_referencia.addEventListener('change', function() {

    vista_previa.innerHTML = `<img id="preview" class="mt-1" src="" alt="Vista previa" style="width: 200px;height: 200px;">`;

    const previewImg = document.getElementById('preview');

    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.addEventListener('load', function() {
        previewImg.src = reader.result;
      });
      reader.readAsDataURL(file);
    }
});

form_create_cliente.addEventListener('submit', (e) => {
    e.preventDefault();

    let csrf = document.querySelector('meta[name="csrf-token"]').content;
    const guardardatos = document.getElementById('guardardatos');

    guardardatos.disabled = true;

    const formData = new FormData(form_create_cliente);

    formData.append("_token", csrf);

    fetch(urlgeneral + "/clientes/crear", {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        guardardatos.disabled = false;

        if(data.respuesta === 'ok') {
            location.href = urlgeneral + "/clientes";
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: data.mensaje
            })
        }
    })
})