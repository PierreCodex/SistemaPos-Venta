# 📚 Documentación CRUD - Sistema POS

> Esta documentación explica la arquitectura y estructura del CRUD implementado.
> Úsala como guía para crear nuevos módulos.

---

## 📁 Estructura de Archivos por Módulo

```
Para cada módulo (ejemplo: Marcas) necesitas crear:

app/
├── Http/
│   ├── Controllers/
│   │   └── MarcaController.php           # Controlador
│   └── Requests/
│       └── Marca/
│           ├── StoreMarcaRequest.php     # Validación crear
│           └── UpdateMarcaRequest.php    # Validación actualizar
├── Models/
│   └── Marca.php                         # Modelo
├── Services/
│   └── MarcaService.php                  # Lógica de negocio
│
database/
└── migrations/
    └── xxxx_xx_xx_create_marcas_table.php  # Migración
│
resources/
└── views/
    └── marcas/
        └── index.blade.php               # Vista principal
│
routes/
└── web.php                               # Agregar ruta
```

---

## 🔢 Paso 1: Crear Migración, Modelo y Controlador

```bash
# Este comando crea los 3 archivos base
php artisan make:model Marca -mcr

# Esto genera:
# - database/migrations/xxxx_xx_xx_xxxxxx_create_marcas_table.php
# - app/Models/Marca.php
# - app/Http/Controllers/MarcaController.php
```

---

## 🔢 Paso 2: Configurar la Migración

**Archivo:** `database/migrations/xxxx_create_marcas_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marcas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('descripcion', 500)->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marcas');
    }
};
```

---

## 🔢 Paso 3: Configurar el Modelo

**Archivo:** `app/Models/Marca.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla en la base de datos.
     */
    protected $table = 'marcas';

    /**
     * Campos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    /**
     * Casteo de tipos.
     */
    protected $casts = [
        'estado' => 'boolean',
    ];

    // ========================================
    // RELACIONES (si las hay)
    // ========================================

    /**
     * Una marca tiene muchos productos.
     */
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
```

---

## 🔢 Paso 4: Crear Form Requests (Validaciones)

```bash
php artisan make:request Marca/StoreMarcaRequest
php artisan make:request Marca/UpdateMarcaRequest
```

**Archivo:** `app/Http/Requests/Marca/StoreMarcaRequest.php`

```php
<?php

namespace App\Http\Requests\Marca;

use Illuminate\Foundation\Http\FormRequest;

class StoreMarcaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:100|unique:marcas,nombre',
            'descripcion' => 'nullable|string|max:500',
            'estado' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres.',
            'nombre.unique' => 'Ya existe una marca con este nombre.',
        ];
    }
}
```

**Archivo:** `app/Http/Requests/Marca/UpdateMarcaRequest.php`

```php
<?php

namespace App\Http\Requests\Marca;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMarcaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Obtener el ID del registro actual para excluirlo del unique
        $id = $this->route('marca');
        
        return [
            'nombre' => 'required|string|max:100|unique:marcas,nombre,' . $id,
            'descripcion' => 'nullable|string|max:500',
            'estado' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Ya existe una marca con este nombre.',
        ];
    }
}
```

---

## 🔢 Paso 5: Crear el Service (Lógica de Negocio)

**Archivo:** `app/Services/MarcaService.php`

```php
<?php

namespace App\Services;

use App\Models\Marca;
use Illuminate\Database\Eloquent\Collection;

class MarcaService
{
    /**
     * Obtiene todas las marcas ordenadas por nombre.
     */
    public function obtenerTodas(): Collection
    {
        return Marca::orderBy('nombre')->get();
    }

    /**
     * Obtiene solo las marcas activas.
     */
    public function obtenerActivas(): Collection
    {
        return Marca::where('estado', true)
                    ->orderBy('nombre')
                    ->get();
    }

    /**
     * Busca una marca por su ID.
     */
    public function buscarPorId(int $id): Marca
    {
        return Marca::findOrFail($id);
    }

    /**
     * Crea una nueva marca.
     */
    public function crear(array $datos): Marca
    {
        return Marca::create([
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'estado' => $datos['estado'] ?? true,
        ]);
    }

    /**
     * Actualiza una marca existente.
     */
    public function actualizar(int $id, array $datos): Marca
    {
        $marca = $this->buscarPorId($id);
        
        $marca->update([
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? $marca->descripcion,
            'estado' => isset($datos['estado']) ? $datos['estado'] : $marca->estado,
        ]);

        return $marca;
    }

    /**
     * Elimina una marca.
     */
    public function eliminar(int $id): bool
    {
        $marca = $this->buscarPorId($id);
        return $marca->delete();
    }

    /**
     * Verifica si la marca puede eliminarse (no tiene productos).
     */
    public function puedeEliminarse(int $id): bool
    {
        $marca = $this->buscarPorId($id);
        return $marca->productos()->count() === 0;
    }
}
```

---

## 🔢 Paso 6: Configurar el Controlador

