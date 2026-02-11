<?php

namespace App\Services;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Kardex;
use App\Services\CajaService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Servicio para la lógica de negocio de Ventas.
 * 
 * Maneja la creación, anulación y consulta de ventas,
 * incluyendo el descuento de stock, registro en Kardex,
 * y generación de comprobantes electrónicos.
 * 
 * @package App\Services
 */
class VentaService
{
    protected ComprobanteElectronicoService $comprobanteService;
    protected CajaService $cajaService;

    /**
     * Roles que pueden ver todas las ventas
     */
    protected array $rolesAdministrativos = ['super-admin', 'administrador', 'Admin'];

    public function __construct(ComprobanteElectronicoService $comprobanteService, CajaService $cajaService)
    {
        $this->comprobanteService = $comprobanteService;
        $this->cajaService = $cajaService;
    }

    /**
     * Verifica si el usuario actual tiene rol administrativo
     */
    public function esAdministrador(): bool
    {
        $user = Auth::user();
        return $user ? $user->hasAnyRole($this->rolesAdministrativos) : false;
    }

    /**
     * Aplica filtro de usuario a una query si no es administrador
     */
    protected function aplicarFiltroUsuario($query)
    {
        if (!$this->esAdministrador()) {
            $query->where('user_id', Auth::id());
        }
        return $query;
    }

    /**
     * Obtiene todas las ventas con relaciones
     * Filtrado por usuario según rol
     */
    public function obtenerTodas(): Collection
    {
        $query = Venta::with(['cliente', 'vendedor', 'detalles.producto'])
            ->orderBy('fecha_emision', 'desc');
        
        $this->aplicarFiltroUsuario($query);
        
        return $query->get();
    }

    /**
     * Obtiene ventas filtradas por rango de fechas
     * Filtrado por usuario según rol
     */
    public function obtenerPorFechas(string $fechaInicio, string $fechaFin): Collection
    {
        $query = Venta::with(['cliente', 'vendedor'])
            ->whereBetween('fecha_emision', [
                Carbon::parse($fechaInicio)->startOfDay(),
                Carbon::parse($fechaFin)->endOfDay()
            ])
            ->orderBy('fecha_emision', 'desc');
        
        $this->aplicarFiltroUsuario($query);
        
        return $query->get();
    }

    /**
     * Obtiene las ventas de hoy
     * Filtrado por usuario según rol
     */
    public function obtenerVentasHoy(): Collection
    {
        $query = Venta::with(['cliente', 'vendedor'])
            ->whereDate('fecha_emision', today())
            ->orderBy('fecha_emision', 'desc');
        
        $this->aplicarFiltroUsuario($query);
        
        return $query->get();
    }

    /**
     * Busca una venta por ID con todos sus detalles
     */
    public function buscarPorId(int $id): Venta
    {
        return Venta::with(['cliente', 'vendedor', 'detalles.producto'])
            ->findOrFail($id);
    }

