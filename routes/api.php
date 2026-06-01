<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/login',  'Api\AuthController@login');

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', 'Api\AuthController@logout');
    Route::get('/me',      'Api\AuthController@me');

    // Vendedor
    Route::get('/vendedor/dashboard',            'Api\VendedorApiController@dashboard');
    Route::get('/vendedor/stock',                'Api\VendedorApiController@stock');
    Route::get('/vendedor/venta',                'Api\VentaApiController@index');
    Route::get('/vendedor/clientes/buscar',      'Api\VentaApiController@buscarClientes');
    Route::post('/vendedor/venta/guardar',       'Api\VentaApiController@guardar');
    Route::get('/vendedor/cobros',               'Api\CobrosApiController@index');
    Route::get('/vendedor/cobros/{id}/detalle',  'Api\CobrosApiController@detalle');
    Route::post('/vendedor/cobros/guardar',      'Api\CobrosApiController@guardar');

    // Historiales
    Route::get('/vendedor/historial-cargas',                       'Api\HistorialApiController@cargas');
    Route::get('/vendedor/historial-cargas/{id}/detalle',          'Api\HistorialApiController@cargaDetalle');
    Route::get('/vendedor/historial-ventas',                       'Api\HistorialApiController@ventas');
    Route::get('/vendedor/historial-ventas/{id}/detalle',          'Api\HistorialApiController@ventaDetalle');
    Route::get('/vendedor/historial-cobros',                       'Api\HistorialApiController@cobros');
    Route::get('/vendedor/historial-cobros/{id}/detalle',          'Api\HistorialApiController@cobroDetalle');

    Route::post('/consultar-dni-ruc',            'Api\ConsultaController@consultarDniRuc');
});
