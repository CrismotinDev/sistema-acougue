<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $appends = ['endereco_completo'];

    protected $fillable = [
        'nome',
        'telefone',
        'cep',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
    ];

    public function getEnderecoCompletoAttribute(): string
    {
        $partes = array_filter([
            $this->endereco,
            $this->numero ? 'N ' . $this->numero : null,
            $this->complemento,
            $this->bairro,
            $this->cidade,
            $this->uf,
            $this->cep,
        ]);

        return implode(', ', $partes);
    }
}
