<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();

            // Capa e Imagem
            $table->string('capa')->nullable();
            $table->string('url_image')->nullable();

            // Dados principais
            $table->string('titulo');
            $table->string('nome_breve');
            $table->text('descricao')->nullable();

            // Financeiro
            $table->decimal('preco', 10, 2)->default(0);
            $table->boolean('gratuito')->default(false);
            $table->boolean('inscricao')->default(true);

            // Datas (usando tipo date para melhor controle)
            $table->date('data_inicio_inscricao')->nullable();
            $table->date('data_fim_inscricao')->nullable();
            $table->date('data_inicio')->nullable();
            $table->date('data_termino')->nullable();

            // Relacionamentos
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->foreignId('responsavel_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('instituicao_id')->nullable()->constrained('users')->nullOnDelete();

            // Enum-like (texto simples)
            $table->enum('duracao', ['UM_MES', 'TRES_MESES', 'SEIS_MESES', 'UM_ANO'])->nullable();
            $table->enum('nivel', ['INICIANTE', 'INTERMEDIARIO', 'AVANCADO', 'CURSO_PROFISSIONAL'])->nullable();
            $table->enum('privacidade', ['PUBLICO', 'PRIVADO'])->default('PUBLICO');

            // Descritivos
            $table->text('oqueaprender')->nullable();
            $table->text('sobre')->nullable();
            $table->string('video_introducao')->nullable();

            // Campos extras
            $table->string('tipo')->nullable();
            $table->string('visibilidade')->nullable();
            $table->json('configuracoes')->nullable();

            // Auditoria
            $table->timestamp('data_criacao')->nullable();
            $table->timestamp('data_atualizacao')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cursos');
    }
};
