<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_answers', function (Blueprint $table) {
            $table->id();
            $table->text('answer')->nullable()->comment('Resposta do estudante');
            $table->text('justification')->nullable()->comment('Justificação do estudante');
            $table->decimal('score', 5, 2)->default(0)->comment('Pontuação obtida');
            $table->text('feedback')->nullable()->comment('Feedback do professor');
            $table->boolean('concluido')->default(false);
            $table->timestamp('submitted_at')->nullable();
            
            // Chaves estrangeiras
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            
            $table->timestamps();
            
            // Índice único para evitar respostas duplicadas
            $table->unique(['question_id', 'student_id']);
            
            // Índices
            $table->index(['student_id', 'concluido']);
            $table->index('submitted_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_answers');
    }
};