{{-- Modal Global de Autorización por PIN --}}
<div class="modal fade" id="modalSupervisorAuth" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-size-16">
                    <i class="ri-shield-keyhole-line me-2"></i> Autorización
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="avatar-md mx-auto mb-3">
                    <div class="avatar-title bg-danger-subtle text-danger rounded-circle fs-24">
                        <i class="ri-lock-password-line"></i>
                    </div>
                </div>
                <h5 class="mb-1">Acción Crítica</h5>
                <p class="text-muted mb-4 fs-13" id="supervisorActionDesc">Se requiere PIN de supervisor para continuar.
                </p>

                <div class="mb-3">
                    <input type="password" id="supervisorPinInput"
                        class="form-control form-control-lg text-center letter-spacing-5" placeholder="****"
                        maxlength="6" inputmode="numeric"
                        style="letter-spacing: 1rem; font-size: 1.5rem; font-weight: bold;">
                </div>

                <div id="supervisorAuthError" class="text-danger small mb-3 d-none">
                    PIN incorrecto o sin privilegios.
                </div>

                <div class="d-grid">
                    <button type="button" class="btn btn-danger btn-lg" id="btnConfirmSupervisorPin">
                        <span class="spinner-border spinner-border-sm d-none me-2" id="supervisorLoading"></span>
                        Autorizar
                    </button>
                </div>
            </div>
            <div class="modal-footer bg-light justify-content-center">
                <p class="mb-0 fs-11 text-muted text-uppercase fw-semibold">Sistema de Auditoría Activo</p>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * Helper Global para solicitar autorización de supervisor
     * @param {string} modulo El módulo (ej: 'ventas', 'productos')
     * @param {string} accion La acción (ej: 'eliminar', 'anular')
     * @param {string} descripcion Texto para el usuario
     * @returns {Promise} Resuelve si el PIN es correcto, rechaza si se cancela o falla
     */
    window.solicitarAutorizacion = function(modulo, accion, descripcion) {
        return new Promise((resolve, reject) => {
            const modalEl = document.getElementById('modalSupervisorAuth');
            const modal = new bootstrap.Modal(modalEl);
            const pinInput = document.getElementById('supervisorPinInput');
            const confirmBtn = document.getElementById('btnConfirmSupervisorPin');
            const errorDiv = document.getElementById('supervisorAuthError');
            const loading = document.getElementById('supervisorLoading');
            const descText = document.getElementById('supervisorActionDesc');

            // Reset
            pinInput.value = '';
            errorDiv.classList.add('d-none');
            descText.textContent = descripcion;
            confirmBtn.disabled = false;
            loading.classList.add('d-none');

            modal.show();

            // Enfocar PIN al abrir
            modalEl.addEventListener('shown.bs.modal', () => pinInput.focus(), {
                once: true
            });

            const handleConfirm = () => {
                const pin = pinInput.value;
                if (!pin) return;

                confirmBtn.disabled = true;
                loading.classList.remove('d-none');
                errorDiv.classList.add('d-none');

                fetch('{{ route('supervisor.verificar-pin') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            pin,
                            modulo,
                            accion,
                            descripcion
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            modal.hide();
                            resolve(data);
                        } else {
                            errorDiv.textContent = data.message;
                            errorDiv.classList.remove('d-none');
                            pinInput.value = '';
                            pinInput.focus();
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        errorDiv.textContent = 'Error de conexión';
                        errorDiv.classList.remove('d-none');
                    })
                    .finally(() => {
                        confirmBtn.disabled = false;
                        loading.classList.add('d-none');
                    });
            };

            confirmBtn.onclick = handleConfirm;
            pinInput.onkeypress = (e) => {
                if (e.key === 'Enter') handleConfirm();
            };

            modalEl.addEventListener('hidden.bs.modal', () => {
                confirmBtn.onclick = null;
                pinInput.onkeypress = null;
                // Si cerramos sin éxito, rechazamos
                // reject({ cancelled: true });
            }, {
                once: true
            });
        });
    };
</script>
