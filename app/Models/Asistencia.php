<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Asistencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'user_id',
        'horario_id',
        'fecha',
        'hora_entrada',
        'hora_inicio_refrigerio',
        'hora_fin_refrigerio',
        'hora_salida',
        'minutos_tardanza',
        'minutos_trabajados',
        'minutos_extra',
        'estado',
        'observaciones'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class, 'horario_id');
    }

    public function getDuracionRefrigerioAttribute()
    {
        if ($this->hora_inicio_refrigerio && $this->hora_fin_refrigerio) {
            $inicio = Carbon::parse($this->hora_inicio_refrigerio);
            $fin = Carbon::parse($this->hora_fin_refrigerio);
            return $inicio->diffInMinutes($fin);
        }
        return 0;
    }

    public function scopeDelDia($query, $fecha = null)
    {
        return $query->whereDate('fecha', $fecha ?? Carbon::today());
    }

    public function scopeDelMes($query, $mes, $anio)
    {
        return $query->whereMonth('fecha', $mes)->whereYear('fecha', $anio);
    }

    public function scopeDelEmpleado($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public static function generarCodigo()
    {
        do {
            $codigo = strtoupper(Str::random(12));
        } while (self::where('codigo', $codigo)->exists());

        return $codigo;
    }
}
