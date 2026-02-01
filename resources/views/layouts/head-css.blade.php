@yield('css')
<!-- Layout config Js -->
<script src="{{ URL::asset('build/js/layout.js') }}"></script>

@vite(['resources/scss/bootstrap.scss', 'resources/scss/icons.scss', 'resources/scss/app.scss', 'resources/scss/custom.scss'])

<!-- Toastify Css -->
<link href="{{ URL::asset('build/libs/toastify-js/src/toastify.css') }}" rel="stylesheet" type="text/css" />
