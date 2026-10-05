@extends('layouts.app')

@section('title', 'Inicio - Alumno')
@section('page_title', 'Tablero Principal de Alumno')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon amber">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="stat-details">
            <span class="stat-value">{{ $pendientes }}</span>
            <span class="stat-label">Tareas Pendientes</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div class="stat-details">
            <span class="stat-value">{{ $entregadas }}</span>
            <span class="stat-label">Evidencias Entregadas</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon blue">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        </div>
        <div class="stat-details">
            <span class="stat-value">{{ $notificacionesCount }}</span>
            <span class="stat-label">Notificaciones Sin Leer</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon teal">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
        <div class="stat-details">
            <span class="stat-value">{{ $promedio ? number_format($promedio, 1) : 'N/A' }}</span>
            <span class="stat-label">Promedio General</span>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Tareas Asignadas Recientemente
        </h2>
        <a href="{{ route('tareas.index') }}" class="btn btn-outline btn-sm">Ver Historial Completo</a>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Título / Categoría</th>
                    <th>Tipo</th>
                    <th>Fecha Límite</th>
                    <th>Estado de Entrega</th>
                    <th>Calificación</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tareasRecientes as $tarea)
                    @php
                        $evidencia = $tarea->evidencias->first();
                        $calificacion = $tarea->calificaciones->first();
                    @endphp
                    <tr>
                        <td><strong>{{ $tarea->titulo }}</strong></td>
                        <td><span class="badge badge-active">{{ $tarea->tipo_nombre }}</span></td>
                        <td>{{ $tarea->fecha_entrega->format('d/m/Y 23:59') }}</td>
                        <td>
                            @if($evidencia)
                                <span class="badge badge-success">Entregado</span>
                            @else
                                @if($tarea->es_vencida)
                                    <span class="badge badge-danger">Vencida (Sin entregar)</span>
                                @else
                                    <span class="badge badge-pending">Pendiente</span>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if($calificacion && $calificacion->estado === 'publicada')
                                <strong style="color: var(--ocean-blue); font-size: 14px;">{{ number_format($calificacion->calificacion, 1) }} / 10</strong>
                            @else
                                <span style="color: var(--text-muted); font-size: 12px;">Pendiente</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('tareas.show', $tarea->id) }}" class="btn btn-primary btn-sm">
                                {{ $evidencia ? 'Ver Detalle' : 'Subir Evidencia' }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            No tienes tareas asignadas actualmente.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
