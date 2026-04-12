const urlgeneral = document.getElementById('url_raiz_proyecto').value;

const btn_add = document.getElementById('btnAddTraslado');
const guia = document.getElementById('form_guia');
const buscarconductor=document.getElementById('buscarconductor');

window.addEventListener("load", function (event) {
   $(".loader").fadeOut("slow");
 });

 //CARGAR LAS UBICACIONES SEGUN EL STOCK

 function selectsedes(id){

      $.get(urlgeneral+"/ubicaciones_stock_sede/"+id,function(data){

           let contenido="";
           contenido +=' <option value="">--Seleccionar--</option>';
           for (let i = 0; i < data.length; i++) {
             
            contenido +=' <option value="'+data[i].id+'">'+data[i].abreviatura+'/'+data[i].ubicacion+'</option>';
            
           }
           document.getElementById('almacen_destino').innerHTML=contenido;
      })
 }

/*const element = document.querySelector('#change_cliente');
const example = new Choices(element);

example.setChoices([
    { value: "One", label: "Label One" },
    { value: "Two", label: "Label Two", selected: true },
    { value: "Three", label: "Label Three" }
]);*/

traer_ubigeo();
traer_productos();
//traer_clientes();
var cont=0;

btn_add.addEventListener('click', () => {

    if (validarstock() == true) {

        const cantidad = document.getElementById('cantidad_trasladar');
        const stock = document.getElementById('stock_p');
        const id_p = document.getElementById('search_producto');
        const text_p = id_p.options[id_p.selectedIndex].text;

       // alert(cantidad.value+' '+stock.value)

        if(Number(cantidad.value)> Number(stock.value)){

            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'La cantidad Demandada no debe superar al stock actual!',
                footer: '!! OPP'
              })

           
        }else{

             
                let html = `
                <tr id="fila${cont}">
                    <input type="hidden" class="id_producto" name="id_producto[]" value="${id_p.value}" />
                    <input type="hidden" class="cantidadx" name="cantidad[]" value="${cantidad.value}" />
                    <td>${text_p}</td>
                    <td>UNIDADES</td>
                    <td>${cantidad.value}</td>
                    <td onclick="eliminar(${cont})">
                        <i class="bx bx-trash fs-4 align-middle text-danger" style="cursor: pointer"></i>
                    </td>
                </tr>
            `;

            cont++;

            $("#contentTraslado").append(html);


        }

     
   
        cantidad.value = "";
        stock.value = "";

        
    }

   

});



function eliminar(index){


    $("#fila" +index).remove();
   
}
//FUNCION PARA QUE VALIDE QUE LA CANTIDAD A INGRESAR NO SEAS MAYOR DE LA CANTIDAD DEL STOCK

