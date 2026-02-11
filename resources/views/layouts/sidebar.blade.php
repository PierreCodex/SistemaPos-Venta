<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="{{ url('/') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('build/images/logo-dark.png') }}" alt="" height="17">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="{{ url('/') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('build/images/logo-light.png') }}" alt="" height="17">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    {{-- Usuario logueado --}}
    <div class="dropdown sidebar-user m-1 rounded">
        <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
            <span class="d-flex align-items-center gap-2">
                <img class="rounded header-profile-user"
                    src="@if (Auth::user()->avatar != '') {{ URL::asset('images/' . Auth::user()->avatar) }}@else{{ URL::asset('build/images/users/avatar-1.jpg') }} @endif"
                    alt="Header Avatar">
                <span class="text-start">
                    <span class="d-block fw-medium sidebar-user-name-text">{{ Auth::user()->name }}</span>
                    <span class="d-block fs-14 sidebar-user-name-sub-text">
                        <i class="ri ri-circle-fill fs-10 text-success align-baseline"></i>
                        <span class="align-middle">
                            @if (Auth::user()->hasRole('super-admin'))
                                Super Admin
                            @elseif(Auth::user()->roles->first())
                                {{ ucfirst(Auth::user()->roles->first()->name) }}
                            @else
                                Usuario
                            @endif
                        </span>
                    </span>
                </span>
            </span>
        </button>
        <div class="dropdown-menu dropdown-menu-end">
            <h6 class="dropdown-header">Bienvenido {{ Auth::user()->name }}!</h6>
            <a class="dropdown-item" href="{{ route('root') }}">
                <i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i>
                <span class="align-middle">Perfil</span>
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="javascript:void();"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i>
                <span>Cerrar Sesión</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>

            <ul class="navbar-nav" id="navbar-nav">

                {{-- =====================================================
                     MENÚ PRINCIPAL
                ===================================================== --}}
                <li class="menu-title"><span>MENÚ PRINCIPAL</span></li>

                {{-- Dashboard - Siempre visible para usuarios autenticados --}}
                @can('dashboard.ver')
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ url('/') }}">
                            <i class="ri-dashboard-2-line"></i> <span>Dashboard</span>
                        </a>
                    </li>
                @endcan

                {{-- =====================================================
                     CATÁLOGO - Solo si tiene permiso de ver categorías o productos
                ===================================================== --}}
                @canany(['categorias.ver', 'productos.ver', 'marcas.ver', 'unidades.ver', 'proveedores.ver'])
                    <li class="menu-title"><i class="ri-more-fill"></i> <span>CATÁLOGO</span></li>

                    {{-- Submenú Catálogo --}}
                    @canany(['categorias.ver', 'marcas.ver', 'unidades.ver', 'proveedores.ver'])
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarCatalogo" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="sidebarCatalogo">
                                <i class="ri-folder-3-line"></i> <span>Catálogo</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarCatalogo">
                                <ul class="nav nav-sm flex-column">
                                    @can('categorias.ver')
                                        <li class="nav-item">
                                            <a class="nav-link menu-link {{ request()->routeIs('categorias-globales.*') ? 'active' : '' }}"
                                                href="{{ route('categorias-globales.index') }}">Categorías Globales</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link menu-link {{ request()->routeIs('categorias.*') ? 'active' : '' }}"
                                                href="{{ route('categorias.index') }}">Categorías</a>
                                        </li>
                                    @endcan

                                    @can('marcas.ver')
                                        <li class="nav-item">
                                            <a class="nav-link menu-link {{ request()->routeIs('marcas.*') ? 'active' : '' }}"
                                                href="{{ route('marcas.index') }}">Marcas</a>
                                        </li>
                                    @endcan

                                    @can('unidades.ver')
                                        <li class="nav-item">
                                            <a class="nav-link menu-link {{ request()->routeIs('unidades.*') ? 'active' : '' }}"
                                                href="{{ route('unidades.index') }}">Unidades</a>
                                        </li>
                                    @endcan

                                    @can('proveedores.ver')
                                        <li class="nav-item">
                                            <a class="nav-link menu-link {{ request()->routeIs('proveedores.*') ? 'active' : '' }}"
                                                href="{{ route('proveedores.index') }}">Proveedores</a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endcanany

                    {{-- Productos --}}
                    @can('productos.ver')
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('productos.*') ? 'active' : '' }}"
                                href="{{ route('productos.index') }}">
                                <i class="ri-shopping-bag-line"></i> <span>Productos</span>
                            </a>
                        </li>
                    @endcan
                @endcanany

                {{-- =====================================================
                     VENTAS - Solo si tiene permisos de ventas o créditos
                ===================================================== --}}
                @canany(['ventas.ver', 'creditos.ver'])
                    <li class="menu-title"><i class="ri-more-fill"></i> <span>VENTAS</span></li>

                    {{-- Submenú Ventas --}}
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarVentas" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarVentas">
                            <i class="ri-shopping-cart-line"></i> <span>Ventas</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarVentas">
                            <ul class="nav nav-sm flex-column">

                                {{-- Nueva Venta (POS) --}}
                                @can('ventas.crear')
                                    <li class="nav-item">
                                        <a class="nav-link menu-link {{ request()->routeIs('ventas.create') ? 'active' : '' }}"
                                            href="{{ route('ventas.create') }}">
                                            <i class="ri-shopping-cart-2-line"></i> <span>Nueva Venta</span>
                                            <span class="badge bg-danger ms-auto">POS</span>
                                        </a>
                                    </li>
                                @endcan

                                {{-- Historial de Ventas --}}
                                @can('ventas.ver')
                                    <li class="nav-item">
                                        <a class="nav-link menu-link {{ request()->routeIs('ventas.index') ? 'active' : '' }}"
                                            href="{{ route('ventas.index') }}">
                                            <i class="ri-file-list-3-line"></i> <span>Historial Ventas</span>
                                        </a>
                                    </li>
                                @endcan

                                {{-- Ventas a Crédito --}}
                                @can('creditos.ver')
                                    <li class="nav-item">
                                        <a class="nav-link menu-link {{ request()->routeIs('ventas-credito.index') ? 'active' : '' }}"
                                            href="{{ route('ventas-credito.index') }}">
                                            <i class="ri-hand-coin-line"></i> <span>Ventas a Crédito</span>
                                        </a>
                                    </li>
                                @endcan

                                {{-- Historial de Pagos de Crédito --}}
                                @can('creditos.historial')
                                    <li class="nav-item">
                                        <a class="nav-link menu-link {{ request()->routeIs('ventas-credito.historial-general') ? 'active' : '' }}"
                                            href="{{ route('ventas-credito.historial-general') }}">
                                            <i class="ri-history-line"></i> <span>Pagos Créditos</span>
                                        </a>
                                    </li>
                                @endcan

                            </ul>
                        </div>
                    </li>
                @endcanany

                {{-- =====================================================
                     CAJA - Solo si tiene permisos de caja
                ===================================================== --}}
                @can('caja.ver')
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('caja.*') ? 'active' : '' }}"
                            href="{{ route('caja.index') }}">
                            <i class="ri-safe-2-line"></i>
                            <span>Caja</span>
                            @php
                                $cajaAbierta = \App\Models\CajaSesion::abierta()->exists();
                            @endphp
                            @if ($cajaAbierta)
                                <span class="badge bg-success ms-auto">Abierta</span>
                            @else
                                <span class="badge bg-warning text-dark ms-auto">Cerrada</span>
                            @endif
                        </a>
                    </li>
                @endcan

                {{-- =====================================================
                     COMPRAS - Solo si tiene permisos de compras
                ===================================================== --}}
                @can('compras.ver')
                    <li class="menu-title"><i class="ri-more-fill"></i> <span>COMPRAS</span></li>

                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarCompras" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarCompras">
                            <i class="ri-shopping-bag-3-line"></i> <span>Compras</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarCompras">
                            <ul class="nav nav-sm flex-column">

                                {{-- Nueva Compra --}}
                                @can('compras.crear')
                                    <li class="nav-item">
                                        <a class="nav-link menu-link {{ request()->routeIs('compras.create') ? 'active' : '' }}"
                                            href="{{ route('compras.create') }}">
                                            <i class="ri-add-line"></i> <span>Nueva Compra</span>
                                        </a>
                                    </li>
                                @endcan

                                {{-- Historial de Compras --}}
                                <li class="nav-item">
                                    <a class="nav-link menu-link {{ request()->routeIs('compras.index') ? 'active' : '' }}"
                                        href="{{ route('compras.index') }}">
                                        <i class="ri-file-list-3-line"></i> <span>Historial Compras</span>
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </li>
                @endcan

                {{-- =====================================================
                     INVENTARIO - Solo si tiene permisos de inventario
                ===================================================== --}}
                @can('inventario.ver')
                    <li class="menu-title"><i class="ri-more-fill"></i> <span>INVENTARIO</span></li>

                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarInventario" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarInventario">
                            <i class="ri-stack-line"></i> <span>Inventario</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarInventario">
                            <ul class="nav nav-sm flex-column">

                                {{-- Kardex --}}
                                <li class="nav-item">
                                    <a class="nav-link menu-link {{ request()->routeIs('inventario.kardex.*') ? 'active' : '' }}"
                                        href="{{ route('inventario.kardex.index') }}">
                                        <i class="ri-history-line"></i> <span>Kardex (Movimientos)</span>
                                    </a>
                                </li>

                                {{-- Ajustes de Inventario --}}
                                @can('inventario.ajustar')
                                    <li class="nav-item">
                                        <a class="nav-link menu-link {{ request()->routeIs('inventario.ajustes.*') ? 'active' : '' }}"
                                            href="{{ route('inventario.ajustes.index') }}">
                                            <i class="ri-settings-4-line"></i> <span>Ajustes de Stock</span>
                                        </a>
                                    </li>
                                @endcan

                            </ul>
                        </div>
                    </li>
                @endcan

                {{-- =====================================================
                     REPORTES - Solo si tiene permisos de reportes
                ===================================================== --}}
                @canany(['reportes.ventas', 'reportes.productos', 'reportes.creditos', 'reportes.caja'])
                    <li class="menu-title"><i class="ri-more-fill"></i> <span>REPORTES</span></li>

                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarReportes" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarReportes">
                            <i class="ri-bar-chart-box-line"></i> <span>Reportes</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarReportes">
                            <ul class="nav nav-sm flex-column">
                                @if (Auth::user()->hasRole(['Admin', 'super-admin']) || Auth::user()->can('reportes.ventas'))
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">Ventas del Día</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">Ventas Mensuales</a>
                                    </li>
                                @endif
                                @if (Auth::user()->hasRole(['Admin', 'super-admin']) || Auth::user()->can('reportes.productos'))
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">Productos más Vendidos</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">Inventario</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    @endif

                    {{-- =====================================================
                     RECURSOS HUMANOS
                ===================================================== --}}
                    @canany(['horarios.ver', 'asistencias.ver', 'asistencias.registrar'])
                        <li class="menu-title"><i class="ri-more-fill"></i> <span>RECURSOS HUMANOS</span></li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarRRHH" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="sidebarRRHH">
                                <i class="ri-team-line"></i> <span>Personal</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarRRHH">
                                <ul class="nav nav-sm flex-column">
                                    @can('horarios.ver')
                                        @if (Auth::user()->hasAnyRole(['super-admin', 'Admin', 'administrador']))
                                            <li class="nav-item">
                                                <a href="{{ route('horarios.index') }}"
                                                    class="nav-link {{ request()->routeIs('horarios.*') ? 'active' : '' }}">Horarios</a>
                                            </li>
                                        @endif
                                    @endcan
                                    @can('asistencias.ver')
                                        @if (Auth::user()->hasAnyRole(['super-admin', 'Admin', 'administrador']))
                                            {{-- Admin: ve todas las asistencias --}}
                                            <li class="nav-item">
                                                <a href="{{ route('asistencias.index') }}"
                                                    class="nav-link {{ request()->routeIs('asistencias.index') ? 'active' : '' }}">Asistencias</a>
                                            </li>
                                        @endif
                                        {{-- Todos: ven su propio calendario --}}
                                        <li class="nav-item">
                                            <a href="{{ route('asistencias.show', Auth::id()) }}"
                                                class="nav-link {{ request()->routeIs('asistencias.show') ? 'active' : '' }}">Mi
                                                Asistencia</a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endcanany

                    {{-- =====================================================
                     CONFIGURACIÓN - Solo para administradores
                ===================================================== --}}
                    @if (Auth::user()->hasRole(['Admin', 'super-admin']) ||
                            Auth::user()->canAny(['usuarios.ver', 'roles.ver', 'configuracion.ver']))
                        <li class="menu-title"><i class="ri-more-fill"></i> <span>CONFIGURACIÓN</span></li>

                        {{-- Usuarios --}}
                        @if (Auth::user()->hasRole(['Admin', 'super-admin']) || Auth::user()->can('usuarios.ver'))
                            <li class="nav-item">
                                <a class="nav-link menu-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}"
                                    href="{{ route('usuarios.index') }}">
                                    <i class="ri-user-settings-line"></i> <span>Usuarios</span>
                                </a>
                            </li>
                        @endif

                        {{-- Roles y Permisos - --}}
                        @if (Auth::user()->hasRole(['Admin', 'super-admin']) || Auth::user()->can('roles.ver'))
                            <li class="nav-item">
                                <a class="nav-link menu-link {{ request()->routeIs('roles.*') ? 'active' : '' }}"
                                    href="{{ route('roles.index') }}">
                                    <i class="ri-shield-user-line"></i> <span>Roles y Permisos</span>
                                </a>
                            </li>
                        @endif

                        {{-- Configuración General --}}
                        @if (Auth::user()->hasRole(['Admin', 'super-admin']) || Auth::user()->can('configuracion.ver'))
                            <li class="nav-item">
                                <a class="nav-link menu-link {{ request()->routeIs('configuracion.*') ? 'active' : '' }}"
                                    href="{{ route('configuracion.index') }}">
                                    <i class="ri-settings-3-line"></i> <span>Configuración</span>
                                </a>
                            </li>
                        @endif
                    @endif

                </ul>
            </div>
        </div>

        <div class="sidebar-background"></div>
    </div>
    <!-- Left Sidebar End -->
    <div class="vertical-overlay"></div>
