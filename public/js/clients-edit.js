/**
 * CLIENTS EDIT - JAVASCRIPT
 * Módulo para la edición de clientes (contactos, combos, eliminación)
 */

// ============================================================
// VARIABLES GLOBALES
// ============================================================
const toDelete = new Set();
let newIdx = 0;

// Datos del cliente desde el backend (preselección de país/ciudad)
const clientCountryName = window.clientCountryName || '';
const clientCityName = window.clientCityName || '';

// ============================================================
// ELIMINAR / RESTAURAR CONTACTO EXISTENTE
// ============================================================
function markDelete(id, btn) {
    const row = document.getElementById('existing-row-' + id);
    if (!row) return;

    if (toDelete.has(id)) {
        toDelete.delete(id);
        row.classList.remove('is-deleted');
        btn.classList.remove('active');
        btn.title = 'Eliminar contacto';
        row.querySelectorAll('input[data-original-name]').forEach(i => {
            i.name = i.dataset.originalName;
            i.disabled = false;
        });
    } else {
        if (!confirm('¿Eliminar este contacto al guardar?')) return;
        toDelete.add(id);
        row.classList.add('is-deleted');
        btn.classList.add('active');
        btn.title = 'Clic para deshacer';
        row.querySelectorAll('input').forEach(i => {
            if (!i.dataset.originalName) i.dataset.originalName = i.name;
            i.removeAttribute('name'); // <-- ya no se envía, en vez de disabled
        });
    }
    syncDeleteInputs();
}

function syncDeleteInputs() {
    const container = document.getElementById('delete-inputs');
    if (!container) return;
    container.innerHTML = '';
    toDelete.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete_contacts[]';
        input.value = id;
        container.appendChild(input);
    });
}

// ============================================================
// SOLO UN CONTACTO PRINCIPAL A LA VEZ
// ============================================================
document.addEventListener('change', function(e) {
    if (e.target.classList && e.target.classList.contains('principal-checkbox') && e.target.checked) {
        document.querySelectorAll('.principal-checkbox').forEach(cb => {
            if (cb !== e.target) cb.checked = false;
        });
    }
});

