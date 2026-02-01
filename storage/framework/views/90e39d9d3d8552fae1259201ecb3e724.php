
<?php $__env->startSection('title'); ?>
    Gestión de Usuarios
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.css')); ?>">
    <style>
        .user-card {
            transition: all 0.3s ease;
        }

        .user-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .role-badge {
            font-size: 0.7rem;
            padding: 0.35rem 0.65rem;
        }

        .avatar-lg {
            width: 64px;
            height: 64px;
        }

        .form-check-input:checked {
            background-color: var(--vz-primary);
            border-color: var(--vz-primary);
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0"><i class="ri-user-settings-line me-2"></i>Gestión de Usuarios</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Usuarios</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Usuarios</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4" id="totalUsuarios">
                                <span class="counter-value" data-target="<?php echo e($users->count()); ?>"><?php echo e($users->count()); ?></span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                <i class="ri-user-line text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Super Admins</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4" id="totalSuperAdmins">
                                <?php echo e($users->filter(fn($u) => $u->hasRole('super-admin'))->count()); ?>

                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-danger-subtle rounded fs-3">
                                <i class="ri-shield-star-line text-danger"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Administradores</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <?php echo e($users->filter(fn($u) => $u->hasRole('administrador'))->count()); ?>

                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                <i class="ri-admin-line text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Vendedores</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <?php echo e($users->filter(fn($u) => $u->hasRole('vendedor'))->count()); ?>

                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-info-subtle rounded fs-3">
                                <i class="ri-user-star-line text-info"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">
                        <i class="ri-team-line me-1"></i> Lista de Usuarios
                    </h5>
                    <div class="flex-shrink-0">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('usuarios.crear')): ?>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
                                <i class="ri-add-line me-1"></i> Nuevo Usuario
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row" id="usersContainer">
                        
                        <div class="col-12 text-center py-4">
                            <div class="spinner-border text-primary"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white"><i class="ri-user-add-line me-1"></i> Nuevo Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formNuevoUsuario">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nombre Completo <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required
                                    placeholder="Ej: Juan Pérez">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" required
                                    placeholder="usuario@ejemplo.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Contraseña <span
                                        class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="password" required minlength="6">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirmar Contraseña <span
                                        class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="password_confirmation" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Roles</label>
                                <div class="d-flex flex-wrap gap-3" id="rolesCheckboxesCreate">
                                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]"
                                                value="<?php echo e($role->name); ?>" id="roleCreate_<?php echo e($role->id); ?>">
                                            <label class="form-check-label" for="roleCreate_<?php echo e($role->id); ?>">
                                                <?php echo e(ucfirst($role->name)); ?>

                                            </label>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i> Crear Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-dark"><i class="ri-user-settings-line me-1"></i> Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formEditarUsuario">
                    <input type="hidden" name="user_id" id="editUserId">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nombre Completo <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="editName" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" id="editEmail" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nueva Contraseña</label>
                                <input type="password" class="form-control" name="password" minlength="6"
                                    placeholder="Dejar vacío para no cambiar">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirmar Contraseña</label>
                                <input type="password" class="form-control" name="password_confirmation">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Roles</label>
                                <div class="d-flex flex-wrap gap-3" id="rolesCheckboxesEdit">
                                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]"
                                                value="<?php echo e($role->name); ?>" id="roleEdit_<?php echo e($role->id); ?>">
                                            <label class="form-check-label" for="roleEdit_<?php echo e($role->id); ?>">
                                                <?php echo e(ucfirst($role->name)); ?>

                                            </label>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="ri-save-line me-1"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.js')); ?>"></script>
    <script>
        const ROUTES = {
            list: '<?php echo e(route('usuarios.api.list')); ?>',
            store: '<?php echo e(route('usuarios.api.store')); ?>',
            show: '<?php echo e(route('usuarios.api.show', ':id')); ?>',
            update: '<?php echo e(route('usuarios.api.update', ':id')); ?>',
            destroy: '<?php echo e(route('usuarios.api.destroy', ':id')); ?>'
        };

        const PERMISOS = {
            canEdit: <?php echo e(auth()->user()->can('usuarios.editar') ? 'true' : 'false'); ?>,
            canDelete: <?php echo e(auth()->user()->can('usuarios.eliminar') ? 'true' : 'false'); ?>

        };

        document.addEventListener('DOMContentLoaded', function() {
            cargarUsuarios();

            // Form Nuevo Usuario
            document.getElementById('formNuevoUsuario').addEventListener('submit', function(e) {
                e.preventDefault();
                crearUsuario(new FormData(this));
            });

            // Form Editar Usuario
            document.getElementById('formEditarUsuario').addEventListener('submit', function(e) {
                e.preventDefault();
                actualizarUsuario(new FormData(this));
            });
        });

        function cargarUsuarios() {
            fetch(ROUTES.list)
                .then(res => res.json())
                .then(data => {
                    renderizarUsuarios(data.users);
                })
                .catch(err => console.error(err));
        }

        function renderizarUsuarios(users) {
            const container = document.getElementById('usersContainer');

            if (users.length === 0) {
                container.innerHTML = `
                    <div class="col-12 text-center py-5 text-muted">
                        <i class="ri-user-line fs-1 d-block mb-2"></i>
                        <p class="mb-0">No hay usuarios registrados</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = users.map(user => {
                const rolesHtml = user.roles.map(role => {
                    const colors = {
                        'super-admin': 'danger',
                        'administrador': 'success',
                        'vendedor': 'info',
                        'almacenero': 'warning'
                    };
                    return `<span class="badge bg-${colors[role] || 'secondary'}-subtle text-${colors[role] || 'secondary'} role-badge">${role}</span>`;
                }).join(' ');

                // Generar los botones de acciones según permisos
                let botonesAcciones = '';

                if (PERMISOS.canEdit) {
                    botonesAcciones += `
                        <button class="btn btn-soft-primary btn-sm" onclick="editarUsuario(${user.id})">
                            <i class="ri-edit-line"></i>
                        </button>
                    `;
                }

                if (PERMISOS.canDelete && !user.is_current) {
                    botonesAcciones += `
                        <button class="btn btn-soft-danger btn-sm" onclick="eliminarUsuario(${user.id}, '${user.name}')">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    `;
                } else if (user.is_current) {
                    botonesAcciones += `
                        <button class="btn btn-soft-secondary btn-sm" disabled title="Usuario actual">
                            <i class="ri-user-line"></i>
                        </button>
                    `;
                }

                return `
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="card user-card border">
                            <div class="card-body text-center">
                                <div class="avatar-lg mx-auto mb-3">
                                    <img src="${user.avatar ? '/images/' + user.avatar : '<?php echo e(URL::asset('build/images/users/avatar-1.jpg')); ?>'}" 
                                         alt="${user.name}" class="rounded-circle img-fluid">
                                </div>
                                <h5 class="fs-15 mb-1">${user.name}</h5>
                                <p class="text-muted mb-2 fs-12">${user.email}</p>
                                <div class="mb-3">
                                    ${rolesHtml || '<span class="badge bg-light text-muted role-badge">Sin rol</span>'}
                                </div>
                                <div class="d-flex gap-2 justify-content-center">
                                    ${botonesAcciones}
                                </div>
                                <p class="text-muted mb-0 mt-2 fs-11">
                                    <i class="ri-time-line"></i> ${user.created_at}
                                </p>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function crearUsuario(formData) {
            const roles = [];
            formData.getAll('roles[]').forEach(r => roles.push(r));

            fetch(ROUTES.store, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: JSON.stringify({
                        name: formData.get('name'),
                        email: formData.get('email'),
                        password: formData.get('password'),
                        password_confirmation: formData.get('password_confirmation'),
                        roles: roles
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast('✅ ' + data.message, 'success');
                        bootstrap.Modal.getInstance(document.getElementById('modalNuevoUsuario')).hide();
                        document.getElementById('formNuevoUsuario').reset();
                        cargarUsuarios();
                    } else {
                        mostrarToast('❌ ' + data.message, 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    mostrarToast('❌ Error de conexión', 'error');
                });
        }

        function editarUsuario(id) {
            fetch(ROUTES.show.replace(':id', id))
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const user = data.user;
                        document.getElementById('editUserId').value = user.id;
                        document.getElementById('editName').value = user.name;
                        document.getElementById('editEmail').value = user.email;

                        // Limpiar y marcar checkboxes de roles
                        document.querySelectorAll('#rolesCheckboxesEdit input[type="checkbox"]').forEach(cb => {
                            cb.checked = user.roles.includes(cb.value);
                        });

                        new bootstrap.Modal(document.getElementById('modalEditarUsuario')).show();
                    }
                })
                .catch(err => console.error(err));
        }

        function actualizarUsuario(formData) {
            const id = formData.get('user_id');
            const roles = [];
            formData.getAll('roles[]').forEach(r => roles.push(r));

            const payload = {
                name: formData.get('name'),
                email: formData.get('email'),
                roles: roles
            };

            if (formData.get('password')) {
                payload.password = formData.get('password');
                payload.password_confirmation = formData.get('password_confirmation');
            }

            fetch(ROUTES.update.replace(':id', id), {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast('✅ ' + data.message, 'success');
                        bootstrap.Modal.getInstance(document.getElementById('modalEditarUsuario')).hide();
                        cargarUsuarios();
                    } else {
                        mostrarToast('❌ ' + data.message, 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    mostrarToast('❌ Error de conexión', 'error');
                });
        }

        function eliminarUsuario(id, name) {
            Swal.fire({
                title: '¿Eliminar usuario?',
                html: `Se eliminará permanentemente a <strong>${name}</strong>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f06548',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(ROUTES.destroy.replace(':id', id), {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                mostrarToast('✅ ' + data.message, 'success');
                                cargarUsuarios();
                            } else {
                                mostrarToast('❌ ' + data.message, 'error');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            mostrarToast('❌ Error de conexión', 'error');
                        });
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\master\resources\views/usuarios/index.blade.php ENDPATH**/ ?>