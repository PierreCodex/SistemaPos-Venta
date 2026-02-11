@extends('layouts.master')

@section('title')
    Cerrar Caja
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Cierre de Caja</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('caja.index') }}">Caja</a></li>
                        <li class="breadcrumb-item active">Cierre</li>
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

    <div class="row">
        {{-- Resumen de la Sesión --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header bg-warning-subtle">
                    <h5 class="card-title mb-0">
                        <i class="ri-file-list-3-line me-2"></i>Resumen de Sesión #{{ $cajaAbierta->id }}
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Info de apertura --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Abierta por:</p>
                            <h6>{{ $cajaAbierta->usuario->name ?? 'N/A' }}</h6>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Fecha de apertura:</p>
                            <h6>{{ $cajaAbierta->fecha_apertura->format('d/m/Y H:i') }}</h6>
                        </div>
                    </div>

                    <hr>

                    {{-- Desglose de montos --}}
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="ps-0">
                                        <i class="ri-wallet-line text-primary me-2"></i>
                                        Monto Inicial
                                    </td>
                                    <td class="text-end pe-0 fw-semibold">
                                        S/ {{ number_format($resumen['monto_inicial'], 2) }}
                                    </td>
                                </tr>
                                <tr class="text-success">
                                    <td class="ps-0">
                                        <i class="ri-add-circle-line me-2"></i>
                                        Total Ingresos
                                    </td>
                                    <td class="text-end pe-0 fw-semibold">
                                        + S/ {{ number_format($resumen['total_ingresos'], 2) }}
                                    </td>
                                </tr>
                                <tr class="text-danger">
                                    <td class="ps-0">
                                        <i class="ri-indeterminate-circle-line me-2"></i>
                                        Total Egresos
                                    </td>
                                    <td class="text-end pe-0 fw-semibold">
                                        - S/ {{ number_format($resumen['total_egresos'], 2) }}
                                    </td>
                                </tr>
                                <tr class="border-top">
                                    <td class="ps-0 pt-3">
                                        <strong>
                                            <i class="ri-calculator-line text-info me-2"></i>
                                            MONTO ESPERADO EN CAJA
                                        </strong>
                                    </td>
                                    <td class="text-end pe-0 pt-3">
                                        <h4 class="text-info mb-0" id="montoEsperado">
                                            S/ {{ number_format($resumen['monto_esperado'], 2) }}
                                        </h4>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    {{-- Estadísticas --}}
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-light rounded">
                                <h3 class="text-primary mb-1">{{ $resumen['cantidad_ventas'] }}</h3>
                                <small class="text-muted">Ventas realizadas</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-success-subtle rounded">
                                <h5 class="text-success mb-1">S/ {{ number_format($resumen['total_ventas'], 2) }}</h5>
                                <small class="text-muted">Total vendido</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-info-subtle rounded">
                                <h5 class="text-info mb-1">{{ $resumen['duracion'] }}</h5>
                                <small class="text-muted">Duración turno</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Desglose por concepto --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success-subtle py-2">
                            <h6 class="mb-0 text-success"><i class="ri-add-line me-1"></i> Ingresos por Concepto</h6>
                        </div>
                        <div class="card-body py-2">
                            @forelse($resumen['ingresos_por_concepto'] as $concepto => $total)
                                <div class="d-flex justify-content-between py-1">
                                    <span>{{ \App\Models\CajaMovimiento::conceptos()[$concepto] ?? $concepto }}</span>
                                    <strong class="text-success">S/ {{ number_format($total, 2) }}</strong>
                                </div>
                            @empty
                                <p class="text-muted mb-0 small">Sin ingresos registrados</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-danger">
                        <div class="card-header bg-danger-subtle py-2">
                            <h6 class="mb-0 text-danger"><i class="ri-subtract-line me-1"></i> Egresos por Concepto</h6>
                        </div>
                        <div class="card-body py-2">
                            @forelse($resumen['egresos_por_concepto'] as $concepto => $total)
                                <div class="d-flex justify-content-between py-1">
                                    <span>{{ \App\Models\CajaMovimiento::conceptos()[$concepto] ?? $concepto }}</span>
                                    <strong class="text-danger">S/ {{ number_format($total, 2) }}</strong>
                                </div>
                            @empty
                                <p class="text-muted mb-0 small">Sin egresos registrados</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Formulario de Cierre --}}
        <div class="col-lg-5">
            <div class="card border-warning sticky-top" style="top: 80px;">
                <div class="card-header bg-warning">
                    <h5 class="card-title mb-0 text-dark">
                        <i class="ri-lock-line me-2"></i>Arqueo de Caja
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('caja.cerrar') }}" method="POST" id="formCierre">
                        @csrf
                        
                        {{-- Monto Físico --}}
                        <div class="mb-4">
                            <label for="monto_fisico" class="form-label fw-semibold">
                                <i class="ri-money-dollar-circle-line me-1"></i>
                                Monto Físico Contado <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-warning-subtle fw-bold">S/</span>
                                <input type="number" 
                                       class="form-control form-control-lg @error('monto_fisico') is-invalid @enderror" 
                                       id="monto_fisico" 
                                       name="monto_fisico" 
                                       step="0.01" 
                                       min="0" 
                                       value="{{ old('monto_fisico') }}"
                                       placeholder="0.00"
                                       required
                                       autofocus>
                                @error('monto_fisico')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Ingrese el efectivo que hay físicamente en la caja</small>
                        </div>

                        {{-- Diferencia Calculada --}}
                        <div class="mb-4 p-3 rounded" id="boxDiferencia" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">Diferencia:</span>
                                <h4 class="mb-0" id="valorDiferencia">S/ 0.00</h4>
                            </div>
                            <small id="textoDiferencia" class="text-muted"></small>
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
                                      placeholder="Notas sobre el cierre, justificación de diferencias...">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Botones --}}
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning btn-lg" id="btnCerrar">
                                <i class="ri-lock-line me-1"></i> Cerrar Caja
                            </button>
                            <a href="{{ route('caja.index') }}" class="btn btn-light">
                                <i class="ri-arrow-left-line me-1"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const montoFisicoInput = document.getElementById('monto_fisico');
            const boxDiferencia = document.getElementById('boxDiferencia');
            const valorDiferencia = document.getElementById('valorDiferencia');
            const textoDiferencia = document.getElementById('textoDiferencia');
            const montoEsperado = {{ $resumen['monto_esperado'] }};

            function calcularDiferencia() {
                const montoFisico = parseFloat(montoFisicoInput.value) || 0;
                const diferencia = montoFisico - montoEsperado;
                
                boxDiferencia.style.display = 'block';
                valorDiferencia.textContent = 'S/ ' + diferencia.toFixed(2);
                
                if (diferencia > 0) {
                    boxDiferencia.className = 'mb-4 p-3 rounded bg-success-subtle';
                    valorDiferencia.className = 'mb-0 text-success';
                    textoDiferencia.textContent = 'Hay un SOBRANTE en caja';
                    textoDiferencia.className = 'text-success';
                } else if (diferencia < 0) {
                    boxDiferencia.className = 'mb-4 p-3 rounded bg-danger-subtle';
                    valorDiferencia.className = 'mb-0 text-danger';
                    textoDiferencia.textContent = 'Hay un FALTANTE en caja';
                    textoDiferencia.className = 'text-danger';
                } else {
                    boxDiferencia.className = 'mb-4 p-3 rounded bg-info-subtle';
                    valorDiferencia.className = 'mb-0 text-info';
                    textoDiferencia.textContent = 'La caja cuadra perfectamente';
                    textoDiferencia.className = 'text-info';
                }
            }

            montoFisicoInput.addEventListener('input', calcularDiferencia);
            
            // Focus inicial
            montoFisicoInput.focus();
        });
    </script>
@endsection
