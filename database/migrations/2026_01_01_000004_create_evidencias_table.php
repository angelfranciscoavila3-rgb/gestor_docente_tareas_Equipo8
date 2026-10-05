<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evidencias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tarea_id')
                ->constrained('tareas')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('alumno_id')
                ->constrained('alumnos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('nombre_original', 255);
            $table->string('ruta', 500);
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('tamano_bytes');
            $table->dateTime('fecha_subida');

            $table->timestamps();

            $table->unique([
                'tarea_id',
                'alumno_id',
            ]);

            $table->index('alumno_id');
            $table->index('tarea_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidencias');
    }
};