todo=[];
guia.addEventListener('submit', (e) => {
    e.preventDefault();

    if (datosobligatorio() == true) {

        guia.disabled = true;

    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    //tipo de translado
    //DATOS DEL TIPO DE TRASLADO
    let selectdoc_traslado=document.getElementById("tipo_traslado");
    let tipo_traslado=selectdoc_traslado.options[selectdoc_traslado.selectedIndex].value;
    //DATOS DEL CLIENTE
    //let selectcliente_id=document.getElementById("change_cliente");
    let cliente_id=$("#id_persona_tempe").val();//selectcliente_id.options[selectcliente_id.selectedIndex].value;
    //motivo  email
    let email=$("#email").val();
    //motivo de translado
    let selectmotivo=document.getElementById("motivo");
    let motivo=selectmotivo.options[selectmotivo.selectedIndex].value;
    //modalidad de traslado
    let selectmodalidad=document.getElementById("modalidad_traslado");
    let modalidad_traslado=selectmodalidad.options[selectmodalidad.selectedIndex].value;
    //fecha de translado
    let fecha=$("#fecha_traslado").val();
    //peso bruto
    let peso_bruto=$("#peso_bruto").val();
    //bultos
    let bultos=$("#bultos").val();
    //TREAR LA DATA EL ID DEL CONDUTOR
    let conductor_id=$("#conductor_id").val();
    //DIRECCIONES punto de partida
    let direccion_partida=$("#direccion_partida").val();
    let selectubigeo_partida=document.getElementById("ubigeo_partida");
    let ubigeo_partida=selectubigeo_partida.options[selectubigeo_partida.selectedIndex].value;

    //LAS SEDE
   

    if(ubigeo_partida==="Seleccione"){

        alert("El ubigeo de Partida es obligatorio");
        return false;
    }
    //DIRECCIONES PUNTO DE LLEGADA
    let direccion_llegada=$("#direccion_llegada").val();
    let selectubigeo_llegada=document.getElementById("ubigeo_llegada");
    let ubigeo_llegada=selectubigeo_llegada.options[selectubigeo_llegada.selectedIndex].value;

    if(ubigeo_llegada==="Seleccione"){

        alert("El ubigeo de Llegada es obligatorio");
        return false;
    }
    //almacenes 
    let selectalmacen_origen=document.getElementById("almacen_origen");
    let almacen_origen=selectalmacen_origen.options[selectalmacen_origen.selectedIndex].value;

    if(almacen_origen==""){

        alert("El Almacen de Origin obligatorio");
        return false;
    }
    
    let selectalmacen_destino=document.getElementById("almacen_destino");
    let almacen_destino=selectalmacen_destino.options[selectalmacen_destino.selectedIndex].value;

    if(almacen_destino==""){

        alert("El Almacen de destino obligatorio");
        return false;
    }
    //validar la sede destino sea obligatoria



    //CODIGO DEL DOCUMENTO ELECTRONICO
    let id_documento_electronico=$("#id_documento_electronico").val();

    //AGREGAR LAS CABECERAS AL DETALLE
    let cabezera=new cabezeratranslado(tipo_traslado,cliente_id,email,motivo,modalidad_traslado,fecha,peso_bruto,bultos,conductor_id,direccion_partida,ubigeo_partida,direccion_llegada,ubigeo_llegada,almacen_origen,almacen_destino,id_documento_electronico);
    todo.push(cabezera);
    //console.log(todo)
 

    //AGREGAR EL DETALLE DE LOS PRODUCTOS

    const producto_id=document.getElementsByClassName('id_producto');
    const cantidad=document.getElementsByClassName('cantidadx');


    
    for (let i = 0; i < producto_id.length; i++) {

        let detalle=new detalletraslado(producto_id[i].value,cantidad[i].value);
        todo.push(detalle);
    }


    showSpinner();

    $.ajax({
        type : "POST",
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        contentType:"application/json",
        dataType:'json',
        data:JSON.stringify(todo),
        processData:false,
        cache:false,
        url :urlgeneral+ "/traslado/guardar",
        success : function (result) {

            console.log(result)

            if(result.respuesta=="error"){

                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: result.mensaje,
                    footer: 'Es muy importante que agregue datos al detalle de la Guia'
                  });

                  todo=[];

            }else{

                Swal.fire({
                    icon: 'success',
                    title: 'Oops...',
                    text: result.mensaje,
                    footer: ''
                })

            }

       

            location.href =urlgeneral+"/traslados";


       },



        error : function(xhr,errmsg,err) {

            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'los Campos marcados con (*) son obligatorios!',
                footer: 'Por favor recargue la página para poder empezar de nuevo'
              })

                        console.log(xhr.status + ": " + xhr.responseText);
                        }

        });


    }
   /* fetch(urlgeneral+"/traslado/guardar",{
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        console.log(data);
    });*/


});

//crear los constructores para poder guardar la información
function cabezeratranslado(tipo_traslado,cliente_id,email,motivo,modalidad_traslado,fecha,peso_bruto,bultos,conductor_id,direccion_partida,ubigeo_partida,direccion_llegada,ubigeo_llegada,almacen_origen,almacen_destino,id_documento_electronico){
    
          this.tipo_traslado=tipo_traslado;
          this.cliente_id=cliente_id;
          this.email=email;
          this.motivo=motivo;
          this.modalidad_traslado=modalidad_traslado;
          this.fecha=fecha;
          this.peso_bruto=peso_bruto;
          this.bultos=bultos;
          this.conductor_id=conductor_id;
          this.direccion_partida=direccion_partida;
          this.ubigeo_partida=ubigeo_partida;
          this.direccion_llegada=direccion_llegada;
          this.ubigeo_llegada=ubigeo_llegada;
          this.almacen_origen=almacen_origen;
          this.almacen_destino=almacen_destino;
          this.id_documento_electronico=id_documento_electronico
          
}

//DETALLE DEL TRANSLADO
function detalletraslado(producto_id,cantidad){

    this.producto_id=producto_id;
    this.cantidad=cantidad;

}



function traer_ubigeo(){
    fetch(urlgeneral+"/traslado/traer_ubigeo/"+0)
    .then(res => res.json())
    .then(data => {
        const partida = document.querySelector('#ubigeo_partida');
        const ubigeo_partida = new Choices(partida, {
            placeholderValue: "This is a placeholder set in the config",
            searchPlaceholderValue: "Busque aquí el ubigeo",
            choices: data
        });

        const llegada = document.querySelector('#ubigeo_llegada');
        const ubigeo_llegada = new Choices(llegada, {
            placeholderValue: "This is a placeholder set in the config",
            searchPlaceholderValue: "Busque aquí el ubigeo",
            choices: data
        });
    })
}

