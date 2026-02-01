/**
 * Módulo JavaScript para Unidades de Medida
 * 
 * Maneja el CRUD completo con AJAX y DataTables
 * 
 * Requiere: window.UNIDADES_CONFIG = { unidades, routes, csrfToken }
 */

// =============================================
// VARIABLES GLOBALES
// =============================================
let unidades = [];
let ROUTES = {};
let CSRF_TOKEN = '';
let dataTable = null;
let editandoId = null;

// =============================================
// INICIALIZACIÓN
// =============================================
document.addEventListener('DOMContentLoaded', function () {
    if (window.UNIDADES_CONFIG) {
        unidades = window.UNIDADES_CONFIG.unidades || [];
        ROUTES = window.UNIDADES_CONFIG.routes || {};
        CSRF_TOKEN = window.UNIDADES_CONFIG.csrfToken || '';
    }

    initDataTable();
    initFormEvents();
});

// =============================================
// DATATABLES
// =============================================
function initDataTable() {
    dataTable = $('#tablaUnidades').DataTable({
        autoWidth: false,
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                className: 'buttons-excel d-none',
                title: 'Reporte de Unidades de Medida',
                exportOptions: {
                    columns: ':not(.no-exportar)',
                    format: {
                        body: function (data, row, column, node) {
                            if (!data) return '';
                            return typeof data === 'string'
                                ? data.replace(/<[^>]+>/g, '').trim()
                                : data;
                        }
                    }
                }
            },
            {
                extend: 'pdf',
                className: 'buttons-pdf d-none',
                title: 'Reporte de Unidades de Medida',
                exportOptions: {
                    columns: ':not(.no-exportar)',
                    format: {
                        body: function (data, row, column, node) {
                            if (!data) return '';
                            return typeof data === 'string'
                                ? data.replace(/<[^>]+>/g, '').trim()
                                : data;
                        }
                    }
                }
            },
            {
                extend: 'print',
                className: 'buttons-print d-none',
                text: 'Imprimir',
                exportOptions: {
                    columns: ':not(.no-exportar)'
                }
            }
        ],
        columnDefs: [{
            orderable: false,
            targets: [-1, -2]
        }]
    });

    // Conectar botones personalizados
    $('#btnExportarExcel').on('click', function () {
        dataTable.button('.buttons-excel').trigger();
    });

    $('#btnExportarPDF').on('click', function () {
        dataTable.button('.buttons-pdf').trigger();
    });
}

// =============================================
// EVENTOS DE FORMULARIOS
// =============================================
function initFormEvents() {
    const formUnidad = document.getElementById('formUnidad');
    if (formUnidad) {
        formUnidad.addEventListener('submit', handleFormSubmit);
    }

    const formEliminar = document.getElementById('formEliminar');
    if (formEliminar) {
        formEliminar.addEventListener('submit', handleDeleteSubmit);
    }
}

function handleFormSubmit(e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);
    const btn = document.getElementById('btnGuardar');
    const textoNormal = document.getElementById('btnGuardarTexto');
    const spinnerTexto = document.getElementById('btnGuardarSpinner');
    const isEditing = editandoId !== null;

    btn.disabled = true;
    textoNormal.classList.add('d-none');
    spinnerTexto.classList.remove('d-none');

    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(response => {
            if (response.ok) {
                return response.json();
            } else if (response.status === 422) {
                return response.json().then(data => {
                    throw { validationErrors: data.errors };
                });
            } else {
                throw new Error('Error del servidor');
            }
        })
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalUnidad')).hide();

                if (isEditing) {
                    actualizarFilaEnTabla(data.unidad);
                    actualizarArrayLocal(data.unidad);
                } else {
                    agregarFilaATabla(data.unidad);
                    unidades.push(data.unidad);
                }

                mostrarToast(data.message, 'success');
                limpiarFormulario();
            }
        })
        .catch(error => {
            btn.disabled = false;
            textoNormal.classList.remove('d-none');
            spinnerTexto.classList.add('d-none');

            if (error.validationErrors) {
                Object.keys(error.validationErrors).forEach(field => {
                    const input = document.getElementById(field);
                    if (input) input.classList.add('is-invalid');

                    error.validationErrors[field].forEach(message => {
                        mostrarToast(message, 'error');
                    });
                });
            } else {
                mostrarToast("Error al procesar la solicitud", 'error');
            }
        });
}

function handleDeleteSubmit(e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(response => {
            if (response.ok) return response.json();
            return response.json().then(data => { throw data; });
        })
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalEliminar')).hide();
                eliminarFilaDeTabla(data.id);
                unidades = unidades.filter(u => u.id !== data.id);
                mostrarToast(data.message, 'success');
            } else {
                mostrarToast(data.message || "Error al eliminar", 'error');
            }
        })
        .catch(error => {
            console.error(error);
            mostrarToast(error.message || "Error al eliminar", 'error');
        });
}

// =============================================
// FUNCIONES CRUD
// =============================================
function limpiarFormulario() {
    document.getElementById('modalTitle').textContent = 'Nueva Unidad';
    document.getElementById('formUnidad').reset();
    document.getElementById('formUnidad').action = ROUTES.store;
    document.getElementById('formMethod').value = 'POST';
    editandoId = null;

    document.getElementById('btnGuardar').disabled = false;
    document.getElementById('btnGuardarTexto').classList.remove('d-none');
    document.getElementById('btnGuardarSpinner').classList.add('d-none');

    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}

