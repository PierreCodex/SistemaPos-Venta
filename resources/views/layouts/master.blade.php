<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light" data-sidebar="dark"
    data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="material"
    data-theme-colors="default" data-layout-style="default" data-layout-width="fluid" data-layout-position="fixed"
    data-sidebar-view="default">

<head>
    <meta charset="utf-8" />
    <title>@yield('title') | Velzon - Admin & Dashboard Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('build/images/favicon.ico') }}">

    <script>
        /**
         * Sistema de Persistencia de Tema (Garantiza que el modo claro/oscuro no se pierda)
         */
        (function() {
            try {
                // Buscamos el tema en localStorage (permanente) o sessionStorage (Velzon default)
                let savedTheme = localStorage.getItem('data-bs-theme');

                if (!savedTheme) {
                    // Si no está en localStorage, intentamos recuperar de la configuración de Velzon
                    const config = JSON.parse(sessionStorage.getItem('defaultAttribute'));
                    if (config && config['data-bs-theme']) {
                        savedTheme = config['data-bs-theme'];
                    }
                }

                // Si no hay nada guardado, usamos 'dark' por defecto (preferencia del sistema)
                const theme = savedTheme || 'light';

                // Aplicamos al documento antes de que renderice para evitar el parpadeo blanco
                document.documentElement.setAttribute('data-bs-theme', theme);
                document.documentElement.setAttribute('data-layout-mode', theme);

                // Sincronizamos con sessionStorage para que Velzon no lo sobrescriba
                sessionStorage.setItem('data-bs-theme', theme);
                sessionStorage.setItem('data-layout-mode', theme);
            } catch (e) {
                console.error("Error al restaurar el tema:", e);
            }
        })();
    </script>


    @include('layouts.head-css')
</head>

<body>
    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('layouts.topbar')
        @include('layouts.sidebar')
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            @include('layouts.footer')
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    @include('layouts.customizer')

    @include('layouts.supervisor-modal')

    <!-- JAVASCRIPT -->
    @include('layouts.vendor-scripts')
</body>

</html>
