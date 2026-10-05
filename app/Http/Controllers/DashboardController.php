<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Calificacion;
use App\Models\Evidencia;
use App\Models\Notificacion;
use App\Models\Tarea;

class DashboardController extends Controller
{
    public function index()
    {
        if (session('auth_user_type') === 'docente') {
            return $this->docenteDashboard();
        }

        return $this->alumnoDashboard();
    }

    private function docenteDashboard()
    {
        $totalAlumnos = Alumno::where('activo', true)->count();
        $totalAlumnosInactivos = Alumno::where('activo', false)->count();

        $tareasActivas = Tarea::where('estado', 'activa')
            ->where('fecha_entrega', '>=', now())
            ->count();

        $tareasVencidas = Tarea::where('fecha_entrega', '<', now())
            ->where('estado', '!=', 'cancelada')
            ->count();

        $totalEvidencias = Evidencia::count();

        $tareasRecientes = Tarea::withCount([
            'evidencias',
            'calificaciones',
        ])
            ->latest('fecha_asignacion')
            ->take(6)
            ->get();

        return view('docente.dashboard', compact(
            'totalAlumnos',
            'totalAlumnosInactivos',
            'tareasActivas',
            'tareasVencidas',
            'totalEvidencias',
            'tareasRecientes'
        ));
    }

    private function alumnoDashboard()
    {
        $alumnoId = session('auth_user_id');
        $alumno = Alumno::findOrFail($alumnoId);

        $tareasRecientes = Tarea::with([
            'evidencias' => fn ($query) => $query->where('alumno_id', $alumnoId),
            'calificaciones' => fn ($query) => $query->where('alumno_id', $alumnoId),
        ])
            ->where('estado', '!=', 'cancelada')
            ->latest('fecha_asignacion')
            ->take(6)
            ->get();

        $pendientes = Tarea::where('estado', 'activa')
            ->where('fecha_entrega', '>=', now())
            ->whereDoesntHave('evidencias', function ($query) use ($alumnoId) {
                $query->where('alumno_id', $alumnoId);
            })
            ->count();

        $entregadas = Evidencia::where('alumno_id', $alumnoId)->count();

        $notificacionesCount = Notificacion::where('alumno_id', $alumnoId)
            ->where('leida', false)
            ->count();

        $promedio = Calificacion::where('alumno_id', $alumnoId)
            ->where('estado', 'publicada')
            ->avg('calificacion');

        return view('alumno.dashboard', compact(
            'alumno',
            'tareasRecientes',
            'pendientes',
            'entregadas',
            'notificacionesCount',
            'promedio'
        ));
    }
}
