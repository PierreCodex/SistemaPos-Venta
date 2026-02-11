<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Ventas realizadas por este usuario (vendedor)
     */
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'user_id');
    }

    /**
     * Horarios asignados al usuario
     */
    public function horarios()
    {
        return $this->belongsToMany(Horario::class, 'horario_user')
                    ->withPivot('fecha_asignacion')
                    ->withTimestamps();
    }

    /**
     * Asistencias del usuario
     */
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'user_id');
    }

    /**
     * Retorna el horario vigente para el usuario
     */
    public function horarioActual()
    {
        return $this->horarios()->where('activo', true)->latest('fecha_asignacion')->first();
    }
}
