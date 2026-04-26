const urlgeneral = document.getElementById('url_raiz_proyecto').value

initDataTable("#dataTableExample", {
    columnDefs: [{
        "searchable": false,
        "orderable": false,
        "targets": 0
    }],
    order: [[6, 'desc']]
}).on('order.dt search.dt', function () {
    let i = 1;
    $('#dataTableExample').DataTable().cells(null, 0, { search: 'applied', order: 'applied' }).every(function (cell) {
        this.data(i++);
    });
}).draw();