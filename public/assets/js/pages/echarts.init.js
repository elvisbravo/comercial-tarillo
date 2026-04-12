const urlgeeneral=$("#url_raiz_proyecto").val();
const tbody = document.getElementById('clientes_morosos');

const tbodydos=document.getElementById('clientes_buen_pagadores');

function getChartColorsArray(e) {
    e = $(e).attr("data-colors");
    return (e = JSON.parse(e)).map(function (e) {
        e = e.replace(" ", "");
        if (-1 == e.indexOf("--")) return e;
        e = getComputedStyle(document.documentElement).getPropertyValue(e);
        return e || void 0;
    });
}

//LISTAR LA DATA
fetch(urlgeeneral+'/ventasPorMesYAnio')
.then(response => response.json()) // Convierte la respuesta a formato JSON
.then(data => {
  // Procesa los datos para ajustarlos al formato requerido por ECharts
  const xAxisData = data.map(item => item.mes);
  const seriesData = data.map(item => item.total);

   //console.log(seriesData)

  // Crea la opción para la gráfica con los datos procesados
  var lineColors = getChartColorsArray("#line-chart"),
    dom = document.getElementById("line-chart"),
    myChart = echarts.init(dom),
    app = {};
(option = null),
    (option = {
        grid: { zlevel: 0, x: 50, x2: 50, y: 30, y2: 30, borderWidth: 0, backgroundColor: "rgba(0,0,0,0)", borderColor: "rgba(0,0,0,0)" },
        xAxis: { type: "category", data: xAxisData, axisLine: { lineStyle: { color: "#858d98" } } },
        yAxis: { type: "value", axisLine: { lineStyle: { color: "#858d98" } }, splitLine: { lineStyle: { color: "rgba(133, 141, 152, 0.1)" } } },
        series: [{ data: seriesData, type: "line" }],
        color: lineColors,
    }),


    option && "object" == typeof option && myChart.setOption(option, !0);



})
.catch(error => console.error('Error al obtener los datos:', error));


//MOSTRAR LA DATA DE VENTA AL CREDITO Y AL CONTADO
fetch(urlgeeneral+'/VentasContadoCredito')
.then(response => response.json()) // Convierte la respuesta a formato JSON
.then(data => {

    // Obtiene los nombres de los meses para el eje X
    const xAxisData = data.contado.map(item => item.mes);

          // Obtiene los totales de ventas al contado para la serie "Contado"
    const ventasContado = data.contado.map(item => item.total);

      // Obtiene los totales de ventas al crédito para la serie "Crédito"
    const ventasCredito = data.credito.map(item => item.total);

     // Obtiene los totales para la serie "Average Temperature"
     const ventasPromedio = data.contado.map((item, index) => {
        const totalContado = parseFloat(item.total);
        const totalCredito = parseFloat(data.credito[index].total);
        return (totalContado + totalCredito) / 2;
      });



    var mixlinebarColors = getChartColorsArray("#mix-line-bar"),
    dom = document.getElementById("mix-line-bar"),
    myChart = echarts.init(dom);

(option = null),
    ((app = {}).title = "Data view"),
    (option = {
        grid: { zlevel: 0, x: 80, x2: 50, y: 30, y2: 30, borderWidth: 0, backgroundColor: "rgba(0,0,0,0)", borderColor: "rgba(0,0,0,0)" },
        tooltip: { trigger: "axis", axisPointer: { type: "cross", crossStyle: { color: "#999" } } },
        toolbox: {
            orient: "center",
            left: 0,
            top: 20,
            feature: { dataView: { readOnly: !1, title: "Data View" }, magicType: { type: ["line", "bar"], title: { line: "For line chart", bar: "For bar chart" } }, restore: { title: "restore" }, saveAsImage: { title: "Download Image" } },
        },
        color: mixlinebarColors,
        legend: { data: ["Ventas al Contado", "Ventas al Crédito","Ventas Promedio"], textStyle: { color: "#858d98" } },
        xAxis: [{ type: "category", data: xAxisData, axisPointer: { type: "shadow" }, axisLine: { lineStyle: { color: "#858d98" } } }],
        yAxis: [
            { type: "value", name: "Ventas", min: 0, max: 190000, interval: 20000, axisLine: { lineStyle: { color: "#858d98" } }, splitLine: { lineStyle: { color: "rgba(133, 141, 152, 0.1)" } }, axisLabel: { formatter: "{value} ml" } },
            { type: "value", name: "Promedio", min: 0, max: 200000, interval: 10000, axisLine: { lineStyle: { color: "#858d98" } }, splitLine: { lineStyle: { color: "rgba(133, 141, 152, 0.1)" } }, axisLabel: { formatter: "{value} Ã‚Â°C" } },
        ],
        series: [
            { name: "Ventas al Contado", type: "bar", data: ventasContado  },
            { name: "Ventas al Crédito", type: "bar", data: ventasCredito  },
            { name: "Ventas Promedio", type: "line", yAxisIndex: 1, data: ventasPromedio },
        ],
    }),
    option && "object" == typeof option && myChart.setOption(option, !0);







})
.catch(error => console.error('Error al obtener los datos:', error));





//MOSTRAR LA DATA EN LA GRAFICA DE TORTAS

