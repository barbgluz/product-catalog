<?php

use Illuminate\Support\Facades\Route;

Route::get('/products', '\App\Http\Controllers\ProductController@getProducts');
