@extends('layouts.app')

@section('title', 'Evaluar Tarea - Docente')
@section('page_title', 'Evaluación de Tarea Académica')

@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <span class="badge badge-active">{{ $tarea->tipo_nombre }}</span>
            <h2 class="card-title" style="margin-top: 6px;">Evaluación: {{ $tarea->titulo }}</h2>
            <span style="font-size: 12px; color: var(--text-secondary);">
                Fecha límite vencida el: {{ $tarea->fecha_entrega->format('d/m/Y 23:59') }}
            </span>
        </div>
        <a href="{{ route('tareas.show', $tarea->id) }}" class="btn btn-outline btn-sm">Volver a Detalles</a>
    </div>

    <div class="alert alert-warning" style="margin-bottom: 20px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>
            <strong>Instrucciones de Evaluación:</strong> Ingresa una calificación en la escala de <strong>0 a 10</strong>. 
            Utiliza el botón <strong>"Guardar Borrador"</strong> para reservar las notas de forma privada, o el botón <strong>"Publicar Calificaciones"</strong> para hacerlas visibles a los alumnos inmediatamente.
        </span>
    </div>

    <form action="{{ route('calificaciones.guardar', $tarea->id) }}" method="POST">
        @csrf

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Matrícula</th>
                        <th>Alumno</th>
                        <th>Estado de Entrega</th>
                        <th>Evidencia PDF</th>
                        <th style="width: 130px;">Calificación (0-10)</th>
                        <th>Observaciones / Comentario</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alumnos as $alumno)
                        @php
                            $evidencia = $evidenciasMap->get($alumno->id);
                            $calificacion = $calificacionesMap->get($alumno->id);
                            $valActual = $calificacion ? $calificacion->calificacion : '';
                            $comActual = $calificacion ? $calificacion->comentario : '';
                        @endphp
                        <tr>
                            <td><code>{{ $alumno->matricula }}</code></td>
                            <td><strong>{{ $alumno->nombre_completo }}</strong></td>
                            <td>
                                @if($evidencia)
                                    <span class="badge badge-success">Entregado</span>
                                @else
                                    <span class="badge badge-danger">No entregó</span>
                                @endif
                            </td>
                            <td>
                                @if($evidencia)
                                    <a href="{{ route('evidencias.download', $evidencia->id) }}" class="btn btn-outline btn-sm" style="padding: 4px 8px; font-size: 11px;" target="_blank">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                        Ver PDF
                                    </a>
                                @else
                                    <span style="color: var(--text-muted); font-size: 12px;">Sin archivo</span>
                                @endif
                            </td>
                            <td>
                                <input type="number" step="0.1" min="0" max="10" name="calificaciones[{{ $alumno->id }}]" value="{{ old("calificaciones.{$alumno->id}", $valActual) }}" class="form-control" placeholder="0 - 10" style="padding: 6px 10px; text-align: center; font-weight: 700;">
                            </td>
                            <td>
                                <input type="text" name="comentarios[{{ $alumno->id }}]" value="{{ old("comentarios.{$alumno->id}", $comActual) }}" class="form-control" placeholder="Retroalimentación opcional..." style="padding: 6px 10px;">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
            <button type="submit" name="accion" value="borrador" class="btn btn-outline">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Guardar Borrador
            </button>
            <button type="submit" name="accion" value="publicar" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Publicar Calificaciones
            </button>
        </div>
    </form>
</div>
@endsection
