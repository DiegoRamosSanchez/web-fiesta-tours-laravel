@extends('layouts.app')
@section('title', 'Editar Cliente')
@section('content')

<div class="page-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.4rem">
    <div>
        <div class="page-title">Editar Cliente</div>
        <div class="page-sub">Modifica los datos y contactos del cliente</div>
    </div>
    <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary btn-sm">
        <i class="ti ti-arrow-left" style="font-size:13px"></i> Volver
    </a>
</div>

@if($errors->any())
    <div class="alert alert-error" style="margin-bottom:1rem">
        <i class="ti ti-alert-circle"></i>
        <ul style="list-style:none;margin-left:.5rem">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<style>
    .edit-client-layout{
        display:grid;
        grid-template-columns: 550px 1fr;
        gap:1.4rem;
        align-items:start;
    }
    @media (max-width: 980px){
        .edit-client-layout{ grid-template-columns: 1fr; }
        .edit-client-left{ position:static !important; }
    }
    .edit-client-left{
        position:sticky;
        top:1rem;
    }

    /* ── Cards de datos (rediseño ordenado) ── */
    .info-card{
        background:#fff; border:1px solid #e2e8f0; border-radius:14px;
        padding:1.4rem 1.5rem; margin-bottom:1rem;
    }
    .info-card-header{
        display:flex; align-items:flex-start; gap:12px;
        margin-bottom:1.3rem; padding-bottom:1.1rem; border-bottom:1px solid #f1f5f9;
    }
    .info-card-icon{
        width:38px; height:38px; border-radius:10px; background:#eef2ff; color:#6366f1;
        display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0;
    }
    .info-card-title{ font-size:14.5px; font-weight:700; color:#0f172a; margin:0 0 1px; }
    .info-card-sub{ font-size:11.5px; color:#94a3b8; margin:0; }

    .field-group{ display:flex; flex-direction:column; gap:.35rem; margin-bottom:1.05rem; }
    .field-group:last-child{ margin-bottom:0; }
    .field-group label{
        font-size:10.5px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.4px;
    }
    .field-group label .req{ color:#ef4444; }
    .field-group input[type="text"],
    .field-group input[type="email"]{
        width:100%; padding:.62rem .8rem; border:1px solid #e2e8f0; border-radius:9px;
        font-size:13px; color:#0f172a; outline:none; transition:border-color .15s, background .15s;
        background:#fff; box-sizing:border-box;
    }
    .field-group input::placeholder{ color:#cbd5e1; }
    .field-group input:focus{ border-color:#6366f1; }

    .field-grid-2{ display:grid; grid-template-columns:1fr 1fr; gap:.9rem; }
    @media (max-width:480px){ .field-grid-2{ grid-template-columns:1fr; } }

    /* ── Combos buscables (País / Ciudad) ── */
    .combo-wrap{ position:relative; }
    .combo-input{
        width:100%; padding:.62rem .8rem; border:1px solid #e2e8f0; border-radius:9px;
        font-size:13px; color:#0f172a; outline:none; transition:border-color .15s, background .15s;
        background:#fff; box-sizing:border-box;
    }
    .combo-input:focus{ border-color:#6366f1; }
    .combo-input[disabled]{ background:#f8fafc; color:#94a3b8; cursor:not-allowed; }
    .combo-list{
        position:absolute; top:calc(100% + 4px); left:0; right:0;
        background:#fff; border:1px solid #e2e8f0; border-radius:9px;
        max-height:220px; overflow-y:auto; z-index:50;
        box-shadow:0 10px 25px -5px rgba(0,0,0,.1);
        display:none;
    }
    .combo-list.show{ display:block; }
    .combo-item{ padding:.55rem .8rem; font-size:13px; color:#0f172a; cursor:pointer; }
    .combo-item:hover, .combo-item.active{ background:#eef2ff; color:#4338ca; }
    .combo-empty{ padding:.6rem .8rem; font-size:12.5px; color:#94a3b8; }
    .combo-clear{
        position:absolute; right:.6rem; top:50%; transform:translateY(-50%);
        background:none; border:none; color:#cbd5e1; cursor:pointer; font-size:14px;
        display:none; padding:2px; line-height:1;
    }
    .combo-clear.show{ display:block; }
    .combo-clear:hover{ color:#ef4444; }

    /* ── Tabla de contactos ── */
    .contacts-table-wrap{ overflow-x:auto; border:1px solid #e2e8f0; border-radius:10px; }
    table.contacts-table{ width:100%; border-collapse:collapse; min-width:880px; }
    table.contacts-table thead th{
        background:#f8fafc; color:#94a3b8; font-size:10.5px; font-weight:700;
        text-transform:uppercase; letter-spacing:.4px; text-align:left;
        padding:.6rem .6rem; border-bottom:1px solid #e2e8f0; white-space:nowrap;
    }
    table.contacts-table tbody td{
        border-bottom:1px solid #f1f5f9; padding:.35rem .5rem; vertical-align:middle;
    }
    table.contacts-table tbody tr:last-child td{ border-bottom:none; }
    table.contacts-table tbody tr.is-new{ background:#f8fdfb; }
    table.contacts-table tbody tr.is-deleted{ opacity:.35; }
    table.contacts-table tbody tr.is-deleted input{ pointer-events:none; }

    .contacts-table input[type="text"],
    .contacts-table input[type="email"]{
        width:100%; border:1px solid transparent; background:transparent;
        font-size:12.5px; padding:.4rem .45rem; border-radius:6px; color:#0f172a;
        min-width:110px;
    }
    .contacts-table input[type="text"]:hover,
    .contacts-table input[type="email"]:hover{ background:#f8fafc; }
    .contacts-table input[type="text"]:focus,
    .contacts-table input[type="email"]:focus{
        background:#fff; border-color:#cbd5e1; outline:none;
        box-shadow:0 0 0 2px rgba(203,213,225,.4);
    }
    .contacts-table td.col-name input{ font-weight:600; min-width:130px; }
    .contacts-table td.col-principal{ text-align:center; width:44px; }
    .contacts-table td.col-actions{ text-align:center; width:40px; }

    .star-toggle{ width:16px; height:16px; cursor:pointer; accent-color:#d97706; }

    .row-del-btn{
        background:none; border:none; cursor:pointer; color:#cbd5e1; font-size:15px;
        padding:.3rem; border-radius:6px; line-height:1;
    }
    .row-del-btn:hover{ color:#e63232; background:#fef2f2; }
    .row-del-btn.active{ color:#e63232; }

    .add-row-btn{
        display:inline-flex; align-items:center; gap:6px; padding:.55rem .9rem;
        background:#10b981; color:#fff; border:none; border-radius:8px;
        font-size:12px; font-weight:600; cursor:pointer; white-space:nowrap;
    }
    .add-row-btn:hover{ background:#0d9b6c; }

    .table-empty-note{ color:#94a3b8; font-size:12.5px; text-align:center; padding:1.4rem; }
</style>

<form action="{{ route('admin.clients.update', $client->id_client) }}"
      method="POST" id="edit-form">
    @csrf @method('PUT')

    {{-- Campos ocultos para contactos a eliminar --}}
    <div id="delete-inputs"></div>

    <div class="edit-client-layout">

        {{-- ══════════ IZQUIERDA: DATOS DEL CLIENTE ══════════ --}}
        <div class="edit-client-left">

            {{-- ── DATOS DE LA EMPRESA ── --}}
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon">
                        <i class="ti ti-building"></i>
                    </div>
                    <div>
                        <p class="info-card-title">Datos de la empresa</p>
                        <p class="info-card-sub">ID #{{ $client->id_client }}</p>
                    </div>
                </div>

                <div class="field-group">
                    <label>Nombre comercial <span class="req">*</span></label>
                    <input type="text" name="name_client"
                        value="{{ old('name_client', $client->name_client) }}"
                        maxlength="120" required autofocus
                        placeholder="Ej: Fiesta Tours Perú S.A.C.">
                </div>

                <div class="field-group">
                    <label>Razón Social</label>
                    <input type="text" name="business_name"
                        value="{{ old('business_name', $client->business_name) }}"
                        maxlength="150" placeholder="Ej: Fiesta Tours Perú S.A.C.">
                </div>

                <div class="field-grid-2">
                    <div class="field-group">
                        <label>Código tributario (RUC)</label>
                        <input type="text" name="tax_code"
                            value="{{ old('tax_code', $client->tax_code) }}"
                            maxlength="20" placeholder="Ej: 20123456789">
                    </div>
                    <div class="field-group">
                        <label>Teléfono general</label>
                        <input type="text" name="general_phone"
                            value="{{ old('general_phone', $client->general_phone) }}"
                            maxlength="20" placeholder="Ej: 01-234567">
                    </div>
                </div>

                <div class="field-group">
                    <label>Email general</label>
                    <input type="email" name="general_email"
                        value="{{ old('general_email', $client->general_email) }}"
                        maxlength="120" placeholder="contacto@empresa.com">
                </div>
            </div>

            {{-- ── UBICACIÓN ── --}}
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon">
                        <i class="ti ti-map-pin"></i>
                    </div>
                    <div>
                        <p class="info-card-title">Ubicación</p>
                        <p class="info-card-sub">País, ciudad y dirección exacta del cliente</p>
                    </div>
                </div>

                <div class="field-grid-2">
                    <div class="field-group">
                        <label>País</label>
                        <div class="combo-wrap" id="combo-pais">
                            <input type="text" class="combo-input" id="edit-pais-input"
                                   placeholder="Cargando países..." autocomplete="off" disabled>
                            <button type="button" class="combo-clear" id="edit-pais-clear" tabindex="-1">
                                <i class="ti ti-x"></i>
                            </button>
                            <div class="combo-list" id="edit-pais-list"></div>
                        </div>
                        <input type="hidden" name="country_name" id="edit-country-name"
                            value="{{ old('country_name', $client->country_name) }}">
                        <input type="hidden" id="edit-country-code">
                    </div>

                    <div class="field-group">
                        <label>Ciudad</label>
                        <div class="combo-wrap" id="combo-ciudad">
                            <input type="text" class="combo-input" id="edit-ciudad-input"
                                   placeholder="Seleccione país primero" autocomplete="off" disabled>
                            <button type="button" class="combo-clear" id="edit-ciudad-clear" tabindex="-1">
                                <i class="ti ti-x"></i>
                            </button>
                            <div class="combo-list" id="edit-ciudad-list"></div>
                        </div>
                        <input type="hidden" name="city_name" id="edit-ciudad-name"
                            value="{{ old('city_name', $client->city_name) }}">
                    </div>
                </div>

                <div class="field-group">
                    <label>Dirección</label>
                    <input type="text" name="address"
                        value="{{ old('address', $client->address) }}"
                        maxlength="255"
                        placeholder="Ej: Avenida Suecia, calle 124, primera casa">
                </div>
            </div>

            <div style="display:flex;gap:.8rem">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy" style="font-size:14px"></i> Guardar cambios
                </button>
                <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>

        {{-- ══════════ DERECHA: CONTACTOS EN TABLA ══════════ --}}
        <div class="edit-client-right">
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Contactos registrados</div>
                        <div class="card-sub" id="contacts-count">{{ $client->contacts->count() }} contacto(s)</div>
                    </div>
                    <button type="button" class="add-row-btn" onclick="addNewContact()">
                        <i class="ti ti-plus" style="font-size:14px"></i> Añadir contacto
                    </button>
                </div>

                <div class="contacts-table-wrap">
                    <table class="contacts-table">
                        <thead>
                            <tr>
                                <th title="Principal"><i class="ti ti-star"></i></th>
                                <th>Nombre *</th>
                                <th>Apellidos</th>
                                <th>Email</th>
                                <th>Cargo</th>
                                <th>Teléfono 1</th>
                                <th>Teléfono 2</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="contacts-tbody">
                            @foreach($client->contacts as $idx => $contact)
                            <tr class="contact-row" id="existing-row-{{ $contact->id_contacts }}">
                                <td class="col-principal">
                                    <input type="hidden" name="contacts[{{ $idx }}][id]" value="{{ $contact->id_contacts }}">
                                    <input type="checkbox" class="star-toggle principal-checkbox"
                                           name="contacts[{{ $idx }}][es_principal]" value="1"
                                           title="Marcar como principal"
                                           {{ old('contacts.'.$idx.'.es_principal', $contact->es_principal) ? 'checked' : '' }}>
                                </td>
                                <td class="col-name">
                                    <input type="text" name="contacts[{{ $idx }}][name]"
                                           value="{{ old('contacts.'.$idx.'.name', $contact->name) }}"
                                           placeholder="Nombre" required>
                                </td>
                                <td>
                                    <input type="text" name="contacts[{{ $idx }}][last_names]"
                                           value="{{ old('contacts.'.$idx.'.last_names', $contact->last_names) }}"
                                           placeholder="Apellidos">
                                </td>
                                <td>
                                    <input type="email" name="contacts[{{ $idx }}][email]"
                                           value="{{ old('contacts.'.$idx.'.email', $contact->email) }}"
                                           placeholder="correo@ejemplo.com">
                                </td>
                                <td>
                                    <input type="text" name="contacts[{{ $idx }}][qualification]"
                                           value="{{ old('contacts.'.$idx.'.qualification', $contact->qualification) }}"
                                           placeholder="Ej: Gerente">
                                </td>
                                <td>
                                    <input type="text" name="contacts[{{ $idx }}][first_phone]"
                                           value="{{ old('contacts.'.$idx.'.first_phone', $contact->first_phone) }}"
                                           placeholder="Principal">
                                </td>
                                <td>
                                    <input type="text" name="contacts[{{ $idx }}][second_phone]"
                                           value="{{ old('contacts.'.$idx.'.second_phone', $contact->second_phone) }}"
                                           placeholder="Opcional">
                                </td>
                                <td class="col-actions">
                                    <button type="button" class="row-del-btn"
                                            onclick="markDelete({{ $contact->id_contacts }}, this)"
                                            title="Eliminar contacto">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <p class="table-empty-note" id="no-contacts-note"
                       style="{{ $client->contacts->isEmpty() ? '' : 'display:none' }}">
                        Este cliente no tiene contactos aún. Usa "Añadir contacto" para crear uno.
                    </p>
                </div>
            </div>
        </div>

    </div>
</form>

@push('scripts')
<script>
// ── Marcar contacto existente para eliminar ───────────────────
const toDelete = new Set();

// ── UBICACIÓN: valores existentes del cliente (para preseleccionar) ──
const clientCountryName = @json(old('country_name', $client->country_name));
const clientCityName    = @json(old('city_name', $client->city_name));

// ════════════════════════════════════════════════════════════════
// ── COMBO BUSCABLE GENÉRICO (input + lista filtrable) ───────────
// ════════════════════════════════════════════════════════════════
function crearCombo({ inputId, listId, clearId, onSelect, onClear }) {
    const input = document.getElementById(inputId);
    const list  = document.getElementById(listId);
    const clear = document.getElementById(clearId);
    let options = [];   // [{value, label, ...extra}]
    let activeIndex = -1;

    function normalizar(str) {
        return (str || '').toString()
            .toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, ''); // quita acentos
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
        if (el) { el.classList.add('active'); el.scrollIntoView({ block: 'nearest' }); }
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
        onClear && onClear(false);
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
        onClear && onClear(true);
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
        // Preselecciona una opción por su label sin disparar onSelect (usado al cargar datos existentes)
        preselect(label) {
            if (!label) return;
            const opt = options.find(o => o.label === label);
            if (opt) seleccionar(opt, true);
        }
    };
}

// ── Instancias de los 2 combos (País → Ciudad) ────────────────
const comboPais = crearCombo({
    inputId: 'edit-pais-input',
    listId:  'edit-pais-list',
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
    listId:  'edit-ciudad-list',
    clearId: 'edit-ciudad-clear',
    onSelect: (opt) => {
        document.getElementById('edit-ciudad-name').value = opt.label;
    },
    onClear: (full) => {
        document.getElementById('edit-ciudad-name').value = '';
    }
});

// ── Carga de datos + preselección del valor actual del cliente ─
function cargarPaisesEdit() {
    fetch('/api/geo/paises')
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

function cargarCiudadesEdit(countryCode, selectedCity = null) {
    comboCiudad.disable('Cargando...');

    if (!countryCode) {
        comboCiudad.disable('Seleccione país primero');
        return;
    }

    fetch(`/api/geo/ciudades?country=${countryCode}`)
        .then(r => r.json())
        .then(ciudades => {
            const opciones = ciudades.map(c => ({ value: c.nombre, label: c.nombre, geoNameId: c.geoNameId }));
            comboCiudad.setOptions(opciones, 'Escribe para buscar ciudad...');

            if (selectedCity) {
                const match = opciones.find(o => o.label === selectedCity);
                if (match) {
                    document.getElementById('edit-ciudad-input').value = match.label;
                    document.getElementById('edit-ciudad-clear').classList.add('show');
                } else {
                    // La ciudad guardada no aparece en el listado actual: se conserva el valor igualmente
                    document.getElementById('edit-ciudad-input').value = selectedCity;
                    document.getElementById('edit-ciudad-clear').classList.add('show');
                }
            }
        })
        .catch(() => comboCiudad.setOptions([], 'No se pudo cargar'));
}

cargarPaisesEdit();

// ── Eliminar / restaurar contacto existente ───────────────────
function markDelete(id, btn) {
    const row = document.getElementById('existing-row-' + id);
    if (toDelete.has(id)) {
        toDelete.delete(id);
        row.classList.remove('is-deleted');
        btn.classList.remove('active');
        btn.title = 'Eliminar contacto';
        row.querySelectorAll('input').forEach(i => i.disabled = false);
    } else {
        if (!confirm('¿Eliminar este contacto al guardar?')) return;
        toDelete.add(id);
        row.classList.add('is-deleted');
        btn.classList.add('active');
        btn.title = 'Clic para deshacer';
        row.querySelectorAll('input').forEach(i => {
            if (i.type !== 'hidden') i.disabled = true;
        });
    }
    syncDeleteInputs();
}

function syncDeleteInputs() {
    const container = document.getElementById('delete-inputs');
    container.innerHTML = '';
    toDelete.forEach(id => {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = 'delete_contacts[]';
        input.value = id;
        container.appendChild(input);
    });
}

// ── Solo un contacto principal a la vez ───────────────────────
document.addEventListener('change', function (e) {
    if (e.target.classList && e.target.classList.contains('principal-checkbox') && e.target.checked) {
        document.querySelectorAll('.principal-checkbox').forEach(cb => {
            if (cb !== e.target) cb.checked = false;
        });
    }
});

// ── Agregar nuevo contacto (como fila de la tabla) ────────────
let newIdx = 0;

function addNewContact() {
    document.getElementById('no-contacts-note').style.display = 'none';
    const tbody = document.getElementById('contacts-tbody');
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
        </td>`;
    tbody.appendChild(tr);
    tr.querySelector('input[name^="new_contacts"]').focus();
}
</script>
@endpush
@endsection
