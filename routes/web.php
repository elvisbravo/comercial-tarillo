<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth/login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/ventasPorMesYAnio', 'HomeController@ventasPorMesYAnio');
Route::get('/VentasContadoCredito', 'HomeController@VentasContadoCredito');
Route::get('/top5ProductosMasVendidos', 'HomeController@top5ProductosMasVendidos');
Route::get('/top10ClientesMasDeudores', 'HomeController@top10ClientesMasDeudores');
Route::get('/top10ClientesMasCompraron', 'HomeController@top10ClientesMasCompraron');

//METODO PARA CREAR LOS PERMISOS
Route::get('permisos/listapermisos', 'PermisosController@listapermisos');
Route::post('permisos/crear', 'PermisosController@crear');
Route::resource('permisos', 'PermisosController');

//CONTROLADOR PARA SEDES
Route::resource('sedes', 'SedeController');
Route::post('sedes/update_tipo_envio', 'SedeController@update_envio');
Route::post('sedes/update_estado', 'SedeController@update_estado');
Route::get('sedes/correlativos/{id}', 'SedeController@correlativos');
Route::post('sedes/select_comprobante', 'SedeController@select_comprobante');
Route::post('sedes/guardar_correlativos', 'SedeController@guardar_correlativos');
Route::post('sedes/seleccionar_sede', 'SedeController@seleccionar_sede');

//CONTROLADOR PARA ALMACENES
Route::get('almacenes/render', 'AlmacenController@render');
Route::post('almacenes/guardar', 'AlmacenController@guardar');
Route::get('almacenes/eliminar/{id}', 'AlmacenController@eliminar');
Route::resource('almacenes', 'AlmacenController');

//CONTROLADOR PARA KARDEX
Route::post('kardex/guardar', 'KardexController@guardar');
Route::post('kardex/traer_productos', 'KardexController@traer_productos');
Route::resource('kardex', 'KardexController');

//CONTROLADOR PARA TRASLADOS
Route::get('ubicaciones_stock_sede/{documento}', 'TrasladoController@ubicaciones_stock_sede');
Route::get('traslado/lista_conductor/{documento}', 'TrasladoController@lista_conductor');
Route::get('traslado/detalle/{id}', 'TrasladoController@detail');
Route::get('traslado/render_producto/{id}/{ubicacion_id}', 'TrasladoController@render_producto');
Route::get('traslado/traer_clientes', 'TrasladoController@clientes');
Route::get('traslado/traer_ubigeo/{id}', 'TrasladoController@ubigeo');
Route::get('traslado/mostrar', 'TrasladoController@mostrar');
Route::get('traslado/guia', 'TrasladoController@guia');
Route::get('traslado/traer_productos', 'TrasladoController@traer_productos');
Route::get('traslado/listadoguias', 'TrasladoController@listadoguias');
Route::get('traslado/generar_guia/{id}', 'TrasladoController@generar_guia');
Route::post('traslado/data_producto', 'TrasladoController@data_producto');
Route::get('traslados/show/{id}', 'TrasladoController@show');
Route::delete('traslados/eliminar/{id}', 'TrasladoController@eliminar');
Route::post('traslado/guardar', 'TrasladoController@guardar');
Route::resource('traslados', 'TrasladoController');

//CONTROLADOR DE COLORES
Route::get('colores/listadocolores', 'ColoresController@listadocolores');
Route::get('colores/editarcolor/{id}', 'ColoresController@editarcolor');
Route::post('colores/crear', 'ColoresController@crear');
Route::post('colores/modificar', 'ColoresController@update');
Route::delete('colores/eliminar/{id}', 'ColoresController@eliminar');
Route::resource('colores', 'ColoresController');

//METODOS PARA GUARDAR LOS CANDADOS
Route::get('candados-creditos/listacandados', 'CandadoController@listacandados');
Route::get('candados-creditos/edit/{id}', 'CandadoController@edit');
Route::post('candados-creditos/crear', 'CandadoController@crear');
Route::post('candados-creditos/modificar', 'CandadoController@update');
Route::delete('candados-creditos/eliminar/{id}', 'CandadoController@eliminar');
Route::resource('candados-creditos', 'CandadoController');


