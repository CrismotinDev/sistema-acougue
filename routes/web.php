<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProdutoController; // Importe o novo Controller
use Illuminate\Support\Facades\Route;

// Visitantes
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

// Autenticados
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [PedidoController::class, 'dashboard'])->name('dashboard');

    // --- ROTA DE PEDIDOS ---
    Route::get('/pedidos/novo', [PedidoController::class, 'create'])->name('pedidos.create');
    Route::post('/pedidos', [PedidoController::class, 'store'])->name('pedidos.store');
    Route::get('/pedidos/{pedido}/editar', [PedidoController::class, 'edit'])->name('pedidos.edit');
    Route::get('/pedidos/{pedido}/imprimir', [PedidoController::class, 'print'])->name('pedidos.print');
    Route::put('/pedidos/{pedido}', [PedidoController::class, 'update'])->name('pedidos.update');
    Route::delete('/pedidos/{pedido}', [PedidoController::class, 'destroy'])->name('pedidos.destroy');

    // --- ROTAS DE PRODUTOS ---

    Route::get('/produtos', [ProdutoController::class, 'index'])->name('produtos.index');
    Route::post('/produtos', [ProdutoController::class, 'store'])->name('produtos.store');
    Route::put('/produtos/{produto}', [ProdutoController::class, 'update'])->name('produtos.update'); // ADICIONE ESTA
    Route::delete('/produtos/{produto}', [ProdutoController::class, 'destroy'])->name('produtos.destroy');

    // --- ROTAS DE Clientes ---
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/criar', [ClienteController::class, 'create'])->name('clientes.create');
    Route::get('/clientes/{cliente}/editar', [ClienteController::class, 'edit'])->name('clientes.edit');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');


    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});
