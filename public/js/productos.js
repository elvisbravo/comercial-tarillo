urlgeeneral=$("#url_raiz_proyecto").val();
$("#actualizar").hide();

//FUNCION LOAD
window.addEventListener("load", function (event) {

   listadoproductos();
  $(".loader").fadeOut("slow");


});


//metodo para llenar la tabla de productos
function listadoproductos(){


   $.get(urlgeeneral+"/productos/listarproductos",function(data){


          llenalistado(data);

   });

}

function llenalistado(data){

    var contenido = "";

      for (var i = 0; i < data.length; i++) {
        contenido += "<tr>";
        contenido += "<td style='padding:1px;text-align:center;'>" +  parseInt(i+1,10) + "</td>";

        contenido += "<td style='padding:1px;text-align:center'>" + data[i].nomb_pro + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>" + data[i].categoria + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>" + data[i].subcategoria + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>" + data[i].unidad + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>" + data[i].marca + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>" + data[i].precio_contado + "</td>";
        contenido += "<td style='padding:1px;text-align:center'>" + data[i].precio_credito + "</td>";
        contenido += "<td style='padding:1px;text-align:center'> <strong>" + data[i].stock +"</strong> </td>";
        contenido += "<td style='padding:1px;text-align:center'>";
        //contenido +='<div class="dropdown">';
        //contenido +=' <button class="btn btn-facebook dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"> Action</button>';
        //contenido +=' <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">';
        //contenido +=' <li><a class="dropdown-item" href="productos/'+data[i].id+'/edit">Editar</a></li>';
        //contenido +=' <li class="eliminar"><button class="dropdown-item" type="button" onclick="eliminarsector(\''+ data[i].id +'\')" class="btn btn-danger  eliminar"> <i class="fa fa-trash eliminar" aria-hidden="true"></i>  Anular</button></li>';
        //contenido +=' </ul>';
        //contenido +='</div>';


        //contenido +='<a href="productos/'+data[i].id+'" type="button" class="btn btn-success "><i class="fab fa-searchengin"></i> </a>';
        contenido +='<a href="productos/'+data[i].id+'/edit" type="button" class="btn btn-info " ><i class="fas fa-edit"></i> </a>';
        contenido +='<button type="button" onclick="eliminarsector(\''+ data[i].id +'\')" class="btn btn-danger  eliminar"><i class="fa fa-trash eliminar" aria-hidden="true"></i> </button>';


        contenido +="</td>";
        contenido += "</tr>";


      }

      document.getElementById("listadoproductos").innerHTML = contenido;
        $("#datatable").DataTable();


}


function eliminarsector(id){



      const tabla = document.getElementById('datatable');

      tabla.addEventListener('click', (e) => {
        if (e.target.classList.contains('eliminar') || e.target.classList.contains('bx')) {
            Swal.fire({
                title: '¿Desea anular el Producto?',
                text: "No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, eliminar!',
                cancelButtonText: 'Cancelar'
              }).then((result) => {
                if (result.isConfirmed) {
                //Metodo para

              var csrf = document.querySelector('meta[name="csrf-token"]').content;
                  $.ajax({
                    type: "POST",
                    url: urlgeeneral+"/productos/eliminar/"+id,
                    data: {"_method": "delete",'_token': csrf},

                    success: function (data) {

                      location.href =urlgeeneral+"/productos";

                        listadoproductos(0);
                        console.log(data);
                        if(data=="ERROR"){

                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Lo siento no podemos anular el producto porque el sistema a detectado que el producto tiene stock disponible!',
                                footer: ''
                              })

                        }else{

                            Swal.fire(
                                'Eliminado!',
                                'El servicio ha sido eliminado.',
                                'success'
                              );
                              location.reload();

                        }




                    }

                });





                }
              })
        }
    })



}
