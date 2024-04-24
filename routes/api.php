<?php

use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::get('/products', '\App\Http\Controllers\ProductController@getProducts');
});
