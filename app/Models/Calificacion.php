<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Calificacion extends Model
{
    use HasFactory;

    protected $table = 'calificaciones';

    protected $fillable = [
        'tarea_id',
        'alumno_id',
        'calificacion',
        'comentario',
        'estado',
        'fecha_evaluacion',
        'fecha_publicacion',
    ];

    protected $casts = [
        'calificacion' => 'float',
        'fecha_evaluacion' => 'datetime',
        'fecha_publicacion' => 'datetime',
    ];

    public function tarea(): BelongsTo
    {
        return $this->belongsTo(Tarea::class, 'tarea_id');
    }

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }
}
