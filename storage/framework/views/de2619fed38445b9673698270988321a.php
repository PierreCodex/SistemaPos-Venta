

<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.profile'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="position-relative mx-n4 mt-n4">
        <div class="profile-wid-bg profile-setting-img">
            <img src="<?php echo e(URL::asset('build/images/profile-bg.jpg')); ?>" class="profile-wid-img" alt="" />
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-3">
            <div class="card mt-n5 border-0 shadow-none">
                <div class="card-body p-4">
                    <div class="text-center">
                        <div class="profile-user position-relative d-inline-block mx-auto mb-4">
                            <?php if(Auth::user()->avatar && file_exists(public_path('images/' . Auth::user()->avatar))): ?>
                                <img src="<?php echo e(URL::asset('images/' . Auth::user()->avatar)); ?>"
                                    class="rounded-circle avatar-xl img-thumbnail user-profile-image material-shadow"
                                    alt="user-profile-image">
                            <?php else: ?>
                                <div class="avatar-title rounded-circle bg-light text-primary avatar-xl img-thumbnail material-shadow text-uppercase"
                                    style="font-size: 2.5rem;">
                                    <?php echo e(substr(Auth::user()->name, 0, 1)); ?>

                                </div>
                            <?php endif; ?>
                            <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                                <input id="profile-img-file-input" type="file" name="avatar"
                                    class="profile-img-file-input" form="profile-form" accept="image/*">
                                <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                                    <span class="avatar-title rounded-circle bg-light text-body material-shadow">
                                        <i class="ri-camera-fill"></i>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <h5 class="fs-16 mb-1 text-uppercase fw-bold"><?php echo e(Auth::user()->name); ?></h5>
                        <p class="text-muted mb-0 uppercase fs-12">
                            <?php echo e(Auth::user()->roles->pluck('name')->first() ?? 'Usuario'); ?></p>
                    </div>
                </div>
            </div>
            <!--end card-->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0">Estado de la Cuenta</h5>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <h6 class="fs-14 mb-1">Cuenta Activa</h6>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    <?php echo e(Auth::user()->activo ? 'checked' : ''); ?> disabled>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="fs-14 mb-1">ID de Usuario</h6>
                        </div>
                        <div class="flex-shrink-0">
                            <span
                                class="badge bg-primary-subtle text-primary">#<?php echo e(str_pad(Auth::user()->id, 6, '0', STR_PAD_LEFT)); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
        <div class="col-xxl-9">
            <div class="card mt-xxl-n5">
                <div class="card-header">
                    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#personalDetails" role="tab">
                                <i class="fas fa-home"></i> Detalles Personales
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content">
                        <div class="tab-pane active" id="personalDetails" role="tabpanel">
                            <form action="<?php echo e(route('updateProfile', Auth::user()->id)); ?>" method="POST"
                                enctype="multipart/form-data" id="profile-form">
                                <?php echo csrf_field(); ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label for="firstnameInput"
                                                class="form-label text-uppercase fs-12 fw-bold">Nombre Completo</label>
                                            <input type="text" class="form-control" name="name" id="firstnameInput"
                                                placeholder="Ingrese su nombre" value="<?php echo e(Auth::user()->name); ?>" required>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="phonenumberInput"
                                                class="form-label text-uppercase fs-12 fw-bold">Teléfono / WhatsApp</label>
                                            <input type="text" class="form-control" name="telefono" id="phonenumberInput"
                                                placeholder="Ingrese su teléfono" value="<?php echo e(Auth::user()->telefono); ?>">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="emailInput" class="form-label text-uppercase fs-12 fw-bold">Correo
                                                Electrónico</label>
                                            <input type="email" class="form-control" name="email" id="emailInput"
                                                placeholder="Ingrese su email" value="<?php echo e(Auth::user()->email); ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 mt-3">
                                        <div class="alert alert-info border-0 mb-4">
                                            <p class="mb-0 fs-12 text-uppercase fw-bold"><i
                                                    class="ri-lock-2-line me-1"></i> Cambiar Contraseña</p>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="passwordInput"
                                                class="form-label text-uppercase fs-12 fw-bold">Nueva Contraseña</label>
                                            <input type="password" class="form-control" name="password"
                                                id="passwordInput" placeholder="Dejar en blanco para no cambiar">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="confirmmerpasswordInput"
                                                class="form-label text-uppercase fs-12 fw-bold">Confirmar Nueva
                                                Contraseña</label>
                                            <input type="password" class="form-control" name="password_confirmation"
                                                id="confirmmerpasswordInput" placeholder="Repita su nueva contraseña">
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="hstack gap-2 justify-content-end mt-4">
                                            <button type="submit" class="btn btn-primary text-uppercase fw-bold"><i
                                                    class="ri-save-line me-1"></i> GUARDAR CAMBIOS</button>
                                        </div>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </form>
                        </div>
                        <!--end tab-pane-->
                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        // Previsualización de imagen de perfil estandard de Velzon
        document.querySelector("#profile-img-file-input").addEventListener("change", function() {
            var preview = document.querySelector(".user-profile-image");
            var file = document.querySelector(".profile-img-file-input").files[0];
            var reader = new FileReader();

            reader.addEventListener("load", function() {
                if (preview) {
                    preview.src = reader.result;
                } else {
                    // Si no había imagen (estaba la inicial), recargar para mostrar
                    location.reload();
                }
            }, false);

            if (file) {
                reader.readAsDataURL(file);
            }
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\master\resources\views/pages-profile.blade.php ENDPATH**/ ?>