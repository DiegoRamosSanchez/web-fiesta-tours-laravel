/**
 * CLIENTS CREATE - JAVASCRIPT
 * Módulo para la creación de clientes (contactos, combos, modales)
 */

// ============================================================
// CONTACTOS - GESTIÓN
// ============================================================
let contactos = [];
let editandoId = null;

function agregarContactoATabla(contacto) {
    const tbody = document.getElementById('contact-table-body');
    const empty = document.getElementById('empty-contacts');

    empty.style.display = 'none';

    const existingRow = document.getElementById(`contact-row-${contacto.id}`);
    if (existingRow) {
        actualizarFilaContacto(contacto);
        return;
    }

    const tr = document.createElement('tr');
    tr.id = `contact-row-${contacto.id}`;
    tr.innerHTML = generarFilaContacto(contacto);
    tbody.appendChild(tr);

    actualizarContador();
}

function generarFilaContacto(contacto) {
    return `
        <td>${contacto.id}</td>
        <td><strong>${escapeHtml(contacto.name)}</strong> ${contacto.lastnames ? escapeHtml(contacto.lastnames) : ''}</td>
        <td>${contacto.email ? escapeHtml(contacto.email) : '-'}</td>
        <td>${contacto.phone1 ? escapeHtml(contacto.phone1) : '-'}</td>
        <td>${contacto.principal ? '<span class="badge-principal"><i class="ti ti-star-filled"></i> Principal</span>' : ''}</td>
        <td>
            <div class="action-cell">
                <button type="button" class="btn-action btn-edit-contact" onclick="editarContacto(${contacto.id})" title="Editar contacto">
                    <i class="ti ti-pencil"></i>
                </button>
                <button type="button" class="btn-action btn-remove-contact" onclick="eliminarContacto(${contacto.id})" title="Eliminar contacto">
                    <i class="ti ti-trash"></i>
                </button>
            </div>
        </td>
    `;
}

function actualizarFilaContacto(contacto) {
    const row = document.getElementById(`contact-row-${contacto.id}`);
    if (row) {
        row.innerHTML = generarFilaContacto(contacto);
    }
}

function eliminarContacto(id) {
    if (!confirm('¿Estás seguro de eliminar este contacto?')) return;

    const eraPrincipal = contactos.find(c => c.id === id)?.principal;
    contactos = contactos.filter(c => c.id !== id);

    if (eraPrincipal && contactos.length > 0) {
        contactos[0].principal = true;
        actualizarFilaContacto(contactos[0]);
    }

    const row = document.getElementById(`contact-row-${id}`);
    if (row) row.remove();

    if (contactos.length === 0) {
        document.getElementById('empty-contacts').style.display = 'block';
    }

    actualizarContador();
}

function editarContacto(id) {
    const contacto = contactos.find(c => c.id === id);
    if (!contacto) return;

    editandoId = id;
    document.getElementById('modal-contact-title').innerHTML = '<i class="ti ti-pencil"></i> Editar Contacto';
    document.getElementById('modal-contact-save-btn').innerHTML = '<i class="ti ti-check"></i> Actualizar contacto';
    document.getElementById('modal-contact-edit-id').value = id;

    document.getElementById('modal-contact-name').value = contacto.name || '';
    document.getElementById('modal-contact-lastnames').value = contacto.lastnames || '';
    document.getElementById('modal-contact-email').value = contacto.email || '';
    document.getElementById('modal-contact-qualification').value = contacto.qualification || '';
    document.getElementById('modal-contact-phone1').value = contacto.phone1 || '';
    document.getElementById('modal-contact-phone2').value = contacto.phone2 || '';

    document.getElementById('modal-contacto').classList.add('active');
    setTimeout(() => document.getElementById('modal-contact-name').focus(), 150);
}

function actualizarContador() {
    document.getElementById('contact-counter').textContent = contactos.length;
}

