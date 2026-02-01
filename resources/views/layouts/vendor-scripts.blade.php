<script src="{{ URL::asset('build/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/feather-icons/feather.min.js') }}"></script>
<script src="{{ URL::asset('build/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
<script src="{{ URL::asset('build/js/plugins.js') }}"></script>

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
    });
</script>

{{-- Script principal de Velzon (maneja sidebar, dark mode, etc.) --}}
@vite('resources/js/app.js')

@yield('script')
@yield('script-bottom')
