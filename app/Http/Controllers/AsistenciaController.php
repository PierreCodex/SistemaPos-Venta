<?php

namespace App\Http\Controllers;

use App\Services\AsistenciaService;
use App\Models\User;
use App\Models\Asistencia;
use Illuminate\Http\Request;
use Exception;
use Carbon\Carbon;

class AsistenciaController extends Controller
{
    protected $asistenciaService;

    public function __construct(AsistenciaService $asistenciaService)
    {
        $this->asistenciaService = $asistenciaService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $esAdmin = $user->hasAnyRole(['super-admin', 'Admin', 'administrador']);
        $puedeRegistrar = $user->can('asistencias.registrar') || $esAdmin;

        $filtros = [
            'fecha_inicio' => $request->get('fecha_inicio', Carbon::today()->startOfMonth()->toDateString()),
            'fecha_fin' => $request->get('fecha_fin', Carbon::today()->toDateString()),
            'user_id' => $request->get('user_id')
        ];

        // Si no es admin, forzar filtro a su propio ID
        if (!$esAdmin) {
            $filtros['user_id'] = $user->id;
        }

        $asistencias = $this->asistenciaService->listar($filtros);

        // Solo admins ven la lista completa de usuarios para el filtro/modal
        $usuarios = $esAdmin ? User::all() : collect([$user]);

        return view('asistencias.index', compact('asistencias', 'usuarios', 'filtros', 'esAdmin', 'puedeRegistrar'));
    }

    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $esAdmin = $user->hasAnyRole(['super-admin', 'Admin', 'administrador']);

            if ($request->has('estado')) {
                // Registro manual desde el modal
                $targetUserId = $request->input('user_id');
                
                // Si no es admin:
                // 1. Debe tener permiso 'asistencias.registrar'
                // 2. Solo puede registrarse a sí mismo
                // 3. Forzamos FECHA y HORA actual para evitar trampas
                if (!$esAdmin) {
                    if (!$user->can('asistencias.registrar')) {
                        throw new Exception('No tienes permiso para registrar asistencias.');
                    }
                    if ((int)$targetUserId !== (int)$user->id) {
                        throw new Exception('Solo puedes registrar tu propia asistencia.');
                    }
                    
                    // Sobrescribir datos del request por seguridad
                    $request->merge([
                        'fecha' => Carbon::now()->toDateString(),
                        'hora_entrada' => Carbon::now()->toTimeString()
                    ]);
                }

                $data = $request->validate([
                    'user_id' => 'required|exists:users,id',
                    'estado' => 'required|string',
                    'fecha' => 'required|date',
                    'hora_entrada' => 'required', // Ahora requerida
                    'observaciones' => 'nullable|string'
                ]);

                $this->asistenciaService->registrarManual($data);
                $message = 'Asistencia registrada exitosamente.';
            } else {
                // Registro rápido de entrada
                // Vendedor solo puede registrar su propia entrada
                $userId = $esAdmin ? $request->input('user_id', $user->id) : $user->id;
                $this->asistenciaService->registrarEntrada($userId);
                $message = 'Entrada registrada exitosamente.';
            }

            return response()->json(['success' => true, 'message' => $message]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $user = auth()->user();
        if (!$user->can('asistencias.ver_mio') && !$user->can('asistencias.ver')) {
            abort(403, 'No tienes permiso para ver esta sección.');
        }

        $esAdmin = $user->hasAnyRole(['super-admin', 'Admin', 'administrador']);

        // Si no es admin, solo puede ver su propio calendario
        if (!$esAdmin && (int) $id !== (int) $user->id) {
            abort(403, 'No tienes permiso para ver la asistencia de otro usuario.');
        }

        $usuario = User::findOrFail($id);
        $asistenciaHoy = Asistencia::where('user_id', $id)->where('fecha', Carbon::today()->toDateString())->first();

        return view('asistencias.show', compact('usuario', 'asistenciaHoy', 'esAdmin'));
    }

    public function registrarEvento(Request $request)
    {
        try {
            $user = auth()->user();
            $esAdmin = $user->hasAnyRole(['super-admin', 'Admin', 'administrador']);

            $tipo = $request->tipo;
            $asistenciaId = $request->asistencia_id;

            // Si no es admin, verificar que la asistencia le pertenece
            if (!$esAdmin) {
                $asistencia = Asistencia::findOrFail($asistenciaId);
                if ((int) $asistencia->user_id !== (int) $user->id) {
                    throw new Exception('No tienes permiso para modificar esta asistencia.');
                }
            }

            $asistencia = match ($tipo) {
                'refrigerio_inicio' => $this->asistenciaService->registrarInicioRefrigerio($asistenciaId),
                'refrigerio_fin' => $this->asistenciaService->registrarFinRefrigerio($asistenciaId),
                'salida' => $this->asistenciaService->registrarSalida($asistenciaId),
                default => throw new Exception("Tipo de evento no válido.")
            };

            return response()->json(['success' => true, 'message' => 'Evento registrado exitosamente.', 'asistencia' => $asistencia]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function calendarioData(Request $request, $userId)
    {
        $user = auth()->user();
        $esAdmin = $user->hasAnyRole(['super-admin', 'Admin', 'administrador']);

        // Si no es admin, solo puede consultar sus propios datos
        if (!$esAdmin && (int) $userId !== (int) $user->id) {
            return response()->json([], 403);
        }

        $mes = $request->get('month', Carbon::now()->month);
        $anio = $request->get('year', Carbon::now()->year);

        $eventos = $this->asistenciaService->getCalendarioEmpleado($userId, $mes, $anio);
        return response()->json($eventos);
    }

    public function generarNomina(Request $request)
    {
        $mes = $request->get('mes', Carbon::now()->month);
        $anio = $request->get('anio', Carbon::now()->year);

        $resumen = $this->asistenciaService->getResumenMensual($mes, $anio);

        return response()->json(['data' => $resumen]);
    }
}
