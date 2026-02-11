<?php

namespace App\Services;

use App\Models\Asistencia;
use App\Models\Horario;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AsistenciaService
{
    public function listar($filtros = [])
    {
        $query = Asistencia::with(['usuario', 'horario']);

        if (!empty($filtros['fecha_inicio'])) {
            $query->whereDate('fecha', '>=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $query->whereDate('fecha', '<=', $filtros['fecha_fin']);
        }
        if (!empty($filtros['user_id'])) {
            $query->where('user_id', $filtros['user_id']);
        }

        return $query->orderBy('fecha', 'desc')->get();
    }

    public function registrarEntrada($userId)
    {
        $user = User::findOrFail($userId);
        $horario = $user->horarioActual();

        if (!$horario) {
            throw new \Exception("El usuario no tiene un horario asignado actualmente.");
        }

        $hoy = Carbon::today()->toDateString();
        $asistenciaExistente = Asistencia::where('user_id', $userId)
            ->where('fecha', $hoy)
            ->first();

        if ($asistenciaExistente) {
            throw new \Exception("Ya existe un registro de asistencia para hoy.");
        }

        $ahora = Carbon::now();
        $horaEntrada = $ahora->toTimeString();
        
        // Calcular tardanza
        $horaInicioTurno = Carbon::parse($hoy . ' ' . $horario->hora_inicio);
        $minutosTardanza = 0;

        if ($ahora->greaterThan($horaInicioTurno)) {
            $diferencia = $horaInicioTurno->diffInMinutes($ahora);
            if ($diferencia > $horario->tolerancia_minutos) {
                $minutosTardanza = $diferencia;
            }
        }

        $estado = $minutosTardanza > 0 ? 'TARDANZA' : 'PRESENTE';

        return Asistencia::create([
            'codigo' => Asistencia::generarCodigo(),
            'user_id' => $userId,
            'horario_id' => $horario->id,
            'fecha' => $hoy,
            'hora_entrada' => $horaEntrada,
            'minutos_tardanza' => $minutosTardanza,
            'estado' => $estado
        ]);
    }

    public function registrarManual(array $data)
    {
        $user = User::findOrFail($data['user_id']);
        $horario = $user->horarioActual();

        if (!$horario) {
            throw new \Exception("El usuario no tiene un horario asignado actualmente.");
        }

        // Si ya existe asistencia para esa fecha, actualizarla o lanzar error
        $asistenciaExistente = Asistencia::where('user_id', $data['user_id'])
            ->where('fecha', $data['fecha'])
            ->first();

        if ($asistenciaExistente) {
            throw new \Exception("Ya existe un registro de asistencia para este empleado en la fecha seleccionada.");
        }

        // Calcular tardanza si es PRESENTE o TARDANZA y se proporciona hora_entrada
        $minutosTardanza = 0;
        if (in_array($data['estado'], ['PRESENTE', 'TARDANZA']) && !empty($data['hora_entrada'])) {
            $horaInicioTurno = Carbon::parse($data['fecha'] . ' ' . $horario->hora_inicio);
            $horaEntrada = Carbon::parse($data['fecha'] . ' ' . $data['hora_entrada']);
            
            if ($horaEntrada->greaterThan($horaInicioTurno)) {
                $diferencia = $horaInicioTurno->diffInMinutes($horaEntrada);
                if ($diferencia > $horario->tolerancia_minutos) {
                    $minutosTardanza = $diferencia;
                }
            }
        }

        return Asistencia::create([
            'codigo' => Asistencia::generarCodigo(),
            'user_id' => $data['user_id'],
            'horario_id' => $horario->id,
            'fecha' => $data['fecha'],
            'hora_entrada' => $data['hora_entrada'] ?? null,
            'estado' => $data['estado'],
            'minutos_tardanza' => $minutosTardanza,
            'observaciones' => $data['observaciones'] ?? null,
        ]);
    }

    public function registrarInicioRefrigerio($id)
    {
        $asistencia = Asistencia::findOrFail($id);
        if ($asistencia->hora_inicio_refrigerio) {
            throw new \Exception("Ya se registró el inicio del refrigerio.");
        }
        $asistencia->update(['hora_inicio_refrigerio' => Carbon::now()->toTimeString()]);
        return $asistencia;
    }

    public function registrarFinRefrigerio($id)
    {
        $asistencia = Asistencia::findOrFail($id);
        if (!$asistencia->hora_inicio_refrigerio) {
            throw new \Exception("Debe registrar el inicio del refrigerio primero.");
        }
        if ($asistencia->hora_fin_refrigerio) {
            throw new \Exception("Ya se registró el fin del refrigerio.");
        }
        $asistencia->update(['hora_fin_refrigerio' => Carbon::now()->toTimeString()]);
        return $asistencia;
    }

    public function registrarSalida($id)
    {
        $asistencia = Asistencia::findOrFail($id);
        if ($asistencia->hora_salida) {
            throw new \Exception("Ya se registró la salida.");
        }

        $ahora = Carbon::now();
        $horaSalida = $ahora->toTimeString();
        $horario = $asistencia->horario;

        // Calcular minutos trabajados (excluyendo refrigerio si hubo)
        $inicio = Carbon::parse($asistencia->fecha . ' ' . $asistencia->hora_entrada);
        $fin = $ahora;
        
        $minutosTotales = $inicio->diffInMinutes($fin);
        $minutosRefrigerio = $asistencia->duracion_refrigerio;
        $minutosTrabajados = $minutosTotales - $minutosRefrigerio;

        // Calcular horas extra
        $horaFinTurno = Carbon::parse($asistencia->fecha . ' ' . $horario->hora_fin);
        $minutosExtra = 0;

        if ($ahora->greaterThan($horaFinTurno)) {
            $minutosExtra = $horaFinTurno->diffInMinutes($ahora);
        }

        $asistencia->update([
            'hora_salida' => $horaSalida,
            'minutos_trabajados' => $minutosTrabajados,
            'minutos_extra' => $minutosExtra
        ]);

        return $asistencia;
    }

    public function getCalendarioEmpleado($userId, $mes, $anio)
    {
        $asistencias = Asistencia::delEmpleado($userId)
            ->delMes($mes, $anio)
            ->with('horario')
            ->get();

        $eventos = [];
        foreach ($asistencias as $asistencia) {
            $label = match ($asistencia->estado) {
                'PRESENTE' => 'Asistio',
                'TARDANZA' => 'Tardanza',
                'FALTA' => 'Falto',
                default => $asistencia->estado,
            };

            // Verificar si el horario tiene refrigerio configurado
            $tieneRefrigerio = false;
            if ($asistencia->horario) {
                $tieneRefrigerio = !empty($asistencia->horario->hora_inicio_refrigerio) && !empty($asistencia->horario->hora_fin_refrigerio);
            }

            $eventos[] = [
                'id' => $asistencia->id,
                'title' => $label,
                'start' => $asistencia->fecha,
                'backgroundColor' => $this->getColorEstado($asistencia->estado),
                'borderColor' => $this->getColorEstado($asistencia->estado),
                'extendedProps' => [
                    'asistencia_id' => $asistencia->id,
                    'entrada' => $asistencia->hora_entrada,
                    'salida' => $asistencia->hora_salida,
                    'inicio_refrigerio' => $asistencia->hora_inicio_refrigerio,
                    'fin_refrigerio' => $asistencia->hora_fin_refrigerio,
                    'tardanza' => $asistencia->minutos_tardanza,
                    'minutos_extra' => $asistencia->minutos_extra,
                    'estado' => $asistencia->estado,
                    'tiene_refrigerio' => $tieneRefrigerio,
                    'observaciones' => $asistencia->observaciones,
                ]
            ];
        }

        return $eventos;
    }

    private function getColorEstado($estado)
    {
        return match ($estado) {
            'PRESENTE' => '#28a745',
            'TARDANZA' => '#ffc107',
            'FALTA' => '#dc3545',
            default => '#6c757d',
        };
    }

    public function getResumenMensual($mes, $anio)
    {
        $diasDelMes = Carbon::create($anio, $mes)->daysInMonth;
        
        $resumen = Asistencia::with(['usuario', 'horario'])
            ->delMes($mes, $anio)
            ->select('user_id', 
                DB::raw('count(*) as total_registros'),
                DB::raw('sum(case when estado = "PRESENTE" or estado = "TARDANZA" then 1 else 0 end) as dias_asistidos'),
                DB::raw('sum(case when estado = "FALTA" then 1 else 0 end) as total_faltas'),
                DB::raw('sum(minutos_tardanza) as total_tardanza'),
                DB::raw('sum(minutos_trabajados) as total_trabajados'),
                DB::raw('sum(minutos_extra) as total_extra')
            )
            ->groupBy('user_id')
            ->get();

        foreach ($resumen as $item) {
            $user = User::find($item->user_id);
            $horario = $user->horarioActual();
            
            if ($horario) {
                $sueldoBase = $horario->sueldo_base;
                $pagoPorDia = $sueldoBase / $diasDelMes;
                $item->descuento_faltas = $pagoPorDia * $item->total_faltas;
                $item->descuento_tardanza = ($item->total_tardanza * $horario->descuento_minuto);
                $item->pago_horas_extra = ($item->total_extra / 60) * $horario->pago_hora_extra;
                $item->sueldo_neto = $sueldoBase - $item->descuento_faltas - $item->descuento_tardanza + $item->pago_horas_extra;
            } else {
                $item->descuento_faltas = 0;
                $item->descuento_tardanza = 0;
                $item->pago_horas_extra = 0;
                $item->sueldo_neto = 0;
            }
        }

        return $resumen;
    }
}
