<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('operation_logs', function (Blueprint $table) {
            $table->id();
            $table->string('object_type');  // Ex: 'User', 'Product'
            $table->string('object_id');    // ID do objeto afetado
            $table->string('operation');    // 'create', 'update', 'delete'
            $table->text('description');    // Detalhes da operação
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->json('old_values')->nullable();  // Valores antes da alteração
            $table->json('new_values')->nullable();  // Valores após alteração
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('operation_logs');
    }
};