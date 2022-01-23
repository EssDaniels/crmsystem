<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

$NS = MODULES_NS . 'Importer\Http\Controllers\\';

$router->name('importers.')->group(function () use ($router, $NS) {

    $router->get('importers/raport/{filename}/{path}', $NS . 'ImporterController@raport');
    $router->get('importers/insert', $NS . 'ImporterController@add');
    $router->get('importers/inserttask1', $NS . 'TaskOneController@taskOne');
    $router->post('/uploadFile', $NS . 'ImporterController@uploadFile')->name('uploadFile');
    $router->get('importers/raport', $NS . 'ImporterController@index');
    $router->get('/', $NS . 'ImporterController@index');
});

$router->resource('importers', $NS . 'ImporterController');
