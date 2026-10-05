<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('alumno_id')
                ->constrained('alumnos')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('tarea_id')
                ->nullable()
                ->constrained('tareas')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->enum('tipo', [
                'nueva_tarea',
                'tarea_cancelada',
            ]);

            $table->string('titulo', 200);
            $table->text('mensaje');

            $table->boolean('leida')->default(false);
            $table->dateTime('fecha_lectura')->nullable();

            $table->timestamps();

            $table->index([
                'alumno_id',
                'leida',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
