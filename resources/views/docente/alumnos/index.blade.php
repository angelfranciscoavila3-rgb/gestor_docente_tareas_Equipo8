@extends('layouts.app')

@section('title', 'Gestión de Alumnos - Docente')
@section('page_title', 'Administración de Alumnos del Grupo')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Listado de Alumnos</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('alumnos.bulk') }}" class="btn btn-secondary btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Carga Masiva (CSV / Texto)
            </a>
            <a href="{{ route('alumnos.create') }}" class="btn btn-primary btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Captura Individual
            </a>
        </div>
    </div>

    <!-- Buscador y filtro -->
    <form action="{{ route('alumnos.index') }}" method="GET" style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap;">
        <div style="flex: 2; min-width: 200px;">
            <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Buscar por matrícula, nombre o correo...">
        </div>
        <div style="flex: 1; min-width: 150px;">
            <select name="estado" class="form-control" onchange="this.form.submit()">
                <option value="">-- Todos los estados --</option>
                <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activos</option>
                <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Desactivados</option>
            </select>
        </div>
        <button type="submit" class="btn btn-outline">Buscar</button>
        @if(request()->hasAny(['buscar', 'estado']))
            <a href="{{ route('alumnos.index') }}" class="btn btn-outline">Limpiar</a>
        @endif
    </form>

    @if(session('bulk_errors'))
        <div class="alert alert-warning">
            <div>
                <strong>Ocurrieron las siguientes observaciones durante la carga masiva:</strong>
                <ul style="margin-top: 6px; padding-left: 20px;">
                    @foreach(session('bulk_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Matrícula</th>
                    <th>Nombre Completo</th>
                    <th>Correo Electrónico</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alumnos as $alumno)
                    <tr>
                        <td><code>{{ $alumno->matricula }}</code></td>
                        <td><strong>{{ $alumno->nombre_completo }}</strong></td>
                        <td>{{ $alumno->email }}</td>
                        <td>
                            @if($alumno->activo)
                                <span class="badge badge-success">Activo</span>
                            @else
                                <span class="badge badge-danger">Desactivado</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 6px;">
                                <a href="{{ route('alumnos.edit', $alumno->id) }}" class="btn btn-outline btn-sm">Editar</a>
                                <form action="{{ route('alumnos.toggle', $alumno->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn {{ $alumno->activo ? 'btn-danger' : 'btn-secondary' }} btn-sm" onclick="return confirm('¿Estás seguro de cambiar el estado de este alumno?')">
                                        {{ $alumno->activo ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            No hay alumnos registrados que coincidan con la búsqueda.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 16px;">
        {{ $alumnos->links() }}
    </div>
</div>
@endsection
