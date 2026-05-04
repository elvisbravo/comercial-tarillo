window.addEventListener("load", function (event) {
    $(".loader").fadeOut("slow");
});

const urlgeneral = document.getElementById("url_raiz_proyecto").value;

initDataTable("#dataTableExample", {
    paging: false,
});
