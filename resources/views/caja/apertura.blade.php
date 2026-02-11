@extends('layouts.master')

@section('title')
    Abrir Caja
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Apertura de Caja</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('caja.index') }}">Caja</a></li>
                        <li class="breadcrumb-item active">Apertura</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Alertas --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <div class="d-flex align-items-center">
                        <i class="ri-lock-unlock-line fs-24 me-2"></i>
                        <h5 class="card-title mb-0 text-white">Abrir Nueva Sesión de Caja</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('caja.abrir') }}" method="POST" id="formApertura">
                        @csrf
                        
                        {{-- Información del Usuario --}}
                        <div class="mb-4 p-3 bg-light rounded">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-primary-subtle rounded-circle">
                                        <i class="ri-user-line text-primary fs-20"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 small">Usuario:</p>
                                    <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                                </div>
                                <div class="ms-auto text-end">
                                    <p class="text-muted mb-0 small">Fecha y Hora:</p>
                                    <h6 class="mb-0">{{ now()->format('d/m/Y H:i') }}</h6>
                                </div>
                            </div>
                        </div>

                        {{-- Monto Inicial --}}
                        <div class="mb-4">
                            <label for="monto_inicial" class="form-label fw-semibold">
                                <i class="ri-money-dollar-circle-line me-1"></i>
                                Monto Inicial en Caja <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-success-subtle text-success fw-bold">S/</span>
                                <input type="number" 
                                       class="form-control form-control-lg @error('monto_inicial') is-invalid @enderror" 
                                       id="monto_inicial" 
                                       name="monto_inicial" 
                                       step="0.01" 
                                       min="0" 
                                       value="{{ old('monto_inicial', 0) }}"
                                       placeholder="0.00"
                                       required
                                       autofocus>
                                @error('monto_inicial')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Ingrese el efectivo con el que inicia el turno</small>
                        </div>

                        {{-- Observaciones --}}
                        <div class="mb-4">
                            <label for="observaciones" class="form-label fw-semibold">
                                <i class="ri-file-text-line me-1"></i>
                                Observaciones (Opcional)
                            </label>
                            <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                      id="observaciones" 
                                      name="observaciones" 
                                      rows="3" 
                                      placeholder="Notas adicionales sobre la apertura...">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Botones --}}
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('caja.index') }}" class="btn btn-light btn-lg">
                                <i class="ri-arrow-left-line me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="ri-lock-unlock-line me-1"></i> Abrir Caja
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Información adicional --}}
            <div class="card border-info">
                <div class="card-body">
                    <h6 class="text-info"><i class="ri-information-line me-1"></i> Información</h6>
                    <ul class="text-muted mb-0 small">
                        <li>El monto inicial representa el efectivo disponible al inicio del turno.</li>
                        <li>Puede ingresar $0 si no hay efectivo inicial.</li>
                        <li>Todas las ventas realizadas se asociarán a esta sesión de caja.</li>
                        <li>Al cerrar la caja, se realizará el arqueo comparando el monto esperado con el físico.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Focus en el input de monto
            document.getElementById('monto_inicial').focus();
            document.getElementById('monto_inicial').select();
        });
    </script>
@endsection
