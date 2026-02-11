@extends('layouts.master')

@section('title')
    Productos
@endsection

@section('css')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />

    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
    <style>
        /* Asegurar que el dropdown de Select2 aparezca sobre el modal */
        .select2-container--open {
            z-index: 9999 !important;
        }

        .select2-container {
            width: 100% !important;
        }

        /* Ajuste para que Select2 se vea bien con Bootstrap 5 */
        .select2-container--default .select2-selection--single {
            border: 1px solid #ced4da;
            height: 38px;
            line-height: 36px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            padding-left: 12px;
            color: #212529;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        /* Estilo para error de validación en Select2 */
        .is-invalid+.select2-container--default .select2-selection--single {
            border-color: #f06548;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Productos</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Directorio</a></li>
                        <li class="breadcrumb-item active">Productos</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-0 align-items-center d-flex flex-wrap gap-2">
                    <h5 class="card-title mb-0 flex-grow-1 text-uppercase fw-bold">Listado de Productos</h5>
                    <div class="d-flex flex-wrap gap-2 mt-0">
                        <button type="button" id="btnExportarPDF"
                            class="btn btn-soft-danger waves-effect waves-light shadow-none d-flex align-items-center">
                            <i class="ri-file-pdf-line fs-18"></i> <span
                                class="d-none d-sm-inline ms-1 text-uppercase">PDF</span>
                        </button>
                        <button type="button" id="btnExportarExcel"
                            class="btn btn-soft-success waves-effect waves-light shadow-none d-flex align-items-center">
                            <i class="ri-file-excel-line fs-18"></i> <span
                                class="d-none d-sm-inline ms-1 text-uppercase">Excel</span>
                        </button>
                        @can('productos.crear')
                            <button type="button" class="btn btn-primary d-flex align-items-center shadow-sm"
                                data-bs-toggle="modal" data-bs-target="#modalProducto" onclick="limpiarFormulario()">
                                <i class="ri-add-line fs-18 me-1"></i> <span class="d-none d-md-inline text-uppercase">Nuevo
                                    Producto</span><span class="d-inline d-md-none text-uppercase">Nuevo</span>
                            </button>
                        @endcan
                    </div>
                </div>


                <div class="card-body">
                    <table id="tablaProductos" class="table nowrap align-middle" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>Código/Barras</th>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Marca</th>
                                <th>Stock</th>
                                <th>Precio Venta</th>
                                <th style="width: 100px;">Estado</th>
                                <th class="no-exportar">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($productos as $producto)
                                <tr data-id="{{ $producto->id }}">
                                    <td>
                                        <span class="badge bg-light text-primary">{{ $producto->codigo }}</span><br>
                                        <small class="text-muted">{{ $producto->codigo_barras ?? 'Sin barras' }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if ($producto->imagen)
                                                <img src="{{ asset('storage/productos/' . $producto->imagen) }}"
                                                    class="avatar-xs rounded me-2">
                                            @else
                                                <div class="avatar-xs me-2"><span
                                                        class="avatar-title rounded bg-soft-warning text-warning">P</span>
                                                </div>
                                            @endif
                                            <strong>{{ $producto->nombre }}</strong>
                                        </div>
                                    </td>
                                    <td>{{ $producto->categoria->nombre }}</td>
                                    <td>{{ $producto->marca->nombre }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $producto->stock <= $producto->stock_minimo ? 'bg-danger' : 'bg-success' }}">
                                            {{ number_format($producto->stock, 2) }} {{ $producto->unidad->codigo }}
                                        </span>
                                    </td>
                                    <td><strong>{{ $moneda ?? 'S/' }}
                                            {{ number_format($producto->precio_venta, 2) }}</strong></td>
                                    <td id="estado-badge-{{ $producto->id }}">
                                        @if ($producto->estado)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info"
                                            onclick="verProducto({{ $producto->id }})" title="Ver Detalles">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                        @can('productos.editar')
                                            <button type="button" class="btn btn-sm btn-warning"
                                                onclick="editarProducto({{ $producto->id }})" title="Editar">
                                                <i class="ri-pencil-line"></i>
                                            </button>
                                        @endcan
                                        @can('productos.eliminar')
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="eliminarProducto({{ $producto->id }}, '{{ $producto->nombre }}')"
                                                title="Eliminar">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        @endcan

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Crear/Editar - Con Indicadores de Progreso por Pasos --}}
    <div class="modal fade" id="modalProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header border-bottom pb-3">
                    <div>
                        <h5 class="modal-title mb-1" id="modalTitle">
                            <i class="ri-shopping-basket-2-line me-2 align-middle"></i>Registro de Producto
                        </h5>
                        <p class="text-muted fw-medium fs-13 mb-0">Complete la información en los pasos indicados.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formProducto" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        {{-- Indicadores de Pasos Visuales --}}
                        {{-- Wizard Nativo de Velzon (Puro) --}}
                        <div class="step-arrow-nav mb-4">
                            <ul class="nav nav-pills custom-nav nav-justified" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="step1-tab" data-bs-toggle="pill"
                                        data-bs-target="#tab-general" type="button" role="tab" aria-selected="true"
                                        onclick="irAPaso(1)">
                                        General
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="step2-tab" data-bs-toggle="pill"
                                        data-bs-target="#tab-precios" type="button" role="tab"
                                        aria-selected="false" onclick="irAPaso(2)">
                                        Precios
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="step3-tab" data-bs-toggle="pill"
                                        data-bs-target="#tab-adicional" type="button" role="tab"
                                        aria-selected="false" onclick="irAPaso(3)">
                                        Adicional
                                    </button>
                                </li>
                            </ul>
                        </div>


                        {{-- Alerta de campos faltantes --}}
                        <div class="alert alert-warning d-none mb-3" id="alertaCampos" role="alert">
                            <i class="ri-alert-line me-2"></i>
                            <span id="alertaCamposTexto">Complete los campos obligatorios marcados con (*)</span>
                        </div>

                        <div class="tab-content">
                            {{-- PASO 1: Información General --}}
                            <div class="tab-pane fade show active" id="tab-general" role="tabpanel">
                                <h6 class="text-primary mb-3"><i class="ri-information-line me-1"></i>Información General
                                </h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="nombre" class="form-label fw-semibold">
                                            Nombre del Producto <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" id="nombre" name="nombre"
                                            class="form-control campo-paso1" placeholder="Ej: Arroz Costeño 1kg" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="categoria_id" class="form-label">Categoría <span
                                                class="text-danger">*</span></label>
                                        <select
                                            class="form-select js-example-basic-single @error('categoria_id') is-invalid @enderror"
                                            id="categoria_id" name="categoria_id" required>
                                            <option value="">-- Seleccionar --</option>
                                            @foreach ($categorias as $cat)
                                                <option value="{{ $cat->id }}"
                                                    {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>
                                                    {{ $cat->nombre }}
                                                </option>
                                            @endforeach
                                        </select>

                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label for="marca_id" class="form-label mb-0">Marca <span
                                                    class="text-danger">*</span></label>
                                            <button type="button"
                                                class="btn btn-link link-primary p-0 shadow-none fs-12 fw-bold"
                                                data-bs-toggle="modal" data-bs-target="#modalNuevaMarca">
                                                <i class="ri-add-line align-middle me-1"></i> NUEVA MARCA
                                            </button>
                                        </div>
                                        <select
                                            class="form-select js-example-basic-single @error('marca_id') is-invalid @enderror"
                                            id="marca_id" name="marca_id" required>
                                            <option value="">-- Seleccionar --</option>
                                            @foreach ($marcas as $mar)
                                                <option value="{{ $mar->id }}">{{ $mar->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="codigo" class="form-label">Código SKU <small
                                                class="text-muted">(Opcional)</small></label>
                                        <input type="text" id="codigo" name="codigo" class="form-control"
                                            placeholder="Se genera automáticamente si está vacío">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="codigo_barras" class="form-label">Código de Barras <small
                                                class="text-muted">(Opcional)</small></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ri-barcode-line"></i></span>
                                            <input type="text" id="codigo_barras" name="codigo_barras"
                                                class="form-control" placeholder="Escanear o escribir">
                                            <button class="btn btn-soft-primary" type="button" id="btnEscanearCamara"
                                                title="Escanear con Cámara">
                                                <i class="ri-camera-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                                    <button type="button" class="btn btn-primary" onclick="irAPaso(2)">
                                        Siguiente: Precios <i class="ri-arrow-right-line ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- PASO 2: Precios y Stock --}}
                            <div class="tab-pane fade" id="tab-precios" role="tabpanel">
                                <h6 class="text-primary mb-3"><i class="ri-money-dollar-circle-line me-1"></i>Precios y
                                    Stock</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="precio_compra" class="form-label">Precio de Compra <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">S/</span>
                                            <input type="number" step="0.01" min="0" id="precio_compra"
                                                name="precio_compra" class="form-control campo-paso2" placeholder="0.00"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="precio_venta" class="form-label">Precio de Venta <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-success text-white">S/</span>
                                            <input type="number" step="0.01" min="0" id="precio_venta"
                                                name="precio_venta" class="form-control border-success campo-paso2"
                                                placeholder="0.00" required>
                                        </div>
                                        <small class="text-success" id="margenGanancia"></small>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="unidad_id" class="form-label">Unidad <span
                                                class="text-danger">*</span></label>
                                        <select
                                            class="form-select js-example-basic-single @error('unidad_id') is-invalid @enderror"
                                            id="unidad_id" name="unidad_id" required>
                                            <option value="">-- Seleccionar --</option>
                                            @foreach ($unidades as $u)
                                                <option value="{{ $u->id }}">{{ $u->nombre }}
                                                    ({{ $u->codigo }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="stock_inicial" class="form-label">
                                            <i class="ri-stack-line text-primary me-1"></i>Stock Inicial <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="number" step="0.01" min="0" id="stock_inicial"
                                            name="stock_inicial" class="form-control border-primary campo-paso2"
                                            placeholder="Cantidad" required>
                                        <small class="text-muted">Se registra en Kardex</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="stock_minimo" class="form-label">Stock Mínimo</label>
                                        <input type="number" step="0.01" min="0" id="stock_minimo"
                                            name="stock_minimo" class="form-control" value="5"
                                            placeholder="Alerta">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                                    <button type="button" class="btn btn-light" onclick="irAPaso(1)">
                                        <i class="ri-arrow-left-line me-1"></i> Anterior
                                    </button>
                                    <button type="button" class="btn btn-primary" onclick="irAPaso(3)">
                                        Siguiente: Datos Adicionales <i class="ri-arrow-right-line ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- PASO 3: Datos Adicionales (Opcionales) --}}
                            <div class="tab-pane fade" id="tab-adicional" role="tabpanel">
                                <h6 class="text-primary mb-3"><i class="ri-file-list-3-line me-1"></i>Datos Adicionales
                                    <span class="badge bg-soft-secondary text-secondary">Opcional</span>
                                </h6>
                                <div class="alert alert-soft-info mb-3 py-2">
                                    <i class="ri-lightbulb-line me-1"></i>
                                    Estos campos son <strong>opcionales</strong>. Puede guardar el producto sin
                                    completarlos.
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="imagen" class="form-label">Imagen del Producto</label>
                                        <input type="file" id="imagen" name="imagen" class="form-control"
                                            accept="image/*">
                                        <small class="text-muted">JPG, PNG o WEBP (Máx 10MB)</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="ubicacion" class="form-label">Ubicación en Tienda</label>
                                        <input type="text" id="ubicacion" name="ubicacion" class="form-control"
                                            placeholder="Ej: Estante A-1, Refrigerador">
                                    </div>
                                    <div class="col-12">
                                        <label for="descripcion" class="form-label">Descripción</label>
                                        <textarea id="descripcion" name="descripcion" class="form-control" rows="2"
                                            placeholder="Información adicional del producto..."></textarea>
                                    </div>
                                    <input type="hidden" id="material" name="material" value="">
                                </div>
                                <div
                                    class="d-flex flex-column-reverse flex-sm-row justify-content-between mt-4 pt-3 border-top gap-2">
                                    <button type="button" class="btn btn-light" onclick="irAPaso(2)">
                                        <i class="ri-arrow-left-line me-1"></i> Anterior
                                    </button>
                                    <button type="submit" id="btnGuardar" class="btn btn-success btn-lg px-4 shadow-sm">
                                        <span id="btnGuardarTexto"><i class="ri-save-line me-1"></i>Guardar
                                            Producto</span>
                                        <span id="btnGuardarSpinner" class="d-none">
                                            <span class="spinner-border spinner-border-sm me-1"></span>Guardando...
                                        </span>
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Estilos para ajustes del modal y Wizard --}}
    <style>
        /* Animación de error sutil */
        .campo-incompleto {
            border-color: var(--vz-danger) !important;
            box-shadow: 0 0 0 0.25rem rgba(var(--vz-danger-rgb), .25) !important;
        }
    </style>

    {{-- Modal Ver - Diseño Nativo Velzon con Tabs --}}
    <div class="modal fade" id="modalVer" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content overflow-hidden">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="ri-search-eye-line me-2 fs-22"></i>
                        <span id="verNombre">--</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-0">
                    {{-- Tabs Navigation --}}
                    <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified mb-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab-ver-general" role="tab">
                                <i class="ri-information-line me-1 align-bottom"></i> General
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-ver-inventario" role="tab">
                                <i class="ri-stack-line me-1 align-bottom"></i> Inventario
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-ver-precios" role="tab">
                                <i class="ri-price-tag-3-line me-1 align-bottom"></i> Precios
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content p-4">
                        {{-- Tab 1: General --}}
                        <div class="tab-pane active" id="tab-ver-general" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-md-5 text-center border-end-md">
                                    <div class="avatar-xl mx-auto mb-3">
                                        <img id="verImagenPrincipal" src=""
                                            class="img-thumbnail rounded-circle shadow-sm d-none"
                                            style="width: 120px; height: 120px; object-fit: cover;">
                                        <div id="verSinImagen"
                                            class="avatar-title rounded-circle bg-light text-muted fs-40">
                                            <i class="ri-image-line"></i>
                                        </div>
                                    </div>
                                    <div class="bg-light p-2 rounded border mb-3">
                                        <svg id="verBarcode" class="mw-100"></svg>
                                    </div>
                                    <h6 class="text-muted text-uppercase fw-semibold fs-12 mb-1">SKU / CÓDIGO</h6>
                                    <p class="h5 text-primary fw-bold mb-0" id="verCodigo">--</p>
                                </div>
                                <div class="col-md-7">
                                    <div class="mb-3">
                                        <label
                                            class="form-label text-muted text-uppercase fs-11 fw-bold">Descripción</label>
                                        <p id="verDescripcion" class="border p-2 rounded min-h-60 mb-0">--</p>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <label
                                                class="form-label text-muted text-uppercase fs-11 fw-bold">Categoría</label>
                                            <p id="verCategoria" class="fw-medium mb-0">--</p>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label text-muted text-uppercase fs-11 fw-bold">Marca</label>
                                            <p id="verMarca" class="fw-medium mb-0">--</p>
                                        </div>
                                        <div class="col-6">
                                            <label
                                                class="form-label text-muted text-uppercase fs-11 fw-bold">Estado</label>
                                            <div id="verEstadoBadge">--</div>
                                        </div>
                                        <div class="col-6">
                                            <label
                                                class="form-label text-muted text-uppercase fs-11 fw-bold">Material</label>
                                            <p id="verMaterial" class="fw-medium mb-0">--</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 2: Inventario --}}
                        <div class="tab-pane" id="tab-ver-inventario" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light-subtle shadow-none mb-0">
                                        <div class="card-body p-3 border rounded">
                                            <h5 class="text-success fw-bold mb-1" id="verStock">0.00</h5>
                                            <p class="text-muted text-uppercase fs-11 fw-bold mb-0">Stock Disponible (<span
                                                    id="verUnidadBadge">UND</span>)</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light-subtle shadow-none mb-0">
                                        <div class="card-body p-3 border rounded">
                                            <h5 class="text-danger fw-bold mb-1" id="verStockMinimo">0.00</h5>
                                            <p class="text-muted text-uppercase fs-11 fw-bold mb-0">Stock Mínimo</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded">
                                        <label class="form-label text-muted text-uppercase fs-11 fw-bold mb-1">Proveedor
                                            Principal</label>
                                        <p id="verProveedor" class="h6 mb-0">--</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded">
                                        <label
                                            class="form-label text-muted text-uppercase fs-11 fw-bold mb-1">Ubicación</label>
                                        <p id="verUbicacion" class="h6 mb-0">--</p>
                                    </div>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="alert alert-soft-info border-dashed mb-0 d-flex align-items-center">
                                        <i class="ri-information-line fs-18 me-2"></i>
                                        <div class="row w-100 g-2">
                                            <div class="col-sm-6">
                                                <span class="text-muted">Servicio:</span> <span id="verEsServicio"
                                                    class="fw-bold ms-1 text-dark">NO</span>
                                            </div>
                                            <div class="col-sm-6 text-sm-end">
                                                <span class="text-muted">Permite Venta Negativa:</span> <span
                                                    id="verPermiteNegativo" class="fw-bold ms-1 text-dark">NO</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 3: Precios e Impuestos --}}
                        <div class="tab-pane" id="tab-ver-precios" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-4 text-center">
                                    <div class="p-3 border rounded bg-light">
                                        <p class="text-muted text-uppercase fs-11 fw-bold mb-1">Precio Venta</p>
                                        <h4 class="text-success fw-bold mb-0" id="verPrecioVenta">S/ 0.00</h4>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="p-3 border rounded bg-light">
                                        <p class="text-muted text-uppercase fs-11 fw-bold mb-1">Precio Compra</p>
                                        <h4 class="text-muted fw-bold mb-0" id="verPrecioCompra">S/ 0.00</h4>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="p-3 border rounded bg-light">
                                        <p class="text-muted text-uppercase fs-11 fw-bold mb-1">Prev. V. Mayor</p>
                                        <h4 class="text-info fw-bold mb-0" id="verPrecioMayorista">S/ 0.00</h4>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span class="text-muted">Aplica IGV (Gravado):</span>
                                            <span id="verAplicaIGV" class="badge bg-primary px-3">SÍ</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span class="text-muted">Fecha Vencimiento:</span>
                                            <span id="verVencimiento" class="fw-medium text-dark">--</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light px-4">
                    <button class="btn btn-soft-danger waves-effect shadow-none me-auto" onclick="descargarBarcode()">
                        <i class="ri-download-2-line"></i> Barcode
                    </button>
                    <button type="button" class="btn btn-ghost-dark waves-effect shadow-none"
                        data-bs-dismiss="modal">Cerrar</button>
                    @can('productos.editar')
                        <button type="button" class="btn btn-success waves-effect waves-light shadow-sm px-4"
                            id="btnVerEditar">
                            <i class="ri-edit-line me-1"></i> Editar
                        </button>
                    @endcan
                </div>
            </div>
        </div>
    </div>




    {{-- Modal Eliminar --}}
    <div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="ri-error-warning-line text-danger" style="font-size: 4rem;"></i>
                    <p class="mt-3">¿Está seguro de eliminar el producto?</p>
                    <p class="fw-bold fs-5 text-primary" id="nombreEliminar"></p>
                    <p class="text-muted small">Esta acción no se puede deshacer si el producto ya tiene movimientos.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="formEliminar" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Lector de Cámara --}}
    <div class="modal fade" id="modalLectorCamara" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ri-camera-line me-2"></i>Escanear Código de Barras</h5>
                    <button type="button" class="btn-close" id="btnCerrarLector" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="reader"
                        style="width: 100%; min-height: 300px; background: #000; border-radius: 8px; overflow: hidden;">
                    </div>
                    <div class="mt-3 text-center text-muted small">
                        Enfoque el código de barras dentro del recuadro para escanearlo automáticamente.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Mini Modal para Nueva Marca (Acceso Rápido) --}}
    <div class="modal fade" id="modalNuevaMarca" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="ri-price-tag-3-line me-2"></i>Nueva Marca</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="btnCerrarMarcaModal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="formNuevaMarcaRapida">
                        <div class="mb-3">
                            <label for="marca_nombre_rapido" class="form-label fw-semibold">Nombre de la Marca <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="marca_nombre_rapido" name="nombre"
                                placeholder="Ej: Samsung, Nike" required>
                        </div>
                        <div class="mb-3">
                            <label for="marca_codigo_rapido" class="form-label fw-semibold">Código <small
                                    class="text-muted">(Opcional)</small></label>
                            <input type="text" class="form-control" id="marca_codigo_rapido" name="codigo"
                                placeholder="Auto-generado si se deja vacío">
                        </div>
                        <div class="mb-0">
                            <label for="marca_descripcion_rapida" class="form-label fw-semibold">Descripción <small
                                    class="text-muted">(Opcional)</small></label>
                            <textarea class="form-control" id="marca_descripcion_rapida" name="descripcion" rows="2"
                                placeholder="Opcional..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light w-100 mb-2" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary w-100" id="btnGuardarMarcaRapida">
                        <span id="labelGuardarMarca">Guardar Marca</span>
                        <span id="spinnerGuardarMarca" class="d-none">
                            <span class="spinner-border spinner-border-sm me-1"></span>Procesando...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    <script>
        window.PRODUCTOS_CONFIG = {
            productos: @json($productos),
            categorias: @json($categorias),
            marcas: @json($marcas),
            unidades: @json($unidades),
            proveedores: @json($proveedores),
            routes: {
                store: "{{ route('productos.store') }}"
            },
            csrfToken: "{{ csrf_token() }}"
        };
    </script>
    {{-- Select2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{-- Barcode Generator JS --}}
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    {{-- Camera Scanner JS --}}
    <script src="https://unpkg.com/html5-qrcode"></script>



    <script src="{{ URL::asset('js/modules/productos/index.js') }}"></script>
    <script>
        /**
         * Lógica para el registro rápido de marcas desde el modal de productos
         */
        document.addEventListener('DOMContentLoaded', function() {
            const btnGuardarMarca = document.getElementById('btnGuardarMarcaRapida');
            const formMarca = document.getElementById('formNuevaMarcaRapida');
            const modalMarca = new bootstrap.Modal(document.getElementById('modalNuevaMarca'));
            const selectMarca = $('#marca_id'); // Select2 usa JQuery

            if (btnGuardarMarca) {
                btnGuardarMarca.addEventListener('click', function() {
                    const nombre = document.getElementById('marca_nombre_rapido').value;
                    const codigo = document.getElementById('marca_codigo_rapido').value;
                    const descripcion = document.getElementById('marca_descripcion_rapida').value;

                    if (!nombre) {
                        Toastify({
                            text: "Por favor, ingrese el nombre de la marca",
                            className: "bg-danger",
                            style: {
                                background: "#f06548"
                            }
                        }).showToast();
                        return;
                    }

                    // UI Loading
                    btnGuardarMarca.disabled = true;
                    document.getElementById('labelGuardarMarca').classList.add('d-none');
                    document.getElementById('spinnerGuardarMarca').classList.remove('d-none');

                    // Ajax Request
                    fetch("{{ route('marcas.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                nombre: nombre,
                                codigo: codigo,
                                descripcion: descripcion,
                                estado: 1
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // 1. Notificar
                                Toastify({
                                    text: "¡Marca registrada con éxito!",
                                    className: "bg-success",
                                    style: {
                                        background: "#0ab39c"
                                    }
                                }).showToast();

                                // 2. Agregar al select y seleccionar
                                const newOption = new Option(data.marca.nombre, data.marca.id, true,
                                    true);
                                selectMarca.append(newOption).trigger('change');

                                // 3. Limpiar y cerrar
                                formMarca.reset();
                                document.getElementById('btnCerrarMarcaModal').click();
                            } else {
                                throw new Error(data.message || "Error al registrar la marca");
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Toastify({
                                text: error.message || "No se pudo registrar la marca",
                                className: "bg-danger",
                                style: {
                                    background: "#f06548"
                                }
                            }).showToast();
                        })
                        .finally(() => {
                            // Reset UI
                            btnGuardarMarca.disabled = false;
                            document.getElementById('labelGuardarMarca').classList.remove('d-none');
                            document.getElementById('spinnerGuardarMarca').classList.add('d-none');
                        });
                });
            }
        });

        // Variable global para controlar el paso actual
        window.pasoActual = 1;

        // Función para cambiar de paso con VALIDACIÓN
        function irAPaso(paso) {
            // Si intenta avanzar al paso 2 desde el 1, validamos campos obligatorios
            if (paso === 2 && window.pasoActual === 1) {
                const nombre = document.getElementById('nombre').value;
                const categoria = document.getElementById('categoria_id').value;
                const marca = document.getElementById('marca_id').value;

                let errores = false;

                if (!nombre || !categoria || !marca) {
                    errores = true;
                    // Resaltar campos vacíos
                    const campos = ['nombre', 'categoria_id', 'marca_id'];
                    campos.forEach(id => {
                        const el = document.getElementById(id);
                        if (!el.value) {
                            el.classList.add('campo-incompleto');
                            // Si es select2, el borde está en el container
                            if (id === 'categoria_id' || id === 'marca_id') {
                                $(el).next('.select2-container').find('.select2-selection').css('border-color',
                                    'var(--vz-danger)');
                            }
                        } else {
                            el.classList.remove('campo-incompleto');
                            if (id === 'categoria_id' || id === 'marca_id') {
                                $(el).next('.select2-container').find('.select2-selection').css('border-color', '');
                            }
                        }
                    });

                    Toastify({
                        text: "Por favor, complete los campos obligatorios del Paso 1",
                        className: "bg-danger",
                        style: {
                            background: "#f06548"
                        }
                    }).showToast();

                    return false; // No avanza
                }
            }

            // Cambiar de pestaña visualmente
            const tabEl = document.querySelector(`#step${paso}-tab`);
            if (tabEl) {
                const bootstrapTab = new bootstrap.Tab(tabEl);
                bootstrapTab.show();
                window.pasoActual = paso;
            }
        }

        // Limpiar errores cuando se escribe
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('campo-incompleto')) {
                e.target.classList.remove('campo-incompleto');
            }
        });

        // Limpiar Select2 cuando cambia
        $(document).on('change', '#categoria_id, #marca_id', function() {
            $(this).next('.select2-container').find('.select2-selection').css('border-color', '');
            $(this).removeClass('campo-incompleto');
        });
    </script>
@endsection