//CONTROLADOR DE MODELOS
Route::get('modelos/litadomodelos', 'ModelosController@litadomodelos');
Route::get('modelos/editarmodelo/{id}', 'ModelosController@editarmodelo');
Route::post('modelos/crear', 'ModelosController@crear');
Route::post('modelos/modificar', 'ModelosController@update');
Route::delete('modelos/eliminar/{id}', 'ModelosController@eliminar');
Route::resource('modelos', 'ModelosController');

//CONTROLADOR DE MARCAS

Route::get('marcas/listadomarca', 'MarcasController@listadomarca');
Route::get('marcas/editarmarca/{id}', 'MarcasController@editarmarca');
Route::post('marcas/crear', 'MarcasController@crear');
Route::post('marcas/modificar', 'MarcasController@update');
Route::delete('marcas/eliminar/{id}', 'MarcasController@eliminar');
Route::resource('marcas', 'MarcasController');

//CONTROLADOR DE PROVEEDORES
Route::get('proveedores/listadomarca', 'ProveedorController@liestadoproveedores');
Route::get('proveedores/editar/{id}', 'ProveedorController@editar');
Route::post('proveedores/crear', 'ProveedorController@crear');
Route::post('proveedores/modificar', 'ProveedorController@update');
Route::delete('proveedores/eliminar/{id}', 'ProveedorController@eliminar');
Route::delete('proveedores/activar/{id}', 'ProveedorController@activar');
Route::resource('proveedores', 'ProveedorController');

//controlador categorias
Route::get('categorias/listado', 'CategoriasController@listado');
Route::get('categorias/editar/{id}', 'CategoriasController@editar');
Route::post('categorias/crear', 'CategoriasController@crear');
Route::post('categorias/modificar', 'CategoriasController@update');
Route::delete('categorias/eliminar/{id}', 'CategoriasController@eliminar');
Route::delete('categorias/activar/{id}', 'CategoriasController@activar');
Route::resource('categorias', 'CategoriasController');


//controlador subcategorias
Route::get('subcategorias/listado', 'SubCategoriasController@listado');
Route::get('subcategorias/listadocategorias', 'SubCategoriasController@listadocategorias');
Route::get('subcategorias/editar/{id}', 'SubCategoriasController@editar');
Route::post('subcategorias/crear', 'SubCategoriasController@crear');
Route::post('subcategorias/modificar', 'SubCategoriasController@update');
Route::delete('subcategorias/eliminar/{id}', 'SubCategoriasController@eliminar');
Route::delete('subcategorias/activar/{id}', 'SubCategoriasController@activar');
Route::resource('subcategorias', 'SubCategoriasController');

//PRODUCTOS MAESTRO
Route::post('productos-maestro/modificar', 'ProductosMaestroController@update');
Route::get('productos-maestro/listadoproductosmaestro', 'ProductosMaestroController@listadoproductosmaestro');
Route::resource('productos-maestro', 'ProductosMaestroController');

//controlador productos
Route::get('productos/impuesto_cliente', 'ProductosController@impuesto_cliente');
Route::get('productos/listarproductos', 'ProductosController@listarproductos');
Route::get('productos/unidades', 'ProductosController@unidades');
Route::get('productos/consultacreacionalmacen/{id_producto}/{id_almacen}', 'ProductosController@consultacreacionalmacen');
Route::get('productos/subcategorias/{id}', 'ProductosController@subcategorias');
Route::get('productos/listadoproductos', 'ProductosController@listadoproductos');
Route::get('productos/editar/{id}', 'ProductosController@editar');
Route::get('productos/searchproduct/{text}', 'ProductosController@searchproduct');
Route::get('productos/listaproduct', 'ProductosController@listaproduct');
Route::post('productos/modificar', 'ProductosController@update');
Route::delete('productos/eliminar/{id}', 'ProductosController@eliminar');
Route::delete('productos/activar/{id}', 'ProductosController@activar');
Route::resource('productos', 'ProductosController');

