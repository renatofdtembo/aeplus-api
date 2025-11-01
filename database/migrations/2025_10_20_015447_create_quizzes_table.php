<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->integer('time_limit')->nullable()->comment('Tempo limite em minutos');
            $table->boolean('is_active')->default(true);
            $table->boolean('show_justification')->default(false);
            
            // Chave estrangeira para atividade
            $table->foreignId('atividade_id')->constrained('atividades')->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['is_active', 'atividade_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('quizzes');
    }
};