urlgeeneral=$("#url_raiz_proyecto").val();
window.addEventListener("load", function (event) {
    $(".loader").fadeOut("slow");

    var m = moment().format("YYYY-MM-DD");
    $("#fecha").val(m);


    //fechaactual();
   // listar();
});

$("#buscar").on("click",function(){

    var selectid_sector=document.getElementById("sector_id");
    var id_sector=selectid_sector.options[selectid_sector.selectedIndex].value;

    let fecha=$("#fecha").val();



     if(id_sector==''){

        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Los campos marcados de rojo son obligatorios!',
            footer: ''
          })



     }else{

        $.get(urlgeeneral+"/impresion-planilla/cuotas_pendientes/"+id_sector+"/"+fecha,function(data){

            let contenido="";
            for (var i = 0; i < data.length; i++) {
              contenido += "<tr>";
              contenido += "<td style='padding:1px;text-align:center'>" +  data[i].documento + "</td>";
              contenido += "<td style='padding:1px;text-align:center'> " + data[i].razon_social + "</td>";
              contenido += "<td style='padding:1px;text-align:center'> " + data[i].nomb_sec + "</td>";
              contenido += "<td style='padding:1px;text-align:center'> " + data[i].saldo + "</td>";

              contenido += "</tr>";


            }

            document.getElementById("lisatadocredtios").innerHTML = contenido;
            $("#datatable").dataTable();



        });


     }




});


$("#imprimir").on("click",function(){

    var selectid_sector=document.getElementById("sector_id");
    var id_sector=selectid_sector.options[selectid_sector.selectedIndex].value;

    let fecha=$("#fecha").val();



     if(id_sector==''){

        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Los campos marcados de rojo son obligatorios!',
            footer: ''
          })



     }else{


        window.open(urlgeeneral+"/impresion-planilla/masivo/"+id_sector+"/"+fecha, '_blank');
     }


})