//CONTROLADOR DE COMPRAS
Route::get('compras/ver/{id}', 'ComprasController@ver');
Route::get('compras/listacompras', 'ComprasController@listacompras');
Route::get('compras/topoproductos', 'ComprasController@topoproductos');
Route::post('compras/crear', 'ComprasController@crear');
Route::get('compras/unidades', 'ComprasController@unidades');
Route::delete('compras/eliminar/{id}', 'ComprasController@eliminar');
Route::resource('compras', 'ComprasController');



//MOVIMIENTOS
Route::post('movimiento/add', 'MovimientosController@guardar');
Route::resource('movimientos', 'MovimientosController');

//CONCEPTOS
Route::get('conceptos/filtrar_tipo/{id}', 'ConceptosController@filtrar');
Route::post('conceptos/guardar', 'ConceptosController@guardar');
Route::get('conceptos/listado', 'ConceptosController@listado');
Route::resource('conceptos', 'ConceptosController');

Route::get('lista-precios/listaprecios', 'ListaPreciosController@listaprecios');
Route::get('lista-precios/editarlista/{id}', 'ListaPreciosController@editarlista');
Route::post('lista-precios/crear', 'ListaPreciosController@crear');
Route::post('lista-precios/modificar', 'ListaPreciosController@update');
Route::delete('lista-precios/eliminar/{id}', 'ListaPreciosController@eliminar');
Route::resource('lista-precios', 'ListaPreciosController');


//RUTAS DE LA VISTA PRECIOS
Route::get('precios/lista_precios', 'PreciosController@lista_precios');
Route::get('precios/validar_producto/{id_lista}/{id_producto}/{id_sede}', 'PreciosController@validar_producto');
Route::get('precios/sedes', 'PreciosController@sedes');
Route::post('precios/crear', 'PreciosController@crear');
Route::post('precios/modificar', 'PreciosController@update');
Route::resource('precios', 'PreciosController');

//CONTROLADOR CLIENTE
Route::get('clientes/listado/{estado}', 'ClientesController@listado');
Route::get('clientes/sector', 'ClientesController@sector');
Route::resource('clientes', 'ClientesController');
Route::post('clientes/crear', 'ClientesController@crear');
Route::post('clientes/modificardir', 'ClientesController@guardardireccion');
Route::post('clientes/modificardatos', 'ClientesController@update');
Route::get('clientes/validar/{docuemnto}', 'ClientesController@validar');
Route::get('clientes/listadirecciones/{id_cliente}', 'ClientesController@listadirecciones');
Route::delete('clientes/eliminar/{id}', 'ClientesController@eliminar');
Route::delete('clientes/activar/{id}', 'ClientesController@activar');
Route::get('clientes/direccion/{id_cliente}', 'ClientesController@direccion');
Route::get('getImagenesDireccion/{id_cliente}', 'ClientesController@getImagesAddress');
Route::get('clientes/imagenDireccionEliminar/{id_direccion}', 'ClientesController@deleteImagenDireccion');


//CONTROLADOR CAJA
Route::get('caja/validar', 'CajaController@validar_caja');
Route::get('cerrar_caja/{id}', 'CajaController@cerrar_caja');
Route::get('caja/verificarapertura', 'CajaController@verificarapertura');
Route::resource('caja', 'CajaController');
Route::get('caja/cantidadtransacciones', 'CajaController@cantidadtransacciones');
Route::get('caja/totaldiario', 'CajaController@totaldiario');
Route::get('caja/ultimomneto', 'CajaController@ultimomneto');
Route::get('caja/ultimoid', 'CajaController@ultimoid');
Route::post('caja/crear', 'CajaController@crear');
Route::post('caja/modificar', 'CajaController@update');

//CONTROLADOR CREDITOS

Route::get('creditos/ventas_credito', 'CreditoController@ventas_credito');
Route::get('creditos/contrato/{codigo}', 'CreditoController@contrato');
Route::get('creditos/validador_candados/{monto}', 'CreditoController@validador_candados');
Route::get('creditos/deudaantigua/{codigo}', 'CreditoController@deudaantigua');
Route::get('creditos/cuotas', 'CreditoController@cuotas');
Route::resource('creditos', 'CreditoController');
Route::get('creditos', 'CreditoController@index');
Route::post('creditos/crear', 'CreditoController@crear');

