@props(['fechaInicio', 'fechaFin', 'formId' => 'formFiltros'])

<form id="{{ $formId }}" action="javascript:void(0);">
    <div class="row g-4 align-items-end">
        <div class="col-sm-4">
            <label for="fecha_inicio" class="form-label fw-semibold text-muted text-uppercase fs-11">FECHA INICIO</label>
            <div class="input-group">
                <input type="text" id="fecha_inicio" class="form-control border-light bg-light">
                <span class="input-group-text border-light bg-light"><i class="ri-calendar-event-line"></i></span>
            </div>
        </div>
        <div class="col-sm-4">
            <label for="fecha_fin" class="form-label fw-semibold text-muted text-uppercase fs-11">FECHA FIN</label>
            <div class="input-group">
                <input type="text" id="fecha_fin" class="form-control border-light bg-light">
                <span class="input-group-text border-light bg-light"><i class="ri-calendar-event-line"></i></span>
            </div>
        </div>
        <div class="col-sm-4">
            <button type="submit" class="btn btn-primary w-100 py-2 fs-14 shadow-none text-uppercase fw-bold">
                <i class="ri-filter-3-line me-1 align-middle"></i> Filtrar
            </button>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Flatpickrs
        if (typeof initDateRangePicker === 'function') {
            initDateRangePicker("#fecha_inicio", "#fecha_fin", "{{ $fechaInicio }}", "{{ $fechaFin }}");
        }

        // Manejador automático de redirección para filtros estándar
        const filterForm = document.getElementById('{{ $formId }}');
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                const inicio = document.getElementById('fecha_inicio').value;
                const fin = document.getElementById('fecha_fin').value;

                // Redirigir a la misma URL (limpia parámetros actuales y pone los nuevos)
                const url = new URL(window.location.href);
                url.searchParams.set('fecha_inicio', inicio);
                url.searchParams.set('fecha_fin', fin);

                window.location.href = url.toString();
            });
        }
    });
</script>
