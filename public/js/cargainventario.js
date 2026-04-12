urlgeeneral=$("#url_raiz_proyecto").val();
$("#actualizar").hide();

//FUNCION LOAD
window.addEventListener("load", function (event) {

   index();
  $(".loader").fadeOut("slow"); 


});


function index(){

  $.get(urlgeeneral+"/cargainventario/index", function (data) {
    console.log(data);

      
  });

}
