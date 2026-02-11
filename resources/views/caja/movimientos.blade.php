@extends('layouts.master')

@section('title')
    Movimientos de Caja
@endsection

@section('css')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Movimientos - Sesión #{{ $cajaSesion->id }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('caja.index') }}">Caja</a></li>
                        <li class="breadcrumb-item active">Movimientos</li>
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

    {{-- Tabla de Movimientos --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-exchange-funds-line me-2"></i>Lista de Movimientos
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaMovimientos" class="table table-hover align-middle" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha/Hora</th>
                                    <th>Tipo</th>
                                    <th>Concepto</th>
                                    <th>Descripción</th>
                                    <th>Usuario</th>
                                    <th class="text-end">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($movimientos as $movimiento)
                                    <tr>
                                        <td>{{ $movimiento->id }}</td>
                                        <td>{{ $movimiento->created_at->format('d/m/Y H:i:s') }}</td>
                                        <td>{!! $movimiento->badge_tipo !!}</td>
                                        <td>{{ $movimiento->concepto_texto }}</td>
                                        <td>
                                            <span class="text-truncate d-inline-block" style="max-width: 250px;" title="{{ $movimiento->descripcion }}">
                                                {{ $movimiento->descripcion }}
                                            </span>
                                        </td>
                                        <td>{{ $movimiento->usuario->name ?? 'Sistema' }}</td>
                                        <td class="text-end">{!! $movimiento->monto_formateado !!}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación --}}
                    <div class="d-flex justify-content-center mt-3">
                        {{ $movimientos->links() }}
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
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
@endsection
