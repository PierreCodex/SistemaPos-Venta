<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Empresa
 * 
 * Representa los datos de la empresa/negocio
 * 
 * @property int $id
 * @property string $ruc
 * @property string $razon_social
 * @property string|null $nombre_comercial
 * @property string|null $direccion
 * @property string|null $telefono
 * @property string|null $email
 * @property string|null $logo
 * @property float $igv_porcentaje
 * @property string $moneda
 */
class Empresa extends Model
{
    protected $table = 'empresa';

    protected $fillable = [
        'ruc',
        'razon_social',
        'nombre_comercial',
        'direccion',
        'telefono',
        'email',
        'logo',
        'igv_porcentaje',
        'moneda',
        'sunat_sol_user',
        'sunat_sol_pass',
        'sunat_cert_path',
        'sunat_client_id',
        'sunat_client_secret',
        'sunat_produccion'
    ];

    protected $casts = [
        'igv_porcentaje' => 'decimal:2',
    ];

    /**
     * Obtiene la empresa principal (ID = 1)
     */
    public static function principal(): ?self
    {
        return self::find(1);
    }

    /**
     * Obtiene el nombre a mostrar (comercial o razón social)
     */
    public function getNombreAttribute(): string
    {
        return $this->nombre_comercial ?? $this->razon_social;
    }
}