//CONTROLADOR DE AMORTIZACIONES
Route::get('amortizacion/cantidad_amortizada', 'AmortizacionesController@cantidad_amortizada');
Route::get('amortizacion/detalle_product/{id}', 'AmortizacionesController@detalle_product');
Route::get('amortizacion/clientes', 'AmortizacionesController@clientes');
Route::get('amortizacion/recibo', 'AmortizacionesController@recibo');
Route::get('amortizacion/creditos', 'AmortizacionesController@creditos');
Route::post('amortizacion/anular_recibo_amort', 'AmortizacionesController@anular_recibo_amort');
Route::resource('amortizacion', 'AmortizacionesController');
Route::post('amortizacion/crear', 'AmortizacionesController@crear');

//CONTROLADOR PARA CREAR LOS SECTORES DE LOS
Route::get('sectores/listado', 'SectorController@listado');
Route::get('sectores/edit/{id}', 'SectorController@edit');
Route::delete('sectores/eliminar/{id}', 'SectorController@eliminar');
Route::post('sectores/crear', 'SectorController@crear');
Route::post('sectores/modificar', 'SectorController@update');
Route::resource('sectores', 'SectorController');

//REPORTES DE CREDITOS PENDIENTES
Route::get('creditos-pendientes/listadoclientes', 'ReporteCreditos@listadoclientes');
Route::get('creditos-pendientes/creditos/{codigo}/{estado}', 'ReporteCreditos@creditos');
Route::get('creditos-pendientes/estado/{codigo}', 'ReporteCreditos@estado');
Route::get('creditos-pendientes/cuotas/{codigo}', 'ReporteCreditos@cuotas');
Route::get('creditos-pendientes/cuota/{codigo}', 'ReporteCreditos@cuota');
Route::get('creditos-pendientes/estado_cuenta/{codigo}/{estado}', 'ReporteCreditos@estado_cuenta');
Route::resource('creditos-pendientes', 'ReporteCreditos');

//CONSULTA DE AMORTIZACIONES
Route::get('consulta-amortizaciones/recibo/{codigo}', 'ConsultaAmortizacionesController@recibo');
Route::get('listamortizaciones/{codigo}/{fechauno}/{fechados}', 'ConsultaAmortizacionesController@listamortizaciones');
Route::resource('consulta-amortizaciones', 'ConsultaAmortizacionesController');

//METODO PARA ANULAR CREDITO
Route::get('anular-credito/verificardorcuota/{codigo}', 'AnularCreditoController@verificardorcuota');
Route::post('anular-credito/anular', 'AnularCreditoController@anular');
Route::resource('anular-credito', 'AnularCreditoController');

//MTODO PARA ANULAR AMORTIZACIONES
Route::post('anular-amortizaciones/anular', 'AnularAmortizacionController@anular');
Route::get('anular-amortizaciones/recibo/{codigo}', 'AnularAmortizacionController@recibo');
Route::resource('anular-amortizaciones', 'AnularAmortizacionController');

//METODO PARA LA IMPRESIÓN DE CUOTAS VENCIDAS
Route::get('impresion-planilla/cuotas_pendientes/{codigo_sector}/{fecha}', 'ImpresionCuotasVencidas@cuotas_pendientes');
Route::get('impresion-planilla/masivo/{codigo_sector}/{fecha}', 'ImpresionCuotasVencidas@masivo');
Route::resource('impresion-planilla', 'ImpresionCuotasVencidas');

Route::get('venta/ticket/{id}', 'VentaController@ticket');
Route::post('consultar_dni_ruc', 'VentaController@consultar_dni_ruc');
Route::post('generar_venta', 'VentaController@generar_venta');
Route::post('search-productos-tipo-venta', 'VentaController@search_productos');
Route::get('render-productos-tipo-venta', 'VentaController@render_productos');
Route::get('traer_candado/{id}', 'VentaController@traer_candado');
//CONTROLADOR VENTAS
Route::get('ventas/listado', 'VentaController@listado');
Route::resource('ventas', 'VentaController');
Route::get('enviar-comprobante/{id}', 'VentaController@enviar_comprobante');
Route::post('generar-nota-credito', 'VentaController@generarNotaCredito');
Route::get('delete-nota-venta/{id}', 'VentaController@deleteNotaVenta');


