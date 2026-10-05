@extends('layouts.app')

@section('title', 'Carga Masiva de Alumnos - Docente')
@section('page_title', 'Carga Masiva de Alumnos')

@section('content')
<div class="card" style="max-width: 750px; margin: 0 auto;">
    <div class="card-header">
        <h2 class="card-title">Importación Masiva de Registros</h2>
        <a href="{{ route('alumnos.index') }}" class="btn btn-outline btn-sm">Volver</a>
    </div>

    <div class="alert alert-warning" style="margin-bottom: 24px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>
            <strong>Formato requerido:</strong> Cada línea debe contener los valores separados por coma (,):<br>
            <code>MATRICULA, NOMBRE, APELLIDO_PATERNO, APELLIDO_MATERNO, CORREO, CONTRASEÑA</code><br>
            Ejemplo:<br>
            <code>ALU016, Carlos, Mendoza, Ruiz, carlos.mendoza@example.com, password123</code>
        </span>
    </div>

    <form action="{{ route('alumnos.bulk.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="archivo_csv" class="form-label">Opción A: Subir Archivo CSV o TXT</label>
            <input type="file" id="archivo_csv" name="archivo_csv" class="form-control" accept=".csv,.txt">
            @error('archivo_csv') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div style="text-align: center; font-weight: 700; color: var(--text-muted); margin: 16px 0;">— Ó —</div>

        <div class="form-group">
            <label for="texto_masivo" class="form-label">Opción B: Pegar Registros de Texto Directamente</label>
            <textarea id="texto_masivo" name="texto_masivo" class="form-control" rows="8" placeholder="ALU016, Carlos, Mendoza, Ruiz, carlos.mendoza@example.com, password123&#10;ALU017, Maria, Torres, Vega, maria.torres@example.com, password123"></textarea>
            @error('texto_masivo') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
            <a href="{{ route('alumnos.index') }}" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Procesar Carga Masiva
            </button>
        </div>
    </form>
</div>
@endsection
