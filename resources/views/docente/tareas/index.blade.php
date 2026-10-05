@extends('layouts.app')

@section('title', 'Tareas Asignadas - Docente')
@section('page_title', 'Tareas & Evidencias Asignadas')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Listado de Tareas</h2>
        <a href="{{ route('tareas.create') }}" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Crear Nueva Tarea
        </a>
    </div>

    <!-- Filtros -->
    <form action="{{ route('tareas.index') }}" method="GET" style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 180px;">
            <select name="tipo" class="form-control" onchange="this.form.submit()">
                <option value="">-- Filtrar por Tipo --</option>
                <option value="tarea_casa" {{ request('tipo') === 'tarea_casa' ? 'selected' : '' }}>Tareas en casa</option>
                <option value="actividad_clase" {{ request('tipo') === 'actividad_clase' ? 'selected' : '' }}>Actividades en clase</option>
                <option value="exposicion" {{ request('tipo') === 'exposicion' ? 'selected' : '' }}>Exposiciones</option>
            </select>
        </div>
        <div style="flex: 1; min-width: 180px;">
            <select name="estado" class="form-control" onchange="this.form.submit()">
                <option value="">-- Filtrar por Estado --</option>
                <option value="activa" {{ request('estado') === 'activa' ? 'selected' : '' }}>Activa</option>
                <option value="vencida" {{ request('estado') === 'vencida' ? 'selected' : '' }}>Vencida</option>
                <option value="cancelada" {{ request('estado') === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
            </select>
        </div>
        @if(request()->hasAny(['tipo', 'estado']))
            <a href="{{ route('tareas.index') }}" class="btn btn-outline">Limpiar Filtros</a>
        @endif
    </form>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Tipo</th>
                    <th>Asignada</th>
                    <th>Límite de Entrega</th>
                    <th>Evidencias</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tareas as $tarea)
                    <tr>
                        <td><strong>{{ $tarea->titulo }}</strong></td>
                        <td><span class="badge badge-active">{{ $tarea->tipo_nombre }}</span></td>
                        <td>{{ $tarea->fecha_asignacion->format('d/m/Y H:i') }}</td>
                        <td>{{ $tarea->fecha_entrega->format('d/m/Y 23:59') }}</td>
                        <td><strong>{{ $tarea->evidencias_count }}</strong> recibidas</td>
                        <td>
                            @if($tarea->estado === 'cancelada')
                                <span class="badge badge-danger">Cancelada</span>
                            @elseif($tarea->es_vencida)
                                <span class="badge badge-pending">Vencida</span>
                            @else
                                <span class="badge badge-success">Activa</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 6px;">
                                <a href="{{ route('tareas.show', $tarea->id) }}" class="btn btn-outline btn-sm">Ver Entregas</a>
                                @if($tarea->es_vencida && $tarea->estado !== 'cancelada')
                                    <a href="{{ route('calificaciones.evaluar', $tarea->id) }}" class="btn btn-secondary btn-sm">Evaluar</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            No se encontraron tareas con los filtros seleccionados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 16px;">
        {{ $tareas->links() }}
    </div>
</div>
@endsection
