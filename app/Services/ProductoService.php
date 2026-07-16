<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\ProductoPresentacion;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Unidad;
use App\Models\Proveedor;
use App\Models\Kardex;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;

/**
 * Servicio para la lógica de negocio de Marcas.
 * 
 * @package App\Services
 */
class ProductoService
{
    public function obtenerTodos(): Collection
    {
        return Producto::with(['categoria', 'marca', 'unidad'])
                        ->orderBy('created_at', 'desc')
                        ->get();
    }

    public function buscarPorId(int $id): Producto
    {
        return Producto::with(['categoria', 'marca', 'unidad', 'proveedor'])->findOrFail($id);
    }

    /**
     * Crea un producto, procesa imagen y registra movimiento inicial en Kardex.
     */
    public function crear(array $datos): Producto
    {
        return DB::transaction(function () use ($datos) {
            // 1. Generar SKU automático si es necesario
            $datos['codigo'] = $datos['codigo'] ?? 'PROD-' . strtoupper(Str::random(8));

            // 2. Procesar Imagen de 10MB
            if (isset($datos['imagen'])) {
                $datos['imagen'] = $this->procesarYSubirImagen($datos['imagen']);
            }

            // 3. Crear Producto con Stock Inicial (siempre en unidad base)
            $datos['stock'] = $datos['stock_inicial'];
            $producto = Producto::create($datos);

            // 4. Presentación base: sin ella el producto no puede operar stock.
            $presentacionBase = $this->crearPresentacionBase($producto);

            // 5. Registro Obligatorio en KARDEX
            Kardex::create([
                'producto_id'      => $producto->id,
                'presentacion_id'  => $presentacionBase->id,
                'tipo_movimiento'  => 'INVENTARIO_INICIAL',
                'cantidad'         => $datos['stock_inicial'],
                'cantidad_presentacion' => $datos['stock_inicial'],
                'stock_anterior'   => 0,
                'stock_resultante' => $datos['stock_inicial'],
                'user_id'          => auth()->id(),
                'observaciones'    => 'Registro inicial del producto'
            ]);

            $producto->load(['categoria', 'marca', 'unidad']);
            return $producto->fresh(['categoria', 'marca', 'unidad', 'presentacionBase']);
        });
    }


    /**
     * Crea la presentación base (factor 1) de un producto recién creado.
     *
     * Todo producto necesita una: es la presentación en la que se lleva el
     * stock y la que se usa cuando una venta no especifica cuál.
     */
    private function crearPresentacionBase(Producto $producto): ProductoPresentacion
    {
        return $producto->presentaciones()->create([
            'unidad_id'    => $producto->unidad_id ?? $this->unidadPorDefectoId(),
            'factor'       => 1,
            'precio_venta' => $producto->precio_venta ?? 0,
            'es_base'      => true,
            'estado'       => true,
        ]);
    }

    /**
     * Unidad para productos que se crean sin unidad asignada.
     * Un producto sin unidad es implícitamente "unidades", y la
     * presentación base necesita una unidad para poder convertir.
     */
    private function unidadPorDefectoId(): int
    {
        return Unidad::firstOrCreate(
            ['codigo' => 'UND'],
            ['nombre' => 'Unidades', 'permite_decimales' => false, 'estado' => true]
        )->id;
    }

    /**
     * Mantiene la presentación base alineada con el producto.
     *
     * Sin esto, cambiar la unidad o el precio del producto dejaría la base
     * apuntando a la unidad vieja y el POS vendería con el precio anterior.
     */
    private function sincronizarPresentacionBase(Producto $producto, array $datos): void
    {
        $base = $producto->presentacionBase()->first();

        if (!$base) {
            $this->crearPresentacionBase($producto);
            return;
        }

        $cambios = [];

        if (array_key_exists('unidad_id', $datos) && $datos['unidad_id'] != $base->unidad_id) {
            $cambios['unidad_id'] = $datos['unidad_id'] ?? $this->unidadPorDefectoId();
        }

        if (array_key_exists('precio_venta', $datos) && (float) $datos['precio_venta'] != (float) $base->precio_venta) {
            $cambios['precio_venta'] = $datos['precio_venta'];
        }

        if ($cambios) {
            $base->update($cambios);
        }
    }

    /**
     * Procesa imágenes pesadas para optimizar almacenamiento
     */
    private function procesarYSubirImagen($file): string
    {
        $nombreImagen = time() . '_' . Str::slug($file->getClientOriginalName()) . '.webp';
        
        // Crear carpeta si no existe
        if (!Storage::disk('public')->exists('productos')) {
            Storage::disk('public')->makeDirectory('productos');
        }

        $rutaDestino = storage_path('app/public/productos/' . $nombreImagen);

        // API v3 de Intervention Image
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);
        
        // Redimensionar si es necesario (ej: ancho máximo 800)
        $image->scale(width: 800);
        
        // Codificar y guardar
        $image->toWebp(75)->save($rutaDestino);

        return $nombreImagen;
    }
    
    public function actualizar(int $id, array $datos): Producto
    {
        return DB::transaction(function () use ($id, $datos) {
            $producto = Producto::findOrFail($id);

            if (isset($datos['imagen'])) {
                // Borrar imagen anterior si existe
                if ($producto->imagen) {
                    Storage::disk('public')->delete('productos/' . $producto->imagen);
                }
                $datos['imagen'] = $this->subirImagen($datos['imagen']);
            }

            $producto->update($datos);

            $this->sincronizarPresentacionBase($producto, $datos);

            return $producto->fresh(['categoria', 'marca', 'unidad', 'presentacionBase']);
        });
    }

    public function eliminar(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $producto = Producto::findOrFail($id);
            
            // Al eliminar, también lo desactivamos por coherencia
            $producto->update(['estado' => 0]);
            
            return $producto->delete();
        });
    }

    /**
     * Lógica para procesar la imagen
     */
    private function subirImagen($file): string
    {
        $nombreImagen = time() . '_' . $file->getClientOriginalName();
        // Aquí podrías usar Intervention Image para redimensionar antes de guardar
        $file->storeAs('productos', $nombreImagen, 'public');
        return $nombreImagen;
    }

    /**
     * Métodos para llenar los combos del modal
     */
    public function obtenerCategoriasParaCombo(): Collection {
        return Categoria::where('estado', true)->orderBy('nombre')->get();
    }

    public function obtenerMarcasParaCombo(): Collection {
        return Marca::where('estado', true)->orderBy('nombre')->get();
    }

    public function obtenerUnidadesParaCombo(): Collection {
        return Unidad::where('estado', true)->orderBy('nombre')->get();
    }

    public function obtenerProveedoresParaCombo(): Collection {
        return Proveedor::where('estado', true)->orderBy('nombre')->get();
    }

    public function toggleEstado(int $id): Producto
    {
        $producto = Producto::findOrFail($id);
        $producto->estado = !$producto->estado;
        $producto->save();
        return $producto;
    }
}
