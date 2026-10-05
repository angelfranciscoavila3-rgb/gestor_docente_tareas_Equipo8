@extends('layouts.app')

@section('title', 'Mis Tareas - Alumno')
@section('page_title', 'Historial y Tareas Asignadas')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Mis Tareas</h2>
    </div>

    <!-- Filtros -->
    <form action="{{ route('tareas.index') }}" method="GET" style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 180px;">
            <select name="tipo" class="form-control" onchange="this.form.submit()">
                <option value="">-- Todos los Tipos --</option>
                <option value="tarea_casa" {{ request('tipo') === 'tarea_casa' ? 'selected' : '' }}>Tareas en casa</option>
                <option value="actividad_clase" {{ request('tipo') === 'actividad_clase' ? 'selected' : '' }}>Actividades en clase</option>
                <option value="exposicion" {{ request('tipo') === 'exposicion' ? 'selected' : '' }}>Exposiciones</option>
            </select>
        </div>
        @if(request()->has('tipo'))
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
                    <th>Fecha Límite</th>
                    <th>Mi Entrega</th>
                    <th>Calificación</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tareas as $tarea)
                    @php
                        $evidencia = $tarea->evidencias->first();
                        $calificacion = $tarea->calificaciones->first();
                    @endphp
                    <tr>
                        <td><strong>{{ $tarea->titulo }}</strong></td>
                        <td><span class="badge badge-active">{{ $tarea->tipo_nombre }}</span></td>
                        <td>{{ $tarea->fecha_asignacion->format('d/m/Y H:i') }}</td>
                        <td>{{ $tarea->fecha_entrega->format('d/m/Y 23:59') }}</td>
                        <td>
                            @if($evidencia)
                                <span class="badge badge-success">Entregado</span>
                            @else
                                @if($tarea->es_vencida)
                                    <span class="badge badge-danger">Vencida</span>
                                @else
                                    <span class="badge badge-pending">Pendiente</span>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if($calificacion && $calificacion->estado === 'publicada')
                                <strong style="color: var(--ocean-blue); font-size: 14px;">{{ number_format($calificacion->calificacion, 1) }} / 10</strong>
                            @else
                                <span style="color: var(--text-muted); font-size: 12px;">Sin publicar</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('tareas.show', $tarea->id) }}" class="btn btn-outline btn-sm">
                                {{ $evidencia ? 'Ver Detalle' : 'Entregar PDF' }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            No hay tareas registradas para mostrar.
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