function traer_clientes(){

    $.get(urlgeneral+"/creditos-pendientes/listadoclientes",function(data){

        var contenido = "";
        for (var i = 0; i < data.length; i++) {
            contenido += "<tr>";
    
            contenido += "<td style='padding:1px;text-align:center' id='documento"+data[i].id+"'>" + data[i].documento + "</td>";
            contenido += "<td style='padding:1px;text-align:center' id='nombre"+data[i].id+"'>" + data[i].razon_social + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>" + data[i].dire_per + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>";
            contenido +='<a href="#" onclick="seleccionar(\''+data[i].id+'\')" type="button" class="btn btn-success"><i class="fas fa-check"></i> </a>'
            contenido +="</td>";
    
            contenido += "</tr>";
        }
    
        document.getElementById("listaclientes").innerHTML = contenido;
        $("#datatablesx").DataTable();
    
    
    });

    

    /*fetch(urlgeneral+"/traslado/traer_clientes")
    .then(res => res.json())
    .then(data => {
        const change_cliente = document.querySelector('#change_cliente');
        console.log(data);
   
        const ubigeo_llegada = new Choices(change_cliente, {
            placeholderValue: "This is a placeholder set in the config",
            searchPlaceholderValue: "Busque aquí el cliente",
            //choices: data
        });
    })*/
}

function seleccionar(id){

    $("#documento").val($("#documento"+id).text());
    $("#nombresdata").val($("#nombre"+id).text());
    $("#id_persona_tempe").val(id)


    console.log("hola "+ id);

    $(".bs-example-modal-xl").modal('hide');

}

function traer_productos() {

    fetch(urlgeneral+"/traslado/traer_productos")
    .then(res => res.json())
    .then(data => {
        const prod = document.querySelector("#search_producto");
        const productos = new Choices(prod, {
            placeholderValue: "This is a placeholder set in the config",
            searchPlaceholderValue: "Busque aquí el producto",
            choices: data
        });

        prod.addEventListener(
            'addItem',
            function(event) {
                //console.log(event.detail.value);
                render_producto(event.detail.value)
            }
        );
    })
}

function render_producto(id) {

    let selectalmacen_origen=document.getElementById("almacen_origen");
    let almacen_origen=selectalmacen_origen.options[selectalmacen_origen.selectedIndex].value;

    if(almacen_origen==""){

        alert("El Almacen de Origin obligatorio");
        return false;
    }
    

    fetch(urlgeneral+"/traslado/render_producto/"+id+"/"+almacen_origen)
    .then(res => res.json())
    .then(data => {
        //console.log(data);

        if(data.stock==undefined){

            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Para que puedas hacer una trasferencia de Mercaderia es muy importante que selecciones el Almacen Incial segun la sede donde te encuentres !',
                footer: '!! Se dectecto que estas utilizando un almacen diferente a tu sede de Origen'
              })
        }else{
            const stock = document.getElementById('stock_p');
            stock.value = data.stock;

        }
       
       
    })
}

//metodo para validar que el monto no sea mayor al stock
function validarstock(){

    const stock = document.getElementById('stock_p');
    const stock_ingresado = document.getElementById('stock_p');

    var bien = true;

    if(stock_ingresado>stock){
        bien = false;
    }

    return bien;


}

//metodo para treaer los datos de los conductores
const buscarconductores=async(document)=>{

    const url=urlgeneral+'/traslado/lista_conductor/'+document;

        fetch(url)
        .then(response => response.json())
        .then(data => {
        // aquí puedes trabajar con los datos recibidos
        console.log(data);
        $("#nombre_conductor").val(data.nombre);
        $("#conductor_id").val(data.id);

        })
        .catch(error => {
        // manejo del error
        });


}

buscarconductor.addEventListener('click',function(){

    let documento=$("#numero_conductor").val();
    buscarconductores(documento);
})

//METDO PARA VALIDAR SI LOS DATOS SON OBLIGATORIOS

function datosobligatorio() {
    var bien = true;

    var obligarotio = document.getElementsByClassName("obligatorio");
    var ncontroles = obligarotio.length;

    for (var i = 0; i < ncontroles; i++) {
        if (obligarotio[i].value == "") {
            bien = false;
            //alert("vacios");
            //obligarotio[i].parentNode.classList.add("form-control error");
            //swal("Here's a message!")
            //swal("Error!", "Los Campos Son Obligatorios!", "error")
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Todos los campos Marcados con (*) son Obligatorios !',
                footer: 'Y Es muy importante que agregue datos al detalle de compras'
              })
            //alert("Campos Obligatorios");
            //swal("Error!", "Los Campos Marcados de Rojo son requeridos!", "error")
            //alert("Los datos son Obliatorios");
        } else {
            obligarotio[i].parentNode.classList.remove("error")
        }
    }
    return bien;

    }

    //validar los impust que sean obligatorios

    function showSpinner() {
        $('.loader').show();
        $('#spinner').show();
      }
      
      function hideSpinner() {
        $('.loader').hide();
        $('#spinner').hide();
      }
      