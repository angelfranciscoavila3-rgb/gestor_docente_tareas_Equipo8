@extends('layouts.app')

@section('title', 'Captura Individual de Alumno - Docente')
@section('page_title', 'Registrar Nuevo Alumno (Captura Individual)')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h2 class="card-title">Datos del Alumno</h2>
        <a href="{{ route('alumnos.index') }}" class="btn btn-outline btn-sm">Volver</a>
    </div>

    <form action="{{ route('alumnos.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="matricula" class="form-label">Matrícula</label>
            <input type="text" id="matricula" name="matricula" class="form-control" value="{{ old('matricula') }}" placeholder="ej. ALU016" required>
            @error('matricula') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="nombre" class="form-label">Nombre(s)</label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="{{ old('nombre') }}" placeholder="ej. Juan" required>
            @error('nombre') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
            <input type="text" id="apellido_paterno" name="apellido_paterno" class="form-control" value="{{ old('apellido_paterno') }}" placeholder="ej. Pérez" required>
            @error('apellido_paterno') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="apellido_materno" class="form-label">Apellido Materno (Opcional)</label>
            <input type="text" id="apellido_materno" name="apellido_materno" class="form-control" value="{{ old('apellido_materno') }}" placeholder="ej. Gómez">
            @error('apellido_materno') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="ej. juan.perez@example.com" required>
            @error('email') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Contraseña de Acceso</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            <small style="color: var(--text-muted); font-size: 11px;">Mínimo 6 caracteres.</small>
            @error('password') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
            <a href="{{ route('alumnos.index') }}" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary">Registrar Alumno</button>
        </div>
    </form>
</div>
@endsection
