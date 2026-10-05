@extends('layouts.app')

@section('title', 'Detalle de Tarea - Alumno')
@section('page_title', 'Detalle de Tarea & Entrega de Evidencia')

@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <span class="badge badge-active">{{ $tarea->tipo_nombre }}</span>
            <h2 class="card-title" style="margin-top: 6px;">{{ $tarea->titulo }}</h2>
        </div>
        <a href="{{ route('tareas.index') }}" class="btn btn-outline btn-sm">Volver a Mis Tareas</a>
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
            <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Estado de Entrega</span>
            <p style="margin-top: 2px;">
                @if($evidencia)
                    <span class="badge badge-success">Entregada ({{ $evidencia->fecha_subida->format('d/m/Y H:i') }})</span>
                @else
                    @if($tarea->es_vencida)
                        <span class="badge badge-danger">Vencida (Plazo cerrado)</span>
                    @else
                        <span class="badge badge-pending">Pendiente de entrega</span>
                    @endif
                @endif
            </p>
        </div>
    </div>

    <div style="margin-bottom: 24px;">
        <h3 style="font-size: 13px; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 8px;">Instrucciones / Descripción</h3>
        <div style="background: var(--bg-main); padding: 16px; border-radius: var(--border-radius-md); border-left: 4px solid var(--ocean-blue);">
            {!! nl2br(e($tarea->descripcion)) !!}
        </div>
    </div>
</div>

<!-- Sección de Calificación Publicada (si existe) -->
@if($calificacion)
    <div class="card" style="border-color: var(--ocean-blue);">
        <div class="card-header">
            <h3 class="card-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                Calificación del Docente
            </h3>
            <span class="badge badge-success">Publicada</span>
        </div>
        <div style="display: flex; align-items: center; gap: 24px;">
            <div style="background: var(--bg-main); padding: 18px 24px; border-radius: var(--border-radius-lg); text-align: center;">
                <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Nota Final</span>
                <div style="font-size: 32px; font-weight: 800; color: var(--ocean-blue); line-height: 1.1; margin-top: 4px;">
                    {{ number_format($calificacion->calificacion, 1) }} <span style="font-size: 16px; color: var(--text-secondary);">/ 10</span>
                </div>
            </div>
            <div>
                <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Observaciones / Retroalimentación:</span>
                <p style="font-size: 14px; margin-top: 4px; color: var(--text-primary);">
                    {{ $calificacion->comentario ?? 'Sin comentarios adicionales.' }}
                </p>
            </div>
        </div>
    </div>
@endif

<!-- Sección de Subida / Evidencia -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            Evidencia de la Tarea (Únicamente Formato PDF - Máximo 2MB)
        </h3>
    </div>

    @if($evidencia)
        <div style="background: var(--bg-main); padding: 18px; border-radius: var(--border-radius-md); margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 42px; height: 42px; background: var(--badge-danger-bg); color: var(--badge-danger-text); border-radius: var(--border-radius-md); display: flex; align-items: center; justify-content: center; font-weight: 700;">
                    PDF
                </div>
                <div>
                    <strong>{{ $evidencia->nombre_original }}</strong>
                    <div style="font-size: 12px; color: var(--text-muted);">
                        Subido el {{ $evidencia->fecha_subida->format('d/m/Y H:i') }} • Tamaño: {{ $evidencia->tamano_formateado }}
                    </div>
                </div>
            </div>
            <a href="{{ route('evidencias.download', $evidencia->id) }}" class="btn btn-outline btn-sm">
                Descargar Evidencia
            </a>
        </div>
    @endif

    @if(!$tarea->es_vencida && $tarea->estado !== 'cancelada')
        <form action="{{ route('evidencias.store', $tarea->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group">
                <label for="archivo_evidencia" class="form-label">
                    {{ $evidencia ? 'Reemplazar Archivo PDF de Evidencia' : 'Seleccionar Archivo PDF' }}
                </label>
                <input type="file" id="archivo_evidencia" name="archivo_evidencia" class="form-control" accept="application/pdf" required>
                <div id="file-error-msg" class="form-error" style="display: none; font-weight: 700; margin-top: 6px;"></div>
                <small style="color: var(--text-muted); font-size: 11px; margin-top: 6px; display: block;">
                    * Reglas: Solo se permite 1 archivo por tarea, únicamente en formato <strong>PDF</strong> y con un tamaño no mayor a <strong>2MB</strong>.
                </small>
                @error('archivo_evidencia') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                {{ $evidencia ? 'Actualizar Evidencia PDF' : 'Subir Evidencia PDF' }}
            </button>
        </form>
    @else
        @if(!$evidencia)
            <div class="alert alert-danger" style="margin-top: 10px;">
                El tiempo límite para subir evidencias de esta tarea ha caducado.
            </div>
        @endif
    @endif
</div>
@endsection
