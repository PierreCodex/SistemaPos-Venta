

<?php $__env->startSection('title'); ?>
    Configuración del Sistema
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0"><i class="ri-settings-3-line me-2"></i>Configuración del Sistema</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Configuración</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        
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
                        <a class="nav-link" href="#facturacion" data-bs-toggle="pill">
                            <i class="ri-shield-check-line me-2"></i> Facturación Electrónica
                        </a>
                    </nav>
                </div>
            </div>

            
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="ri-information-line me-1"></i> Información
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">RUC:</small>
                        <div class="fw-semibold"><?php echo e($empresa->ruc ?? 'No configurado'); ?></div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">IGV:</small>
                        <div class="fw-semibold"><?php echo e($empresa->igv_porcentaje ?? 18); ?>%</div>
                    </div>
                    <div>
                        <small class="text-muted">Moneda:</small>
                        <div class="fw-semibold"><?php echo e($empresa->moneda ?? 'PEN'); ?></div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-lg-9">
            <form id="formConfiguracion">
                <div class="tab-content">
                    
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
                                            <?php if($empresa->logo): ?>
                                                <img src="<?php echo e(asset('storage/' . $empresa->logo)); ?>" alt="Logo">
                                            <?php else: ?>
                                                <i class="ri-image-line placeholder-icon"></i>
                                            <?php endif; ?>
                                            <div class="logo-actions">
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('configuracion.editar')): ?>
                                                    <label class="btn btn-sm btn-primary mb-0">
                                                        <i class="ri-upload-line"></i>
                                                        <input type="file" id="inputLogo" accept="image/*" class="d-none">
                                                    </label>
                                                    <?php if($empresa->logo): ?>
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            onclick="eliminarLogo()">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                <?php endif; ?>
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
                                                    value="<?php echo e($empresa->razon_social); ?>" required
                                                    placeholder="Ej: MI EMPRESA S.A.C.">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Nombre Comercial</label>
                                                <input type="text" class="form-control" name="nombre_comercial"
                                                    value="<?php echo e($empresa->nombre_comercial); ?>" placeholder="Ej: Mi Tienda">
                                                <small class="text-muted">Este nombre se mostrará en los tickets</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">RUC</label>
                                                <input type="text" class="form-control" name="ruc"
                                                    value="<?php echo e($empresa->ruc); ?>" maxlength="20"
                                                    placeholder="Ej: 20123456789">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Teléfono</label>
                                                <input type="text" class="form-control" name="telefono"
                                                    value="<?php echo e($empresa->telefono); ?>" maxlength="50"
                                                    placeholder="Ej: 01-1234567">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Email</label>
                                                <input type="email" class="form-control" name="email"
                                                    value="<?php echo e($empresa->email); ?>" maxlength="100"
                                                    placeholder="Ej: contacto@miempresa.com">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Dirección</label>
                                                <textarea class="form-control" name="direccion" rows="2" maxlength="500"
                                                    placeholder="Ej: Av. Principal 123, Lima"><?php echo e($empresa->direccion); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="tab-pane fade" id="facturacion">
                        <div class="card">
                            <div class="card-header d-flex align-items-center">
                                <h5 class="card-title mb-0 flex-grow-1">
                                    <i class="ri-shield-check-line me-1 text-primary"></i> Credenciales SUNAT
                                </h5>
                                <div class="flex-shrink-0">
                                    <div class="form-check form-switch form-switch-right form-switch-md">
                                        <label for="sunat_produccion" class="form-label text-muted">MODO
                                            PRODUCCIÓN</label>
                                        <input class="form-check-input code-switcher" type="checkbox"
                                            name="sunat_produccion" id="sunat_produccion" value="1"
                                            <?php echo e($empresa->sunat_produccion ? 'checked' : ''); ?>>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning border-0 d-flex align-items-center mb-4" role="alert">
                                    <i class="ri-error-warning-line me-2 fs-20"></i>
                                    <div>
                                        <strong>¡Importante!</strong> Estas credenciales son necesarias para la emisión de
                                        Boletas, Facturas y Notas de Crédito. Si están vacías, el sistema usará las
                                        credenciales de prueba por defecto.
                                    </div>
                                </div>

                                <div class="row g-4">
                                    <div class="col-md-6 border-end border-end-dashed">
                                        <h6 class="fw-bold text-uppercase fs-12 mb-3">Acceso Clave SOL (SEE)</h6>
                                        <div class="mb-3">
                                            <label class="form-label">Usuario SOL</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i
                                                        class="ri-user-settings-line"></i></span>
                                                <input type="text" class="form-control" name="sunat_sol_user"
                                                    value="<?php echo e($empresa->sunat_sol_user); ?>" placeholder="Ej: MODDATOS">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Clave SOL</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i
                                                        class="ri-lock-password-line"></i></span>
                                                <input type="password" class="form-control" name="sunat_sol_pass"
                                                    value="<?php echo e($empresa->sunat_sol_pass); ?>" placeholder="********">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="fw-bold text-uppercase fs-12 mb-3">API Sunat (Opcional / Guías)</h6>
                                        <div class="mb-3">
                                            <label class="form-label">Client ID</label>
                                            <input type="text" class="form-control" name="sunat_client_id"
                                                value="<?php echo e($empresa->sunat_client_id); ?>"
                                                placeholder="ID de la aplicación SUNAT">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Client Secret</label>
                                            <input type="password" class="form-control" name="sunat_client_secret"
                                                value="<?php echo e($empresa->sunat_client_secret); ?>"
                                                placeholder="Secret de la aplicación SUNAT">
                                        </div>
                                    </div>

                                    <div class="col-12 border-top border-top-dashed pt-4">
                                        <h6 class="fw-bold text-uppercase fs-12 mb-3">Certificado Digital (.pem)</h6>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-sm">
                                                <div class="avatar-title bg-light text-primary rounded fs-24">
                                                    <i class="ri-file-shield-2-line"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <?php if($empresa->sunat_cert_path): ?>
                                                    <h6 class="mb-1 text-success"><i
                                                            class="ri-checkbox-circle-fill me-1"></i> Certificado Cargado
                                                    </h6>
                                                    <p class="text-muted mb-0 fs-12"><?php echo e($empresa->sunat_cert_path); ?></p>
                                                <?php else: ?>
                                                    <h6 class="mb-1 text-muted">No se ha cargado un certificado</h6>
                                                    <p class="text-muted mb-0 fs-12">Suba su archivo .pem para firmar los
                                                        comprobantes.</p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <label class="btn btn-soft-primary mb-0">
                                                    <i class="ri-upload-2-line me-1"></i> Subir Certificado
                                                    <input type="file" id="inputCertificado" accept=".pem"
                                                        class="d-none">
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('configuracion.editar')): ?>
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
                <?php endif; ?>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        const ROUTES = {
            update: '<?php echo e(route('configuracion.update')); ?>',
            uploadLogo: '<?php echo e(route('configuracion.upload-logo')); ?>',
            uploadCert: '<?php echo e(route('configuracion.upload-cert')); ?>',
            deleteLogo: '<?php echo e(route('configuracion.delete-logo')); ?>'
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

        // Upload cert
        document.getElementById('inputCertificado')?.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            subirCertificado(this.files[0]);
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

            // Manejar el checkbox de sunat_produccion específicamente
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });

            // Si el checkbox no está en formData, marcar como 0/false
            data['sunat_produccion'] = document.getElementById('sunat_produccion')?.checked ? 1 : 0;

            fetch(ROUTES.update, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
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
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
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

        function subirCertificado(file) {
            const formData = new FormData();
            formData.append('certificado', file);

            mostrarToast('📤 Subiendo certificado...', 'info');

            fetch(ROUTES.uploadCert, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast('✅ ' + data.message, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        mostrarToast('❌ ' + data.message, 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    mostrarToast('❌ Error al subir el certificado', 'error');
                });
        }

        function eliminarLogo() {
            if (!confirm('¿Eliminar el logo de la empresa?')) return;

            fetch(ROUTES.deleteLogo, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\master\resources\views/configuracion/index.blade.php ENDPATH**/ ?>