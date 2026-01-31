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
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Listado de Productos</h5>
                    <div class="d-flex flex-shrink-0 gap-2">
                        <button type="button" id="btnExportarPDF" class="btn btn-soft-danger waves-effect waves-light">
                            <i class="las la-file-pdf fs-3"></i><span>PDF</span>
                        </button>
                        <button type="button" id="btnExportarExcel" class="btn btn-soft-success waves-effect waves-light">
                            <i class="las la-file-excel fs-3"></i><span>Excel</span>
                        </button>
                        @can('productos.crear')
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#modalProducto" onclick="limpiarFormulario()">
                                <i class="ri-add-line me-1"></i> Nuevo Producto
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
                                        <div class="d-flex gap-1">
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
                                        </div>
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
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="ri-shopping-basket-2-line me-2"></i>Nuevo Producto
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formProducto" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        {{-- Indicadores de Pasos Visuales --}}
                        <div class="step-wizard mb-4">
                            <div class="d-flex justify-content-center align-items-center">
                                {{-- Paso 1 --}}
                                <div class="step-item text-center" data-step="1">
                                    <div class="step-circle active" id="stepCircle1" onclick="irAPaso(1)">
                                        <span class="step-number">1</span>
                                        <i class="ri-check-line step-check"></i>
                                    </div>
                                    <small class="d-block mt-1 step-label">General</small>
                                </div>
                                <div class="step-line" id="stepLine1"></div>
                                {{-- Paso 2 --}}
                                <div class="step-item text-center" data-step="2">
                                    <div class="step-circle" id="stepCircle2" onclick="irAPaso(2)">
                                        <span class="step-number">2</span>
                                        <i class="ri-check-line step-check"></i>
                                    </div>
                                    <small class="d-block mt-1 step-label">Precios</small>
                                </div>
                                <div class="step-line" id="stepLine2"></div>
                                {{-- Paso 3 --}}
                                <div class="step-item text-center" data-step="3">
                                    <div class="step-circle" id="stepCircle3" onclick="irAPaso(3)">
                                        <span class="step-number">3</span>
                                        <i class="ri-check-line step-check"></i>
                                    </div>
                                    <small class="d-block mt-1 step-label">Adicional</small>
                                </div>
                            </div>
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
                                        <label for="marca_id" class="form-label">Marca <span
                                                class="text-danger">*</span></label>
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
                                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                                    <button type="button" class="btn btn-light" onclick="irAPaso(2)">
                                        <i class="ri-arrow-left-line me-1"></i> Anterior
                                    </button>
                                    <button type="submit" id="btnGuardar" class="btn btn-success btn-lg px-4">
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

    {{-- Estilos para el Step Wizard --}}
    <style>
        .step-wizard .step-circle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background-color: #e9ecef;
            color: #6c757d;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .step-wizard .step-circle:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .step-wizard .step-circle.active {
            background-color: var(--vz-primary);
            color: white;
            border-color: var(--vz-primary);
        }

        .step-wizard .step-circle.completed {
            background-color: #0ab39c;
            color: white;
            border-color: #0ab39c;
        }

        .step-wizard .step-circle .step-check {
            display: none;
        }

        .step-wizard .step-circle.completed .step-number {
            display: none;
        }

        .step-wizard .step-circle.completed .step-check {
            display: inline;
            font-size: 1.2rem;
        }

        .step-wizard .step-line {
            width: 80px;
            height: 4px;
            background-color: #e9ecef;
            margin: 0 10px;
            transition: background-color 0.3s ease;
        }

        .step-wizard .step-line.completed {
            background-color: #0ab39c;
        }

        .step-wizard .step-label {
            color: #6c757d;
            font-weight: 500;
        }

        .step-wizard .step-item[data-step].active .step-label {
            color: var(--vz-primary);
            font-weight: 600;
        }

        /* Campos incompletos */
        .campo-incompleto {
            border-color: #f06548 !important;
            animation: shake 0.5s;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }
    </style>

    {{-- Modal Ver --}}
    <div class="modal fade" id="modalVer" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom pb-4">
                    <h5 class="modal-title"><i class="ri-product-hunting-line me-2"></i>Detalle del Producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-borderless">
                        <tr>
                            <th class="text-muted" style="width: 35%;">ID:</th>
                            <td id="verID" class="fw-bold"></td>
                        </tr>
                        <tr>
                            <th class="text-muted">SKU:</th>
                            <td id="verCodigo" class="fw-bold"></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Nombre:</th>
                            <td id="verNombre" class="fw-bold text-primary"></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Categoría:</th>
                            <td id="verCategoria"></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Marca:</th>
                            <td id="verMarca"></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Precio Venta:</th>
                            <td id="verPrecioVenta" class="fw-bold"></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Stock Actual:</th>
                            <td id="verStock"></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Estado:</th>
                            <td id="verEstado"></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Eliminar --}}
    <div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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

    <script src="{{ URL::asset('js/modules/productos/index.js') }}"></script>
@endsection
