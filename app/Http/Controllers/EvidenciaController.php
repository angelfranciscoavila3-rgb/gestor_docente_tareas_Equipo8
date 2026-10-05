<?php

namespace App\Http\Controllers;

use App\Models\Evidencia;
use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EvidenciaController extends Controller
{
    public function store(Request $request, $tareaId)
    {
        $tarea = Tarea::findOrFail($tareaId);
        $alumnoId = session('auth_user_id');

        if (session('auth_user_type') !== 'alumno') {
            abort(403, 'Solo los alumnos pueden subir evidencias.');
        }

        if ($tarea->estado === 'cancelada') {
            return back()->with('error', 'No se pueden subir evidencias a una tarea cancelada.');
        }

        if (now()->greaterThan($tarea->fecha_entrega)) {
            return back()->with('error', 'El tiempo límite de entrega para esta tarea ha expirado (Venció el ' . $tarea->fecha_entrega->format('d/m/Y H:i') . ').');
        }

        $request->validate([
            'archivo_evidencia' => [
                'required',
                'file',
                'mimes:pdf',
                'max:2048', // Máximo 2MB (2048 KB)
            ],
        ], [
            'archivo_evidencia.required' => 'Debes seleccionar un archivo de evidencia.',
            'archivo_evidencia.mimes' => 'La evidencia debe ser estrictamente un archivo en formato PDF.',
            'archivo_evidencia.max' => 'El archivo no debe superar el tamaño máximo de 2MB.',
        ]);

        $file = $request->file('archivo_evidencia');
        $nombreOriginal = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();
        $tamanoBytes = $file->getSize();

        // Almacenar en storage/app/public/evidencias
        $rutaAlmacenada = $file->store('evidencias', 'public');

        // Buscar si ya existía una evidencia previa para reemplazarla
        $evidenciaExistente = Evidencia::where('tarea_id', $tarea->id)
            ->where('alumno_id', $alumnoId)
            ->first();

        if ($evidenciaExistente) {
            // Eliminar archivo antiguo
            if (Storage::disk('public')->exists($evidenciaExistente->ruta)) {
                Storage::disk('public')->delete($evidenciaExistente->ruta);
            }

            $evidenciaExistente->update([
                'nombre_original' => $nombreOriginal,
                'ruta' => $rutaAlmacenada,
                'mime_type' => $mimeType,
                'tamano_bytes' => $tamanoBytes,
                'fecha_subida' => now(),
            ]);

            return back()->with('success', '¡Evidencia actualizada exitosamente!');
        }

        Evidencia::create([
            'tarea_id' => $tarea->id,
            'alumno_id' => $alumnoId,
            'nombre_original' => $nombreOriginal,
            'ruta' => $rutaAlmacenada,
            'mime_type' => $mimeType,
            'tamano_bytes' => $tamanoBytes,
            'fecha_subida' => now(),
        ]);

        return back()->with('success', '¡Evidencia entregada exitosamente!');
    }

    public function download($id)
    {
        $evidencia = Evidencia::with('tarea')->findOrFail($id);
        $userType = session('auth_user_type');
        $userId = session('auth_user_id');

        // Validar permisos
        if ($userType === 'alumno' && $evidencia->alumno_id != $userId) {
            abort(403, 'No tienes permiso para ver esta evidencia.');
        }

        if (!Storage::disk('public')->exists($evidencia->ruta)) {
            return back()->with('error', 'El archivo solicitado no existe en el servidor.');
        }

        return Storage::disk('public')->download($evidencia->ruta, $evidencia->nombre_original);
    }
}
