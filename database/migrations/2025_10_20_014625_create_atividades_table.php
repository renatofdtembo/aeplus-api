<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\TipoAtividade;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atividades', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('conteudo')->nullable();
            $table->json('configuracoes')->nullable();
            $table->enum('tipo', array_column(TipoAtividade::cases(), 'value'))->default(TipoAtividade::TEXTO->value);

            $table->boolean('status')->default(true);
            $table->boolean('change_aba')->default(false);
            $table->boolean('required_camera')->default(false);

            $table->integer('posicao')->nullable();
            $table->double('peso')->default(0.0);

            $table->foreignId('id_modulo')
                ->constrained('modulos')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atividades');
    }
};
