<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->text('text');
            $table->text('justification')->nullable();
            $table->enum('type', ['multiple_choice', 'true_false', 'open_ended', 'fill_blank']);
            $table->text('correct_answer')->nullable()->comment('Resposta correta para tipos abertos');
            $table->boolean('requires_justification')->default(false);
            $table->integer('points')->default(1);
            $table->integer('order')->default(0);
            
            // Chave estrangeira para quiz
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            
            $table->timestamps();
            
            // Índices
            $table->index(['quiz_id', 'order']);
            $table->index('type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('questions');
    }
};