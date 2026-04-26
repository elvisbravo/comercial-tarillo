

window.addEventListener("load", function (event) {
    getUsersList();
});

function getUsersList() {
    $.get(urlGeneral + "/users/getList", function (data) {
        buildDataTable(data);
    });
}

function buildDataTable(data) {
    let contenidoHtml = "";
    for (let indice = 0; indice < data.length; indice++) {
        contenidoHtml += "<tr>";
        contenidoHtml += "<td>" + (indice + 1) + "</td>";
        contenidoHtml += "<td>" + data[indice].name + "</td>";
        contenidoHtml += "<td>" + data[indice].email + "</td>";
        
        // Roles
        let rolesHtml = "";
        if (data[indice].roles && data[indice].roles.length > 0) {
            data[indice].roles.forEach(function(role) {
                rolesHtml += '<label class="btn btn-success me-1">' + role + '</label>';
            });
        }
        contenidoHtml += "<td>" + rolesHtml + "</td>";
        
        // Image
        contenidoHtml += "<td><img src='" + urlGeneral + "/perfil/" + (data[indice].img ? data[indice].img : 'default.png') + "' alt='' class='img-thumbnail' width='50px'></td>";
        
        // Actions
        contenidoHtml += "<td>";
        contenidoHtml += '<a class="btn btn-info me-1" href="' + urlGeneral + '/users/' + data[indice].id + '"><i class="bx bxs-show label-icon"></i> Ver</a>';
        
        if (typeof canEdit !== 'undefined' && canEdit) {
            contenidoHtml += '<a class="btn btn-primary me-1" href="' + urlGeneral + '/users/' + data[indice].id + '/edit"><i class="bx bx-pencil label-icon"></i> Editar</a>';
        }
        
        if (typeof canDelete !== 'undefined' && canDelete) {
            contenidoHtml += '<button type="button" class="btn btn-danger" onclick="eliminarUsuario(' + data[indice].id + ')">Eliminar</button>';
        }
        
        contenidoHtml += "</td>";
        contenidoHtml += "</tr>";
    }

    if ($.fn.DataTable.isDataTable('#datatable')) {
        $('#datatable').DataTable().destroy();
    }
    
    document.getElementById("serviciosdatos").innerHTML = contenidoHtml;
    initDataTable("#datatable");
}