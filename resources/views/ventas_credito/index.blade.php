@extends('layouts.master')

@section('title')
    Ventas a Crédito
@endsection

@section('css')
    <link href="{{ URL::asset('build/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
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
                    <x-filtros-fecha :fechaInicio="$fechaInicio" :fechaFin="$fechaFin" />
                </div>
            </div>
        </div>

        {{-- Bloque de Resumen --}}
        <div class="col-lg-4">
            <div class="card card-height-100">
                <div class="card-body p-4">
                    <div class="d-flex gap-3 h-100 align-items-center">
                        {{-- Total --}}
                        <div class="text-center p-3 rounded-3 flex-fill bg-light">
                            <p class="text-success text-uppercase fw-bold mb-2 fs-12">Total</p>
                            <h3 class="mb-1 fw-bold text-success">
                                <span class="fs-12 fw-normal text-muted me-1">S/.</span>
                                <span>{{ number_format($estadisticas['total'], 2) }}</span>
                            </h3>
                            <p class="text-muted mb-0 fs-13">Ventas a crédito</p>
                        </div>
                        {{-- Saldo Pendiente --}}
                        <div class="text-center p-3 rounded-3 flex-fill bg-light">
                            <p class="text-warning text-uppercase fw-bold mb-2 fs-12">Saldo Pendiente</p>
                            <h3 class="mb-1 fw-bold text-warning">
                                <span class="fs-12 fw-normal text-muted me-1">S/.</span>
                                <span>{{ number_format($estadisticas['saldo_pendiente'], 2) }}</span>
                            </h3>
                            <p class="text-muted mb-0 fs-13">Por cobrar</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0 flex-grow-1 text-uppercase fw-bold"><i class="ri-table-line me-1"></i>
                        REGISTROS DE CRÉDITOS</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button"
                            class="btn btn-purple btn-label waves-effect waves-light d-flex align-items-center shadow-none"
                            onclick="verHistorialGeneral()">
                            <i class="ri-history-line label-icon align-middle fs-16 me-2"></i>
                            <span class="d-none d-sm-inline text-uppercase">Historial de Pagos</span>
                            <span class="d-inline d-sm-none text-uppercase">Historial</span>
                        </button>
                        <button type="button"
                            class="btn btn-success btn-label waves-effect waves-light d-flex align-items-center shadow-none">
                            <i class="ri-file-excel-2-line label-icon align-middle fs-16 me-2"></i>
                            <span class="d-none d-sm-inline text-uppercase">Excel</span>
                        </button>
                    </div>
                </div>


                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaCreditos" class="table nowrap align-middle mb-0" style="width:100%">
                            <thead class="table-light text-muted">
                                <tr class="text-uppercase fs-12">
                                    <th>COMPROBANTE</th>
                                    <th>CLIENTE</th>
                                    <th>VENDEDOR</th>
                                    <th>MÉTODO</th>
                                    <th>FECHA</th>
                                    <th>TOTAL</th>
                                    <th>SALDO PEND.</th>
                                    <th class="text-center">ESTADO</th>
                                    <th class="no-exportar text-center">ACCIONES</th>
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
                                        <td class="text-nowrap text-uppercase">
                                            <div class="fw-medium">{{ $venta->fecha_emision->format('d/m/Y') }}</div>
                                            <div class="text-muted fs-11">{{ $venta->fecha_emision->format('H:i:s') }}
                                            </div>
                                        </td>
                                        <td><span class="text-primary fw-bold">S/
                                                {{ number_format($venta->total, 2) }}</span>
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
                                                        <li><a class="dropdown-item edit-item-btn"
                                                                href="javascript:void(0);"
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
                                        <td colspan="9" class="text-center py-4 text-muted">No hay registros de
                                            créditos en
                                            el rango seleccionado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
                            <label class="form-label fw-semibold text-muted text-uppercase fs-11">Fecha de Pago</label>
                            <div class="input-group">
                                <input type="text" class="form-control border-light bg-light" name="fecha_pago"
                                    data-provider="flatpickr" data-date-format="Y-m-d" value="{{ date('Y-m-d') }}"
                                    required>
                                <span class="input-group-text border-light bg-light"><i
                                        class="ri-calendar-event-line"></i></span>
                            </div>
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
                <div class="modal-header bg-primary">
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#tablaCreditos').DataTable({
                responsive: false,
                scrollX: true,
                order: [
                    [4, 'desc']
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                }
            });
        });
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
                        body.innerHTML = data.pagos.map(p => {
                            const fecha = new Date(p.fecha_pago);
                            const fechaFormateada = fecha.toLocaleDateString('es-PE', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric'
                            });
                            const horaFormateada = fecha.toLocaleTimeString('es-PE', {
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit'
                            });

                            return `
                            <tr>
                                <td class="text-nowrap text-uppercase">
                                    <div class="fw-medium">${fechaFormateada}</div>
                                    <div class="text-muted fs-11">${horaFormateada}</div>
                                </td>
                                <td class="fw-bold text-success">S/ ${parseFloat(p.monto).toFixed(2)}</td>
                                <td>
                                    <span class="badge bg-info-subtle text-info p-1 text-uppercase">${p.metodo_pago}</span>
                                </td>
                                <td class="fs-12">${p.numero_operacion || '-'}</td>
                                <td class="fs-12 text-uppercase">${p.user?.name || '-'}</td>
                            </tr>
                        `
                        }).join('');
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
