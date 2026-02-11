@extends('layouts.master')

@section('title')
    Reporte de Caja
@endsection

@section('css')
    <link href="{{ URL::asset('build/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Reporte de Caja</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('caja.index') }}">Caja</a></li>
                        <li class="breadcrumb-item active">Reporte</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('caja.reporte') }}" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="{{ $fechaInicio }}">
                        </div>
                        <div class="col-md-4">
                            <label for="fecha_fin" class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="{{ $fechaFin }}">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ri-search-line me-1"></i> Filtrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Estadísticas --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-uppercase fw-medium text-muted mb-0">Sesiones</p>
                            <h2 class="mt-4 ff-secondary fw-semibold">{{ $estadisticas['cantidad_sesiones'] }}</h2>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle rounded-circle fs-2">
                                <i class="ri-safe-2-line text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Ventas</p>
                            <h4 class="mt-4 ff-secondary fw-semibold text-success">
                                S/ {{ number_format($estadisticas['total_ventas'], 2) }}
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded-circle fs-2">
                                <i class="ri-shopping-cart-2-line text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Ingresos</p>
                            <h4 class="mt-4 ff-secondary fw-semibold text-info">
                                S/ {{ number_format($estadisticas['total_ingresos'], 2) }}
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-info-subtle rounded-circle fs-2">
                                <i class="ri-add-circle-line text-info"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Egresos</p>
                            <h4 class="mt-4 ff-secondary fw-semibold text-danger">
                                S/ {{ number_format($estadisticas['total_egresos'], 2) }}
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-danger-subtle rounded-circle fs-2">
                                <i class="ri-indeterminate-circle-line text-danger"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Estadísticas de Arqueo --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h1 class="text-success">{{ $estadisticas['sesiones_cuadradas'] }}</h1>
                    <p class="text-muted mb-0">Sesiones Cuadradas</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h1 class="text-info">{{ $estadisticas['sesiones_con_sobrante'] }}</h1>
                    <p class="text-muted mb-0">Con Sobrante</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <h1 class="text-danger">{{ $estadisticas['sesiones_con_faltante'] }}</h1>
                    <p class="text-muted mb-0">Con Faltante</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Historial --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-history-line me-2"></i>Historial de Sesiones
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Apertura</th>
                                    <th>Cierre</th>
                                    <th>Usuario</th>
                                    <th class="text-end">M. Inicial</th>
                                    <th class="text-end">Ventas</th>
                                    <th class="text-end">Ingresos</th>
                                    <th class="text-end">Egresos</th>
                                    <th class="text-end">M. Final</th>
                                    <th class="text-center">Diferencia</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($historial as $sesion)
                                    <tr>
                                        <td><strong>{{ $sesion->id }}</strong></td>
                                        <td>{{ $sesion->fecha_apertura->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($sesion->fecha_cierre)
                                                {{ $sesion->fecha_cierre->format('d/m/Y H:i') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $sesion->usuario->name ?? 'N/A' }}</td>
                                        <td class="text-end">S/ {{ number_format($sesion->monto_inicial, 2) }}</td>
                                        <td class="text-end text-success">S/ {{ number_format($sesion->total_ventas, 2) }}</td>
                                        <td class="text-end text-success">S/ {{ number_format($sesion->total_ingresos, 2) }}</td>
                                        <td class="text-end text-danger">S/ {{ number_format($sesion->total_egresos, 2) }}</td>
                                        <td class="text-end">
                                            @if($sesion->monto_final !== null)
                                                S/ {{ number_format($sesion->monto_final, 2) }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{!! $sesion->badge_diferencia !!}</td>
                                        <td class="text-center">
                                            <a href="{{ route('caja.show', $sesion->id) }}" class="btn btn-soft-info btn-sm">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center text-muted py-4">
                                            No hay sesiones en el período seleccionado
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
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>
@endsection
