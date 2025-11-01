<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inscricao', function (Blueprint $table) {
            $table->id();
            $table->json('configuracoes')->nullable();
            $table->enum('status', [
                'PENDENTE',
                'ATIVO',
                'CONCLUIDO',
                'CANCELADO',
                'TRANCADO'
            ])->default('PENDENTE');
            $table->double('nota')->default(0);
            
            $table->foreignId('curso_id')
                ->constrained('cursos')
                ->onDelete('cascade');

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscricao');
    }
};