<?php

use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalificacionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvidenciaController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\TareaController;
use Illuminate\Support\Facades\Route;

// Redirección principal
Route::get('/', function () {
    if (session()->has('auth_user_id')) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas por autenticación personalizada
Route::middleware(['custom.auth'])->group(function () {
    // Dashboard unificado
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Tareas consulta común
    Route::get('/tareas', [TareaController::class, 'index'])->name('tareas.index');
    Route::get('/tareas/detalle/{id}', [TareaController::class, 'show'])->name('tareas.show');
    Route::get('/evidencias/{id}/descargar', [EvidenciaController::class, 'download'])->name('evidencias.download');

    // Rutas exclusivas del Docente
    Route::middleware(['role:docente'])->group(function () {
        Route::get('/tareas/crear', [TareaController::class, 'create'])->name('tareas.create');
        Route::post('/tareas', [TareaController::class, 'store'])->name('tareas.store');
        Route::post('/tareas/{id}/cancelar', [TareaController::class, 'cancelar'])->name('tareas.cancelar');

        Route::get('/tareas/{id}/evaluar', [CalificacionController::class, 'evaluarView'])->name('calificaciones.evaluar');
        Route::post('/tareas/{id}/evaluar', [CalificacionController::class, 'guardarEvaluacion'])->name('calificaciones.guardar');

        Route::get('/alumnos', [AlumnoController::class, 'index'])->name('alumnos.index');
        Route::get('/alumnos/crear', [AlumnoController::class, 'create'])->name('alumnos.create');
        Route::post('/alumnos', [AlumnoController::class, 'store'])->name('alumnos.store');
        Route::get('/alumnos/masiva', [AlumnoController::class, 'showBulk'])->name('alumnos.bulk');
        Route::post('/alumnos/masiva', [AlumnoController::class, 'storeBulk'])->name('alumnos.bulk.store');
        Route::get('/alumnos/{id}/editar', [AlumnoController::class, 'edit'])->name('alumnos.edit');
        Route::post('/alumnos/{id}', [AlumnoController::class, 'update'])->name('alumnos.update');
        Route::post('/alumnos/{id}/estado', [AlumnoController::class, 'toggleEstado'])->name('alumnos.toggle');
    });

    // Rutas exclusivas del Alumno
    Route::middleware(['role:alumno'])->group(function () {
        Route::post('/tareas/{id}/evidencia', [EvidenciaController::class, 'store'])->name('evidencias.store');

        Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
        Route::post('/notificaciones/{id}/marcar', [NotificacionController::class, 'marcarLeida'])->name('notificaciones.marcar');
        Route::post('/notificaciones/marcar-todas', [NotificacionController::class, 'marcarTodasLeidas'])->name('notificaciones.marcarTodas');
    });
});
