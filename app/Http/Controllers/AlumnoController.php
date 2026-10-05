<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AlumnoController extends Controller
{
    public function index(Request $request)
    {
        $query = Alumno::query();

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('apellido_paterno', 'like', "%{$buscar}%")
                    ->orWhere('apellido_materno', 'like', "%{$buscar}%")
                    ->orWhere('matricula', 'like', "%{$buscar}%")
                    ->orWhere('email', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('estado')) {
            $activo = $request->estado === 'activo';
            $query->where('activo', $activo);
        }

        $alumnos = $query->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombre')
            ->paginate(15);

        return view('docente.alumnos.index', compact('alumnos'));
    }

    public function create()
    {
        return view('docente.alumnos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'matricula' => ['required', 'string', 'max:50', 'unique:alumnos,matricula'],
            'nombre' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:alumnos,email'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'matricula.required' => 'La matrícula es obligatoria.',
            'matricula.unique' => 'La matrícula ya está registrada.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        Alumno::create([
            'matricula' => strtoupper(trim($validated['matricula'])),
            'nombre' => trim($validated['nombre']),
            'apellido_paterno' => trim($validated['apellido_paterno']),
            'apellido_materno' => $validated['apellido_materno'] ? trim($validated['apellido_materno']) : null,
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'activo' => true,
        ]);

        return redirect()
            ->route('alumnos.index')
            ->with('success', 'Alumno registrado exitosamente.');
    }

    public function showBulk()
    {
        return view('docente.alumnos.bulk');
    }

    public function storeBulk(Request $request)
    {
        $request->validate([
            'archivo_csv' => ['nullable', 'file', 'mimes:csv,txt', 'max:2048'],
            'texto_masivo' => ['nullable', 'string'],
        ]);

        $lineas = [];

        if ($request->hasFile('archivo_csv')) {
            $content = file_get_contents($request->file('archivo_csv')->getRealPath());
            $lineas = explode("\n", str_replace("\r", "", $content));
        } elseif ($request->filled('texto_masivo')) {
            $lineas = explode("\n", str_replace("\r", "", $request->texto_masivo));
        } else {
            return back()->withErrors(['texto_masivo' => 'Debes adjuntar un archivo CSV o escribir los registros en el área de texto.']);
        }

        $creados = 0;
        $errores = [];

        foreach ($lineas as $index => $linea) {
            $linea = trim($linea);
            if (empty($linea) || str_starts_with(strtolower($linea), 'matricula')) {
                continue; // Omitir encabezado o líneas vacías
            }

            $partes = array_map('trim', explode(',', $linea));

            if (count($partes) < 4) {
                $errores[] = "Línea " . ($index + 1) . ": Formato insuficiente (se requiere al menos matrícula, nombre, apellido paterno y correo).";
                continue;
            }

            $matricula = strtoupper($partes[0]);
            $nombre = $partes[1];
            $apellidoPaterno = $partes[2];
            $apellidoMaterno = isset($partes[3]) && !str_contains($partes[3], '@') ? $partes[3] : null;
            
            // Buscar email en partes
            $email = null;
            $password = 'password';

            foreach ($partes as $p) {
                if (str_contains($p, '@')) {
                    $email = strtolower($p);
                }
            }

            // Si la última parte no es email y hay más partes, tomar la última como password
            if (count($partes) >= 5 && !str_contains(end($partes), '@')) {
                $password = end($partes);
            }

            if (!$email) {
                $errores[] = "Línea " . ($index + 1) . ": No se encontró un correo electrónico válido.";
                continue;
            }

            if (Alumno::where('matricula', $matricula)->exists()) {
                $errores[] = "Línea " . ($index + 1) . ": Matrícula $matricula ya existe.";
                continue;
            }

            if (Alumno::where('email', $email)->exists()) {
                $errores[] = "Línea " . ($index + 1) . ": Correo $email ya existe.";
                continue;
            }

            Alumno::create([
                'matricula' => $matricula,
                'nombre' => $nombre,
                'apellido_paterno' => $apellidoPaterno,
                'apellido_materno' => $apellidoMaterno,
                'email' => $email,
                'password' => Hash::make($password),
                'activo' => true,
            ]);

            $creados++;
        }

        $msg = "Se crearon $creados alumnos correctamente.";
        if (count($errores) > 0) {
            return redirect()->route('alumnos.index')
                ->with('warning', $msg . ' Se encontraron ' . count($errores) . ' advertencias.')
                ->with('bulk_errors', $errores);
        }

        return redirect()
            ->route('alumnos.index')
            ->with('success', $msg);
    }

    public function edit($id)
    {
        $alumno = Alumno::findOrFail($id);
        return view('docente.alumnos.edit', compact('alumno'));
    }

    public function update(Request $request, $id)
    {
        $alumno = Alumno::findOrFail($id);

        $validated = $request->validate([
            'matricula' => ['required', 'string', 'max:50', 'unique:alumnos,matricula,' . $alumno->id],
            'nombre' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:alumnos,email,' . $alumno->id],
            'password' => ['nullable', 'string', 'min:6'],
            'activo' => ['required', 'boolean'],
        ]);

        $data = [
            'matricula' => strtoupper(trim($validated['matricula'])),
            'nombre' => trim($validated['nombre']),
            'apellido_paterno' => trim($validated['apellido_paterno']),
            'apellido_materno' => $validated['apellido_materno'] ? trim($validated['apellido_materno']) : null,
            'email' => strtolower(trim($validated['email'])),
            'activo' => (bool)$validated['activo'],
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $alumno->update($data);

        return redirect()
            ->route('alumnos.index')
            ->with('success', 'Datos del alumno actualizados correctamente.');
    }

    public function toggleEstado($id)
    {
        $alumno = Alumno::findOrFail($id);
        $alumno->update(['activo' => !$alumno->activo]);

        $estadoStr = $alumno->activo ? 'activado' : 'desactivado';
        return back()->with('success', "Perfil del alumno {$alumno->nombre} {$alumno->apellido_paterno} ha sido {$estadoStr}.");
    }
}
