<?php

namespace App\Services;

use App\Models\Horario;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class HorarioService
{
    public function listar()
    {
        return Horario::withCount('usuarios')->get();
    }

    public function crear(array $data)
    {
        $data['codigo'] = Horario::generarCodigo();
        return Horario::create($data);
    }

    public function actualizar($id, array $data)
    {
        $horario = Horario::findOrFail($id);
        $horario->update($data);
        return $horario;
    }

    public function eliminar($id)
    {
        $horario = Horario::findOrFail($id);
        return $horario->delete();
    }

    public function toggleEstado($id)
    {
        $horario = Horario::findOrFail($id);
        $horario->activo = !$horario->activo;
        $horario->save();
        return $horario;
    }

    public function asignarUsuarios($horarioId, array $userIds)
    {
        $horario = Horario::findOrFail($horarioId);
        $fechaAsignacion = now()->toDateString();
        
        $syncData = [];
        foreach ($userIds as $userId) {
            $syncData[$userId] = ['fecha_asignacion' => $fechaAsignacion];
        }

        return $horario->usuarios()->sync($syncData);
    }

    public function getUsuariosDisponibles()
    {
        return User::all(); // Simple for now, could filter those without current active schedule
    }

    public function getUsuariosAsignados($horarioId)
    {
        $horario = Horario::findOrFail($horarioId);
        return $horario->usuarios()->select('users.id', 'users.name')->get();
    }
}
