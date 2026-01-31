@extends('layouts.master')
@section('title')
    Historial de Pagos a Crédito
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .bg-purple {
            background-color: #695eef !important;
        }

        .table-dark-custom {
            background-color: #1a1d21;
            color: #adb5bd;
        }

        .cursor-pointer {
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 text-uppercase"><i class="ri-history-line me-2"></i>Historial de Pagos a Crédito</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active text-uppercase">Historial de Pagos a Crédito</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-body shadow-sm border-0">
                <form id="formFiltros" class="row g-3 align-items-end">
                    <div class="col-md-3 text-uppercase">
                        <label class="form-label fw-bold text-muted fs-12 uppercase">FECHA INICIO</label>
                        <input type="text" class="form-control flatpickr-input border-light" id="fecha_inicio"
                            value="{{ $fechaInicio }}" placeholder="dd-mm-aaaa">
                    </div>
                    <div class="col-md-3 text-uppercase">
                        <label class="form-label fw-bold text-muted fs-12 uppercase">FECHA FIN</label>
                        <input type="text" class="form-control flatpickr-input border-light" id="fecha_fin"
                            value="{{ $fechaFin }}" placeholder="dd-mm-aaaa">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 text-uppercase">
                            <i class="ri-filter-fill me-1 align-bottom"></i> Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Estadísticas y Acciones --}}
    <div class="row mb-3">
        <div class="col-md-6 text-uppercase">
            <p class="text-muted mb-0">
                <i class="ri-list-check me-1"></i> Historial de pagos registrados
            </p>
            <span class="text-muted fs-13">Mostrando: <span class="fw-bold">{{ $pagos->count() }}</span> registros</span>
            <span class="mx-2 text-muted">|</span>
            <span class="text-muted fs-13">Total Recaudado: <span class="fw-bold text-success">S/
                    {{ number_format($totalRecaudado, 2) }}</span></span>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('ventas-credito.index') }}"
                class="btn btn-soft-purple btn-label waves-effect waves-light btn-sm text-uppercase">
                <i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> VOLVER
            </a>
            <button class="btn btn-success btn-sm ms-2 text-uppercase">
                <i class="ri-file-excel-2-line me-1"></i> Excel
            </button>
        </div>
    </div>

    {{-- Listado --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0 pb-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <input type="text" id="buscarPago" class="form-control form-control-sm border-light"
                                placeholder="Buscar pago..." style="max-width: 250px;">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-nowrap mb-0" id="tablaPagos">
                            <thead class="table-light fs-11 text-muted text-uppercase">
                                <tr>
                                    <th>CÓDIGO PAGO</th>
                                    <th>COMPROB. VENTA</th>
                                    <th>CLIENTE</th>
                                    <th>MÉTODO PAGO</th>
                                    <th class="text-end">MONTO PAGADO</th>
                                    <th>N° OPERACIÓN</th>
                                    <th class="text-center">OBSERV.</th>
                                    <th>REGISTRADO POR</th>
                                    <th>FECHA PAGO</th>
                                    <th class="text-center">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pagos as $pago)
                                    <tr>
                                        <td>
                                            <span
                                                class="badge bg-primary-subtle text-primary border border-primary-subtle fs-12 fw-medium">
                                                #{{ $pago->numero_operacion }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <h6 class="fs-13 mb-0">{{ $pago->venta->comprobante }}</h6>
                                                    <small
                                                        class="text-muted">{{ $pago->venta->serie }}-{{ str_pad($pago->venta->numero, 8, '0', STR_PAD_LEFT) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xxs flex-shrink-0 me-2">
                                                    <div class="avatar-title bg-light text-primary rounded-circle">
                                                        <i class="ri-user-line"></i>
                                                    </div>
                                                </div>
                                                <span class="fw-medium">{{ $pago->venta->nombre_cliente }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $metodoIcon = match ($pago->metodo_pago) {
                                                    'EFECTIVO' => 'ri-money-dollar-circle-fill text-success',
                                                    'TRANSFERENCIA' => 'ri-bank-card-fill text-primary',
                                                    'YAPE', 'PLIN' => 'ri-smartphone-line text-info',
                                                    default => 'ri-wallet-3-fill text-muted',
                                                };
                                            @endphp
                                            <span class="badge bg-light text-dark border border-light">
                                                <i class="{{ $metodoIcon }} me-1 align-middle"></i>
                                                {{ $pago->metodo_pago }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <span class="text-success fw-bold fs-14">S/
                                                {{ number_format($pago->monto, 2) }}</span>
                                        </td>
                                        <td>{{ $pago->numero_operacion ?: '-' }}</td>
                                        <td class="text-center">
                                            @if ($pago->observaciones)
                                                <i class="ri-chat-1-line text-muted fs-16 cursor-pointer"
                                                    data-bs-toggle="tooltip" title="{{ $pago->observaciones }}"></i>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ri-user-settings-line text-muted me-1"></i>
                                                {{ $pago->user->name ?? 'Sistema' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <i class="ri-calendar-event-line text-muted me-1"></i>
                                                {{ $pago->fecha_pago->format('d/m/Y') }}
                                            </div>
                                            <div class="text-muted fs-11">
                                                <i class="ri-time-line me-1"></i> {{ $pago->fecha_pago->format('H:i:s') }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-soft-danger btn-icon btn-sm"
                                                onclick="anularPago({{ $pago->id }})">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">No se encontraron pagos registrados en
                                            este rango.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#fecha_inicio", {
                dateFormat: "Y-m-d"
            });
            flatpickr("#fecha_fin", {
                dateFormat: "Y-m-d"
            });

            // Filtros
            document.getElementById('formFiltros').addEventListener('submit', function(e) {
                e.preventDefault();
                const inicio = document.getElementById('fecha_inicio').value;
                const fin = document.getElementById('fecha_fin').value;
                window.location.href = `?fecha_inicio=${inicio}&fecha_fin=${fin}`;
            });

            // Buscador simple
            document.getElementById('buscarPago').addEventListener('keyup', function() {
                const term = this.value.toLowerCase();
                const rows = document.querySelectorAll('#tablaPagos tbody tr');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(term) ? '' : 'none';
                });
            });
        });

        function anularPago(id) {
            Swal.fire({
                title: '¿Anular este pago?',
                text: "Esta acción NO se puede deshacer y el saldo de la venta volverá a aumentar.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f06548',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, anular',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    mostrarToast('Funcionalidad en desarrollo', 'warning');
                }
            });
        }

        function mostrarToast(mensaje, tipo = 'success') {
            const colors = {
                success: "linear-gradient(to right, #0ab39c, #0ab39c)",
                error: "linear-gradient(to right, #f06548, #f06548)",
                warning: "linear-gradient(to right, #f7b84b, #f7b84b)"
            };

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
    </script>
@endsection
