<?php

use Illuminate\Support\Facades\Route;

Route::get('/phong-thi', function () {
    return view('thi'); 
});
Route::get('/ket-qua', function () {
    return view('ket-qua'); // Tìm file ket-qua.blade.php trong resources/views
});
Route::get('/', function () {
    return view('chon-de'); // Sẽ tìm file chon-de.blade.php trong resources/views
});