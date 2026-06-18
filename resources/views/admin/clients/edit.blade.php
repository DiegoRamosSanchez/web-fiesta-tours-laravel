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

<div style="max-width:700px">
    @if($errors->any())
        <div class="alert alert-error">
            <i class="ti ti-alert-circle"></i>
            <ul style="list-style:none;margin-left:.5rem">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.clients.update', $client->id_client) }}"
          method="POST" id="edit-form">
        @csrf @method('PUT')

        {{-- Campos ocultos para contactos a eliminar --}}
        <div id="delete-inputs"></div>

        {{-- ── DATOS DEL CLIENTE ── --}}
        <div class="card" style="margin-bottom:1rem">
            <div class="card-header">
                <div>
                    <div class="card-title">Datos de la empresa</div>
                    <div class="card-sub">ID #{{ $client->id_client }}</div>
                </div>
            </div>
            <div class="form-field">
                <label>Nombre del cliente *</label>
                <input type="text" name="name_client"
                       value="{{ old('name_client', $client->name_client) }}"
                       maxlength="100" required autofocus>
            </div>
        </div>

        {{-- ── CONTACTOS EXISTENTES ── --}}
        <div class="card" style="margin-bottom:1rem">
            <div class="card-header">
                <div>
                    <div class="card-title">Contactos registrados</div>
                    <div class="card-sub">{{ $client->contacts->count() }} contacto(s)</div>
                </div>
            </div>

            <div id="existing-contacts">
                @foreach($client->contacts as $idx => $contact)
                <div class="contact-block" id="existing-{{ $contact->id_contacts }}"
                     style="border:1px solid #e2e8f0;border-radius:10px;padding:1rem;
                            margin-bottom:.8rem;position:relative;transition:opacity .2s">

                    {{-- Badge principal --}}
                    @if($contact->es_principal)
                        <span style="position:absolute;top:.7rem;left:1rem;
                                     background:#fef3c7;color:#92400e;font-size:10px;
                                     font-weight:700;padding:2px 8px;border-radius:999px;
                                     border:1px solid #fde68a">
                            ⭐ Principal
                        </span>
                    @endif

                    {{-- Botón eliminar contacto --}}
                    <button type="button"
                            onclick="markDelete({{ $contact->id_contacts }}, this)"
                            style="position:absolute;top:.7rem;right:.7rem;background:none;
                                   border:none;cursor:pointer;color:#94a3b8;font-size:16px"
                            title="Eliminar contacto">
                        <i class="ti ti-trash"></i>
                    </button>

                    <input type="hidden" name="contacts[{{ $idx }}][id]" value="{{ $contact->id_contacts }}">

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.7rem;margin-top:{{ $contact->es_principal ? '1.8rem' : '.3rem' }}">
                        <div class="form-field">
                            <label>Nombre *</label>
                            <input type="text" name="contacts[{{ $idx }}][name]"
                                   value="{{ old('contacts.'.$idx.'.name', $contact->name) }}"
                                   required>
                        </div>
                        <div class="form-field">
                            <label>Apellidos</label>
                            <input type="text" name="contacts[{{ $idx }}][last_names]"
                                   value="{{ old('contacts.'.$idx.'.last_names', $contact->last_names) }}">
                        </div>
                        <div class="form-field">
                            <label>Email</label>
                            <input type="email" name="contacts[{{ $idx }}][email]"
                                   value="{{ old('contacts.'.$idx.'.email', $contact->email) }}">
                        </div>
                        <div class="form-field">
                            <label>Cargo</label>
                            <input type="text" name="contacts[{{ $idx }}][qualification]"
                                   value="{{ old('contacts.'.$idx.'.qualification', $contact->qualification) }}"
                                   placeholder="Ej: Gerente, Coordinador...">
                        </div>
                        <div class="form-field">
                            <label>Teléfono 1</label>
                            <input type="text" name="contacts[{{ $idx }}][first_phone]"
                                   value="{{ old('contacts.'.$idx.'.first_phone', $contact->first_phone) }}">
                        </div>
                        <div class="form-field">
                            <label>Teléfono 2</label>
                            <input type="text" name="contacts[{{ $idx }}][second_phone]"
                                   value="{{ old('contacts.'.$idx.'.second_phone', $contact->second_phone) }}">
                        </div>
                    </div>

                    {{-- Marcar como principal --}}
                    <div style="margin-top:.8rem;display:flex;align-items:center;gap:8px">
                        <input type="checkbox" name="contacts[{{ $idx }}][es_principal]"
                               id="principal-{{ $idx }}" value="1"
                               {{ $contact->es_principal ? 'checked' : '' }}
                               style="width:15px;height:15px;accent-color:#e63232;cursor:pointer">
                        <label for="principal-{{ $idx }}"
                               style="font-size:12px;color:#64748b;cursor:pointer;
                                      text-transform:none;letter-spacing:0">
                            Marcar como contacto principal
                        </label>
                    </div>
                </div>
                @endforeach
            </div>

            @if($client->contacts->isEmpty())
                <p style="color:#94a3b8;font-size:13px;text-align:center;padding:1rem">
                    Este cliente no tiene contactos aún
                </p>
            @endif
        </div>

        {{-- ── NUEVOS CONTACTOS ── --}}
        <div class="card" style="margin-bottom:1rem">
            <div class="card-header">
                <div>
                    <div class="card-title">Agregar nuevos contactos</div>
                    <div class="card-sub">Opcional — se agregarán al guardar</div>
                </div>
                <button type="button" onclick="addNewContact()"
                        style="display:inline-flex;align-items:center;gap:6px;
                               padding:.5rem .9rem;background:#10b981;color:#fff;
                               border:none;border-radius:8px;font-size:12px;
                               font-weight:600;cursor:pointer">
                    <i class="ti ti-plus" style="font-size:14px"></i> Añadir contacto
                </button>
            </div>
            <div id="new-contacts-wrapper">
                <p id="new-contacts-empty"
                   style="color:#94a3b8;font-size:13px;text-align:center;padding:.5rem">
                    Haz clic en "Añadir contacto" para agregar uno nuevo
                </p>
            </div>
        </div>

        {{-- ── ACCIONES ── --}}
        <div style="display:flex;gap:.8rem">
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-device-floppy" style="font-size:14px"></i> Guardar cambios
            </button>
            <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>

    </form>
