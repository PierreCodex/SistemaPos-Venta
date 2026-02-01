@extends('layouts.master')

@section('title')
    Registrar Compra
@endsection

@section('css')
    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
    <style>
        .metodo-pago-btn {
            width: 100%;
            padding: 12px 8px;
            border: 2px solid var(--vz-input-border);
            border-radius: 8px;
            background: var(--vz-input-bg);
            color: var(--vz-body-color);
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .metodo-pago-btn:hover {
            border-color: var(--vz-primary);
            background: var(--vz-light);
        }

        .metodo-pago-btn.active {
            border-color: var(--vz-primary);
            background: rgba(64, 81, 137, 0.15);
            font-weight: 600;
        }

        .metodo-pago-btn .icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            font-size: 20px;
        }

        .producto-item {
            transition: background 0.2s;
            border-bottom: 1px solid #e9ebec;
        }

        .producto-item:hover {
            background: #f3f6f9;
        }

        .producto-item:last-child {
            border-bottom: none;
        }

        .upload-zone {
            border: 2px dashed #ced4da;
            border-radius: 8px;
            padding: 30px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .upload-zone:hover {
            border-color: #405189;
            background: #f8f9fa;
        }

        .upload-zone i {
            font-size: 32px;
            color: #878a99;
        }

        /* Asegurar que el dropdown de Select2 aparezca sobre el modal */
        .select2-container--open {
            z-index: 9999 !important;
        }

        .select2-dropdown {
            z-index: 9999 !important;
        }

        /* Select2 dentro de un input-group */
        .input-group>.select2-container--default {
            flex: 1 1 auto;
            width: 1% !important;
        }

        .input-group>.select2-container--default .select2-selection--single {
            height: 38px !important;
            display: flex;
            align-items: center;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .input-group>.btn {
            z-index: 4;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        /* Evitar el parpadeo de Select2 (FOUC) */
        select.select2-basic,
        select#buscarProducto {
            display: none;
        }

        /* Espaciado para la barra sticky en móvil */
        @media (max-width: 991.98px) {
            body {
                padding-bottom: 80px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-1">
                        <i class="ri-shopping-bag-3-line me-2 text-primary"></i>Registrar Compra
                    </h4>
                    <p class="text-muted mb-0">Registra una nueva compra de productos para tu tienda.</p>
                </div>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('compras.index') }}">Compras</a></li>
                        <li class="breadcrumb-item active">Nueva Compra</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <form id="formCompra">
        @csrf
        <div class="row">
            <!-- Columna Izquierda: Datos Generales -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ri-information-line me-2 text-info"></i>Datos Generales
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Proveedor -->
                        <div class="mb-3">
                            <label for="proveedor_id" class="form-label">Proveedor <span
                                    class="text-danger">*</span></label>
                            <div class="input-group flex-nowrap">
                                <select id="proveedor_id" name="proveedor_id" class="form-select select2-basic" required>
                                    <option value="">-- Seleccionar --</option>
                                    @foreach ($proveedores as $proveedor)
                                        <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}
                                            ({{ $proveedor->documento }})
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modalNuevoProveedor" title="Agregar proveedor">
                                    <i class="ri-user-add-line"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Comprobante -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tipo_comprobante" class="form-label">Tipo Comp. <span
                                        class="text-danger">*</span></label>
                                <select id="tipo_comprobante" name="tipo_comprobante" class="form-select" required>
                                    <option value="FACTURA" selected>Factura</option>
                                    <option value="BOLETA">Boleta</option>
                                    <option value="RECIBO">Recibo</option>
                                    <option value="NOTA_ENTRADA">Nota de Entrada</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="numero_comprobante" class="form-label">Nro Comp. <span
                                        class="text-danger">*</span></label>
                                <input type="text" id="numero_comprobante" name="numero_comprobante" class="form-control"
                                    placeholder="001-000001" required>
                            </div>
                        </div>

                        <!-- Fecha -->
                        <div class="mb-3">
                            <label for="fecha_emision" class="form-label">Fecha <span class="text-danger">*</span></label>
                            <input type="datetime-local" id="fecha_emision" name="fecha_emision" class="form-control"
                                value="{{ now()->format('Y-m-d\TH:i') }}" required>
                        </div>

                        <!-- Imagen Adjunto -->
                        <div class="mb-3">
                            <label class="form-label">Imagen adjunto</label>
                            <div class="upload-zone" onclick="document.getElementById('imagen').click()">
                                <i class="ri-upload-cloud-2-line"></i>
                                <p class="mb-0 text-muted">Click para subir una imagen</p>
                                <small class="text-muted">PNG, JPG, JPEG (MAX. 2MB)</small>
                            </div>
                            <input type="file" id="imagen" name="imagen" class="d-none" accept="image/*">
                            <div id="previewImagen" class="mt-2"></div>
                        </div>

                        <!-- Observación -->
                        <div class="mb-3">
                            <label for="observaciones" class="form-label">Observación</label>
                            <textarea id="observaciones" name="observaciones" class="form-control" rows="3"
                                placeholder="Observaciones adicionales..."></textarea>
                        </div>

                        <!-- Checkbox -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="registrar_gasto" name="registrar_gasto">
                            <label class="form-check-label" for="registrar_gasto">
                                Registrar como gasto
                            </label>
                        </div>

                        <!-- Botón Registrar (Oculto aquí, movido a la columna de acciones para mejor flujo) -->
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Detalles de Compra -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ri-list-check-2 me-2 text-success"></i>Detalles de Compra
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Buscador de Productos -->
                        <div class="mb-4">
                            <label class="form-label">Buscar Producto <span class="text-danger">*</span></label>
                            <select id="buscarProducto" class="form-select select2-basic" style="width: 100%;">
                                <option value="">Buscar por nombre o código...</option>
                            </select>
                        </div>

                        <!-- Tabla de Productos -->
                        <div class="table-responsive mb-4" style="max-height: 250px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th style="width: 35%">PRODUCTO</th>
                                        <th style="width: 15%" class="text-center">CANTIDAD</th>
                                        <th style="width: 20%" class="text-end">P. UNITARIO</th>
                                        <th style="width: 20%" class="text-end">SUBTOTAL</th>
                                        <th style="width: 5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="listaProductos">
                                    <tr id="filaVacia">
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="ri-shopping-basket-2-line fs-2 d-block mb-2"></i>
                                            No hay productos agregados
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <!-- Precios resumen -->
                            <div class="col-md-4 mb-3">
                                <div class="bg-light p-3 rounded h-100">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">Subtotal:</span>
                                        <span class="fw-semibold" id="txtSubtotal">S/ 0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">IGV (18%):</span>
                                        <span class="fw-semibold" id="txtIgv">S/ 0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                                        <span class="h5 mb-0">Total:</span>
                                        <span class="h5 mb-0 text-primary" id="txtTotal">S/ 0.00</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Métodos de Pago -->
                            <div class="col-md-8">
                                <label class="form-label">Mét. Pago <span class="text-danger">*</span></label>
                                <div class="row g-2">
                                    <div class="col-4">
                                        <div class="metodo-pago-btn active" data-metodo="EFECTIVO">
                                            <div class="icon bg-success-subtle text-success">
                                                <i class="ri-money-dollar-circle-line"></i>
                                            </div>
                                            <small>Efectivo</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metodo-pago-btn" data-metodo="TRANSFERENCIA">
                                            <div class="icon bg-info-subtle text-info">
                                                <i class="ri-bank-line"></i>
                                            </div>
                                            <small>Transferencia</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metodo-pago-btn" data-metodo="TARJETA">
                                            <div class="icon bg-danger-subtle text-danger">
                                                <i class="ri-bank-card-line"></i>
                                            </div>
                                            <small>Tarjeta</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metodo-pago-btn" data-metodo="CREDITO">
                                            <div class="icon bg-warning-subtle text-warning">
                                                <i class="ri-wallet-3-line"></i>
                                            </div>
                                            <small>Crédito</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metodo-pago-btn" data-metodo="YAPE">
                                            <div class="icon bg-secondary-subtle text-secondary">
                                                <i class="ri-smartphone-line"></i>
                                            </div>
                                            <small>Yape/Plin</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metodo-pago-btn" data-metodo="OTRO">
                                            <div class="icon bg-dark-subtle text-dark">
                                                <i class="ri-more-line"></i>
                                            </div>
                                            <small>Otros</small>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="forma_pago" name="forma_pago" value="EFECTIVO">
                            </div>
                            <!-- Botón Registrar (Desktop) -->
                            <div class="mt-4 d-none d-lg-block">
                                <button type="submit" class="btn btn-success btn-lg w-100 shadow-sm" id="btnRegistrar">
                                    <i class="ri-save-line me-1"></i> Finalizar Registro de Compra
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barra de Acción Sticky para Móvil -->
            <div class="d-lg-none fixed-bottom bg-white border-top p-3 shadow-lg" style="z-index: 1040;">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div class="flex-grow-1">
                        <small class="text-muted d-block fs-10 text-uppercase fw-bold">Total a Pagar</small>
                        <span class="h4 mb-0 text-primary fw-bold" id="txtTotalMobile">S/ 0.00</span>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg px-4" id="btnRegistrarMobile">
                        <i class="ri-save-line"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
        </div>
    </form>

    <!-- Modal Nuevo Proveedor -->
    <div class="modal fade" id="modalNuevoProveedor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ri-user-add-line me-2"></i>Nuevo Proveedor
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Tabs de búsqueda -->
                    <ul class="nav nav-pills nav-justified mb-4" id="tabsProveedor" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-manual" data-bs-toggle="pill"
                                data-bs-target="#panel-manual" type="button">Manual</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-dni" data-bs-toggle="pill" data-bs-target="#panel-dni"
                                type="button">Buscar por DNI</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-ruc" data-bs-toggle="pill" data-bs-target="#panel-ruc"
                                type="button">Buscar por RUC</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Panel Manual -->
                        <div class="tab-pane fade show active" id="panel-manual">
                            <form id="formNuevoProveedor">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tipo de documento <span
                                                class="text-danger">*</span></label>
                                        <select id="prov_tipo_documento" class="form-select select2-basic" required>
                                            <option value="">-- Seleccionar --</option>
                                            <option value="DNI">DNI</option>
                                            <option value="RUC">RUC</option>
                                            <option value="CE">Carnet de Extranjería</option>
                                            <option value="PASAPORTE">Pasaporte</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Número de documento <span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="prov_documento" class="form-control"
                                            placeholder="Ej: 12345678 o 12345678901" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
                                    <input type="text" id="prov_nombre" class="form-control"
                                        placeholder="Ej: Juan Pérez" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text" id="prov_telefono" class="form-control"
                                            placeholder="Ej: +34 612 345 678">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Correo electrónico</label>
                                        <input type="email" id="prov_email" class="form-control"
                                            placeholder="Ej: usuario@email.com">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Dirección</label>
                                    <textarea id="prov_direccion" class="form-control" rows="2" placeholder="Ej: Calle 123, Ciudad, País"></textarea>
                                </div>
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary px-4" id="btnGuardarProveedor">
                                        <i class="ri-save-line me-1"></i> Guardar Proveedor
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Panel Buscar DNI -->
                        <div class="tab-pane fade" id="panel-dni">
                            <div class="mb-3">
                                <label class="form-label">Ingrese DNI</label>
                                <div class="input-group">
                                    <input type="text" id="buscar_dni" class="form-control"
                                        placeholder="Ej: 12345678" maxlength="8">
                                    <button type="button" class="btn btn-primary" onclick="buscarDNI()">
                                        <i class="ri-search-line"></i> Buscar
                                    </button>
                                </div>
                            </div>
                            <div id="resultado_dni" class="d-none">
                                <div class="alert alert-info">
                                    <strong>Datos encontrados:</strong>
                                    <p class="mb-0" id="datos_dni"></p>
                                </div>
                                <button type="button" class="btn btn-primary w-100" onclick="usarDatosDNI()">
                                    <i class="ri-check-line me-1"></i> Usar estos datos
                                </button>
                            </div>
                        </div>

                        <!-- Panel Buscar RUC -->
                        <div class="tab-pane fade" id="panel-ruc">
                            <div class="mb-3">
                                <label class="form-label">Ingrese RUC</label>
                                <div class="input-group">
                                    <input type="text" id="buscar_ruc" class="form-control"
                                        placeholder="Ej: 20123456789" maxlength="11">
                                    <button type="button" class="btn btn-primary" onclick="buscarRUC()">
                                        <i class="ri-search-line"></i> Buscar
                                    </button>
                                </div>
                            </div>
                            <div id="resultado_ruc" class="d-none">
                                <div class="alert alert-info">
                                    <strong>Datos encontrados:</strong>
                                    <p class="mb-0" id="datos_ruc"></p>
                                </div>
                                <button type="button" class="btn btn-primary w-100" onclick="usarDatosRUC()">
                                    <i class="ri-check-line me-1"></i> Usar estos datos
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    {{-- jQuery (requerido por Select2 y DataTables) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- Select2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Estado de la compra
        let productosCompra = [];
        let contadorProductos = 0;

        // Inicializar Select2 para proveedor
        $('.select2-basic').select2({
            placeholder: '-- Seleccionar --',
            allowClear: true,
            minimumResultsForSearch: 0
        });

        // Reinicializar Select2 en modal con dropdownParent para que se vea sobre el modal
        $('#modalNuevoProveedor').on('shown.bs.modal', function() {
            // Destruir y recrear Select2 con dropdownParent para que funcione dentro del modal
            if ($('#prov_tipo_documento').hasClass('select2-hidden-accessible')) {
                $('#prov_tipo_documento').select2('destroy');
            }
            $('#prov_tipo_documento').select2({
                placeholder: '-- Seleccionar --',
                allowClear: true,
                dropdownParent: $('#modalNuevoProveedor'),
                minimumResultsForSearch: 0
            });
        });

        // Inicializar Select2 para buscar productos
        $('#buscarProducto').select2({
            placeholder: 'Buscar producto por nombre o código...',
            allowClear: true,
            ajax: {
                url: '{{ route('compras.buscar-productos') }}',
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return {
                        q: params.term // enviar lo que el usuario escribe como 'q'
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.map(function(p) {
                            return {
                                id: p.id,
                                text: p.nombre + ' (' + p.codigo + ')',
                                producto: p
                            };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 1 // Bajamos a 1 para que sea más fácil ver si funciona
        });

        // Al seleccionar un producto
        $('#buscarProducto').on('select2:select', function(e) {
            const producto = e.params.data.producto;
            agregarProducto(producto);
            $(this).val(null).trigger('change');
        });

        // Agregar producto a la lista
        function agregarProducto(producto) {
            const existente = productosCompra.find(p => p.producto_id === producto.id);
            if (existente) {
                existente.cantidad++;
                existente.subtotal = existente.cantidad * existente.costo_unitario;
            } else {
                contadorProductos++;
                productosCompra.push({
                    index: contadorProductos,
                    producto_id: producto.id,
                    nombre: producto.nombre,
                    codigo: producto.codigo,
                    cantidad: 1,
                    costo_unitario: parseFloat(producto.precio_compra) || 0,
                    subtotal: parseFloat(producto.precio_compra) || 0
                });
            }
            renderizarProductos();
            calcularTotal();
        }

        // Renderizar productos
        function renderizarProductos() {
            const tbody = document.getElementById('listaProductos');

            if (productosCompra.length === 0) {
                tbody.innerHTML = `
                    <tr id="filaVacia">
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="ri-shopping-basket-2-line fs-2 d-block mb-2"></i>
                            No hay productos agregados
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = productosCompra.map((p, idx) => `
                <tr class="producto-item">
                    <td class="text-center">${idx + 1}</td>
                    <td>
                        <strong>${p.nombre}</strong>
                        <br><small class="text-muted">${p.codigo}</small>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm text-center" 
                            value="${p.cantidad}" min="0.001" step="0.001"
                            onchange="actualizarCantidad(${idx}, this.value)">
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm text-end" 
                            value="${p.costo_unitario.toFixed(2)}" min="0" step="0.01"
                            onchange="actualizarPrecio(${idx}, this.value)">
                    </td>
                    <td class="text-end fw-semibold">S/ ${p.subtotal.toFixed(2)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-soft-danger btn-sm" onclick="quitarProducto(${idx})">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Actualizar cantidad
        function actualizarCantidad(idx, valor) {
            productosCompra[idx].cantidad = parseFloat(valor) || 0;
            productosCompra[idx].subtotal = productosCompra[idx].cantidad * productosCompra[idx].costo_unitario;
            renderizarProductos();
            calcularTotal();
        }

        // Actualizar precio
        function actualizarPrecio(idx, valor) {
            productosCompra[idx].costo_unitario = parseFloat(valor) || 0;
            productosCompra[idx].subtotal = productosCompra[idx].cantidad * productosCompra[idx].costo_unitario;
            renderizarProductos();
            calcularTotal();
        }

        // Quitar producto
        function quitarProducto(idx) {
            productosCompra.splice(idx, 1);
            renderizarProductos();
            calcularTotal();
        }

        function calcularTotal() {
            const total = productosCompra.reduce((sum, p) => sum + p.subtotal, 0);
            const subtotal = total / 1.18;
            const igv = total - subtotal;

            const totalFormateado = `S/ ${total.toFixed(2)}`;
            document.getElementById('txtSubtotal').textContent = `S/ ${subtotal.toFixed(2)}`;
            document.getElementById('txtIgv').textContent = `S/ ${igv.toFixed(2)}`;
            document.getElementById('txtTotal').textContent = totalFormateado;

            // Actualizar también el total en la barra móvil
            if (document.getElementById('txtTotalMobile')) {
                document.getElementById('txtTotalMobile').textContent = totalFormateado;
            }
        }

        // Métodos de pago
        document.querySelectorAll('.metodo-pago-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.metodo-pago-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('forma_pago').value = this.dataset.metodo;
            });
        });

        // Preview imagen
        document.getElementById('imagen').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const preview = document.getElementById('previewImagen');
                preview.innerHTML =
                    `<img src="${URL.createObjectURL(file)}" class="img-fluid rounded" style="max-height: 100px;">`;
            }
        });

        // Enviar formulario
        document.getElementById('formCompra').addEventListener('submit', function(e) {
            e.preventDefault();

            if (productosCompra.length === 0) {
                Toastify({
                    text: "Debe agregar al menos un producto",
                    duration: 3000,
                    gravity: "top",
                    position: "center",
                    className: "bg-danger"
                }).showToast();
                return;
            }

            const btn = document.getElementById('btnRegistrar');
            const btnMobile = document.getElementById('btnRegistrarMobile');

            const btnContent = '<i class="ri-save-line me-1"></i> Finalizar Registro de Compra';
            const loadingContent = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';

            if (btn) {
                btn.disabled = true;
                btn.innerHTML = loadingContent;
            }
            if (btnMobile) {
                btnMobile.disabled = true;
                btnMobile.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            }

            const total = productosCompra.reduce((sum, p) => sum + p.subtotal, 0);
            const subtotal = total / 1.18;
            const igv = total - subtotal;
            const formaPago = document.getElementById('forma_pago').value;

            const datos = {
                proveedor_id: document.getElementById('proveedor_id').value,
                tipo_comprobante: document.getElementById('tipo_comprobante').value,
                numero_comprobante: document.getElementById('numero_comprobante').value,
                fecha_emision: document.getElementById('fecha_emision').value,
                forma_pago: formaPago === 'CREDITO' ? 'CREDITO' : 'CONTADO',
                observaciones: document.getElementById('observaciones').value,
                productos: productosCompra,
                subtotal: subtotal,
                igv: igv,
                descuento: 0,
                total: total
            };

            fetch('{{ route('compras.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(datos)
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Toastify({
                            text: data.message || "Compra registrada correctamente",
                            duration: 2000,
                            gravity: "top",
                            position: "center",
                            className: "bg-success"
                        }).showToast();

                        setTimeout(() => {
                            window.location.href = '{{ route('compras.index') }}';
                        }, 1500);
                    } else {
                        Toastify({
                            text: data.message || "Error en el servidor",
                            duration: 4000,
                            gravity: "top",
                            position: "center",
                            className: "bg-danger"
                        }).showToast();
                        resetButtons();
                    }
                })
                .catch(err => {
                    console.error('Error en fetch:', err);
                    Toastify({
                        text: "Error inesperado al conectar con el servidor",
                        duration: 4000,
                        gravity: "top",
                        position: "center",
                        className: "bg-danger"
                    }).showToast();
                    resetButtons();
                });

            function resetButtons() {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = btnContent;
                }
                if (btnMobile) {
                    btnMobile.disabled = false;
                    btnMobile.innerHTML = '<i class="ri-save-line"></i> Guardar';
                }
            }
        });

        // ==========================================
        // MODAL NUEVO PROVEEDOR
        // ==========================================
        let datosEncontrados = null;

        // Guardar proveedor desde el modal
        document.getElementById('formNuevoProveedor').addEventListener('submit', function(e) {
            e.preventDefault();

            const btn = document.getElementById('btnGuardarProveedor');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

            const datos = {
                tipo_documento: document.getElementById('prov_tipo_documento').value,
                documento: document.getElementById('prov_documento').value,
                nombre: document.getElementById('prov_nombre').value,
                telefono: document.getElementById('prov_telefono').value,
                email: document.getElementById('prov_email').value,
                direccion: document.getElementById('prov_direccion').value,
                estado: true
            };

            fetch('{{ route('proveedores.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(datos)
                })
                .then(r => r.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ri-save-line me-1"></i> Guardar Proveedor';

                    if (data.success || data.proveedor) {
                        const proveedor = data.proveedor || data.data;

                        // Agregar nuevo proveedor al select
                        const select = document.getElementById('proveedor_id');
                        const option = new Option(`${proveedor.nombre} (${proveedor.documento})`, proveedor.id,
                            true, true);
                        select.add(option);

                        // Actualizar Select2
                        $('#proveedor_id').trigger('change');

                        // Cerrar modal y limpiar
                        bootstrap.Modal.getInstance(document.getElementById('modalNuevoProveedor')).hide();
                        limpiarFormularioProveedor();

                        Toastify({
                            text: "¡Proveedor creado y seleccionado!",
                            duration: 3000,
                            gravity: "top",
                            position: "center",
                            className: "bg-success"
                        }).showToast();
                    } else {
                        Toastify({
                            text: data.message || "Error al guardar el proveedor",
                            duration: 4000,
                            gravity: "top",
                            position: "center",
                            className: "bg-danger"
                        }).showToast();
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ri-save-line me-1"></i> Guardar Proveedor';
                    Toastify({
                        text: "Error al conectar con el servidor",
                        duration: 4000,
                        gravity: "top",
                        position: "center",
                        className: "bg-danger"
                    }).showToast();
                });
        });

        // Limpiar formulario de proveedor
        function limpiarFormularioProveedor() {
            $('#prov_tipo_documento').val(null).trigger('change');
            document.getElementById('prov_documento').value = '';
            document.getElementById('prov_nombre').value = '';
            document.getElementById('prov_telefono').value = '';
            document.getElementById('prov_email').value = '';
            document.getElementById('prov_direccion').value = '';
        }

        // Buscar por DNI (placeholder - implementar API)
        function buscarDNI() {
            const dni = document.getElementById('buscar_dni').value;
            if (dni.length !== 8) {
                Toastify({
                    text: "El DNI debe tener 8 dígitos",
                    duration: 3000,
                    className: "bg-warning"
                }).showToast();
                return;
            }

            Toastify({
                text: "Búsqueda DNI en desarrollo (manualmente por ahora)",
                duration: 4000,
                className: "bg-info"
            }).showToast();

            document.getElementById('tab-manual').click();
            $('#prov_tipo_documento').val('DNI').trigger('change');
            document.getElementById('prov_documento').value = dni;
        }

        // Buscar por RUC (placeholder - implementar API)
        function buscarRUC() {
            const ruc = document.getElementById('buscar_ruc').value;
            if (ruc.length !== 11) {
                Toastify({
                    text: "El RUC debe tener 11 dígitos",
                    duration: 3000,
                    className: "bg-warning"
                }).showToast();
                return;
            }

            Toastify({
                text: "Búsqueda RUC en desarrollo (manualmente por ahora)",
                duration: 4000,
                className: "bg-info"
            }).showToast();

            document.getElementById('tab-manual').click();
            $('#prov_tipo_documento').val('RUC').trigger('change');
            document.getElementById('prov_documento').value = ruc;
        }

        // Usar datos de DNI encontrados
        function usarDatosDNI() {
            if (datosEncontrados) {
                document.getElementById('tab-manual').click();
                document.getElementById('prov_tipo_documento').value = 'DNI';
                document.getElementById('prov_documento').value = datosEncontrados.dni || '';
                document.getElementById('prov_nombre').value = datosEncontrados.nombre || '';
            }
        }

        // Usar datos de RUC encontrados
        function usarDatosRUC() {
            if (datosEncontrados) {
                document.getElementById('tab-manual').click();
                document.getElementById('prov_tipo_documento').value = 'RUC';
                document.getElementById('prov_documento').value = datosEncontrados.ruc || '';
                document.getElementById('prov_nombre').value = datosEncontrados.razon_social || '';
                document.getElementById('prov_direccion').value = datosEncontrados.direccion || '';
            }
        }
    </script>
@endsection
