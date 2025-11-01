<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assinar_atividade', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_atividade')->constrained('atividades')->onDelete('cascade');
            $table->foreignId('id_usuario')->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assinar_atividade');
    }
};
