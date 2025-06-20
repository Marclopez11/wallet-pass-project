<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WalletPassController;

Route::get('/', function () {
    return view('wallet-pass');
});

// Rutas para Wallet Pass de Eventos
Route::prefix('wallet-pass')->name('wallet-pass.')->group(function () {
    // Lista todos los pases
    Route::get('/', [WalletPassController::class, 'index'])->name('index');

    // Crear pase de evento
    Route::post('/create', [WalletPassController::class, 'createEventPass'])->name('create');

    // Descargar un pase específico
    Route::get('/download/{mobilePass}', [WalletPassController::class, 'downloadPass'])->name('download');
});
