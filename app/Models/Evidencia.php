<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evidencia extends Model
{
    use HasFactory;

    protected $table = 'evidencias';

    protected $fillable = [
        'tarea_id',
        'alumno_id',
        'nombre_original',
        'ruta',
        'mime_type',
        'tamano_bytes',
        'fecha_subida',
    ];

    protected $casts = [
        'fecha_subida' => 'datetime',
        'tamano_bytes' => 'integer',
    ];

    public function tarea(): BelongsTo
    {
        return $this->belongsTo(Tarea::class, 'tarea_id');
    }

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }

    public function getTamanoFormateadoAttribute(): string
    {
        $bytes = $this->tamano_bytes;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        return number_format($bytes / 1024, 2) . ' KB';
    }
}
