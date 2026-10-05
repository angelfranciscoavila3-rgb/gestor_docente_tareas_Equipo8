<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tarea extends Model
{
    use HasFactory;

    protected $table = 'tareas';

    protected $fillable = [
        'docente_id',
        'titulo',
        'descripcion',
        'tipo',
        'estado',
        'fecha_asignacion',
        'fecha_entrega',
        'fecha_cierre',
        'fecha_cancelacion',
        'motivo_cancelacion',
    ];

    protected $casts = [
        'fecha_asignacion' => 'datetime',
        'fecha_entrega' => 'datetime',
        'fecha_cierre' => 'datetime',
        'fecha_cancelacion' => 'datetime',
    ];

    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class, 'docente_id');
    }

    public function evidencias(): HasMany
    {
        return $this->hasMany(Evidencia::class, 'tarea_id');
    }

    public function calificaciones(): HasMany
    {
        return $this->hasMany(Calificacion::class, 'tarea_id');
    }

    public function notificaciones(): HasMany
    {
        return $this->hasMany(Notificacion::class, 'tarea_id');
    }

    public function getTipoNombreAttribute(): string
    {
        return match ($this->tipo) {
            'tarea_casa' => 'Tarea en casa',
            'actividad_clase' => 'Actividad en clase',
            'exposicion' => 'Exposición',
            default => 'Tarea',
        };
    }

    public function getEsVencidaAttribute(): bool
    {
        return $this->estado !== 'cancelada' && now()->greaterThan($this->fecha_entrega);
    }
}
