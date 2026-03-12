<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/login', function (Request $request) {
    // 1. Validamos apenas como 'required' e 'string', pois pode não ser e-mail
    $request->validate([
        'login' => 'required|string',
        'password' => 'required',
    ]);

    // 2. Descobrimos se o que foi digitado é um e-mail ou username
    $fieldType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'login';

    // 3. Tentamos o login com o campo dinâmico
    $credentials = [
        $fieldType => $request->login,
        'password' => $request->password
    ];

    if (Auth::attempt($credentials, $request->remember)) {
        $request->session()->regenerate();
        return response()->json(['user' => Auth::user()], 200);
    }

    // Erro caso falhe
    throw ValidationException::withMessages([
        'login' => ['As credenciais informadas estão incorretas.'],
    ]);
});
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return response()->json(['message' => 'Logged out'], 200);
});
