
const urlgeneral = document.getElementById('url_raiz_proyecto').value;

const tras = document.getElementById('traslados');

render();

function render(){
    fetch(urlgeneral+"/traslado/mostrar")
    .then(res => res.json())
    .then(data => {
        $("#dataTableExample").DataTable().destroy();
        tras.innerHTML = data;
        $("#dataTableExample").DataTable();
    })
}