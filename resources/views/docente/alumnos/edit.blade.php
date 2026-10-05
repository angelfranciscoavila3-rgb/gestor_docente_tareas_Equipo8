@extends('layouts.app')

@section('title', 'Editar Alumno - Docente')
@section('page_title', 'Editar Perfil del Alumno')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h2 class="card-title">Editar Alumno: {{ $alumno->nombre_completo }}</h2>
        <a href="{{ route('alumnos.index') }}" class="btn btn-outline btn-sm">Volver</a>
    </div>

    <form action="{{ route('alumnos.update', $alumno->id) }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="matricula" class="form-label">Matrícula</label>
            <input type="text" id="matricula" name="matricula" class="form-control" value="{{ old('matricula', $alumno->matricula) }}" required>
            @error('matricula') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="nombre" class="form-label">Nombre(s)</label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="{{ old('nombre', $alumno->nombre) }}" required>
            @error('nombre') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
            <input type="text" id="apellido_paterno" name="apellido_paterno" class="form-control" value="{{ old('apellido_paterno', $alumno->apellido_paterno) }}" required>
            @error('apellido_paterno') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="apellido_materno" class="form-label">Apellido Materno (Opcional)</label>
            <input type="text" id="apellido_materno" name="apellido_materno" class="form-control" value="{{ old('apellido_materno', $alumno->apellido_materno) }}">
            @error('apellido_materno') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $alumno->email) }}" required>
            @error('email') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Cambiar Contraseña (Dejar en blanco para mantener la actual)</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="••••••••">
            @error('password') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="activo" class="form-label">Estado de la Cuenta</label>
            <select id="activo" name="activo" class="form-control" required>
                <option value="1" {{ old('activo', $alumno->activo) ? 'selected' : '' }}>Activo</option>
                <option value="0" {{ !old('activo', $alumno->activo) ? 'selected' : '' }}>Desactivado</option>
            </select>
            @error('activo') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
            <a href="{{ route('alumnos.index') }}" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>
</div>
@endsection
