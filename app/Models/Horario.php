<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Horario extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
        'hora_inicio',
        'hora_fin',
        'hora_inicio_refrigerio',
        'hora_fin_refrigerio',
        'dias_laborales',
        'tolerancia_minutos',
        'sueldo_base',
        'descuento_falta',
        'descuento_minuto',
        'pago_hora_extra',
        'activo'
    ];

    protected $casts = [
        'dias_laborales' => 'array',
        'activo' => 'boolean',
        'sueldo_base' => 'decimal:2',
        'descuento_falta' => 'decimal:2',
        'descuento_minuto' => 'decimal:2',
        'pago_hora_extra' => 'decimal:2',
    ];

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'horario_user')
                    ->withPivot('fecha_asignacion')
                    ->withTimestamps();
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public static function generarCodigo()
    {
        do {
            $codigo = strtoupper(Str::random(6));
        } while (self::where('codigo', $codigo)->exists());

        return $codigo;
    }

    // Mutators/Accessors for time formatting if needed
    public function getHoraInicioFormattedAttribute()
    {
        return \Carbon\Carbon::parse($this->hora_inicio)->format('h:i A');
    }

    public function getHoraFinFormattedAttribute()
    {
        return \Carbon\Carbon::parse($this->hora_fin)->format('h:i A');
    }
}
