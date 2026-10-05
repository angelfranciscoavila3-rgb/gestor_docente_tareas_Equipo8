<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Gestión Académica</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="login-body">
    <div class="login-card">
        <div class="login-header">
            <div class="login-logo">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
            </div>
            <h1 class="login-title">Iniciar Sesión</h1>
            <p class="login-subtitle">Sistema de Tareas y Evidencias Académicas</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->has('login'))
            <div class="alert alert-danger">
                <span>{{ $errors->first('login') }}</span>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="login" class="form-label">Correo electrónico / Matrícula</label>
                <input type="text" id="login" name="login" class="form-control" value="{{ old('login') }}" placeholder="ej. docente@example.com ó ALU001" required autofocus>
                @error('login')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                Ingresar al Sistema
            </button>
        </form>

        <div style="margin-top: 24px; text-align: center; font-size: 11px; color: var(--text-muted);">
            Plataforma de entrega de evidencias para Docentes y Alumnos
        </div>
    </div>
</body>
</html>
