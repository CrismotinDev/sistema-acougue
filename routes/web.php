<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProdutoController; // Importe o novo Controller
use App\Models\Produto; // Importe o Model
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Visitantes
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

// Autenticados
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // --- ROTA DE PEDIDOS ---
    // Alteramos para buscar os produtos do banco antes de abrir a tela
    Route::get('/pedidos/novo', function () {
        return Inertia::render('Pedidos/Create', [
            'produtosCadastrados' => Produto::orderBy('nome')->get()
        ]);
    })->name('pedidos.create');

    // --- ROTAS DE PRODUTOS ---
    // --- ROTAS DE PRODUTOS ---
    Route::get('/produtos', [ProdutoController::class, 'index'])->name('produtos.index');
    Route::post('/produtos', [ProdutoController::class, 'store'])->name('produtos.store');
    Route::put('/produtos/{produto}', [ProdutoController::class, 'update'])->name('produtos.update'); // ADICIONE ESTA
    Route::delete('/produtos/{produto}', [ProdutoController::class, 'destroy'])->name('produtos.destroy');

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});