// ============================================================
// MODAL CONTACTO
// ============================================================
function abrirModalContacto() {
    editandoId = null;
    document.getElementById('modal-contact-title').innerHTML = '<i class="ti ti-user-plus"></i> Nuevo Contacto';
    document.getElementById('modal-contact-save-btn').innerHTML = '<i class="ti ti-check"></i> Agregar contacto';
    document.getElementById('modal-contact-edit-id').value = '';
    document.getElementById('modal-contact-name').value = '';
    document.getElementById('modal-contact-lastnames').value = '';
    document.getElementById('modal-contact-email').value = '';
    document.getElementById('modal-contact-qualification').value = '';
    document.getElementById('modal-contact-phone1').value = '';
    document.getElementById('modal-contact-phone2').value = '';
    document.getElementById('modal-contacto').classList.add('active');
    setTimeout(() => document.getElementById('modal-contact-name').focus(), 150);
}

function cerrarModalContacto() {
    document.getElementById('modal-contacto').classList.remove('active');
    editandoId = null;
}

function guardarContactoModal() {
    const name = document.getElementById('modal-contact-name').value.trim();
    if (!name) {
        alert('El nombre del contacto es obligatorio.');
        document.getElementById('modal-contact-name').focus();
        return;
    }

    const lastnames = document.getElementById('modal-contact-lastnames').value.trim();
    const email = document.getElementById('modal-contact-email').value.trim();
    const qualification = document.getElementById('modal-contact-qualification').value.trim();
    const phone1 = document.getElementById('modal-contact-phone1').value.trim();
    const phone2 = document.getElementById('modal-contact-phone2').value.trim();

    const editId = parseInt(document.getElementById('modal-contact-edit-id').value);

    if (editId) {
        const index = contactos.findIndex(c => c.id === editId);
        if (index !== -1) {
            contactos[index] = {
                ...contactos[index],
                name: name,
                lastnames: lastnames,
                email: email,
                qualification: qualification,
                phone1: phone1,
                phone2: phone2
            };
            actualizarFilaContacto(contactos[index]);
        }
    } else {
        const contacto = {
            id: contactos.length + 1,
            name: name,
            lastnames: lastnames,
            email: email,
            qualification: qualification,
            phone1: phone1,
            phone2: phone2,
            principal: contactos.length === 0
        };
        contactos.push(contacto);
        agregarContactoATabla(contacto);
    }

    cerrarModalContacto();
}

// ============================================================
// UTILIDADES
// ============================================================
function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

// ============================================================
// ENVÍO DE FORMULARIO
// ============================================================
document.getElementById('form-client').addEventListener('submit', function(e) {
    document.querySelectorAll('input[name^="contacts"]').forEach(el => el.remove());

    contactos.forEach((contacto, index) => {
        // Mapeo: clave en el objeto JS -> nombre de campo que espera el controller
        const fieldMap = {
            name: 'name',
            lastnames: 'last_names',
            email: 'email',
            qualification: 'qualification',
            phone1: 'first_phone',
            phone2: 'second_phone'
        };

        Object.entries(fieldMap).forEach(([jsKey, backendKey]) => {
            if (contacto[jsKey]) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `contacts[${index}][${backendKey}]`;
                input.value = contacto[jsKey];
                this.appendChild(input);
            }
        });

        if (contacto.principal) {
            const principalInput = document.createElement('input');
            principalInput.type = 'hidden';
            principalInput.name = `contacts[${index}][es_principal]`;
            principalInput.value = '1';
            this.appendChild(principalInput);
        }
    });
});

