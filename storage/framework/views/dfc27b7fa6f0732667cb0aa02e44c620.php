<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" data-layout="vertical" data-topbar="light" data-sidebar="dark"
    data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="material"
    data-theme-colors="default" data-layout-style="default" data-layout-width="fluid" data-layout-position="fixed"
    data-sidebar-view="default" data-bs-theme="dark" data-layout-mode="dark">

<head>
    <meta charset="utf-8" />
    <title><?php echo $__env->yieldContent('title'); ?> | Velzon - Admin & Dashboard Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo e(URL::asset('build/images/favicon.ico')); ?>">

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
                const theme = savedTheme || 'dark';

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


    <?php echo $__env->make('layouts.head-css', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>

<body>
    <!-- Begin page -->
    <div id="layout-wrapper">
        <?php echo $__env->make('layouts.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('layouts.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            <?php echo $__env->make('layouts.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    

    <!-- JAVASCRIPT -->
    <?php echo $__env->make('layouts.vendor-scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>

</html>
<?php /**PATH C:\xampp\htdocs\master\resources\views/layouts/master.blade.php ENDPATH**/ ?>