    /**
     * Crea una nueva venta completa
     * 
     * @param array $datosVenta Datos de la cabecera de venta
     * @param array $detalles Array de productos [{producto_id, cantidad, precio_unitario, descuento}]
     * @return Venta
     */
    public function crear(array $datosVenta, array $detalles): Venta
    {
        return DB::transaction(function () use ($datosVenta, $detalles) {
            // 0. Validar que haya caja abierta
            $this->cajaService->validarCajaParaVenta();
            $cajaSesionId = $this->cajaService->getCajaAbiertaId();
            
            // 1. Gestión de clientes (venta anónima, registro automático, validación crédito)
            $datosVenta = $this->gestionarCliente($datosVenta);
            
            // 2. Generar número correlativo
            $datosVenta['numero'] = $this->generarNumeroCorrelativo($datosVenta['serie'] ?? 'B001');
            $datosVenta['fecha_emision'] = now();
            $datosVenta['user_id'] = Auth::id();
            $datosVenta['caja_sesion_id'] = $cajaSesionId;

            // 2. Calcular totales
            $totales = $this->calcularTotales($detalles, $datosVenta['descuento'] ?? 0);
            $datosVenta = array_merge($datosVenta, $totales);

            // 3. Ajustar estado y saldos si es crédito
            if (!isset($datosVenta['es_credito']) || !$datosVenta['es_credito']) {
                $montoRecibido = $datosVenta['monto_recibido'] ?? $datosVenta['total'];
                $datosVenta['vuelto'] = max(0, $montoRecibido - $datosVenta['total']);
                $datosVenta['saldo_pendiente'] = 0;
                $datosVenta['estado_pago'] = 'PAGADO';
            } else {
                $datosVenta['vuelto'] = 0;
                $datosVenta['estado_pago'] = $datosVenta['estado_pago'] ?? 'PENDIENTE';
            }

            // 4. Crear la venta
            $venta = Venta::create($datosVenta);

            // 5. Crear detalles y descontar stock
            foreach ($detalles as $detalle) {
                $this->agregarDetalle($venta, $detalle);
            }

            // 6. Generar comprobante electrónico si corresponde
            if (in_array($datosVenta['comprobante'], ['FACTURA', 'BOLETA', 'TICKET'])) {
                try {
                    $tipoComprobante = $datosVenta['comprobante'] === 'TICKET' ? 'NOTA_VENTA' : $datosVenta['comprobante'];
                    $this->comprobanteService->generarDesdeVenta($venta, $tipoComprobante);
                    
                    Log::info('Comprobante generado para venta', [
                        'venta_id' => $venta->id,
                        'tipo_comprobante' => $tipoComprobante
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error al generar comprobante electrónico', [
                        'venta_id' => $venta->id,
                        'error' => $e->getMessage()
                    ]);
                    // No lanzar excepción - la venta ya está creada
                    // El comprobante se puede regenerar después
                }
            }

            // 7. Registrar movimiento de caja (si hay pago en efectivo)
            $this->cajaService->registrarVenta($venta);

            return $venta->fresh(['cliente', 'vendedor', 'detalles.producto', 'comprobanteElectronico']);
        });
    }

    /**
     * Agrega un detalle a la venta y descuenta stock
     */
    private function agregarDetalle(Venta $venta, array $detalle): DetalleVenta
    {
        $producto = Producto::with('unidad')->findOrFail($detalle['producto_id']);

        // Validación de decimales según unidad de medida
        $permiteDecimales = $producto->unidad->permite_decimales ?? false;
        $cantidad = $detalle['cantidad'];

        if (!$permiteDecimales && floor($cantidad) != $cantidad) {
            throw new \Exception("El producto '{$producto->nombre}' solo permite cantidades enteras.");
        }

        // Calcular subtotal del item
        $precioUnitario = $detalle['precio_unitario'] ?? $producto->precio_venta;
        $cantidad = $detalle['cantidad'];
        $descuento = $detalle['descuento'] ?? 0;
        $subtotal = ($cantidad * $precioUnitario) - $descuento;

        // Crear detalle
        $detalleVenta = DetalleVenta::create([
            'venta_id' => $venta->id,
            'producto_id' => $producto->id,
            'cantidad' => $cantidad,
            'precio_unitario' => $precioUnitario,
            'precio_original' => $producto->precio_venta,
            'descuento' => $descuento,
            'subtotal' => $subtotal
        ]);

        // Descontar stock
        $stockAnterior = $producto->stock;
        $producto->stock -= $cantidad;
        $producto->save();

        // Registrar en Kardex
        Kardex::create([
            'producto_id' => $producto->id,
            'tipo_movimiento' => 'VENTA',
            'cantidad' => -$cantidad, // Negativo porque es salida
            'stock_anterior' => $stockAnterior,
            'stock_resultante' => $producto->stock,
            'user_id' => Auth::id(),
            'referencia_tipo' => 'ventas',
            'referencia_id' => $venta->id,
            'observaciones' => "Venta {$venta->serie}-{$venta->numero}"
        ]);

        return $detalleVenta;
    }

