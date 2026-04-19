/*window.onload = () => {
    validar_caja();
}
*/



urlgeeneral=$("#url_raiz_proyecto").val();

window.addEventListener("load", function (event) {

    $(".loader").fadeOut("slow");
    fechaactual();
    document.getElementById('imprimir').disabled=true;


});
//devolver la fecha actual
function fechaactual(){

    var fecha = new Date(); //Fecha actual
    var mes = fecha.getMonth()+1; //obteniendo mes
    var dia = fecha.getDate(); //obteniendo dia
    var ano = fecha.getFullYear(); //obteniendo año
    if(dia<10)
      dia='0'+dia; //agrega cero si el menor de 10
    if(mes<10)
      mes='0'+mes //agrega cero si el menor de 10
    document.getElementById('fecha').value=ano+"-"+mes+"-"+dia;

}
//METODO PARA SUMAR SI SELECCIONO LOS DATOS
let table = document.getElementsByTagName("table")[0];
let rows = table.rows;
let sum = 0;


for (var i = 0; i < rows.length; i++) {
    var checkbox = rows[i].cells[0].firstChild;
    if (checkbox) {
      checkbox.addEventListener("change", function() {
        var rowIndex = this.parentNode.parentNode.rowIndex;
        var cells = rows[rowIndex].cells;
        var value = parseFloat(cells[5].innerHTML);
        if (this.checked) {
          sum += value;
        } else {
          sum -= value;
        }
        console.log("La suma total es: " + sum);
        $("#importe").val(sum);
      });
    }
  }

  //METODO PARA VERIFICAR SI LA CAJA ES ABIERTA
  /*function validar_caja() {
    fetch(urlgeeneral+'/caja/validar')
    .then(res => res.json())
    .then(data => {
        if (data.status == 1) {
            $("#modal_caja").modal('show');
            document.getElementById('mensaje_caja').textContent = data.mensaje;
            document.getElementById('ir_caja').setAttribute('href',urlgeeneral+'/caja');
        }
        
    })
}*/

//METODO PARA GUARDAR LA AMORTIZACIÓN

$("#amortizar").on("click",function(){

     if(datosobligatorio() == true){


        var frm = new FormData();
        var csrf = document.querySelector('meta[name="csrf-token"]').content;

        let fecha=$("#fecha").val();//document.getElementById('fecha').val();
        let mont_rec=$("#importe").val();
        let docu_ref=$("#referencia").val();
        let cliente_id=$("#cliente_id").val();
        let credito_id=$("#credito_id").val();
        let obse_rec=$("#obse_rec").val();
        let selectfpag_rec=document.getElementById('fpag_rec');
        let fpag_rec=selectfpag_rec.options[selectfpag_rec.selectedIndex].value;

        if(fpag_rec==""){  Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'La Forma de pago es Obligatoria!',
          footer: ''
        }); return false;}

        let selectvendedor_id=document.getElementById('vendedor_id');
        let vendedor_id=selectvendedor_id.options[selectvendedor_id.selectedIndex].value;

        if(vendedor_id==""){  Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'El vendedor es Obligatorio!',
          footer: ''
        }); return false;}


        frm.append("_token", csrf);
        frm.append("fech_rec", fecha);
        frm.append("mont_rec", mont_rec);
        frm.append("docu_ref", docu_ref);
        frm.append("cliente_id", cliente_id);
        frm.append("credito_id", credito_id);
        frm.append("obse_rec", obse_rec);
        frm.append("vendedor_id", vendedor_id);
        frm.append("fpag_rec", fpag_rec);

        let saldo_real=$("#saldo_real").text();
        parseFloat(saldo_real);

        document.getElementById('amortizar').disabled=true;

        $("#staticBackdropdos").modal('show');

        //alert("Monto =>"+mont_rec + "saldo => "+saldo_real);

        if(parseFloat(mont_rec) > parseFloat(saldo_real)){

            $("#staticBackdropdos").modal('hide');

            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Lo siento no podemos procesar la amortización porque el monto ingresado excede el saldo pendiente!',
                footer: 'Por favor recargue la página para poder empezar de nuevo'
              })


        }else{


            $.ajax({
                type: "POST",
                url: urlgeeneral+"/amortizacion/crear",
                data: frm,
                dataType: 'json',
                contentType: false,
                processData: false,
                success : function (result) {

                    console.log(result);
                    $("#staticBackdropdos").modal('hide');

                    $("#imprimir").unbind('click', false);
                    //document.getElementById('pagar').disabled=true;
                    //$('#staticBackdropdos').css('display','none');
                    document.getElementById('amortizar').disabled=true;
                    document.getElementById('imprimir').disabled=false;



                    Swal.fire({
                        icon: 'success',
                        title: 'Ok...',
                        text: 'Cuota cobrada Correctamente',
                        footer: ''
                    }).then(function() {
                        location.reload();
                    });

                    //location.href =urlgeeneral+"/compras";


               },



                error : function(xhr,errmsg,err) {

                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Ocurrio un error al intentar generar la amortización!',
                        footer: 'Por favor recargue la página para poder empezar de nuevo'
                      })

                                console.log(xhr.status + ": " + xhr.responseText);
                                }

        });







        }











     }





});

