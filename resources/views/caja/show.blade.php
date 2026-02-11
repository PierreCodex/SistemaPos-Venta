@extends('layouts.master')

@section('title')
    Detalle de Sesión de Caja
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Sesión de Caja #{{ $cajaSesion->id }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('caja.index') }}">Caja</a></li>
                        <li class="breadcrumb-item active">Sesión #{{ $cajaSesion->id }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Alertas --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-check-double-line me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- Información de la Sesión --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header {{ $cajaSesion->estaAbierta() ? 'bg-success-subtle' : 'bg-secondary-subtle' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Información</h5>
                        {!! $cajaSesion->badge_estado !!}
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted">Apertura:</td>
                                    <td class="text-end fw-semibold">{{ $cajaSesion->fecha_apertura->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Abierta por:</td>
                                    <td class="text-end">{{ $cajaSesion->usuario->name ?? 'N/A' }}</td>
                                </tr>
                                @if($cajaSesion->fecha_cierre)
                                    <tr>
                                        <td class="text-muted">Cierre:</td>
                                        <td class="text-end fw-semibold">{{ $cajaSesion->fecha_cierre->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Cerrada por:</td>
                                        <td class="text-end">{{ $cajaSesion->usuarioCierre->name ?? 'N/A' }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="text-muted">Duración:</td>
                                    <td class="text-end">{{ $cajaSesion->duracion }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Resumen Financiero --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ri-money-dollar-circle-line me-2"></i>Resumen</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Monto Inicial:</span>
                        <strong>S/ {{ number_format($cajaSesion->monto_inicial, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>+ Ingresos:</span>
                        <strong>S/ {{ number_format($resumen['total_ingresos'], 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-danger">
                        <span>- Egresos:</span>
                        <strong>S/ {{ number_format($resumen['total_egresos'], 2) }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">Monto Esperado:</span>
                        <strong class="text-info fs-5">S/ {{ number_format($resumen['monto_esperado'], 2) }}</strong>
                    </div>
                    @if($cajaSesion->monto_final !== null)
                        <div class="d-flex justify-content-between mb-2">
                            <span>Monto Físico:</span>
                            <strong>S/ {{ number_format($cajaSesion->monto_final, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Diferencia:</span>
                            {!! $cajaSesion->badge_diferencia !!}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Estadísticas --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ri-bar-chart-line me-2"></i>Estadísticas</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="p-3 bg-primary-subtle rounded">
                                <h3 class="text-primary mb-0">{{ $resumen['cantidad_ventas'] }}</h3>
                                <small class="text-muted">Ventas</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-success-subtle rounded">
                                <h5 class="text-success mb-0">S/ {{ number_format($resumen['total_ventas'], 2) }}</h5>
                                <small class="text-muted">Total Vendido</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-info-subtle rounded">
                                <h5 class="text-info mb-0">{{ $resumen['cantidad_movimientos'] }}</h5>
                                <small class="text-muted">Total Movimientos</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($cajaSesion->observaciones)
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ri-file-text-line me-2"></i>Observaciones</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-0" style="white-space: pre-line;">{{ $cajaSesion->observaciones }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Movimientos --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ri-exchange-funds-line me-2"></i>Movimientos de Caja
                    </h5>
                    <span class="badge bg-secondary">{{ $cajaSesion->movimientos->count() }} registros</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Hora</th>
                                    <th>Tipo</th>
                                    <th>Concepto</th>
                                    <th>Descripción</th>
                                    <th>Usuario</th>
                                    <th class="text-end">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cajaSesion->movimientos->sortByDesc('created_at') as $movimiento)
                                    <tr>
                                        <td>
                                            <small>{{ $movimiento->created_at->format('H:i:s') }}</small>
                                        </td>
                                        <td>{!! $movimiento->badge_tipo !!}</td>
                                        <td>{{ $movimiento->concepto_texto }}</td>
                                        <td>
                                            <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $movimiento->descripcion }}">
                                                {{ $movimiento->descripcion }}
                                            </span>
                                        </td>
                                        <td>{{ $movimiento->usuario->name ?? 'Sistema' }}</td>
                                        <td class="text-end">{!! $movimiento->monto_formateado !!}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="ri-inbox-line fs-24 d-block mb-2"></i>
                                            No hay movimientos registrados
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Ventas de la sesión --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ri-shopping-cart-2-line me-2"></i>Ventas Realizadas
                    </h5>
                    <span class="badge bg-success">{{ $cajaSesion->ventas->count() }} ventas</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Comprobante</th>
                                    <th>Cliente</th>
                                    <th>Hora</th>
                                    <th>Estado</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cajaSesion->ventas->sortByDesc('fecha_emision') as $venta)
                                    <tr>
                                        <td>
                                            <a href="{{ route('ventas.show', $venta->id) }}" class="fw-semibold">
                                                {{ $venta->comprobante_completo }}
                                            </a>
                                        </td>
                                        <td>{{ $venta->nombre_cliente }}</td>
                                        <td><small>{{ $venta->fecha_emision->format('H:i') }}</small></td>
                                        <td>{!! $venta->badge_estado !!}</td>
                                        <td class="text-end fw-semibold">S/ {{ number_format($venta->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="ri-shopping-cart-line fs-24 d-block mb-2"></i>
                                            No hay ventas en esta sesión
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

    {{-- Botón Volver --}}
    <div class="row">
        <div class="col-12">
            <a href="{{ route('caja.index') }}" class="btn btn-secondary">
                <i class="ri-arrow-left-line me-1"></i> Volver al Panel de Caja
            </a>
            <a href="{{ route('caja.pagos-credito', $cajaSesion->id) }}" class="btn btn-soft-info">
                <i class="ri-hand-coin-line me-1"></i> Pagos de Crédito
            </a>
            <a href="{{ route('caja.movimientos', $cajaSesion->id) }}" class="btn btn-soft-primary">
                <i class="ri-exchange-funds-line me-1"></i> Todos los Movimientos
            </a>
        </div>
    </div>
@endsection