//CONTROLADOR HISTORICO CAJA

Route::get('pos', 'VentaController@pos');
Route::get('tipo-comprobantes-venta', 'VentaController@traer_comprobantes_venta');
Route::get('tipo-documento-identidad', 'VentaController@traer_documento_identidad');
Route::get('forma-pago', 'VentaController@forma_pago');
Route::get('bancos-venta', 'VentaController@bancos_ventas');
Route::post('render-productos-tipo-venta', 'VentaController@render_productos');
Route::get('render-categorias-productos', 'VentaController@listCategories');
Route::post('traerPrecio', 'VentaController@traer_precios');

//Rutas para resumenes de caja por pdf
Route::get('historico-caja/pdfcaja/{idcajafisica}', 'HistoricoCajaController@pdfcaja');
Route::get('resumen-caja/resumenCaja/{id_caja}/{fecha_apertura}/{fecha_cierre}', 'HistoricoCajaController@resumenCaja');
Route::resource('historico-caja', 'HistoricoCajaController');

//METODO PARA CONTROLAR LAS UBICACIONES DE LOS ALMACENES
Route::get('stock-location/almacenes', 'StockLocationController@almacenes');
Route::get('stock-location/editar/{id}', 'StockLocationController@editar');
Route::get('stock-location/tipohubicacion', 'StockLocationController@tipohubicacion');
Route::post('stock-location/crear', 'StockLocationController@crear');
Route::post('stock-location/modificar', 'StockLocationController@update');
Route::delete('stock-location/eliminar/{id}', 'StockLocationController@eliminar');
Route::delete('stock-location/activar/{id}', 'StockLocationController@activar');
Route::resource('stock-location', 'StockLocationController');

//RUTAS PARA LA PROGRAMACIÓN DE CREDITOS
Route::post('creditos-reprogramacion/crear', 'ReprogramacionCredito@crear');
Route::get('creditos-reprogramacion/cuotas_activas/{codigo}', 'ReprogramacionCredito@cuotas_activas');
Route::resource('creditos-reprogramacion', 'ReprogramacionCredito');


//RUTAS PARA EL MATENIMIENTO DE CONDUCTOR
Route::get('conductor/listadoconductores', 'ConductorController@listadoconductores');
Route::get('conductor/editar/{id}', 'ConductorController@editar');
Route::post('conductor/crear', 'ConductorController@crear');
Route::post('conductor/modificar', 'ConductorController@update');
Route::delete('conductor/eliminar/{id}', 'ConductorController@eliminar');
Route::resource('conductor', 'ConductorController');

//RUTAS DE IMPUESTOS

Route::get('impuestos/listadoimpuesto', 'ImpuestoController@listadoimpuesto');
Route::get('impuestos/listadoempresas', 'ImpuestoController@listadoempresas');
Route::get('impuestos/editarimpuesto/{id}', 'ImpuestoController@editarimpuesto');
Route::post('impuestos/crear', 'ImpuestoController@crear');
Route::post('impuestos/modificar', 'ImpuestoController@modificar');
Route::delete('impuestos/eliminar/{id}', 'ImpuestoController@eliminar');
Route::resource('impuestos', 'ImpuestoController');

//RUTAS DE CARGA INVENTARIO
Route::get('/cargainventario', 'CargaInventarioController@index');
Route::post('cargainventario/import', 'CargaInventarioController@import');
Route::resource('cargainventario', 'CargaInventarioController');

//RUTAS DE REPORTE VENTAS
Route::get('/reporteventas', 'ReporteVentasController@index');
Route::get('/reporteventas/tipocomprobantes', 'ReporteVentasController@tipocomprobantes');
Route::get('/reporteventas/sedes', 'ReporteVentasController@sede');
Route::post('/reporteventas/consulta', 'ReporteVentasController@consulta');
Route::resource('reporteventas', 'ReporteVentasController');

