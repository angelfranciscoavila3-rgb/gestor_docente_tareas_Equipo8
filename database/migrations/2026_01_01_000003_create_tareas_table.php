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

            $table->foreignId('docente_id')
                ->constrained('docentes')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('titulo', 200);
            $table->text('descripcion');

            $table->enum('tipo', [
                'tarea_casa',
                'actividad_clase',
                'exposicion',
            ]);

            $table->enum('estado', [
                'activa',
                'cerrada',
                'cancelada',
            ])->default('activa');

            $table->dateTime('fecha_asignacion');
            $table->dateTime('fecha_entrega');

            $table->dateTime('fecha_cierre')->nullable();
            $table->dateTime('fecha_cancelacion')->nullable();

            $table->text('motivo_cancelacion')->nullable();

            $table->timestamps();

            $table->index([
                'docente_id',
                'estado',
            ]);

            $table->index('fecha_entrega');
            $table->index('fecha_asignacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tareas');
    }
};
