<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('tipo_entrega')->default('retirada')->after('cliente_telefone');
            $table->string('endereco_entrega')->nullable()->after('tipo_entrega');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['tipo_entrega', 'endereco_entrega']);
        });
    }
};
