<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BingoController;

Route::get('/', function () {
    return view('login');
})->name('login');

Route::post('/auth', [BingoController::class, 'auth'])->name('bingo.auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [BingoController::class, 'logout'])->name('bingo.logout');
    Route::get('/bingo', [BingoController::class, 'bingo'])->name('bingo.index');
    Route::post('/bingo/claim', [BingoController::class, 'claim'])->name('bingo.claim');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('/admin/draw', [BingoController::class, 'draw'])->name('bingo.draw');
    Route::post('/admin/reset', [BingoController::class, 'resetGame'])->name('bingo.reset');
    Route::get('/admin', [BingoController::class, 'admin'])->name('bingo.admin');
});