</div>

@push('scripts')
<script>
// ── Marcar contacto existente para eliminar ───────────────────
const toDelete = new Set();

function markDelete(id, btn) {
    const block = document.getElementById('existing-' + id);
    if (toDelete.has(id)) {
        // Desmarcar
        toDelete.delete(id);
        block.style.opacity = '1';
        block.style.pointerEvents = 'auto';
        btn.style.color = '#94a3b8';
        btn.title = 'Eliminar contacto';
        block.querySelector('input[type="hidden"]').disabled = false;
        block.querySelectorAll('input:not([type="hidden"])').forEach(i => i.disabled = false);
    } else {
        // Marcar para eliminar
        if (!confirm('¿Eliminar este contacto al guardar?')) return;
        toDelete.add(id);
        block.style.opacity = '.4';
        block.style.pointerEvents = 'none';
        btn.style.color = '#e63232';
        btn.title = 'Clic para deshacer';
        btn.style.pointerEvents = 'auto';
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

// ── Agregar nuevo contacto ────────────────────────────────────
let newIdx = 0;

function addNewContact() {
    document.getElementById('new-contacts-empty').style.display = 'none';
    const wrapper = document.getElementById('new-contacts-wrapper');
    const i = newIdx++;
    const div = document.createElement('div');
    div.id = 'new-contact-' + i;
    div.style.cssText = 'border:1px solid #e2e8f0;border-radius:10px;padding:1rem;margin-bottom:.8rem;position:relative;background:#f8fafc';
    div.innerHTML = `
        <div style="font-size:12px;font-weight:700;color:#10b981;margin-bottom:.8rem">
            <i class="ti ti-user-plus" style="font-size:14px"></i> Nuevo contacto
        </div>
        <button type="button" onclick="document.getElementById('new-contact-${i}').remove();checkEmpty()"
                style="position:absolute;top:.7rem;right:.7rem;background:none;border:none;
                       cursor:pointer;color:#94a3b8;font-size:16px">
            <i class="ti ti-x"></i>
        </button>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.7rem">
            <div class="form-field">
                <label>Nombre *</label>
                <input type="text" name="new_contacts[${i}][name]" placeholder="Nombre" required>
            </div>
            <div class="form-field">
                <label>Apellidos</label>
                <input type="text" name="new_contacts[${i}][last_names]" placeholder="Apellidos">
            </div>
            <div class="form-field">
                <label>Email</label>
                <input type="email" name="new_contacts[${i}][email]" placeholder="correo@ejemplo.com">
            </div>
            <div class="form-field">
                <label>Cargo</label>
                <input type="text" name="new_contacts[${i}][qualification]" placeholder="Ej: Gerente...">
            </div>
            <div class="form-field">
                <label>Teléfono 1</label>
                <input type="text" name="new_contacts[${i}][first_phone]" placeholder="Principal">
            </div>
            <div class="form-field">
                <label>Teléfono 2</label>
                <input type="text" name="new_contacts[${i}][second_phone]" placeholder="Opcional">
            </div>
        </div>`;
    wrapper.appendChild(div);
}

function checkEmpty() {
    const wrapper = document.getElementById('new-contacts-wrapper');
    const blocks  = wrapper.querySelectorAll('[id^="new-contact-"]');
    document.getElementById('new-contacts-empty').style.display =
        blocks.length === 0 ? 'block' : 'none';
}
</script>
@endpush
@endsection
