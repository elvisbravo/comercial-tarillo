/**
 * Función general para inicializar DataTables con configuración en español.
 * @param {string} selector - Selector CSS de la tabla (ej. '#datatable')
 * @param {object} options - Opciones adicionales para DataTables
 * @returns {object} - Instancia de DataTable
 */
function initDataTable(selector, options = {}) {
    const defaultOptions = {
        language: {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        // Destruir si ya existe para permitir re-inicialización
        destroy: true,
        responsive: true
    };

    // Combinar opciones por defecto con las proporcionadas
    const finalOptions = Object.assign({}, defaultOptions, options);

    return $(selector).DataTable(finalOptions);
}
