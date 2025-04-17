<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/generate-pdf', [App\Http\Controllers\EmailPdfController::class, 'generate']);
