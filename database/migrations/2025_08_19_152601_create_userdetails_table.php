<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();

            // 🔗 Relacionamento com a tabela users (1:1)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Identificação básica
            $table->string('nome');
            $table->string('nome_fantasia')->nullable(); // nome comercial se for empresa
            $table->string('urlImg')->nullable();
            $table->string('contacto')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('biografia')->nullable();

            // Documentos legais
            $table->string('nif')->nullable()->unique(); // Pessoa ou empresa
            $table->string('bi')->nullable()->unique();  // Somente pessoa física
            $table->string('passaporte')->nullable()->unique(); // Pessoa física estrangeira

            // Dados de pessoa física
            $table->enum('genero', ['M', 'F', 'OUTRO'])->nullable();
            $table->enum('estadocivil', ['SOLTEIRO', 'CASADO', 'DIVORCIADO', 'VIUVO', 'UNIAO_DE_FACTO'])->nullable();
            $table->date('nascimento')->nullable();

            // Tipo de entidade
            $table->enum('tipo', ['PESSOA', 'EMPRESA'])->default('PESSOA');

            // Relação com endereço
            $table->foreignId('endereco_id')->nullable()->constrained('enderecos')->onDelete('set null');

            $table->timestamps();

            // Índices úteis
            $table->index(['nif', 'bi', 'tipo']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_details');
    }
};
