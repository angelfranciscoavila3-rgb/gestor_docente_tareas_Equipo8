<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Docente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session()->has('auth_user_id')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Ingresa tu correo electrónico o matrícula.',
            'password.required' => 'Ingresa tu contraseña.',
        ]);

        $loginInput = trim($credentials['login']);

        // Intentar autenticar como Docente
        $docente = Docente::where('email', $loginInput)->first();
        if ($docente) {
            if (!$docente->activo) {
                return back()->withInput()->withErrors(['login' => 'El perfil de docente está desactivado.']);
            }
            if (Hash::check($credentials['password'], $docente->password)) {
                $request->session()->regenerate();
                $request->session()->put([
                    'auth_user_id' => $docente->id,
                    'auth_user_type' => 'docente',
                    'auth_user_name' => $docente->nombre . ' ' . $docente->apellido_paterno,
                    'auth_user_email' => $docente->email,
                ]);
                return redirect()->route('dashboard')->with('success', '¡Bienvenido(a), profesor(a) ' . $docente->nombre . '!');
            }
        }

        // Intentar autenticar como Alumno por email o matricula
        $alumno = Alumno::where('email', $loginInput)
            ->orWhere('matricula', $loginInput)
            ->first();

        if ($alumno) {
            if (!$alumno->activo) {
                return back()->withInput()->withErrors(['login' => 'Tu cuenta de alumno se encuentra desactivada. Contacta al docente.']);
            }
            if (Hash::check($credentials['password'], $alumno->password)) {
                $request->session()->regenerate();
                $request->session()->put([
                    'auth_user_id' => $alumno->id,
                    'auth_user_type' => 'alumno',
                    'auth_user_name' => $alumno->nombre . ' ' . $alumno->apellido_paterno,
                    'auth_user_email' => $alumno->email,
                    'auth_user_matricula' => $alumno->matricula,
                ]);
                return redirect()->route('dashboard')->with('success', '¡Bienvenido(a), ' . $alumno->nombre . '!');
            }
        }

        return back()
            ->withInput($request->only('login'))
            ->withErrors([
                'login' => 'Las credenciales proporcionadas son incorrectas.',
            ]);
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Has cerrado sesión correctamente.');
    }
}
