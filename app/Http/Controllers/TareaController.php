<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Notificacion;
use App\Models\Tarea;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TareaController extends Controller
{
    public function index(Request $request)
    {
        $userType = session('auth_user_type');
        $query = Tarea::query();

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('estado')) {
            if ($request->estado === 'vencida') {
                $query->where('fecha_entrega', '<', now())->where('estado', '!=', 'cancelada');
            } else {
                $query->where('estado', $request->estado);
            }
        }

        if ($userType === 'docente') {
            $tareas = $query->withCount(['evidencias', 'calificaciones'])
                ->latest('fecha_asignacion')
                ->paginate(10);
            return view('docente.tareas.index', compact('tareas'));
        } else {
            $alumnoId = session('auth_user_id');
            $tareas = $query->with([
                'evidencias' => fn ($q) => $q->where('alumno_id', $alumnoId),
                'calificaciones' => fn ($q) => $q->where('alumno_id', $alumnoId),
            ])
                ->where('estado', '!=', 'cancelada')
                ->latest('fecha_asignacion')
                ->paginate(10);

            return view('alumno.tareas.index', compact('tareas'));
        }
    }

    public function create()
    {
        return view('docente.tareas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:200'],
            'descripcion' => ['required', 'string'],
            'tipo' => ['required', 'in:tarea_casa,actividad_clase,exposicion'],
            'fecha_entrega_date' => ['required', 'date', 'after_or_equal:today'],
        ], [
            'titulo.required' => 'El título de la tarea es obligatorio.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'tipo.required' => 'Debes seleccionar un tipo de tarea válido.',
            'tipo.in' => 'El tipo de tarea debe ser Tareas en casa, Actividades en clase o Exposiciones.',
            'fecha_entrega_date.required' => 'Debes indicar la fecha de entrega.',
            'fecha_entrega_date.after_or_equal' => 'La fecha de entrega debe ser hoy o una fecha futura.',
        ]);

        // Ajustar fecha de entrega exacto a las 23:59:59 del día de entrega
        $fechaEntrega = Carbon::parse($validated['fecha_entrega_date'])->setTime(23, 59, 59);

        $tarea = Tarea::create([
            'docente_id' => session('auth_user_id'),
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'],
            'tipo' => $validated['tipo'],
            'estado' => 'activa',
            'fecha_asignacion' => now(),
            'fecha_entrega' => $fechaEntrega,
        ]);

        // Generar notificación para todos los alumnos activos
        $alumnos = Alumno::where('activo', true)->get();
        $tipoNombre = match ($tarea->tipo) {
            'tarea_casa' => 'Tarea en casa',
            'actividad_clase' => 'Actividad en clase',
            'exposicion' => 'Exposición',
        };

        foreach ($alumnos as $alumno) {
            Notificacion::create([
                'alumno_id' => $alumno->id,
                'tarea_id' => $tarea->id,
                'tipo' => 'nueva_tarea',
                'titulo' => 'Nueva tarea asignada: ' . $tarea->titulo,
                'mensaje' => "Se ha publicado la tarea ($tipoNombre). Fecha límite de entrega: " . $fechaEntrega->format('d/m/Y H:i') . ".",
                'leida' => false,
            ]);
        }

        return redirect()
            ->route('tareas.show', $tarea->id)
            ->with('success', 'Tarea creada correctamente y notificada a los alumnos.');
    }

    public function show($id)
    {
        $tarea = Tarea::with(['docente', 'evidencias.alumno', 'calificaciones.alumno'])->findOrFail($id);
        $userType = session('auth_user_type');

        if ($userType === 'docente') {
            $alumnos = Alumno::where('activo', true)->orderBy('apellido_paterno')->orderBy('nombre')->get();
            
            // Map de evidencias por alumno_id
            $evidenciasMap = $tarea->evidencias->keyBy('alumno_id');
            $calificacionesMap = $tarea->calificaciones->keyBy('alumno_id');

            return view('docente.tareas.show', compact('tarea', 'alumnos', 'evidenciasMap', 'calificacionesMap'));
        } else {
            $alumnoId = session('auth_user_id');
            $evidencia = $tarea->evidencias->where('alumno_id', $alumnoId)->first();
            $calificacion = $tarea->calificaciones->where('alumno_id', $alumnoId)->where('estado', 'publicada')->first();

            return view('alumno.tareas.show', compact('tarea', 'evidencia', 'calificacion'));
        }
    }

    public function cancelar(Request $request, $id)
    {
        $tarea = Tarea::findOrFail($id);

        if ($tarea->estado === 'cancelada') {
            return back()->with('error', 'Esta tarea ya ha sido cancelada anteriormente.');
        }

        $request->validate([
            'motivo_cancelacion' => ['nullable', 'string', 'max:500'],
        ]);

        $tarea->update([
            'estado' => 'cancelada',
            'fecha_cancelacion' => now(),
            'motivo_cancelacion' => $request->motivo_cancelacion ?? 'Tarea cancelada por el docente.',
        ]);

        // Notificar a todos los alumnos activos
        $alumnos = Alumno::where('activo', true)->get();
        foreach ($alumnos as $alumno) {
            Notificacion::create([
                'alumno_id' => $alumno->id,
                'tarea_id' => $tarea->id,
                'tipo' => 'tarea_cancelada',
                'titulo' => 'Tarea cancelada: ' . $tarea->titulo,
                'mensaje' => 'La tarea ha sido cancelada por el docente. Motivo: ' . ($request->motivo_cancelacion ?? 'Sin especificar.'),
                'leida' => false,
            ]);
        }

        return redirect()
            ->route('tareas.show', $tarea->id)
            ->with('success', 'La tarea ha sido cancelada y los alumnos han sido notificados.');
    }
}
