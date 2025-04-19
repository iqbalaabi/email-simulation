<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailPdfController;
use App\Http\Controllers\GmailController;
use App\Http\Controllers\LegalPdfController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/generate-pdf', [EmailPdfController::class, 'generate']);
// Route::get('/auth/gmail', [GmailController::class, 'gmailAuth']);

Route::get('/auth/gmail', [GmailController::class, 'redirectToGmail'])->name('gmail.auth');
Route::get('/auth/gmail/callback', [GmailController::class, 'handleGmailCallback'])->name('gmail.callback');


Route::get('/legalpdf', [LegalPdfController::class, 'index'])->name('legalpdf.index');
Route::post('/legalpdf/search', [LegalPdfController::class, 'search'])->name('legalpdf.search');
Route::get('/legalpdf/generate/{threadId}', [LegalPdfController::class, 'generate'])->name('legalpdf.generate');


