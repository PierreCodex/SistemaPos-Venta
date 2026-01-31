@extends('layouts.master-without-nav')

@section('title')
    Punto de Venta (POS)
@endsection

@section('css')
    <style>
        /* Contenedores con scroll optimizado */
        .pos-product-grid {
            height: calc(100vh - 220px);
            overflow-y: auto;
            padding-right: 5px;
        }

        .pos-product-grid::-webkit-scrollbar {
            width: 5px;
        }

        .pos-product-grid::-webkit-scrollbar-track {
            background: transparent;
        }

        .pos-product-grid::-webkit-scrollbar-thumb {
            background: #e2e5ec;
            border-radius: 10px;
        }

        .pos-sidebar-scroll {
            height: calc(100vh - 460px);
            overflow-y: auto;
        }

        .pos-checkout-scroll {
            height: calc(100vh - 400px);
            overflow-y: auto;
            padding-bottom: 20px;
        }

        /* Efectos de tarjetas de producto */
        .product-item {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .product-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .product-item:hover .add-btn {
            background-color: var(--vz-primary) !important;
            color: #fff !important;
        }

        /* Categorías Estilo Pill */
        .category-filter {
            white-space: nowrap;
            overflow-x: auto;
            padding-bottom: 10px;
        }

        .category-filter::-webkit-scrollbar {
            display: none;
        }

        /* Gestión de vistas en sidebar */
        .sidebar-view {
            display: none;
        }

        .sidebar-view.active {
            display: flex;
            flex-direction: column;
            animation: slideInRight 0.3s ease;
        }

        /* Estilo circular para tipo doc */
        .doc-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            border: 1px solid var(--vz-border-color);
            color: var(--vz-muted);
            cursor: pointer;
            transition: all 0.2s;
        }

        .doc-circle.active,
        .btn-check:checked+.doc-circle {
            background-color: var(--vz-primary);
            border-color: var(--vz-primary);
            color: white;
            box-shadow: var(--vz-box-shadow-sm);
        }

        /* Carrito vacío */
        .cart-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--vz-muted);
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
@endsection

