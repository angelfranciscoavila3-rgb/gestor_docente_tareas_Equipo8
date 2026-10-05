@extends('layouts.app')

@section('title', 'Notificaciones - Alumno')
@section('page_title', 'Mis Notificaciones')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Centro de Notificaciones</h2>
        <form action="{{ route('notificaciones.marcarTodas') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline btn-sm">Marcar todas como leídas</button>
        </form>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Estado</th>
                    <th>Tipo</th>
                    <th>Título</th>
                    <th>Mensaje</th>
                    <th>Fecha</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notificaciones as $notif)
                    <tr style="{{ !$notif->leida ? 'font-weight: 600; background-color: rgba(37, 137, 189, 0.05);' : '' }}">
                        <td>
                            @if(!$notif->leida)
                                <span class="badge badge-active">Nueva</span>
                            @else
                                <span class="badge badge-draft" style="background: transparent; border: 1px solid var(--border-color); color: var(--text-muted);">Leída</span>
                            @endif
                        </td>
                        <td>
                            @if($notif->tipo === 'nueva_tarea')
                                <span class="badge badge-success">Nueva Tarea</span>
                            @else
                                <span class="badge badge-danger">Cancelación</span>
                            @endif
                        </td>
                        <td>{{ $notif->titulo }}</td>
                        <td>{{ $notif->mensaje }}</td>
                        <td>{{ $notif->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div style="display: flex; gap: 6px;">
                                @if($notif->tarea_id)
                                    <a href="{{ route('tareas.show', $notif->tarea_id) }}" class="btn btn-primary btn-sm" style="padding: 4px 8px; font-size: 11px;">Ver Tarea</a>
                                @endif
                                @if(!$notif->leida)
                                    <form action="{{ route('notificaciones.marcar', $notif->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline btn-sm" style="padding: 4px 8px; font-size: 11px;">Marcar leída</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            No tienes notificaciones recibidas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 16px;">
        {{ $notificaciones->links() }}
    </div>
</div>
@endsection
