<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Produto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PedidoController extends Controller
{
    public function dashboard()
    {
        $hoje = Carbon::today();
        $agora = Carbon::now();

        $pedidos = Pedido::with('itens')
            ->latest()
            ->take(30)
            ->get()
            ->map(function (Pedido $pedido) use ($agora) {
                $dataHoraEntrega = Carbon::parse("{$pedido->data_entrega} {$pedido->hora_entrega}");
                $itens = $pedido->itens->map(function ($item) {
                    return [
                        'produto_nome' => $item->produto_nome,
                        'quantidade' => (string) $item->quantidade,
                        'observacoes' => $item->observacoes,
                    ];
                })->values();

                return [
                    'id' => $pedido->id,
                    'cliente_nome' => $pedido->cliente_nome,
                    'cliente_telefone' => $pedido->cliente_telefone,
                    'tipo_entrega' => $pedido->tipo_entrega,
                    'endereco_entrega' => $pedido->endereco_entrega,
                    'data_entrega' => $pedido->data_entrega,
                    'hora_entrega' => $pedido->hora_entrega,
                    'status' => $pedido->status,
                    'total_itens' => $pedido->itens->count(),
                    'itens' => $itens,
                    'resumo_itens' => $itens->take(2)->pluck('produto_nome')->implode(', '),
                    'is_atrasado' => $pedido->status === 'pendente' && $dataHoraEntrega->lt($agora),
                    'periodo_label' => $dataHoraEntrega->locale('pt_BR')->diffForHumans($agora, [
                        'parts' => 2,
                        'short' => true,
                        'syntax' => Carbon::DIFF_RELATIVE_TO_NOW,
                    ]),
                    'created_at' => $pedido->created_at?->toDateTimeString(),
                ];
            });

        $proximosPedidos = Pedido::with('itens')
            ->where('status', 'pendente')
            ->orderBy('data_entrega')
            ->orderBy('hora_entrega')
            ->take(5)
            ->get()
            ->map(function (Pedido $pedido) use ($agora) {
                $dataHoraEntrega = Carbon::parse("{$pedido->data_entrega} {$pedido->hora_entrega}");

                return [
                    'id' => $pedido->id,
                    'cliente_nome' => $pedido->cliente_nome,
                    'tipo_entrega' => $pedido->tipo_entrega,
                    'data_entrega' => $pedido->data_entrega,
                    'hora_entrega' => $pedido->hora_entrega,
                    'total_itens' => $pedido->itens->count(),
                    'is_atrasado' => $dataHoraEntrega->lt($agora),
                    'periodo_label' => $dataHoraEntrega->locale('pt_BR')->diffForHumans($agora, [
                        'parts' => 2,
                        'short' => true,
                        'syntax' => Carbon::DIFF_RELATIVE_TO_NOW,
                    ]),
                ];
            });

        return Inertia::render('Dashboard', [

            'highlights' => [
                'pedidos_hoje' => Pedido::whereDate('data_entrega', $hoje)->count(),
                'atrasados' => Pedido::where('status', 'pendente')
                    ->where(function ($query) use ($agora) {
                        $query->whereDate('data_entrega', '<', $agora->toDateString())
                            ->orWhere(function ($sameDay) use ($agora) {
                                $sameDay->whereDate('data_entrega', $agora->toDateString())
                                    ->whereTime('hora_entrega', '<', $agora->format('H:i:s'));
                            });
                    })
                    ->count(),
                'entregas_hoje' => Pedido::whereDate('data_entrega', $hoje)
                    ->where('tipo_entrega', 'entrega')
                    ->count(),
                'retiradas_hoje' => Pedido::whereDate('data_entrega', $hoje)
                    ->where('tipo_entrega', 'retirada')
                    ->count(),
            ],
            'pedidos' => $pedidos,
            'proximosPedidos' => $proximosPedidos,
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

    public function finish(Pedido $pedido)
    {
        if ($pedido->status !== 'concluido') {
            $pedido->update([
                'status' => 'concluido',
            ]);
        }

        return redirect()->route('dashboard');
    }
}
