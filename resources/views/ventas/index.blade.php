@extends('layouts.master')

@section('title')
    Gestión de Ventas
@endsection

@section('css')
    <link href="{{ URL::asset('build/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        /* Estilo para el botón púrpura del mockup */
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

        /* Estilo para las cajas de resumen */
        .stat-box {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            min-height: 100px;
        }

        [data-layout-mode="dark"] .stat-box {
            background: rgba(255, 255, 255, 0.05);
        }

        [data-layout-mode="light"] .stat-box {
            background: #f3f6f9;
            border: 1px solid #e9ebec;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Gestión de Ventas</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
                        <li class="breadcrumb-item active">Ventas</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row h-100 align-items-stretch">
        {{-- Bloque de Filtros --}}
        <div class="col-lg-8">
            <div class="card card-height-100">
                <div class="card-body p-4">
                    <form id="formFiltros" action="javascript:void(0);">
                        <div class="row g-4 align-items-end">
                            <div class="col-sm-4">
                                <label for="fecha_inicio"
                                    class="form-label fw-semibold text-muted text-uppercase fs-11">Fecha Inicio</label>
                                <div class="input-group">
                                    <input type="text" id="fecha_inicio" class="form-control border-light bg-light"
                                        data-provider="flatpickr" data-date-format="Y-m-d" value="{{ date('Y-m-d') }}">
                                    <span class="input-group-text border-light bg-light"><i
                                            class="ri-calendar-event-line"></i></span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="fecha_fin" class="form-label fw-semibold text-muted text-uppercase fs-11">Fecha
                                    Fin</label>
                                <div class="input-group">
                                    <input type="text" id="fecha_fin" class="form-control border-light bg-light"
                                        data-provider="flatpickr" data-date-format="Y-m-d" value="{{ date('Y-m-d') }}">
                                    <span class="input-group-text border-light bg-light"><i
                                            class="ri-calendar-event-line"></i></span>
                                </div>
                            </div>
                            <div class="col-sm-4">
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
                    <div class="d-flex gap-3 h-100 align-items-center">
                        {{-- Emitidas --}}
                        <div class="stat-box text-center p-3 rounded-3 flex-fill">
                            <p class="text-success text-uppercase fw-bold mb-2 fs-12">Emitidas</p>
                            <h3 class="mb-1 fw-bold text-success">
                                <span class="fs-12 fw-normal text-muted me-1">S/.</span>
                                <span id="totalEmitidas">{{ number_format($estadisticas['emitidas']['total'], 2) }}</span>
                            </h3>
                            <p class="text-muted mb-0 fs-13">
                                <span id="cantidadEmitidas">{{ $estadisticas['emitidas']['cantidad'] }}</span> ventas
                            </p>
                        </div>
                        {{-- Anuladas --}}
                        <div class="stat-box text-center p-3 rounded-3 flex-fill">
                            <p class="text-danger text-uppercase fw-bold mb-2 fs-12">Anuladas</p>
                            <h3 class="mb-1 fw-bold text-danger">
                                <span class="fs-12 fw-normal text-muted me-1">S/.</span>
                                <span id="totalAnuladas">{{ number_format($estadisticas['anuladas']['total'], 2) }}</span>
                            </h3>
                            <p class="text-muted mb-0 fs-13">
                                <span id="cantidadAnuladas">{{ $estadisticas['anuladas']['cantidad'] }}</span> ventas
                            </p>
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
                    <h5 class="card-title mb-0 flex-grow-1">Listado de Comprobantes</h5>
                    <div class="d-flex flex-wrap gap-2">
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
                        @can('ventas.crear')
                            <a href="{{ route('ventas.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center">
                                <i class="ri-add-line fs-18 me-1"></i> <span class="d-none d-md-inline text-uppercase">Nueva
                                    Venta</span>
                                <span class="d-inline d-md-none text-uppercase">Nuevo</span>
                            </a>
                        @endcan
                    </div>
                </div>


                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaVentas" class="table nowrap align-middle table-hover mb-0" style="width:100%">
                            <thead class="table-light text-muted">
                                <tr class="text-uppercase fs-12">
                                    <th>Comprobante</th>
                                    <th>Cliente</th>
                                    <th>Vendedor</th>
                                    <th>Método Pago</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th class="text-center">Estado</th>
                                    <th class="no-exportar text-center" style="width: 150px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ventas as $venta)
                                    <tr data-id="{{ $venta->id }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <h6
                                                        class="fs-14 mb-0 fw-bold border-bottom border-primary border-opacity-25 d-inline-block text-uppercase">
                                                        ${{ $venta->comprobante }}</h6>
                                                    <div class="text-muted fs-11">
                                                        ${{ $venta->serie }}-${{ str_pad($venta->numero, 8, '0', STR_PAD_LEFT) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-uppercase">${{ $venta->nombre_cliente }}</td>
                                        <td class="text-uppercase">${{ $venta->vendedor->name ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info p-2 text-uppercase">
                                                <i
                                                    class="ri-wallet-3-line me-1 align-middle"></i>${{ $venta->metodo_pago }}
                                            </span>
                                        </td>
                                        <td class="text-nowrap text-uppercase">
                                            <div class="fw-medium">${{ $venta->fecha_emision->format('d/m/Y') }}</div>
                                            <div class="text-muted fs-11">${{ $venta->fecha_emision->format('H:i:s') }}
                                            </div>
                                        </td>
                                        <td><span class="fw-bold text-primary fs-14">S/
                                                ${{ number_format($venta->total, 2) }}</span></td>
                                        <td class="text-center text-uppercase">${!! $venta->badge_estado !!}</td>
                                        <td class="text-center no-exportar">
                                            <div class="d-flex justify-content-center gap-1">
                                                <button type="button"
                                                    class="btn btn-sm btn-soft-info btn-icon waves-effect waves-light"
                                                    onclick="verDetalles({{ $venta->id }})" title="Ver detalles">
                                                    <i class="ri-eye-line fs-16"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-soft-secondary btn-icon waves-effect waves-light"
                                                    onclick="imprimirVenta({{ $venta->id }})" title="Imprimir">
                                                    <i class="ri-printer-line fs-16"></i>
                                                </button>
                                                @can('ventas.anular')
                                                    @if ($venta->estado !== 'ANULADA')
                                                        <button type="button"
                                                            class="btn btn-sm btn-soft-danger btn-icon waves-effect waves-light"
                                                            onclick="anularVenta({{ $venta->id }})" title="Anular">
                                                            <i class="ri-close-circle-line fs-16"></i>
                                                        </button>
                                                    @endif
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-5 text-uppercase">
                                            <i class="ri-inbox-line fs-48 d-block mb-2 opacity-25"></i>
                                            No hay ventas registradas
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

    {{-- Modal Ver Detalles --}}
    <div class="modal fade" id="modalDetalles" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">
                        <i class="ri-file-list-3-line me-2"></i>Detalles de Venta
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="contenidoDetalles">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Opciones de la Venta (Diseño Integrado Velzon) --}}
    <div class="modal fade" id="modalImpresion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg overflow-hidden">
                {{-- Header con Estilo Nativo Velzon --}}
                <div class="modal-header bg-primary bg-gradient p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar-xs me-2">
                            <span class="avatar-title bg-white-subtle text-white rounded-circle fs-16">
                                <i class="ri-printer-line"></i>
                            </span>
                        </div>
                        <h5 class="modal-title text-white fw-semibold mb-0">Opciones de Venta</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    {{-- Información de la Venta en Card Nativa --}}
                    <div class="card bg-light-subtle border-dashed border-primary-subtle shadow-none mb-4">
                        <div class="card-body p-3">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <p class="text-muted text-uppercase fw-semibold fs-11 mb-1">Cliente / Titular</p>
                                    <h6 class="fs-14 fw-bold mb-0 text-truncate text-uppercase" id="printCliente">--</h6>
                                    <span class="badge bg-info-subtle text-info mt-1" id="printCodigoLabel">--</span>
                                </div>
                                <div class="col-4 text-end">
                                    <p class="text-muted text-uppercase fw-semibold fs-11 mb-1">Total</p>
                                    <h5 class="text-primary fw-bold mb-0" id="printTotal">--</h5>
                                </div>
                            </div>
                            <div class="border-top border-top-dashed mt-3 pt-3">
                                <div class="row text-center">
                                    <div class="col-6 border-end border-end-dashed">
                                        <p class="text-muted fs-11 mb-1">FECHA</p>
                                        <span class="fw-medium" id="printFecha">--</span>
                                    </div>
                                    <div class="col-6">
                                        <p class="text-muted fs-11 mb-1">ESTADO</p>
                                        <div id="printEstado">--</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Acciones Principales --}}
                    <div id="wrapperAcciones">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label text-muted text-uppercase fs-10 fw-bold mb-2">Formatos de
                                    Impresión</label>
                                <div class="d-grid gap-2">
                                    <button type="button" onclick="descargarPDFFormato('58mm')"
                                        class="btn btn-primary btn-label waves-effect waves-light py-2">
                                        <i class="ri-ticket-2-line label-icon align-middle fs-20 me-2"></i>
                                        Generar Ticket Térmico (58mm)
                                    </button>
                                    <button type="button" onclick="descargarPDFFormato('a4')"
                                        class="btn btn-outline-primary btn-label waves-effect waves-light py-2">
                                        <i class="ri-file-text-line label-icon align-middle fs-20 me-2"></i>
                                        Descargar Documento A4
                                    </button>
                                </div>
                            </div>

                            <div class="col-12 mt-4">
                                <label class="form-label text-muted text-uppercase fs-10 fw-bold mb-2">Canales de
                                    Envío</label>
                                <div class="row g-2">
                                    <div class="col-4">
                                        <button type="button" onclick="mostrarSeccionWhatsApp()"
                                            class="btn btn-soft-success w-100 py-3">
                                            <i class="ri-whatsapp-fill fs-24 d-block mb-1"></i>
                                            <span class="fs-12 fw-medium">WhatsApp</span>
                                        </button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="btn btn-soft-info w-100 py-3">
                                            <i class="ri-telegram-fill fs-24 d-block mb-1"></i>
                                            <span class="fs-12 fw-medium">Telegram</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sección WhatsApp Integrada --}}
                    <div id="seccionWhatsApp" class="d-none animate__animated animate__fadeIn">
                        <div class="p-4 bg-success-subtle rounded text-center border border-success border-opacity-25">
                            <div class="avatar-md mx-auto mb-3">
                                <div class="avatar-title bg-success text-white rounded-circle fs-24">
                                    <i class="ri-whatsapp-line"></i>
                                </div>
                            </div>
                            <h5 class="fw-bold text-success mb-2">Enviar a WhatsApp</h5>
                            <p class="text-muted fs-13 mb-4">Ingrese el número del cliente para enviar los enlaces de
                                descarga.</p>

                            <div class="input-group mb-3 shadow-sm">
                                <span class="input-group-text bg-white border-success border-opacity-25 text-success">
                                    <i class="ri-phone-line"></i>
                                </span>
                                <input type="text" id="inputTelefonoWA"
                                    class="form-control border-success border-opacity-25" placeholder="Ej: 51900000000">
                            </div>

                            <div class="d-flex gap-2">
                                <button type="button" onclick="ocultarSeccionWhatsApp()"
                                    class="btn btn-light flex-grow-1">Cancelar</button>
                                <button type="button" onclick="procesarWhatsAppEnvio()"
                                    class="btn btn-success flex-grow-1">
                                    <i class="ri-send-plane-fill me-1"></i> Enviar Ahora
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
    <script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script>
        const ROUTES = {
            show: '{{ route('ventas.show', ':id') }}',
            destroy: '{{ route('ventas.destroy', ':id') }}',
            filtrar: '{{ route('ventas.filtrar-fechas') }}'
        };

        // Ver detalles de una venta
        function verDetalles(id) {
            const modal = new bootstrap.Modal(document.getElementById('modalDetalles'));
            modal.show();

            fetch(ROUTES.show.replace(':id', id), {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const v = data.venta;
                        document.getElementById('contenidoDetalles').innerHTML = `
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Comprobante:</strong> ${v.comprobante}</p>
                                <p class="mb-1"><strong>Serie-Número:</strong> ${v.serie}-${v.numero}</p>
                                <p class="mb-1"><strong>Cliente:</strong> ${v.cliente?.nombre || 'Cliente General'}</p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <p class="mb-1"><strong>Fecha:</strong> ${new Date(v.fecha_emision).toLocaleString()}</p>
                                <p class="mb-1"><strong>Vendedor:</strong> ${v.vendedor?.name || '-'}</p>
                                <p class="mb-1"><strong>Método:</strong> ${v.metodo_pago}</p>
                            </div>
                        </div>
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">P. Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${v.detalles.map(d => `
                                                                                                            <tr>
                                                                                                                <td>${d.producto?.nombre || 'Producto'}</td>
                                                                                                                <td class="text-center">${parseFloat(d.cantidad).toFixed(2)}</td>
                                                                                                                <td class="text-end">S/ ${parseFloat(d.precio_unitario).toFixed(2)}</td>
                                                                                                                <td class="text-end">S/ ${parseFloat(d.subtotal).toFixed(2)}</td>
                                                                                                            </tr>
                                                                                                        `).join('')}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                    <td class="text-end">S/ ${parseFloat(v.subtotal).toFixed(2)}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>IGV (${v.igv_porcentaje}%):</strong></td>
                                    <td class="text-end">S/ ${parseFloat(v.igv_monto).toFixed(2)}</td>
                                </tr>
                                ${v.descuento > 0 ? `
                                                                                                        <tr>
                                                                                                            <td colspan="3" class="text-end"><strong>Descuento:</strong></td>
                                                                                                            <td class="text-end text-danger">- S/ ${parseFloat(v.descuento).toFixed(2)}</td>
                                                                                                        </tr>` : ''}
                                <tr class="table-primary">
                                    <td colspan="3" class="text-end"><strong>TOTAL:</strong></td>
                                    <td class="text-end"><strong>S/ ${parseFloat(v.total).toFixed(2)}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    `;
                    }
                })
                .catch(() => {
                    document.getElementById('contenidoDetalles').innerHTML =
                        '<p class="text-danger text-center">Error al cargar los detalles</p>';
                });
        }

        // Anular venta
        function anularVenta(id) {
            Swal.fire({
                title: '¿Anular esta venta?',
                text: 'Esta acción devolverá el stock de los productos',
                icon: 'warning',
                input: 'textarea',
                inputLabel: 'Motivo de anulación',
                inputPlaceholder: 'Ingrese el motivo...',
                showCancelButton: true,
                confirmButtonColor: '#f06548',
                confirmButtonText: 'Sí, anular',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(ROUTES.destroy.replace(':id', id), {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                motivo: result.value || 'Sin motivo especificado'
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Anulada', data.message, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        });
                }
            });
        }

        // Variables globales para el modal de impresión
        let ventaSeleccionada = null;

        // Imprimir venta - Abre el modal de opciones
        function imprimirVenta(id) {
            fetch(ROUTES.show.replace(':id', id), {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        ventaSeleccionada = data.venta;

                        // Poblar datos del modal con IDs del diseño original
                        document.getElementById('printCodigoLabel').textContent =
                            `${ventaSeleccionada.serie}-${ventaSeleccionada.numero.toString().padStart(8, '0')}`;
                        document.getElementById('printEstado').innerHTML =
                            `<span class="badge bg-success text-uppercase">Emitido</span>`;
                        document.getElementById('printFecha').textContent = new Date(ventaSeleccionada.fecha_emision)
                            .toLocaleString();
                        document.getElementById('printTotal').textContent =
                            `S/ ${parseFloat(ventaSeleccionada.total).toFixed(2)}`;
                        document.getElementById('printCliente').textContent = ventaSeleccionada.cliente?.nombre ||
                            ventaSeleccionada.nombre_cliente_generico || 'Cliente General';

                        // Resetear vista de WhatsApp si estaba abierta
                        ocultarSeccionWhatsApp();

                        let modalEl = document.getElementById('modalImpresion');
                        let modal = bootstrap.Modal.getInstance(modalEl);
                        if (!modal) {
                            modal = new bootstrap.Modal(modalEl);
                        }
                        modal.show();
                    }
                });
        }

        // Descargar PDF en formato específico
        function descargarPDFFormato(formato) {
            if (!ventaSeleccionada) return;
            const url = `{{ url('ventas') }}/${ventaSeleccionada.id}/pdf/${formato}`;
            window.open(url, '_blank');
        }

        // Funciones para la sección de WhatsApp (ajustadas al nuevo ID wrapperAcciones)
        function mostrarSeccionWhatsApp() {
            document.getElementById('wrapperAcciones').classList.add('d-none');
            document.getElementById('seccionWhatsApp').classList.remove('d-none');
            document.getElementById('inputTelefonoWA').value = ventaSeleccionada.cliente?.telefono || '';
            document.getElementById('inputTelefonoWA').focus();
        }

        function ocultarSeccionWhatsApp() {
            document.getElementById('wrapperAcciones').classList.remove('d-none');
            document.getElementById('seccionWhatsApp').classList.add('d-none');
        }

        function procesarWhatsAppEnvio() {
            const telefono = document.getElementById('inputTelefonoWA').value.replace(/\D/g, '');
            if (!telefono) {
                Toastify({
                    text: "Ingrese un número válido",
                    duration: 3000,
                    className: "bg-danger"
                }).showToast();
                return;
            }

            const codigo = `${ventaSeleccionada.serie}-${ventaSeleccionada.numero.toString().padStart(8, '0')}`;
            const baseUrl = window.location.origin;

            // Generar los enlaces tal como pidió el usuario
            const linkTicket = `${baseUrl}/ticket/${ventaSeleccionada.id}`;
            const linkA4 = `${baseUrl}/ticket-a4/${ventaSeleccionada.id}`;

            const mensaje = `¡Hola! Aquí tienes los enlaces de tu comprobante:\n\n` +
                `📄 Ticket: ${linkTicket}\n` +
                `📄 A4: ${linkA4}`;

            const waUrl = `https://wa.me/${telefono}?text=${encodeURIComponent(mensaje)}`;
            window.open(waUrl, '_blank');
        }




        // Filtrar por fechas
        document.getElementById('formFiltros').addEventListener('submit', function(e) {
            e.preventDefault();
            // Recargar la página con los filtros
            const fechaInicio = document.getElementById('fecha_inicio').value;
            const fechaFin = document.getElementById('fecha_fin').value;
            window.location.href = `?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
        });
    </script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