@section('content')
    <!-- Header POS -->
    <div class="row align-items-center mb-4 pt-3 text-uppercase">
        <div class="col">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('ventas.index') }}"
                    class="btn btn-primary d-flex align-items-center justify-content-center p-0"
                    style="width: 45px; height: 45px; border-radius: 12px; background-color: #0ab39c; border-color: #0ab39c;">
                    <i class="ri-arrow-left-s-line fs-24"></i>
                </a>
                <button class="btn btn-soft-light d-flex align-items-center justify-content-center p-0"
                    style="width: 45px; height: 45px; border-radius: 12px;" onclick="toggleFullScreen()">
                    <i class="ri-fullscreen-line fs-20 text-white"></i>
                </button>
            </div>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary d-flex align-items-center justify-content-center p-0 shadow-none border-0"
                style="width: 45px; height: 45px; border-radius: 12px; background-color: #0ab39c;">
                <i class="ri-printer-line fs-20"></i>
            </button>
        </div>
    </div>

    <div class="row text-uppercase">
        <!-- COLUMNA PRODUCTOS -->
        <div class="col-xl-8 col-lg-7 text-uppercase">

            <!-- Barra Superior: Búsqueda y Filtros -->
            <div class="card material-shadow border-0 mb-3 text-uppercase">
                <div class="card-body p-3">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-5">
                            <div class="search-box">
                                <input type="text" id="buscarProducto" class="form-control border-light bg-light"
                                    placeholder="🔍 BUSCAR PRODUCTO O ESCANEAR...">
                                <i class="ri-search-line search-icon text-muted"></i>
                                <div class="position-absolute end-0 top-0 mt-2 me-3">
                                    <i class="ri-barcode-line fs-18 text-primary"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7 text-uppercase">
                            <div class="category-filter d-flex gap-2">
                                <button class="btn btn-primary rounded-pill btn-sm px-3 btn-categoria active"
                                    data-id="0">
                                    TODAS
                                </button>
                                @foreach ($categorias as $cat)
                                    <button
                                        class="btn btn-soft-secondary rounded-pill btn-sm px-3 text-uppercase btn-categoria"
                                        data-id="{{ $cat->id }}">
                                        {{ strtoupper($cat->nombre) }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid de Productos -->
            <div class="row g-3 pos-product-grid text-uppercase" id="productosGrid">
                @foreach ($productos as $prod)
                    <div class="col-xxl-3 col-xl-4 col-sm-6 producto-card" data-id="{{ $prod->id }}"
                        data-nombre="{{ $prod->nombre }}" data-precio="{{ $prod->precio_venta }}"
                        data-stock="{{ $prod->stock }}" data-categoria="{{ $prod->categoria_id }}"
                        data-permite-decimales="{{ $prod->unidad->permite_decimales ?? 0 }}"
                        data-unidad-codigo="{{ $prod->unidad->codigo ?? '' }}">
                        <div class="card product-item material-shadow h-100 border-0 overflow-hidden text-uppercase"
                            onclick="validarAgregarProducto({{ $prod->id }}, '{{ addslashes($prod->nombre) }}', {{ $prod->precio_venta }}, {{ $prod->stock }}, {{ $prod->unidad->permite_decimales ?? 0 }}, '{{ $prod->unidad->codigo ?? '' }}')">
                            <div class="card-body p-0">
                                <div class="position-relative bg-light p-4 text-center">
                                    @if ($prod->imagen)
                                        <img src="{{ asset('storage/productos/' . $prod->imagen) }}" class="img-fluid"
                                            style="max-height: 60px;">
                                    @else
                                        <i class="ri-shopping-basket-line fs-1 text-primary-emphasis opacity-50"></i>
                                    @endif
                                    <span
                                        class="badge {{ $prod->stock <= $prod->stock_minimo ? 'bg-danger' : 'bg-success-subtle text-success' }} position-absolute top-0 start-0 m-2">
                                        STOCK:
                                        {{ $prod->unidad->permite_decimales ?? 0 ? number_format($prod->stock, 3) : number_format($prod->stock, 0) }}
                                        {{ $prod->unidad->codigo ?? '' }}
                                    </span>
                                </div>
                                <div class="p-3">
                                    <p class="text-muted text-uppercase mb-1 fs-11 fw-medium text-uppercase">
                                        {{ $prod->categoria->nombre ?? 'Sin categoría' }}
                                    </p>
                                    <h6 class="fs-14 mb-2 text-truncate text-uppercase">{{ $prod->nombre }}</h6>
                                    <div class="d-flex align-items-center justify-content-between text-uppercase">
                                        <h5 class="text-primary mb-0 fw-bold">S/
                                            {{ number_format($prod->precio_venta, 2) }} <small class="fs-10 text-muted">/
                                                {{ $prod->unidad->codigo ?? '' }}</small></h5>
                                        <button class="btn btn-sm btn-soft-primary add-btn btn-icon">
                                            <i class="ri-add-line fs-16"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- COLUMNA RESUMEN / COMPRA -->
        <div class="col-xl-4 col-lg-5 text-uppercase">
            <div class="card material-shadow border-0 h-100 d-flex flex-column mb-0 text-uppercase">

                <!-- VISTA 1: LISTADO DE PRODUCTOS EN CARRITO -->
                <div id="view-cart" class="sidebar-view active h-100">
                    <div class="card-header bg-transparent border-bottom-dashed py-3 text-uppercase">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1 fw-bold">
                                <i class="ri-shopping-cart-2-fill text-primary align-bottom me-2"></i>
                                RESUMEN DE VENTA
                                <span class="badge bg-primary ms-2" id="cantidadItems">0</span>
                            </h5>
                            <button class="btn btn-soft-danger btn-sm p-1 px-2 fs-12" onclick="limpiarCarrito()">
                                <i class="ri-delete-bin-line me-1"></i>LIMPIAR
                            </button>
                        </div>
                    </div>

                    <div class="card-body p-0 flex-grow-1 overflow-hidden text-uppercase">

                        <!-- Lista Scroll -->
                        <div class="pos-sidebar-scroll text-uppercase text-nowrap" id="carritoContainer">
                            <!-- Carrito vacío -->
                            <div class="cart-empty py-5" id="carritoVacio">
                                <i class="ri-shopping-cart-line fs-1 mb-3"></i>
                                <p>Agrega productos al carrito</p>
                            </div>

                            <!-- Tabla de items -->
                            <table class="table align-middle mb-0 d-none" id="tablaCarrito">
                                <thead class="table-light fs-11 text-uppercase text-nowrap">
                                    <tr>
                                        <th class="ps-3 py-2">PRODUCTO</th>
                                        <th class="text-center py-2">CANT.</th>
                                        <th class="text-end pe-3 py-2">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody id="carritoBody">
                                    <!-- Items dinámicos -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Footer del Carrito -->
                    <div class="card-footer bg-light-subtle border-top-dashed p-4 mt-auto text-uppercase">
                        <div class="d-flex align-items-center justify-content-between mb-4 border-top pt-3 text-uppercase">
                            <h4 class="mb-0 fw-bold">TOTAL</h4>
                            <h3 class="text-primary mb-0 fw-extrabold" id="totalCarrito">S/ 0.00</h3>
                        </div>
                        <button class="btn btn-success w-100 py-3 fw-bold fs-16 shadow-success text-uppercase"
                            id="btnRealizarCobro" disabled data-bs-toggle="modal" data-bs-target="#modalComprobante">
                            REALIZAR COBRO <i class="ri-arrow-right-line ms-1 align-middle"></i>
                        </button>
                    </div>
                </div>

                <!-- VISTA 2: PROCESO DE COBRO (CHECKOUT) -->
                <div id="view-checkout" class="sidebar-view h-100 mt-auto text-uppercase">
                    <div class="card-header bg-transparent border-bottom-dashed py-3 text-uppercase">
                        <div class="d-flex align-items-center">
                            <button class="btn btn-icon btn-sm btn-ghost-secondary me-2 p-0"
                                onclick="toggleSidebarView('cart')">
                                <i class="ri-arrow-left-line fs-20"></i>
                            </button>
                            <h5 class="card-title mb-0 fw-bold text-uppercase">
                                MONTO A COBRAR - <span id="montoACobrar">S/ 0.00</span>
                            </h5>
                        </div>
                    </div>

                    <div class="card-body p-4 pos-checkout-scroll text-uppercase">
                        <!-- Tipo de doc -->
                        <div class="mb-3 text-uppercase">
                            <p class="text-muted fs-11 fw-bold mb-2">TIPO DE DOC:</p>
                            <div class="d-flex gap-2">
                                <input type="radio" class="btn-check" name="payDoc" id="payNV" value="TICKET">
                                <label class="doc-circle" for="payNV">NV</label>

                                <input type="radio" class="btn-check" name="payDoc" id="payB" value="BOLETA"
                                    checked>
                                <label class="doc-circle" for="payB">B</label>

                                <input type="radio" class="btn-check" name="payDoc" id="payF" value="FACTURA">
                                <label class="doc-circle" for="payF">F</label>
                            </div>
                        </div>

                        <!-- Buscar cliente -->
                        <div class="mb-3 text-uppercase">
                            <div class="search-box">
                                <input type="text" id="clienteCheckout" class="form-control bg-light border-0"
                                    placeholder="CLIENTE REGISTRADO">
                                <i class="ri-user-search-line search-icon"></i>
                            </div>
                            <input type="hidden" id="clienteIdCheckout" value="">
                        </div>

                        <!-- Con nombre generico -->
                        <div class="mb-3 text-uppercase">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="conNombreGenerico">
                                <label class="form-check-label" for="conNombreGenerico">
                                    CLIENTE NUEVO
                                </label>
                            </div>
                        </div>

                        <!-- Al activar el checkbox se habilita el campo para ingresar un nombre al cliente -->
                        <div class="mb-3 text-uppercase d-none" id="containerNombreGenerico">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0">
                                    <i class="ri-user-line text-muted"></i>
                                </span>
                                <input type="text" id="clienteGenericoCheckout" class="form-control bg-light border-0"
                                    placeholder="NOMBRE DEL CLIENTE NUEVO">
                            </div>
                        </div>



                        <!-- Método de pago -->
                        <div class="mb-3 text-uppercase">
                            <p class="text-muted fs-11 fw-bold mb-2">MÉTODO DE PAGO:</p>
                            <select class="form-select border-light" id="metodoPago">
                                <option value="EFECTIVO" selected>💵 EFECTIVO</option>
                                <option value="TARJETA">💳 TARJETA</option>
                                <option value="YAPE">📱 YAPE</option>
                                <option value="PLIN">📱 PLIN</option>
                                <option value="TRANSFERENCIA">🏦 TRANSFERENCIA</option>
                            </select>
                        </div>

                        <!-- Monto recibido -->
                        <div class="d-flex align-items-center mb-3 text-uppercase">
                            <label class="form-label fw-bold mb-0">MONTO RECIBIDO</label>
                            <input type="number" id="montoRecibido"
                                class="form-control text-end ms-auto border-light fw-bold"
                                style="width: 150px; height: 45px; font-size: 18px;" value="0" step="0.01">
                        </div>

                        <!-- Botones de Monto Rápido -->
                        <div class="row g-2 mb-4 text-uppercase">
                            @foreach ([5, 10, 20, 50, 100, 200] as $m)
                                <div class="col">
                                    <button class="btn btn-outline-primary w-100 p-2 fs-13 fw-bold btn-monto"
                                        data-monto="{{ $m }}">{{ $m }}</button>
                                </div>
                            @endforeach
                        </div>

                        <!-- Vuelto -->
                        <div class="d-flex align-items-center mb-3 text-uppercase" id="containerVuelto">
                            <span class="text-muted fw-bold">VUELTO</span>
                            <input type="text" id="vueltoDisplay"
                                class="form-control text-end ms-auto border-light fw-bold bg-light"
                                style="width: 150px; height: 45px; font-size: 18px;" value="S/ 0.00" readonly>
                        </div>

                        <hr class="border-top-dashed">

                        <!-- Checkbox Venta a Crédito -->
                        <div class="mb-3 text-uppercase">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="esCredito">
                                <label class="form-check-label fw-bold text-warning" for="esCredito">
                                    <i class="ri-hand-coin-line me-1"></i> VENTA A CRÉDITO (FIADO)
                                </label>
                            </div>
                        </div>

                        <!-- Campos de Crédito (ocultos por defecto) -->
                        <div class="d-none" id="containerCredito">
                            <!-- Monto Inicial (A cuenta) -->
                            <div class="mb-3 text-uppercase">
                                <label class="form-label text-muted fs-12 fw-bold">MONTO INICIAL (A CUENTA) *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-warning text-dark fw-bold">S/</span>
                                    <input type="number" id="montoInicial"
                                        class="form-control border-light fw-bold text-end" style="font-size: 18px;"
                                        value="0" step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>

                            <!-- Saldo Pendiente (Badge) -->
                            <div class="alert alert-danger d-flex align-items-center justify-content-between mb-3 py-2"
                                id="alertaSaldoPendiente">
                                <div>
                                    <i class="ri-error-warning-line me-1"></i>
                                    <span class="fw-bold">SALDO PENDIENTE:</span>
                                </div>
                                <span class="badge bg-danger fs-14 px-3 py-2" id="saldoPendienteDisplay">S/ 0.00</span>
                            </div>

                            <!-- Fecha de Vencimiento del Crédito -->
                            <div class="mb-3 text-uppercase">
                                <label class="form-label text-muted fs-12 fw-bold">FECHA LÍMITE DE PAGO</label>
                                <input type="date" id="fechaVencimientoCredito" class="form-control border-light"
                                    min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <div class="mb-3 text-uppercase">
                            <label class="form-label text-muted fs-12 fw-bold text-uppercase">OBSERVACIONES</label>
                            <textarea class="form-control border-light" id="observaciones" rows="2"
                                placeholder="Observaciones opcionales..."></textarea>
                        </div>
                    </div>

                    <!-- Footer Checkout -->
                    <div class="card-footer bg-transparent border-top-dashed p-4 mt-auto text-uppercase">
                        <div class="d-flex align-items-center justify-content-between mb-4 text-uppercase">
                            <span class="text-dark fw-bold fs-14">DESCUENTO GENERAL</span>
                            <div class="d-flex align-items-center gap-1 text-uppercase">
                                <div class="bg-success text-white px-2 py-1 rounded fw-bold fs-14">S/</div>
                                <input type="number" id="descuentoGeneral" class="form-control text-center border-light"
                                    style="width: 80px;" value="0" step="0.01">
                            </div>
                        </div>
                        <button class="btn btn-primary w-100 py-3 fw-bold fs-16 rounded-3 shadow-lg text-uppercase"
                            id="btnConfirmarVenta" onclick="confirmarVenta()">
                            ACEPTAR Y CREAR
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL SELECCION COMPROBANTE -->
    <div class="modal fade" id="modalComprobante" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered text-uppercase">
            <div class="modal-content border-0 overflow-hidden shadow-lg text-uppercase">
                <div class="modal-header bg-primary p-3 text-uppercase">
                    <h5 class="modal-title text-white fw-bold text-uppercase text-nowrap">📝 SELECCIONE COMPROBANTE</h5>
                    <button type="button" class="btn-close btn-close-white text-uppercase" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light text-uppercase">
                    <div class="row g-3 text-uppercase">
                        <div class="col-12 text-uppercase">
                            <div class="card border border-2 border-dashed text-center p-3 cursor-pointer mb-0 product-item text-uppercase"
                                onclick="goToCheckout('TICKET')">
                                <div class="avatar-sm mx-auto mb-3 text-uppercase">
                                    <div
                                        class="avatar-title bg-primary-subtle text-primary rounded-circle fs-24 text-uppercase">
                                        <i class="ri-file-list-3-line"></i>
                                    </div>
                                </div>
                                <h6 class="fw-bold mb-1 text-uppercase text-nowrap">NOTA DE VENTA</h6>
                            </div>
                        </div>
                        <div class="col-12 text-uppercase">
                            <div class="card border border-2 border-dashed text-center p-3 cursor-pointer mb-0 product-item text-uppercase"
                                onclick="goToCheckout('BOLETA')">
                                <div class="avatar-sm mx-auto mb-3 text-uppercase">
                                    <div
                                        class="avatar-title bg-success-subtle text-success rounded-circle fs-24 text-uppercase">
                                        <i class="ri-file-text-line"></i>
                                    </div>
                                </div>
                                <h6 class="fw-bold mb-1 text-uppercase text-nowrap">BOLETA ELECTRÓNICA</h6>
                            </div>
                        </div>
                        <div class="col-12 text-uppercase">
                            <div class="card border border-2 border-dashed text-center p-3 cursor-pointer mb-0 product-item text-uppercase"
                                onclick="goToCheckout('FACTURA')">
                                <div class="avatar-sm mx-auto mb-3 text-uppercase">
                                    <div class="avatar-title bg-info-subtle text-info rounded-circle fs-24 text-uppercase">
                                        <i class="ri-building-line text-uppercase"></i>
                                    </div>
                                </div>
                                <h6 class="fw-bold mb-1 text-uppercase text-nowrap">FACTURA ELECTRÓNICA</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DESCARGA DE COMPROBANTE -->
    <div class="modal fade" id="modalDescargaComprobante" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 overflow-hidden shadow-lg">
                <div class="modal-header bg-success p-4 border-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-md">
                            <div class="avatar-title bg-white text-success rounded-circle fs-24">
                                <i class="ri-checkbox-circle-line"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="modal-title text-white fw-bold mb-1 text-uppercase">¡VENTA EXITOSA!</h5>
                            <p class="text-white-50 mb-0 fs-13" id="comprobanteNumero">Comprobante generado</p>
                        </div>
                    </div>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="text-center mb-4">
                        <p class="text-muted mb-2 fs-14">Descargue su comprobante en el formato deseado:</p>
                        <h6 class="text-dark fw-bold mb-0" id="tipoComprobanteTexto">BOLETA ELECTRÓNICA</h6>
                    </div>

                    <!-- Opciones de descarga -->
                    <div class="row g-3">
                        <!-- 50mm -->
                        <div class="col-12">
                            <a href="#" id="btnDescargar50mm"
                                class="btn btn-outline-primary w-100 py-3 d-flex align-items-center justify-content-between"
                                target="_blank">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-sm">
                                        <div class="avatar-title bg-primary-subtle text-primary rounded fs-20">
                                            <i class="ri-file-text-line"></i>
                                        </div>
                                    </div>
                                    <div class="text-start">
                                        <h6 class="mb-0 fw-bold text-uppercase">Ticket 50mm</h6>
                                        <small class="text-muted">Impresora térmica pequeña</small>
                                    </div>
                                </div>
                                <i class="ri-download-2-line fs-20"></i>
                            </a>
                        </div>

                        <!-- 80mm -->
                        <div class="col-12">
                            <a href="#" id="btnDescargar80mm"
                                class="btn btn-primary w-100 py-3 d-flex align-items-center justify-content-between"
                                target="_blank">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-sm">
                                        <div class="avatar-title bg-white text-primary rounded fs-20">
                                            <i class="ri-file-text-line"></i>
                                        </div>
                                    </div>
                                    <div class="text-start">
                                        <h6 class="mb-0 fw-bold text-white text-uppercase">Ticket 80mm</h6>
                                        <small class="text-white-50">Impresora térmica estándar (Recomendado)</small>
                                    </div>
                                </div>
                                <i class="ri-download-2-line fs-20 text-white"></i>
                            </a>
                        </div>

                        <!-- A4 -->
                        <div class="col-12">
                            <a href="#" id="btnDescargarA4"
                                class="btn btn-outline-primary w-100 py-3 d-flex align-items-center justify-content-between"
                                target="_blank">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-sm">
                                        <div class="avatar-title bg-primary-subtle text-primary rounded fs-20">
                                            <i class="ri-file-pdf-line"></i>
                                        </div>
                                    </div>
                                    <div class="text-start">
                                        <h6 class="mb-0 fw-bold text-uppercase">Formato A4</h6>
                                        <small class="text-muted">Hoja tamaño carta</small>
                                    </div>
                                </div>
                                <i class="ri-download-2-line fs-20"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="alert alert-info border-0 mt-4 mb-0">
                        <div class="d-flex align-items-start gap-2">
                            <i class="ri-information-line fs-18 mt-1"></i>
                            <div class="flex-grow-1">
                                <p class="mb-1 fw-semibold">Información importante:</p>
                                <ul class="mb-0 ps-3 fs-13">
                                    <li>Los comprobantes se descargarán automáticamente</li>
                                    <li id="infoElectronico" class="d-none">Este comprobante fue enviado a SUNAT</li>
                                    <li id="infoNotaVenta" class="d-none">Esta nota de venta no es válida como comprobante
                                        de pago</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top-dashed">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" onclick="resetearPOS()">
                        <i class="ri-close-line me-1"></i> Cerrar
                    </button>
                    <button type="button" class="btn btn-success px-4" onclick="nuevaVenta()">
                        <i class="ri-add-line me-1"></i> Nueva Venta
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL VENTA POR PESO / IMPORTE (KG) -->
    <div class="modal fade" id="modalVentaPeso" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary p-3">
                    <h5 class="modal-title text-white fw-bold" id="modalVentaPesoTitle">⚖️ VENTA POR PESO</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <h4 class="fw-bold mb-1" id="pesoProductoNombre">-</h4>
                        <span class="badge bg-success-subtle text-success fs-13">PRECIO UNITARIO: S/ <span
                                id="pesoProductoPrecio">0.00</span></span>
                    </div>

                    <div class="row g-3">
                        <!-- Campo Importe (Dinero) -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-muted fs-12 uppercase">IMPORTE QUE PAGA (S/)</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-primary text-primary fw-bold">S/</span>
                                <input type="number" id="inputVentaImporte"
                                    class="form-control border-primary fw-bold text-end" placeholder="0.00"
                                    step="0.01">
                            </div>
                            <small class="text-muted italic">Ingrese cuánto dinero va a pagar el cliente.</small>
                        </div>

                        <div class="col-12 text-center py-2">
                            <div class="avatar-xs mx-auto">
                                <div class="avatar-title bg-light text-primary rounded-circle">
                                    <i class="ri-arrow-up-down-line fs-16"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Campo Peso (KG) -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-muted fs-12 uppercase">PESO / CANTIDAD (KG)</label>
                            <div class="input-group input-group-lg">
                                <input type="number" id="inputVentaPeso"
                                    class="form-control border-success fw-bold text-end" placeholder="0.000"
                                    step="0.001">
                                <span class="input-group-text bg-light border-success text-success fw-bold">KG</span>
                            </div>
                            <small class="text-muted italic">Ingrese el peso marcado en la balanza.</small>
                        </div>
                    </div>

                    <div class="alert alert-info mt-4 mb-0 border-0 shadow-sm d-flex align-items-center">
                        <i class="ri-information-line fs-20 me-2 text-info"></i>
                        <div>
                            <p class="mb-0 fs-12">La cantidad se calculará automáticamente según el precio de venta del
                                producto.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">CANCELAR</button>
                    <button type="button" class="btn btn-primary px-4 fw-bold" id="btnAceptarVentaPeso">
                        AGREGAR AL CARRITO <i class="ri-add-line ms-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // =====================================================
        // CONFIGURACIÓN Y ESTADO
        // =====================================================
        const ROUTES = {
            store: '{{ route('ventas.store') }}',
            buscarProducto: '{{ route('ventas.buscar-producto') }}',
            buscarCliente: '{{ route('ventas.buscar-cliente') }}',
            buscarCodigoBarras: '{{ route('ventas.buscar-codigo-barras') }}',
            productosPorCategoria: '{{ url('ventas/api/productos-categoria') }}'
        };

        const CSRF_TOKEN = '{{ csrf_token() }}';

        // Estado del carrito
        let carrito = [];
        let totalCarrito = 0;
        let clienteSeleccionado = null;
        let productoEnEdicionPeso = null; // Para el modal de peso

        // =====================================================
        // FUNCIONES DE VALIDACIÓN Y MODAL DE PESO
        // =====================================================

        /**
         * Decide si agregar directo o mostrar modal de peso
         */
        function validarAgregarProducto(id, nombre, precio, stock, permiteDecimales, unidadCodigo) {
            if (['KG', 'GR', 'LTR', 'ML'].includes(unidadCodigo)) {
                abrirModalPeso(id, nombre, precio, stock, unidadCodigo);
            } else {
                agregarAlCarrito(id, nombre, precio, stock, 1, permiteDecimales, unidadCodigo);
            }
        }

        /**
         * Configura y abre el modal de peso
         */
        function abrirModalPeso(id, nombre, precio, stock, unidadCodigo) {
            productoEnEdicionPeso = {
                id,
                nombre,
                precio,
                stock,
                unidadCodigo
            };

            document.getElementById('pesoProductoNombre').textContent = nombre;
            document.getElementById('pesoProductoPrecio').textContent = precio.toFixed(2);
            document.getElementById('modalVentaPesoTitle').textContent = `⚖️ VENTA POR ${unidadCodigo}`;

            // Limpiar inputs
            const inputImporte = document.getElementById('inputVentaImporte');
            const inputPeso = document.getElementById('inputVentaPeso');
            inputImporte.value = '';
            inputPeso.value = '';

            // Ajustar label de unidad
            inputPeso.nextElementSibling.textContent = unidadCodigo;

            const modal = new bootstrap.Modal(document.getElementById('modalVentaPeso'));
            modal.show();

            setTimeout(() => inputImporte.focus(), 500);
        }

        // Lógica de cálculo inverso en el modal
        document.getElementById('inputVentaImporte').addEventListener('input', function() {
            if (!productoEnEdicionPeso) return;
            const importe = parseFloat(this.value) || 0;
            const precio = productoEnEdicionPeso.precio;

            if (importe > 0 && precio > 0) {
                const pesoCalculado = (importe / precio).toFixed(3);
                document.getElementById('inputVentaPeso').value = pesoCalculado;
            } else {
                document.getElementById('inputVentaPeso').value = '';
            }
        });

        document.getElementById('inputVentaPeso').addEventListener('input', function() {
            if (!productoEnEdicionPeso) return;
            const peso = parseFloat(this.value) || 0;
            const precio = productoEnEdicionPeso.precio;

            if (peso > 0 && precio > 0) {
                const importeCalculado = (peso * precio).toFixed(2);
                document.getElementById('inputVentaImporte').value = importeCalculado;
            } else {
                document.getElementById('inputVentaImporte').value = '';
            }
        });

        /**
         * Acepta la venta por peso y agrega al carrito
         */
        document.getElementById('btnAceptarVentaPeso').addEventListener('click', function() {
            if (!productoEnEdicionPeso) return;

            const cantidad = parseFloat(document.getElementById('inputVentaPeso').value) || 0;

            if (cantidad <= 0) {
                mostrarToast('Ingrese una cantidad válida', 'warning');
                return;
            }

            if (cantidad > productoEnEdicionPeso.stock) {
                mostrarToast('Stock insuficiente', 'warning');
                return;
            }

            agregarAlCarrito(
                productoEnEdicionPeso.id,
                productoEnEdicionPeso.nombre,
                productoEnEdicionPeso.precio,
                productoEnEdicionPeso.stock,
                cantidad,
                1, // permiteDecimales
                productoEnEdicionPeso.unidadCodigo
            );

            bootstrap.Modal.getInstance(document.getElementById('modalVentaPeso')).hide();
        });

        // =====================================================
        // FUNCIONES DEL CARRITO
        // =====================================================

        /**
         * Agrega un producto al carrito o incrementa cantidad si ya existe
         */
        function agregarAlCarrito(id, nombre, precio, stockDisponible, cantidad = 1, permiteDecimales = 0, unidadCodigo =
            'UND') {
            const existente = carrito.find(item => item.id === id);

            if (existente) {
                const nuevaCantidad = existente.cantidad + cantidad;
                if (nuevaCantidad <= stockDisponible) {
                    existente.cantidad = nuevaCantidad;
                    existente.subtotal = existente.cantidad * existente.precio;
                } else {
                    mostrarToast('Stock insuficiente', 'warning');
                    return;
                }
            } else {
                carrito.push({
                    id: id,
                    nombre: nombre,
                    precio: precio,
                    cantidad: cantidad,
                    subtotal: cantidad * precio,
                    stockDisponible: stockDisponible,
                    permiteDecimales: permiteDecimales,
                    unidadCodigo: unidadCodigo
                });
            }

            actualizarVistaCarrito();
            reproducirSonido();
        }

        /**
         * Actualiza la cantidad de un item en el carrito
         */
        function actualizarCantidad(id, cambio) {
            const item = carrito.find(i => i.id === id);
            if (!item) return;

            // Si no permite decimales, el cambio siempre es entero
            // Si permite decimales, el cambio es +1 o -1, pero podemos permitir ingreso manual si quisiéramos
            const nuevaCantidad = item.cantidad + cambio;

            if (nuevaCantidad <= 0) {
                eliminarDelCarrito(id);
                return;
            }

            if (nuevaCantidad > item.stockDisponible) {
                mostrarToast('Stock insuficiente', 'warning');
                return;
            }

            item.cantidad = nuevaCantidad;
            item.subtotal = item.cantidad * item.precio;
            actualizarVistaCarrito();
        }

        /**
         * Elimina un item del carrito
         */
        function eliminarDelCarrito(id) {
            carrito = carrito.filter(item => item.id !== id);
            actualizarVistaCarrito();
        }

        /**
         * Limpia todo el carrito
         */
        function limpiarCarrito() {
            carrito = [];
            actualizarVistaCarrito();
        }

        /**
         * Actualiza la visualización del carrito
         */
        function actualizarVistaCarrito() {
            const tbody = document.getElementById('carritoBody');
            const tablaCarrito = document.getElementById('tablaCarrito');
            const carritoVacio = document.getElementById('carritoVacio');
            const cantidadItems = document.getElementById('cantidadItems');
            const totalDisplay = document.getElementById('totalCarrito');
            const btnCobro = document.getElementById('btnRealizarCobro');

            if (carrito.length === 0) {
                tablaCarrito.classList.add('d-none');
                carritoVacio.classList.remove('d-none');
                btnCobro.disabled = true;
            } else {
                tablaCarrito.classList.remove('d-none');
                carritoVacio.classList.add('d-none');
                btnCobro.disabled = false;
            }

            // Calcular total
            totalCarrito = carrito.reduce((sum, item) => sum + item.subtotal, 0);

            // Renderizar items
            tbody.innerHTML = carrito.map(item => `
                <tr>
                    <td class="ps-3">
                        <h6 class="fs-13 mb-0 text-uppercase text-truncate" style="max-width: 150px;">${item.nombre}</h6>
                        <small class="text-muted text-nowrap">S/ ${item.precio.toFixed(2)} / ${item.unidadCodigo}</small>
                    </td>
                    <td style="width: 120px;">
                        <div class="d-flex align-items-center justify-content-center bg-light rounded p-1 text-uppercase">
                            <button class="btn btn-link btn-sm p-0 text-muted" onclick="actualizarCantidad(${item.id}, ${item.permiteDecimales ? -0.1 : -1})">
                                <i class="ri-subtract-line"></i>
                            </button>
                            <span class="mx-2 fw-medium">${item.permiteDecimales ? item.cantidad.toFixed(3) : item.cantidad}</span>
                            <button class="btn btn-link btn-sm p-0 text-muted" onclick="actualizarCantidad(${item.id}, ${item.permiteDecimales ? 0.1 : 1})">
                                <i class="ri-add-line"></i>
                            </button>
                        </div>
                    </td>
                    <td class="text-end pe-2 text-uppercase">
                        <span class="fw-bold text-primary">S/ ${item.subtotal.toFixed(2)}</span>
                        <button class="btn btn-link btn-sm p-0 text-danger ms-1" onclick="eliminarDelCarrito(${item.id})">
                            <i class="ri-close-line"></i>
                        </button>
                    </td>
                </tr>
            `).join('');

            // Actualizar contadores
            cantidadItems.textContent = carrito.length;
            totalDisplay.textContent = `S/ ${totalCarrito.toFixed(2)}`;
        }

        // =====================================================
        // FUNCIONES DEL CHECKOUT
        // =====================================================

        function goToCheckout(tipo) {
            const modalEl = document.getElementById('modalComprobante');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

            // Seleccionar el radio button correspondiente
            if (tipo === 'TICKET') document.getElementById('payNV').checked = true;
            if (tipo === 'BOLETA') document.getElementById('payB').checked = true;
            if (tipo === 'FACTURA') document.getElementById('payF').checked = true;

            // Actualizar monto a cobrar
            document.getElementById('montoACobrar').textContent = `S/ ${totalCarrito.toFixed(2)}`;
            document.getElementById('montoRecibido').value = totalCarrito.toFixed(2);
            calcularVuelto();

            toggleSidebarView('checkout');
        }

        function toggleSidebarView(view) {
            const views = document.querySelectorAll('.sidebar-view');
            views.forEach(v => v.classList.remove('active'));

            const targetView = document.getElementById('view-' + view);
            if (targetView) {
                targetView.classList.add('active');
            }
        }

        function calcularVuelto() {
            const montoRecibido = parseFloat(document.getElementById('montoRecibido').value) || 0;
            const descuento = parseFloat(document.getElementById('descuentoGeneral').value) || 0;
            const totalAPagar = totalCarrito - descuento;
            const vuelto = Math.max(0, montoRecibido - totalAPagar);
            document.getElementById('vueltoDisplay').value = `S/ ${vuelto.toFixed(2)}`;
        }

        /**
         * Calcula el saldo pendiente para ventas a crédito
         */
        function calcularSaldoPendiente() {
            const descuento = parseFloat(document.getElementById('descuentoGeneral').value) || 0;
            const totalAPagar = totalCarrito - descuento;
            const montoInicial = parseFloat(document.getElementById('montoInicial').value) || 0;
            const saldoPendiente = Math.max(0, totalAPagar - montoInicial);

            document.getElementById('saldoPendienteDisplay').textContent = `S/ ${saldoPendiente.toFixed(2)}`;

            // Cambiar color según el saldo
            const badge = document.getElementById('saldoPendienteDisplay');
            const alerta = document.getElementById('alertaSaldoPendiente');

            if (saldoPendiente === 0) {
                badge.classList.remove('bg-danger');
                badge.classList.add('bg-success');
                alerta.classList.remove('alert-danger');
                alerta.classList.add('alert-success');
            } else {
                badge.classList.remove('bg-success');
                badge.classList.add('bg-danger');
                alerta.classList.remove('alert-success');
                alerta.classList.add('alert-danger');
            }
        }

        /**
         * Confirma y registra la venta
         */
        async function confirmarVenta() {
            if (carrito.length === 0) {
                mostrarToast('Agrega productos al carrito', 'warning');
                return;
            }

            const comprobante = document.querySelector('input[name="payDoc"]:checked').value;
            const metodoPago = document.getElementById('metodoPago').value;
            const descuento = parseFloat(document.getElementById('descuentoGeneral').value) || 0;
            const observaciones = document.getElementById('observaciones').value;

            // Cliente: puede ser por ID (registrado) o por nombre genérico
            const usaNombreGenerico = document.getElementById('conNombreGenerico').checked;
            const clienteId = usaNombreGenerico ? null : (document.getElementById('clienteIdCheckout').value || null);
            const nombreClienteGenerico = usaNombreGenerico ? document.getElementById('clienteGenericoCheckout').value
                .trim() : null;

            // Crédito
            const esCredito = document.getElementById('esCredito').checked;
            const montoInicial = esCredito ? (parseFloat(document.getElementById('montoInicial').value) || 0) : 0;
            const fechaVencimientoCredito = esCredito ? document.getElementById('fechaVencimientoCredito').value : null;

            const totalAPagar = parseFloat((totalCarrito - descuento).toFixed(2));

            // En crédito, el monto recibido es el monto inicial
            const montoRecibidoInput = parseFloat(document.getElementById('montoRecibido').value) || 0;
            const montoRecibido = esCredito ? montoInicial : montoRecibidoInput;
            const saldoPendiente = esCredito ? Math.max(0, totalAPagar - montoInicial) : 0;

            // Validación de monto solo si no es crédito
            // Usamos un pequeño margen para evitar errores de precisión decimal
            if (!esCredito && (totalAPagar - montoRecibido) > 0.01 && metodoPago === 'EFECTIVO') {
                mostrarToast('El monto recibido es insuficiente', 'error');
                return;
            }

            // Validación de crédito: debe tener cliente o nombre genérico
            if (esCredito && !clienteId && !nombreClienteGenerico) {
                mostrarToast('Para venta a crédito debe especificar un cliente', 'warning');
                return;
            }

            // Preparar detalles
            const detalles = carrito.map(item => ({
                producto_id: item.id,
                cantidad: item.cantidad,
                precio_unitario: item.precio,
                descuento: 0
            }));

            const datos = {
                comprobante: comprobante,
                cliente_id: clienteId,
                nombre_cliente_generico: nombreClienteGenerico,
                metodo_pago: esCredito ? 'CREDITO' : metodoPago,
                monto_recibido: montoRecibido,
                descuento: descuento,
                observaciones: observaciones,
                es_credito: esCredito,
                saldo_pendiente: saldoPendiente,
                fecha_vencimiento_credito: fechaVencimientoCredito,
                estado_pago: esCredito ? (saldoPendiente > 0 ? 'PENDIENTE' : 'PAGADO') : 'PAGADO',
                detalles: detalles
            };

            // Deshabilitar botón mientras procesa
            const btnConfirmar = document.getElementById('btnConfirmarVenta');
            btnConfirmar.disabled = true;
            btnConfirmar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';

            try {
                const response = await fetch(ROUTES.store, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify(datos)
                });

                const result = await response.json();

                if (result.success) {
                    // Audio de confirmación
                    const confirmAudio = new Audio('{{ URL::asset('mp3/sfx-menu6.mp3') }}');
                    confirmAudio.play().catch(e => console.log("Audio play blocked by browser"));

                    // Mostrar modal de descarga si hay comprobante electrónico
                    if (result.tiene_comprobante && result.pdf_urls) {
                        // Configurar URLs de descarga
                        document.getElementById('btnDescargar50mm').href = result.pdf_urls['50mm'];
                        document.getElementById('btnDescargar80mm').href = result.pdf_urls['80mm'];
                        document.getElementById('btnDescargarA4').href = result.pdf_urls['a4'];

                        // Configurar información del comprobante
                        document.getElementById('comprobanteNumero').textContent = result.comprobante;
                        document.getElementById('tipoComprobanteTexto').textContent = result.tipo_comprobante ||
                            'COMPROBANTE';

                        // Mostrar/ocultar información según tipo
                        const esElectronico = result.tipo_comprobante && result.tipo_comprobante.includes(
                            'ELECTRÓNICA');
                        document.getElementById('infoElectronico').classList.toggle('d-none', !esElectronico);
                        document.getElementById('infoNotaVenta').classList.toggle('d-none', esElectronico);

                        // Mostrar modal
                        const modalDescarga = new bootstrap.Modal(document.getElementById('modalDescargaComprobante'));
                        modalDescarga.show();
                    } else {
                        // Fallback: mensaje tradicional si no hay comprobante
                        let htmlContent = `
                            <p class="mb-2">Comprobante: <strong>${result.comprobante}</strong></p>
                            <p class="mb-0">Total: <strong>S/ ${totalAPagar.toFixed(2)}</strong></p>
                        `;

                        if (esCredito && saldoPendiente > 0) {
                            htmlContent += `
                                <hr class="my-2">
                                <p class="mb-1 text-warning"><i class="ri-hand-coin-line me-1"></i><strong>VENTA A CRÉDITO</strong></p>
                                <p class="mb-1">Monto Inicial: <strong>S/ ${montoRecibido.toFixed(2)}</strong></p>
                                <p class="mb-0 text-danger">Saldo Pendiente: <strong>S/ ${saldoPendiente.toFixed(2)}</strong></p>
                            `;
                        }

                        Swal.fire({
                            icon: esCredito && saldoPendiente > 0 ? 'warning' : 'success',
                            title: esCredito && saldoPendiente > 0 ? '¡Venta a Crédito Registrada!' :
                                '¡Venta Registrada!',
                            html: htmlContent,
                            confirmButtonText: 'Nueva Venta',
                            confirmButtonColor: '#0ab39c'
                        }).then(() => {
                            resetearPOS();
                        });
                    }
                } else {
                    Swal.fire('Error', result.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'No se pudo procesar la venta', 'error');
                console.error(error);
            } finally {
                btnConfirmar.disabled = false;
                btnConfirmar.innerHTML = 'ACEPTAR Y CREAR';
            }
        }

        // =====================================================
        // FUNCIONES AUXILIARES
        // =====================================================

        function toggleFullScreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        function reproducirSonido() {
            try {
                const audioContext = new(window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                oscillator.frequency.value = 800;
                oscillator.type = 'sine';
                gainNode.gain.value = 0.1;
                oscillator.start();
                oscillator.stop(audioContext.currentTime + 0.05);
            } catch (e) {}
        }

        function mostrarToast(mensaje, tipo = 'success') {
            const colors = {
                success: "linear-gradient(to right, #0ab39c, #0ab39c)",
                error: "linear-gradient(to right, #f06548, #f06548)",
                warning: "linear-gradient(to right, #f7b84b, #f7b84b)"
            };

            // Audio de confirmación si es éxito
            if (tipo === 'success') {
                const confirmAudio = new Audio('{{ URL::asset('mp3/sfx-menu6.mp3') }}');
                confirmAudio.play().catch(e => console.log("Audio play blocked by browser"));
            }

            Toastify({
                text: mensaje,
                duration: 2000,
                gravity: "top",
                position: "right",
                style: {
                    background: colors[tipo] || colors.success
                }
            }).showToast();
        }

        // =====================================================
        // EVENT LISTENERS
        // =====================================================
        document.addEventListener('DOMContentLoaded', function() {
            // Filtrar por categoría (Frontend)
            document.querySelectorAll('.btn-categoria').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.btn-categoria').forEach(b => {
                        b.classList.remove('btn-primary', 'active');
                        b.classList.add('btn-soft-secondary');
                    });
                    this.classList.remove('btn-soft-secondary');
                    this.classList.add('btn-primary', 'active');

                    const catId = this.dataset.id;
                    document.querySelectorAll('.producto-card').forEach(card => {
                        if (catId === '0' || card.dataset.categoria === catId) {
                            card.classList.remove('d-none');
                        } else {
                            card.classList.add('d-none');
                        }
                    });
                });
            });

            // Búsqueda de productos (Frontend + Opción AJAX si el término es largo)
            let searchTimeout;
            document.getElementById('buscarProducto').addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const termino = this.value.toLowerCase().trim();

                searchTimeout = setTimeout(() => {
                    // Primero filtro frontend
                    let coincidencias = 0;
                    document.querySelectorAll('.producto-card').forEach(card => {
                        const nombre = card.dataset.nombre.toLowerCase();
                        if (nombre.includes(termino) || termino === '') {
                            card.classList.remove('d-none');
                            coincidencias++;
                        } else {
                            card.classList.add('d-none');
                        }
                    });

                    // Si no hay coincidencias frontend y hay más de 2 letras, buscar en BD
                    if (coincidencias === 0 && termino.length >= 2) {
                        buscarProductosBD(termino);
                    }
                }, 300);
            });

            // Búsqueda de clientes (Autocomplete simple) - solo en checkout
            document.getElementById('clienteCheckout').addEventListener('input', (e) => buscarClientesAJAX(e.target
                .value, 'clienteCheckout'));

            // Checkbox "Con nombre genérico" - mostrar/ocultar campo
            document.getElementById('conNombreGenerico').addEventListener('change', function() {
                const containerNombre = document.getElementById('containerNombreGenerico');
                const inputNombre = document.getElementById('clienteGenericoCheckout');
                const inputCliente = document.getElementById('clienteCheckout');

                if (this.checked) {
                    // Mostrar campo de nombre genérico
                    containerNombre.classList.remove('d-none');
                    inputNombre.focus();
                    // Deshabilitar búsqueda de cliente registrado
                    inputCliente.disabled = true;
                    inputCliente.value = '';
                    document.getElementById('clienteIdCheckout').value = '';
                } else {
                    // Ocultar campo de nombre genérico
                    containerNombre.classList.add('d-none');
                    inputNombre.value = '';
                    // Habilitar búsqueda de cliente registrado
                    inputCliente.disabled = false;
                }
            });

            // Checkbox "Venta a Crédito" - mostrar/ocultar campos de crédito
            document.getElementById('esCredito').addEventListener('change', function() {
                const containerCredito = document.getElementById('containerCredito');
                const containerVuelto = document.getElementById('containerVuelto');
                const montoInicial = document.getElementById('montoInicial');

                if (this.checked) {
                    // Mostrar campos de crédito
                    containerCredito.classList.remove('d-none');
                    // Ocultar vuelto (no aplica en crédito)
                    containerVuelto.classList.add('d-none');
                    // Foco en monto inicial
                    montoInicial.focus();
                    // Calcular saldo pendiente inicial
                    calcularSaldoPendiente();
                } else {
                    // Ocultar campos de crédito
                    containerCredito.classList.add('d-none');
                    // Mostrar vuelto
                    containerVuelto.classList.remove('d-none');
                    // Limpiar campos
                    montoInicial.value = 0;
                    document.getElementById('fechaVencimientoCredito').value = '';
                }
            });

            // Calcular saldo pendiente cuando cambia el monto inicial
            document.getElementById('montoInicial').addEventListener('input', calcularSaldoPendiente);
            document.getElementById('descuentoGeneral').addEventListener('input', function() {
                calcularVuelto();
                if (document.getElementById('esCredito').checked) {
                    calcularSaldoPendiente();
                }
            });

            // Escáner de código de barras (Enter key + Auto-procesado)
            let scannerTimer;
            document.getElementById('buscarProducto').addEventListener('input', function(e) {
                const codigo = this.value.trim();

                // Limpiar el temporizador anterior
                clearTimeout(scannerTimer);

                // Si el código parece ser de un escáner (largo y/o solo números)
                // O simplemente para que cualquier entrada finalice tras 150ms de silencio
                if (codigo.length >= 4) {
                    scannerTimer = setTimeout(() => {
                        console.log('Procesando escaneo automático:', codigo);
                        buscarPorCodigoBarras(codigo);
                        this.value = '';
                    }, 150); // 150ms es el tiempo estándar para detectar fin de ráfaga de escáner
                }
            });

            // Mantener el soporte para Enter por si el escáner sí lo envía o el usuario lo usa
            document.getElementById('buscarProducto').addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(scannerTimer);
                    const codigo = this.value.trim();
                    if (codigo) {
                        buscarPorCodigoBarras(codigo);
                        this.value = '';
                    }
                }
            });

            // Calcular vuelto en tiempo real
            document.getElementById('montoRecibido').addEventListener('input', calcularVuelto);
            document.getElementById('descuentoGeneral').addEventListener('input', calcularVuelto);

            // Botones de monto rápido
            document.querySelectorAll('.btn-monto').forEach(btn => {
                btn.addEventListener('click', function() {
                    const montoActual = parseFloat(document.getElementById('montoRecibido')
                        .value) || 0;
                    document.getElementById('montoRecibido').value = (montoActual + parseFloat(this
                        .dataset.monto)).toFixed(2);
                    calcularVuelto();
                });
            });

            // Foco inicial en el buscador de productos para el escáner
            document.getElementById('buscarProducto').focus();
        });

        // Búsqueda de productos en BD via AJAX
        async function buscarProductosBD(termino) {
            try {
                const response = await fetch(`${ROUTES.buscarProducto}?q=${encodeURIComponent(termino)}`);
                const result = await response.json();

                if (result.success && result.productos.length > 0) {
                    // Aquí podrías agregar dinámicamente cartas al grid si no existen
                    // Por simplicidad en este MVP, asumimos que los productos principales están cargados
                    console.log('Productos encontrados en BD:', result.productos);
                }
            } catch (error) {
                console.error('Error buscando productos:', error);
            }
        }

        // Búsqueda de clientes via AJAX
        let clienteTimeout;
        async function buscarClientesAJAX(termino, inputId) {
            clearTimeout(clienteTimeout);
            if (termino.length < 2) return;

            clienteTimeout = setTimeout(async () => {
                try {
                    const response = await fetch(
                        `${ROUTES.buscarCliente}?q=${encodeURIComponent(termino)}`);
                    const result = await response.json();

                    if (result.success && result.clientes.length > 0) {
                        mostrarResultadosClientes(result.clientes, inputId);
                    }
                } catch (error) {
                    console.error('Error buscando clientes:', error);
                }
            }, 300);
        }

        function mostrarResultadosClientes(clientes, inputId) {
            // Eliminar dropdown previo si existe
            const previo = document.getElementById('dropdown-clientes');
            if (previo) previo.remove();

            const input = document.getElementById(inputId);
            const rect = input.getBoundingClientRect();

            const dropdown = document.createElement('div');
            dropdown.id = 'dropdown-clientes';
            dropdown.className = 'list-group shadow-lg position-absolute w-100';
            dropdown.style.zIndex = '1050';
            dropdown.style.top = (input.offsetHeight) + 'px';

            dropdown.innerHTML = clientes.map(c => `
                <button type="button" class="list-group-item list-group-item-action py-2" 
                    onclick="seleccionarCliente(${c.id}, '${c.nombre.replace(/'/g, "\\'")}', '${inputId}')">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${c.nombre}</strong><br>
                            <small class="text-muted">${c.tipo_documento}: ${c.numero_documento}</small>
                        </div>
                        <i class="ri-add-circle-line text-primary fs-18"></i>
                    </div>
                </button>
            `).join('');

            input.parentElement.style.position = 'relative';
            input.parentElement.appendChild(dropdown);

            // Cerrar al hacer click fuera
            const clickOutside = (e) => {
                if (!dropdown.contains(e.target) && e.target !== input) {
                    dropdown.remove();
                    document.removeEventListener('click', clickOutside);
                }
            };
            setTimeout(() => document.addEventListener('click', clickOutside), 10);
        }

        function seleccionarCliente(id, nombre, inputId) {
            if (inputId === 'buscarCliente') {
                document.getElementById('buscarCliente').value = nombre;
                document.getElementById('clienteCheckout').value = nombre;
                document.getElementById('clienteIdCheckout').value = id;
            } else {
                document.getElementById('clienteCheckout').value = nombre;
                document.getElementById('clienteIdCheckout').value = id;
            }

            clienteSeleccionado = {
                id,
                nombre
            };
            const dropdown = document.getElementById('dropdown-clientes');
            if (dropdown) dropdown.remove();

            mostrarToast(`👤 Cliente: ${nombre}`, 'success');
        }

        // Buscar producto por código de barras
        async function buscarPorCodigoBarras(codigo) {
            try {
                const response = await fetch(`${ROUTES.buscarCodigoBarras}?codigo=${encodeURIComponent(codigo)}`);
                const result = await response.json();

                if (result.success && result.producto) {
                    const p = result.producto;
                    validarAgregarProducto(
                        p.id,
                        p.nombre,
                        parseFloat(p.precio_venta),
                        parseFloat(p.stock),
                        p.unidad.permite_decimales,
                        p.unidad.codigo
                    );
                    // mostrarToast(`✅ ${p.nombre} procesado`, 'success');
                } else {
                    mostrarToast('Producto no encontrado', 'warning');
                }
            } catch (error) {
                mostrarToast('Error al buscar producto', 'error');
            }
        }

        // =====================================================
        // FUNCIONES PARA MODAL DE DESCARGA
        // =====================================================

        /**
         * Resetea el POS después de una venta exitosa
         */
        function resetearPOS() {
            limpiarCarrito();
            toggleSidebarView('cart');

            // Resetear checkbox de crédito
            document.getElementById('esCredito').checked = false;
            document.getElementById('containerCredito').classList.add('d-none');
            document.getElementById('containerVuelto').classList.remove('d-none');

            // Resetear campos
            document.getElementById('montoRecibido').value = '0';
            document.getElementById('descuentoGeneral').value = '0';
            document.getElementById('observaciones').value = '';

            // Cerrar modal si está abierto
            const modalDescarga = bootstrap.Modal.getInstance(document.getElementById('modalDescargaComprobante'));
            if (modalDescarga) {
                modalDescarga.hide();
            }

            // Recargar para actualizar stock
            location.reload();
        }

        /**
         * Inicia una nueva venta (cierra modal y resetea)
         */
        function nuevaVenta() {
            resetearPOS();
        }
    </script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
