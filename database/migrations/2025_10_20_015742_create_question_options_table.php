<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->text('text');
            $table->boolean('is_correct')->default(false);
            
            // Chave estrangeira para question
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            
            $table->timestamps();
            
            // Índices
            $table->index(['question_id', 'is_correct']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('question_options');
    }
};