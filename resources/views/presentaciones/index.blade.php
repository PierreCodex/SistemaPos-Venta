@extends('layouts.master')

@section('title')
    Presentaciones · {{ $producto->nombre }}
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Presentaciones del producto</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
                        <li class="breadcrumb-item active">Presentaciones</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            {{-- Cabecera del producto --}}
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <div class="flex-grow-1">
                            <h5 class="mb-1 text-uppercase fw-bold">{{ $producto->nombre }}</h5>
                            <p class="text-muted mb-0">
                                Código: <span class="fw-medium">{{ $producto->codigo }}</span>
                                &nbsp;·&nbsp; Unidad base:
                                <span class="badge bg-primary-subtle text-primary">{{ $producto->unidad->codigo ?? '—' }}</span>
                            </p>
                        </div>
                        <div class="text-end">
                            <span class="text-muted d-block fs-12 text-uppercase">Stock actual (en unidad base)</span>
                            <h4 class="mb-0">{{ rtrim(rtrim(number_format($producto->stock, 3), '0'), '.') }}
                                <small class="text-muted fs-14">{{ $producto->unidad->codigo ?? '' }}</small>
                            </h4>
                        </div>
                        <div>
                            <a href="{{ route('productos.index') }}" class="btn btn-light">
                                <i class="ri-arrow-left-line me-1"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Explicación --}}
            <div class="alert alert-info d-flex align-items-start" role="alert">
                <i class="ri-information-line fs-18 me-2"></i>
                <div>
                    Una presentación es una forma de vender este producto (unidad suelta, paquete, caja).
                    El <strong>factor</strong> indica cuántas unidades base contiene: si la base es
                    <strong>{{ $producto->unidad->codigo ?? 'UND' }}</strong> y vende una caja con factor 24,
                    cada caja descuenta 24 del stock. El inventario es único y siempre se mide en la unidad base.
                </div>
            </div>

            {{-- Listado --}}
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1 text-uppercase fw-bold">Presentaciones</h5>
                    <button type="button" class="btn btn-primary d-flex align-items-center shadow-sm"
                        onclick="abrirModalNueva()">
                        <i class="ri-add-line fs-18 me-1"></i>
                        <span class="text-uppercase">Nueva presentación</span>
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" style="width:100%">
                            <thead class="table-light text-muted">
                                <tr class="text-uppercase fs-12">
                                    <th>Unidad</th>
                                    <th class="text-end">Factor</th>
                                    <th>Equivale a</th>
                                    <th class="text-end">Precio venta</th>
                                    <th>Cód. barras</th>
                                    <th style="width: 90px;">Tipo</th>
                                    <th style="width: 90px;">Estado</th>
                                    <th class="text-end" style="width: 130px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaPresentaciones">
                                {{-- La rellena el JS a partir de PRESENTACIONES --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Crear/Editar --}}
    <div class="modal fade" id="modalPresentacion" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitulo">Nueva presentación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="presentacionId">

                    {{-- Aviso cuando la presentación ya tiene ventas: el factor se bloquea --}}
                    <div class="alert alert-warning py-2 d-none" id="avisoBloqueado">
                        <i class="ri-lock-line me-1"></i>
                        Esta presentación ya tiene ventas o compras. No se puede cambiar su unidad ni su factor;
                        para corregirlos, desactívela y cree una nueva.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Unidad <span class="text-danger">*</span></label>
                        <select class="form-select" id="campoUnidad">
                            <option value="">Seleccione una unidad…</option>
                            @foreach ($unidades as $u)
                                <option value="{{ $u->id }}" data-decimales="{{ $u->permite_decimales ? 1 : 0 }}">
                                    {{ $u->codigo }} — {{ $u->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Factor de conversión <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.0001" min="0.0001" class="form-control" id="campoFactor"
                                placeholder="Ej: 24">
                            <span class="input-group-text" id="sufijoFactor">{{ $producto->unidad->codigo ?? 'UND' }}</span>
                        </div>
                        <small class="text-muted">Cuántas unidades base contiene esta presentación.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Precio de venta <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">S/</span>
                            <input type="number" step="0.01" min="0" class="form-control" id="campoPrecio"
                                placeholder="0.00">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Código de barras <span class="text-muted fs-12">(opcional)</span></label>
                        <input type="text" class="form-control" id="campoCodigoBarras"
                            placeholder="El de la caja, si trae uno propio">
                        <small class="text-muted">Al escanearlo, el POS vende esta presentación directamente.</small>
                    </div>

                    <div class="form-check form-switch" id="contenedorEstado">
                        <input class="form-check-input" type="checkbox" id="campoEstado" checked>
                        <label class="form-check-label" for="campoEstado">Activa (disponible para vender)</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardar" onclick="guardarPresentacion()">
                        <i class="ri-save-line me-1"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Eliminar --}}
    <div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Eliminar presentación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="ri-error-warning-line text-danger" style="font-size: 4rem;"></i>
                    <p class="mt-3 mb-0">¿Eliminar la presentación <strong id="nombreEliminar"></strong>?</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmarEliminar"
                        onclick="confirmarEliminar()">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // =====================================================
        // Configuración desde el servidor
        // =====================================================
        const PRES_CFG = {
            productoId: {{ $producto->id }},
            unidadBase: @json($producto->unidad->codigo ?? 'UND'),
            csrf: "{{ csrf_token() }}",
            rutas: {
                store: "{{ route('productos.presentaciones.store', $producto->id) }}",
                // Se completan por presentación con reemplazo de :id
                update: "{{ route('productos.presentaciones.update', ['producto' => $producto->id, 'presentacion' => ':id']) }}",
                toggle: "{{ route('productos.presentaciones.toggle-estado', ['producto' => $producto->id, 'presentacion' => ':id']) }}",
                destroy: "{{ route('productos.presentaciones.destroy', ['producto' => $producto->id, 'presentacion' => ':id']) }}",
            },
        };

        let PRESENTACIONES = @json($presentacionesJson);

        let eliminarId = null;

        // =====================================================
        // Notificaciones (Toastify ya está cargado globalmente)
        // =====================================================
        function toast(mensaje, tipo = 'success') {
            const colores = {
                success: '#0ab39c',
                error: '#f06548',
                warning: '#f7b84b'
            };
            Toastify({
                text: mensaje,
                duration: tipo === 'success' ? 3000 : 4500,
                gravity: 'top',
                position: 'center',
                close: true,
                stopOnFocus: true,
                style: {
                    background: colores[tipo] || colores.success
                },
            }).showToast();
        }

        function fmt(n) {
            return Number.isInteger(n) ? n.toString() : parseFloat(n.toFixed(4)).toString();
        }

        // =====================================================
        // Render de la tabla
        // =====================================================
        function render() {
            const tbody = document.getElementById('tablaPresentaciones');

            if (PRESENTACIONES.length === 0) {
                tbody.innerHTML =
                    `<tr><td colspan="8" class="text-center text-muted py-4">
                        <i class="ri-inbox-line fs-1 d-block mb-2"></i>Sin presentaciones
                    </td></tr>`;
                return;
            }

            tbody.innerHTML = PRESENTACIONES.map(p => {
                const equivale = p.factor === 1 ?
                    `<span class="text-muted">unidad base</span>` :
                    `${fmt(p.factor)} ${PRES_CFG.unidadBase}`;

                const tipo = p.es_base ?
                    `<span class="badge bg-primary">Base</span>` :
                    `<span class="badge bg-secondary-subtle text-secondary">Extra</span>`;

                const estado = p.estado ?
                    `<span class="badge bg-success">Activa</span>` :
                    `<span class="badge bg-danger">Inactiva</span>`;

                // La base no se puede desactivar ni eliminar
                const btnToggle = p.es_base ? '' : `
                    <button class="btn btn-sm ${p.estado ? 'btn-soft-secondary' : 'btn-soft-success'}"
                        onclick="toggleEstado(${p.id})" title="${p.estado ? 'Desactivar' : 'Activar'}">
                        <i class="ri-${p.estado ? 'pause' : 'play'}-line"></i>
                    </button>`;

                const btnEliminar = (p.es_base || p.tiene_movimientos) ? '' : `
                    <button class="btn btn-sm btn-danger" onclick="pedirEliminar(${p.id})" title="Eliminar">
                        <i class="ri-delete-bin-line"></i>
                    </button>`;

                return `
                    <tr>
                        <td>
                            <span class="fw-bold">${p.unidad_codigo}</span>
                            <small class="text-muted d-block">${p.unidad_nombre}</small>
                        </td>
                        <td class="text-end">${fmt(p.factor)}</td>
                        <td>${equivale}</td>
                        <td class="text-end fw-medium">S/ ${p.precio_venta.toFixed(2)}</td>
                        <td>${p.codigo_barras ? `<code>${p.codigo_barras}</code>` : '<span class="text-muted">—</span>'}</td>
                        <td>${tipo}</td>
                        <td>${estado}</td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <button class="btn btn-sm btn-warning" onclick="abrirModalEditar(${p.id})" title="Editar">
                                    <i class="ri-pencil-line"></i>
                                </button>
                                ${btnToggle}
                                ${btnEliminar}
                            </div>
                        </td>
                    </tr>`;
            }).join('');
        }

        // =====================================================
        // Modal crear / editar
        // =====================================================
        function abrirModalNueva() {
            document.getElementById('modalTitulo').textContent = 'Nueva presentación';
            document.getElementById('presentacionId').value = '';
            document.getElementById('campoUnidad').value = '';
            document.getElementById('campoUnidad').disabled = false;
            document.getElementById('campoFactor').value = '';
            document.getElementById('campoFactor').disabled = false;
            document.getElementById('campoPrecio').value = '';
            document.getElementById('campoCodigoBarras').value = '';
            document.getElementById('campoEstado').checked = true;
            document.getElementById('contenedorEstado').classList.remove('d-none');
            document.getElementById('avisoBloqueado').classList.add('d-none');
            new bootstrap.Modal(document.getElementById('modalPresentacion')).show();
        }

        function abrirModalEditar(id) {
            const p = PRESENTACIONES.find(x => x.id === id);
            if (!p) return;

            document.getElementById('modalTitulo').textContent =
                p.es_base ? 'Editar presentación base' : 'Editar presentación';
            document.getElementById('presentacionId').value = p.id;
            document.getElementById('campoUnidad').value = p.unidad_id;
            document.getElementById('campoFactor').value = fmt(p.factor);
            document.getElementById('campoPrecio').value = p.precio_venta.toFixed(2);
            document.getElementById('campoCodigoBarras').value = p.codigo_barras || '';
            document.getElementById('campoEstado').checked = p.estado;

            // La base define la unidad del stock: no se le cambian unidad ni factor.
            // Una presentación ya usada tampoco (reescribiría el histórico).
            const bloquear = p.es_base || p.tiene_movimientos;
            document.getElementById('campoUnidad').disabled = bloquear;
            document.getElementById('campoFactor').disabled = bloquear;

            // La base no se puede desactivar
            document.getElementById('contenedorEstado').classList.toggle('d-none', p.es_base);

            // Aviso solo cuando el bloqueo es por movimientos (no por ser base)
            document.getElementById('avisoBloqueado').classList.toggle('d-none', !(p.tiene_movimientos && !p.es_base));

            new bootstrap.Modal(document.getElementById('modalPresentacion')).show();
        }

        async function guardarPresentacion() {
            const id = document.getElementById('presentacionId').value;
            const esNueva = !id;

            const payload = {
                unidad_id: document.getElementById('campoUnidad').value,
                factor: document.getElementById('campoFactor').value,
                precio_venta: document.getElementById('campoPrecio').value,
                codigo_barras: document.getElementById('campoCodigoBarras').value.trim() || null,
                estado: document.getElementById('campoEstado').checked ? 1 : 0,
            };

            const url = esNueva ? PRES_CFG.rutas.store : PRES_CFG.rutas.update.replace(':id', id);
            const metodo = esNueva ? 'POST' : 'PUT';

            const btn = document.getElementById('btnGuardar');
            btn.disabled = true;

            try {
                const res = await fetch(url, {
                    method: metodo,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': PRES_CFG.csrf,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();

                if (!res.ok || !data.success) {
                    toast(data.message || 'No se pudo guardar.', 'error');
                    return;
                }

                // Actualizar el arreglo local
                const p = data.presentacion;
                p.tiene_movimientos = esNueva ? false :
                    (PRESENTACIONES.find(x => x.id === p.id)?.tiene_movimientos ?? false);

                if (esNueva) {
                    PRESENTACIONES.push(p);
                } else {
                    const i = PRESENTACIONES.findIndex(x => x.id === p.id);
                    if (i !== -1) PRESENTACIONES[i] = p;
                }

                render();
                bootstrap.Modal.getInstance(document.getElementById('modalPresentacion')).hide();
                toast(data.message);
            } catch (e) {
                toast('Error de red al guardar.', 'error');
            } finally {
                btn.disabled = false;
            }
        }

        // =====================================================
        // Activar / desactivar
        // =====================================================
        async function toggleEstado(id) {
            try {
                const res = await fetch(PRES_CFG.rutas.toggle.replace(':id', id), {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': PRES_CFG.csrf,
                        'Accept': 'application/json'
                    },
                });
                const data = await res.json();
                if (!res.ok || !data.success) {
                    toast(data.message || 'No se pudo cambiar el estado.', 'error');
                    return;
                }
                const p = PRESENTACIONES.find(x => x.id === id);
                if (p) p.estado = data.estado;
                render();
                toast(data.message);
            } catch (e) {
                toast('Error de red.', 'error');
            }
        }

        // =====================================================
        // Eliminar
        // =====================================================
        function pedirEliminar(id) {
            const p = PRESENTACIONES.find(x => x.id === id);
            if (!p) return;
            eliminarId = id;
            document.getElementById('nombreEliminar').textContent = `${p.unidad_codigo} (x${fmt(p.factor)})`;
            new bootstrap.Modal(document.getElementById('modalEliminar')).show();
        }

        async function confirmarEliminar() {
            if (!eliminarId) return;
            try {
                const res = await fetch(PRES_CFG.rutas.destroy.replace(':id', eliminarId), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': PRES_CFG.csrf,
                        'Accept': 'application/json'
                    },
                });
                const data = await res.json();
                if (!res.ok || !data.success) {
                    toast(data.message || 'No se pudo eliminar.', 'error');
                    return;
                }
                PRESENTACIONES = PRESENTACIONES.filter(x => x.id !== eliminarId);
                render();
                bootstrap.Modal.getInstance(document.getElementById('modalEliminar')).hide();
                toast(data.message);
            } catch (e) {
                toast('Error de red al eliminar.', 'error');
            } finally {
                eliminarId = null;
            }
        }

        // Sincroniza el sufijo de factor con la unidad base (informativo)
        document.addEventListener('DOMContentLoaded', render);
    </script>
@endsection