// ============================================================
// COMBO BUSCABLE GENÉRICO
// ============================================================
function crearCombo({ inputId, listId, clearId, onSelect, onClear }) {
    const input = document.getElementById(inputId);
    const list = document.getElementById(listId);
    const clear = document.getElementById(clearId);
    let options = [];
    let activeIndex = -1;

    function normalizar(str) {
        return (str || '').toString().toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }

    function render(filtro) {
        const term = normalizar(filtro);
        const filtradas = term ? options.filter(o => normalizar(o.label).includes(term)) : options;
        list.innerHTML = filtradas.length === 0
            ? '<div class="combo-empty">Sin resultados</div>'
            : filtradas.map((o, idx) => `<div class="combo-item" data-idx="${idx}">${o.label}</div>`).join('');
        list._filtradas = filtradas;
        activeIndex = -1;
        list.classList.add('show');
    }

    function cerrar() {
        list.classList.remove('show');
        activeIndex = -1;
    }

    function seleccionar(opt) {
        input.value = opt.label;
        clear.classList.add('show');
        cerrar();
        onSelect(opt);
    }

    function actualizarActivo() {
        list.querySelectorAll('.combo-item').forEach(el => el.classList.remove('active'));
        const el = list.querySelector(`[data-idx="${activeIndex}"]`);
        if (el) {
            el.classList.add('active');
            el.scrollIntoView({ block: 'nearest' });
        }
    }

    input.addEventListener('focus', () => {
        if (!input.disabled) render(input.value);
    });

    input.addEventListener('input', () => {
        clear.classList.toggle('show', input.value !== '');
        render(input.value);
        if (onClear) onClear(false);
    });

    input.addEventListener('keydown', (e) => {
        const filtradas = list._filtradas || [];
        if (!list.classList.contains('show')) return;
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIndex = Math.min(activeIndex + 1, filtradas.length - 1);
            actualizarActivo();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIndex = Math.max(activeIndex - 1, 0);
            actualizarActivo();
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (activeIndex >= 0 && filtradas[activeIndex]) seleccionar(filtradas[activeIndex]);
        } else if (e.key === 'Escape') {
            cerrar();
        }
    });

    list.addEventListener('mousedown', (e) => {
        e.preventDefault();
        const item = e.target.closest('.combo-item');
        if (!item) return;
        seleccionar(list._filtradas[parseInt(item.dataset.idx)]);
    });

    document.addEventListener('click', (e) => {
        if (!input.contains(e.target) && !list.contains(e.target)) cerrar();
    });

    clear.addEventListener('click', () => {
        input.value = '';
        clear.classList.remove('show');
        cerrar();
        if (onClear) onClear(true);
        input.focus();
    });

    return {
        setOptions(nuevasOpciones, placeholder) {
            options = nuevasOpciones || [];
            input.value = '';
            clear.classList.remove('show');
            input.disabled = false;
            input.placeholder = placeholder || 'Escribe para buscar...';
            cerrar();
        },
        disable(placeholder) {
            input.disabled = true;
            input.value = '';
            clear.classList.remove('show');
            input.placeholder = placeholder || '';
            options = [];
            cerrar();
        }
    };
}

// ============================================================
// INICIALIZACIÓN - COMBOS DE PAÍS Y CIUDAD
// ============================================================
const comboPais = crearCombo({
    inputId: 'create-pais-input',
    listId: 'create-pais-list',
    clearId: 'create-pais-clear',
    onSelect: (opt) => {
        document.getElementById('create-country-name').value = opt.label;
        document.getElementById('create-country-code').value = opt.value;
        cargarCiudades(opt.value);
    },
    onClear: (full) => {
        document.getElementById('create-country-name').value = '';
        document.getElementById('create-country-code').value = '';
        if (full) {
            comboCiudad.disable('Seleccione país primero');
            document.getElementById('create-ciudad-name').value = '';
        }
    }
});

const comboCiudad = crearCombo({
    inputId: 'create-ciudad-input',
    listId: 'create-ciudad-list',
    clearId: 'create-ciudad-clear',
    onSelect: (opt) => {
        document.getElementById('create-ciudad-name').value = opt.label;
    },
    onClear: () => {
        document.getElementById('create-ciudad-name').value = '';
    }
});

function cargarPaises() {
    fetch(window.geoPaisesUrl)
        .then(r => r.json())
        .then(paises => comboPais.setOptions(paises.map(p => ({ value: p.codigo, label: p.nombre })), 'Escribe para buscar país...'))
        .catch(() => comboPais.setOptions([], 'No se pudo cargar'));
}

function cargarCiudades(countryCode) {
    comboCiudad.disable('Cargando...');
    if (!countryCode) {
        comboCiudad.disable('Seleccione país primero');
        return;
    }
    fetch(`${window.geoCiudadesUrl}?country=${countryCode}`)
        .then(r => r.json())
        .then(ciudades => comboCiudad.setOptions(ciudades.map(c => ({ value: c.nombre, label: c.nombre, geoNameId: c.geoNameId })), 'Escribe para buscar ciudad...'))
        .catch(() => comboCiudad.setOptions([], 'No se pudo cargar'));
}

// ============================================================
// INICIALIZACIÓN - EVENTOS DEL DOM
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    // Cargar países al inicio
    cargarPaises();

    // Cerrar modal con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarModalContacto();
        }
    });

    // Cerrar modal haciendo clic fuera
    document.getElementById('modal-contacto').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModalContacto();
        }
    });
});
