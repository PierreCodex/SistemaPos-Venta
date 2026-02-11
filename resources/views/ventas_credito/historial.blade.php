@extends('layouts.master')
@section('title')
    Historial de Pagos a Crédito
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    {{-- Page Title --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-1 fw-bold text-uppercase">
                        <i class="ri-history-line me-2 text-primary"></i>HISTORIAL DE PAGOS A CRÉDITO
                    </h4>
                    <p class="text-muted mb-0 text-uppercase fs-12">Registro completo de todos los cobros realizados a
                        ventas a crédito.</p>
                </div>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ventas-credito.index') }}">Ventas a Crédito</a></li>
                        <li class="breadcrumb-item active">Historial de Pagos</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    {{-- Filtros y Estadísticas --}}
    <div class="row align-items-stretch">
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
                        {{-- Recaudado --}}
                        <div class="text-center p-3 rounded-3 flex-fill bg-light">
                            <p class="text-success text-uppercase fw-bold mb-2 fs-12">Recaudado</p>
                            <h3 class="mb-1 fw-bold text-success">
                                <span class="fs-12 fw-normal text-muted me-1">S/.</span>
                                <span>{{ number_format($totalRecaudado, 2) }}</span>
                            </h3>
                            <p class="text-muted mb-0 fs-13">
                                <span>{{ $pagos->count() }}</span> pagos
                            </p>
                        </div>
                        {{-- Promedio --}}
                        <div class="text-center p-3 rounded-3 flex-fill bg-light">
                            <p class="text-primary text-uppercase fw-bold mb-2 fs-12">Promedio</p>
                            <h3 class="mb-1 fw-bold text-primary">
                                <span class="fs-12 fw-normal text-muted me-1">S/.</span>
                                <span>{{ $pagos->count() > 0 ? number_format($totalRecaudado / $pagos->count(), 2) : '0.00' }}</span>
                            </h3>
                            <p class="text-muted mb-0 fs-13">
                                por pago
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de Pagos --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0 flex-grow-1 text-uppercase fw-bold">
                        <i class="ri-file-list-3-line me-2"></i>REGISTROS DE PAGOS
                    </h5>
                    <a href="{{ route('ventas-credito.index') }}" class="btn btn-soft-primary btn-sm">
                        <i class="ri-arrow-left-line me-1 align-bottom"></i> Volver a Créditos
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-nowrap mb-0" id="tablaPagos">
                            <thead class="table-light text-muted text-uppercase fs-12">
                                <tr>
                                    <th>CÓDIGO</th>
                                    <th>COMPROBANTE</th>
                                    <th>CLIENTE</th>
                                    <th>MÉTODO</th>
                                    <th class="text-end">MONTO</th>
                                    <th>N° OPERACIÓN</th>
                                    <th class="text-center">OBSERV.</th>
                                    <th>REGISTRADO POR</th>
                                    <th>FECHA DE PAGO</th>
                                    <th class="text-center">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pagos as $pago)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary fs-12">
                                                #{{ str_pad($pago->id, 5, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <h6
                                                        class="fs-14 mb-0 fw-bold border-bottom border-primary border-opacity-25 d-inline-block text-uppercase">
                                                        {{ $pago->venta->comprobante }}</h6>
                                                    <div class="text-muted fs-11">
                                                        {{ $pago->venta->serie }}-{{ str_pad($pago->venta->numero, 8, '0', STR_PAD_LEFT) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-uppercase">{{ $pago->venta->nombre_cliente }}</td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info p-2 text-uppercase">
                                                <i class="ri-wallet-3-line me-1 align-middle"></i>{{ $pago->metodo_pago }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success fs-14">S/
                                                {{ number_format($pago->monto, 2) }}</span>
                                        </td>
                                        <td>
                                            @if ($pago->numero_operacion)
                                                <span class="text-body">{{ $pago->numero_operacion }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($pago->observaciones)
                                                <span class="d-inline-block" data-bs-toggle="tooltip"
                                                    title="{{ $pago->observaciones }}">
                                                    <i class="ri-chat-3-line text-primary fs-16"></i>
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <div class="avatar-xxs flex-shrink-0">
                                                    <div class="avatar-title bg-light text-muted rounded-circle">
                                                        <i class="ri-user-settings-line fs-12"></i>
                                                    </div>
                                                </div>
                                                <span class="text-body fs-13">{{ $pago->user->name ?? 'Sistema' }}</span>
                                            </div>
                                        </td>
                                        <td class="text-nowrap text-uppercase">
                                            <div class="fw-medium">{{ $pago->fecha_pago->format('d/m/Y') }}</div>
                                            <div class="text-muted fs-11">{{ $pago->fecha_pago->format('H:i:s') }}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-soft-secondary btn-sm btn-icon dropdown-toggle"
                                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-fill align-middle"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="ri-eye-fill align-bottom me-2 text-muted"></i> Ver
                                                            detalle
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0);"
                                                            onclick="anularPago({{ $pago->id }})">
                                                            <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                                            Anular pago
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10">
                                            <div class="text-center py-5">
                                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                                    colors="primary:#405189,secondary:#0ab39c"
                                                    style="width:75px;height:75px">
                                                </lord-icon>
                                                <h5 class="mt-3">Sin resultados</h5>
                                                <p class="text-muted mb-0">No se encontraron pagos en el rango de fechas
                                                    seleccionado.</p>
                                            </div>
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
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // DataTable init
            $('#tablaPagos').DataTable({
                responsive: false,
                scrollX: true,
                order: [
                    [8, 'desc']
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                }
            });

            // Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(t) {
                return new bootstrap.Tooltip(t);
            });
        });

        function anularPago(id) {
            Swal.fire({
                title: '¿Anular este pago?',
                text: "Esta acción NO se puede deshacer y el saldo de la venta volverá a aumentar.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f06548',
                cancelButtonColor: '#878a99',
                confirmButtonText: '<i class="ri-delete-bin-line me-1"></i> Sí, anular',
                cancelButtonText: 'Cancelar',
                customClass: {
                    confirmButton: 'btn btn-danger btn-sm w-xs me-2',
                    cancelButton: 'btn btn-light btn-sm w-xs'
                },
                buttonsStyling: false
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
