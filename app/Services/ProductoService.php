<?php

namespace App\Services;

use App\Models\Producto;
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

            // 3. Crear Producto con Stock Inicial
            $datos['stock'] = $datos['stock_inicial'];
            $producto = Producto::create($datos);

            // 4. Registro Obligatorio en KARDEX
            Kardex::create([
                'producto_id'      => $producto->id,
                'tipo_movimiento'  => 'INVENTARIO_INICIAL',
                'cantidad'         => $datos['stock_inicial'],
                'stock_anterior'   => 0,
                'stock_resultante' => $datos['stock_inicial'],
                'user_id'          => auth()->id(),
                'observaciones'    => 'Registro inicial del producto'
            ]);

            $producto->load(['categoria', 'marca', 'unidad']);
            return $producto->fresh(['categoria', 'marca', 'unidad']);
        });
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
            return $producto->fresh(['categoria', 'marca', 'unidad']);
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
