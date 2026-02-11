<script src="<?php echo e(URL::asset('build/libs/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('build/libs/simplebar/simplebar.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('build/libs/node-waves/waves.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('build/libs/feather-icons/feather.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('build/js/pages/plugins/lord-icon-2.1.0.js')); ?>"></script>
<script src="<?php echo e(URL::asset('build/js/plugins.js')); ?>"></script>


<script src="<?php echo e(URL::asset('build/libs/flatpickr/flatpickr.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('build/libs/flatpickr/l10n/es.js')); ?>"></script>
<script>
    // Configuración global de Flatpickr
    flatpickr.localize(flatpickr.l10ns.es);

    // Función de utilidad para inicializar rangos de fecha de forma consistente
    function initDateRangePicker(selectorInicio, selectorFin, fechaInicio, fechaFin) {
        if (!document.querySelector(selectorInicio)) return;

        flatpickr(selectorInicio, {
            dateFormat: "Y-m-d",
            defaultDate: fechaInicio,
            locale: "es",
            onChange: function(selectedDates, dateStr) {
                if (selectorFin) {
                    const minDate = selectedDates[0];
                    const fpFin = document.querySelector(selectorFin)._flatpickr;
                    if (fpFin) fpFin.set('minDate', minDate);
                }
            }
        });

        if (selectorFin && document.querySelector(selectorFin)) {
            flatpickr(selectorFin, {
                dateFormat: "Y-m-d",
                defaultDate: fechaFin,
                locale: "es"
            });
        }
    }
</script>


<script src="<?php echo e(URL::asset('build/libs/toastify-js/src/toastify.js')); ?>"></script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toast de éxito
        <?php if(session('success')): ?>
            Toastify({
                text: "<?php echo e(session('success')); ?>",
                duration: 3000,
                gravity: "top",
                position: "center",
                stopOnFocus: true,
                close: true,
                className: "bg-success",
                style: {
                    background: "linear-gradient(to right, #0ab39c, #0ab39c)"
                }
            }).showToast();
        <?php endif; ?>

        // Toast de error
        <?php if(session('error')): ?>
            Toastify({
                text: "<?php echo e(session('error')); ?>",
                duration: 4000,
                gravity: "top",
                position: "center",
                stopOnFocus: true,
                close: true,
                className: "bg-danger",
                style: {
                    background: "linear-gradient(to right, #f06548, #f06548)"
                }
            }).showToast();
        <?php endif; ?>

        // Toast de advertencia
        <?php if(session('warning')): ?>
            Toastify({
                text: "<?php echo e(session('warning')); ?>",
                duration: 4000,
                gravity: "top",
                position: "center",
                stopOnFocus: true,
                close: true,
                className: "bg-warning",
                style: {
                    background: "linear-gradient(to right, #f7b84b, #f7b84b)"
                }
            }).showToast();
        <?php endif; ?>

        // Toast para errores de validación
        <?php if($errors->any()): ?>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                Toastify({
                    text: "<?php echo e($error); ?>",
                    duration: 5000,
                    gravity: "top",
                    position: "center",
                    stopOnFocus: true,
                    close: true,
                    className: "bg-danger",
                    style: {
                        background: "linear-gradient(to right, #f06548, #f06548)"
                    }
                }).showToast();
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>

        /**
         * Observador para persistir el tema cuando el usuario lo cambia con el botón
         */
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' && (mutation.attributeName ===
                        'data-bs-theme' || mutation.attributeName === 'data-layout-mode')) {
                    const newTheme = document.documentElement.getAttribute('data-bs-theme');
                    if (newTheme) {
                        localStorage.setItem('data-bs-theme', newTheme);
                        // También actualizamos sessionStorage para coherencia con Velzon
                        sessionStorage.setItem('data-bs-theme', newTheme);
                        sessionStorage.setItem('data-layout-mode', newTheme);
                    }
                }
            });
        });
        observer.observe(document.documentElement, {
            attributes: true
        });
    });
</script>


<script src="<?php echo e(URL::asset('build/js/app.js')); ?>"></script>

<?php echo $__env->yieldContent('script'); ?>
<?php echo $__env->yieldContent('script-bottom'); ?>
<?php /**PATH C:\xampp\htdocs\master\resources\views/layouts/vendor-scripts.blade.php ENDPATH**/ ?>