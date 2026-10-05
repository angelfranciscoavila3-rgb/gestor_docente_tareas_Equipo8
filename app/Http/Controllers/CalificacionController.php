<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Calificacion;
use App\Models\Notificacion;
use App\Models\Tarea;
use Illuminate\Http\Request;

class CalificacionController extends Controller
{
    public function evaluarView($tareaId)
    {
        $tarea = Tarea::with(['evidencias.alumno', 'calificaciones'])->findOrFail($tareaId);

        // Verificar que haya pasado la fecha límite de entrega
        if (now()->lessThanOrEqualTo($tarea->fecha_entrega)) {
            return redirect()
                ->route('tareas.show', $tarea->id)
                ->with('error', 'Solo se puede evaluar la tarea después de que haya pasado la fecha límite de entrega (' . $tarea->fecha_entrega->format('d/m/Y H:i') . ').');
        }

        if ($tarea->estado === 'cancelada') {
            return redirect()
                ->route('tareas.show', $tarea->id)
                ->with('error', 'No se puede evaluar una tarea cancelada.');
        }

        $alumnos = Alumno::where('activo', true)
            ->orderBy('apellido_paterno')
            ->orderBy('nombre')
            ->get();

        $evidenciasMap = $tarea->evidencias->keyBy('alumno_id');
        $calificacionesMap = $tarea->calificaciones->keyBy('alumno_id');

        return view('docente.tareas.evaluar', compact('tarea', 'alumnos', 'evidenciasMap', 'calificacionesMap'));
    }

    public function guardarEvaluacion(Request $request, $tareaId)
    {
        $tarea = Tarea::findOrFail($tareaId);

        if (now()->lessThanOrEqualTo($tarea->fecha_entrega)) {
            return back()->with('error', 'No se puede calificar la tarea antes de la fecha límite.');
        }

        $accion = $request->input('accion', 'borrador'); // 'borrador' o 'publicar'
        $estadoCalificacion = ($accion === 'publicar') ? 'publicada' : 'borrador';

        $evaluaciones = $request->input('calificaciones', []);
        $comentarios = $request->input('comentarios', []);

        $guardadas = 0;
        $publicadas = 0;

        foreach ($evaluaciones as $alumnoId => $val) {
            if ($val === null || $val === '') {
                continue;
            }

            $valFloat = (float) $val;
            if ($valFloat < 0 || $valFloat > 10) {
                continue;
            }

            $comentarioStr = $comentarios[$alumnoId] ?? null;

            $califExistente = Calificacion::where('tarea_id', $tarea->id)
                ->where('alumno_id', $alumnoId)
                ->first();

            $fechaPub = ($estadoCalificacion === 'publicada') ? now() : ($califExistente?->fecha_publicacion);

            if ($califExistente) {
                $califExistente->update([
                    'calificacion' => $valFloat,
                    'comentario' => $comentarioStr,
                    'estado' => $estadoCalificacion,
                    'fecha_evaluacion' => now(),
                    'fecha_publicacion' => $fechaPub,
                ]);
            } else {
                Calificacion::create([
                    'tarea_id' => $tarea->id,
                    'alumno_id' => $alumnoId,
                    'calificacion' => $valFloat,
                    'comentario' => $comentarioStr,
                    'estado' => $estadoCalificacion,
                    'fecha_evaluacion' => now(),
                    'fecha_publicacion' => $fechaPub,
                ]);
            }

            $guardadas++;
            if ($estadoCalificacion === 'publicada') {
                $publicadas++;
                // Generar notificación al alumno si la calificación es publicada
                Notificacion::create([
                    'alumno_id' => $alumnoId,
                    'tarea_id' => $tarea->id,
                    'tipo' => 'nueva_tarea', // O notificacion de calificacion
                    'titulo' => 'Calificación publicada: ' . $tarea->titulo,
                    'mensaje' => "Tu docente ha publicado la calificación para la tarea '{$tarea->titulo}'. Calificación: {$valFloat}/10.",
                    'leida' => false,
                ]);
            }
        }

        $mensaje = ($estadoCalificacion === 'publicada')
            ? "Se publicaron $publicadas calificaciones correctamente. Los alumnos ya pueden ver su resultado."
            : "Se guardaron $guardadas calificaciones en borrador. No son visibles aún para los alumnos.";

        return redirect()
            ->route('tareas.show', $tarea->id)
            ->with('success', $mensaje);
    }
}
