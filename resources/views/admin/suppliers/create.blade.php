@extends('layouts.app')
@section('title', 'Nuevo Proveedor')
@section('content')

<div class="page-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.4rem">
    <div>
        <div class="page-title">Nuevo Proveedor</div>
        <div class="page-sub">Registra un nuevo proveedor en el sistema</div>
    </div>
    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary btn-sm">
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

@if(session('error'))
    <div class="alert alert-error" style="margin-bottom:1rem">
        <i class="ti ti-alert-circle"></i>
        {{ session('error') }}
    </div>
@endif

<style>
    .edit-supplier-layout{
        display:grid;
        grid-template-columns: 550px 1fr;
        gap:1.4rem;
        align-items:start;
    }
    @media (max-width: 980px){
        .edit-supplier-layout{ grid-template-columns: 1fr; }
        .edit-supplier-left{ position:static !important; }
    }
    .edit-supplier-left{
        position:sticky;
        top:1rem;
    }

    /* ── Bloques tipo "destino / categoría" reutilizados ── */
    .inline-create-block{
        background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;
        padding:1rem;margin-bottom:1rem;
    }
    .inline-create-block .block-label{
        font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;
        letter-spacing:.5px;margin-bottom:.8rem;display:flex;align-items:center;gap:6px;
    }
    .inline-create-block select{ margin-top:.3rem; }
    .btn-inline-new{
        padding:.6rem .9rem;background:#fff;border:1px solid #e2e8f0;
        border-radius:9px;font-size:12px;font-weight:600;color:#6366f1;
        cursor:pointer;white-space:nowrap;flex-shrink:0;
    }
    .new-field-box{ display:none;margin-top:.7rem; }
    .new-field-box .inner{ background:#ede9fe;border-radius:8px;padding:.8rem; }
    .new-field-box label{
        font-size:10px;font-weight:700;color:#6d28d9;text-transform:uppercase;letter-spacing:.5px;
    }
    .new-field-box input{
        flex:1;padding:.55rem .8rem;border:1px solid #c4b5fd;border-radius:8px;
        font-size:13px;outline:none;
    }
    .new-field-box .btn-close-inline{
        padding:.55rem .8rem;background:none;border:1px solid #c4b5fd;border-radius:8px;
        color:#6d28d9;cursor:pointer;font-size:13px;
    }
    .new-field-box .hint{ font-size:11px;color:#7c3aed;margin-top:.4rem; }

    /* ── Cuentas bancarias (columna derecha) ── */
    .bank-account-row{
        display:flex;gap:.6rem;margin-bottom:.6rem;align-items:flex-end;
        background:#fff;padding:.8rem;border-radius:8px;border:1px solid #e2e8f0;
        transition:all .2s;
    }
    .bank-account-row:hover{ border-color:#cbd5e1; background:#fafbfc; }
    .bank-account-row .form-field{ flex:1; margin:0; min-width:0; }
    .bank-account-row .form-field label{
        font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;
        letter-spacing:.3px;display:block;margin-bottom:2px;
    }
    .bank-account-row .form-field select,
    .bank-account-row .form-field input{
        width:100%;padding:.45rem .7rem;border:1px solid #e2e8f0;border-radius:6px;
        font-size:13px;background:#fff;transition:all .2s;
    }
    .bank-account-row .form-field select:focus,
    .bank-account-row .form-field input:focus{
        border-color:#6366f1;outline:none;box-shadow:0 0 0 3px rgba(99,102,241,.1);
    }
    .bank-account-row .btn-remove{
        padding:.45rem .7rem;background:#fee2e2;border:1px solid #fecaca;border-radius:6px;
        color:#991b1b;cursor:pointer;font-size:13px;flex-shrink:0;transition:all .2s;
        height:38px;display:flex;align-items:center;justify-content:center;
    }
    .bank-account-row .btn-remove:hover{ background:#fecaca;border-color:#f87171; }

    .btn-add-bank{
        padding:.5rem 1rem;background:transparent;border:1px dashed #94a3b8;border-radius:8px;
        color:#475569;cursor:pointer;font-size:13px;width:100%;transition:all .2s;
        display:flex;align-items:center;justify-content:center;gap:6px;
    }
    .btn-add-bank:hover{ border-color:#6366f1;color:#6366f1;background:#f8fafc; }

    .new-bank-container{ margin-top:.8rem;padding-top:.8rem;border-top:1px dashed #e2e8f0; }
    .new-bank-form{ display:none;margin-top:.6rem;background:#ede9fe;border-radius:8px;padding:.8rem; }
    .new-bank-form.active{ display:block; }
    .new-bank-form .form-grid{ display:grid;grid-template-columns:1fr 1fr;gap:.6rem; }
    .new-bank-form .form-field{ margin:0; }
    .new-bank-form .form-field label{
        font-size:10px;font-weight:700;color:#6d28d9;text-transform:uppercase;letter-spacing:.3px;
        display:block;margin-bottom:2px;
    }
    .new-bank-form .form-field input,
    .new-bank-form .form-field select{
        width:100%;padding:.45rem .7rem;border:1px solid #c4b5fd;border-radius:6px;
        font-size:13px;background:#fff;transition:all .2s;
    }
    .new-bank-form .form-field input:focus,
    .new-bank-form .form-field select:focus{
        border-color:#7c3aed;outline:none;box-shadow:0 0 0 3px rgba(124,58,237,.1);
    }
    .new-bank-form .form-actions{ margin-top:.5rem;display:flex;gap:.5rem; }
    .new-bank-form .btn-close-bank{
        padding:.2rem .6rem;background:none;border:1px solid #c4b5fd;border-radius:6px;
        color:#6d28d9;cursor:pointer;font-size:11px;transition:all .2s;
    }
    .new-bank-form .btn-close-bank:hover{ background:#ede9fe; }

    .btn-toggle-bank{
        padding:.4rem .8rem;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:8px;
        color:#6366f1;cursor:pointer;font-size:12px;transition:all .2s;
        display:inline-flex;align-items:center;gap:4px;
    }
    .btn-toggle-bank:hover{ background:#e2e8f0;border-color:#cbd5e1; }

    .bank-counter{
        background:#e2e8f0;color:#475569;font-size:9px;padding:0 6px;border-radius:10px;
        font-weight:700;margin-left:4px;
    }

    @media (max-width: 640px){
        .bank-account-row{ flex-wrap:wrap; }
        .bank-account-row .form-field{ flex:1 1 100%;min-width:100%; }
        .bank-account-row .btn-remove{ flex:1;height:38px; }
        .new-bank-form .form-grid{ grid-template-columns:1fr; }
    }
</style>

<form action="{{ route('admin.suppliers.store') }}" method="POST" id="form-supplier">
    @csrf

    <div class="edit-supplier-layout">

        {{-- ══════════ IZQUIERDA: DATOS DEL PROVEEDOR ══════════ --}}
        <div class="edit-supplier-left">
            <div class="card" style="margin-bottom:1rem">
                <div class="card-header">
                    <div>
                        <div class="card-title">Datos del proveedor</div>
                        <div class="card-sub">Puedes crear destino o categoría al vuelo si no existen</div>
                    </div>
                </div>

                <div class="form-field" style="margin-bottom:1.1rem">
                    <label>Nombre del proveedor *</label>
                    <input type="text" name="supplier_name"
                           value="{{ old('supplier_name') }}"
                           placeholder="Nombre del proveedor"
                           maxlength="100" required autofocus>
                </div>

                <div class="form-field" style="margin-bottom:1.1rem">
                    <label>Razón Social</label>
                    <input type="text" name="business_name"
                           value="{{ old('business_name') }}"
                           placeholder="Razón social de la empresa"
                           maxlength="150">
                </div>

                <div class="form-field" style="margin-bottom:1.1rem">
                    <label>Código Tributario</label>
                    <input type="text" name="tax_code"
                           value="{{ old('tax_code') }}"
                           placeholder="Ej: RUC, NIT, VAT..."
                           maxlength="20">
                </div>

                <div class="form-field" style="margin-bottom:1.1rem">
                    <label>Email Empresarial</label>
                    <input type="email" name="general_email"
                           value="{{ old('general_email') }}"
                           placeholder="proveedor@empresa.com"
                           maxlength="120">
                </div>

                <div class="form-field">
                    <label>Teléfono Empresarial</label>
                    <input type="text" name="general_phone"
                           value="{{ old('general_phone') }}"
                           placeholder="+51 987 654 321"
                           maxlength="20">
                </div>
            </div>

            {{-- DESTINO --}}
            <div class="inline-create-block">
                <p class="block-label"><i class="ti ti-map-pin" style="font-size:14px"></i> Destino</p>
                <div style="display:flex;align-items:flex-end;gap:.6rem">
                    <div class="form-field" style="flex:1;margin:0">
                        <label>Seleccionar destino existente</label>
                        <select name="id_destinations" id="sel-destination">
                            <option value="">— Sin destino —</option>
                            @foreach($destinations as $d)
                                <option value="{{ $d->id_destinations }}"
                                    {{ old('id_destinations') == $d->id_destinations ? 'selected' : '' }}>
                                    {{ $d->destination_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" class="btn-inline-new" onclick="toggleNew('destination')">
                        <i class="ti ti-plus" style="font-size:13px"></i> Nuevo
                    </button>
                </div>

                <div id="new-destination" class="new-field-box">
                    <div class="inner">
                        <label>Nombre del nuevo destino</label>
                        <div style="display:flex;gap:.5rem;margin-top:.4rem">
                            <input type="text" name="new_destination_name" id="new-destination-input"
                                   placeholder="Ej: Lima, Cusco, Cancún...">
                            <button type="button" class="btn-close-inline" onclick="toggleNew('destination')">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                        <p class="hint"><i class="ti ti-info-circle" style="font-size:12px"></i> Se creará automáticamente al guardar el proveedor</p>
                    </div>
                </div>
            </div>

            {{-- CATEGORÍA --}}
            <div class="inline-create-block" style="margin-bottom:0">
                <p class="block-label"><i class="ti ti-tag" style="font-size:14px"></i> Categoría</p>
                <div style="display:flex;align-items:flex-end;gap:.6rem">
                    <div class="form-field" style="flex:1;margin:0">
                        <label>Seleccionar categoría existente</label>
                        <select name="id_categories_suppliers" id="sel-category">
                            <option value="">— Sin categoría —</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id_categories_suppliers }}"
                                    {{ old('id_categories_suppliers') == $c->id_categories_suppliers ? 'selected' : '' }}>
                                    {{ $c->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" class="btn-inline-new" onclick="toggleNew('category')">
                        <i class="ti ti-plus" style="font-size:13px"></i> Nueva
                    </button>
                </div>

                <div id="new-category" class="new-field-box">
                    <div class="inner" style="background:#fef3c7">
                        <label style="color:#92400e">Nombre de la nueva categoría</label>
                        <div style="display:flex;gap:.5rem;margin-top:.4rem">
                            <input type="text" name="new_category_name" id="new-category-input"
                                   placeholder="Ej: Hoteles, Aerolíneas, Transporte..."
                                   style="border-color:#fde68a">
                            <button type="button" class="btn-close-inline" onclick="toggleNew('category')"
                                    style="border-color:#fde68a;color:#92400e">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                        <p class="hint" style="color:#b45309"><i class="ti ti-info-circle" style="font-size:12px"></i> Se creará automáticamente al guardar el proveedor</p>
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:.8rem;margin-top:1rem">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-plus" style="font-size:14px"></i> Crear proveedor
                </button>
                <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>

        {{-- ══════════ DERECHA: CUENTAS BANCARIAS ══════════ --}}
        <div class="edit-supplier-right">
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Cuentas bancarias</div>
                        <div class="card-sub">
                            <span id="bank-counter-label">1 cuenta(s) agregada(s)</span>
                            <span class="bank-counter" id="bank-counter">1</span>
                        </div>
                    </div>
                    <button type="button" class="btn-add-bank" style="width:auto;border:none;background:#10b981;color:#fff" onclick="addBankAccount()">
                        <i class="ti ti-plus" style="font-size:14px"></i> Agregar cuenta
                    </button>
                </div>

                <div id="bank-accounts-container">
                    {{-- Primera fila de cuenta bancaria por defecto --}}
                    <div class="bank-account-row" data-index="0">
                        <div class="form-field">
                            <label>Banco *</label>
                            <select name="bank_accounts[0][id_bank]" class="bank-select">
                                <option value="">Seleccionar banco</option>
                                @foreach($banks as $b)
                                    <option value="{{ $b->id_bank }}" {{ old('bank_accounts.0.id_bank') == $b->id_bank ? 'selected' : '' }}>
                                        {{ $b->bank_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-field">
                            <label>Número de cuenta *</label>
                            <input type="text" name="bank_accounts[0][account_number]"
                                   value="{{ old('bank_accounts.0.account_number') }}"
                                   placeholder="Número de cuenta" maxlength="100">
                        </div>
                        <div class="form-field">
                            <label>CCI</label>
                            <input type="text" name="bank_accounts[0][cci]"
                                   value="{{ old('bank_accounts.0.cci') }}"
                                   placeholder="CCI" maxlength="100">
                        </div>
                        <div class="form-field" style="flex:0.7">
                            <label>Moneda</label>
                            <select name="bank_accounts[0][currency]">
                                <option value="">—</option>
                                <option value="PEN" {{ old('bank_accounts.0.currency') == 'PEN' ? 'selected' : '' }}>PEN</option>
                                <option value="USD" {{ old('bank_accounts.0.currency') == 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="EUR" {{ old('bank_accounts.0.currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                            </select>
                        </div>
                        <button type="button" class="btn-remove" onclick="removeBankAccount(this)" style="display:none">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                </div>

                {{-- Nuevo banco --}}
                <div class="new-bank-container">
                    <button type="button" class="btn-toggle-bank" onclick="toggleNewBank()">
                        <i class="ti ti-plus"></i> Registrar nuevo banco
                    </button>

                    <div class="new-bank-form" id="new-bank-form">
                        <div class="form-grid">
                            <div class="form-field">
                                <label>Nombre del banco *</label>
                                <input type="text" name="new_bank_name"
                                       value="{{ old('new_bank_name') }}"
                                       placeholder="Ej: BBVA, Interbank...">
                            </div>
                            <div class="form-field">
                                <label>Número de cuenta *</label>
                                <input type="text" name="new_bank_account_number"
                                       value="{{ old('new_bank_account_number') }}"
                                       placeholder="Número de cuenta">
                            </div>
                            <div class="form-field">
                                <label>CCI</label>
                                <input type="text" name="new_bank_cci"
                                       value="{{ old('new_bank_cci') }}"
                                       placeholder="CCI">
                            </div>
                            <div class="form-field">
                                <label>Moneda</label>
                                <select name="new_bank_currency">
                                    <option value="">—</option>
                                    <option value="PEN" {{ old('new_bank_currency') == 'PEN' ? 'selected' : '' }}>PEN</option>
                                    <option value="USD" {{ old('new_bank_currency') == 'USD' ? 'selected' : '' }}>USD</option>
                                    <option value="EUR" {{ old('new_bank_currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-actions">
                            <p style="font-size:11px;color:#7c3aed;margin:0;flex:1">
                                <i class="ti ti-info-circle" style="font-size:12px"></i>
                                El banco y la cuenta se crearán automáticamente al guardar
                            </p>
                            <button type="button" class="btn-close-bank" onclick="toggleNewBank()">
                                <i class="ti ti-x"></i> Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>

@push('scripts')
<script>
let accountIndex = 1;

function addBankAccount() {
    const container = document.getElementById('bank-accounts-container');
    const row = document.createElement('div');
    row.className = 'bank-account-row';
    row.dataset.index = accountIndex;
    row.innerHTML = `
        <div class="form-field">
            <label>Banco *</label>
            <select name="bank_accounts[${accountIndex}][id_bank]" class="bank-select">
                <option value="">Seleccionar banco</option>
                @foreach($banks as $b)
                    <option value="{{ $b->id_bank }}">{{ $b->bank_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-field">
            <label>Número de cuenta *</label>
            <input type="text" name="bank_accounts[${accountIndex}][account_number]"
                   placeholder="Número de cuenta" maxlength="100">
        </div>
        <div class="form-field">
            <label>CCI</label>
            <input type="text" name="bank_accounts[${accountIndex}][cci]"
                   placeholder="CCI" maxlength="100">
        </div>
        <div class="form-field" style="flex:0.7">
            <label>Moneda</label>
            <select name="bank_accounts[${accountIndex}][currency]">
                <option value="">—</option>
                <option value="PEN">PEN</option>
                <option value="USD">USD</option>
                <option value="EUR">EUR</option>
            </select>
        </div>
        <button type="button" class="btn-remove" onclick="removeBankAccount(this)">
            <i class="ti ti-trash"></i>
        </button>
    `;
    container.appendChild(row);
    accountIndex++;
    updateBankCounter();
}

function removeBankAccount(button) {
    const container = document.getElementById('bank-accounts-container');
    if (container.children.length > 1) {
        button.closest('.bank-account-row').remove();
        updateBankCounter();
    } else {
        alert('Debe haber al menos una cuenta bancaria. Puedes usar "Registrar nuevo banco" para crear una cuenta con un banco nuevo.');
    }
}

function updateBankCounter() {
    const container = document.getElementById('bank-accounts-container');
    const count = container.children.length;
    document.getElementById('bank-counter').textContent = count;
    document.getElementById('bank-counter-label').textContent = count + ' cuenta(s) agregada(s)';
}

function toggleNewBank() {
    document.getElementById('new-bank-form').classList.toggle('active');
}

// Funciones existentes para destino y categoría
function toggleNew(type) {
    const box   = document.getElementById('new-' + type);
    const sel   = document.getElementById('sel-' + type);
    const input = document.getElementById('new-' + type + '-input');
    const open  = box.style.display === 'none' || box.style.display === '';

    box.style.display = open ? 'block' : 'none';

    if (open) {
        sel.value = '';
        sel.disabled = true;
        if (input) input.focus();
    } else {
        sel.disabled = false;
        if (input) input.value = '';
    }
}
</script>
@endpush
@endsection