//RUTAS DE REPORTE DE VENTAS POR SUCURSALES
Route::post('/reporteallventas/consulta', 'ReporteAllVentasController@consulta');
Route::resource('reporteallventas', 'ReporteAllVentasController');

//RUTAS DE VEHÍCULOS
Route::get('vehiculos/listadovehiculo', 'VehiculoController@listadovehiculo');
Route::get('vehiculos/listadotipovehiculo', 'VehiculoController@listadotipovehiculo');
Route::get('vehiculos/editarvehiculo/{id}', 'VehiculoController@editarvehiculo');
Route::post('vehiculos/crear', 'VehiculoController@crear');
Route::post('vehiculos/modificar', 'VehiculoController@modificar');
Route::delete('vehiculos/eliminar/{id}', 'VehiculoController@eliminar');

Route::resource('vehiculos', 'VehiculoController');


Route::get('recepcion/listadoguiasrecepcion', 'RecepcionGuiaController@listadoguiasrecepcion');
Route::get('articulo_demandado/{id}/{id_guia}', 'RecepcionGuiaController@articulo_demandado');
Route::get('detalle/{id}', 'RecepcionGuiaController@detalle');
Route::get('guardar/{id}/{ubicaciones_id}', 'RecepcionGuiaController@guardar');
Route::get('recepcion/{id}', 'RecepcionGuiaController@show');
Route::post('recepcion-mercaderia/crear', 'RecepcionGuiaController@save');
Route::resource('recepcion-mercaderia', 'RecepcionGuiaController');

//RUTAS DE REPORTE COMPRAS
Route::get('reportecompra/index', 'ReporteComprasController@index');
Route::get('reportecompra/tipocomprobantes', 'ReporteComprasController@tipocomprobantes');
Route::get('reportecompra/sede', 'ReporteComprasController@sede');
Route::get('reportecompra/listareporte/{desde}/{hasta}/{T_comprobante}/{sede}', 'ReporteComprasController@listareporte');
Route::get('exportar/{desde}/{hasta}/{T_comprobante}/{sede}', 'ReporteComprasController@exportarCompra')->name('exportar');
Route::resource('reportecompras', 'ReporteComprasController');

//RUTAS DE BANCOS
Route::get('bancos/index', 'BancosController@index');
Route::get('bancos/listadobancos', 'BancosController@listadobancos');
Route::get('bancos/editarbanco/{id}', 'BancosController@editarbanco');
Route::post('bancos/crear', 'BancosController@crear');
Route::post('bancos/modificar', 'BancosController@modificar');
Route::delete('bancos/eliminar/{id}', 'BancosController@eliminar');
Route::resource('bancos', 'BancosController');


//RUTAS CUENTAS BANCARIAS
Route::get('cuentasbancarias/index', 'CuentasBancariasController@index');
Route::get('cuentasbancarias/listadoCuentasBancarias', 'CuentasBancariasController@listadoCuentasBancarias');
Route::get('cuentasbancarias/listadoBancos', 'CuentasBancariasController@listadoBancos');
Route::get('cuentasbancarias/editarCuentasBancarias/{id}', 'CuentasBancariasController@editarCuentasBancarias');
Route::post('cuentasbancarias/crear', 'CuentasBancariasController@crear');
Route::post('cuentasbancarias/modificar', 'CuentasBancariasController@modificar');
Route::delete('cuentasbancarias/eliminar/{id}', 'CuentasBancariasController@eliminar');
Route::resource('cuentasbancarias', 'CuentasBancariasController');



//CONTROLADOR DE INVENTARIO
Route::get('reporte/listarinventario/{codigo}', 'ReporteInventarioController@listarinventario');
Route::get('reporteinventarios/index', 'ReporteInventarioController@index');
Route::get('exportar/{ubicacion}', 'ReporteInventarioController@exportarInventario')->name('exportar');
Route::resource('reporteinventarios', 'ReporteInventarioController');

