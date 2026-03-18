<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProdutoController extends Controller
{
    public function index()
    {
        return Inertia::render('Produtos/Index', [
            'produtos' => Produto::orderBy('nome')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:produtos,nome'
        ]);

        Produto::create($validated);

        return redirect()->back(); // Atualiza a tabela no Vue automaticamente
    }

    public function update(Request $request, Produto $produto)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:produtos,nome,' . $produto->id
        ]);

        $produto->update($validated);

        return redirect()->back();
    }

    public function destroy(Produto $produto)
    {
        $produto->delete();
        return redirect()->back();
    }
}
