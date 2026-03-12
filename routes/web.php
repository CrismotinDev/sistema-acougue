<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia; // Importe o Inertia

// Rota Inicial (Login)
Route::get('/', function () {
    return Inertia::render('Login'); // Renderiza o componente Login.vue
})->name('login');

// Rota do Dashboard (Protegida)
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard'); // Renderiza o componente Dashboard.vue
})->middleware('auth');

// Lógica de Login
Route::post('/login', function (Request $request) {
    $request->validate([
        'login' => 'required|string',
        'password' => 'required',
    ]);

    $fieldType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'login';

    if (Auth::attempt([$fieldType => $request->login, 'password' => $request->password], $request->remember)) {
        $request->session()->regenerate();

        // No Inertia, após um POST de sucesso, redirecionamos para a rota desejada
        return redirect()->intended('/dashboard');
    }

    throw ValidationException::withMessages([
        'login' => ['As credenciais informadas estão incorretas.'],
    ]);
});

// Logout
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
});