    /**
     * Calcula los totales de la venta
     */
    private function calcularTotales(array $detalles, float $descuentoGeneral = 0): array
    {
        $subtotalBruto = 0;

        foreach ($detalles as $detalle) {
            $precio = $detalle['precio_unitario'];
            $cantidad = $detalle['cantidad'];
            $descuento = $detalle['descuento'] ?? 0;
            $subtotalBruto += ($cantidad * $precio) - $descuento;
        }

        // Aplicar descuento general
        $subtotalConDescuento = $subtotalBruto - $descuentoGeneral;

        // Calcular IGV (18% en Perú)
        $igvPorcentaje = 18.00;
        $baseImponible = $subtotalConDescuento / (1 + ($igvPorcentaje / 100));
        $igvMonto = $subtotalConDescuento - $baseImponible;

        return [
            'subtotal' => round($baseImponible, 2),
            'igv_porcentaje' => $igvPorcentaje,
            'igv_monto' => round($igvMonto, 2),
            'descuento' => $descuentoGeneral,
            'total' => round($subtotalConDescuento, 2)
        ];
    }

    /**
     * Genera el número correlativo para un comprobante
     */
    private function generarNumeroCorrelativo(string $serie): string
    {
        $ultimoNumero = Venta::where('serie', $serie)
            ->max('numero');

        $siguiente = $ultimoNumero ? ((int) $ultimoNumero + 1) : 1;

        return str_pad($siguiente, 8, '0', STR_PAD_LEFT);
    }

    /**
     * Anula una venta y revierte el stock
     */
    public function anular(int $id, string $motivo): Venta
    {
        return DB::transaction(function () use ($id, $motivo) {
            $venta = Venta::with('detalles.producto')->findOrFail($id);

            if ($venta->estado === 'ANULADA') {
                throw new \Exception('Esta venta ya está anulada');
            }

            // Revertir stock de cada producto
            foreach ($venta->detalles as $detalle) {
                $producto = $detalle->producto;
                $stockAnterior = $producto->stock;
                $producto->stock += $detalle->cantidad;
                $producto->save();

                // Registrar devolución en Kardex
                Kardex::create([
                    'producto_id' => $producto->id,
                    'tipo_movimiento' => 'ANULACION_VENTA',
                    'cantidad' => $detalle->cantidad, // Positivo porque vuelve al stock
                    'stock_anterior' => $stockAnterior,
                    'stock_resultante' => $producto->stock,
                    'user_id' => Auth::id(),
                    'referencia_tipo' => 'ventas',
                    'referencia_id' => $venta->id,
                    'observaciones' => "Anulación de venta {$venta->serie}-{$venta->numero}"
                ]);
            }

            // Actualizar estado de la venta
            $venta->update([
                'estado' => 'ANULADA',
                'motivo_anulacion' => $motivo,
                'fecha_anulacion' => now(),
                'user_anulacion_id' => Auth::id()
            ]);

            return $venta->fresh();
        });
    }

    /**
     * Obtiene estadísticas de ventas del día
     * Filtrado por usuario según rol
     */
    public function obtenerEstadisticasHoy(): array
    {
        $hoy = today();

        $queryEmitidas = Venta::whereDate('fecha_emision', $hoy)
            ->where('estado', 'COMPLETADA');
        $this->aplicarFiltroUsuario($queryEmitidas);
        $emitidas = $queryEmitidas->selectRaw('COUNT(*) as cantidad, COALESCE(SUM(total), 0) as total')
            ->first();

        $queryAnuladas = Venta::whereDate('fecha_emision', $hoy)
            ->where('estado', 'ANULADA');
        $this->aplicarFiltroUsuario($queryAnuladas);
        $anuladas = $queryAnuladas->selectRaw('COUNT(*) as cantidad, COALESCE(SUM(total), 0) as total')
            ->first();

        return [
            'emitidas' => [
                'cantidad' => $emitidas->cantidad ?? 0,
                'total' => $emitidas->total ?? 0
            ],
            'anuladas' => [
                'cantidad' => $anuladas->cantidad ?? 0,
                'total' => $anuladas->total ?? 0
            ]
        ];
    }

