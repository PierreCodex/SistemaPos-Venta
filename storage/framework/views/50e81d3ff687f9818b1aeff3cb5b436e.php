<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" data-layout="vertical" data-topbar="light" data-sidebar="dark"
    data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="material"
    data-theme-colors="default" data-layout-style="default" data-layout-mode="dark" data-layout-width="fluid"
    data-layout-position="fixed" data-sidebar-view="default">

<head>
    <meta charset="utf-8" />
    <title><?php echo $__env->yieldContent('title'); ?> | Velzon - Admin & Dashboard Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo e(URL::asset('build/images/favicon.ico')); ?>">

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
                    document.documentElement.setAttribute('data-bs-theme', 'dark');
                    document.documentElement.setAttribute('data-layout-mode', 'dark');
                }
            } catch (e) {
                console.error("Error al restaurar el tema:", e);
            }
        })();
    </script>

    <?php echo $__env->make('layouts.head-css', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>

<body>
    <div id="layout-wrapper">
        <div class="main-content" style="margin-left: 0; padding-top: 0;">
            <div class="page-content" style="padding: 0;">
                <div class="container-fluid">
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </div>
        </div>
    </div>

    <?php echo $__env->make('layouts.vendor-scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>

</html>
<?php /**PATH C:\xampp\htdocs\master\resources\views/layouts/master-without-nav.blade.php ENDPATH**/ ?>