**Archivo:** `app/Http/Controllers/MarcaController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\Marca\StoreMarcaRequest;
use App\Http\Requests\Marca\UpdateMarcaRequest;
use App\Services\MarcaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MarcaController extends Controller
{
    protected MarcaService $service;

    public function __construct(MarcaService $service)
    {
        $this->service = $service;
    }

    /**
     * Listar todas las marcas.
     * GET /marcas
     */
    public function index(): View
    {
        $marcas = $this->service->obtenerTodas();
        return view('marcas.index', compact('marcas'));
    }

    /**
     * Mostrar formulario de creación.
     * GET /marcas/create
     */
    public function create(): View
    {
        return view('marcas.create');
    }

    /**
     * Guardar nueva marca.
     * POST /marcas
     */
    public function store(StoreMarcaRequest $request): RedirectResponse
    {
        $this->service->crear($request->validated());

        return redirect()
            ->route('marcas.index')
            ->with('success', 'Marca creada correctamente.');
    }

    /**
     * Mostrar detalle de marca.
     * GET /marcas/{id}
     */
    public function show(string $id): View
    {
        $marca = $this->service->buscarPorId((int) $id);
        return view('marcas.show', compact('marca'));
    }

    /**
     * Mostrar formulario de edición.
     * GET /marcas/{id}/edit
     */
    public function edit(string $id): View
    {
        $marca = $this->service->buscarPorId((int) $id);
        return view('marcas.edit', compact('marca'));
    }

    /**
     * Actualizar marca.
     * PUT /marcas/{id}
     */
    public function update(UpdateMarcaRequest $request, string $id): RedirectResponse
    {
        $this->service->actualizar((int) $id, $request->validated());

        return redirect()
            ->route('marcas.index')
            ->with('success', 'Marca actualizada correctamente.');
    }

    /**
     * Eliminar marca.
     * DELETE /marcas/{id}
     */
    public function destroy(string $id): RedirectResponse
    {
        if (!$this->service->puedeEliminarse((int) $id)) {
            return redirect()
                ->route('marcas.index')
                ->with('error', 'No se puede eliminar: tiene productos asociados.');
        }

        $this->service->eliminar((int) $id);

        return redirect()
            ->route('marcas.index')
            ->with('success', 'Marca eliminada correctamente.');
    }
}
```

---

## 🔢 Paso 7: Agregar Ruta en web.php

**Archivo:** `routes/web.php`

```php
// Dentro del grupo de autenticación
Route::middleware(['auth'])->group(function () {
    
    // ... otras rutas ...
    
    Route::resource('marcas', MarcaController::class);
});
```

---

## 🔢 Paso 8: Crear la Vista (con Modales)

**Archivo:** `resources/views/marcas/index.blade.php`

```blade
@extends('layouts.master')

@section('title')
    Marcas
@endsection

@section('content')
    {{-- Breadcrumb --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Marcas</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Catálogo</a></li>
                        <li class="breadcrumb-item active">Marcas</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Alertas --}}
    <div id="alertContainer">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    {{-- Tabla de datos --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Lista de Marcas</h5>
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-primary" 
                                data-bs-toggle="modal" data-bs-target="#modalMarca"
                                onclick="limpiarFormulario()">
                            <i class="ri-add-line me-1"></i> Nueva Marca
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th style="width: 100px;">Estado</th>
                                    <th style="width: 150px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($marcas as $marca)
                                    <tr>
                                        <td>{{ $marca->id }}</td>
                                        <td><strong>{{ $marca->nombre }}</strong></td>
                                        <td>{{ Str::limit($marca->descripcion, 50) ?? '-' }}</td>
                                        <td>
                                            @if($marca->estado)
                                                <span class="badge bg-success">Activo</span>
                                            @else
                                                <span class="badge bg-danger">Inactivo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        onclick="verMarca({{ $marca->id }})" title="Ver">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-warning" 
                                                        onclick="editarMarca({{ $marca->id }})" title="Editar">
                                                    <i class="ri-pencil-line"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        onclick="eliminarMarca({{ $marca->id }}, '{{ $marca->nombre }}')" 
                                                        title="Eliminar">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="ri-price-tag-3-line fs-1 d-block mb-2"></i>
                                            No hay marcas registradas
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL CREAR/EDITAR --}}
    <div class="modal fade" id="modalMarca" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nueva Marca</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formMarca" method="POST">
                    @csrf
                    <input type="hidden" id="formMethod" name="_method" value="POST">
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">
                                Nombre <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   placeholder="Ej: Nike, Samsung, Apple" required>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" 
                                      rows="3" placeholder="Descripción opcional..."></textarea>
                        </div>

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" 
                                   id="estado" name="estado" value="1" checked>
                            <label class="form-check-label" for="estado">Estado Activo</label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL VER --}}
    <div class="modal fade" id="modalVer" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle de Marca</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-borderless">
                        <tr><th>ID:</th><td id="verID"></td></tr>
                        <tr><th>Nombre:</th><td id="verNombre" class="fw-bold"></td></tr>
                        <tr><th>Descripción:</th><td id="verDescripcion"></td></tr>
                        <tr><th>Estado:</th><td id="verEstado"></td></tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL ELIMINAR --}}
    <div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="ri-error-warning-line text-danger" style="font-size: 4rem;"></i>
                    <p class="mt-3">¿Estás seguro de eliminar la marca:</p>
                    <p class="fw-bold fs-5" id="nombreEliminar"></p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <form id="formEliminar" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="ri-delete-bin-line me-1"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    // Datos de marcas para JavaScript
    const marcas = @json($marcas);
    
    /**
     * Limpia el formulario para crear nuevo registro.
     */
    function limpiarFormulario() {
        document.getElementById('modalTitle').textContent = 'Nueva Marca';
        document.getElementById('formMarca').reset();
        document.getElementById('formMarca').action = "{{ route('marcas.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('estado').checked = true;
    }
    
    /**
     * Abre el modal de edición con los datos del registro.
     */
    function editarMarca(id) {
        const marca = marcas.find(m => m.id === id);
        if (!marca) return;
        
        document.getElementById('modalTitle').textContent = 'Editar Marca';
        document.getElementById('formMarca').action = `/marcas/${id}`;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('nombre').value = marca.nombre;
        document.getElementById('descripcion').value = marca.descripcion || '';
        document.getElementById('estado').checked = marca.estado == 1;
        
        new bootstrap.Modal(document.getElementById('modalMarca')).show();
    }
    
    /**
     * Muestra los detalles del registro en un modal.
     */
    function verMarca(id) {
        const marca = marcas.find(m => m.id === id);
        if (!marca) return;
        
        document.getElementById('verID').textContent = marca.id;
        document.getElementById('verNombre').textContent = marca.nombre;
        document.getElementById('verDescripcion').textContent = marca.descripcion || 'Sin descripción';
        document.getElementById('verEstado').innerHTML = marca.estado 
            ? '<span class="badge bg-success">Activo</span>' 
            : '<span class="badge bg-danger">Inactivo</span>';
        
        new bootstrap.Modal(document.getElementById('modalVer')).show();
    }
    
    /**
     * Muestra el modal de confirmación para eliminar.
     */
    function eliminarMarca(id, nombre) {
        document.getElementById('nombreEliminar').textContent = nombre;
        document.getElementById('formEliminar').action = `/marcas/${id}`;
        new bootstrap.Modal(document.getElementById('modalEliminar')).show();
    }
</script>
@endsection
```

