@extends('layouts.master')

@section('title')
    Pagos de Crédito - Caja
@endsection

@section('css')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Pagos de Crédito Recibidos</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('caja.index') }}">Caja</a></li>
                        <li class="breadcrumb-item active">Pagos de Crédito</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Info de la Sesión --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card bg-light border-0">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            {!! $cajaSesion->badge_estado !!}
                        </div>
                        <div class="col">
                            <span class="text-muted">Sesión #{{ $cajaSesion->id }}</span>
                            <span class="mx-2">|</span>
                            <span class="text-muted">Apertura:</span>
                            <strong>{{ $cajaSesion->fecha_apertura->format('d/m/Y H:i') }}</strong>
                            <span class="mx-2">|</span>
                            <span class="text-muted">Usuario:</span>
                            <strong>{{ $cajaSesion->usuario->name ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('caja.show', $cajaSesion->id) }}" class="btn btn-soft-primary btn-sm">
                                <i class="ri-eye-line me-1"></i> Ver Sesión Completa
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Resumen de Totales --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-success rounded-circle fs-2">
                                <i class="ri-money-dollar-circle-fill text-success"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-uppercase fw-medium text-muted mb-1">Abonos Efectivo</p>
                            <h4 class="mb-0 text-success">S/ {{ number_format($totales['efectivo'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-info rounded-circle fs-2">
                                <i class="ri-bank-card-fill text-info"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-uppercase fw-medium text-muted mb-1">Abonos Otros</p>
                            <h4 class="mb-0 text-info">S/ {{ number_format($totales['otros'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-primary rounded-circle fs-2">
                                <i class="ri-wallet-3-fill text-primary"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-uppercase fw-medium text-muted mb-1">Total Abonos</p>
                            <h4 class="mb-0 text-primary">S/ {{ number_format($totales['total'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-animate text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-light rounded-circle fs-2">
                                <i class="ri-file-list-3-fill text-white"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-uppercase fw-medium text-white-50 mb-1">Cuentas por Cobrar (Nuevas)</p>
                            <h4 class="mb-0 text-white">S/ {{ number_format($totales['total_creditos_otorgados'], 2) }}</h4>
                            <small>{{ $totales['cantidad_creditos'] }} ventas al crédito</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de Pagos --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-hand-coin-line me-2"></i>Pagos de Crédito Recibidos en Esta Sesión
                    </h5>
                </div>
                <div class="card-body">
                    @if ($pagos->count() > 0)
                        <div class="table-responsive">
                            <table id="tablaPagos" class="table table-hover align-middle" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha/Hora</th>
                                        <th>Venta</th>
                                        <th>Cliente</th>
                                        <th>Método</th>
                                        <th>N° Operación</th>
                                        <th>Recibido por</th>
                                        <th class="text-end">Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pagos as $pago)
                                        <tr>
                                            <td>{{ $pago->id }}</td>
                                            <td>{{ $pago->fecha_pago->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('ventas.show', $pago->venta_id) }}" class="fw-medium">
                                                    #{{ $pago->venta_id }}
                                                </a>
                                                @if ($pago->venta)
                                                    <br>
                                                    <small
                                                        class="text-muted">{{ $pago->venta->comprobante_completo ?? '' }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($pago->venta && $pago->venta->cliente)
                                                    {{ $pago->venta->cliente->razon_social ?? $pago->venta->cliente->nombres }}
                                                @else
                                                    <span class="text-muted">Cliente genérico</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $badgeClass = match (strtoupper($pago->metodo_pago)) {
                                                        'EFECTIVO' => 'success',
                                                        'YAPE' => 'primary',
                                                        'PLIN' => 'info',
                                                        'TARJETA' => 'warning',
                                                        'TRANSFERENCIA' => 'secondary',
                                                        default => 'dark',
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $badgeClass }}">{{ $pago->metodo_pago }}</span>
                                            </td>
                                            <td>
                                                <code>{{ $pago->numero_operacion ?? '-' }}</code>
                                            </td>
                                            <td>{{ $pago->user->name ?? 'Sistema' }}</td>
                                            <td class="text-end fw-bold text-success">
                                                S/ {{ number_format($pago->monto, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="7" class="text-end">Total:</th>
                                        <th class="text-end text-success fs-5">S/ {{ number_format($totales['total'], 2) }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="avatar-lg mx-auto mb-4">
                                <div class="avatar-title bg-soft-secondary rounded-circle fs-1">
                                    <i class="ri-hand-coin-line text-secondary"></i>
                                </div>
                            </div>
                            <h5 class="text-muted">No hay pagos de crédito registrados</h5>
                            <p class="text-muted mb-0">Aún no se han recibido pagos de ventas a crédito en esta sesión.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de Ventas al Crédito (Créditos Otorgados) --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-warning border-top border-4">
                <div class="card-header bg-warning-subtle py-3">
                    <h5 class="card-title mb-0 text-dark">
                        <i class="ri-shopping-bag-3-line me-2"></i>Ventas al Crédito Realizadas en Esta Sesión
                    </h5>
                </div>
                <div class="card-body">
                    @if ($ventasCredito->count() > 0)
                        <div class="table-responsive">
                            <table id="tablaVentasCredito" class="table table-hover align-middle mb-0"
                                style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>Comprobante</th>
                                        <th>Cliente</th>
                                        <th>Hora</th>
                                        <th>Estado</th>
                                        <th class="text-end">Monto Total</th>
                                        <th class="text-end">Saldo Pendiente</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ventasCredito as $venta)
                                        <tr>
                                            <td>
                                                <a href="{{ route('ventas.show', $venta->id) }}" class="fw-bold">
                                                    {{ $venta->comprobante_completo }}
                                                </a>
                                            </td>
                                            <td>{{ $venta->nombre_cliente }}</td>
                                            <td><small>{{ $venta->fecha_emision->format('H:i') }}</small></td>
                                            <td>{!! $venta->badge_estado !!}</td>
                                            <td class="text-end fw-bold">S/ {{ number_format($venta->total, 2) }}</td>
                                            <td class="text-end text-danger fw-bold">S/
                                                {{ number_format($venta->saldo_pendiente, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="4" class="text-end">Total Créditos:</th>
                                        <th class="text-end fs-5 text-dark">S/
                                            {{ number_format($totales['total_creditos_otorgados'], 2) }}</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="ri-information-line fs-24 text-muted mb-2 d-block"></i>
                            <p class="text-muted mb-0">No se realizaron ventas al crédito en esta sesión.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Botones Volver --}}
    <div class="row mt-3">
        <div class="col-12">
            <a href="{{ route('caja.index') }}" class="btn btn-secondary">
                <i class="ri-arrow-left-line me-1"></i> Volver al Panel de Caja
            </a>
            <a href="{{ route('caja.show', $cajaSesion->id) }}" class="btn btn-soft-primary">
                <i class="ri-eye-line me-1"></i> Ver Sesión Completa
            </a>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tablaPagos').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
                },
                order: [
                    [1, 'desc']
                ],
                pageLength: 10
            });

            $('#tablaVentasCredito').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
                },
                order: [
                    [2, 'desc']
                ],
                pageLength: 10
            });
        });
    </script>
@endsection
