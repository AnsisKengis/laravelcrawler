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

Route::get('/', function () {
    return view('layouts.app');
});

Route::group(['prefix' => 'cars'], function() {
    Route::get('{category?}', 'CarsController@show');
    Route::get('{category?}/page-{page?}', 'CarsController@show');
    Route::get('{category?}/page-{page?}/year-{year?}', 'CarsController@show');
});