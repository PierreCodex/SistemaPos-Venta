@extends('layouts.master')
@section('title')
    Roles y Permisos
@endsection
@section('css')
    <style>
        .permission-group {
            background: var(--vz-card-bg);
            border: 1px solid var(--vz-border-color);
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        .permission-group-header {
            background: var(--vz-light);
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--vz-border-color);
            border-radius: 0.5rem 0.5rem 0 0;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .permission-group-body {
            padding: 1rem;
        }

        .permission-checkbox {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            border-radius: 0.25rem;
            transition: background 0.2s;
        }

        .permission-checkbox:hover {
            background: var(--vz-light);
        }

        .role-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .role-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .role-card.active {
            border-color: var(--vz-primary) !important;
            box-shadow: 0 0 0 2px rgba(64, 81, 137, 0.25);
        }

        .users-count-badge {
            position: absolute;
            top: -8px;
            right: -8px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0"><i class="ri-shield-user-line me-2"></i>Roles y Permisos</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Roles y Permisos</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Panel Izquierdo: Lista de Roles --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">
                        <i class="ri-team-line me-1"></i> Roles del Sistema
                    </h5>
                    @can('roles.crear')
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoRol">
                            <i class="ri-add-line"></i> Nuevo
                        </button>
                    @endcan
                </div>
                <div class="card-body p-0">
                    <div class="p-3" id="rolesContainer">
                        {{-- Se carga vía JS --}}
                        <div class="text-center py-4">
                            <div class="spinner-border spinner-border-sm text-primary"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Estadísticas --}}
            <div class="card">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="p-2">
                                <h4 class="mb-1 text-primary" id="totalRoles">0</h4>
                                <p class="text-muted mb-0 fs-12 text-uppercase">Roles</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2">
                                <h4 class="mb-1 text-success" id="totalPermisos">0</h4>
                                <p class="text-muted mb-0 fs-12 text-uppercase">Permisos</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Panel Derecho: Matriz de Permisos --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0">
                                <i class="ri-checkbox-multiple-line me-1"></i>
                                Permisos del Rol: <span class="text-primary" id="selectedRoleName">Selecciona un rol</span>
                            </h5>
                        </div>
                        <div class="flex-shrink-0">
                            @can('roles.asignar')
                                <button class="btn btn-soft-success btn-sm d-none" id="btnGuardarPermisos"
                                    onclick="guardarPermisos()">
                                    <i class="ri-save-line me-1"></i> Guardar Cambios
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="permissionsMatrix">
                        <div class="text-center py-5 text-muted">
                            <i class="ri-arrow-left-line fs-2 d-block mb-2"></i>
                            <p class="mb-0">Selecciona un rol del panel izquierdo para ver y editar sus permisos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Nuevo Rol --}}
    <div class="modal fade" id="modalNuevoRol" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white"><i class="ri-add-circle-line me-1"></i> Nuevo Rol</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formNuevoRol">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nombre del Rol <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name"
                                placeholder="Ej: Vendedor, Almacenero, Cajero..." required>
                            <small class="text-muted">Solo letras y guiones. Será convertido a minúsculas.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i> Crear Rol
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: Nuevo Permiso --}}
    <div class="modal fade" id="modalNuevoPermiso" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white"><i class="ri-key-2-line me-1"></i> Nuevo Permiso</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formNuevoPermiso">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nombre del Permiso <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name"
                                placeholder="Ej: ventas.crear, productos.eliminar" required>
                            <small class="text-muted">Usa formato: <code>modulo.accion</code> (ej: ventas.ver,
                                productos.eliminar)</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">
                            <i class="ri-save-line me-1"></i> Crear Permiso
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const ROUTES = {
            listRoles: '{{ route('roles.api.list') }}',
            storeRole: '{{ route('roles.api.store') }}',
            updateRole: '{{ route('roles.api.update', ':id') }}',
            destroyRole: '{{ route('roles.api.destroy', ':id') }}',
            rolePermissions: '{{ route('roles.api.permissions', ':id') }}',
            listPermissions: '{{ route('roles.api.permissions.list') }}',
            storePermission: '{{ route('roles.api.permissions.store') }}'
        };

        const PERMISOS_ROLES = {
            canEdit: {{ auth()->user()->can('roles.editar') ? 'true' : 'false' }},
            canDelete: {{ auth()->user()->can('roles.eliminar') ? 'true' : 'false' }},
            canAssign: {{ auth()->user()->can('roles.asignar') ? 'true' : 'false' }},
            canCreate: {{ auth()->user()->can('roles.crear') ? 'true' : 'false' }}
        };

        let selectedRoleId = null;
        let allPermissions = [];
        let allRoles = [];

        document.addEventListener('DOMContentLoaded', function() {
            cargarRoles();
            cargarPermisos();

            // Form Nuevo Rol
            document.getElementById('formNuevoRol').addEventListener('submit', function(e) {
                e.preventDefault();
                crearRol(new FormData(this));
            });

            // Form Nuevo Permiso
            document.getElementById('formNuevoPermiso').addEventListener('submit', function(e) {
                e.preventDefault();
                crearPermiso(new FormData(this));
            });
        });

        function cargarRoles() {
            fetch(ROUTES.listRoles)
                .then(res => res.json())
                .then(data => {
                    allRoles = data.roles;
                    renderizarRoles(data.roles);
                    document.getElementById('totalRoles').textContent = data.roles.length;
                })
                .catch(err => console.error(err));
        }

        function cargarPermisos() {
            fetch(ROUTES.listPermissions)
                .then(res => res.json())
                .then(data => {
                    allPermissions = data.permissions;
                    let total = 0;
                    Object.values(data.permissions).forEach(arr => total += arr.length);
                    document.getElementById('totalPermisos').textContent = total;
                })
                .catch(err => console.error(err));
        }

        function renderizarRoles(roles) {
            const container = document.getElementById('rolesContainer');

            if (roles.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-4 text-muted">
                        <i class="ri-information-line fs-2 d-block mb-2"></i>
                        <p class="mb-0">No hay roles creados</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = roles.map(role => `
                <div class="role-card card border mb-2 position-relative ${selectedRoleId == role.id ? 'active' : ''}" 
                     onclick="seleccionarRol(${role.id}, '${role.name}')" data-role-id="${role.id}">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-16">
                                    <i class="ri-shield-user-fill"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-uppercase">${role.name}</h6>
                                <small class="text-muted">${role.permissions_count || 0} permisos</small>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="badge bg-info-subtle text-info">${role.users_count || 0} usuarios</span>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function seleccionarRol(roleId, roleName) {
            selectedRoleId = roleId;
            document.getElementById('selectedRoleName').textContent = roleName.toUpperCase();
            document.getElementById('btnGuardarPermisos').classList.remove('d-none');

            // Marcar tarjeta activa
            document.querySelectorAll('.role-card').forEach(card => card.classList.remove('active'));
            document.querySelector(`.role-card[data-role-id="${roleId}"]`).classList.add('active');

            // Cargar permisos del rol
            fetch(ROUTES.rolePermissions.replace(':id', roleId))
                .then(res => res.json())
                .then(data => {
                    renderizarMatrizPermisos(data.permissions);
                })
                .catch(err => console.error(err));
        }

        function nombreModulo(modulo) {
            const nombres = {
                'api': 'APIs',
                'dashboard': 'Dashboard',
                'productos': 'Productos',
                'categorias-globales': 'Categorías Globales',
                'categorias': 'Categorías',
                'marcas': 'Marcas',
                'unidades': 'Unidades',
                'clientes': 'Clientes',
                'proveedores': 'Proveedores',
                'ventas': 'Ventas',
                'creditos': 'Créditos',
                'usuarios': 'Usuarios',
                'roles': 'Roles y Permisos',
                'configuracion': 'Configuración',
                'reportes': 'Reportes',
                'compras': 'Compras',
                'inventario': 'Inventario',
                'kardex': 'Kardex',
                'ajustes': 'Ajustes',
                'caja': 'Caja',
                'horarios': 'Horarios',
                'asistencias': 'Asistencias',
            };
            return nombres[modulo] || modulo.charAt(0).toUpperCase() + modulo.slice(1);
        }

        function iconoModulo(modulo) {
            const iconos = {
                'api': 'ri-plug-line',
                'dashboard': 'ri-dashboard-line',
                'productos': 'ri-box-3-line',
                'categorias-globales': 'ri-folders-line',
                'categorias': 'ri-folder-3-line',
                'marcas': 'ri-price-tag-3-line',
                'unidades': 'ri-ruler-line',
                'clientes': 'ri-user-3-line',
                'proveedores': 'ri-truck-line',
                'ventas': 'ri-shopping-cart-2-line',
                'creditos': 'ri-bank-card-line',
                'usuarios': 'ri-user-settings-line',
                'roles': 'ri-shield-user-line',
                'configuracion': 'ri-settings-3-line',
                'reportes': 'ri-bar-chart-2-line',
                'compras': 'ri-shopping-bag-3-line',
                'inventario': 'ri-archive-line',
                'kardex': 'ri-exchange-line',
                'ajustes': 'ri-equalizer-line',
                'caja': 'ri-coins-line',
                'horarios': 'ri-time-line',
                'asistencias': 'ri-calendar-check-line',
            };
            return iconos[modulo] || 'ri-folder-shield-2-line';
        }

        function nombrePermiso(nombre, modulo) {
            if (modulo === 'api') {
                const partes = nombre.split('.');
                // api.productos.ver -> productos.ver
                // api.vigilante.ventas -> vigilante.ventas
                return partes.slice(1).join('.');
            }
            return nombre.split('.')[1] || nombre;
        }

        function renderizarMatrizPermisos(rolePermissions) {
            const container = document.getElementById('permissionsMatrix');

            if (Object.keys(allPermissions).length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5">
                        <i class="ri-key-line fs-2 text-muted d-block mb-2"></i>
                        <p class="text-muted mb-3">No hay permisos definidos en el sistema</p>
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoPermiso">
                            <i class="ri-add-line"></i> Crear Primer Permiso
                        </button>
                    </div>
                `;
                return;
            }

            let html = '<div class="row">';

            for (const [modulo, permisos] of Object.entries(allPermissions)) {
                html += `
                    <div class="col-md-6 col-lg-4">
                        <div class="permission-group">
                            <div class="permission-group-header d-flex align-items-center">
                                <i class="${iconoModulo(modulo)} me-2"></i>
                                ${nombreModulo(modulo)}
                                <button class="btn btn-sm btn-soft-primary ms-auto" onclick="toggleModulo('${modulo}')">
                                    <i class="ri-checkbox-multiple-line"></i>
                                </button>
                            </div>
                            <div class="permission-group-body">
                `;

                permisos.forEach(permiso => {
                    const isChecked = rolePermissions.includes(permiso.name);
                    html += `
                        <div class="permission-checkbox">
                            <div class="form-check">
                                <input class="form-check-input permission-check" type="checkbox"
                                       id="perm_${permiso.id}" value="${permiso.name}"
                                       data-modulo="${modulo}"
                                       ${isChecked ? 'checked' : ''}>
                                <label class="form-check-label" for="perm_${permiso.id}">
                                    ${nombrePermiso(permiso.name, modulo)}
                                </label>
                            </div>
                        </div>
                    `;
                });

                html += `
                            </div>
                        </div>
                    </div>
                `;
            }

            html += '</div>';
            html += `
                <div class="text-end mt-3">
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoPermiso">
                        <i class="ri-add-line"></i> Agregar Permiso
                    </button>
                </div>
            `;

            container.innerHTML = html;
        }

        function toggleModulo(modulo) {
            const checkboxes = document.querySelectorAll(`.permission-check[data-modulo="${modulo}"]`);
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
        }

        function guardarPermisos() {
            if (!selectedRoleId) return;

            const permisos = [];
            document.querySelectorAll('.permission-check:checked').forEach(cb => {
                permisos.push(cb.value);
            });

            fetch(ROUTES.updateRole.replace(':id', selectedRoleId), {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        name: document.getElementById('selectedRoleName').textContent.toLowerCase(),
                        permissions: permisos
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast('✅ Permisos actualizados correctamente', 'success');
                        cargarRoles();
                    } else {
                        mostrarToast('❌ ' + data.message, 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    mostrarToast('❌ Error de conexión', 'error');
                });
        }

        function crearRol(formData) {
            fetch(ROUTES.storeRole, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        name: formData.get('name').toLowerCase()
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast('✅ Rol creado correctamente', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('modalNuevoRol')).hide();
                        document.getElementById('formNuevoRol').reset();
                        cargarRoles();
                    } else {
                        mostrarToast('❌ ' + data.message, 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    mostrarToast('❌ Error de conexión', 'error');
                });
        }

        function crearPermiso(formData) {
            fetch(ROUTES.storePermission, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        name: formData.get('name').toLowerCase()
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        mostrarToast('✅ Permiso creado correctamente', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('modalNuevoPermiso')).hide();
                        document.getElementById('formNuevoPermiso').reset();
                        cargarPermisos();
                        if (selectedRoleId) {
                            seleccionarRol(selectedRoleId, document.getElementById('selectedRoleName').textContent);
                        }
                    } else {
                        mostrarToast('❌ ' + data.message, 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    mostrarToast('❌ Error de conexión', 'error');
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
@endsection
