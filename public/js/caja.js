const urlgeeneral = $("#url_raiz_proyecto").val();

const abrir_cerrar = document.getElementById('abrir_cerrar_caja');
const estado_caja = document.getElementById('estado_caja');
const form_caja = document.getElementById('aperturar_caja');
const fechaInput = document.getElementById('fecha_apertura');
const guardarBtn = document.getElementById('guardar');

const idcaja = document.getElementById('idcaja');

const fechaActual = new Date();
const fechaFormateada = fechaActual.toISOString().substr(0, 10);
fechaInput.value = fechaFormateada;

abrir_cerrar.addEventListener('click', (e) => {
    if (estado_caja.value == 0) {
        $("#modal-aperturar-caja").modal('show');
    } else {
        Swal.fire({
            title: '¿Desea cerrar Caja?',
            text: "Cierra caja para volver aperturarla!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Cerrar Caja'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(urlgeeneral+"/cerrar_caja/"+idcaja.value)
                .then(res => res.json())
                .then(data => {
                    if (data == 'ok') {
                        Swal.fire({
                            position: 'top-center',
                            icon: 'success',
                            title: 'Se cerro la caja correctamente',
                            showConfirmButton: false,
                            timer: 2500
                        });

                        setTimeout(() => {
                            location.reload();
                        }, 2500);
                    } else {
                        alert('Error');
                    }
                })

            }
        })
    }
});

form_caja.addEventListener('submit', (e) => {
    e.preventDefault();

    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    guardarBtn.disabled = true;


    const formData = new FormData(form_caja);

    formData.append('_token', csrf);

    fetch(urlgeeneral+"/caja/crear",{
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data == "OK") {
            $("#modal-aperturar-caja").modal('hide');

            Swal.fire({
                position: 'top-center',
                icon: 'success',
                title: 'Se aperturo la caja correctamente',
                showConfirmButton: false,
                timer: 2500
            });

            setTimeout(() => {
                location.reload();
            }, 2500);

        } else {
            alert('Error');
        }
    })
})

function getChartColorsArray(e) {
    e = $(e).attr("data-colors");
    return (e = JSON.parse(e)).map(function (e) {
        e = e.replace(" ", "");
        if (-1 == e.indexOf("--")) return e;
        e = getComputedStyle(document.documentElement).getPropertyValue(e);
        return e || void 0;
    });
}

var lineColors = getChartColorsArray("#line-chart");
var dom = document.getElementById("line-chart");

//var lineColors2 = getChartColorsArray("#line-chart2")
//var dom2 = document.getElementById("line-chart2");

var myChart = echarts.init(dom);
//var myChart2 = echarts.init(dom2);
var app = {};

(option = null),
    (option = {
        grid: {
            zlevel: 0,
            x: 50,
            x2: 50,
            y: 30,
            y2: 30,
            borderWidth: 0,
            backgroundColor: "rgba(0,0,0,0)",
            borderColor: "rgba(0,0,0,0)",
        },
        xAxis: {
            type: "category",
            data: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
            axisLine: { lineStyle: { color: "#858d98" } },
        },
        yAxis: {
            type: "value",
            axisLine: { lineStyle: { color: "#858d98" } },
            splitLine: { lineStyle: { color: "rgba(133, 141, 152, 0.1)" } },
        },
        series: [
            { data: [820, 932, 901, 934, 1290, 1330, 1320], type: "line" },
        ],
        color: lineColors,
    }),
    option && "object" == typeof option && myChart.setOption(option, !0);

//option && "object" == typeof option && myChart2.setOption(option, !0);