//METODO PARA VALIDAR LOS DATOS VACIOS

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
                text: 'Los Campos marcados (*) son Obligatorio!',
                footer: ''
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


//IMPRIMIR RECIBO AMORTIZADO
$("#imprimir").on("click",function(data){

    //location.href =urlgeeneral+"/amortizacion/recibo";
    window.open(urlgeeneral+"/amortizacion/recibo", '_blank');


});



//FUNCION PARA CARGAR TODOS LOS CLIENTES ACTIVOS
/*$(".buscar").on("click",function(){

       $("#staticBackdrop").modal('show');
       $.get(urlgeeneral+"/amortizacion/clientes",function(data){

            let contenido="";
            for (var i = 0; i < data.length; i++) {

                contenido += "<tr>";
                contenido += "<td style='padding:1px;text-align:center' id='documento_cliente"+data[i].id+"'>" +  data[i].documento + " </td>";
                contenido += "<td style='padding:1px;text-align:center' id='razon_social"+data[i].id+"'>" +  data[i].razon_social + " <input type='hidden' id='dire_per"+data[i].id+"' value='"+data[i].dire_per+"'></td>";
                contenido += "<td style='padding:1px;text-align:center'>";
                contenido +='<a href="#" onclick="seleccionar(\''+data[i].id+'\')" type="button" class="btn btn-success"><i class="fas fa-check"></i> </a>'
                contenido +="</td>";

                contenido += "</tr>";

            }

            document.getElementById("listaclientes").innerHTML = contenido;
            $("#datatabledos").dataTable();


       });

});*/

//FUNCION PARA LLENAR TODO EL FORMULARIO
/*function seleccionar(id){

    //(id);

      $("#cliente_id").val(id);
      $("#documento").val($("#documento_cliente"+id).text());
      $("#navbarForm").val($("#razon_social"+id).text());
      $("#direccion").val($("#dire_per"+id).val());

     //alert(id);
     $.get(urlgeeneral+"/amortizacion/creditos/"+id,function(data){


          if(data.length>0){


            let contenido="";
            for (var i = 0; i < data.length; i++) {

             contenido += "<tr>";

             contenido += "<td style='padding:1px;text-align:center' id='credito_id"+data[i].id+"'>" +  data[i].credito_id + "</td>";
             contenido += "<td style='padding:1px;text-align:center' id='numero_cuo"+data[i].id+"'>" +  data[i].numero_cuo + "</td>";
             contenido += "<td style='padding:1px;text-align:center'  id='mont_cuo"+data[i].id+"'>" +  data[i].mont_cuo + "</td>";
             contenido += "<td style='padding:1px;text-align:center'  id='interes"+data[i].id+"'>0.00</td>";
             contenido += "<td style='padding:1px;text-align:center'  id='capi_cuo"+data[i].id+"'>" +  data[i].capi_cuo + "</td>";
               contenido += "<td style='padding:1px;text-align:center' id='fven_cuo"+data[i].id+"'>" +  data[i].fven_cuo + "</td>";
             contenido += "<td style='padding:1px;text-align:center' id='esta_cuo"+data[i].id+"'>" +  data[i].esta_cuo + "</td>";
             contenido += "<td style='padding:1px;text-align:center'>";
             contenido +='<a href="#" onclick="marcar(\''+data[i].id+'\')" type="button" class="btn btn-success"><i class="fas fa-check"></i> </a>'


             contenido += "</tr>";


            }

            document.getElementById("listadocuotas").innerHTML = contenido;
            $("#datatable").dataTable();
            $("#staticBackdrop").modal('hide');

            Swal.fire({
                icon: 'success',
                title: 'Bien...',
                text: 'Información Cargada Correctamente',
                footer: ''
            });



          }else{

            document.getElementById("listadocuotas").innerHTML = '';

            $("#staticBackdrop").modal('hide');

            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'El cliente no Tienen Ningun Credito Pendiente!',
                footer: ''
              })



          }








     });
}*/
