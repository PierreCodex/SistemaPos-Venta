<script src="{{ URL::asset('build/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/feather-icons/feather.min.js') }}"></script>
<script src="{{ URL::asset('build/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
<script src="{{ URL::asset('build/js/plugins.js') }}"></script>

{{-- Flatpickr: Idioma Español global --}}
<script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/flatpickr/l10n/es.js') }}"></script>
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

{{-- Toastify JS para notificaciones --}}
<script src="{{ URL::asset('build/libs/toastify-js/src/toastify.js') }}"></script>

{{-- Script para mostrar notificaciones de sesión de Laravel --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toast de éxito
        @if (session('success'))
            Toastify({
                text: "{{ session('success') }}",
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
        @endif

        // Toast de error
        @if (session('error'))
            Toastify({
                text: "{{ session('error') }}",
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
        @endif

        // Toast de advertencia
        @if (session('warning'))
            Toastify({
                text: "{{ session('warning') }}",
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
        @endif

        // Toast para errores de validación
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                Toastify({
                    text: "{{ $error }}",
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
            @endforeach
        @endif

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

{{-- Script principal de Velzon (maneja sidebar, dark mode, etc.) --}}
<script src="{{ URL::asset('build/js/app.js') }}"></script>

@yield('script')
@yield('script-bottom')
