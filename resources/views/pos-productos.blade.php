{{-- 
    =====================================================
    🎓 TUTORIAL: Tu primera vista en Laravel
    =====================================================
    
    Esta vista hereda del "master.blade.php" que es el layout principal.
    El layout ya incluye:
    - Barra superior (topbar)
    - Menú lateral (sidebar)  
    - Footer
    - CSS y JavaScript
    
    Tú solo defines el CONTENIDO de tu página.
    =====================================================
--}}

{{-- @extends = "Heredar de" - Usamos el layout master que ya tiene todo el diseño --}}
@extends('layouts.master')

{{-- @section('title') = El título que aparece en la pestaña del navegador --}}
@section('title')
    Productos POS
@endsection

{{-- @section('content') = Aquí va TODO el contenido de tu página --}}
@section('content')
    {{-- ========== BREADCRUMB (Navegación) ========== --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Productos</h4>

                {{-- Breadcrumb: POS > Productos --}}
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">POS</a></li>
                        <li class="breadcrumb-item active">Productos</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    {{-- Fin Breadcrumb --}}

    {{-- ========== CONTENIDO PRINCIPAL ========== --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                {{-- Cabecera de la tarjeta --}}
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Lista de Productos</h5>
                    {{-- Botón para agregar producto --}}
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-primary">
                            <i class="ri-add-line align-bottom me-1"></i> Nuevo Producto
                        </button>
                    </div>
                </div>

                {{-- Cuerpo de la tarjeta --}}
                <div class="card-body">
                    {{-- Tabla de productos --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Código</th>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Categoría</th>
                                    <th scope="col">Precio</th>
                                    <th scope="col">Stock</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- 
                                    📌 NOTA: Por ahora usamos datos de ejemplo.
                                    Después conectaremos con la base de datos usando:
                                    @foreach ($productos as $producto)
                                --}}
                                <tr>
                                    <td>1</td>
                                    <td>PROD-001</td>
                                    <td>Laptop HP Pavilion</td>
                                    <td>Electrónicos</td>
                                    <td>S/ 2,500.00</td>
                                    <td>15</td>
                                    <td><span class="badge bg-success">Activo</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info"><i class="ri-eye-line"></i></button>
                                        <button class="btn btn-sm btn-warning"><i class="ri-pencil-line"></i></button>
                                        <button class="btn btn-sm btn-danger"><i class="ri-delete-bin-line"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>PROD-002</td>
                                    <td>Mouse Logitech G502</td>
                                    <td>Accesorios</td>
                                    <td>S/ 180.00</td>
                                    <td>50</td>
                                    <td><span class="badge bg-success">Activo</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info"><i class="ri-eye-line"></i></button>
                                        <button class="btn btn-sm btn-warning"><i class="ri-pencil-line"></i></button>
                                        <button class="btn btn-sm btn-danger"><i class="ri-delete-bin-line"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>PROD-003</td>
                                    <td>Teclado Mecánico Redragon</td>
                                    <td>Accesorios</td>
                                    <td>S/ 250.00</td>
                                    <td>0</td>
                                    <td><span class="badge bg-danger">Sin Stock</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info"><i class="ri-eye-line"></i></button>
                                        <button class="btn btn-sm btn-warning"><i class="ri-pencil-line"></i></button>
                                        <button class="btn btn-sm btn-danger"><i class="ri-delete-bin-line"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    {{-- Fin tabla --}}
                </div>
                {{-- Fin card-body --}}
            </div>
            {{-- Fin card --}}
        </div>
    </div>
    {{-- Fin contenido principal --}}
@endsection

{{-- 
    =====================================================
    📌 RESUMEN DE LO QUE APRENDISTE:
    =====================================================
    
    1. @extends('layouts.master') 
       → Hereda el diseño principal (sidebar, header, footer)
    
    2. @section('title') ... @endsection
       → Define el título de la página
    
    3. @section('content') ... @endsection
       → Aquí va todo el HTML de tu página
    
    4. {{ $variable }}
       → Muestra una variable PHP (ya lo usaremos después)
    
    5. {{-- comentario --}}
→ Comentarios que NO aparecen en el HTML final

=====================================================
🚀 PRÓXIMO PASO: Abre http://127.0.0.1:8000/pos-productos
=====================================================
--}}