fetch(urlgeeneral+'/top5ProductosMasVendidos')
.then(response => response.json()) // Convierte la respuesta a formato JSON
.then(data => {

    const nombresProductos = data.map(item => item.nombre_producto);
    const valoresProductos = data.map(item => item.total_unidades_vendidas);


    var doughnutColors = getChartColorsArray("#doughnut-chart"),
    dom = document.getElementById("doughnut-chart"),
    myChart = echarts.init(dom),
    app = {};
(option = null),
    (option = {
        tooltip: { trigger: "item", formatter: "{a} <br/>{b}: {c} ({d}%)" },
        legend: { orient: "vertical", x: "left", data:nombresProductos, textStyle: { color: "#858d98" } },
        color: doughnutColors,
        series: [
            {
                name: "Total sales",
                type: "pie",
                radius: ["50%", "70%"],
                avoidLabelOverlap: !1,
                label: { normal: { show: !1, position: "center" }, emphasis: { show: !0, textStyle: { fontSize: "30", fontWeight: "bold" } } },
                labelLine: { normal: { show: !1 } },
                data: data.map((item, index) => ({ value: valoresProductos[index], name: item.nombre_producto })),
            },
        ],
    }),
    option && "object" == typeof option && myChart.setOption(option, !0);







})
.catch(error => console.error('Error al obtener los datos:', error));






fetch(urlgeeneral+'/top5ProductosMasVendidos')
.then(response => response.json()) // Convierte la respuesta a formato JSON
.then(data => {


    const nombresProductos = data.map(item => item.nombre_producto);
    const valoresProductos = data.map(item => item.total_unidades_vendidas);



var pieColors = getChartColorsArray("#pie-chart"),
dom = document.getElementById("pie-chart"),
myChart = echarts.init(dom),
app = {};
(option = null),
(option = {
    tooltip: { trigger: "item", formatter: "{a} <br/>{b} : {c} ({d}%)" },
    legend: { orient: "vertical", left: "left", data: nombresProductos, textStyle: { color: "#858d98" } },
    color: pieColors,
    series: [
        {
            name: "Total sales",
            type: "pie",
            radius: "55%",
            center: ["50%", "60%"],
            data: data.map((item, index) => ({ value: valoresProductos[index], name: item.nombre_producto })),
            itemStyle: { emphasis: { shadowBlur: 10, shadowOffsetX: 0, shadowColor: "rgba(0, 0, 0, 0.5)" } },
        },
    ],
}),
option && "object" == typeof option && myChart.setOption(option, !0);




})
.catch(error => console.error('Error al obtener los datos:', error));


fetch(urlgeeneral+'/top10ClientesMasDeudores')
.then(response => response.json()) // Convierte la respuesta a formato JSON
.then(data => {

     //console.log(data)

     cargarDatosTabla(data);


})
.catch(error => console.error('Error al obtener los datos:', error));


fetch(urlgeeneral+'/top10ClientesMasCompraron')
.then(response => response.json()) // Convierte la respuesta a formato JSON
.then(data => {

     //console.log(data)

     cargarDatosTabla_dos(data);


})
.catch(error => console.error('Error al obtener los datos:', error));





var scatterColors = getChartColorsArray("#scatter-chart"),
dom = document.getElementById("scatter-chart"),
myChart = echarts.init(dom),
app = {};
(option = null),
(option = {
    grid: { zlevel: 0, x: 50, x2: 50, y: 30, y2: 30, borderWidth: 0, backgroundColor: "rgba(0,0,0,0)", borderColor: "rgba(0,0,0,0)" },
    xAxis: { axisLine: { lineStyle: { color: "#858d98" } }, splitLine: { lineStyle: { color: "rgba(133, 141, 152, 0.1)" } } },
    yAxis: { axisLine: { lineStyle: { color: "#858d98" } }, splitLine: { lineStyle: { color: "rgba(133, 141, 152, 0.1)" } } },
    series: [
        {
            symbolSize: 10,
            data: [
                [10, 8.04],
                [8, 6.95],
                [13, 7.58],
                [9, 8.81],
                [11, 8.33],
                [14, 9.96],
                [6, 7.24],
                [4, 4.26],
                [12, 10.84],
                [7, 4.82],
                [5, 5.68],
            ],
            type: "scatter",
        },
    ],
    color: scatterColors,



















})





//LISTAS LOS CLIENTES MÁS DEUDORES

function crearFila(data){


    const fila = document.createElement('tr');

    const icono = document.createElement('td');
    const iconoDiv = document.createElement('div');
    iconoDiv.classList.add('font-size-22', 'text-danger');
    iconoDiv.innerHTML = '<i class="bx bx-down-arrow-circle d-block"></i>';
    icono.appendChild(iconoDiv);
    fila.appendChild(icono);

    const nombreCliente = document.createElement('td');
    nombreCliente.textContent = data.nombre_cliente;
    fila.appendChild(nombreCliente);

    const totalDeuda = document.createElement('td');
    totalDeuda.textContent = 'S/ ' + parseFloat(data.total_deuda).toFixed(2);
    fila.appendChild(totalDeuda);

    return fila;




}


// Función para cargar los datos en la tabla
function cargarDatosTabla(data) {
    data.forEach(dato => {
      const fila = crearFila(dato);
      tbody.appendChild(fila);
    });
  }

  //

  function crearFila_dos(data){


    const fila = document.createElement('tr');

    const icono = document.createElement('td');
    const iconoDiv = document.createElement('div');
    iconoDiv.classList.add('font-size-22', 'text-success');
    iconoDiv.innerHTML = '<i class="bx bx-down-arrow-circle d-block"></i>';
    icono.appendChild(iconoDiv);
    fila.appendChild(icono);

    const nombreCliente = document.createElement('td');
    nombreCliente.textContent = data.nombre_cliente;
    fila.appendChild(nombreCliente);

    const totalDeuda = document.createElement('td');
    totalDeuda.textContent = 'S/ ' + parseFloat(data.total_compras).toFixed(2);
    fila.appendChild(totalDeuda);

    return fila;



  }


  function cargarDatosTabla_dos(data) {
    data.forEach(dato => {
      const fila = crearFila_dos(dato);
      tbodydos.appendChild(fila);
    });
  }
