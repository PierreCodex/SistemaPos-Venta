<?php

namespace App\Http\Controllers;

use App\Services\VentaService;
use App\Services\PdfComprobanteService;
use App\Http\Requests\StoreVentaRequest;
use App\Models\Venta;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Controlador para gestionar las ventas (POS)
 * 
 * Maneja tanto las vistas como las operaciones AJAX
 * para el punto de venta.
 */
class VentaController extends Controller
{
    public function __construct(
        private VentaService $ventaService
    ) {}

    /**
     * Muestra el listado de ventas
     */
    public function index(Request $request): View|JsonResponse
    {
        $esAdmin = $this->ventaService->esAdministrador();

        // Si es AJAX, retornar JSON
        if ($request->ajax()) {
            $ventas = $this->ventaService->obtenerTodas();
            $estadisticas = $this->ventaService->obtenerEstadisticasHoy();
            
            return response()->json([
                'success' => true,
                'ventas' => $ventas,
                'estadisticas' => $estadisticas
            ]);
        }

        // Vista normal
        $fechaInicio = $request->input('fecha_inicio', now()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));

        $ventas = $this->ventaService->obtenerPorFechas($fechaInicio, $fechaFin);
        $estadisticas = $this->ventaService->obtenerEstadisticasHoy();

        return view('ventas.index', compact('ventas', 'estadisticas', 'fechaInicio', 'fechaFin', 'esAdmin'));
    }

    /**
     * Muestra el formulario de creación (POS)
     */
    public function create(): View
    {
        $productos = $this->ventaService->obtenerProductosParaPOS();
        $categorias = $this->ventaService->obtenerCategoriasActivas();

        return view('ventas.create', compact('productos', 'categorias'));
    }

    /**
     * Almacena una nueva venta
     */
    public function store(StoreVentaRequest $request): JsonResponse
    {
        try {
            $datosVenta = $request->except('detalles');
            $detalles = $request->input('detalles');

            $venta = $this->ventaService->crear($datosVenta, $detalles);

            // Preparar URLs de descarga de PDFs si hay comprobante electrónico
            $pdfUrls = null;
            if ($venta->comprobanteElectronico) {
                $comprobante = $venta->comprobanteElectronico;
                $pdfUrls = [
                    '50mm' => route('ventas.descargar-pdf', ['id' => $venta->id, 'formato' => '50mm']),
                    '80mm' => route('ventas.descargar-pdf', ['id' => $venta->id, 'formato' => '80mm']),
                    'a4' => route('ventas.descargar-pdf', ['id' => $venta->id, 'formato' => 'a4']),
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Venta registrada correctamente',
                'venta' => $venta,
                'comprobante' => $venta->comprobante_completo,
                'pdf_urls' => $pdfUrls,
                'tiene_comprobante' => $venta->comprobanteElectronico !== null,
                'tipo_comprobante' => $venta->comprobanteElectronico?->tipo_comprobante_nombre ?? null
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la venta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra los detalles de una venta
     * Verifica que el usuario tenga acceso a esta venta
     */
    public function show(int $id): JsonResponse
    {
        try {
            $venta = $this->ventaService->buscarPorId($id);

            // Verificar acceso: solo admin o dueño de la venta
            if (!$this->ventaService->esAdministrador() && $venta->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permiso para ver esta venta'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'venta' => $venta
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Venta no encontrada'
            ], 404);
        }
    }

    /**
     * Anula una venta
     * Verifica que el usuario tenga acceso a esta venta
     */
    public function destroy(int $id, Request $request): JsonResponse
    {
        try {
            // Primero verificar acceso
            $venta = $this->ventaService->buscarPorId($id);
            
            if (!$this->ventaService->esAdministrador() && $venta->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permiso para anular esta venta'
                ], 403);
            }

            $motivo = $request->input('motivo', 'Anulación solicitada por usuario');
            $venta = $this->ventaService->anular($id, $motivo);

            return response()->json([
                'success' => true,
                'message' => 'Venta anulada correctamente',
                'venta' => $venta
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // =====================================================
    // 🔌 API ENDPOINTS PARA EL POS
    // =====================================================

    /**
     * Busca productos para el POS
     */
    public function buscarProducto(Request $request): JsonResponse
    {
        $termino = $request->input('q', '');

        if (strlen($termino) < 2) {
            return response()->json([
                'success' => true,
                'productos' => []
            ]);
        }

        $productos = $this->ventaService->buscarProducto($termino);

        return response()->json([
            'success' => true,
            'productos' => $productos
        ]);
    }

    /**
     * Busca clientes para el POS
     */
    public function buscarCliente(Request $request): JsonResponse
    {
        $termino = $request->input('q', '');

        if (strlen($termino) < 2) {
            return response()->json([
                'success' => true,
                'clientes' => []
            ]);
        }

        $clientes = $this->ventaService->buscarCliente($termino);

        return response()->json([
            'success' => true,
            'clientes' => $clientes
        ]);
    }

    /**
     * Busca producto por código de barras
     */
    public function buscarPorCodigoBarras(Request $request): JsonResponse
    {
        $codigo = $request->input('codigo', '');

        $producto = $this->ventaService->buscarPorCodigoBarras($codigo);

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'producto' => $producto
        ]);
    }

    /**
     * Obtiene productos por categoría
     */
    public function productosPorCategoria(int $categoriaId): JsonResponse
    {
        $productos = \App\Models\Producto::with(['categoria', 'unidad'])
            ->where('estado', true)
            ->where('stock', '>', 0)
            ->where('categoria_id', $categoriaId)
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'success' => true,
            'productos' => $productos
        ]);
    }

    /**
     * Filtra ventas por rango de fechas
     */
    public function filtrarPorFechas(Request $request): JsonResponse
    {
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        if (!$fechaInicio || !$fechaFin) {
            return response()->json([
                'success' => false,
                'message' => 'Debe especificar ambas fechas'
            ], 400);
        }

        $ventas = $this->ventaService->obtenerPorFechas($fechaInicio, $fechaFin);

        return response()->json([
            'success' => true,
            'ventas' => $ventas
        ]);
    }

    /**
     * Descarga el PDF del comprobante en el formato especificado
     */
    public function descargarPdf(int $id, string $formato = '80mm')
    {
        try {
            $venta = Venta::with('comprobanteElectronico')->findOrFail($id);

            if (!$venta->comprobanteElectronico) {
                abort(404, 'Esta venta no tiene comprobante electrónico');
            }

            $comprobante = $venta->comprobanteElectronico;
            
            // Construir la ruta del PDF según el formato
            $filename = $comprobante->serie . '-' . $comprobante->numero . '-' . $formato . '.pdf';
            $path = "greenter/pdf/{$formato}/{$filename}";

            if (!\Illuminate\Support\Facades\Storage::exists($path)) {
                // Si no existe, generar el PDF
                $pdfService = app(\App\Services\PdfComprobanteService::class);
                $pdfService->generarPdf($comprobante, $formato);
            }

            // Descargar el PDF
            return \Illuminate\Support\Facades\Storage::download($path, $filename);

        } catch (\Exception $e) {
            abort(500, 'Error al descargar el PDF: ' . $e->getMessage());
        }
    }
}
