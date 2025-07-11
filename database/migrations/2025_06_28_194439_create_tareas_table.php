<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('tareas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->unsignedBigInteger('autor_id');
            $table->unsignedBigInteger('asignado_id')->nullable();
            $table->text('cuerpo');
            $table->date('fecha_expiracion')->nullable();
            $table->json('categorias')->nullable();
            $table->json('comentarios')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('tareas');
    }
};
