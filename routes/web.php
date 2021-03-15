<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// API
$router->group(['prefix' => 'api/v1/' ], function() use ($router) {

    $router->post("/coba-tambah", "HeartbeatController@addMonitor");
    $router->get("/coba-ambil", "HeartbeatController@getMonitors");
    $router->delete("/coba-hapus/{id}", "HeartbeatController@deleteMonitor");

});
