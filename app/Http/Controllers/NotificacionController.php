<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function index()
    {
        $alumnoId = session('auth_user_id');

        $notificaciones = Notificacion::with('tarea')
            ->where('alumno_id', $alumnoId)
            ->latest()
            ->paginate(15);

        return view('alumno.notificaciones.index', compact('notificaciones'));
    }

    public function marcarLeida($id)
    {
        $alumnoId = session('auth_user_id');

        $notificacion = Notificacion::where('id', $id)
            ->where('alumno_id', $alumnoId)
            ->firstOrFail();

        $notificacion->update([
            'leida' => true,
            'fecha_lectura' => now(),
        ]);

        return back()->with('success', 'Notificación marcada como leída.');
    }

    public function marcarTodasLeidas()
    {
        $alumnoId = session('auth_user_id');

        Notificacion::where('alumno_id', $alumnoId)
            ->where('leida', false)
            ->update([
                'leida' => true,
                'fecha_lectura' => now(),
            ]);

        return back()->with('success', 'Todas las notificaciones han sido marcadas como leídas.');
    }
}
