<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light" data-sidebar="dark"
    data-sidebar-size="sm" data-sidebar-image="none" data-preloader="disable" data-theme="material"
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
        // Restaura el tema guardado antes de que cargue el CSS para evitar el parpadeo blanco
        (function() {
            try {
                const config = JSON.parse(sessionStorage.getItem('defaultAttribute'));
                if (config) {
                    const theme = config['data-bs-theme'] || 'dark';
                    document.documentElement.setAttribute('data-bs-theme', theme);
                    document.documentElement.setAttribute('data-layout-mode', theme);
                } else {
                    // Si no hay config, usamos dark por defecto pero permitimos que el sistema lo cambie
                    document.documentElement.setAttribute('data-bs-theme', 'dark');
                    document.documentElement.setAttribute('data-layout-mode', 'dark');
                }
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

    <!-- JAVASCRIPT -->
    @include('layouts.vendor-scripts')
</body>

</html>
