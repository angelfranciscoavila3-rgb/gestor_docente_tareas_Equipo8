@extends('layouts.app')

@section('title', 'Detalle de Tarea - Docente')
@section('page_title', 'Detalle de Tarea & Seguimiento de Evidencias')

@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <span class="badge badge-active">{{ $tarea->tipo_nombre }}</span>
            <h2 class="card-title" style="margin-top: 6px;">{{ $tarea->titulo }}</h2>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('tareas.index') }}" class="btn btn-outline btn-sm">Volver al Listado</a>
            @if($tarea->estado !== 'cancelada')
                @if($tarea->es_vencida)
                    <a href="{{ route('calificaciones.evaluar', $tarea->id) }}" class="btn btn-secondary btn-sm">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                        Evaluar Tarea (0-10)
                    </a>
                @endif
                <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('modal-cancelar').style.display='flex'">
                    Cancelar Tarea
                </button>
            @endif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; background: var(--bg-main); padding: 16px; border-radius: var(--border-radius-md); margin-bottom: 20px;">
        <div>
            <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Fecha Asignación</span>
            <p style="font-weight: 600; margin-top: 2px;">{{ $tarea->fecha_asignacion->format('d/m/Y H:i') }}</p>
        </div>
        <div>
            <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Fecha Límite Entrega</span>
            <p style="font-weight: 600; margin-top: 2px;">{{ $tarea->fecha_entrega->format('d/m/Y 23:59') }}</p>
        </div>
        <div>
            <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Estado de la Tarea</span>
            <p style="margin-top: 2px;">
                @if($tarea->estado === 'cancelada')
                    <span class="badge badge-danger">Cancelada</span>
                @elseif($tarea->es_vencida)
                    <span class="badge badge-pending">Vencida (Período de entrega cerrado)</span>
                @else
                    <span class="badge badge-success">Activa (En Recepción)</span>
                @endif
            </p>
        </div>
    </div>

    <div style="margin-bottom: 24px;">
        <h3 style="font-size: 13px; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 8px;">Instrucciones / Descripción</h3>
        <div style="background: var(--bg-main); padding: 14px 18px; border-radius: var(--border-radius-md); border-left: 4px solid var(--ocean-blue);">
            {!! nl2br(e($tarea->descripcion)) !!}
        </div>
    </div>

    @if($tarea->estado === 'cancelada')
        <div class="alert alert-warning" style="margin-bottom: 24px;">
            <strong>Tarea Cancelada:</strong> {{ $tarea->motivo_cancelacion }}
        </div>
    @endif
</div>

<!-- Modal para Cancelar Tarea -->
<div id="modal-cancelar" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--border-radius-lg); padding: 24px; width: 100%; max-width: 480px; box-shadow: var(--shadow-lg);">
        <h3 style="font-size: 16px; margin-bottom: 12px; color: var(--text-primary);">Cancelar Tarea</h3>
        <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 16px;">
            Al cancelar la tarea, no se recibirán más evidencias y se enviará una notificación a todos los alumnos.
        </p>

        <form action="{{ route('tareas.cancelar', $tarea->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="motivo_cancelacion" class="form-label">Motivo de la Cancelación</label>
                <textarea id="motivo_cancelacion" name="motivo_cancelacion" class="form-control" rows="3" placeholder="Indica la razón para cancelar esta tarea..." required></textarea>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('modal-cancelar').style.display='none'">Cerrar</button>
                <button type="submit" class="btn btn-danger btn-sm">Confirmar Cancelación</button>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Seguimiento por Alumno -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de Alumnos y Estado de Entrega</h3>
        <span style="font-size: 12px; color: var(--text-secondary);">
            Entregados: <strong>{{ $evidenciasMap->count() }}</strong> / {{ $alumnos->count() }}
        </span>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Matrícula</th>
                    <th>Alumno</th>
                    <th>Estado Entrega</th>
                    <th>Fecha de Subida</th>
                    <th>Archivo PDF</th>
                    <th>Calificación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alumnos as $alumno)
                    @php
                        $evidencia = $evidenciasMap->get($alumno->id);
                        $calificacion = $calificacionesMap->get($alumno->id);
                    @endphp
                    <tr>
                        <td><code>{{ $alumno->matricula }}</code></td>
                        <td><strong>{{ $alumno->nombre_completo }}</strong></td>
                        <td>
                            @if($evidencia)
                                <span class="badge badge-success">Entregado</span>
                            @else
                                @if($tarea->es_vencida)
                                    <span class="badge badge-danger">No entregó</span>
                                @else
                                    <span class="badge badge-pending">Pendiente</span>
                                @endif
                            @endif
                        </td>
                        <td>
                            {{ $evidencia ? $evidencia->fecha_subida->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td>
                            @if($evidencia)
                                <a href="{{ route('evidencias.download', $evidencia->id) }}" class="btn btn-outline btn-sm" style="padding: 4px 8px; font-size: 11px;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    PDF ({{ $evidencia->tamano_formateado }})
                                </a>
                            @else
                                <span style="color: var(--text-muted); font-size: 12px;">Sin archivo</span>
                            @endif
                        </td>
                        <td>
                            @if($calificacion)
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <strong style="font-size: 14px; color: var(--ocean-blue);">{{ number_format($calificacion->calificacion, 1) }} / 10</strong>
                                    @if($calificacion->estado === 'borrador')
                                        <span class="badge badge-draft">Borrador</span>
                                    @else
                                        <span class="badge badge-success">Publicada</span>
                                    @endif
                                </div>
                            @else
                                <span style="color: var(--text-muted); font-size: 12px;">Sin evaluar</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