function editarUnidad(id) {
    const unidad = unidades.find(u => u.id === id);
    if (!unidad) return;

    editandoId = id;
    document.getElementById('modalTitle').textContent = 'Editar Unidad';
    document.getElementById('formUnidad').action = `${ROUTES.store}/${id}`;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('codigo').value = unidad.codigo;
    document.getElementById('nombre').value = unidad.nombre;
    document.getElementById('descripcion').value = unidad.descripcion || '';

    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    new bootstrap.Modal(document.getElementById('modalUnidad')).show();
}

function verUnidad(id) {
    const unidad = unidades.find(u => u.id === id);
    if (!unidad) return;

    document.getElementById('verID').textContent = unidad.id;
    document.getElementById('verCodigo').textContent = unidad.codigo;
    document.getElementById('verNombre').textContent = unidad.nombre;
    document.getElementById('verDescripcion').textContent = unidad.descripcion || 'Sin descripción';
    document.getElementById('verEstado').innerHTML = unidad.estado ?
        '<span class="badge bg-success">Activo</span>' :
        '<span class="badge bg-danger">Inactivo</span>';

    new bootstrap.Modal(document.getElementById('modalVer')).show();
}

function eliminarUnidad(id, nombre) {
    document.getElementById('nombreEliminar').textContent = nombre;
    document.getElementById('formEliminar').action = `${ROUTES.store}/${id}`;
    new bootstrap.Modal(document.getElementById('modalEliminar')).show();
}

function toggleEstado(id) {
    fetch(`${ROUTES.store}/${id}/toggle-estado`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const unidad = unidades.find(u => u.id === id);
                if (unidad) unidad.estado = data.unidad.estado ? 1 : 0;

                const badgeCell = document.getElementById(`estado-badge-${id}`);
                badgeCell.innerHTML = data.unidad.estado ?
                    '<span class="badge bg-success">Activo</span>' :
                    '<span class="badge bg-danger">Inactivo</span>';

                mostrarToast(data.message, data.unidad.estado ? 'success' : 'warning');
            }
        })
        .catch(error => {
            const checkbox = document.getElementById(`toggle-estado-${id}`);
            checkbox.checked = !checkbox.checked;
            mostrarToast("Error al cambiar el estado", 'error');
        });
}

// =============================================
// FUNCIONES DATATABLES
// =============================================
function agregarFilaATabla(unidad) {
    const rowHtml = generarFilaHTML(unidad);
    dataTable.row.add($(rowHtml)).draw(false);
}

function actualizarFilaEnTabla(unidad) {
    const row = $(`tr[data-id="${unidad.id}"]`);
    if (row.length) {
        const rowIndex = dataTable.row(row).index();
        dataTable.row(rowIndex).remove();
        const rowHtml = generarFilaHTML(unidad);
        dataTable.row.add($(rowHtml)).draw(false);
    }
}

function eliminarFilaDeTabla(id) {
    const row = $(`tr[data-id="${id}"]`);
    if (row.length) {
        dataTable.row(row).remove().draw(false);
    }
}

function actualizarArrayLocal(unidad) {
    const index = unidades.findIndex(u => u.id === unidad.id);
    if (index !== -1) {
        unidades[index] = { ...unidades[index], ...unidad };
    }
}

function generarFilaHTML(unidad) {
    return `
        <tr data-id="${unidad.id}">
            <td><span class="badge bg-secondary fs-6">${unidad.codigo}</span></td>
            <td><strong>${unidad.nombre}</strong></td>
            <td>${unidad.descripcion ? '<i class="bx bx-comment-dots me-1"></i>' + unidad.descripcion.substring(0, 40) : '-'}</td>
            <td id="estado-badge-${unidad.id}">
                ${unidad.estado ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'}
            </td>
            <td class="no-exportar">
                <div class="d-flex gap-1 text-center justify-content-center">
                    <button type="button" class="btn btn-sm btn-info" onclick="verUnidad(${unidad.id})" title="Ver">
                        <i class="ri-eye-line"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-warning" onclick="editarUnidad(${unidad.id})" title="Editar">
                        <i class="ri-pencil-line"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarUnidad(${unidad.id}, '${unidad.nombre}')" title="Eliminar">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            </td>
            <td class="no-exportar">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="toggle-estado-${unidad.id}"
                        ${unidad.estado ? 'checked' : ''} onchange="toggleEstado(${unidad.id})">
                </div>
            </td>

        </tr>
    `;
}

// =============================================
// UTILIDADES
// =============================================
function mostrarToast(message, type) {
    const colors = {
        success: "linear-gradient(to right, #0ab39c, #0ab39c)",
        error: "linear-gradient(to right, #f06548, #f06548)",
        warning: "linear-gradient(to right, #f7b84b, #f7b84b)"
    };

    Toastify({
        text: message,
        duration: 3000,
        gravity: "top",
        position: "center",
        close: true,
        style: { background: colors[type] || colors.success }
    }).showToast();
}

/**
 * Genera un código aleatorio para unidades
 * Formato: 3 letras mayúsculas
 */
function generarCodigo() {
    const caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    let codigo = '';

    for (let i = 0; i < 3; i++) {
        codigo += caracteres.charAt(Math.floor(Math.random() * caracteres.length));
    }

    document.getElementById('codigo').value = codigo;
}
