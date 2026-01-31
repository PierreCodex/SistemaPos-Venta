/**
 * Módulo JavaScript para Proveedores
 * 
 * Maneja el CRUD completo con AJAX y DataTables
 */

// =============================================
// VARIABLES GLOBALES
// =============================================
let proveedores = [];
let ROUTES = {};
let CSRF_TOKEN = '';
let dataTable = null;
let editandoId = null;

// =============================================
// INICIALIZACIÓN
// =============================================
document.addEventListener('DOMContentLoaded', function () {
    if (window.PROVEEDORES_CONFIG) {
        proveedores = window.PROVEEDORES_CONFIG.proveedores || [];
        ROUTES = window.PROVEEDORES_CONFIG.routes || {};
        CSRF_TOKEN = window.PROVEEDORES_CONFIG.csrfToken || '';
    }

    initDataTable();
    initFormEvents();
});

// =============================================
// DATATABLES
// =============================================
function initDataTable() {
    dataTable = $('#tablaProveedores').DataTable({
        autoWidth: false,
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                className: 'buttons-excel d-none', // Oculto, se activa desde botón personalizado
                title: 'Reporte de Proveedores',
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
                className: 'buttons-pdf d-none', // Oculto
                title: 'Reporte de Proveedores',
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
                    columns: ':not(:last-child)'
                }
            }
        ],
        columnDefs: [{
            orderable: false,
            targets: [-1, -2] // Acciones y On/Off
        }]
    });

    // Conectar botones personalizados del header
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
    const formProveedor = document.getElementById('formProveedor');
    if (formProveedor) {
        formProveedor.addEventListener('submit', handleFormSubmit);
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
                bootstrap.Modal.getInstance(document.getElementById('modalProveedor')).hide();

                if (isEditing) {
                    actualizarFilaEnTabla(data.proveedor);
                    actualizarArrayLocal(data.proveedor);
                } else {
                    agregarFilaATabla(data.proveedor);
                    proveedores.push(data.proveedor);
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
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalEliminar')).hide();
                eliminarFilaDeTabla(data.id);
                proveedores = proveedores.filter(p => p.id !== data.id);
                mostrarToast(data.message, 'success');
            }
        })
        .catch(error => {
            mostrarToast("Error al eliminar", 'error');
        });
}

// =============================================
// FUNCIONES CRUD
// =============================================
function limpiarFormulario() {
    document.getElementById('modalTitle').textContent = 'Nuevo Proveedor';
    document.getElementById('formProveedor').reset();
    document.getElementById('formProveedor').action = ROUTES.store;
    document.getElementById('formMethod').value = 'POST';
    editandoId = null;

    document.getElementById('btnGuardar').disabled = false;
    document.getElementById('btnGuardarTexto').classList.remove('d-none');
    document.getElementById('btnGuardarSpinner').classList.add('d-none');

    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}

function editarProveedor(id) {
    const prov = proveedores.find(p => p.id === id);
    if (!prov) return;

    editandoId = id;
    document.getElementById('modalTitle').textContent = 'Editar Proveedor';
    document.getElementById('formProveedor').action = `/proveedores/${id}`;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('tipo_documento').value = prov.tipo_documento;
    document.getElementById('documento').value = prov.documento;
    document.getElementById('nombre').value = prov.nombre;
    document.getElementById('telefono').value = prov.telefono || '';
    document.getElementById('email').value = prov.email || '';
    document.getElementById('direccion').value = prov.direccion || '';

    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    new bootstrap.Modal(document.getElementById('modalProveedor')).show();
}

function verProveedor(id) {
    const prov = proveedores.find(p => p.id === id);
    if (!prov) return;

    document.getElementById('verID').textContent = prov.id;
    document.getElementById('verDocumento').innerHTML = `<span class="badge bg-secondary">${prov.tipo_documento}</span> ${prov.documento}`;
    document.getElementById('verNombre').textContent = prov.nombre;
    document.getElementById('verTelefono').textContent = prov.telefono || '-';
    document.getElementById('verEmail').textContent = prov.email || '-';
    document.getElementById('verDireccion').textContent = prov.direccion || '-';
    document.getElementById('verEstado').innerHTML = prov.estado
        ? '<span class="badge bg-success">Activo</span>'
        : '<span class="badge bg-danger">Inactivo</span>';

    new bootstrap.Modal(document.getElementById('modalVer')).show();
}

function eliminarProveedor(id, nombre) {
    document.getElementById('nombreEliminar').textContent = nombre;
    document.getElementById('formEliminar').action = `/proveedores/${id}`;
    new bootstrap.Modal(document.getElementById('modalEliminar')).show();
}

function toggleEstado(id) {
    fetch(`/proveedores/${id}/toggle-estado`, {
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
                const prov = proveedores.find(p => p.id === id);
                if (prov) prov.estado = data.estado ? 1 : 0;

                const badgeCell = document.getElementById(`estado-badge-${id}`);
                badgeCell.innerHTML = data.estado
                    ? '<span class="badge bg-success">Activo</span>'
                    : '<span class="badge bg-danger">Inactivo</span>';

                mostrarToast(data.message, data.estado ? 'success' : 'warning');
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
function agregarFilaATabla(prov) {
    const rowHtml = generarFilaHTML(prov);
    dataTable.row.add($(rowHtml)).draw(false);
}

function actualizarFilaEnTabla(prov) {
    const row = $(`tr[data-id="${prov.id}"]`);
    if (row.length) {
        const rowIndex = dataTable.row(row).index();
        dataTable.row(rowIndex).remove();
        const rowHtml = generarFilaHTML(prov);
        dataTable.row.add($(rowHtml)).draw(false);
    }
}

function eliminarFilaDeTabla(id) {
    const row = $(`tr[data-id="${id}"]`);
    if (row.length) {
        dataTable.row(row).remove().draw(false);
    }
}

function actualizarArrayLocal(prov) {
    const index = proveedores.findIndex(p => p.id === prov.id);
    if (index !== -1) {
        proveedores[index] = { ...proveedores[index], ...prov };
    }
}

function generarFilaHTML(prov) {
    return `
        <tr data-id="${prov.id}">
            <td>
                <span class="badge bg-secondary">${prov.tipo_documento}</span>
                <strong>${prov.documento}</strong>
            </td>
            <td><strong>${prov.nombre}</strong></td>
            <td>${prov.telefono || '-'}</td>
            <td>${prov.email || '-'}</td>
            <td id="estado-badge-${prov.id}">
                ${prov.estado ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'}
            </td>
            <td class="no-exportar">
                <div class="d-flex gap-1">
                    <button type="button" class="btn btn-sm btn-info" onclick="verProveedor(${prov.id})" title="Ver">
                        <i class="ri-eye-line"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-warning" onclick="editarProveedor(${prov.id})" title="Editar">
                        <i class="ri-pencil-line"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarProveedor(${prov.id}, '${prov.nombre}')" title="Eliminar">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            </td>
            <td class="no-exportar">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="toggle-estado-${prov.id}"
                        ${prov.estado ? 'checked' : ''} onchange="toggleEstado(${prov.id})">
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
