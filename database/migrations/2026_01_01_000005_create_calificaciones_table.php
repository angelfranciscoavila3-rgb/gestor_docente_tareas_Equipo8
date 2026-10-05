<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tarea_id')
                ->constrained('tareas')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('alumno_id')
                ->constrained('alumnos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->decimal('calificacion', 3, 1);
            $table->text('comentario')->nullable();

            $table->enum('estado', [
                'borrador',
                'publicada',
            ])->default('borrador');

            $table->dateTime('fecha_evaluacion')->nullable();
            $table->dateTime('fecha_publicacion')->nullable();

            $table->timestamps();

            $table->unique([
                'tarea_id',
                'alumno_id',
            ]);

            $table->index('alumno_id');
            $table->index('tarea_id');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calificaciones');
    }
};
