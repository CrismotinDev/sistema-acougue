<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Grupo de Autenticação (Visitantes)
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

// Grupo Protegido (Usuários Logados)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/pedidos/novo', function () {
        return Inertia::render('Pedidos/Create');
    })->name('pedidos.create');

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});
