@extends('layouts.app')
@section('title', 'Editar Proveedor')
@section('content')

<div class="page-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.4rem">
    <div>
        <div class="page-title">Editar Proveedor</div>
        <div class="page-sub">Modifica los datos del proveedor y sus cuentas bancarias</div>
    </div>
    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary btn-sm">
        <i class="ti ti-arrow-left" style="font-size:13px"></i> Volver
    </a>
</div>

@if($errors->any())
    <div class="alert alert-error" style="margin-bottom:1rem;padding:1rem;border-radius:10px;background:#fef2f2;border:1px solid #fecaca;color:#991b1b">
        <i class="ti ti-alert-circle"></i>
        <ul style="list-style:none;margin-left:.5rem;padding:0">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error" style="margin-bottom:1rem;padding:1rem;border-radius:10px;background:#fef2f2;border:1px solid #fecaca;color:#991b1b">
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

    /* ── Bloques tipo "destino / categoría" ── */
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
    .bank-account-row .btn-remove.delete-marked{
        background:#fecaca;border-color:#f87171;opacity:.7;
    }

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
    .bank-deleted-badge{
        font-size:9px;color:#991b1b;background:#fee2e2;padding:1px 6px;border-radius:3px;
        margin-left:4px;font-weight:600;
    }

    /* ── COMBOS BUSCABLES (País / Ciudad) ── */
    .combo-wrap{ position:relative; }
    .combo-input{
        width:100%; padding:.55rem .7rem; border:1px solid #e2e8f0; border-radius:6px;
        font-size:13px; color:#0f172a; outline:none; transition:border-color .15s, background .15s;
        background:#fff; box-sizing:border-box;
    }
    .combo-input:focus{ border-color:#6366f1; background:#fff; box-shadow:0 0 0 3px rgba(99,102,241,0.08); }
    .combo-input[disabled]{ background:#f8fafc; color:#94a3b8; cursor:not-allowed; }
    .combo-list{
        position:absolute; top:calc(100% + 4px); left:0; right:0;
        background:#fff; border:1px solid #e2e8f0; border-radius:8px;
        max-height:200px; overflow-y:auto; z-index:50;
        box-shadow:0 8px 20px -4px rgba(0,0,0,.12);
        display:none;
    }
    .combo-list.show{ display:block; }
    .combo-item{
        padding:.5rem .7rem; font-size:13px; color:#0f172a; cursor:pointer;
    }
    .combo-item:hover, .combo-item.active{ background:#eef2ff; color:#4338ca; }
    .combo-empty{ padding:.5rem .7rem; font-size:12px; color:#94a3b8; }
    .combo-clear{
        position:absolute; right:.6rem; top:50%; transform:translateY(-50%);
        background:none; border:none; color:#cbd5e1; cursor:pointer; font-size:14px;
        display:none; padding:2px; line-height:1;
    }
    .combo-clear.show{ display:block; }
    .combo-clear:hover{ color:#ef4444; }

    .form-grid-2{ display:grid; grid-template-columns:1fr 1fr; gap:.8rem; }
    @media (max-width: 640px){ .form-grid-2{ grid-template-columns:1fr; } }

    /* ── Contactos (estilo tabla con inputs) ── */
    .contact-section {
        background:#fff;
        border:1px solid #e2e8f0;
        border-radius:10px;
        padding:1.2rem;
        margin-bottom:1rem;
    }
    .contact-section .section-header {
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:0.8rem;
    }
    .contact-section .section-title {
        font-size:14px;
        font-weight:700;
        color:#0f172a;
        display:flex;
        align-items:center;
        gap:6px;
    }
    .contact-section .section-title i {
        color:#6366f1;
        font-size:16px;
    }
    .add-contact-btn {
        padding:.45rem 1rem;
        background:#6366f1;
        border:none;
        border-radius:8px;
        font-size:12px;
        font-weight:600;
        color:#fff;
        cursor:pointer;
        display:inline-flex;
        align-items:center;
        gap:6px;
        transition:all .2s;
    }
    .add-contact-btn:hover {
        background:#4f46e5;
        transform:translateY(-1px);
        box-shadow:0 2px 8px rgba(99,102,241,0.3);
    }
    .contact-counter {
        background:#e2e8f0;
        color:#475569;
        font-size:9px;
        padding:0 8px;
        border-radius:10px;
        font-weight:700;
        margin-left:4px;
    }
    .contacts-table-wrap {
        overflow-x:auto;
        margin-top:0.3rem;
    }
    .contacts-table {
        width:100%;
        border-collapse:collapse;
        font-size:12px;
    }
    .contacts-table thead {
        background:#f1f5f9;
    }
    .contacts-table thead th {
        padding:.5rem .6rem;
        text-align:left;
        font-size:9px;
        font-weight:700;
        color:#64748b;
        text-transform:uppercase;
        letter-spacing:0.3px;
        border-bottom:1px solid #e2e8f0;
        white-space:nowrap;
    }
    .contacts-table tbody tr {
        border-bottom:1px solid #f1f5f9;
        transition:background .2s;
    }
    .contacts-table tbody tr:hover {
        background:#fafbfc;
    }
    .contacts-table tbody td {
        padding:.3rem .4rem;
        vertical-align:middle;
    }
    .contacts-table tbody td input[type="text"],
    .contacts-table tbody td input[type="email"] {
        width:100%;
        padding:.4rem .5rem;
        border:1px solid #e2e8f0;
        border-radius:4px;
        font-size:12px;
        background:#fff;
        transition:all .2s;
        min-width:80px;
    }
    .contacts-table tbody td input:focus {
        border-color:#6366f1;
        outline:none;
        box-shadow:0 0 0 2px rgba(99,102,241,0.1);
    }
    .contacts-table .col-principal {
        text-align:center;
        width:40px;
    }
    .contacts-table .col-name {
        min-width:120px;
    }
    .contacts-table .col-actions {
        text-align:center;
        width:60px;
    }
    .star-toggle {
        width:18px;
        height:18px;
        accent-color:#f59e0b;
        cursor:pointer;
    }
    .row-del-btn {
        background:transparent;
        border:none;
        color:#cbd5e1;
        cursor:pointer;
        font-size:16px;
        padding:4px;
        border-radius:4px;
        transition:all .2s;
    }
    .row-del-btn:hover {
        color:#ef4444;
        background:#fef2f2;
    }
    .row-del-btn.marked {
        color:#ef4444;
        background:#fef2f2;
        opacity:0.7;
    }
    .table-empty-note {
        text-align:center;
        padding:1.5rem;
        color:#94a3b8;
        font-size:13px;
    }
    .table-empty-note i {
        font-size:28px;
        display:block;
        margin-bottom:.3rem;
        color:#cbd5e1;
    }

    @media (max-width: 640px){
        .bank-account-row{ flex-wrap:wrap; }
        .bank-account-row .form-field{ flex:1 1 100%;min-width:100%; }
        .bank-account-row .btn-remove{ flex:1;height:38px; }
        .new-bank-form .form-grid{ grid-template-columns:1fr; }
        .contacts-table thead th,
        .contacts-table tbody td { padding:.2rem .3rem; font-size:11px; }
        .contacts-table tbody td input { font-size:11px; padding:.3rem .4rem; min-width:60px; }
    }
</style>

<form action="{{ route('admin.suppliers.update', $supplier->id_supplier) }}" method="POST" id="form-supplier">
    @csrf @method('PUT')

{{-- Campos ocultos para contactos a eliminar --}}
<div id="delete-contacts-inputs"></div>
    {{-- Campos ocultos para cuentas a eliminar --}}
    <div id="delete-inputs"></div>

    <div class="edit-supplier-layout">

        {{-- ══════════ IZQUIERDA: DATOS DEL PROVEEDOR ══════════ --}}
        <div class="edit-supplier-left">
            <div class="card" style="margin-bottom:1rem">
                <div class="card-header">
                    <div>
                        <div class="card-title">{{ $supplier->supplier_name }}</div>
                        <div class="card-sub">ID #{{ $supplier->id_supplier }}</div>
                    </div>
                </div>

                <div class="form-field" style="margin-bottom:1.1rem">
                    <label>Nombre del proveedor *</label>
                    <input type="text" name="supplier_name"
                           value="{{ old('supplier_name', $supplier->supplier_name) }}"
                           maxlength="100" required autofocus>
                </div>

                <div class="form-field" style="margin-bottom:1.1rem">
                    <label>Razón Social</label>
                    <input type="text" name="business_name"
                           value="{{ old('business_name', $supplier->business_name) }}"
                           placeholder="Razón social de la empresa"
                           maxlength="150">
                </div>

                <div class="form-field" style="margin-bottom:1.1rem">
                    <label>Código Tributario</label>
                    <input type="text" name="tax_code"
                           value="{{ old('tax_code', $supplier->tax_code) }}"
                           placeholder="Ej: RUC, NIT, VAT..."
                           maxlength="20">
                </div>

                <div class="form-field" style="margin-bottom:1.1rem">
                    <label>Email Empresarial</label>
                    <input type="email" name="general_email"
                           value="{{ old('general_email', $supplier->general_email) }}"
                           placeholder="proveedor@empresa.com"
                           maxlength="120">
                </div>

                <div class="form-field" style="margin-bottom:1.1rem">
                    <label>Teléfono Empresarial</label>
                    <input type="text" name="general_phone"
                           value="{{ old('general_phone', $supplier->general_phone) }}"
                           placeholder="+51 987 654 321"
                           maxlength="20">
                </div>
            </div>

            {{-- ══════════ UBICACIÓN ══════════ --}}
            <div class="card" style="margin-bottom:1rem">
                <div class="card-header">
                    <div>
                        <div class="card-title"><i class="ti ti-map-pin" style="font-size:16px;color:#6366f1"></i> Ubicación</div>
                        <div class="card-sub">Escribe para buscar el país y la ciudad</div>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-field">
                        <label>País</label>
                        <div class="combo-wrap" id="combo-pais">
                            <input type="text" class="combo-input" id="edit-pais-input"
                                   placeholder="Cargando países..." autocomplete="off" disabled>
                            <button type="button" class="combo-clear" id="edit-pais-clear" tabindex="-1">
                                <i class="ti ti-x"></i>
                            </button>
                            <div class="combo-list" id="edit-pais-list"></div>
                        </div>
                        <input type="hidden" name="country_name" id="edit-country-name" value="{{ old('country_name', $supplier->country_name) }}">
                        <input type="hidden" id="edit-country-code" value="{{ old('country_code', '') }}">
                    </div>

                    <div class="form-field">
                        <label>Ciudad</label>
                        <div class="combo-wrap" id="combo-ciudad">
                            <input type="text" class="combo-input" id="edit-ciudad-input"
                                   placeholder="Seleccione país primero" autocomplete="off" disabled>
                            <button type="button" class="combo-clear" id="edit-ciudad-clear" tabindex="-1">
                                <i class="ti ti-x"></i>
                            </button>
                            <div class="combo-list" id="edit-ciudad-list"></div>
                        </div>
                        <input type="hidden" name="city_name" id="edit-ciudad-name" value="{{ old('city_name', $supplier->city_name) }}">
                    </div>
                </div>

                <div class="form-field" style="margin-top:.8rem">
                    <label>Dirección</label>
                    <input type="text" name="address" value="{{ old('address', $supplier->address) }}"
                           placeholder="Ej: Avenida Suecia, calle 124, primera casa">
                </div>
            </div>

            <div class="inline-create-block" style="margin-bottom:0">
                <p class="block-label"><i class="ti ti-tag" style="font-size:14px"></i> Categoría</p>
                <div style="display:flex;align-items:flex-end;gap:.6rem">
                    <div class="form-field" style="flex:1;margin:0">
                        <label>Seleccionar categoría existente</label>
                        <select name="id_categories_suppliers" id="sel-category">
                            <option value="">— Sin categoría —</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id_categories_suppliers }}"
                                    {{ old('id_categories_suppliers', $supplier->id_categories_suppliers) == $c->id_categories_suppliers ? 'selected' : '' }}>
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
                        <p class="hint" style="color:#b45309"><i class="ti ti-info-circle" style="font-size:12px"></i> Se creará automáticamente al guardar</p>
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:.8rem;margin-top:1rem">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy" style="font-size:14px"></i> Guardar cambios
                </button>
                <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>

        {{-- ══════════ DERECHA: CUENTAS BANCARIAS Y CONTACTOS ══════════ --}}
        <div class="edit-supplier-right">
            {{-- CUENTAS BANCARIAS --}}
            <div class="card" style="margin-bottom:1rem">
                <div class="card-header">
                    <div>
                        <div class="card-title">Cuentas bancarias</div>
                        <div class="card-sub">
                            <span id="bank-counter-label">{{ $supplier->bankAccounts->count() }} cuenta(s) registrada(s)</span>
                            <span class="bank-counter" id="bank-counter">{{ $supplier->bankAccounts->count() }}</span>
                        </div>
                    </div>
                    <button type="button" class="btn-add-bank" style="width:auto;border:none;background:#10b981;color:#fff" onclick="addBankAccount()">
                        <i class="ti ti-plus" style="font-size:14px"></i> Agregar cuenta
                    </button>
                </div>

                <div id="bank-accounts-container">
                    @php $index = 0; @endphp
                    @foreach($supplier->bankAccounts as $account)
                        <div class="bank-account-row" data-index="{{ $index }}" data-account-id="{{ $account->id_bank_account }}">
                            <input type="hidden" name="bank_accounts[{{ $index }}][id_bank_account]" value="{{ $account->id_bank_account }}">
                            <div class="form-field">
                                <label>Banco *</label>
                                <select name="bank_accounts[{{ $index }}][id_bank]" class="bank-select">
                                    <option value="">Seleccionar banco</option>
                                    @foreach($banks as $b)
                                        <option value="{{ $b->id_bank }}"
                                            {{ old("bank_accounts.{$index}.id_bank", $account->id_bank) == $b->id_bank ? 'selected' : '' }}>
                                            {{ $b->bank_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-field">
                                <label>Número de cuenta *</label>
                                <input type="text" name="bank_accounts[{{ $index }}][account_number]"
                                       value="{{ old("bank_accounts.{$index}.account_number", $account->account_number) }}"
                                       placeholder="Número de cuenta" maxlength="100">
                            </div>
                            <div class="form-field">
                                <label>CCI</label>
                                <input type="text" name="bank_accounts[{{ $index }}][cci]"
                                       value="{{ old("bank_accounts.{$index}.cci", $account->cci) }}"
                                       placeholder="CCI" maxlength="100">
                            </div>
                            <div class="form-field" style="flex:0.7">
                                <label>Moneda</label>
                                <select name="bank_accounts[{{ $index }}][currency]">
                                    <option value="">—</option>
                                    <option value="PEN" {{ old("bank_accounts.{$index}.currency", $account->currency) == 'PEN' ? 'selected' : '' }}>PEN</option>
                                    <option value="USD" {{ old("bank_accounts.{$index}.currency", $account->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                                    <option value="EUR" {{ old("bank_accounts.{$index}.currency", $account->currency) == 'EUR' ? 'selected' : '' }}>EUR</option>
                                </select>
                            </div>
                            <button type="button" class="btn-remove" onclick="deleteBankAccount(this, {{ $account->id_bank_account }})" title="Eliminar cuenta">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                        @php $index++; @endphp
                    @endforeach
                </div>

                <p class="table-empty-note" id="no-banks-note"
                   style="{{ $supplier->bankAccounts->isEmpty() ? '' : 'display:none' }};color:#94a3b8;font-size:12.5px;text-align:center;padding:1rem 0">
                    Este proveedor no tiene cuentas bancarias aún. Usa "Agregar cuenta" para crear una.
                </p>

                <button type="button" class="btn-add-bank" onclick="addBankAccount()" style="margin-top:.6rem">
                    <i class="ti ti-plus" style="font-size:13px"></i> Agregar otra cuenta bancaria
                </button>

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

            {{-- ══════════ CONTACTOS (ESTILO CLIENTES) ══════════ --}}
            <div class="contact-section">
                <div class="section-header">
                    <div>
                        <div class="section-title">
                            <i class="ti ti-users"></i> Contactos
                            <span class="contact-counter" id="contact-counter">{{ $supplier->contacts->count() }}</span>
                        </div>
                        <div class="card-sub" style="font-size:12px;color:#94a3b8;margin-top:2px">
                            {{ $supplier->contacts->count() }} contacto(s) registrado(s)
                        </div>
                    </div>
                    <button type="button" class="add-contact-btn" onclick="addNewContact()">
                        <i class="ti ti-plus" style="font-size:12px"></i> Añadir contacto
                    </button>
                </div>

                <div class="contacts-table-wrap">
                    <table class="contacts-table">
                        <thead>
                            <tr>
                                <th class="col-principal" title="Principal"><i class="ti ti-star"></i></th>
                                <th class="col-name">Nombre *</th>
                                <th>Apellidos</th>
                                <th>Email</th>
                                <th>Cargo</th>
                                <th>Teléfono 1</th>
                                <th>Teléfono 2</th>
                                <th class="col-actions"></th>
                            </tr>
                        </thead>
                        <tbody id="contacts-tbody">
                            @foreach($supplier->contacts as $idx => $contact)
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
                       style="{{ $supplier->contacts->isEmpty() ? '' : 'display:none' }}">
                        <i class="ti ti-user-off"></i>
                        Este proveedor no tiene contactos aún. Usa "Añadir contacto" para crear uno.
                    </p>
                </div>
            </div>
        </div>

    </div>
</form>

@push('scripts')
<script>
// ── COMBOS BUSCABLES (País / Ciudad) ──────────────────────────
function crearCombo({ inputId, listId, clearId, onSelect, onClear }) {
    const input = document.getElementById(inputId);
    const list  = document.getElementById(listId);
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

    function seleccionar(opt) {
        input.value = opt.label;
        clear.classList.add('show');
        cerrar();
        onSelect(opt);
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

// ── Instancias combos País → Ciudad ──────────────────────────
const comboPais = crearCombo({
    inputId: 'edit-pais-input',
    listId:  'edit-pais-list',
    clearId: 'edit-pais-clear',
    onSelect: (opt) => {
        document.getElementById('edit-country-name').value = opt.label;
        document.getElementById('edit-country-code').value = opt.value;
        cargarCiudades(opt.value);
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
    onClear: () => {
        document.getElementById('edit-ciudad-name').value = '';
    }
});

function cargarPaises() {
    fetch(`{{ url('api/geo/paises') }}`)
        .then(r => r.json())
        .then(paises => {
            const opciones = paises.map(p => ({ value: p.codigo, label: p.nombre }));
            comboPais.setOptions(opciones, 'Escribe para buscar país...');

            const countryName = document.getElementById('edit-country-name').value;
            if (countryName) {
                const pais = paises.find(p => p.nombre === countryName);
                if (pais) {
                    document.getElementById('edit-pais-input').value = pais.nombre;
                    document.getElementById('edit-country-code').value = pais.codigo;
                    cargarCiudades(pais.codigo);
                }
            }
        })
        .catch(() => comboPais.setOptions([], 'No se pudo cargar'));
}

function cargarCiudades(countryCode) {
    comboCiudad.disable('Cargando...');
    if (!countryCode) {
        comboCiudad.disable('Seleccione país primero');
        return;
    }
    fetch(`{{ url('api/geo/ciudades') }}?country=${countryCode}`)
        .then(r => r.json())
        .then(ciudades => {
            const opciones = ciudades.map(c => ({ value: c.nombre, label: c.nombre, geoNameId: c.geoNameId }));
            comboCiudad.setOptions(opciones, 'Escribe para buscar ciudad...');

            const cityName = document.getElementById('edit-ciudad-name').value;
            if (cityName) {
                const ciudad = ciudades.find(c => c.nombre === cityName);
                if (ciudad) {
                    document.getElementById('edit-ciudad-input').value = ciudad.nombre;
                }
            }
        })
        .catch(() => comboCiudad.setOptions([], 'No se pudo cargar'));
}

cargarPaises();

// ── Banco ──
let accountIndex = {{ $supplier->bankAccounts->count() }};
const toDeleteBanks = new Set();

document.addEventListener('DOMContentLoaded', function() {
    updateBankCounter();
});

function addBankAccount() {
    document.getElementById('no-banks-note').style.display = 'none';
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
        <button type="button" class="btn-remove" onclick="removeNewBankAccount(this)" title="Quitar fila">
            <i class="ti ti-x"></i>
        </button>
    `;
    container.appendChild(row);
    accountIndex++;
    updateBankCounter();
}

function removeNewBankAccount(button) {
    button.closest('.bank-account-row').remove();
    updateBankCounter();
}

function deleteBankAccount(button, accountId) {
    const row = button.closest('.bank-account-row');

    if (toDeleteBanks.has(accountId)) {
        toDeleteBanks.delete(accountId);
        row.style.opacity = '';
        row.style.pointerEvents = '';
        button.innerHTML = '<i class="ti ti-trash"></i>';
        button.className = 'btn-remove';
        button.title = 'Eliminar cuenta';
        row.querySelectorAll('input, select').forEach(input => {
            if (input.type !== 'hidden') input.disabled = false;
        });
    } else {
        if (!confirm('¿Estás seguro de eliminar esta cuenta bancaria al guardar?')) return;
        toDeleteBanks.add(accountId);
        row.style.opacity = '0.5';
        row.style.pointerEvents = 'none';
        button.innerHTML = '<i class="ti ti-rotate"></i>';
        button.className = 'btn-remove delete-marked';
        button.title = 'Clic para deshacer';
        row.querySelectorAll('input, select').forEach(input => {
            if (input.type !== 'hidden') input.disabled = true;
        });
    }
    syncDeleteInputs();
    updateBankCounter();
}

function syncDeleteInputs() {
    const container = document.getElementById('delete-inputs');
    container.innerHTML = '';
    toDeleteBanks.forEach(id => {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = 'delete_bank_accounts[]';
        input.value = id;
        container.appendChild(input);
    });
}

function updateBankCounter() {
    const container = document.getElementById('bank-accounts-container');
    const allRows = container.querySelectorAll('.bank-account-row');
    const activeCount = Array.from(allRows).filter(row => {
        const id = row.dataset.accountId;
        return !id || !toDeleteBanks.has(parseInt(id));
    }).length;
    document.getElementById('bank-counter').textContent = activeCount;
    document.getElementById('bank-counter-label').textContent = activeCount + ' cuenta(s) registrada(s)';
}

function toggleNewBank() {
    document.getElementById('new-bank-form').classList.toggle('active');
}

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

// ── CONTACTOS (ESTILO CLIENTES) ──────────────────────────────
let contactCounter = {{ $supplier->contacts->count() }};
const toDeleteContacts = new Set();

function addNewContact() {
    const tbody = document.getElementById('contacts-tbody');
    const noNote = document.getElementById('no-contacts-note');
    noNote.style.display = 'none';

    const tr = document.createElement('tr');
    tr.className = 'contact-row';
    tr.id = 'new-row-' + Date.now();
    tr.innerHTML = `
        <td class="col-principal">
            <input type="checkbox" class="star-toggle principal-checkbox"
                   name="contacts[${contactCounter}][es_principal]" value="1"
                   title="Marcar como principal">
        </td>
        <td class="col-name">
            <input type="text" name="contacts[${contactCounter}][name]"
                   placeholder="Nombre" required>
        </td>
        <td>
            <input type="text" name="contacts[${contactCounter}][last_names]"
                   placeholder="Apellidos">
        </td>
        <td>
            <input type="email" name="contacts[${contactCounter}][email]"
                   placeholder="correo@ejemplo.com">
        </td>
        <td>
            <input type="text" name="contacts[${contactCounter}][qualification]"
                   placeholder="Ej: Gerente">
        </td>
        <td>
            <input type="text" name="contacts[${contactCounter}][first_phone]"
                   placeholder="Principal">
        </td>
        <td>
            <input type="text" name="contacts[${contactCounter}][second_phone]"
                   placeholder="Opcional">
        </td>
        <td class="col-actions">
            <button type="button" class="row-del-btn"
                    onclick="this.closest('tr').remove(); updateContactCounter();"
                    title="Quitar fila">
                <i class="ti ti-x"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    contactCounter++;
    updateContactCounter();
    // Focus en el primer input
    tr.querySelector('input[name*="[name]"]').focus();
}

function markDelete(contactId, button) {
    const row = button.closest('tr');

    if (toDeleteContacts.has(contactId)) {
        // Deshacer eliminación
        toDeleteContacts.delete(contactId);
        row.style.opacity = '';
        row.style.pointerEvents = '';
        button.className = 'row-del-btn';
        button.title = 'Eliminar contacto';
        row.querySelectorAll('input').forEach(input => input.disabled = false);
    } else {
        if (!confirm('¿Estás seguro de eliminar este contacto?')) return;
        toDeleteContacts.add(contactId);
        row.style.opacity = '0.5';
        row.style.pointerEvents = 'none';
        button.className = 'row-del-btn marked';
        button.title = 'Clic para deshacer';
        row.querySelectorAll('input').forEach(input => input.disabled = true);
    }
    syncDeleteContactsInputs();
    updateContactCounter();
}

function syncDeleteContactsInputs() {
    const container = document.getElementById('delete-contacts-inputs');
    container.innerHTML = '';
    toDeleteContacts.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete_contacts[]';
        input.value = id;
        container.appendChild(input);
    });
}

function updateContactCounter() {
    const tbody = document.getElementById('contacts-tbody');
    const visibleRows = Array.from(tbody.querySelectorAll('tr.contact-row')).filter(row => {
        // Verificar si la fila no está marcada para eliminar
        const id = row.id.replace('existing-row-', '');
        if (id && id !== row.id && toDeleteContacts.has(parseInt(id))) {
            return false;
        }
        return true;
    });
    const count = visibleRows.length;
    document.getElementById('contact-counter').textContent = count;

    // Mostrar/ocultar mensaje de vacío
    const noNote = document.getElementById('no-contacts-note');
    if (count === 0 && !tbody.querySelector('tr.contact-row')) {
        noNote.style.display = 'block';
    } else {
        noNote.style.display = 'none';
    }
}

// Gestionar "Principal" (solo uno puede estar marcado)
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('principal-checkbox') && e.target.checked) {
        document.querySelectorAll('.principal-checkbox').forEach(cb => {
            if (cb !== e.target) cb.checked = false;
        });
    }
});

// Inicializar contador
document.addEventListener('DOMContentLoaded', function() {
    // Asegurar que solo un principal esté marcado
    const checkedPrincipals = document.querySelectorAll('.principal-checkbox:checked');
    if (checkedPrincipals.length > 1) {
        checkedPrincipals.forEach((cb, index) => {
            if (index > 0) cb.checked = false;
        });
    }
    updateContactCounter();
});
</script>
@endpush
@endsection
