@extends('layouts.app')

@section('title', 'Crear Nueva Tarea - Docente')
@section('page_title', 'Crear Nueva Tarea Académica')

@section('content')
<div class="card" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header">
        <h2 class="card-title">Especificaciones de la Tarea</h2>
        <a href="{{ route('tareas.index') }}" class="btn btn-outline btn-sm">Volver</a>
    </div>

    <form action="{{ route('tareas.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="titulo" class="form-label">Título de la Tarea</label>
            <input type="text" id="titulo" name="titulo" class="form-control" value="{{ old('titulo') }}" placeholder="ej. Investigación sobre Estructura de Datos" required>
            @error('titulo') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="tipo" class="form-label">Tipo de Tarea (Solo 3 opciones permitidas)</label>
            <select id="tipo" name="tipo" class="form-control" required>
                <option value="">-- Selecciona el Tipo --</option>
                <option value="tarea_casa" {{ old('tipo') === 'tarea_casa' ? 'selected' : '' }}>Tareas en casa</option>
                <option value="actividad_clase" {{ old('tipo') === 'actividad_clase' ? 'selected' : '' }}>Actividades en clase</option>
                <option value="exposicion" {{ old('tipo') === 'exposicion' ? 'selected' : '' }}>Exposiciones</option>
            </select>
            @error('tipo') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="fecha_entrega_date" class="form-label">Fecha Límite de Entrega</label>
            <input type="date" id="fecha_entrega_date" name="fecha_entrega_date" class="form-control" value="{{ old('fecha_entrega_date', now()->addDays(3)->format('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
            <small style="color: var(--text-muted); font-size: 11px; margin-top: 4px; display: block;">
                * Nota: El tiempo límite para la entrega vence automáticamente a las 23:59:59 del día asignado.
            </small>
            @error('fecha_entrega_date') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="descripcion" class="form-label">Instrucciones y Requisitos de la Tarea</label>
            <textarea id="descripcion" name="descripcion" class="form-control" rows="5" placeholder="Detalla los requisitos de la entrega, contenido esperado, etc." required>{{ old('descripcion') }}</textarea>
            @error('descripcion') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
            <a href="{{ route('tareas.index') }}" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Asignar & Notificar Tarea
            </button>
        </div>
    </form>
</div>
@endsection
