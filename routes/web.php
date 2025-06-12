<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::namespace('App\Http\Controllers\Sources')->prefix('endpoints')->group(function () {
    Route::post('vk', 'VkController@store');
});
