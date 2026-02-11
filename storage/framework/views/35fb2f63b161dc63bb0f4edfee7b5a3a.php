<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="<?php echo e(url('/')); ?>" class="logo logo-dark">
            <span class="logo-sm">
                <img src="<?php echo e(URL::asset('build/images/logo-sm.png')); ?>" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="<?php echo e(URL::asset('build/images/logo-dark.png')); ?>" alt="" height="17">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="<?php echo e(url('/')); ?>" class="logo logo-light">
            <span class="logo-sm">
                <img src="<?php echo e(URL::asset('build/images/logo-sm.png')); ?>" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="<?php echo e(URL::asset('build/images/logo-light.png')); ?>" alt="" height="17">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    
    <div class="dropdown sidebar-user m-1 rounded">
        <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
            <span class="d-flex align-items-center gap-2">
                <img class="rounded header-profile-user"
                    src="<?php if(Auth::user()->avatar != ''): ?> <?php echo e(URL::asset('images/' . Auth::user()->avatar)); ?><?php else: ?><?php echo e(URL::asset('build/images/users/avatar-1.jpg')); ?> <?php endif; ?>"
                    alt="Header Avatar">
                <span class="text-start">
                    <span class="d-block fw-medium sidebar-user-name-text"><?php echo e(Auth::user()->name); ?></span>
                    <span class="d-block fs-14 sidebar-user-name-sub-text">
                        <i class="ri ri-circle-fill fs-10 text-success align-baseline"></i>
                        <span class="align-middle">
                            <?php if(Auth::user()->hasRole('super-admin')): ?>
                                Super Admin
                            <?php elseif(Auth::user()->roles->first()): ?>
                                <?php echo e(ucfirst(Auth::user()->roles->first()->name)); ?>

                            <?php else: ?>
                                Usuario
                            <?php endif; ?>
                        </span>
                    </span>
                </span>
            </span>
        </button>
        <div class="dropdown-menu dropdown-menu-end">
            <h6 class="dropdown-header">Bienvenido <?php echo e(Auth::user()->name); ?>!</h6>
            <a class="dropdown-item" href="<?php echo e(route('root')); ?>">
                <i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i>
                <span class="align-middle">Perfil</span>
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="javascript:void();"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i>
                <span>Cerrar Sesión</span>
            </a>
            <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                <?php echo csrf_field(); ?>
            </form>
        </div>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>

            <ul class="navbar-nav" id="navbar-nav">

                
                <li class="menu-title"><span>MENÚ PRINCIPAL</span></li>

                
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('dashboard.ver')): ?>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="<?php echo e(url('/')); ?>">
                            <i class="ri-dashboard-2-line"></i> <span>Dashboard</span>
                        </a>
                    </li>
                <?php endif; ?>

                
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['categorias.ver', 'productos.ver', 'marcas.ver', 'unidades.ver', 'proveedores.ver'])): ?>
                    <li class="menu-title"><i class="ri-more-fill"></i> <span>CATÁLOGO</span></li>

                    
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['categorias.ver', 'marcas.ver', 'unidades.ver', 'proveedores.ver'])): ?>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarCatalogo" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="sidebarCatalogo">
                                <i class="ri-folder-3-line"></i> <span>Catálogo</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarCatalogo">
                                <ul class="nav nav-sm flex-column">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('categorias.ver')): ?>
                                        <li class="nav-item">
                                            <a class="nav-link menu-link <?php echo e(request()->routeIs('categorias-globales.*') ? 'active' : ''); ?>"
                                                href="<?php echo e(route('categorias-globales.index')); ?>">Categorías Globales</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link menu-link <?php echo e(request()->routeIs('categorias.*') ? 'active' : ''); ?>"
                                                href="<?php echo e(route('categorias.index')); ?>">Categorías</a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('marcas.ver')): ?>
                                        <li class="nav-item">
                                            <a class="nav-link menu-link <?php echo e(request()->routeIs('marcas.*') ? 'active' : ''); ?>"
                                                href="<?php echo e(route('marcas.index')); ?>">Marcas</a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('unidades.ver')): ?>
                                        <li class="nav-item">
                                            <a class="nav-link menu-link <?php echo e(request()->routeIs('unidades.*') ? 'active' : ''); ?>"
                                                href="<?php echo e(route('unidades.index')); ?>">Unidades</a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('proveedores.ver')): ?>
                                        <li class="nav-item">
                                            <a class="nav-link menu-link <?php echo e(request()->routeIs('proveedores.*') ? 'active' : ''); ?>"
                                                href="<?php echo e(route('proveedores.index')); ?>">Proveedores</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </li>
                    <?php endif; ?>

                    
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('productos.ver')): ?>
                        <li class="nav-item">
                            <a class="nav-link menu-link <?php echo e(request()->routeIs('productos.*') ? 'active' : ''); ?>"
                                href="<?php echo e(route('productos.index')); ?>">
                                <i class="ri-shopping-bag-line"></i> <span>Productos</span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['ventas.ver', 'creditos.ver'])): ?>
                    <li class="menu-title"><i class="ri-more-fill"></i> <span>VENTAS</span></li>

                    
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarVentas" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarVentas">
                            <i class="ri-shopping-cart-line"></i> <span>Ventas</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarVentas">
                            <ul class="nav nav-sm flex-column">

                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ventas.crear')): ?>
                                    <li class="nav-item">
                                        <a class="nav-link menu-link <?php echo e(request()->routeIs('ventas.create') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('ventas.create')); ?>">
                                            <i class="ri-shopping-cart-2-line"></i> <span>Nueva Venta</span>
                                            <span class="badge bg-danger ms-auto">POS</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ventas.ver')): ?>
                                    <li class="nav-item">
                                        <a class="nav-link menu-link <?php echo e(request()->routeIs('ventas.index') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('ventas.index')); ?>">
                                            <i class="ri-file-list-3-line"></i> <span>Historial Ventas</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('creditos.ver')): ?>
                                    <li class="nav-item">
                                        <a class="nav-link menu-link <?php echo e(request()->routeIs('ventas-credito.index') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('ventas-credito.index')); ?>">
                                            <i class="ri-hand-coin-line"></i> <span>Ventas a Crédito</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('creditos.historial')): ?>
                                    <li class="nav-item">
                                        <a class="nav-link menu-link <?php echo e(request()->routeIs('ventas-credito.historial-general') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('ventas-credito.historial-general')); ?>">
                                            <i class="ri-history-line"></i> <span>Pagos Créditos</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                            </ul>
                        </div>
                    </li>
                <?php endif; ?>

                
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('caja.ver')): ?>
                    <li class="nav-item">
                        <a class="nav-link menu-link <?php echo e(request()->routeIs('caja.*') ? 'active' : ''); ?>"
                            href="<?php echo e(route('caja.index')); ?>">
                            <i class="ri-safe-2-line"></i>
                            <span>Caja</span>
                            <?php
                                $cajaAbierta = \App\Models\CajaSesion::abierta()->exists();
                            ?>
                            <?php if($cajaAbierta): ?>
                                <span class="badge bg-success ms-auto">Abierta</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark ms-auto">Cerrada</span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>

                
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('compras.ver')): ?>
                    <li class="menu-title"><i class="ri-more-fill"></i> <span>COMPRAS</span></li>

                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarCompras" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarCompras">
                            <i class="ri-shopping-bag-3-line"></i> <span>Compras</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarCompras">
                            <ul class="nav nav-sm flex-column">

                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('compras.crear')): ?>
                                    <li class="nav-item">
                                        <a class="nav-link menu-link <?php echo e(request()->routeIs('compras.create') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('compras.create')); ?>">
                                            <i class="ri-add-line"></i> <span>Nueva Compra</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                
                                <li class="nav-item">
                                    <a class="nav-link menu-link <?php echo e(request()->routeIs('compras.index') ? 'active' : ''); ?>"
                                        href="<?php echo e(route('compras.index')); ?>">
                                        <i class="ri-file-list-3-line"></i> <span>Historial Compras</span>
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </li>
                <?php endif; ?>

                
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('inventario.ver')): ?>
                    <li class="menu-title"><i class="ri-more-fill"></i> <span>INVENTARIO</span></li>

                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarInventario" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarInventario">
                            <i class="ri-stack-line"></i> <span>Inventario</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarInventario">
                            <ul class="nav nav-sm flex-column">

                                
                                <li class="nav-item">
                                    <a class="nav-link menu-link <?php echo e(request()->routeIs('inventario.kardex.*') ? 'active' : ''); ?>"
                                        href="<?php echo e(route('inventario.kardex.index')); ?>">
                                        <i class="ri-history-line"></i> <span>Kardex (Movimientos)</span>
                                    </a>
                                </li>

                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('inventario.ajustar')): ?>
                                    <li class="nav-item">
                                        <a class="nav-link menu-link <?php echo e(request()->routeIs('inventario.ajustes.*') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('inventario.ajustes.index')); ?>">
                                            <i class="ri-settings-4-line"></i> <span>Ajustes de Stock</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                            </ul>
                        </div>
                    </li>
                <?php endif; ?>

                
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['reportes.ventas', 'reportes.productos', 'reportes.creditos', 'reportes.caja'])): ?>
                    <li class="menu-title"><i class="ri-more-fill"></i> <span>REPORTES</span></li>

                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarReportes" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarReportes">
                            <i class="ri-bar-chart-box-line"></i> <span>Reportes</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarReportes">
                            <ul class="nav nav-sm flex-column">
                                <?php if(Auth::user()->hasRole(['Admin', 'super-admin']) || Auth::user()->can('reportes.ventas')): ?>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">Ventas del Día</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">Ventas Mensuales</a>
                                    </li>
                                <?php endif; ?>
                                <?php if(Auth::user()->hasRole(['Admin', 'super-admin']) || Auth::user()->can('reportes.productos')): ?>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">Productos más Vendidos</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">Inventario</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>

                    
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['horarios.ver', 'asistencias.ver', 'asistencias.registrar'])): ?>
                        <li class="menu-title"><i class="ri-more-fill"></i> <span>RECURSOS HUMANOS</span></li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarRRHH" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="sidebarRRHH">
                                <i class="ri-team-line"></i> <span>Personal</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarRRHH">
                                <ul class="nav nav-sm flex-column">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('horarios.ver')): ?>
                                        <?php if(Auth::user()->hasAnyRole(['super-admin', 'Admin', 'administrador'])): ?>
                                            <li class="nav-item">
                                                <a href="<?php echo e(route('horarios.index')); ?>"
                                                    class="nav-link <?php echo e(request()->routeIs('horarios.*') ? 'active' : ''); ?>">Horarios</a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('asistencias.ver')): ?>
                                        <?php if(Auth::user()->hasAnyRole(['super-admin', 'Admin', 'administrador'])): ?>
                                            
                                            <li class="nav-item">
                                                <a href="<?php echo e(route('asistencias.index')); ?>"
                                                    class="nav-link <?php echo e(request()->routeIs('asistencias.index') ? 'active' : ''); ?>">Asistencias</a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <li class="nav-item">
                                            <a href="<?php echo e(route('asistencias.show', Auth::id())); ?>"
                                                class="nav-link <?php echo e(request()->routeIs('asistencias.show') ? 'active' : ''); ?>">Mi
                                                Asistencia</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </li>
                    <?php endif; ?>

                    
                    <?php if(Auth::user()->hasRole(['Admin', 'super-admin']) ||
                            Auth::user()->canAny(['usuarios.ver', 'roles.ver', 'configuracion.ver'])): ?>
                        <li class="menu-title"><i class="ri-more-fill"></i> <span>CONFIGURACIÓN</span></li>

                        
                        <?php if(Auth::user()->hasRole(['Admin', 'super-admin']) || Auth::user()->can('usuarios.ver')): ?>
                            <li class="nav-item">
                                <a class="nav-link menu-link <?php echo e(request()->routeIs('usuarios.*') ? 'active' : ''); ?>"
                                    href="<?php echo e(route('usuarios.index')); ?>">
                                    <i class="ri-user-settings-line"></i> <span>Usuarios</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        
                        <?php if(Auth::user()->hasRole(['Admin', 'super-admin']) || Auth::user()->can('roles.ver')): ?>
                            <li class="nav-item">
                                <a class="nav-link menu-link <?php echo e(request()->routeIs('roles.*') ? 'active' : ''); ?>"
                                    href="<?php echo e(route('roles.index')); ?>">
                                    <i class="ri-shield-user-line"></i> <span>Roles y Permisos</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        
                        <?php if(Auth::user()->hasRole(['Admin', 'super-admin']) || Auth::user()->can('configuracion.ver')): ?>
                            <li class="nav-item">
                                <a class="nav-link menu-link <?php echo e(request()->routeIs('configuracion.*') ? 'active' : ''); ?>"
                                    href="<?php echo e(route('configuracion.index')); ?>">
                                    <i class="ri-settings-3-line"></i> <span>Configuración</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                </ul>
            </div>
        </div>

        <div class="sidebar-background"></div>
    </div>
    <!-- Left Sidebar End -->
    <div class="vertical-overlay"></div>
<?php /**PATH C:\xampp\htdocs\master\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>