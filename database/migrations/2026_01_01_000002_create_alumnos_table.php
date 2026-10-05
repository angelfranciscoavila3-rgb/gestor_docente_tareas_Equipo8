<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumnos', function (Blueprint $table) {
            $table->id();

            $table->string('matricula', 50)->unique();

            $table->string('nombre', 100);
            $table->string('apellido_paterno', 100);
            $table->string('apellido_materno', 100)->nullable();

            $table->string('email', 150)->unique();
            $table->string('password');

            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->index('activo');
            $table->index([
                'apellido_paterno',
                'apellido_materno',
                'nombre',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumnos');
    }
};
