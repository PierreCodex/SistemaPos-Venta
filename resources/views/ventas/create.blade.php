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
            background: var(--vz-border-color);
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
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
        }

        .product-item:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
            border-color: var(--vz-primary-subtle) !important;
        }

        .product-item:hover .add-btn {
            background-color: var(--vz-primary) !important;
            color: #fff !important;
            transform: scale(1.1);
        }

        .add-btn {
            transition: all 0.2s ease;
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

        /* ====== ESCÁNER CON CÁMARA (PREMIUM) ====== */
        #reader {
            width: 100% !important;
            height: 350px !important;
            background: #000 !important;
            border-radius: 0 !important;
            border: none !important;
            position: relative;
        }

        #reader video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }

        /* Ocultar bordes automáticos de la librería */
        #reader__scan_region {
            border: none !important;
        }

        /* Overlay de escaneo personalizado */
        .scanner-container {
            position: relative;
            width: 100%;
            height: 350px;
            overflow: hidden;
        }

        .scanner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .scanner-target {
            width: 280px;
            height: 180px;
            position: relative;
            box-shadow: 0 0 0 1000px rgba(0, 0, 0, 0.4);
            border-radius: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            overflow: hidden;
        }

        /* Esquinas prominentes */
        .scanner-target::before,
        .scanner-target::after {
            content: '';
            position: absolute;
            width: 30px;
            height: 30px;
            border-color: var(--vz-primary);
            border-style: solid;
            pointer-events: none;
        }

        /* Top Left */
        .scanner-target::before {
            top: 0;
            left: 0;
            border-width: 4px 0 0 4px;
            border-radius: 15px 0 0 0;
        }

        /* Bottom Right */
        .scanner-target::after {
            bottom: 0;
            right: 0;
            border-width: 0 4px 4px 0;
            border-radius: 0 0 15px 0;
        }

        /* Otros dos corners */
        .scanner-corner-tr,
        .scanner-corner-bl {
            position: absolute;
            width: 30px;
            height: 30px;
            border-color: var(--vz-primary);
            border-style: solid;
            pointer-events: none;
        }

        .scanner-corner-tr {
            top: 0;
            right: 0;
            border-width: 4px 4px 0 0;
            border-radius: 0 15px 0 0;
        }

        .scanner-corner-bl {
            bottom: 0;
            left: 0;
            border-width: 0 0 4px 4px;
            border-radius: 0 0 0 15px;
        }

        .scanning-line {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(to right, transparent, var(--vz-primary), transparent);
            box-shadow: 0 0 15px var(--vz-primary);
            animation: scan-move 2.5s ease-in-out infinite;
            z-index: 11;
        }

        @keyframes scan-move {
            0% {
                top: 5%;
            }

            50% {
                top: 95%;
            }

            100% {
                top: 5%;
            }
        }

        .scanner-hint {
            position: absolute;
            bottom: 20px;
            left: 0;
            width: 100%;
            text-align: center;
            color: white;
            z-index: 12;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        .btn-camera-scan {
            background: var(--vz-primary);
            border: none;
            color: #fff;
            border-radius: 8px;
            padding: 10px 16px;
            transition: all 0.3s ease;
        }

        .btn-camera-scan:hover {
            filter: brightness(0.9);
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(var(--vz-primary-rgb), 0.4);
        }

        .btn-camera-scan i {
            font-size: 20px;
        }

        /* Modal Escáner Fullscreen en Móvil */
        @media (max-width: 767.98px) {
            #modalLectorCamara .modal-dialog {
                margin: 0;
                width: 100%;
                height: 100%;
                max-width: none;
            }

            #modalLectorCamara .modal-content {
                height: 100%;
                border-radius: 0;
            }

            #reader,
            .scanner-container {
                height: 300px !important;
            }

            .modal-cart-scroll {
                height: 200px !important;
                overflow-y: auto;
            }
        }

        .modal-cart-scroll {
            max-height: 250px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--vz-primary) var(--vz-light);
        }

        .modal-cart-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .modal-cart-scroll::-webkit-scrollbar-track {
            background: var(--vz-light);
        }

        .modal-cart-scroll::-webkit-scrollbar-thumb {
            background-color: var(--vz-primary);
            border-radius: 10px;
        }

        .pulse-badge {
            animation: pulse-animation 2s infinite;
        }

        @keyframes pulse-animation {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Estilo lista carrito en modal */
        .modal-cart-item {
            border-bottom: 1px solid var(--vz-border-color);
            padding: 10px 0;
        }

        .modal-cart-item:last-child {
            border-bottom: none;
        }
    </style>
@endsection

@section('content')
    <div class="row align-items-center mb-4 pt-4 text-uppercase">
        <div class="col">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('ventas.index') }}"
                    class="btn btn-primary d-flex align-items-center justify-content-center p-0 shadow material-shadow"
                    style="width: 48px; height: 48px; border-radius: 16px; background-color: var(--vz-primary); border: none;">
                    <i class="ri-arrow-left-s-line fs-24"></i>
                </a>
                <div class="vr mx-1 opacity-25"></div>
                <button class="btn btn-soft-light d-flex align-items-center justify-content-center p-0"
                    style="width: 48px; height: 48px; border-radius: 16px;" onclick="toggleFullScreen()"
                    title="Pantalla Completa">
                    <i class="ri-fullscreen-line fs-20 text-muted"></i>
                </button>
                <div class="vr mx-1 opacity-25"></div>
                <button class="btn btn-soft-light d-flex align-items-center justify-content-center p-0 light-dark-mode"
                    style="width: 48px; height: 48px; border-radius: 16px;" title="Cambiar Tema">
                    <i class="ri-moon-line fs-20 text-muted"></i>
                </button>
            </div>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                <button class="btn btn-white d-flex align-items-center justify-content-center p-0 shadow-sm border"
                    style="width: 48px; height: 48px; border-radius: 16px; color: var(--vz-body-color);">
                    <i class="ri-printer-line fs-20"></i>
                </button>
                <div class="dropdown">
                    <button class="btn btn-primary d-flex align-items-center gap-2 px-3 material-shadow"
                        style="height: 48px; border-radius: 16px; background-color: var(--vz-indigo); border: none;"
                        data-bs-toggle="dropdown">
                        <i class="ri-user-settings-line fs-18"></i>
                        <span class="d-none d-md-inline">{{ Auth::user()->name ?? 'Vendedor' }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                        <li><a class="dropdown-item" href="#"><i class="ri-user-line me-2"></i>Mi Perfil</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="#"><i
                                    class="ri-logout-box-line me-2"></i>Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Alerta de caja cerrada --}}
    @if (!$cajaAbierta)
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger border-2 d-flex align-items-center shadow-sm" role="alert">
                    <i class="ri-alert-line fs-3 me-3"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-1">Caja cerrada</h6>
                        <p class="mb-0">Debes aperturar la caja antes de registrar cualquier venta.</p>
                    </div>
                    <a href="{{ route('caja.apertura') }}" class="btn btn-sm btn-danger ms-3">
                        <i class="ri-safe-2-line me-1"></i> Abrir Caja
                    </a>
                </div>
            </div>
        </div>
    @endif

    <div class="row text-uppercase">
        <!-- COLUMNA PRODUCTOS -->
        <div class="col-xl-8 col-lg-7 text-uppercase">

            <!-- Barra Superior: Búsqueda y Filtros -->
            <div class="card material-shadow border-0 mb-3 text-uppercase">
                <div class="card-body p-3">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-5">
                            <div class="d-flex gap-2 align-items-center">
                                <div class="search-box flex-grow-1">
                                    <input type="text" id="buscarProducto" class="form-control border-light bg-light"
                                        placeholder="🔍 BUSCAR O ESCANEAR...">
                                    <i class="ri-search-line search-icon text-muted"></i>
                                </div>
                                <button type="button" class="btn btn-camera-scan" id="btnAbrirCamara"
                                    title="Escanear con cámara">
                                    <i class="ri-camera-line"></i>
                                </button>

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
                        data-unidad-codigo="{{ $prod->unidad->codigo ?? '' }}"
                        {{-- El stock SIEMPRE está en unidad base; el POS convierte con el factor --}}
                        data-stock-base="{{ $prod->stock }}"
                        data-presentaciones="{{ json_encode($prod->presentacionesParaPos()) }}">
                        <div class="card product-item material-shadow h-100 border-0 overflow-hidden text-uppercase"
                            onclick="abrirSelectorProducto({{ $prod->id }})">
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
                    <div class="card-footer bg-white border-top-dashed p-4 mt-auto text-uppercase">
                        <div class="d-flex align-items-center justify-content-between mb-4 pt-2 text-uppercase">
                            <div>
                                <h6 class="text-muted mb-1 fs-12 fw-bold">TOTAL A PAGAR</h6>
                                <h2 class="text-primary mb-0 fw-extrabold" id="totalCarrito"
                                    style="letter-spacing: -1px;">S/ 0.00</h2>
                            </div>
                            <div class="text-end d-none d-md-block">
                                <span
                                    class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                    <i class="ri-checkbox-circle-line me-1"></i>Venta Segura
                                </span>
                            </div>
                        </div>
                        <button class="btn btn-success w-100 py-3 fw-bold fs-16 shadow material-shadow text-uppercase"
                            id="btnRealizarCobro" disabled data-bs-toggle="modal" data-bs-target="#modalComprobante"
                            style="border-radius: 16px; background-color: var(--vz-success); border: none;">
                            REALIZAR COBRO <i class="ri-arrow-right-line ms-1 align-middle fs-20"></i>
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
                                <div class="input-group">
                                    <input type="text" id="fechaVencimientoCredito"
                                        class="form-control border-light bg-light" data-provider="flatpickr"
                                        data-date-format="Y-m-d"
                                        data-min-date="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                    <span class="input-group-text border-light bg-light"><i
                                            class="ri-calendar-event-line"></i></span>
                                </div>
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

    <!-- MODAL DESCARGA DE COMPROBANTE (Versión Compacta) -->
    <div class="modal fade" id="modalDescargaComprobante" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body p-4">
                    <h5 class="fw-bold mb-1 text-dark text-uppercase fs-16">
                        Comprobante: <span id="comprobanteNumero" class="text-primary">#000-000000</span>
                    </h5>
                    <p class="text-muted fs-11 fw-medium text-uppercase mb-4" id="tipoComprobanteTexto">COMPROBANTE</p>

                    <!-- Formatos de impresión compactos -->
                    <div class="d-flex justify-content-between align-items-center mb-4 gap-2">
                        <!-- A4 -->
                        <div class="text-center flex-grow-1">
                            <a href="#" id="btnDescargarA4" target="_blank"
                                class="d-block text-decoration-none group">
                                <div class="avatar-sm mx-auto mb-2">
                                    <div class="avatar-title bg-info rounded text-white fs-20 shadow-sm">
                                        <i class="ri-file-text-line"></i>
                                    </div>
                                </div>
                                <span class="text-muted fw-bold fs-11 text-uppercase">Imprimir A4</span>
                            </a>
                        </div>
                        <!-- 80mm -->
                        <div class="text-center flex-grow-1 border-start border-end border-light">
                            <a href="#" id="btnDescargar80mm" target="_blank"
                                class="d-block text-decoration-none group">
                                <div class="avatar-sm mx-auto mb-2">
                                    <div class="avatar-title bg-info rounded text-white fs-20 shadow-sm">
                                        <i class="ri-bill-line"></i>
                                    </div>
                                </div>
                                <span class="text-muted fw-bold fs-11 text-uppercase">Ticket 80mm</span>
                            </a>
                        </div>
                        <!-- 50mm -->
                        <div class="text-center flex-grow-1 border-end border-light">
                            <a href="#" id="btnDescargar50mm" target="_blank"
                                class="d-block text-decoration-none group">
                                <div class="avatar-sm mx-auto mb-2">
                                    <div class="avatar-title bg-info rounded text-white fs-20 shadow-sm">
                                        <i class="ri-coupon-3-line"></i>
                                    </div>
                                </div>
                                <span class="text-muted fw-bold fs-11 text-uppercase">Ticket 50mm</span>
                            </a>
                        </div>
                        <!-- A5 (Opcional, lo dejamos como A4 por ahora si no hay ruta específica) -->
                        <div class="text-center flex-grow-1">
                            <a href="#" target="_blank" class="d-block text-decoration-none opacity-50">
                                <div class="avatar-sm mx-auto mb-2">
                                    <div class="avatar-title bg-secondary rounded text-white fs-20">
                                        <i class="ri-file-list-line"></i>
                                    </div>
                                </div>
                                <span class="text-muted fw-bold fs-11 text-uppercase">Imprimir A5</span>
                            </a>
                        </div>
                    </div>

                    <!-- Canales de envío -->
                    <div class="vstack gap-3 mt-2">
                        <!-- Email -->
                        <div class="input-group">
                            <input type="email" class="form-control border-light-subtle bg-light"
                                placeholder="Correo electrónico del cliente" id="emailClienteEnvio">
                            <button class="btn btn-outline-light border-light-subtle text-muted" type="button"
                                id="btnEnviarEmail">
                                <i class="ri-mail-send-line me-1"></i> Enviar
                            </button>
                        </div>
                        <!-- WhatsApp -->
                        <div class="input-group">
                            <span class="input-group-text border-light-subtle bg-light text-muted">+51</span>
                            <input type="text" class="form-control border-light-subtle bg-light"
                                placeholder="Número de celular" id="celularClienteEnvio">
                            <button class="btn btn-outline-light border-light-subtle text-muted" type="button"
                                id="btnEnviarWhatsapp">
                                Enviar <i class="ri-whatsapp-line ms-1 text-success"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Alerta de estado pequeña -->
                    <div class="mt-4 pt-2 border-top border-light border-dashed">
                        <p class="mb-0 fs-11 text-muted text-uppercase fw-medium" id="infoNotaVenta">
                            <i class="ri-error-warning-line me-1 text-warning"></i> No tiene validez tributaria
                        </p>
                        <p class="mb-0 fs-11 text-success text-uppercase fw-bold d-none" id="infoElectronico">
                            <i class="ri-checkbox-circle-line me-1"></i> Comprobante validado SUNAT
                        </p>
                    </div>
                </div>
                <div class="modal-footer bg-light-subtle border-0 p-3">
                    <div class="d-flex w-100 gap-2">
                        <button type="button" class="btn btn-primary flex-grow-1 fw-bold shadow-sm"
                            onclick="nuevaVenta()">
                            NUEVA VENTA <i class="ri-add-line ms-1"></i>
                        </button>
                        <button type="button" class="btn btn-white border fw-bold px-4" data-bs-dismiss="modal"
                            onclick="resetearPOS()">
                            CERRAR
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL SELECTOR DE PRESENTACIÓN -->
    {{-- Solo aparece si el producto se vende en más de una unidad --}}
    <div class="modal fade" id="modalPresentacion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary p-3">
                    <h5 class="modal-title text-white fw-bold">📦 ¿EN QUÉ PRESENTACIÓN?</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-3">
                    <p class="text-muted fs-13 mb-3 text-uppercase" id="presentacionProductoNombre"></p>
                    <div class="list-group" id="listaPresentaciones"></div>
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

    <!-- MODAL ESCÁNER CON CÁMARA (Responsive + Lista) -->
    <div class="modal fade" id="modalLectorCamara" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary p-3">
                    <h5 class="modal-title text-white fw-bold"><i class="ri-camera-line me-2"></i>AGREGAR PRODUCTOS</h5>
                    <button type="button" class="btn-close btn-close-white" id="btnCerrarLector"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 bg-light">
                    <!-- Sección de Cámara con Overlay -->
                    <div class="scanner-container">
                        <div id="reader"></div>
                        <div class="scanner-overlay">
                            <div class="scanner-target">
                                <div class="scanner-corner-tr"></div>
                                <div class="scanner-corner-bl"></div>
                                <div class="scanning-line"></div>
                            </div>
                            <div class="scanner-hint">Enfoque el código de barras aquí</div>
                        </div>
                    </div>

                    <!-- Sección de Lista de Productos Scaneados -->
                    <div class="p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="text-uppercase fw-bold mb-0">Productos Agregados</h6>
                            <span class="badge bg-primary-subtle text-primary pulse-badge" id="modalScanCount">0
                                Items</span>
                        </div>

                        <div id="modalCartList" class="modal-cart-scroll bg-white rounded border p-2">
                            <!-- Aquí se cargarán los productos en tiempo real -->
                            <div class="text-center py-4 text-muted" id="modalCartEmpty">
                                <i class="ri-barcode-line fs-24 d-block mb-2"></i>
                                Escanee un código para comenzar
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block text-uppercase">Total a Pagar</small>
                        <h4 class="text-primary fw-bold mb-0" id="modalCartTotal">S/ 0.00</h4>
                    </div>
                    <button type="button" class="btn btn-success btn-lg px-4 fw-bold" onclick="goToCheckoutFromModal()">
                        <i class="ri-money-dollar-circle-line me-1"></i> COBRAR
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://unpkg.com/html5-qrcode"></script>
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
        const CAJA_ABIERTA = {{ $cajaAbierta ? 'true' : 'false' }};

        // =====================================================
        // FUNCIONES DE VALIDACIÓN (Declinadas al inicio para evitar errores de ReferenceError)
        // =====================================================
        // =====================================================
        // PRESENTACIONES
        // =====================================================
        // Un producto puede venderse en varias unidades (suelto, caja x24)
        // contra un único stock, que SIEMPRE está en unidad base. El factor
        // de cada presentación dice cuántas unidades base contiene.
        //
        // Antes había una lista fija de códigos (['KG','GR','LTR','ML']) para
        // decidir si se vendía por peso: cualquier unidad nueva que creara el
        // cliente quedaba fuera. Ahora lo decide permite_decimales de la
        // unidad, que es el dato real.
        // =====================================================

        /**
         * Lee los datos de un producto desde su tarjeta del grid
         */
        function datosProducto(productoId) {
            const card = document.querySelector(`.producto-card[data-id="${productoId}"]`);
            if (!card) return null;

            return {
                id: productoId,
                nombre: card.dataset.nombre,
                stockBase: parseFloat(card.dataset.stockBase) || 0,
                presentaciones: JSON.parse(card.dataset.presentaciones || '[]')
            };
        }

        /**
         * Punto de entrada al tocar un producto: si tiene una sola
         * presentación va directo; si tiene varias, pregunta cuál.
         */
        function abrirSelectorProducto(productoId) {
            const prod = datosProducto(productoId);
            if (!prod) return;

            if (prod.presentaciones.length === 0) {
                mostrarToast('Este producto no tiene presentaciones configuradas', 'error');
                return;
            }

            if (prod.presentaciones.length === 1) {
                elegirPresentacion(productoId, prod.presentaciones[0].id);
                return;
            }

            abrirModalPresentacion(prod);
        }

        /**
         * Aplica la presentación elegida: pide peso si la unidad admite
         * decimales, o agrega una unidad directamente.
         */
        function aplicarPresentacion(prod, pres) {
            const modalAbierto = bootstrap.Modal.getInstance(document.getElementById('modalPresentacion'));
            if (modalAbierto) modalAbierto.hide();

            if (pres.decimales) {
                abrirModalPeso(prod, pres);
            } else {
                agregarAlCarrito(prod, pres, 1);
            }
        }

        /**
         * Elige una presentación de un producto del grid
         */
        function elegirPresentacion(productoId, presentacionId) {
            const prod = datosProducto(productoId);
            if (!prod) return;

            const pres = prod.presentaciones.find(p => p.id === presentacionId);
            if (pres) aplicarPresentacion(prod, pres);
        }

        /**
         * Elige una presentación desde el modal.
         *
         * Usa el producto guardado en productoEnSeleccion en vez de releerlo
         * del grid: un producto escaneado puede no estar en pantalla.
         */
        function elegirPresentacionDeModal(presentacionId) {
            const prod = productoEnSeleccion;
            if (!prod) return;

            const pres = prod.presentaciones.find(p => p.id === presentacionId);
            if (pres) aplicarPresentacion(prod, pres);
        }

        /**
         * Muestra las presentaciones disponibles con el stock que queda
         * de cada una, expresado en su propia unidad.
         */
        function abrirModalPresentacion(prod) {
            productoEnSeleccion = prod;
            document.getElementById('presentacionProductoNombre').textContent = prod.nombre;

            const disponibleBase = prod.stockBase - stockBaseComprometido(prod.id);

            document.getElementById('listaPresentaciones').innerHTML = prod.presentaciones.map(p => {
                const cabe = p.factor <= disponibleBase + TOLERANCIA;
                const equivale = p.factor === 1 ? 'Unidad base' : `Contiene ${p.factor} und. base`;
                const posibles = p.factor > 0 ? Math.floor(disponibleBase / p.factor) : 0;

                return `
                    <button type="button"
                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center ${cabe ? '' : 'disabled opacity-50'}"
                        ${cabe ? `onclick="elegirPresentacionDeModal(${p.id})"` : ''}>
                        <div class="text-start">
                            <h6 class="mb-0 text-uppercase">${p.nombre || p.unidad}</h6>
                            <small class="text-muted">${equivale}</small>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold text-primary d-block">S/ ${p.precio.toFixed(2)}</span>
                            <small class="text-muted">${cabe ? `Alcanza para ${posibles}` : 'Sin stock'}</small>
                        </div>
                    </button>
                `;
            }).join('');

            new bootstrap.Modal(document.getElementById('modalPresentacion')).show();
        }

        // Estado del carrito
        let carrito = [];
        let totalCarrito = 0;
        let clienteSeleccionado = null;
        let productoEnEdicionPeso = null; // Para el modal de peso
        let productoEnSeleccion = null; // Para el modal de presentación

        // Margen para comparaciones de punto flotante (0.001 es la precisión
        // de stock en la base de datos)
        const TOLERANCIA = 0.0005;

        /**
         * Clave de una línea del carrito.
         *
         * El mismo producto en dos presentaciones son DOS líneas: 1 caja y
         * 3 unidades sueltas no se pueden sumar entre sí.
         */
        function claveCarrito(productoId, presentacionId) {
            return `${productoId}::${presentacionId}`;
        }

        /**
         * Stock base ya comprometido por el carrito para un producto.
         *
         * Todas las líneas del mismo producto comen del MISMO stock, sin
         * importar en qué presentación estén: 1 caja x24 + 90 unidades son
         * 114 unidades base y no caben en un stock de 100.
         */
        function stockBaseComprometido(productoId, exceptoClave = null) {
            return carrito
                .filter(i => i.productoId === productoId && i.clave !== exceptoClave)
                .reduce((suma, i) => suma + (i.cantidad * i.factor), 0);
        }

        /**
         * Verifica si una cantidad cabe en el stock disponible del producto,
         * contando lo que ya tienen las demás líneas.
         */
        function cabeEnStock(prod, factor, cantidad, exceptoClave = null) {
            const requerido = stockBaseComprometido(prod.id, exceptoClave) + (cantidad * factor);
            return requerido <= prod.stockBase + TOLERANCIA;
        }

        // =====================================================
        // FUNCIONES DE VALIDACIÓN Y MODAL DE PESO
        // =====================================================


        /**
         * Configura y abre el modal de peso
         */
        function abrirModalPeso(prod, pres) {
            productoEnEdicionPeso = {
                prod,
                pres
            };

            document.getElementById('pesoProductoNombre').textContent = prod.nombre;
            document.getElementById('pesoProductoPrecio').textContent = pres.precio.toFixed(2);
            document.getElementById('modalVentaPesoTitle').textContent = `⚖️ VENTA POR ${pres.unidad}`;

            // Limpiar inputs
            const inputImporte = document.getElementById('inputVentaImporte');
            const inputPeso = document.getElementById('inputVentaPeso');
            inputImporte.value = '';
            inputPeso.value = '';

            // Ajustar label de unidad
            inputPeso.nextElementSibling.textContent = pres.unidad;

            const modal = new bootstrap.Modal(document.getElementById('modalVentaPeso'));
            modal.show();

            setTimeout(() => inputImporte.focus(), 500);
        }

        // Lógica de cálculo inverso en el modal
        document.getElementById('inputVentaImporte').addEventListener('input', function() {
            if (!productoEnEdicionPeso) return;
            const importe = parseFloat(this.value) || 0;
            const precio = productoEnEdicionPeso.pres.precio;

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
            const precio = productoEnEdicionPeso.pres.precio;

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
            const {
                prod,
                pres
            } = productoEnEdicionPeso;

            if (cantidad <= 0) {
                mostrarToast('Ingrese una cantidad válida', 'warning');
                return;
            }

            // El stock se valida en unidad base, contando lo que ya hay en el carrito
            const clave = claveCarrito(prod.id, pres.id);
            const yaEnCarrito = carrito.find(i => i.clave === clave);
            const cantidadTotal = (yaEnCarrito ? yaEnCarrito.cantidad : 0) + cantidad;

            if (!cabeEnStock(prod, pres.factor, cantidadTotal, clave)) {
                mostrarToast('Stock insuficiente', 'warning');
                return;
            }

            agregarAlCarrito(prod, pres, cantidad);

            bootstrap.Modal.getInstance(document.getElementById('modalVentaPeso')).hide();
        });

        // =====================================================
        // FUNCIONES DEL CARRITO
        // =====================================================

        /**
         * Agrega un producto al carrito o incrementa cantidad si ya existe
         */
        function agregarAlCarrito(prod, pres, cantidad = 1) {
            const clave = claveCarrito(prod.id, pres.id);
            const existente = carrito.find(item => item.clave === clave);
            const cantidadFinal = (existente ? existente.cantidad : 0) + cantidad;

            if (!cabeEnStock(prod, pres.factor, cantidadFinal, clave)) {
                mostrarToast('Stock insuficiente', 'warning');
                return;
            }

            if (existente) {
                existente.cantidad = cantidadFinal;
                existente.subtotal = existente.cantidad * existente.precio;
            } else {
                carrito.push({
                    clave: clave,
                    productoId: prod.id,
                    presentacionId: pres.id,
                    nombre: prod.nombre,
                    precio: pres.precio,
                    factor: pres.factor,
                    cantidad: cantidad,
                    subtotal: cantidad * pres.precio,
                    stockBase: prod.stockBase,
                    permiteDecimales: pres.decimales,
                    unidadCodigo: pres.unidad
                });
            }

            actualizarVistaCarrito();
            reproducirSonido();
        }

        /**
         * Actualiza la cantidad de una línea del carrito
         */
        function actualizarCantidad(clave, cambio) {
            const item = carrito.find(i => i.clave === clave);
            if (!item) return;

            const nuevaCantidad = redondearCantidad(item.cantidad + cambio, item.permiteDecimales);

            if (nuevaCantidad <= 0) {
                eliminarDelCarrito(clave);
                return;
            }

            // El stock lo comparten todas las líneas del mismo producto
            const prod = {
                id: item.productoId,
                stockBase: item.stockBase
            };

            if (!cabeEnStock(prod, item.factor, nuevaCantidad, clave)) {
                mostrarToast('Stock insuficiente', 'warning');
                return;
            }

            item.cantidad = nuevaCantidad;
            item.subtotal = item.cantidad * item.precio;
            actualizarVistaCarrito();
        }

        /**
         * Evita que sumar 0.1 repetidas veces arrastre error binario
         * (0.1 + 0.2 = 0.30000000000000004)
         */
        function redondearCantidad(valor, permiteDecimales) {
            return permiteDecimales ? Math.round(valor * 1000) / 1000 : Math.round(valor);
        }

        /**
         * Elimina una línea del carrito
         */
        function eliminarDelCarrito(clave) {
            carrito = carrito.filter(item => item.clave !== clave);
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
                        <small class="text-muted text-nowrap">S/ ${item.precio.toFixed(2)} / ${item.unidadCodigo}${item.factor > 1 ? ` (x${item.factor})` : ''}</small>
                    </td>
                    <td style="width: 120px;">
                        <div class="d-flex align-items-center justify-content-center bg-light rounded p-1 text-uppercase">
                            <button class="btn btn-link btn-sm p-0 text-muted" onclick="actualizarCantidad('${item.clave}', ${item.permiteDecimales ? -0.1 : -1})">
                                <i class="ri-subtract-line"></i>
                            </button>
                            <span class="mx-2 fw-medium">${item.permiteDecimales ? item.cantidad.toFixed(3) : item.cantidad}</span>
                            <button class="btn btn-link btn-sm p-0 text-muted" onclick="actualizarCantidad('${item.clave}', ${item.permiteDecimales ? 0.1 : 1})">
                                <i class="ri-add-line"></i>
                            </button>
                        </div>
                    </td>
                    <td class="text-end pe-2 text-uppercase">
                        <span class="fw-bold text-primary">S/ ${item.subtotal.toFixed(2)}</span>
                        <button class="btn btn-link btn-sm p-0 text-danger ms-1" onclick="eliminarDelCarrito('${item.clave}')">
                            <i class="ri-close-line"></i>
                        </button>
                    </td>
                </tr>
            `).join('');

            // Actualizar contadores
            cantidadItems.textContent = carrito.length;
            totalDisplay.textContent = `S/ ${totalCarrito.toFixed(2)}`;

            // --- Actualizar Vista en Modal Escáner ---
            const modalCartList = document.getElementById('modalCartList');
            const modalCartEmpty = document.getElementById('modalCartEmpty');
            const modalCartTotal = document.getElementById('modalCartTotal');
            const modalScanCount = document.getElementById('modalScanCount');

            if (modalCartList) {
                if (carrito.length === 0) {
                    modalCartList.innerHTML =
                        `<div class="text-center py-4 text-muted" id="modalCartEmpty"><i class="ri-barcode-line fs-24 d-block mb-2"></i>Escanee un código para comenzar</div>`;
                } else {
                    modalCartList.innerHTML = carrito.map(item => `
                        <div class="modal-cart-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fs-12 mb-0 text-uppercase">${item.nombre}</h6>
                                <small class="text-muted">S/ ${item.precio.toFixed(2)} x ${item.cantidad}</small>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold text-primary">S/ ${item.subtotal.toFixed(2)}</span>
                            </div>
                        </div>
                    `).join('');
                }
                if (modalCartTotal) modalCartTotal.textContent = `S/ ${totalCarrito.toFixed(2)}`;
                if (modalScanCount) modalScanCount.textContent = `${carrito.length} Items`;
            }
        }


        // =====================================================
        // FUNCIONES DEL CHECKOUT
        // =====================================================

        function goToCheckout(tipo) {
            if (!CAJA_ABIERTA) {
                mostrarToast('Debe abrir la caja antes de realizar ventas.', 'error');
                return;
            }

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

            if (!CAJA_ABIERTA) {
                mostrarToast('Debe abrir la caja antes de realizar ventas.', 'error');
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
            // presentacion_id le dice al servidor con qué factor convertir la
            // cantidad a unidad base antes de descontar el stock.
            const detalles = carrito.map(item => ({
                producto_id: item.productoId,
                presentacion_id: item.presentacionId,
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
                    const presentaciones = result.presentaciones || [];

                    if (presentaciones.length === 0) {
                        mostrarToast('Este producto no tiene presentaciones configuradas', 'error');
                        return;
                    }

                    const prod = {
                        id: p.id,
                        nombre: p.nombre,
                        stockBase: parseFloat(p.stock),
                        presentaciones: presentaciones
                    };

                    // Si se escaneó el código de una caja, el servidor ya dice
                    // cuál es: se vende esa presentación sin preguntar.
                    const pres = result.presentacion_id ?
                        presentaciones.find(x => x.id === result.presentacion_id) :
                        (presentaciones.length === 1 ? presentaciones[0] : null);

                    if (!pres) {
                        abrirModalPresentacion(prod);
                        return;
                    }

                    if (pres.decimales) {
                        abrirModalPeso(prod, pres);
                    } else {
                        agregarAlCarrito(prod, pres, 1);
                    }
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

        // =====================================================
        // ESCÁNER DE CÓDIGO DE BARRAS CON CÁMARA (html5-qrcode)
        // =====================================================

        let html5QrCode = null;

        function inicializarLectorCamara() {
            const config = {
                fps: 20,
                qrbox: {
                    width: 280,
                    height: 180
                },
                aspectRatio: 1.777778 // 16:9 para mejor compatibilidad con móviles
            };

            // Si ya hay una instancia, limpiarla antes de empezar
            if (html5QrCode) {
                html5QrCode.stop().catch(err => console.error("Error al detener scanner:", err));
            }

            html5QrCode = new Html5Qrcode("reader");

            let lastScannedText = "";
            let lastScannedTime = 0;

            const onScanSuccess = (decodedText, decodedResult) => {
                const now = Date.now();

                // Evitar escaneos repetidos del mismo código en menos de 2 segundos
                if (decodedText === lastScannedText && (now - lastScannedTime < 2000)) {
                    return;
                }

                // Delay mínimo global entre cualquier escaneo para evitar ráfagas
                if (now - lastScannedTime < 700) {
                    return;
                }

                lastScannedText = decodedText;
                lastScannedTime = now;

                // Sonido de escaneo
                reproducirBeep();

                // Buscar el producto
                buscarPorCodigoBarras(decodedText);

                // Feedback visual de escaneo exitoso (Línea verde)
                const scanLine = document.querySelector('.scanning-line');
                const scannerTarget = document.querySelector('.scanner-target');
                if (scanLine) {
                    scanLine.style.background = '#0ab39c';
                    scanLine.style.boxShadow = '0 0 20px #0ab39c';
                    if (scannerTarget) scannerTarget.style.borderColor = '#0ab39c';
                    setTimeout(() => {
                        scanLine.style.background = '';
                        scanLine.style.boxShadow = '';
                        if (scannerTarget) scannerTarget.style.borderColor = '';
                    }, 500);
                }
            };


            const onScanFailure = (error) => {
                // Se ignoran fallos menores de detección
            };

            html5QrCode.start({
                    facingMode: "environment"
                }, // Prioriza cámara trasera
                config,
                onScanSuccess,
                onScanFailure
            ).catch(err => {
                console.error("No se pudo iniciar la cámara:", err);
                mostrarToast("No se pudo acceder a la cámara. Verifique los permisos.", "error");
                const modalLector = bootstrap.Modal.getInstance(document.getElementById('modalLectorCamara'));
                if (modalLector) modalLector.hide();
            });
        }

        function detenerLectorCamara() {
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    console.log("Cámara detenida.");
                }).catch(err => {
                    console.error("Error al detener la cámara:", err);
                });
            }
        }

        function reproducirBeep() {
            try {
                const audioContext = new(window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);

                oscillator.frequency.value = 1000;
                oscillator.type = 'sine';
                gainNode.gain.value = 0.3;

                oscillator.start();
                setTimeout(() => oscillator.stop(), 100);
            } catch (e) {}
        }

        // Event Listeners para el lector de cámara
        document.getElementById('btnAbrirCamara').addEventListener('click', function() {
            const modalLector = new bootstrap.Modal(document.getElementById('modalLectorCamara'));
            modalLector.show();
            // Esperar a que el modal se muestre para iniciar la cámara
            document.getElementById('modalLectorCamara').addEventListener('shown.bs.modal', function() {
                inicializarLectorCamara();
            }, {
                once: true
            });
        });

        // Asegurar que la cámara se detenga al cerrar el modal
        document.getElementById('modalLectorCamara').addEventListener('hidden.bs.modal', function() {
            detenerLectorCamara();
        });

        document.getElementById('btnCerrarLector').addEventListener('click', function() {
            detenerLectorCamara();
        });

        /**
         * Función puente para ir al checkout desde el modal de escaner
         */
        function goToCheckoutFromModal() {
            if (carrito.length === 0) {
                mostrarToast('Agregue productos para continuar', 'warning');
                return;
            }

            if (!CAJA_ABIERTA) {
                mostrarToast('Debe abrir la caja antes de realizar ventas.', 'error');
                return;
            }

            // Primero detenemos cámara y cerramos modal
            detenerLectorCamara();
            const modalEl = document.getElementById('modalLectorCamara');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

            // Abrimos el modal de comprobantes o directamente a checkout
            // Según tu lógica actual, btnRealizarCobro abre modalComprobante
            const modalComprobante = new bootstrap.Modal(document.getElementById('modalComprobante'));
            modalComprobante.show();
        }
    </script>
@endsection