    /**
     * Obtiene productos activos para el POS
     */
    public function obtenerProductosParaPOS(): Collection
    {
        return Producto::with(['categoria', 'unidad'])
            ->where('estado', true)
            ->where('stock', '>', 0)
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Obtiene categorías activas para filtrar en el POS
     */
    public function obtenerCategoriasActivas(): Collection
    {
        return \App\Models\Categoria::where('estado', true)
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Busca productos por nombre o código de barras
     */
    public function buscarProducto(string $termino): Collection
    {
        return Producto::with(['categoria', 'unidad'])
            ->where('estado', true)
            ->where(function ($query) use ($termino) {
                $query->where('nombre', 'like', "%{$termino}%")
                    ->orWhere('codigo', 'like', "%{$termino}%")
                    ->orWhere('codigo_barras', 'like', "%{$termino}%");
            })
            ->limit(20)
            ->get();
    }

    /**
     * Obtiene un producto por código de barras (para el escáner)
     */
    public function buscarPorCodigoBarras(string $codigo): ?Producto
    {
        return Producto::with(['categoria', 'unidad'])
            ->where('estado', true)
            ->where(function ($query) use ($codigo) {
                $query->where('codigo_barras', $codigo)
                    ->orWhere('codigo', $codigo);
            })
            ->first();
    }

    /**
     * Busca clientes por nombre o documento
     */
    public function buscarCliente(string $termino): Collection
    {
        return \App\Models\Cliente::activos()
            ->where(function ($query) use ($termino) {
                $query->where('nombre', 'like', "%{$termino}%")
                    ->orWhere('numero_documento', 'like', "%{$termino}%");
            })
            ->limit(10)
            ->get();
    }

    /**
     * Gestiona la asignación de cliente para la venta
     * - Venta anónima: asigna cliente genérico (DNI 00000000)
     * - Registro automático: crea cliente si no existe
     * - Validación de crédito: no permite fiar al cliente genérico
     */
    private function gestionarCliente(array $datosVenta): array
    {
        try {
            // Obtener cliente genérico
            $clienteGenerico = Cliente::where('numero_documento', '00000000')->first();
            
            if (!$clienteGenerico) {
                // Si no existe, crearlo para evitar fallos
                $clienteGenerico = Cliente::create([
                    'nombre' => 'CLIENTE GENERAL',
                    'tipo_documento' => 'DNI',
                    'numero_documento' => '00000000',
                    'direccion' => '-',
                    'estado' => true
                ]);
            }

            // 1. Venta anónima (sin nombre cliente generico y sin cliente_id)
            if (empty($datosVenta['cliente_id']) && empty($datosVenta['nombre_cliente_generico'])) {
                $datosVenta['cliente_id'] = $clienteGenerico->id;
                $datosVenta['nombre_cliente_generico'] = null;
                
                Log::info('Venta anónima asignada a cliente genérico', ['cliente_id' => $clienteGenerico->id]);
            }

            // 2. Registro automático / CLIENTE NUEVO (nombre nuevo + sin ID)
            if (!empty($datosVenta['nombre_cliente_generico']) && empty($datosVenta['cliente_id'])) {
                $nombreCliente = trim($datosVenta['nombre_cliente_generico']);
                
                // Intentar buscar por nombre primero
                $cliente = Cliente::where('nombre', $nombreCliente)->first();
                
                if (!$cliente) {
                    // Si no existe, crear con un número de documento temporal único
                    $numDocTemp = 'TEMP' . time() . rand(10, 99);
                    
                    $cliente = Cliente::create([
                        'nombre' => $nombreCliente,
                        'tipo_documento' => 'DNI',
                        'numero_documento' => $numDocTemp,
                        'direccion' => '-',
                        'telefono' => '-',
                        'email' => '-',
                        'estado' => true
                    ]);
                    
                    Log::info('Nuevo cliente registrado en venta', [
                        'cliente_id' => $cliente->id,
                        'nombre' => $nombreCliente,
                        'doc' => $numDocTemp
                    ]);
                } else {
                    Log::info('Cliente existente encontrado por nombre', ['cliente_id' => $cliente->id]);
                }
                
                $datosVenta['cliente_id'] = $cliente->id;
            }

            // 3. Validación de crédito
            $esCredito = $datosVenta['es_credito'] ?? false;
            if ($esCredito && $datosVenta['cliente_id'] == $clienteGenerico->id) {
                throw new \Exception('No se puede realizar una venta a crédito al cliente genérico. Debe especificar un nombre de cliente.');
            }

            return $datosVenta;

        } catch (\Exception $e) {
            Log::error('Error en gestionarCliente: ' . $e->getMessage());
            throw $e;
        }
    }
}
