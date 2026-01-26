<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('homescreen');
});

Route::get('/jadwal', function () {
    return view('jadwal');
});