//RUTAS VENDEDORES
Route::get('vendedores/index', 'VendedorController@index');
Route::get('vendedores/listadovendedores', 'VendedorController@listadovendedores');
Route::get('vendedores/listadousuarios', 'VendedorController@listadousuarios');
Route::get('vendedores/editarvendedor/{id}', 'VendedorController@editarvendedor');
Route::post('vendedores/crear', 'VendedorController@crear');
Route::post('vendedores/modificar', 'VendedorController@modificar');
Route::delete('vendedores/eliminar/{id}', 'VendedorController@eliminar');
Route::resource('vendedores', 'VendedorController');

//FORMULARIO PARA PODER CREAR LAS GUIAS
Route::post('guias/guardar', 'GuiasController@guardar');
Route::resource('guias', 'GuiasController');


//TRANSPORTISTAS
Route::get('transportistas/index', 'TransportistasController@index');
Route::get('transportistas/listatransportistas', 'TransportistasController@liestadotransportistas');
Route::get('transportistas/editar/{id}', 'TransportistasController@editar');
Route::post('transportistas/crear', 'TransportistasController@crear');
Route::post('transportistas/modificar', 'TransportistasController@update');
Route::delete('transportistas/eliminar/{id}', 'TransportistasController@eliminar');
Route::resource('transportistas', 'TransportistasController');

//MODULO_PADRE
Route::get('modulo_padre/index', 'ModuloPadreController@index');
Route::get('modulo_padre/getListParentModule', 'ModuloPadreController@getListParentModule');
Route::get('modulo_padre/getParentModuleById/{id}', 'ModuloPadreController@getParentModuleById');
Route::post('modulo_padre/create', 'ModuloPadreController@create');
Route::post('modulo_padre/edit', 'ModuloPadreController@edit');
Route::delete('modulo_padre/delete/{id}', 'ModuloPadreController@delete');
Route::resource('modulo_padre', 'ModuloPadreController');

//MODULO
Route::get('modulo/index', 'ModuloController@index');
Route::get('modulo/getListModule', 'ModuloController@getListModule');
Route::get('modulo/getParentModules', 'ModuloController@getParentModules');
Route::get('modulo/getModuleById/{id}', 'ModuloController@getModuleById');
Route::post('modulo/create', 'ModuloController@create');
Route::post('modulo/edit', 'ModuloController@edit');
Route::delete('modulo/delete/{id}', 'ModuloController@delete');
Route::resource('modulo', 'ModuloController');

//FUNCION
Route::get('funcion/index', 'FuncionController@index');
Route::get('funcion/getListFunction', 'FuncionController@getListFunction');
Route::get('funcion/getFunctionById/{id}', 'FuncionController@getFunctionById');
Route::post('funcion/create', 'FuncionController@create');
Route::post('funcion/edit', 'FuncionController@edit');
Route::delete('funcion/delete/{id}', 'FuncionController@delete');
Route::resource('funcion', 'FuncionController');

Route::get('reportecuotas/getData', 'ReporteCuotasController@getData');
Route::get('reportecuotas', 'ReporteCuotasController@index')->name('reportecuotas.index');

Route::group(['middleware' => ['auth']], function () {
    Route::resource('roles', 'RoleController');
    Route::get('users/getList', 'UserController@getList');
    Route::resource('users', 'UserController');
});

//ACCIONES
Route::get('acciones/getList', 'AccionesController@getList');
Route::get('acciones/getById/{id}', 'AccionesController@getById');
Route::post('acciones/update', 'AccionesController@update');
Route::resource('acciones', 'AccionesController');
Route::resource('permisos', 'PermisosController');
Route::get('permisos/getByRole/{id}', 'PermisosController@getPermissionsByRole');
Route::post('permisos/save', 'PermisosController@save');
Route::resource('configuracion-acciones', 'ConfiguracionAccionesController');
Route::get('configuracion-acciones/getAssignments/{id}', 'ConfiguracionAccionesController@getAssignments');
Route::post('configuracion-acciones/save', 'ConfiguracionAccionesController@saveAssignments');
