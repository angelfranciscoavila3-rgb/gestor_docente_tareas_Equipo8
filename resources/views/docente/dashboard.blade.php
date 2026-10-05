@extends('layouts.app')

@section('title', 'Panel de Control - Docente')
@section('page_title', 'Panel de Control Docente')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="stat-details">
            <span class="stat-value">{{ $totalAlumnos }}</span>
            <span class="stat-label">Alumnos Activos</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon teal">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </div>
        <div class="stat-details">
            <span class="stat-value">{{ $tareasActivas }}</span>
            <span class="stat-label">Tareas Activas</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon amber">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="stat-details">
            <span class="stat-value">{{ $tareasVencidas }}</span>
            <span class="stat-label">Tareas Vencidas</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        </div>
        <div class="stat-details">
            <span class="stat-value">{{ $totalEvidencias }}</span>
            <span class="stat-label">Evidencias Recibidas</span>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Tareas Recientes
        </h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('tareas.create') }}" class="btn btn-primary btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nueva Tarea
            </a>
            <a href="{{ route('tareas.index') }}" class="btn btn-outline btn-sm">Ver Todas</a>
        </div>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Título / Categoría</th>
                    <th>Tipo</th>
                    <th>Asignada</th>
                    <th>Límite de Entrega</th>
                    <th>Avance Entregas</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tareasRecientes as $tarea)
                    <tr>
                        <td>
                            <strong>{{ $tarea->titulo }}</strong>
                        </td>
                        <td>
                            <span class="badge badge-active">{{ $tarea->tipo_nombre }}</span>
                        </td>
                        <td>{{ $tarea->fecha_asignacion->format('d/m/Y H:i') }}</td>
                        <td>{{ $tarea->fecha_entrega->format('d/m/Y 23:59') }}</td>
                        <td>
                            <strong>{{ $tarea->evidencias_count }}</strong> / {{ $totalAlumnos }} alumnos
                        </td>
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
                            <a href="{{ route('tareas.show', $tarea->id) }}" class="btn btn-outline btn-sm">Detalles</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            No hay tareas registradas aún. Haz clic en "Nueva Tarea" para crear la primera.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
