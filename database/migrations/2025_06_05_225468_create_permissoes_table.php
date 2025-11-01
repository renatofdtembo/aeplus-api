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
        Schema::create('permissoes', function (Blueprint $table) {
            $table->id();
            $table->boolean('canView')->default(false);
            $table->boolean('canCreate')->default(false);
            $table->boolean('canUpdate')->default(false);
            $table->boolean('canDelete')->default(false);

            // FK menu_id
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');

            // FK designacao_id
            $table->foreignId('funcao_id')->constrained('funcaos')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissoes');
    }
};
