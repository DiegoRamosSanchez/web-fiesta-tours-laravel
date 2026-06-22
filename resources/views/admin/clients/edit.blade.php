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
            <div class="card" style="margin-bottom:1rem">
                <div class="card-header">
                    <div>
                        <div class="card-title">Datos de la empresa</div>
                        <div class="card-sub">ID #{{ $client->id_client }}</div>
                    </div>
                </div>

                <div class="form-field" style="margin-bottom:1.1rem">
                    <label>Nombre comercial *</label>
                    <input type="text" name="name_client"
                        value="{{ old('name_client', $client->name_client) }}"
                        maxlength="120" required autofocus>
                </div>

                <div class="form-field" style="margin-bottom:1.1rem">
                    <label>Razón Social</label>
                    <input type="text" name="business_name"
                        value="{{ old('business_name', $client->business_name) }}"
                        maxlength="150">
                </div>

                <div class="form-field" style="margin-bottom:1.1rem">
                    <label>Código tributario (RUC)</label>
                    <input type="text" name="tax_code"
                        value="{{ old('tax_code', $client->tax_code) }}"
                        maxlength="20" placeholder="Ej: 20123456789">
                </div>
                <div class="form-field" style="margin-bottom:1.1rem">
                    <label>Teléfono general</label>
                    <input type="text" name="general_phone"
                        value="{{ old('general_phone', $client->general_phone) }}"
                        maxlength="20" placeholder="Ej: 01-234567">
                </div>
                <div class="form-field">
                    <label>Email general</label>
                    <input type="email" name="general_email"
                        value="{{ old('general_email', $client->general_email) }}"
                        maxlength="120" placeholder="contacto@empresa.com">
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
