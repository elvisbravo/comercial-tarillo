
urlgeeneral=$("#url_raiz_proyecto").val();
$("#actualizar").hide();

//FUNCION LOAD
window.addEventListener("load", function (event) {

   
   tipocomprobantes();
   sedes();
  $(".loader").fadeOut("slow"); 


});


function index(){

  $.get(urlgeeneral+"/reportecompra/index", function (data) {
    console.log(data);

      
  });

}

$(function () {
  var today = new Date();
  var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
  var time = today.getHours() + ":" + today.getMinutes();
  var dateTime = date+' '+time;
  $("#form_datetime").datetimepicker({
      format: 'yyyy-mm-dd hh:ii',
      autoclose: true,
      todayBtn: true,
      startDate: dateTime
  });
});

 function tipocomprobantes(){

   
  
  
    $.get(urlgeeneral+"/reportecompra/tipocomprobantes",function(data){
      console.log(data);
      let contenido="";
      contenido += "<option value='' >--Seleccionar--</option >";
      contenido += "<option value='0'>TODOS</option >";
      for (var i = 0; i < data.length; i++) {

          console.log(data[i]);
          contenido += "<option value='"+data[i].id+"' >"+ data[i].descripcion+"</option >";
      }

      document.getElementById("tipo_comprobante_id").innerHTML=contenido;



      
        
  
        });
  
  
  } 
  function sedes(){

       
  
  
    $.get(urlgeeneral+"/reportecompra/sede",function(data){

        let contenido="";
        contenido += "<option value='' >--Seleccionar--</option >";
        contenido += "<option value='0' >TODOS</option >";
        for (var i = 0; i < data.length; i++) {


          contenido += "<option value='"+data[i].id+"' >"+ data[i].nombre+"</option >";

        }

        document.getElementById("sede_id").innerHTML=contenido;

  
        
  
        });
  
  
  }

  //MÉTODO PARA GENERAR REPORTE DE ACUERDO A LAS FECHAS
 
//mandar como parametros, para listar reportes de acuerdo a ellos

function generar_reporte(){

  if (datosobligatorio() == true) {

    let selecComprobante = document.getElementById('tipo_comprobante_id');
    let comprobante_id = selecComprobante.options[selecComprobante.selectedIndex].value;

    let selectSucursal = document.getElementById('sede_id');
    let sede_id = selectSucursal.options[selectSucursal.selectedIndex].value;

    
    //alert(comprobante_id);
    var desde=$("#desde").val();
    var hasta=$("#hasta").val();

    $.get(urlgeeneral+"/reportecompra/listareporte/"+ desde + "/" + hasta + "/" + comprobante_id +"/" + sede_id, function (data) {
      llenar_reporte(data);
     
    
    });
  }
  
}

function llenar_reporte(data){
  if ($.fn.DataTable.isDataTable('#datatable')) {
      $('#datatable').DataTable().destroy();
  }
  let contenido="";
  for (var i = 0; i < data.length; i++) {
    contenido += "<tr>";
    contenido += "<td style='padding:1px;text-align:center'>" +  parseInt(i+1,10) + "</td>";
    contenido += "<td style='padding:1px;text-align:center'> " + data[i].id + "</td>";
    contenido += "<td style='padding:1px;text-align:center'> " + data[i].nombre_comercial + "</td>";
    contenido += "<td style='padding:1px;text-align:center'> " + data[i].nombre + "</td>";
    contenido += "<td style='padding:1px;text-align:center'> " + data[i].moneda + "</td>";
    contenido += "<td style='padding:1px;text-align:center'> " + data[i].name + "</td>";
    contenido += "<td style='padding:1px;text-align:center'> " + data[i].forma_pago + "</td>";
    contenido += "<td style='padding:1px;text-align:center'> " + data[i].tipo_pago + "</td>";
    contenido += "<td style='padding:1px;text-align:center'> " + data[i].tipo_comprobante + "</td>";
    contenido += "<td style='padding:1px;text-align:center'> " + data[i].fecha_ingreso + "</td>";
    contenido += "<td style='padding:1px;text-align:center'> " + data[i].serie_comprobante + "</td>";
    contenido += "<td style='padding:1px;text-align:center'> " + data[i].correlativo_comprobante + "</td>";
    contenido += "<td style='padding:1px;text-align:center'> " + data[i].compra_venta + "</td>";
    contenido += "<td style='padding:1px;text-align:center'> " + data[i].total_igv + "</td>";
    contenido += "<td style='padding:1px;text-align:center'> " + data[i].total_compra + "</td>";
    contenido += "<td style='padding:1px;text-align:center'> " + data[i].porcentaje_igv + "</td>";
    contenido += "<td style='padding:1px;text-align:center'> " + data[i].sede + "</td>";
    contenido += "<td style='padding:1px;text-align:center'> " + data[i].total_compra_flete + "</td>";
    
    contenido += "<td style='padding:1px;text-align:center'>";
    //contenido +='<i class="fas fa-edit"></i>';
    contenido +="</td>";
    contenido += "</tr>";


  }

  document.getElementById("listado_reporte_compras").innerHTML = contenido;
  initDataTable("#datatable");
}
$("#exportarexcel").on("click",function(){

  //window.open("http://127.0.0.1:8000/facturacion/resumen/35" , "ventana1" , "width=420,height=600,scrollbars=NO")
  let selecComprobante = document.getElementById('tipo_comprobante_id');
  let comprobante_id = selecComprobante.options[selecComprobante.selectedIndex].value;

  let selectSucursal = document.getElementById('sede_id');
  let sede_id = selectSucursal.options[selectSucursal.selectedIndex].value;
  
  var desde=$("#desde").val();
  var hasta=$("#hasta").val();
  
  window.location.href="exportar/"+ desde + "/" + hasta + "/" + comprobante_id +"/" + sede_id;

});
function datosobligatorio() {
  var bien = true;

  var obligarotio = document.getElementsByClassName("obligatorio");
  var ncontroles = obligarotio.length;

  for (var i = 0; i < ncontroles; i++) {
      if (obligarotio[i].value == "") {
         bien = false;
         /* alert("vacios");
          obligarotio[i].parentNode.classList.add("form-control error");
          swal("Here's a message!")
          swal("Error!", "Los Campos Son Obligatorios!", "error") */
          Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Todos los campos con * son Obligatorios!',
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
  
