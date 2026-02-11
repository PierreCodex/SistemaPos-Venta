<?php

namespace App\Http\Controllers;

use App\Services\HorarioService;
use Illuminate\Http\Request;
use Exception;

class HorarioController extends Controller
{
    protected $horarioService;

    public function __construct(HorarioService $horarioService)
    {
        $this->horarioService = $horarioService;
    }

    public function index()
    {
        $horarios = $this->horarioService->listar();
        $usuariosDisponibles = $this->horarioService->getUsuariosDisponibles();
        return view('horarios.index', compact('horarios', 'usuariosDisponibles'));
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'nombre' => 'required|string|max:100',
                'hora_inicio' => 'required',
                'hora_fin' => 'required',
                'dias_laborales' => 'required|array',
                'tolerancia_minutos' => 'required|integer|min:0',
                'sueldo_base' => 'required|numeric|min:0',
                'descuento_falta' => 'required|numeric|min:0',
                'descuento_minuto' => 'required|numeric|min:0',
                'pago_hora_extra' => 'required|numeric|min:0',
                'hora_inicio_refrigerio' => 'nullable',
                'hora_fin_refrigerio' => 'nullable',
            ]);

            $this->horarioService->crear($data);

            return response()->json(['success' => true, 'message' => 'Horario creado exitosamente.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear horario: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'nombre' => 'required|string|max:100',
                'hora_inicio' => 'required',
                'hora_fin' => 'required',
                'dias_laborales' => 'required|array',
                'tolerancia_minutos' => 'required|integer|min:0',
                'sueldo_base' => 'required|numeric|min:0',
                'descuento_falta' => 'required|numeric|min:0',
                'descuento_minuto' => 'required|numeric|min:0',
                'pago_hora_extra' => 'required|numeric|min:0',
                'hora_inicio_refrigerio' => 'nullable',
                'hora_fin_refrigerio' => 'nullable',
            ]);

            $this->horarioService->actualizar($id, $data);

            return response()->json(['success' => true, 'message' => 'Horario actualizado exitosamente.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar horario: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->horarioService->eliminar($id);
            return response()->json(['success' => true, 'message' => 'Horario eliminado exitosamente.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar horario: ' . $e->getMessage()], 500);
        }
    }

    public function toggleEstado($id)
    {
        try {
            $this->horarioService->toggleEstado($id);
            return response()->json(['success' => true, 'message' => 'Estado actualizado exitosamente.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar estado: ' . $e->getMessage()], 500);
        }
    }

    public function asignarUsuarios(Request $request)
    {
        try {
            $request->validate([
                'horario_id' => 'required|exists:horarios,id',
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id'
            ]);

            $this->horarioService->asignarUsuarios($request->horario_id, $request->user_ids);

            return response()->json(['success' => true, 'message' => 'Usuarios asignados exitosamente.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al asignar usuarios: ' . $e->getMessage()], 500);
        }
    }

    public function getUsuariosAsignados($id)
    {
        $usuarios = $this->horarioService->getUsuariosAsignados($id);
        return response()->json($usuarios);
    }
}
