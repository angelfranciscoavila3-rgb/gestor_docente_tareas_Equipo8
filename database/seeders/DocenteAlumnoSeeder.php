<?php

namespace Database\Seeders;

use App\Models\Alumno;
use App\Models\Docente;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DocenteAlumnoSeeder extends Seeder
{
    public function run(): void
    {
        Docente::create([
            'nombre' => 'Carlos',
            'apellido_paterno' => 'Ramírez',
            'apellido_materno' => 'López',
            'email' => 'docente@example.com',
            'password' => Hash::make('password'),
            'activo' => true,
        ]);

        $alumnos = [
            [
                'matricula' => 'ALU001',
                'nombre' => 'Ana',
                'apellido_paterno' => 'García',
                'apellido_materno' => 'López',
                'email' => 'ana.garcia@example.com',
            ],
            [
                'matricula' => 'ALU002',
                'nombre' => 'Luis',
                'apellido_paterno' => 'Martínez',
                'apellido_materno' => 'Soto',
                'email' => 'luis.martinez@example.com',
            ],
            [
                'matricula' => 'ALU003',
                'nombre' => 'Sofía',
                'apellido_paterno' => 'Hernández',
                'apellido_materno' => 'Ruiz',
                'email' => 'sofia.hernandez@example.com',
            ],
            [
                'matricula' => 'ALU004',
                'nombre' => 'Diego',
                'apellido_paterno' => 'Torres',
                'apellido_materno' => 'Vega',
                'email' => 'diego.torres@example.com',
            ],
            [
                'matricula' => 'ALU005',
                'nombre' => 'Valeria',
                'apellido_paterno' => 'Morales',
                'apellido_materno' => 'Cruz',
                'email' => 'valeria.morales@example.com',
            ],
            [
                'matricula' => 'ALU006',
                'nombre' => 'Mateo',
                'apellido_paterno' => 'Flores',
                'apellido_materno' => 'Nava',
                'email' => 'mateo.flores@example.com',
            ],
            [
                'matricula' => 'ALU007',
                'nombre' => 'Camila',
                'apellido_paterno' => 'Vargas',
                'apellido_materno' => 'Mendoza',
                'email' => 'camila.vargas@example.com',
            ],
            [
                'matricula' => 'ALU008',
                'nombre' => 'Daniel',
                'apellido_paterno' => 'Castillo',
                'apellido_materno' => 'Rojas',
                'email' => 'daniel.castillo@example.com',
            ],
            [
                'matricula' => 'ALU009',
                'nombre' => 'Mariana',
                'apellido_paterno' => 'Navarro',
                'apellido_materno' => 'Santos',
                'email' => 'mariana.navarro@example.com',
            ],
            [
                'matricula' => 'ALU010',
                'nombre' => 'Emiliano',
                'apellido_paterno' => 'Ortega',
                'apellido_materno' => 'Pérez',
                'email' => 'emiliano.ortega@example.com',
            ],
            [
                'matricula' => 'ALU011',
                'nombre' => 'Regina',
                'apellido_paterno' => 'Mendoza',
                'apellido_materno' => 'Silva',
                'email' => 'regina.mendoza@example.com',
            ],
            [
                'matricula' => 'ALU012',
                'nombre' => 'Sebastián',
                'apellido_paterno' => 'Reyes',
                'apellido_materno' => 'Campos',
                'email' => 'sebastian.reyes@example.com',
            ],
            [
                'matricula' => 'ALU013',
                'nombre' => 'Natalia',
                'apellido_paterno' => 'Jiménez',
                'apellido_materno' => 'Valdez',
                'email' => 'natalia.jimenez@example.com',
            ],
            [
                'matricula' => 'ALU014',
                'nombre' => 'Alejandro',
                'apellido_paterno' => 'Pineda',
                'apellido_materno' => 'Luna',
                'email' => 'alejandro.pineda@example.com',
            ],
            [
                'matricula' => 'ALU015',
                'nombre' => 'Fernanda',
                'apellido_paterno' => 'Salazar',
                'apellido_materno' => 'Mora',
                'email' => 'fernanda.salazar@example.com',
            ],
        ];

        foreach ($alumnos as $alumno) {
            Alumno::create([
                ...$alumno,
                'password' => Hash::make('password'),
                'activo' => true,
            ]);
        }
    }
}
