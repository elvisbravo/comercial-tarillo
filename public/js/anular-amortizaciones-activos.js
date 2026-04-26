urlgeeneral=$("#url_raiz_proyecto").val();
window.addEventListener("load", function (event) {
    $(".loader").fadeOut("slow");
    //fechaactual();
   // listar();
});



$(".align-middle").on("click",function(){

    let id=$("#nombresdata").val();



    $.get(urlgeeneral + "/anular-amortizaciones/recibo/" + id, function (data) {
        if ($.fn.DataTable.isDataTable('#datatable')) {
            $('#datatable').DataTable().destroy();
        }

        let contenido = "";
        for (var i = 0; i < data.length; i++) {
            contenido += "<tr>";
            contenido += "<td style='padding:1px;text-align:center'>" + data[i].id + "</td>";
            contenido += "<td style='padding:1px;text-align:center'> " + data[i].num_recibo + "</td>";
            contenido += "<td style='padding:1px;text-align:center'> " + data[i].mont_rec + "</td>";
            contenido += "<td style='padding:1px;text-align:center'> " + data[i].fech_rec + "</td>";
            contenido += "<td style='padding:1px;text-align:center'> " + data[i].esta_rec + "</td>";
            contenido += "<td style='padding:1px;text-align:center'> " + data[i].usuario + "</td>";
            contenido += "<td style='padding:1px;text-align:center'>";

            if (typeof canDelete !== 'undefined' && canDelete) {
                contenido += '<a href="#" onclick="abrimodal(' + data[i].id + ')" type="button" class="btn btn-danger waves-effect waves-light" title="Anular Amortización"><i class="fas fa-trash-alt"></i> </a>';
            }

            contenido += "</td>";
            contenido += "</tr>";
        }

        $('#lisatadocredtios').empty().html(contenido);
        initDataTable("#datatable");
    });






});

//metodo para anulzar la amortizacion
function abrimodal(id){

    $(".bs-example-modal-xl-y").modal("show");
    $("#codigo_credito").val(id);
    const numeros = [10, 2, 3, 4, 5];
   numeros.length = 1;
     console.log(numeros);


}

$("#guardar").on("click",function(){


    var obse_rec = document.getElementById('observacion').value;
    let codigo=$("#codigo_credito").val();

    var frm = new FormData();
    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    if(obse_rec.length==0){

        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'El campo Observación es Obligatorio!',
            footer: ''
          })

    }else{

        frm.append("_token", csrf);
        frm.append("obse_rec", obse_rec);
        frm.append("id", codigo);

        document.getElementById('guardar').disabled=true;

        $.ajax({
            type: "POST",
            url: urlgeeneral+"/anular-amortizaciones/anular",
            data: frm,
            dataType: 'json',
            contentType: false,
            processData: false,
            success : function (result) {

                console.log(result);
                document.getElementById('guardar').disabled=true;
                $(".bs-example-modal-xl-y").modal("hide");



                Swal.fire({
                    icon: 'success',
                    title: 'Ok...',
                    text: 'Anulado Correctamente',
                    footer: ''
                })

                //location.href =urlgeeneral+"/compras";


           },



            error : function(xhr,errmsg,err) {

                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Ocurrio un error al intentar anlar el Amortización!',
                    footer: 'Por favor recargue la página para poder empezar de nuevo'
                  })

                            console.log(xhr.status + ": " + xhr.responseText);
                            }

    });





    }



});


//METODO PARA VALIDAR QUE LOS DATOS SEAN VACIOS
