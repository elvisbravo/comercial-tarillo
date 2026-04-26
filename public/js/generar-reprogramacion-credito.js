urlgeeneral=$("#url_raiz_proyecto").val();
$("#actualizar").hide();

window.addEventListener("load", function (event) {

    //listadocategorias();
    $(".loader").fadeOut("slow");
    document.getElementById('guardar').disabled=true;
    document.getElementById('cuotas').disabled=true;
    document.getElementById('contrato').disabled=true;
    
    fechaactual();


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
  document.getElementById('fecha_inicio').value=ano+"-"+mes+"-"+dia;

}


  //METODO PARA CARGAR LOS DATOS DE LOS CLIENTES

$.get(urlgeeneral+"/creditos-pendientes/listadoclientes",function(data){

  var contenido = "";
  for (var i = 0; i < data.length; i++) {
      contenido += "<tr>";

      contenido += "<td style='padding:1px;text-align:center' id='documento"+data[i].id+"'>" + data[i].documento + "</td>";
      contenido += "<td style='padding:1px;text-align:center' id='nombre"+data[i].id+"'>" + data[i].nomb_per + "</td>";
      contenido += "<td style='padding:1px;text-align:center'>" + data[i].dire_per + "</td>";
      contenido += "<td style='padding:1px;text-align:center'>";
      contenido +='<a href="#" onclick="seleccionar(\''+data[i].id+'\')" type="button" class="btn btn-success"><i class="fas fa-check"></i> </a>'
      contenido +="</td>";

      contenido += "</tr>";
  }

  document.getElementById("listaclientes").innerHTML = contenido;
  if ($.fn.DataTable.isDataTable('#datatables')) {
    $('#datatables').DataTable().destroy();
  }
  $("#datatables").DataTable();


});


//METODO PARA ASIGAR LOS DATOS
function seleccionar(id){
  document.getElementById('cuotas').disabled=true;
  document.getElementById('contrato').disabled=true;
  $("#documento").val($("#documento"+id).text());
  $("#nombresdata").val($("#nombre"+id).text());

  $(".bs-example-modal-xl-y").modal("show");


  console.log("hola "+ $("#id_persona_tempe").val(id));

  $(".bs-example-modal-xl").modal('hide');

  showSpinner();

  $.get(urlgeeneral+"/creditos-pendientes/creditos/"+id,function(data){

    hideSpinner();

     if(data.length>0){

         //document.getElementById("estado").disabled=false;
         let contenido="";
         //document.getElementById("lisatadocredtios").innerHTML = contenido;

         for (var i = 0; i < data.length; i++) {
             contenido += "<tr>";
             contenido += "<td style='padding:1px;text-align:center'  id='id_credito"+data[i].id+"'>" +  data[i].id  + "</td>";
             contenido += "<td style='padding:1px;text-align:center'> " + data[i].documento + "</td>";
             contenido += "<td style='padding:1px;text-align:center'> " + data[i].razon_social + "</td>";
             contenido += "<td style='padding:1px;text-align:center'> " + data[i].fpag_cre + "</td>";
             contenido += "<td style='padding:1px;text-align:center'> " + data[i].peri_cre + "</td>";
             contenido += "<td style='padding:1px;text-align:center'  id='periodo"+data[i].id+"'> " + data[i].periodo_pago + "</td>";
             contenido += "<td style='padding:1px;text-align:center'> " + data[i].impo_cre + "</td>";


             contenido += "<td style='padding:1px;text-align:center'  id='saldo"+data[i].id+"'> " + data[i].saldo + "</td>";
             contenido += "<td style='padding:1px;text-align:center'> <span class='badge bg-success'>Activo </span></td>";


             contenido += "<td style='padding:1px;text-align:center'>";
             //contenido +='<i class="fas fa-edit"></i>';
             contenido +='<a href="#" onclick="abrimodal('+ data[i].id +')" type="button" class="btn btn-info " ><i class="fas fa-check"></i> </a>';
             contenido +="</td>";
             contenido += "</tr>";


           }

           document.getElementById("listacreditos_pendientes").innerHTML = contenido;
           if ($.fn.DataTable.isDataTable('#datatableg')) {
             $('#datatableg').DataTable().destroy();
           }
           $("#datatableg").dataTable();


     }else{

         //document.getElementById("estado").disabled=true;
         document.getElementById("listacreditos_pendientes").innerHTML = '';

         Swal.fire({
             icon: 'error',
             title: 'Oops...',
             text: 'Lo Siento no se encontraron datos para el cliente seleccionado!',
             footer: ''
           });

           $(".bs-example-modal-xl-y").modal("hide");



     }




  });




}

//metodo para cargar la información en la vista

    function abrimodal(id){

      const saldo_credito=document.querySelector('#saldo_credito');
      saldo_credito.value=$("#saldo"+id).text();
      const id_credito= document.querySelector("#id_credito");


      id_credito.value=$("#id_credito"+id).text();

      $("#periodo").val($("#periodo"+id).text());


      $(".bs-example-modal-xl-y").modal("hide");

      Swal.fire({
        icon: 'success',
        title: 'Oops...',
        text: 'Credito Seleccionado Correctamente',
        footer: ''
    })


    }


//METODOS PARA SELECCIONAR LOS DATOS
/*function seleccionar(id){

  $("#documento_cliente").val($("#documento_cliente"+id).text());
  $("#id_cliente").val($("#id_clienteg"+id).val());
  $("#razon_social").val($("#razon_social"+id).text());
  $("#comprobante").val($("#serie_comprobante"+id).text());
  $("#monto").val($("#monto"+id).text());
  $("#fecha_credito").val($("#fecha_credito"+id).text());
  $("#id_venta").val(id);
  document.getElementById('monto').disabled=true;
$.get(urlgeeneral+"/traer_candado/"+$("#monto"+id).text(),function(data){

     $("#meses_temporal").val(data.nmeses);
     $("#cuotainical").val(data.monto_inicial);

});

$.get(urlgeeneral+"/creditos/deudaantigua/"+$("#id_clienteg"+id).val(),function(data){


          if(data==0){
              $total=0;

            $("#deudaacomulada").text($total.toFixed(2));

          }else{

            $("#deudaacomulada").text(data);

          }

});

  $("#staticBackdrop").modal("hide");

}*/

    //METODO PARA DESBLOQUEAR EL INPUT DE LOS DATOS
    $("#switch1").on("change",function(){

    if ($(this).is(':checked')) {

    console.log($(this).val() + ' is checked');
    document.getElementById('num_cuota').disabled=false;

    }else{
    console.log($(this).val() + ' is now checked');
    $("#num_cuota").val("");
    document.getElementById('num_cuota').disabled=true;

    }

    });

    //METODO PARA MOSTRAR AL USUARIOS EL NUMERO DE CUOTAS DISPONIBLES PARA PODER REALIZAR EL CREDITO
    /*$("#id_periodo").on("change",function(){

   
    let perido=$("#periodo").val();
    let cantidad=maxima(periodo(perido));
    if(isNaN(cantidad)){
      cantidad='';
    }

    $("#cuotainformativa").text(cantidad);

    // alert(cantidad);
    });*/

//METODO PARA ANALIZAR LAS CUOTAS MÁXIMAS Y PODER GENERAR EL CREDITO
/*$("#num_cuota").on("keyup",function(){

let cantidad=$("#num_cuota").val();
let perido=$("#periodo").val();

let condicion=maxima(periodo(perido));



});*/

//metodo para capturar el credito
function periodo_function(periodo_des){

  var id_periodo=0;

      switch(periodo_des){

        case 'Diario':
          id_periodo='1';
        break;
        case 'Semanal':
          id_periodo='2';
        break;
        case 'Quincenal':
          id_periodo='3';
        break;
        case 'Mensual':
          id_periodo='4';
        break;
        default:
          num=0;
          break;
      

      }

      return id_periodo;

}



//METODO PARA CALCULAR CUOTA
$("#calcuar_cuota").on("click",function(){

if(datosobligatorio() == true){


  $("#total_pagar").val("");
  $("#capital").val("");
 

const monto=$("#saldo_credito").val();
let fecha_cuota=$("#fecha_inicio").val();

/*var selectperiodo = document.getElementById("id_periodo"); 
var id_periodo = selectperiodo.options[selectperiodo.selectedIndex].value;*/

let num_cuota=$("#num_cuota").val();
//id del credito
    let id_credito=$("#id_credito").val();
    let perido=$("#periodo").val();


      //let cuotainical=$("#cuotainical").val();
      let fechas=[];
      let interes=$("#interes").val();
      let ncutoassistema=0;
      let capital=parseFloat(monto)//- parseFloat(cuotainical);


      let cuota=0;
      if(num_cuota==""){
      ncutoassistema=maxima(periodo_function(perido));


     

      $("#temporal_total_cuota").val(ncutoassistema);
      cuota=((parseFloat(capital)/parseInt(ncutoassistema))*(1+(interes*parseInt(ncutoassistema)/100))).toFixed(2);


      }else{

      ncutoassistema=num_cuota;
      
      $("#temporal_total_cuota").val(ncutoassistema);
      cuota=((parseFloat(capital)/parseInt(ncutoassistema))*(1+(interes*parseInt(ncutoassistema)/100))).toFixed(2);

      }





    
    //.toFixed(2)
    //2022-05-04 -2022-05-04








    var contenido = "";
    var suma=0;
    let total=0;
    let cuotas_num=0;

    let cuotas_activas=[];
showSpinner();

$.get(urlgeeneral+"/creditos-reprogramacion/cuotas_activas/"+id_credito,function(data){

 
    //cuotas_activas.push(data);
    //console.log(data );
    hideSpinner();
    var contenidoactivo = "";
  
     data.forEach(element => {

      console.log(element.sald_cap );
      contenidoactivo += "<tr style=''>";
      //suma=suma+cuotainical;
      contenidoactivo += "<td style='padding:1px;text-align:center' >"+element.numero_cuo+"</td>";
      contenidoactivo += "<td style='padding:1px;text-align:center' >" + moment(element.fven_cuo).format('DD-MM-YYYY')  + " </td>";
   
      contenidoactivo += "<td style='padding:1px;text-align:center' >" + 0 + "%  <input type='hidden' class='interes_cre'  value='"+interes+"'></td>";
      contenidoactivo += "<td style='padding:1px;text-align:center' >" +  element.sald_cap + "</td>";
      contenidoactivo += "<td style='padding:1px;text-align:center;text-decoration: line-through;color:red;font-weight: bold;' >A REPROGRAMAR </td>";
      contenidoactivo += "</tr>";


      });
      document.getElementById("cuotas_activas").innerHTML = contenidoactivo;

    

});


    
      let currentDate = moment();
      
      console.log('ID DEL PERIODO =>'+periodo_function(perido.replace(/\s/g, "")),perido.replace(/\s/g, ""));


      console.log("ID_EVNIADO=>"+periodo_function(perido.replace(/\s/g, "")));


        for(var i=0;i<ncutoassistema;i++){

        fechas[i]=currentDate.add(data(periodo_function(perido.replace(/\s/g, ""))), "days").format("DD-MM-YYYY");
        //mes.add(data(periodo(perido)),'day');
        //mes.add(moment.duration(data(id_periodo)),'day');
        //console.log("cantidad de días: "+mes);
        suma=parseFloat(suma)+parseFloat(cuota);

        let lastItem=ncutoassistema-1;

        if([i]==lastItem){
        total=(parseFloat(monto)- parseFloat(suma)).toFixed(2);
        console.log(total);
        //
        suma=parseFloat(total)+parseFloat(suma);
        }

        contenido += "<tr>";


        contenido += "<td style='padding:1px;text-align:center' >" +  parseInt(i+1,10) + "  <input type='hidden' class='numeros_cuota_cre' value='"+parseInt(i+1,10)+"'></td>";
        contenido += "<td style='padding:1px;text-align:center' >" +  fechas[i] + "  <input type='hidden' class='fechas_cre'  value='"+fechas[i]+"'></td>";
        contenido += "<td style='padding:1px;text-align:center' >"+perido+"</td>";
        contenido += "<td style='padding:1px;text-align:center' >" + interes + "%  <input type='hidden' class='interes_cre'  value='"+interes+"'></td>";
        if([i]==lastItem){

            contenido += "<td style='padding:1px;text-align:center' > <input type='text' onkeyup='calcular_importe("+i+")'  class='monto_cuo_cre' id='valox"+i+"'  value='"+(parseFloat(cuota)+parseFloat(total)).toFixed(2)+"'></td>";

        }else{
            contenido += "<td style='padding:1px;text-align:center' ><input type='text' onkeyup='calcular_importe("+i+")' class='monto_cuo_cre' id='valox"+i+"'  value='"+cuota+"'></td>";
        }








contenido += "</tr>";

suma=parseFloat(suma)+parseFloat(total);



}
//ultimacuota(suma);
document.getElementById("reprogramaconcuotas").innerHTML = contenido;
$("#capital").val(monto);
$("#interesporcentaje").val(0);
//$("#total_pagar").val(suma.toFixed(2));
document.getElementById('guardar').disabled=false;
total_compra();



}



});


function showSpinner() {
  $('.loader').show();
  $('#spinner').show();
}

function hideSpinner() {
  $('.loader').hide();
  $('#spinner').hide();
}

//METODO PARA REDONDEAR LAS CUOTAS
function calcular_importe(id){


const valox = document.getElementById('valox'+id);

const total_imp = valox.value * 1;
valox.textContent = total_imp.toFixed(2);

console.log(valox.textContent = total_imp.toFixed(2));

total_compra();


}
//METOD PARA CALCULAR EL VALOR TOTAL
    function  total_compra(){


        const monto_cuo_cre=document.getElementsByClassName('monto_cuo_cre');
        let total_importe = 0;



        for (var i = 0; i < monto_cuo_cre.length; i++) {

        let subt =monto_cuo_cre[i].value;

        total_importe += parseFloat(subt);


    }

console.log("total "+ parseFloat(total_importe));



$("#total_pagar").val(total_importe.toFixed(2));
$("#capital").val(total_importe.toFixed(2));





}
//FUNCIONA PARA SUMAR LA DIFERENCIA EN AL ULTIMA CUOTA
  /*function ultimacuota(suma){

        let total=0;

        let valor=parseFloat(monto)- parseFloat(suma);
        if(valor==0){
        total=0;
        }else if(valor>0){
        total=parseFloat(monto)- parseFloat(suma);
        }

        return (total).toFixed(2);


  }*/

  //funciion para asignar el numero de periodo



//FUNCION PARA VALIDAR LA CANTIDAD DEL MES
function data(id_periodo){

       var num=0;

      switch(id_periodo){

              case '1':
                  num= 1;
                  break;
              case '2':
                  num=7;
                  break;
              case '3':
                  num=15;
                  break;
              case '4':
                  num=30;
                  break;
              default:
                  num=0;
                  break;
              }

              return num;

}


//FUNCION PARA VALIAR LA CANTIDAD MAXIMA POR DIA
function maxima(id_periodo){

          var cuotamaxima = 0;
          var dias=30;
          var semanas=4;
          var quincenas=2;
          var meses=1;
          var nmesesguardado=$("#meses_temporal").val();
          switch(id_periodo){
          case '1':
          cuotamaxima= parseInt(dias)*parseInt(nmesesguardado);
          break;
          case '2':
          cuotamaxima= parseInt(semanas)*parseInt(nmesesguardado);
          break;
          case '3':
          cuotamaxima= parseInt(quincenas)*parseInt(nmesesguardado);
          break;
          case '4':
          cuotamaxima= parseInt(meses)*parseInt(nmesesguardado);
          break;
          default:
          cuotamaxima=0;
          break;
          }

          return cuotamaxima;

}

//METODO PARA GUARDAR LOS DATOS DE LOS CREDITOS
todo=[];
$("#guardar").on("click",function(){

var peri_cre=$("#temporal_total_cuota").val();
let mont_cre=$("#saldo_credito").val();
let fpag_cre=$("#fecha_inicio").val();
let fech_cre=$("#fecha_credito").val();
let inte_cre=$("#interes").val();
let cliente_id=$("#id_cliente").val();
let obse_cre=$("#observacion").val();
let tipo_doc=$("#comprobante").val();
let id_venta=$("#id_venta").val();


var combo_select = document.getElementById("conceto_id");
var id_con = combo_select.options[combo_select.selectedIndex].value;

let id_credito=$("#id_credito").val();

//alert(periodo);

var cabezera=new cabezeracredito(id_credito);
todo.push(cabezera);

const numero_cuo=document.getElementsByClassName('numeros_cuota_cre');
const fven_cuo=document.getElementsByClassName('fechas_cre');
const mont_cuo=document.getElementsByClassName('monto_cuo_cre');

for (var i = 0; i < mont_cuo.length; i++) {

var detalledata=new detalle(mont_cuo[i].value,fven_cuo[i].value,numero_cuo[i].value);
todo.push(detalledata);

}

document.getElementById('guardar').disabled=true;
document.getElementById('calcuar_cuota').disabled=true;

$("#staticBackdropdos").modal('show');
console.log(todo);

showSpinner();

        $.ajax({
        type : "POST",
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        contentType:"application/json",
        dataType:'json',
        data:JSON.stringify(todo),
        processData:false,
        cache:false,
        url :urlgeeneral+ "/creditos-reprogramacion/crear",
        success : function (result) {

          hideSpinner();

        console.log(result);
        //credito(result);
        document.getElementById('guardar').disabled=true;
        document.getElementById('calcuar_cuota').disabled=true;
        document.getElementById('cuotas').disabled=false;
        document.getElementById('contrato').disabled=false;

//$("#imprimir").unbind('click', false);
//document.getElementById('pagar').disabled=true;


          Swal.fire({
              icon: 'success',
              title: 'Bien...',
              text: 'Credito Generada Correctamente',
              footer: ''
          });

    bloquear();
    $("#staticBackdropdos").modal('hide');
//location.href =urlgeeneral+"/creditos";


},
  error : function(xhr,errmsg,err) {

          Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Lo siento hubo un error al intentar generar el credito!',
              footer: 'Por favor recargue la página para poder empezar de nuevo'
            })

              console.log(xhr.status + ": " + xhr.responseText);
              //location.href =urlgeeneral+"/creditos";


              }

  });





});



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
    text: 'Los campos Marcados con (* ) son Obligatorio!',
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



//ARAMR EL ARRY CABEZERA DEL CREDITO
function cabezeracredito(credito_id){

this.credito_id = credito_id;


}

function detalle(mont_cuo,fven_cuo,numero_cuo){

this.mont_cuo=mont_cuo;
this.fven_cuo=fven_cuo;
this.numero_cuo=numero_cuo;


}

//BLOQUEAR CAJAS DE TEXTO

function bloquear(){

var obligarotio = document.getElementsByClassName("obligatorio");
var ncontroles = obligarotio.length;

for (var i = 0; i < ncontroles; i++) {

obligarotio[i].disabled=true;
}


}

//IMPRIMIR LAS CUOTAS DEL CREDITO

$("#cuotas").on("click",function(){

  let id_credito=$("#id_credito").val();

window.open(urlgeeneral+"/creditos-pendientes/cuota/"+id_credito, '_blank');


});

//METODO PARA IMPRIMIR EL CONTRATO DEL CREDITO
function credito(id){

$("#contrato").on("click",function(){

window.open(urlgeeneral+"/creditos/contrato/"+id, '_blank');

});



}

