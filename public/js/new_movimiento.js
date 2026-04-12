window.onload = () => {
    validar_caja();
}

const urlgeneral = document.getElementById('url_raiz_proyecto').value;

const concepto = document.getElementById('concepto');

const tipo = document.getElementById('tipo_movimiento');

const form_mov = document.getElementById('form_movimiento');

tipo.addEventListener('change', (e) => {
    const valor = e.target.value;
    
    fetch(urlgeneral+"/conceptos/filtrar_tipo/"+valor)
    .then(res => res.json())
    .then(data => {
        let html = `<option value="">SELECCIONE::</option>`;

        data.forEach(con => {
            html += `
                <option value="${con.id}">${con.descripcion}</option>
            `;
        });

        concepto.innerHTML = html;
    })

});

form_mov.addEventListener('submit', (e) => {
    e.preventDefault();

    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    const formData = new FormData(form_mov);
    formData.append('_token', csrf);

    fetch(urlgeneral+"/movimiento/add",{
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
            })

            setTimeout(() => {
                window.location.href = urlgeneral+"/movimientos";
            }, 2500);
        } else {
            Swal.fire({
                position: 'top-center',
                icon: 'error',
                title: data.mensaje,
                showConfirmButton: false,
                timer: 2500
            })
        }
    })
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