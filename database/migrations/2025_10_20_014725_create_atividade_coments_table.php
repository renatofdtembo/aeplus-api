<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\TipoComentario;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atividade_coments', function (Blueprint $table) {
            $table->id();
            $table->longText('message')->nullable();
            $table->enum('tipo', array_column(TipoComentario::cases(), 'value'))
                ->default(TipoComentario::COMENTARIO->value);

            $table->foreignId('id_pai')->nullable()->constrained('atividade_coments')->onDelete('cascade');
            $table->foreignId('id_atividade')->constrained('atividades')->onDelete('cascade');
            $table->foreignId('id_usuario')->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atividade_coments');
    }
};
