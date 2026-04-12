const urlgeneral = document.getElementById('url_raiz_proyecto').value
const btn_add = document.getElementById('btnadd');

const form = document.getElementById('form_almacen');

const tabla = document.getElementById('contentAlmacen');

btn_add.addEventListener('click', (e) => {
    $("#modal_almacen").modal('show');
    document.getElementById('titleModal').textContent = "Agregar Almacen";
    document.getElementById('btnform').textContent = "Guardar";
});

form.addEventListener('submit', (e) => {
    e.preventDefault();

    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    const formData = new FormData(form);
    formData.append("_token", csrf);

    fetch(urlgeneral+"/almacenes/guardar",{
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.respuesta == "ok") {
            $("#modal_almacen").modal('hide');
            renderAlmacenes();
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: data.mensaje,
                showConfirmButton: false,
                timer: 1500
            })
        } else {
            alert(data.mensaje);
        }
    })

});

tabla.addEventListener('click', (e) => {

    if (e.target.classList.contains('editar')) {
        editar_almacen(e);
    }

    if (e.target.classList.contains('eliminar')) {
        eliminar_almacen(e);
    }

})

renderAlmacenes();

function renderAlmacenes(){
    fetch(urlgeneral+"/almacenes/render")
    .then(res => res.json())
    .then(data => {
        let html = "";

        data.forEach((almacen,index) => {
            html += `
                <tr>
                    <td>${index+1}</td>
                    <td>${almacen.nombre}</td>
                    <td>${almacen.abreviatura}</td>
                    <td>${almacen.direccion}</td>
                    <td>
                        <button type="button" class="btn btn-info btn-sm editar" data-id="${almacen.id}" data-name="${almacen.nombre}" abreviatura="${almacen.abreviatura}" data-address="${almacen.direccion}">
                            Editar
                        </button>
                        <button type="button" class="btn btn-danger btn-sm eliminar" data-id="${almacen.id}" data-name="${almacen.nombre}" abreviatura="${almacen.abreviatura}" data-address="${almacen.direccion}">
                            Eliminar
                        </button>
                    </td>
                </tr>
            `;
        });

        $("#dataTableExample").DataTable().destroy();
        tabla.innerHTML = html;
        $("#dataTableExample").DataTable();

    })
}

function editar_almacen(e){
    const id = e.target.getAttribute('data-id');
    const name = e.target.getAttribute('data-name');
    const address = e.target.getAttribute('data-address');
    const abreviatura=e.target.getAttribute('abreviatura');

    $("#modal_almacen").modal('show');
    const html_idalmacen = document.getElementById('idalmacen');
    html_idalmacen.value = id;

    document.getElementById('titleModal').textContent = "Editar Almacen";
    document.getElementById('btnform').textContent = "Editar";

    document.getElementById('nombre_almacen').value = name;
    document.getElementById('direccion_almacen').value = address;
    document.getElementById('abreviatura').value = abreviatura;
}

function eliminar_almacen(e){
    const id = e.target.getAttribute('data-id');

    Swal.fire({
        title: '¿Desea eliminar el almacen?',
        text: "una vez realizado la acción no podrás revertir",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {

            fetch(urlgeneral+"/almacenes/eliminar/"+id)
            .then(res => res.json())
            .then(data => {

                if (data == "ok") {
                    Swal.fire({
                        position: 'top-center',
                        icon: 'success',
                        title: "Se elimino correctamente el almacen",
                        showConfirmButton: false,
                        timer: 1500
                    });

                    renderAlmacenes();
                } else {
                    alert('ocurrio un error');
                }

            })

        
        }
    })

}