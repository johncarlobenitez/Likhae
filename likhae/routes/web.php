<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('guest.home');
});

Route::get('/products', function () {
    return view('guest.products');
});

Route::get('/products/{slug}', function () {
    return view('guest.product-details');
});