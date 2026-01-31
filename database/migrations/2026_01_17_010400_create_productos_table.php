<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================
 * 🎓 TUTORIAL: Migración de Productos (TABLA PRINCIPAL)
 * =====================================================
 * 
 * Esta es la tabla más importante del sistema POS.
 * Tiene relaciones con: categorias, marcas, unidades, proveedores
 * 
 * 💡 CONCEPTOS NUEVOS:
 * - foreignId() = Crea un campo + llave foránea automáticamente
 * - constrained() = Indica la tabla a la que se relaciona
 * - decimal(10,2) = Número con 10 dígitos, 2 decimales (para precios)
 * 
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
   Schema::create('productos', function (Blueprint $table) {
    $table->id();
    
    // 🏷️ Identificadores
    $table->string('codigo', 50)->unique()->comment('Código SKU interno');
    $table->string('codigo_barras', 50)->nullable()->index()->comment('EAN-13, UPC, etc.');
    
    // 📝 Información básica
    $table->string('nombre', 200)->index();
    $table->text('descripcion')->nullable();
    
    // 🔗 Relaciones (Ya las tienes perfectas)
    $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
    $table->foreignId('marca_id')->nullable()->constrained('marcas')->nullOnDelete();
    $table->foreignId('unidad_id')->nullable()->constrained('unidades')->nullOnDelete();
    $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
    
    // 💰 Precios (decimal 10,2 es correcto para moneda)
    $table->decimal('precio_compra', 10, 2)->default(0.00);
    $table->decimal('precio_venta', 10, 2)->default(0.00);
    
    // 📦 Inventario (Cambiado a decimal para soportar pesables como KG)
    $table->decimal('stock', 12, 3)->default(0.000);
    $table->decimal('stock_minimo', 12, 3)->default(5.000);
    
    // ⚙️ Switches de Lógica de Negocio
    $table->boolean('aplica_igv')->default(true)->comment('Si el producto está gravado');
    $table->boolean('es_servicio')->default(false)->comment('Si es true, no afecta stock');
    $table->boolean('permite_venta_negativa')->default(false);
    
    // 🖼️ Multimedia (Máx 10MB manejado en Service/Request)
    $table->string('imagen', 255)->nullable();
    $table->string('imagen_url', 500)->nullable();
    
    // 📍 Atributos adicionales
    $table->string('ubicacion', 100)->nullable();
    $table->string('material', 100)->nullable();
    $table->date('fecha_vencimiento')->nullable();
    
    // ✅ Estado y Control
    $table->boolean('estado')->default(true);
    $table->timestamps();
    $table->softDeletes(); // Para no borrar datos contables accidentalmente
});
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