---

## 🔢 Paso 9: Ejecutar Migración

```bash
php artisan migrate
```

---

## 🔢 Paso 10: Agregar al Sidebar

**Archivo:** `resources/views/layouts/sidebar.blade.php`

Buscar la sección de Catálogo y agregar:

```blade
{{-- Marcas --}}
<li class="nav-item">
    <a class="nav-link menu-link {{ request()->routeIs('marcas.*') ? 'active' : '' }}" 
       href="{{ route('marcas.index') }}">
        <i class="ri-award-line"></i> <span>Marcas</span>
    </a>
</li>
```

---

## 📋 Checklist Rápido

```
☐ php artisan make:model NombreModelo -mcr
☐ Configurar migración (campos de tabla)
☐ Configurar modelo ($fillable, relaciones)
☐ php artisan make:request NombreModelo/StoreNombreModeloRequest
☐ php artisan make:request NombreModelo/UpdateNombreModeloRequest
☐ Configurar Form Requests (rules, messages)
☐ Crear Service (app/Services/NombreModeloService.php)
☐ Configurar Controlador (inyectar service, usar requests)
☐ Agregar ruta en web.php
☐ Crear vista index.blade.php con modales
☐ php artisan migrate
☐ Agregar al sidebar
☐ Probar: Crear, Ver, Editar, Eliminar
```

---

## 🔗 Rutas Generadas por Route::resource

| Método | URI | Acción | Nombre de Ruta |
|--------|-----|--------|----------------|
| GET | /marcas | index | marcas.index |
| GET | /marcas/create | create | marcas.create |
| POST | /marcas | store | marcas.store |
| GET | /marcas/{marca} | show | marcas.show |
| GET | /marcas/{marca}/edit | edit | marcas.edit |
| PUT/PATCH | /marcas/{marca} | update | marcas.update |
| DELETE | /marcas/{marca} | destroy | marcas.destroy |

---

## ⚠️ Notas Importantes

1. **Nombres en singular para modelos**: `Marca`, no `Marcas`
2. **Nombres en plural para tablas**: `marcas`, no `marca`
3. **Form Requests deben retornar `true` en `authorize()`**
4. **Los Services van en `app/Services/`** (crear la carpeta si no existe)
5. **Siempre usar `$request->validated()`** en el controlador
6. **Para relaciones FK, usar `exists:tabla,columna`** en validaciones

---

## 📝 Ejemplo de Modelo con Relaciones

```php
// Producto pertenece a: Categoría, Marca, Unidad
class Producto extends Model
{
    protected $fillable = [
        'categoria_id',
        'marca_id', 
        'unidad_id',
        'nombre',
        'precio',
        'stock',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }
}
```

---

**Autor:** Sistema POS  
**Fecha:** Enero 2026  
**Versión:** 1.0
