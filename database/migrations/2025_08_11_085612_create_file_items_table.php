<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('file_items', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->enum('type', ['file', 'folder']);
            $table->string('path');
            $table->unsignedBigInteger('size')->nullable();
            $table->string('extension')->nullable();
            $table->dateTime('modifiedAt');
            $table->dateTime('createdAt');
            $table->json('permissions')->nullable();
            $table->string('parent_id')->nullable();
            $table->string('entity_id')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('file_items')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('file_items');
    }
};