// ============================================================
// AGREGAR NUEVO CONTACTO
// ============================================================
function addNewContact() {
    const note = document.getElementById('no-contacts-note');
    if (note) note.style.display = 'none';

    const tbody = document.getElementById('contacts-tbody');
    if (!tbody) return;

    const i = newIdx++;
    const tr = document.createElement('tr');
    tr.className = 'contact-row is-new';
    tr.id = 'new-row-' + i;
    tr.innerHTML = `
        <td class="col-principal" title="Los contactos nuevos no inician como principal">—</td>
        <td class="col-name">
            <input type="text" name="new_contacts[${i}][name]" placeholder="Nombre" required>
        </td>
        <td>
            <input type="text" name="new_contacts[${i}][last_names]" placeholder="Apellidos">
        </td>
        <td>
            <input type="email" name="new_contacts[${i}][email]" placeholder="correo@ejemplo.com">
        </td>
        <td>
            <input type="text" name="new_contacts[${i}][qualification]" placeholder="Ej: Gerente">
        </td>
        <td>
            <input type="text" name="new_contacts[${i}][first_phone]" placeholder="Principal">
        </td>
        <td>
            <input type="text" name="new_contacts[${i}][second_phone]" placeholder="Opcional">
        </td>
        <td class="col-actions">
            <button type="button" class="row-del-btn" title="Quitar fila"
                    onclick="document.getElementById('new-row-${i}').remove()">
                <i class="ti ti-x"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    const firstInput = tr.querySelector('input[name^="new_contacts"]');
    if (firstInput) firstInput.focus();
}

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
        return (str || '').toString()
            .toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }

    function render(filtro) {
        const term = normalizar(filtro);
        const filtradas = term
            ? options.filter(o => normalizar(o.label).includes(term))
            : options;

        if (filtradas.length === 0) {
            list.innerHTML = '<div class="combo-empty">Sin resultados</div>';
        } else {
            list.innerHTML = filtradas.map((o, idx) =>
                `<div class="combo-item" data-idx="${idx}">${o.label}</div>`
            ).join('');
        }
        list._filtradas = filtradas;
        activeIndex = -1;
        list.classList.add('show');
    }

    function cerrar() {
        list.classList.remove('show');
        activeIndex = -1;
    }

    function seleccionar(opt, silent) {
        input.value = opt.label;
        clear.classList.add('show');
        cerrar();
        if (!silent) onSelect(opt);
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
        if (input.value === '') {
            clear.classList.remove('show');
        } else {
            clear.classList.add('show');
        }
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
            if (activeIndex >= 0 && filtradas[activeIndex]) {
                seleccionar(filtradas[activeIndex]);
            }
        } else if (e.key === 'Escape') {
            cerrar();
        }
    });

    list.addEventListener('mousedown', (e) => {
        e.preventDefault();
        const item = e.target.closest('.combo-item');
        if (!item) return;
        const idx = parseInt(item.dataset.idx);
        seleccionar(list._filtradas[idx]);
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
        },
        preselect(label) {
            if (!label) return;
            const opt = options.find(o => o.label === label);
            if (opt) seleccionar(opt, true);
        }
    };
}

// ============================================================
// INICIALIZACIÓN - COMBOS DE PAÍS Y CIUDAD
// ============================================================
const comboPais = crearCombo({
    inputId: 'edit-pais-input',
    listId: 'edit-pais-list',
    clearId: 'edit-pais-clear',
    onSelect: (opt) => {
        document.getElementById('edit-country-name').value = opt.label;
        document.getElementById('edit-country-code').value = opt.value;
        cargarCiudadesEdit(opt.value);
    },
    onClear: (full) => {
        document.getElementById('edit-country-name').value = '';
        document.getElementById('edit-country-code').value = '';
        if (full) {
            comboCiudad.disable('Seleccione país primero');
            document.getElementById('edit-ciudad-name').value = '';
        }
    }
});

const comboCiudad = crearCombo({
    inputId: 'edit-ciudad-input',
    listId: 'edit-ciudad-list',
    clearId: 'edit-ciudad-clear',
    onSelect: (opt) => {
        document.getElementById('edit-ciudad-name').value = opt.label;
    },
    onClear: () => {
        document.getElementById('edit-ciudad-name').value = '';
    }
});

function cargarPaisesEdit() {
    fetch(window.geoPaisesUrl)
        .then(r => r.json())
        .then(paises => {
            const opciones = paises.map(p => ({ value: p.codigo, label: p.nombre }));
            comboPais.setOptions(opciones, 'Escribe para buscar país...');

            if (clientCountryName) {
                const match = opciones.find(o => o.label === clientCountryName);
                if (match) {
                    document.getElementById('edit-pais-input').value = match.label;
                    document.getElementById('edit-country-code').value = match.value;
                    document.getElementById('edit-pais-clear').classList.add('show');
                    cargarCiudadesEdit(match.value, clientCityName);
                }
            }
        })
        .catch(() => comboPais.setOptions([], 'No se pudo cargar'));
}

function cargarCiudadesEdit(countryCode, selectedCity) {
    comboCiudad.disable('Cargando...');

    if (!countryCode) {
        comboCiudad.disable('Seleccione país primero');
        return;
    }

    fetch(`${window.geoCiudadesUrl}?country=${countryCode}`)
        .then(r => r.json())
        .then(ciudades => {
            const opciones = ciudades.map(c => ({ value: c.nombre, label: c.nombre, geoNameId: c.geoNameId }));
            comboCiudad.setOptions(opciones, 'Escribe para buscar ciudad...');

            const cityToSelect = selectedCity || clientCityName;
            if (cityToSelect) {
                const match = opciones.find(o => o.label === cityToSelect);
                if (match) {
                    document.getElementById('edit-ciudad-input').value = match.label;
                    document.getElementById('edit-ciudad-clear').classList.add('show');
                } else {
                    document.getElementById('edit-ciudad-input').value = cityToSelect;
                    document.getElementById('edit-ciudad-clear').classList.add('show');
                }
            }
        })
        .catch(() => comboCiudad.setOptions([], 'No se pudo cargar'));
}

// ============================================================
// INICIALIZACIÓN DEL DOM
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    cargarPaisesEdit();

    // Actualizar contador de contactos al eliminar/agregar
    const observer = new MutationObserver(() => {
        const rows = document.querySelectorAll('#contacts-tbody .contact-row');
        const count = document.getElementById('contacts-count');
        if (count) {
            const total = rows.length;
            count.textContent = total + ' contacto(s)';
        }
    });

    const tbody = document.getElementById('contacts-tbody');
    if (tbody) {
        observer.observe(tbody, { childList: true, subtree: true });
    }
});
