

<?php $__env->startSection('title'); ?>
    Gestor de Keys API
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Gestión de Tokens de API</h4>
                </div>
            </div>
        </div>

        <!-- Alerta de Nuevo Token (Solo se muestra una vez) -->
        <?php if(session('new_token')): ?>
            <div class="row">
                <div class="col-12">
                    <div class="card border-success shadow">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0 text-white"><i class="ri-broadcast-line align-middle"></i> ¡Token
                                Generado Exitosamente!</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-danger font-weight-bold">⚠️ IMPORTANTE: Copia este token ahora. No podrás volver
                                a verlo por razones de seguridad.</p>
                            <div class="input-group">
                                <input type="text" id="plainToken" class="form-control form-control-lg bg-light"
                                    value="<?php echo e(session('new_token')); ?>" readonly>
                                <button class="btn btn-primary" onclick="copyToken()">Copiar Token</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Formulario Crear Token -->
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Generar Nueva Key</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo e(route('api-tokens.store')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <div class="mb-3">
                                <label class="form-label">Nombre de la Integración (ej: n8n, App Móvil)</label>
                                <input type="text" name="token_name" class="form-control"
                                    placeholder="Nombre descriptivo" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Crear Token</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Lista de Tokens -->
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Tus Tokens Activos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Último Uso</th>
                                        <th>Creado</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $tokens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $token): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td class="fw-medium"><?php echo e($token->name); ?></td>
                                            <td><?php echo e($token->last_used_at ? $token->last_used_at->diffForHumans() : 'Nunca usado'); ?>

                                            </td>
                                            <td><?php echo e($token->created_at->format('d/m/Y')); ?></td>
                                            <td>
                                                <form action="<?php echo e(route('api-tokens.destroy', $token->id)); ?>" method="POST"
                                                    onsubmit="return confirm('¿Estás seguro de revocar este token?')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit"
                                                        class="btn btn-sm btn-soft-danger">Eliminar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No tienes tokens activos.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        function copyToken() {
            var copyText = document.getElementById("plainToken");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);

            // Notificación básica
            alert("¡Token copiado al portapapeles!");
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\master\resources\views/api/tokens.blade.php ENDPATH**/ ?>