@extends('layouts.master')

@section('title')
    Ventas a Crédito
@endsection

@section('css')
    <link href="{{ URL::asset('build/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        .filter-btn-purple {
            background-color: #903ef5 !important;
            border-color: #903ef5 !important;
            color: #fff !important;
            font-weight: 500;
        }

        .filter-btn-purple:hover {
            background-color: #7a32d4 !important;
            border-color: #7a32d4 !important;
        }

        .stat-box {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            min-height: 80px;
        }

        [data-layout-mode="dark"] .stat-box {
            background: rgba(255, 255, 255, 0.05);
        }

        [data-layout-mode="light"] .stat-box {
            background: #f3f6f9;
            border: 1px solid #e9ebec;
        }

        /* Estilo para los badges de estado de pago */
        .badge-pago-pendiente {
            background-color: #ffbe0b;
            color: #000;
        }

        .badge-pago-parcial {
            background-color: #fb5607;
            color: #fff;
        }

        .badge-pago-pagado {
            background-color: #06d6a0;
            color: #fff;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Ventas a Crédito</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Ventas a Crédito</li>
                        <li class="breadcrumb-item active">Listado</li>
                    </ol>
                </div>
            </div>
            <p class="text-muted">Administra y gestiona las ventas a crédito pendientes de pago.</p>
        </div>
    </div>

    <div class="row align-items-stretch">
        {{-- Bloque de Filtros --}}
        <div class="col-lg-8">
            <div class="card card-height-100">
                <div class="card-body p-4">
                    <form id="formFiltros" action="javascript:void(0);">
                        <div class="row g-3 align-items-end">
                            <div class="col-sm-5">
                                <label for="fecha_inicio"
                                    class="form-label fw-semibold text-muted text-uppercase fs-11">Fecha Inicio</label>
                                <div class="input-group">
                                    <input type="text" id="fecha_inicio" class="form-control border-light bg-light"
                                        data-provider="flatpickr" data-date-format="Y-m-d" value="{{ $fechaInicio }}">
                                    <span class="input-group-text border-light bg-light"><i
                                            class="ri-calendar-event-line"></i></span>
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <label for="fecha_fin" class="form-label fw-semibold text-muted text-uppercase fs-11">Fecha
                                    Fin</label>
                                <div class="input-group">
                                    <input type="text" id="fecha_fin" class="form-control border-light bg-light"
                                        data-provider="flatpickr" data-date-format="Y-m-d" value="{{ $fechaFin }}">
                                    <span class="input-group-text border-light bg-light"><i
                                            class="ri-calendar-event-line"></i></span>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" class="btn filter-btn-purple w-100 py-2 fs-14 shadow-none">
                                    <i class="ri-filter-3-line me-1 align-middle"></i> Filtrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Bloque de Resumen --}}
        <div class="col-lg-4">
            <div class="card card-height-100">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stat-box text-center p-3 rounded-3">
                                <p class="text-success text-uppercase fw-bold mb-2 fs-12">Total</p>
                                <h4 class="mb-0 fw-bold">
                                    <span class="fs-12 fw-normal text-muted me-1">S/</span>
                                    {{ number_format($estadisticas['total'], 2) }}
                                </h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box text-center p-3 rounded-3">
                                <p class="text-warning text-uppercase fw-bold mb-2 fs-12">Saldo Pendiente</p>
                                <h4 class="mb-0 fw-bold">
                                    <span class="fs-12 fw-normal text-muted me-1">S/</span>
                                    {{ number_format($estadisticas['saldo_pendiente'], 2) }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1"><i class="ri-table-line me-1"></i> REGISTROS DE CRÉDITOS</h5>
                    <div class="d-flex flex-shrink-0 gap-2">
                        <button type="button" class="btn btn-purple btn-label waves-effect waves-light"
                            onclick="verHistorialGeneral()">
                            <i class="ri-history-line label-icon align-middle fs-16 me-2"></i> HISTORIAL DE PAGOS
                        </button>
                        <button type="button" class="btn btn-success btn-label waves-effect waves-light">
                            <i class="ri-file-excel-2-line label-icon align-middle fs-16 me-2"></i> Excel
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <table id="tablaCreditos" class="table nowrap align-middle" style="width:100%">
                        <thead class="table-light">
                            <tr class="text-uppercase fs-11">
                                <th>Comprobante</th>
                                <th>Nom. Cliente</th>
                                <th>Vendedor</th>
                                <th>Met. Pago</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Saldo Pend.</th>
                                <th>Estado</th>
                                <th class="no-exportar">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ventas as $venta)
                                <tr>
                                    <td>
                                        <small
                                            class="text-muted text-uppercase d-block">{{ $venta->serie }}{{ $venta->numero }}</small>
                                        <span class="fw-bold text-uppercase">{{ $venta->comprobante }}</span><br>
                                        <small
                                            class="text-muted">{{ $venta->serie }}-{{ str_pad($venta->numero, 8, '0', STR_PAD_LEFT) }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="ri-user-line me-2 text-muted"></i>
                                            <span class="text-uppercase">{{ $venta->nombre_cliente }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="ri-user-star-line me-2 text-muted"></i>
                                            <span>{{ $venta->vendedor->name ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="ri-money-dollar-circle-line me-2 text-success"></i>
                                            <span class="text-uppercase">{{ $venta->metodo_pago }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-muted small">
                                            <i
                                                class="ri-calendar-line me-1"></i>{{ $venta->fecha_emision->format('d/m/Y') }}<br>
                                            <i class="ri-time-line me-1"></i>{{ $venta->fecha_emision->format('H:i:s') }}
                                        </div>
                                    </td>
                                    <td><span class="text-primary fw-bold">S/ {{ number_format($venta->total, 2) }}</span>
                                    </td>
                                    <td><span class="text-warning fw-bold">S/
                                            {{ number_format($venta->saldo_pendiente, 2) }}</span></td>
                                    <td>
                                        @php
                                            $badge = match ($venta->estado_pago) {
                                                'PAGADO' => 'bg-success',
                                                'PARCIAL' => 'bg-warning',
                                                default => 'bg-danger',
                                            };
                                        @endphp
                                        <span
                                            class="badge {{ $badge }} text-uppercase">{{ $venta->estado_pago }}</span>
                                    </td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="javascript:void(0);"
                                                        onclick="verDetalles({{ $venta->id }})"><i
                                                            class="ri-eye-fill align-bottom me-2 text-muted"></i> Ver
                                                        Detalle</a></li>
                                                @if ($venta->saldo_pendiente > 0)
                                                    <li><a class="dropdown-item edit-item-btn" href="javascript:void(0);"
                                                            onclick="abrirModalPago({{ $venta->id }}, {{ $venta->saldo_pendiente }}, '{{ $venta->nombre_cliente }}')"><i
                                                                class="ri-hand-coin-line align-bottom me-2 text-muted"></i>
                                                            Registrar Pago</a></li>
                                                @endif
                                                <li><a class="dropdown-item" href="javascript:void(0);"
                                                        onclick="verHistorialPagos({{ $venta->id }})"><i
                                                            class="ri-history-line align-bottom me-2 text-muted"></i>
                                                        Historial de Pagos</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">No hay registros de créditos en
                                        el rango seleccionado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Registrar Pago --}}
    <div class="modal fade" id="modalPago" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white"><i class="ri-hand-coin-line me-2"></i>Registrar Pago / Abono</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formPago">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info py-2 fs-13">
                            <strong>Cliente:</strong> <span id="pagoClienteNombre"></span><br>
                            <strong>Deuda Pendiente:</strong> S/ <span id="pagoSaldoPendiente"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Monto a Pagar (S/)</label>
                            <input type="number" step="0.01" class="form-control" name="monto" id="pagoMonto"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Método de Pago</label>
                            <select class="form-select" name="metodo_pago" required>
                                <option value="EFECTIVO">EFECTIVO</option>
                                <option value="YAPE">YAPE</option>
                                <option value="PLIN">PLIN</option>
                                <option value="TARJETA">TARJETA</option>
                                <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Fecha de Pago</label>
                            <input type="date" class="form-control" name="fecha_pago" value="{{ date('Y-m-d') }}"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nro. Operación / Referencia</label>
                            <input type="text" class="form-control" name="numero_operacion"
                                placeholder="Opcional (se generará uno si se deja vacío)">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observaciones</label>
                            <textarea class="form-control" name="observaciones" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="venta_id" id="pagoVentaId">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success" id="btnGuardarPago">Confirmar Pago</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Historial de Pagos --}}
    <div class="modal fade" id="modalHistorialPagos" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-purple">
                    <h5 class="modal-title text-white"><i class="ri-history-line me-2"></i>Historial de Pagos</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                    <th>Método</th>
                                    <th>Nro. Op</th>
                                    <th>Registrado por</th>
                                </tr>
                            </thead>
                            <tbody id="historialPagosBody">
                                {{-- Se llena via JS --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Ver Detalle de Venta (PREMIUM) --}}
    <div class="modal fade" id="modalDetalleVenta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 overflow-hidden shadow-lg">
                <div class="modal-header p-3 bg-info">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <div class="avatar-title bg-white-50 text-white rounded-circle fs-20">
                                    <i class="ri-file-text-line"></i>
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="modal-title text-white fw-bold mb-0 text-uppercase" id="detComprobanteTipo">DETALLE
                                DE VENTA</h5>
                            <span class="text-white-50 fs-12 uppercase" id="detComprobanteNum"></span>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="bg-light-subtle p-3 border-bottom border-bottom-dashed">
                        <div class="row g-3">
                            <div class="col-6">
                                <p class="text-muted text-uppercase fw-semibold fs-11 mb-1">CLIENTE</p>
                                <h6 class="fs-14 mb-0 fw-bold" id="detClienteNombre"></h6>
                                <small class="text-muted" id="detClienteDoc"></small>
                            </div>
                            <div class="col-6 text-end">
                                <p class="text-muted text-uppercase fw-semibold fs-11 mb-1">FECHA EMISIÓN</p>
                                <h6 class="fs-13 mb-0" id="detFechaEmision"></h6>
                            </div>
                        </div>
                    </div>

                    <div class="p-4">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead
                                    class="text-muted table-light border-bottom border-bottom-dashed fs-11 text-uppercase">
                                    <tr>
                                        <th scope="col">Producto</th>
                                        <th scope="col" class="text-center">Cant.</th>
                                        <th scope="col" class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="detProductosBody">
                                    {{-- Se llena vía JS --}}
                                </tbody>
                                <tfoot>
                                    <tr class="border-top border-top-dashed">
                                        <td colspan="2" class="fw-bold text-end">TOTAL VENTA:</td>
                                        <td class="text-end fw-bold text-primary fs-14" id="detTotalVenta"></td>
                                    </tr>
                                    <tr class="table-warning-subtle">
                                        <td colspan="2" class="fw-bold text-end">SALDO PENDIENTE (CRÉDITO):</td>
                                        <td class="text-end fw-bold text-danger fs-14" id="detMontoCredito"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="mb-4 text-center">
                        <div class="badge bg-success-subtle text-success fs-12 p-2">
                            <i class="ri-secure-payment-line me-1"></i> MÉTODO: <span id="detMetodoPago"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-ghost-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-info btn-label waves-effect waves-light"
                        onclick="imprimirActual()">
                        <i class="ri-printer-line label-icon align-middle fs-16 me-2"></i> IMPRIMIR TICKET
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script>
        const ROUTES = {
            pagar: '{{ route('ventas-credito.registrar-pago', ':id') }}',
            historial: '{{ route('ventas-credito.historial-pagos', ':id') }}',
            show: '{{ route('ventas-credito.show', ':id') }}',
            historialGeneral: '{{ route('ventas-credito.historial-general') }}'
        };

        function mostrarToast(mensaje, tipo = 'success') {
            const colors = {
                success: "linear-gradient(to right, #0ab39c, #0ab39c)",
                error: "linear-gradient(to right, #f06548, #f06548)",
                warning: "linear-gradient(to right, #f7b84b, #f7b84b)"
            };

            // Audio de confirmación si es éxito
            if (tipo === 'success') {
                const confirmAudio = new Audio('{{ URL::asset('mp3/sfx-menu6.mp3') }}');
                confirmAudio.play().catch(e => console.log("Audio play blocked"));
            }

            Toastify({
                text: mensaje,
                duration: 3000,
                gravity: "top",
                position: "right",
                style: {
                    background: colors[tipo] || colors.success
                }
            }).showToast();
        }

        // Filtrar
        document.getElementById('formFiltros').addEventListener('submit', function(e) {
            e.preventDefault();
            const fechaInicio = document.getElementById('fecha_inicio').value;
            const fechaFin = document.getElementById('fecha_fin').value;
            window.location.href = `?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
        });

        // Abrir Modal Pago
        function abrirModalPago(id, saldo, cliente) {
            document.getElementById('pagoVentaId').value = id;
            document.getElementById('pagoClienteNombre').textContent = cliente;
            document.getElementById('pagoSaldoPendiente').textContent = saldo.toFixed(2);
            document.getElementById('pagoMonto').value = saldo.toFixed(2);
            document.getElementById('pagoMonto').max = saldo;

            new bootstrap.Modal(document.getElementById('modalPago')).show();
        }

        // Guardar Pago
        document.getElementById('formPago').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('pagoVentaId').value;
            const formData = new FormData(this);
            const btn = document.getElementById('btnGuardarPago');

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';

            fetch(ROUTES.pagar.replace(':id', id), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('modalPago')).hide();
                        mostrarToast('✅ ' + data.message, 'success');

                        // Recargar la página después de un breve delay para que se vean los cambios
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        mostrarToast('❌ ' + (data.message || 'Error al procesar'), 'error');
                    }
                })
                .catch(err => {
                    mostrarToast('❌ Ocurrió un error en el servidor', 'error');
                    console.error(err);
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = 'Confirmar Pago';
                });
        });

        // Ver Historial de Pagos
        function verHistorialPagos(id) {
            const body = document.getElementById('historialPagosBody');
            body.innerHTML =
                '<tr><td colspan="5" class="text-center py-3"><div class="spinner-border text-primary" role="status"></div></td></tr>';

            new bootstrap.Modal(document.getElementById('modalHistorialPagos')).show();

            fetch(ROUTES.historial.replace(':id', id))
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.pagos.length > 0) {
                        body.innerHTML = data.pagos.map(p => `
                        <tr>
                            <td>${new Date(p.fecha_pago).toLocaleDateString()}</td>
                            <td class="fw-bold text-success">S/ ${parseFloat(p.monto).toFixed(2)}</td>
                            <td>${p.metodo_pago}</td>
                            <td>${p.numero_operacion || '-'}</td>
                            <td>${p.user?.name || '-'}</td>
                        </tr>
                    `).join('');
                    } else {
                        body.innerHTML =
                            '<tr><td colspan="5" class="text-center py-3">No hay pagos registrados</td></tr>';
                    }
                });
        }

        function verDetalles(id) {
            const modalElement = document.getElementById('modalDetalleVenta');
            const modal = new bootstrap.Modal(modalElement);

            // Limpiar productos previos
            document.getElementById('detProductosBody').innerHTML =
                '<tr><td colspan="3" class="text-center py-4"><div class="spinner-border spinner-border-sm text-info"></div></td></tr>';

            modal.show();

            fetch(ROUTES.show.replace(':id', id))
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        const d = response.data;

                        // Cabecera
                        document.getElementById('detComprobanteTipo').textContent = d.cabecera.comprobante_tipo;
                        document.getElementById('detComprobanteNum').textContent = d.cabecera.comprobante_num;
                        document.getElementById('detFechaEmision').textContent = d.cabecera.fecha_emision;

                        // Cliente
                        document.getElementById('detClienteNombre').textContent = d.cliente.nombre;
                        document.getElementById('detClienteDoc').textContent = d.cliente.documento === 'S/D' ?
                            'SIN DOCUMENTO' : `DOC: ${d.cliente.documento}`;

                        // Finanzas
                        const simbolo = d.finanzas.simbolo;
                        document.getElementById('detTotalVenta').textContent =
                            `${simbolo} ${d.finanzas.total_venta.toFixed(2)}`;
                        document.getElementById('detMontoCredito').textContent =
                            `${simbolo} ${d.finanzas.monto_credito.toFixed(2)}`;
                        document.getElementById('detMetodoPago').textContent = d.finanzas.metodo_pago;

                        // Productos
                        document.getElementById('detProductosBody').innerHTML = d.productos.map(p => `
                            <tr>
                                <td>
                                    <h6 class="fs-13 mb-0">${p.nombre}</h6>
                                    <small class="text-muted">Unidad: ${p.unidad}</small>
                                </td>
                                <td class="text-center">${p.formato_decimal ? p.cantidad.toFixed(3) : p.cantidad.toFixed(0)}</td>
                                <td class="text-end fw-medium">${simbolo} ${p.subtotal.toFixed(2)}</td>
                            </tr>
                        `).join('');

                        // Guardar ID para impresión si se requiere
                        modalElement.dataset.ventaId = id;
                    } else {
                        mostrarToast('❌ Error al cargar detalles', 'error');
                        modal.hide();
                    }
                })
                .catch(err => {
                    console.error(err);
                    mostrarToast('❌ Error de conexión', 'error');
                    modal.hide();
                });
        }

        function imprimirActual() {
            const id = document.getElementById('modalDetalleVenta').dataset.ventaId;
            if (id) {
                window.open(`/ventas/${id}/pdf/80mm`, '_blank');
            }
        }

        function verHistorialGeneral() {
            window.location.href = ROUTES.historialGeneral;
        }
    </script>
@endsection
