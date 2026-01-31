@extends('layouts.master')

@section('title')
    Configuración del Sistema
@endsection

@section('css')
    <style>
        .config-section {
            background: var(--vz-card-bg);
            border: 1px solid var(--vz-border-color);
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .config-section-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--vz-heading-color);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--vz-primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo-preview {
            width: 150px;
            height: 150px;
            border: 2px dashed var(--vz-border-color);
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: var(--vz-light);
            position: relative;
        }

        .logo-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .logo-preview .placeholder-icon {
            font-size: 3rem;
            color: var(--vz-secondary-color);
        }

        .logo-actions {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.7);
            padding: 0.5rem;
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .logo-preview:hover .logo-actions {
            opacity: 1;
        }

        .nav-config .nav-link {
            color: var(--vz-body-color);
            padding: 0.75rem 1.25rem;
            border-radius: 0.375rem;
            margin-bottom: 0.25rem;
            transition: all 0.2s;
        }

        .nav-config .nav-link:hover {
            background: var(--vz-light);
        }

        .nav-config .nav-link.active {
            background: var(--vz-primary);
            color: #fff;
        }

        .nav-config .nav-link i {
            width: 24px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0"><i class="ri-settings-3-line me-2"></i>Configuración del Sistema</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Configuración</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Menú lateral --}}
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <nav class="nav flex-column nav-config">
                        <a class="nav-link active" href="#empresa" data-bs-toggle="pill">
                            <i class="ri-building-line me-2"></i> Datos de Empresa
                        </a>
                        <a class="nav-link" href="#impuestos" data-bs-toggle="pill">
                            <i class="ri-percent-line me-2"></i> Impuestos y Moneda
                        </a>
                    </nav>
                </div>
            </div>

            {{-- Info de la configuración actual --}}
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="ri-information-line me-1"></i> Información
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">RUC:</small>
                        <div class="fw-semibold">{{ $empresa->ruc ?? 'No configurado' }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">IGV:</small>
                        <div class="fw-semibold">{{ $empresa->igv_porcentaje ?? 18 }}%</div>
                    </div>
                    <div>
                        <small class="text-muted">Moneda:</small>
                        <div class="fw-semibold">{{ $empresa->moneda ?? 'PEN' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contenido --}}
        <div class="col-lg-9">
            <form id="formConfiguracion">
                <div class="tab-content">
                    {{-- Tab: Datos de Empresa --}}
                    <div class="tab-pane fade show active" id="empresa">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="ri-building-line me-1 text-primary"></i> Datos de la Empresa
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 text-center mb-4">
                                        <label class="form-label fw-semibold">Logo de la Empresa</label>
                                        <div class="logo-preview mx-auto" id="logoPreview">
                                            @if ($empresa->logo)
                                                <img src="{{ asset('storage/' . $empresa->logo) }}" alt="Logo">
                                            @else
                                                <i class="ri-image-line placeholder-icon"></i>
                                            @endif
                                            <div class="logo-actions">
                                                @can('configuracion.editar')
                                                    <label class="btn btn-sm btn-primary mb-0">
                                                        <i class="ri-upload-line"></i>
                                                        <input type="file" id="inputLogo" accept="image/*" class="d-none">
                                                    </label>
                                                    @if ($empresa->logo)
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            onclick="eliminarLogo()">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    @endif
                                                @endcan
                                            </div>
                                        </div>
                                        <small class="text-muted mt-2 d-block">Formatos: JPG, PNG, SVG. Máx: 2MB</small>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Razón Social <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="razon_social"
                                                    value="{{ $empresa->razon_social }}" required
                                                    placeholder="Ej: MI EMPRESA S.A.C.">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Nombre Comercial</label>
                                                <input type="text" class="form-control" name="nombre_comercial"
                                                    value="{{ $empresa->nombre_comercial }}" placeholder="Ej: Mi Tienda">
                                                <small class="text-muted">Este nombre se mostrará en los tickets</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">RUC</label>
                                                <input type="text" class="form-control" name="ruc"
                                                    value="{{ $empresa->ruc }}" maxlength="20"
                                                    placeholder="Ej: 20123456789">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Teléfono</label>
                                                <input type="text" class="form-control" name="telefono"
                                                    value="{{ $empresa->telefono }}" maxlength="50"
                                                    placeholder="Ej: 01-1234567">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Email</label>
                                                <input type="email" class="form-control" name="email"
                                                    value="{{ $empresa->email }}" maxlength="100"
                                                    placeholder="Ej: contacto@miempresa.com">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Dirección</label>
                                                <textarea class="form-control" name="direccion" rows="2" maxlength="500"
                                                    placeholder="Ej: Av. Principal 123, Lima">{{ $empresa->direccion }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tab: Impuestos y Moneda --}}
                    <div class="tab-pane fade" id="impuestos">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="ri-percent-line me-1 text-primary"></i> Configuración de Impuestos y Moneda
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">IGV (%) <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="igv_porcentaje"
                                                value="{{ $empresa->igv_porcentaje ?? 18 }}" min="0"
                                                max="100" step="0.01" required>
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <small class="text-muted">Impuesto General a las Ventas (18% en Perú)</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Moneda <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" name="moneda" required>
                                            <option value="PEN"
                                                {{ ($empresa->moneda ?? 'PEN') == 'PEN' ? 'selected' : '' }}>Soles (PEN) -
                                                S/</option>
                                            <option value="USD"
                                                {{ ($empresa->moneda ?? '') == 'USD' ? 'selected' : '' }}>Dólares (USD) - $
                                            </option>
                                            <option value="EUR"
                                                {{ ($empresa->moneda ?? '') == 'EUR' ? 'selected' : '' }}>Euros (EUR) - €
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="alert alert-info mt-4 mb-0">
                                    <i class="ri-information-line me-2"></i>
                                    <strong>Nota:</strong> El IGV se calcula automáticamente en las ventas según el tipo de
                                    comprobante emitido.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Botón Guardar --}}
                @can('configuracion.editar')
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-light" onclick="location.reload()">
                                    <i class="ri-refresh-line me-1"></i> Recargar
                                </button>
                                <button type="submit" class="btn btn-primary" id="btnGuardar">
                                    <span id="btnGuardarTexto">
                                        <i class="ri-save-line me-1"></i> Guardar Configuración
                                    </span>
                                    <span id="btnGuardarSpinner" class="d-none">
                                        <span class="spinner-border spinner-border-sm me-1"></span> Guardando...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endcan
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const ROUTES = {
            update: '{{ route('configuracion.update') }}',
            uploadLogo: '{{ route('configuracion.upload-logo') }}',
            deleteLogo: '{{ route('configuracion.delete-logo') }}'
        };

        document.addEventListener('DOMContentLoaded', function() {
            // Form submit
            document.getElementById('formConfiguracion').addEventListener('submit', function(e) {
                e.preventDefault();
                guardarConfiguracion(new FormData(this));
            });

            // Upload logo
            document.getElementById('inputLogo')?.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    subirLogo(this.files[0]);
                }
            });
        });

        function guardarConfiguracion(formData) {
            const btn = document.getElementById('btnGuardar');
            const btnTexto = document.getElementById('btnGuardarTexto');
            const btnSpinner = document.getElementById('btnGuardarSpinner');

            btn.disabled = true;
            btnTexto.classList.add('d-none');
            btnSpinner.classList.remove('d-none');

            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });

            fetch(ROUTES.update, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast('✅ ' + data.message, 'success');
                    } else {
                        mostrarToast('❌ ' + data.message, 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    mostrarToast('❌ Error de conexión', 'error');
                })
                .finally(() => {
                    btn.disabled = false;
                    btnTexto.classList.remove('d-none');
                    btnSpinner.classList.add('d-none');
                });
        }

        function subirLogo(file) {
            const formData = new FormData();
            formData.append('logo', file);

            mostrarToast('📤 Subiendo logo...', 'info');

            fetch(ROUTES.uploadLogo, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast('✅ ' + data.message, 'success');
                        // Actualizar preview
                        document.getElementById('logoPreview').innerHTML = `
                        <img src="${data.logo_url}" alt="Logo">
                        <div class="logo-actions">
                            <label class="btn btn-sm btn-primary mb-0">
                                <i class="ri-upload-line"></i>
                                <input type="file" id="inputLogo" accept="image/*" class="d-none" onchange="subirLogo(this.files[0])">
                            </label>
                            <button type="button" class="btn btn-sm btn-danger" onclick="eliminarLogo()">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    `;
                    } else {
                        mostrarToast('❌ ' + data.message, 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    mostrarToast('❌ Error al subir el logo', 'error');
                });
        }

        function eliminarLogo() {
            if (!confirm('¿Eliminar el logo de la empresa?')) return;

            fetch(ROUTES.deleteLogo, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast('✅ ' + data.message, 'success');
                        // Actualizar preview
                        document.getElementById('logoPreview').innerHTML = `
                        <i class="ri-image-line placeholder-icon"></i>
                        <div class="logo-actions">
                            <label class="btn btn-sm btn-primary mb-0">
                                <i class="ri-upload-line"></i>
                                <input type="file" id="inputLogo" accept="image/*" class="d-none" onchange="subirLogo(this.files[0])">
                            </label>
                        </div>
                    `;
                    } else {
                        mostrarToast('❌ ' + data.message, 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    mostrarToast('❌ Error al eliminar el logo', 'error');
                });
        }

        function mostrarToast(mensaje, tipo = 'success') {
            const colors = {
                success: "linear-gradient(to right, #0ab39c, #0ab39c)",
                error: "linear-gradient(to right, #f06548, #f06548)",
                warning: "linear-gradient(to right, #f7b84b, #f7b84b)",
                info: "linear-gradient(to right, #299cdb, #299cdb)"
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
