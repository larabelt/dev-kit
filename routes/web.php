<?php

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

use App\Services;

Route::get('/git/fetch', function () {
    $service = new Services\GitService();
    foreach ($service->packages() as $package) {
        $service->fetch($package);
    }
});

Route::get('/status', 'GitController@status');
Route::get('/test', 'PhpUnitController@test');

Route::get('/', function () {
    return view('welcome');
});
