<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PedidoController extends Controller
{
    public function dashboard()
    {
        $pedidos = Pedido::with('itens')
            ->latest()
            ->take(10)
            ->get()
            ->map(function (Pedido $pedido) {
                return [
                    'id' => $pedido->id,
                    'cliente_nome' => $pedido->cliente_nome,
                    'cliente_telefone' => $pedido->cliente_telefone,
                    'tipo_entrega' => $pedido->tipo_entrega,
                    'data_entrega' => $pedido->data_entrega,
                    'hora_entrega' => $pedido->hora_entrega,
                    'status' => $pedido->status,
                    'total_itens' => $pedido->itens->count(),
                ];
            });

        return Inertia::render('Dashboard', [
            'stats' => [
                [
                    'title' => 'Pedidos Salvos',
                    'value' => Pedido::count(),
                    'hexColor' => '#8D021F',
                ],
                [
                    'title' => 'Clientes',
                    'value' => Cliente::count(),
                    'hexColor' => '#C2410C',
                ],
                [
                    'title' => 'Produtos',
                    'value' => Produto::count(),
                    'hexColor' => '#1D4ED8',
                ],
                [
                    'title' => 'Pendentes',
                    'value' => Pedido::where('status', 'pendente')->count(),
                    'hexColor' => '#15803D',
                ],
            ],
            'pedidos' => $pedidos,
        ]);
    }

    public function create()
    {
        return Inertia::render('Pedidos/Create', [
            'clientesCadastrados' => Cliente::orderBy('nome')->get(),
            'produtosCadastrados' => Produto::orderBy('nome')->get(),
            'pedido' => null,
            'imprimir' => false,
        ]);
    }

    public function edit(Request $request, Pedido $pedido)
    {
        $pedido->load('itens');

        return Inertia::render('Pedidos/Create', [
            'clientesCadastrados' => Cliente::orderBy('nome')->get(),
            'produtosCadastrados' => Produto::orderBy('nome')->get(),
            'pedido' => [
                'id' => $pedido->id,
                'cliente_id' => $pedido->cliente_id,
                'cliente_nome' => $pedido->cliente_nome,
                'cliente_telefone' => $pedido->cliente_telefone,
                'tipo_entrega' => $pedido->tipo_entrega,
                'endereco_entrega' => $pedido->endereco_entrega,
                'data_entrega' => $pedido->data_entrega,
                'hora_entrega' => $pedido->hora_entrega,
                'status' => $pedido->status,
                'itens' => $pedido->itens->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'produto' => $item->produto_id,
                        'produtoNome' => $item->produto_nome,
                        'quantidade' => (string) $item->quantidade,
                        'obs' => $item->observacoes,
                    ];
                })->values(),
            ],
            'imprimir' => $request->boolean('imprimir'),
        ]);
    }

    public function print(Pedido $pedido)
    {
        $pedido->load('itens');

        return Inertia::render('Pedidos/Print', [
            'pedido' => [
                'id' => $pedido->id,
                'cliente_nome' => $pedido->cliente_nome,
                'cliente_telefone' => $pedido->cliente_telefone,
                'tipo_entrega' => $pedido->tipo_entrega,
                'endereco_entrega' => $pedido->endereco_entrega,
                'criado_em_data' => $pedido->created_at?->format('Y-m-d'),
                'criado_em_hora' => $pedido->created_at?->format('H:i'),
                'data_entrega' => $pedido->data_entrega,
                'hora_entrega' => $pedido->hora_entrega,
                'itens' => $pedido->itens->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'produto_nome' => $item->produto_nome,
                        'quantidade' => (string) $item->quantidade,
                        'observacoes' => $item->observacoes,
                    ];
                })->values(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'cliente_nome' => 'required|string|max:255',
            'cliente_telefone' => 'nullable|string|max:20',
            'tipo_entrega' => 'required|in:entrega,retirada',
            'endereco_entrega' => 'nullable|string|max:255',
            'data_entrega' => 'required|date',
            'hora_entrega' => 'required|date_format:H:i',
            'itens' => 'required|array|min:1',
            'itens.*.produto_id' => 'nullable|exists:produtos,id',
            'itens.*.produto_nome' => 'required|string|max:255',
            'itens.*.quantidade' => 'required|numeric|min:0.01',
            'itens.*.observacoes' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            $pedido = Pedido::create([
                'cliente_id' => $validated['cliente_id'] ?? null,
                'cliente_nome' => $validated['cliente_nome'],
                'cliente_telefone' => $validated['cliente_telefone'] ?? null,
                'tipo_entrega' => $validated['tipo_entrega'],
                'endereco_entrega' => $validated['endereco_entrega'] ?? null,
                'data_entrega' => $validated['data_entrega'],
                'hora_entrega' => $validated['hora_entrega'],
                'status' => 'pendente',
            ]);

            foreach ($validated['itens'] as $item) {
                $pedido->itens()->create([
                    'produto_id' => $item['produto_id'] ?? null,
                    'produto_nome' => $item['produto_nome'],
                    'quantidade' => $item['quantidade'],
                    'observacoes' => $item['observacoes'] ?? null,
                ]);
            }
        });

        return redirect()->route('dashboard');
    }

    public function update(Request $request, Pedido $pedido)
    {
        $validated = $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'cliente_nome' => 'required|string|max:255',
            'cliente_telefone' => 'nullable|string|max:20',
            'tipo_entrega' => 'required|in:entrega,retirada',
            'endereco_entrega' => 'nullable|string|max:255',
            'data_entrega' => 'required|date',
            'hora_entrega' => 'required|date_format:H:i',
            'itens' => 'required|array|min:1',
            'itens.*.produto_id' => 'nullable|exists:produtos,id',
            'itens.*.produto_nome' => 'required|string|max:255',
            'itens.*.quantidade' => 'required|numeric|min:0.01',
            'itens.*.observacoes' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $pedido) {
            $pedido->update([
                'cliente_id' => $validated['cliente_id'] ?? null,
                'cliente_nome' => $validated['cliente_nome'],
                'cliente_telefone' => $validated['cliente_telefone'] ?? null,
                'tipo_entrega' => $validated['tipo_entrega'],
                'endereco_entrega' => $validated['endereco_entrega'] ?? null,
                'data_entrega' => $validated['data_entrega'],
                'hora_entrega' => $validated['hora_entrega'],
            ]);

            $pedido->itens()->delete();

            foreach ($validated['itens'] as $item) {
                $pedido->itens()->create([
                    'produto_id' => $item['produto_id'] ?? null,
                    'produto_nome' => $item['produto_nome'],
                    'quantidade' => $item['quantidade'],
                    'observacoes' => $item['observacoes'] ?? null,
                ]);
            }
        });

        return redirect()->route('dashboard');
    }

    public function destroy(Pedido $pedido)
    {
        $pedido->delete();

        return redirect()->route('dashboard');
    }
}
