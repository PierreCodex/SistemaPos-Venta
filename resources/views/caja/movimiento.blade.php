@extends('layouts.master')

@section('title')
    Registrar Movimiento de Caja
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Registrar Movimiento</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('caja.index') }}">Caja</a></li>
                        <li class="breadcrumb-item active">Movimiento</li>
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
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-exchange-funds-line me-2"></i>Nuevo Movimiento de Caja
                    </h5>
                </div>
                <div class="card-body p-4">
                    {{-- Estado actual de caja --}}
                    <div class="alert alert-info mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="ri-safe-2-line me-2"></i> Monto actual en caja:</span>
                            <strong class="fs-5">S/ {{ number_format($cajaAbierta->monto_actual, 2) }}</strong>
                        </div>
                    </div>

                    <form action="{{ route('caja.movimiento.store') }}" method="POST" id="formMovimiento">
                        @csrf
                        
                        {{-- Tipo de Movimiento --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Tipo de Movimiento <span class="text-danger">*</span>
                            </label>
                            <div class="row g-3">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="tipo" id="tipoIngreso" value="INGRESO" {{ old('tipo') == 'INGRESO' ? 'checked' : '' }} required>
                                    <label class="btn btn-outline-success w-100 py-3" for="tipoIngreso">
                                        <i class="ri-add-circle-line fs-24 d-block mb-1"></i>
                                        INGRESO
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="tipo" id="tipoEgreso" value="EGRESO" {{ old('tipo') == 'EGRESO' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-danger w-100 py-3" for="tipoEgreso">
                                        <i class="ri-indeterminate-circle-line fs-24 d-block mb-1"></i>
                                        EGRESO
                                    </label>
                                </div>
                            </div>
                            @error('tipo')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Concepto --}}
                        <div class="mb-4">
                            <label for="concepto" class="form-label fw-semibold">
                                Concepto <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('concepto') is-invalid @enderror" 
                                    id="concepto" 
                                    name="concepto" 
                                    required>
                                <option value="">Seleccione un concepto...</option>
                            </select>
                            @error('concepto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Monto --}}
                        <div class="mb-4">
                            <label for="monto" class="form-label fw-semibold">
                                Monto <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text fw-bold">S/</span>
                                <input type="number" 
                                       class="form-control form-control-lg @error('monto') is-invalid @enderror" 
                                       id="monto" 
                                       name="monto" 
                                       step="0.01" 
                                       min="0.01" 
                                       value="{{ old('monto') }}"
                                       placeholder="0.00"
                                       required>
                                @error('monto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Descripción --}}
                        <div class="mb-4">
                            <label for="descripcion" class="form-label fw-semibold">
                                Descripción <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                      id="descripcion" 
                                      name="descripcion" 
                                      rows="3" 
                                      placeholder="Detalle del movimiento..."
                                      required>{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Botones --}}
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('caja.index') }}" class="btn btn-light btn-lg">
                                <i class="ri-arrow-left-line me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg" id="btnGuardar">
                                <i class="ri-save-line me-1"></i> Registrar Movimiento
                            </button>
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
            const conceptosIngreso = @json($conceptosIngreso);
            const conceptosEgreso = @json($conceptosEgreso);
            const selectConcepto = document.getElementById('concepto');
            const tipoInputs = document.querySelectorAll('input[name="tipo"]');

            function actualizarConceptos() {
                const tipoSeleccionado = document.querySelector('input[name="tipo"]:checked');
                if (!tipoSeleccionado) return;

                const conceptos = tipoSeleccionado.value === 'INGRESO' ? conceptosIngreso : conceptosEgreso;
                
                selectConcepto.innerHTML = '<option value="">Seleccione un concepto...</option>';
                
                for (const [key, value] of Object.entries(conceptos)) {
                    const option = document.createElement('option');
                    option.value = key;
                    option.textContent = value;
                    selectConcepto.appendChild(option);
                }
            }

            tipoInputs.forEach(input => {
                input.addEventListener('change', actualizarConceptos);
            });

            // Inicializar si hay valor previo
            actualizarConceptos();
        });
    </script>
@endsection
