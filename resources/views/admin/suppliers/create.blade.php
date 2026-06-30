@extends('layouts.app')
@section('title', 'Nuevo Proveedor')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Nuevo Proveedor</div>
        <div class="page-sub">Registra un nuevo proveedor en el sistema</div>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary btn-sm">
            <i class="ti ti-arrow-left" style="font-size:13px"></i> Volver
        </a>
        <button type="submit" form="form-supplier" class="btn-primary btn-sm">
            <i class="ti ti-plus"></i> Guardar proveedor
        </button>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-error" style="margin-bottom:1.5rem;padding:1rem;border-radius:10px;background:#fef2f2;border:1px solid #fecaca;color:#991b1b">
        <i class="ti ti-alert-circle"></i>
        <ul style="list-style:none;margin-left:.5rem;padding:0">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error" style="margin-bottom:1.5rem;padding:1rem;border-radius:10px;background:#fef2f2;border:1px solid #fecaca;color:#991b1b">
        <i class="ti ti-alert-circle"></i>
        {{ session('error') }}
    </div>
@endif

<style>
    /* ── Header ── */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.4rem;
    }
    .page-header .page-title {
        font-size: 22px;
        font-weight: 700;
        color: #0f172a;
    }
    .page-header .page-sub {
        font-size: 13px;
        color: #64748b;
        margin-top: 3px;
    }
    .page-header-actions {
        display:flex;
        align-items:center;
        gap:0.75rem;
        flex-shrink:0;
    }
    .page-header-actions .btn-sm {
        padding:8px 18px !important;
        font-size:13px !important;
        border-radius:8px !important;
    }
    .page-header-actions .btn-primary.btn-sm:hover {
        transform:translateY(-1px);
    }

    .edit-supplier-layout{
        display:grid;
        grid-template-columns: 560px 1fr;
        gap:2rem;
        align-items:start;
    }
    @media (max-width: 1024px){
        .edit-supplier-layout{ grid-template-columns:1fr; }
        .edit-supplier-left{ position:static !important; }
    }
    .edit-supplier-left{
        position:sticky;
        top:1rem;
    }

    /* ── Cards mejoradas ── */
    .card-modern {
        background: rgb(255, 255, 255);
        border-radius:16px;
        border:1px solid #e9edf2;
        padding:1.5rem;
        margin-bottom:1.5rem;
        box-shadow:0 1px 3px rgba(0,0,0,0.04);
        transition:box-shadow .2s;
    }
    .card-modern:hover {
        box-shadow:0 4px 12px rgba(0,0,0,0.06);
    }

    .card-modern .card-header-custom {
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:1.5rem;
        padding-bottom:0.75rem;
        border-bottom:2px solid #f1f5f9;
    }
    .card-modern .card-title-custom {
        font-size:16px;
        font-weight:700;
        color:#0f172a;
        display:flex;
        align-items:center;
        gap:8px;
    }
    .card-modern .card-title-custom i {
        color:#6366f1;
        font-size:18px;
    }
    .card-modern .card-sub-custom {
        font-size:13px;
        color:#94a3b8;
        margin-top:2px;
    }

    /* ── Campos de formulario mejorados ── */
    .field-group {
        margin-bottom:1.25rem;
    }
    .field-group:last-child {
        margin-bottom:0;
    }
    .field-group label {
        display:block;
        font-size:12px;
        font-weight:700;
        color:#475569;
        text-transform:uppercase;
        letter-spacing:0.4px;
        margin-bottom:6px;
    }
    .field-group label .req {
        color:#ef4444;
        margin-left:2px;
    }
    .field-group input,
    .field-group select {
        width:100%;
        padding:0.7rem 0.9rem;
        border:1.5px solid #e2e8f0;
        border-radius:10px;
        font-size:14px;
        background:#fafbfc;
        transition:all .2s;
        color:#0f172a;
    }
    .field-group input:focus,
    .field-group select:focus {
        border-color:#6366f1;
        outline:none;
        background:#ffffff;
        box-shadow:0 0 0 4px rgba(99,102,241,0.08);
    }
    .field-group input::placeholder {
        color:#94a3b8;
    }

    /* ── Bloques inline (categoría) ── */
    .inline-create-block {
        background:#f8fafc;
        border:1.5px solid #e9edf2;
        border-radius:12px;
        padding:1.25rem;
        margin-bottom:1.5rem;
    }
    .inline-create-block .block-label {
        font-size:11px;
        font-weight:700;
        color:#94a3b8;
        text-transform:uppercase;
        letter-spacing:.6px;
        margin-bottom:0.8rem;
        display:flex;
        align-items:center;
        gap:8px;
    }
    .inline-create-block .block-label i {
        font-size:15px;
        color:#6366f1;
    }
    .inline-create-block .inline-row {
        display:flex;
        align-items:flex-end;
        gap:0.75rem;
    }
    .inline-create-block .inline-row .field-group {
        flex:1;
        margin-bottom:0;
    }
    .btn-inline-new {
        padding:0.65rem 1.2rem;
        background:#fff;
        border:1.5px solid #e2e8f0;
        border-radius:10px;
        font-size:12px;
        font-weight:600;
        color:#6366f1;
        cursor:pointer;
        white-space:nowrap;
        flex-shrink:0;
        transition:all .2s;
        display:flex;
        align-items:center;
        gap:6px;
    }
    .btn-inline-new:hover {
        border-color:#6366f1;
        background:#eef2ff;
    }

    .new-field-box {
        display:none;
        margin-top:0.8rem;
    }
    .new-field-box .inner {
        background:#ede9fe;
        border-radius:10px;
        padding:1rem;
    }
    .new-field-box .inner label {
        font-size:10px;
        font-weight:700;
        color:#6d28d9;
        text-transform:uppercase;
        letter-spacing:.5px;
        display:block;
        margin-bottom:6px;
    }
    .new-field-box .inner .input-row {
        display:flex;
        gap:0.6rem;
    }
    .new-field-box .inner .input-row input {
        flex:1;
        padding:0.6rem 0.9rem;
        border:1.5px solid #c4b5fd;
        border-radius:8px;
        font-size:13px;
        outline:none;
        background:#fff;
    }
    .new-field-box .inner .input-row input:focus {
        border-color:#7c3aed;
        box-shadow:0 0 0 4px rgba(124,58,237,0.1);
    }
    .new-field-box .btn-close-inline {
        padding:0.55rem 0.9rem;
        background:none;
        border:1.5px solid #c4b5fd;
        border-radius:8px;
        color:#6d28d9;
        cursor:pointer;
        font-size:13px;
        transition:all .2s;
    }
    .new-field-box .btn-close-inline:hover {
        background:#ede9fe;
    }
    .new-field-box .hint {
        font-size:11px;
        color:#7c3aed;
        margin-top:0.6rem;
        display:flex;
        align-items:center;
        gap:5px;
    }

    /* ── Botones ── */
    .btn-primary {
        background:#6366f1;
        color:#fff;
        border:none;
        padding:0.75rem 2rem;
        border-radius:10px;
        font-weight:600;
        font-size:14px;
        cursor:pointer;
        transition:all .2s;
        display:flex;
        align-items:center;
        gap:8px;
    }
    .btn-primary:hover {
        background:#4f46e5;
        transform:translateY(-2px);
        box-shadow:0 4px 16px rgba(99,102,241,0.3);
    }
    .btn-secondary {
        background:#f1f5f9;
        color:#475569;
        border:none;
        padding:0.75rem 2rem;
        border-radius:10px;
        font-weight:600;
        font-size:14px;
        cursor:pointer;
        transition:all .2s;
        text-decoration:none;
        display:inline-flex;
        align-items:center;
        gap:6px;
    }
    .btn-secondary:hover {
        background:#e2e8f0;
        color:#0f172a;
    }

    /* ── Cuentas bancarias ── */
    .bank-account-row {
        display:flex;
        gap:0.75rem;
        margin-bottom:0.75rem;
        align-items:flex-end;
        background:#fafbfc;
        padding:1rem;
        border-radius:12px;
        border:1.5px solid #e9edf2;
        transition:all .2s;
    }
    .bank-account-row:hover {
        border-color:#cbd5e1;
        background:#f8fafc;
    }
    .bank-account-row .field-group {
        flex:1;
        margin:0;
        min-width:0;
    }
    .bank-account-row .field-group label {
        font-size:10px;
        font-weight:700;
        color:#94a3b8;
        text-transform:uppercase;
        letter-spacing:.4px;
        margin-bottom:4px;
    }
    .bank-account-row .field-group input,
    .bank-account-row .field-group select {
        padding:0.5rem 0.7rem;
        border:1.5px solid #e2e8f0;
        border-radius:8px;
        font-size:13px;
        background:#fff;
    }
    .bank-account-row .field-group input:focus,
    .bank-account-row .field-group select:focus {
        border-color:#6366f1;
        box-shadow:0 0 0 3px rgba(99,102,241,0.08);
    }
    .bank-account-row .btn-remove {
        padding:0.5rem 0.7rem;
        background:#fee2e2;
        border:1.5px solid #fecaca;
        border-radius:8px;
        color:#991b1b;
        cursor:pointer;
        font-size:13px;
        flex-shrink:0;
        transition:all .2s;
        height:40px;
        display:flex;
        align-items:center;
        justify-content:center;
        width:40px;
    }
    .bank-account-row .btn-remove:hover {
        background:#fecaca;
        border-color:#f87171;
    }

    .btn-add-bank {
        padding:0.6rem 1.2rem;
        background:transparent;
        border:1.5px dashed #94a3b8;
        border-radius:10px;
        color:#475569;
        cursor:pointer;
        font-size:13px;
        width:100%;
        transition:all .2s;
        display:flex;
        align-items:center;
        justify-content:center;
        gap:8px;
        margin-top:0.5rem;
    }
    .btn-add-bank:hover {
        border-color:#6366f1;
        color:#6366f1;
        background:#f8fafc;
    }

    .new-bank-container {
        margin-top:1rem;
        padding-top:1rem;
        border-top:1.5px dashed #e2e8f0;
    }
    .new-bank-form {
        margin-top:0.8rem;
        background:#ede9fe;
        border-radius:12px;
        padding:1rem;
        position:relative;
    }
    .new-bank-form .form-grid {
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:0.8rem;
    }
    .new-bank-form .field-group {
        margin:0;
    }
    .new-bank-form .field-group label {
        font-size:10px;
        font-weight:700;
        color:#6d28d9;
        text-transform:uppercase;
        letter-spacing:.4px;
        display:block;
        margin-bottom:4px;
    }
    .new-bank-form .field-group input,
    .new-bank-form .field-group select {
        width:100%;
        padding:0.5rem 0.7rem;
        border:1.5px solid #c4b5fd;
        border-radius:8px;
        font-size:13px;
        background:#fff;
        transition:all .2s;
    }
    .new-bank-form .field-group input:focus,
    .new-bank-form .field-group select:focus {
        border-color:#7c3aed;
        outline:none;
        box-shadow:0 0 0 4px rgba(124,58,237,0.08);
    }
    .new-bank-form .form-actions {
        margin-top:0.8rem;
        display:flex;
        gap:0.8rem;
        align-items:center;
    }
    .new-bank-form .btn-close-bank {
        padding:0.4rem 0.9rem;
        background:none;
        border:1.5px solid #c4b5fd;
        border-radius:8px;
        color:#6d28d9;
        cursor:pointer;
        font-size:12px;
        transition:all .2s;
    }
    .new-bank-form .btn-close-bank:hover {
        background:#ede9fe;
    }
    .new-bank-form .new-bank-badge {
        position:absolute;
        top:-9px;
        left:14px;
        background:#7c3aed;
        color:#fff;
        font-size:10px;
        font-weight:700;
        padding:2px 10px;
        border-radius:10px;
        letter-spacing:.4px;
        text-transform:uppercase;
    }

    .btn-toggle-bank {
        padding:0.5rem 1rem;
        background:#f1f5f9;
        border:1.5px solid #e2e8f0;
        border-radius:10px;
        color:#6366f1;
        cursor:pointer;
        font-size:12px;
        transition:all .2s;
        display:inline-flex;
        align-items:center;
        gap:6px;
        font-weight:600;
    }
    .btn-toggle-bank:hover {
        background:#e2e8f0;
        border-color:#cbd5e1;
    }

    .bank-counter {
        background:#e2e8f0;
        color:#475569;
        font-size:9px;
        padding:2px 8px;
        border-radius:12px;
        font-weight:700;
        margin-left:6px;
    }

    @media (max-width: 640px) {
        .bank-account-row {
            flex-wrap:wrap;
        }
        .bank-account-row .field-group {
            flex:1 1 100%;
            min-width:100%;
        }
        .bank-account-row .btn-remove {
            flex:1;
            height:40px;
            width:100%;
        }
        .new-bank-form .form-grid {
            grid-template-columns:1fr;
        }
        .inline-create-block .inline-row {
            flex-wrap:wrap;
        }
        .inline-create-block .inline-row .field-group {
            flex:1 1 100%;
        }
        .page-header {
            flex-wrap:wrap;
            gap:0.8rem;
        }
        .page-header-actions {
            width:100%;
        }
        .page-header-actions .btn-sm {
            flex:1;
            justify-content:center;
        }
    }

    /* ── Contactos - TABLA ── */
    .contact-section {
        background:#f8fafc;
        border:1.5px solid #e9edf2;
        border-radius:16px;
        padding:1.5rem;
        margin-bottom:1.5rem;
    }
    .contact-section .section-header {
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:1rem;
    }
    .contact-section .section-title {
        font-size:16px;
        font-weight:700;
        color:#0f172a;
        display:flex;
        align-items:center;
        gap:8px;
    }
    .contact-section .section-title i {
        color:#6366f1;
        font-size:18px;
    }
    .contact-section .section-hint {
        font-size:13px;
        color:#94a3b8;
        margin-top:2px;
    }
    .add-contact-btn {
        padding:0.5rem 1.2rem;
        background:#6366f1;
        border:none;
        border-radius:10px;
        font-size:12px;
        font-weight:600;
        color:#fff;
        cursor:pointer;
        display:inline-flex;
        align-items:center;
        gap:6px;
        white-space:nowrap;
        transition:all .2s;
        box-shadow:0 2px 8px rgba(99,102,241,0.2);
    }
    .add-contact-btn:hover {
        background:#4f46e5;
        transform:translateY(-2px);
        box-shadow:0 4px 16px rgba(99,102,241,0.3);
    }

    .contact-counter {
        background:#e2e8f0;
        color:#475569;
        font-size:10px;
        padding:2px 10px;
        border-radius:12px;
        font-weight:700;
        margin-left:8px;
    }

    .contact-table-wrapper {
        overflow-x:auto;
        margin-top:0.5rem;
    }
    .contact-table {
        width:100%;
        border-collapse:collapse;
        font-size:13px;
    }
    .contact-table thead {
        background:#f1f5f9;
        border-radius:8px;
    }
    .contact-table thead th {
        padding:0.7rem 0.8rem;
        text-align:left;
        font-size:10px;
        font-weight:700;
        color:#64748b;
        text-transform:uppercase;
        letter-spacing:0.5px;
        border-bottom:2px solid #e2e8f0;
        white-space:nowrap;
    }
    .contact-table tbody tr {
        border-bottom:1px solid #f1f5f9;
        transition:background .2s;
    }
    .contact-table tbody tr:hover {
        background:#f8fafc;
    }
    .contact-table tbody td {
        padding:0.6rem 0.8rem;
        color:#334155;
        vertical-align:middle;
    }
    .contact-table .badge-principal {
        font-size:9px;
        font-weight:700;
        color:#b45309;
        background:#fef3c7;
        padding:2px 10px;
        border-radius:10px;
        display:inline-block;
    }
    .contact-table .btn-action {
        background:transparent;
        border:1.5px solid #e2e8f0;
        border-radius:6px;
        width:28px;
        height:28px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        cursor:pointer;
        font-size:13px;
        transition:all .2s;
        margin:0 2px;
    }
    .contact-table .btn-action:hover {
        transform:scale(1.05);
    }
    .contact-table .btn-edit-contact {
        color:#6366f1;
        border-color:#c7d2fe;
    }
    .contact-table .btn-edit-contact:hover {
        background:#eef2ff;
        border-color:#6366f1;
    }
    .contact-table .btn-remove-contact {
        color:#991b1b;
        border-color:#fecaca;
    }
    .contact-table .btn-remove-contact:hover {
        background:#fee2e2;
        border-color:#f87171;
    }

    .contact-table .empty-message {
        text-align:center;
        padding:2rem;
        color:#94a3b8;
        font-size:14px;
    }
    .contact-table .empty-message i {
        font-size:32px;
        display:block;
        margin-bottom:0.5rem;
        color:#cbd5e1;
    }

    .contact-table .action-cell {
        display:flex;
        gap:4px;
        align-items:center;
    }

    @media (max-width: 640px) {
        .contact-table thead th,
        .contact-table tbody td {
            padding:0.5rem;
            font-size:12px;
        }
        .contact-table .badge-principal {
            font-size:8px;
            padding:2px 6px;
        }
        .contact-table .action-cell {
            flex-direction:column;
            gap:4px;
        }
    }

    /* ── Modal mejorado ── */
    .modal-overlay {
        position:fixed;
        top:0;
        left:0;
        width:100%;
        height:100%;
        background:rgba(15,23,42,0.6);
        display:none;
        justify-content:center;
        align-items:center;
        z-index:9999;
        backdrop-filter:blur(6px);
    }
    .modal-overlay.active {
        display:flex;
    }
    .modal-box {
        background:#fff;
        border-radius:20px;
        padding:2rem 2.5rem;
        max-width:580px;
        width:92%;
        max-height:90vh;
        overflow-y:auto;
        box-shadow:0 24px 64px rgba(0,0,0,0.25);
        animation:modalFade .3s ease;
    }
    @keyframes modalFade {
        from {
            opacity:0;
            transform:scale(0.96) translateY(12px);
        }
        to {
            opacity:1;
            transform:scale(1) translateY(0);
        }
    }
    .modal-header {
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:1.5rem;
        padding-bottom:1rem;
        border-bottom:2px solid #f1f5f9;
    }
    .modal-title {
        font-size:20px;
        font-weight:700;
        color:#0f172a;
        display:flex;
        align-items:center;
        gap:10px;
    }
    .modal-title i {
        color:#6366f1;
        font-size:22px;
    }
    .modal-close {
        background:#f1f5f9;
        border:none;
        border-radius:50%;
        width:40px;
        height:40px;
        display:flex;
        align-items:center;
        justify-content:center;
        cursor:pointer;
        font-size:20px;
        color:#475569;
        transition:all .2s;
    }
    .modal-close:hover {
        background:#e2e8f0;
        color:#0f172a;
        transform:rotate(90deg);
    }
    .modal-body .field-group {
        margin-bottom:1.2rem;
    }
    .modal-body .field-group label {
        font-size:12px;
        font-weight:700;
        color:#475569;
        display:block;
        margin-bottom:5px;
    }
    .modal-body .field-group label .req {
        color:#ef4444;
    }
    .modal-body .field-group input,
    .modal-body .field-group select {
        width:100%;
        padding:0.7rem 0.9rem;
        border:1.5px solid #e2e8f0;
        border-radius:10px;
        font-size:14px;
        transition:all .2s;
    }
    .modal-body .field-group input:focus,
    .modal-body .field-group select:focus {
        border-color:#6366f1;
        outline:none;
        box-shadow:0 0 0 4px rgba(99,102,241,0.08);
    }
    .modal-body .field-group.phone-grid {
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:0.8rem;
    }
    .modal-footer {
        display:flex;
        justify-content:flex-end;
        gap:1rem;
        margin-top:1.5rem;
        padding-top:1.2rem;
        border-top:2px solid #f1f5f9;
    }
    .modal-footer .btn {
        padding:0.7rem 2rem;
        border-radius:10px;
        font-weight:600;
        font-size:13px;
        cursor:pointer;
        border:none;
        transition:all .2s;
        display:inline-flex;
        align-items:center;
        gap:6px;
    }
    .modal-footer .btn-secondary {
        background:#f1f5f9;
        color:#475569;
    }
    .modal-footer .btn-secondary:hover {
        background:#e2e8f0;
        color:#0f172a;
    }
    .modal-footer .btn-primary {
        background:#6366f1;
        color:#fff;
    }
    .modal-footer .btn-primary:hover {
        background:#4f46e5;
        transform:translateY(-2px);
        box-shadow:0 4px 16px rgba(99,102,241,0.3);
    }

    @media (max-width: 480px) {
        .modal-box {
            padding:1.5rem;
        }
        .modal-body .field-group.phone-grid {
            grid-template-columns:1fr;
        }
    }

    /* ── COMBOS BUSCABLES (País / Ciudad) ── */
    .combo-wrap{ position:relative; }
    .combo-input{
        width:100%; padding:.62rem .8rem; border:1.5px solid #e2e8f0; border-radius:10px;
        font-size:14px; color:#0f172a; outline:none; transition:border-color .15s, background .15s;
        background:#fafbfc; box-sizing:border-box;
    }
    .combo-input:focus{ border-color:#6366f1; background:#fff; box-shadow:0 0 0 4px rgba(99,102,241,0.08); }
    .combo-input[disabled]{ background:#f8fafc; color:#94a3b8; cursor:not-allowed; }
    .combo-list{
        position:absolute; top:calc(100% + 4px); left:0; right:0;
        background:#fff; border:1.5px solid #e2e8f0; border-radius:10px;
        max-height:220px; overflow-y:auto; z-index:50;
        box-shadow:0 10px 25px -5px rgba(0,0,0,.1);
        display:none;
    }
    .combo-list.show{ display:block; }
    .combo-item{
        padding:.55rem .8rem; font-size:13px; color:#0f172a; cursor:pointer;
    }
    .combo-item:hover, .combo-item.active{ background:#eef2ff; color:#4338ca; }
    .combo-empty{ padding:.6rem .8rem; font-size:12.5px; color:#94a3b8; }
    .combo-clear{
        position:absolute; right:.6rem; top:50%; transform:translateY(-50%);
        background:none; border:none; color:#cbd5e1; cursor:pointer; font-size:14px;
        display:none; padding:2px; line-height:1;
    }
    .combo-clear.show{ display:block; }
    .combo-clear:hover{ color:#ef4444; }

    .form-grid-2{ display:grid; grid-template-columns:1fr 1fr; gap:.9rem; }
    @media (max-width: 640px){ .form-grid-2{ grid-template-columns:1fr; } }
</style>

<form action="{{ route('admin.suppliers.store') }}" method="POST" id="form-supplier">
    @csrf
    <div class="edit-supplier-layout">

        {{-- ══════════ COLUMNA IZQUIERDA ══════════ --}}
        <div class="edit-supplier-left">

            {{-- Datos del proveedor --}}
            <div class="card-modern">
                <div class="card-header-custom">
                    <div>
                        <div class="card-title-custom">
                            <i class="ti ti-building-store"></i> Datos del proveedor
                        </div>
                        <div class="card-sub-custom">Ingresa la información principal del proveedor</div>
                    </div>
                </div>

                <div class="field-group">
                    <label>Nombre Comercial <span class="req">*</span></label>
                    <input type="text" name="supplier_name"
                           value="{{ old('supplier_name') }}"
                           placeholder="Ej: Agencia de Viajes Andina"
                           maxlength="100" required autofocus>
                </div>

                <div class="field-group">
                    <label>Razón Social</label>
                    <input type="text" name="business_name"
                           value="{{ old('business_name') }}"
                           placeholder="Razón social de la empresa"
                           maxlength="150">
                </div>

                <div class="field-group">
                    <label>Código Tributario</label>
                    <input type="text" name="tax_code"
                           value="{{ old('tax_code') }}"
                           placeholder="Ej: RUC, NIT, VAT..."
                           maxlength="20">
                </div>

                <div class="field-group">
                    <label>Email Empresarial</label>
                    <input type="email" name="general_email"
                           value="{{ old('general_email') }}"
                           placeholder="proveedor@empresa.com"
                           maxlength="120">
                </div>

                <div class="field-group">
                    <label>Teléfono Empresarial</label>
                    <input type="text" name="general_phone"
                           value="{{ old('general_phone') }}"
                           placeholder="+51 987 654 321"
                           maxlength="20">
                </div>
            </div>

            {{-- ══════════ UBICACIÓN ══════════ --}}
            <div class="card-modern">
                <div class="card-header-custom">
                    <div>
                        <div class="card-title-custom">
                            <i class="ti ti-map-pin"></i> Ubicación
                        </div>
                        <div class="card-sub-custom">Escribe para buscar el país y la ciudad</div>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="field-group">
                        <label>País</label>
                        <div class="combo-wrap" id="combo-pais">
                            <input type="text" class="combo-input" id="create-pais-input"
                                   placeholder="Cargando países..." autocomplete="off" disabled>
                            <button type="button" class="combo-clear" id="create-pais-clear" tabindex="-1">
                                <i class="ti ti-x"></i>
                            </button>
                            <div class="combo-list" id="create-pais-list"></div>
                        </div>
                        <input type="hidden" name="country_name" id="create-country-name">
                        <input type="hidden" id="create-country-code">
                    </div>

                    <div class="field-group">
                        <label>Ciudad</label>
                        <div class="combo-wrap" id="combo-ciudad">
                            <input type="text" class="combo-input" id="create-ciudad-input"
                                   placeholder="Seleccione país primero" autocomplete="off" disabled>
                            <button type="button" class="combo-clear" id="create-ciudad-clear" tabindex="-1">
                                <i class="ti ti-x"></i>
                            </button>
                            <div class="combo-list" id="create-ciudad-list"></div>
                        </div>
                        <input type="hidden" name="city_name" id="create-ciudad-name">
                    </div>
                </div>

                <div class="field-group" style="margin-top:.9rem">
                    <label>Dirección</label>
                    <input type="text" name="address" value="{{ old('address') }}"
                           placeholder="Ej: Avenida Suecia, calle 124, primera casa">
                </div>
            </div>

            {{-- Categoría --}}
            <div class="inline-create-block">
                <div class="block-label">
                    <i class="ti ti-tag"></i> Categoría del proveedor
                </div>
                <div class="inline-row">
                    <div class="field-group">
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
                        <i class="ti ti-plus"></i> Nueva
                    </button>
                </div>

                <div id="new-category" class="new-field-box">
                    <div class="inner" style="background:#fef3c7">
                        <label style="color:#92400e"><i class="ti ti-plus"></i> Nueva categoría</label>
                        <div class="input-row">
                            <input type="text" name="new_category_name" id="new-category-input"
                                   placeholder="Ej: Hoteles, Aerolíneas, Transporte..."
                                   style="border-color:#fde68a">
                            <button type="button" class="btn-close-inline" onclick="toggleNew('category')"
                                    style="border-color:#fde68a;color:#92400e">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                        <div class="hint" style="color:#b45309">
                            <i class="ti ti-info-circle"></i> Se creará automáticamente al guardar
                        </div>
                    </div>
                </div>
            </div>


        </div>

        {{-- ══════════ COLUMNA DERECHA ══════════ --}}
        <div class="edit-supplier-right">
            {{-- Cuentas bancarias --}}
            <div class="card-modern">
                <div class="card-header-custom">
                    <div>
                        <div class="card-title-custom">
                            <i class="ti ti-credit-card"></i> Cuentas bancarias
                        </div>
                        <div class="card-sub-custom">
                            <span id="bank-counter-label">1 cuenta(s) agregada(s)</span>
                            <span class="bank-counter" id="bank-counter">1</span>
                        </div>
                    </div>
                </div>

                <div class="card-sub-custom" style="margin-bottom:0.8rem">
                    Usa <strong>"Banco"</strong> si el banco ya existe en el sistema. Si es un banco nuevo,
                    usa <strong>"Registrar nuevo banco"</strong> abajo: puedes agregar todos los que necesites
                    antes de guardar, sin tener que guardar y volver a editar.
                </div>

                <div id="bank-accounts-container">
                    {{-- Primera cuenta por defecto (banco ya existente) --}}
                    <div class="bank-account-row" data-index="0">
                        <div class="field-group">
                            <label>Banco</label>
                            <select name="bank_accounts[0][id_bank]" class="bank-select">
                                <option value="">Seleccionar banco</option>
                                @foreach($banks as $b)
                                    <option value="{{ $b->id_bank }}" {{ old('bank_accounts.0.id_bank') == $b->id_bank ? 'selected' : '' }}>
                                        {{ $b->bank_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field-group">
                            <label>Número de cuenta</label>
                            <input type="text" name="bank_accounts[0][account_number]"
                                   value="{{ old('bank_accounts.0.account_number') }}"
                                   placeholder="Número de cuenta" maxlength="100">
                        </div>
                        <div class="field-group">
                            <label>CCI</label>
                            <input type="text" name="bank_accounts[0][cci]"
                                   value="{{ old('bank_accounts.0.cci') }}"
                                   placeholder="CCI" maxlength="100">
                        </div>
                        <div class="field-group" style="flex:0.7">
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

                <button type="button" class="btn-add-bank" onclick="addBankAccount()">
                    <i class="ti ti-plus"></i> Agregar cuenta con banco existente
                </button>

                {{-- Nuevos bancos (repetible) --}}
                <div class="new-bank-container">
                    <button type="button" class="btn-toggle-bank" onclick="addNewBankEntry()">
                        <i class="ti ti-plus"></i> Registrar nuevo banco
                    </button>

                    <div id="new-banks-list">
                        @if(old('new_banks'))
                            @foreach(old('new_banks') as $idx => $nb)
                                <div class="new-bank-form" data-new-bank-index="{{ $idx }}">
                                    <span class="new-bank-badge">Banco nuevo</span>
                                    <div class="form-grid">
                                        <div class="field-group">
                                            <label>Nombre del banco <span class="req">*</span></label>
                                            <input type="text" name="new_banks[{{ $idx }}][bank_name]"
                                                   value="{{ $nb['bank_name'] ?? '' }}"
                                                   placeholder="Ej: BBVA, Interbank...">
                                        </div>
                                        <div class="field-group">
                                            <label>Número de cuenta <span class="req">*</span></label>
                                            <input type="text" name="new_banks[{{ $idx }}][account_number]"
                                                   value="{{ $nb['account_number'] ?? '' }}"
                                                   placeholder="Número de cuenta">
                                        </div>
                                        <div class="field-group">
                                            <label>CCI</label>
                                            <input type="text" name="new_banks[{{ $idx }}][cci]"
                                                   value="{{ $nb['cci'] ?? '' }}"
                                                   placeholder="CCI">
                                        </div>
                                        <div class="field-group">
                                            <label>Moneda</label>
                                            <select name="new_banks[{{ $idx }}][currency]">
                                                <option value="">—</option>
                                                <option value="PEN" {{ ($nb['currency'] ?? '') == 'PEN' ? 'selected' : '' }}>PEN</option>
                                                <option value="USD" {{ ($nb['currency'] ?? '') == 'USD' ? 'selected' : '' }}>USD</option>
                                                <option value="EUR" {{ ($nb['currency'] ?? '') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <span style="font-size:12px;color:#7c3aed;flex:1;display:flex;align-items:center;gap:6px">
                                            <i class="ti ti-info-circle"></i> Banco y cuenta se crearán automáticamente
                                        </span>
                                        <button type="button" class="btn-close-bank" onclick="removeNewBankEntry({{ $idx }})">
                                            <i class="ti ti-x"></i> Quitar
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            {{-- Contactos - TABLA --}}
            <div class="contact-section">
                <div class="section-header">
                    <div>
                        <div class="section-title">
                            <i class="ti ti-users"></i> Contactos
                            <span class="contact-counter" id="contact-counter">0</span>
                        </div>
                        <div class="section-hint">Los contactos se agregarán a la tabla</div>
                    </div>
                    <button type="button" class="add-contact-btn" onclick="abrirModalContacto()">
                        <i class="ti ti-plus"></i> Añadir contacto
                    </button>
                </div>

                <div class="contact-table-wrapper">
                    <table class="contact-table">
                        <thead>
                            <tr>
                                <th style="width:40px">#</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th style="width:90px">Estado</th>
                                <th style="width:90px">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="contact-table-body">
                            <!-- Los contactos se agregan aquí dinámicamente -->
                        </tbody>
                    </table>
                    <div id="empty-contacts" class="empty-message">
                        <i class="ti ti-user-off"></i>
                        No hay contactos registrados
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- ══════════ MODAL PARA NUEVO/EDITAR CONTACTO ══════════ --}}
<div class="modal-overlay" id="modal-contacto">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title" id="modal-contact-title">
                <i class="ti ti-user-plus"></i>
                Nuevo Contacto
            </div>
            <button type="button" class="modal-close" onclick="cerrarModalContacto()">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="modal-contact-edit-id" value="">
            <div class="field-group">
                <label>Nombre <span class="req">*</span></label>
                <input type="text" id="modal-contact-name" placeholder="Nombre del contacto" required>
            </div>
            <div class="field-group">
                <label>Apellidos</label>
                <input type="text" id="modal-contact-lastnames" placeholder="Apellidos del contacto">
            </div>
            <div class="field-group">
                <label>Correo electrónico</label>
                <input type="email" id="modal-contact-email" placeholder="ejemplo@correo.com">
            </div>
            <div class="field-group">
                <label>Cargo / Puesto</label>
                <input type="text" id="modal-contact-qualification" placeholder="Ej: Gerente, Coordinador...">
            </div>
            <div class="field-group phone-grid">
                <div>
                    <label>Teléfono 1</label>
                    <input type="text" id="modal-contact-phone1" placeholder="Teléfono principal">
                </div>
                <div>
                    <label>Teléfono 2</label>
                    <input type="text" id="modal-contact-phone2" placeholder="Teléfono opcional">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="cerrarModalContacto()">
                <i class="ti ti-x"></i> Cancelar
            </button>
            <button type="button" class="btn btn-primary" id="modal-contact-save-btn" onclick="guardarContactoModal()">
                <i class="ti ti-check"></i> Agregar contacto
            </button>
        </div>
    </div>
</div>

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
    inputId: 'create-pais-input',
    listId:  'create-pais-list',
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
    listId:  'create-ciudad-list',
    clearId: 'create-ciudad-clear',
    onSelect: (opt) => {
        document.getElementById('create-ciudad-name').value = opt.label;
    },
    onClear: () => {
        document.getElementById('create-ciudad-name').value = '';
    }
});

function cargarPaises() {
    fetch(`{{ url('api/geo/paises') }}`)
        .then(r => r.json())
        .then(paises => {
            const opciones = paises.map(p => ({ value: p.codigo, label: p.nombre }));
            comboPais.setOptions(opciones, 'Escribe para buscar país...');
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
        })
        .catch(() => comboCiudad.setOptions([], 'No se pudo cargar'));
}

cargarPaises();

// ── Cuentas bancarias (banco ya existente) ──
let accountIndex = 1;
let contactos = [];
let editandoId = null;

function addBankAccount() {
    const container = document.getElementById('bank-accounts-container');
    const row = document.createElement('div');
    row.className = 'bank-account-row';
    row.dataset.index = accountIndex;
    row.innerHTML = `
        <div class="field-group">
            <label>Banco</label>
            <select name="bank_accounts[${accountIndex}][id_bank]" class="bank-select">
                <option value="">Seleccionar banco</option>
                @foreach($banks as $b)
                    <option value="{{ $b->id_bank }}">{{ $b->bank_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="field-group">
            <label>Número de cuenta</label>
            <input type="text" name="bank_accounts[${accountIndex}][account_number]"
                   placeholder="Número de cuenta" maxlength="100">
        </div>
        <div class="field-group">
            <label>CCI</label>
            <input type="text" name="bank_accounts[${accountIndex}][cci]"
                   placeholder="CCI" maxlength="100">
        </div>
        <div class="field-group" style="flex:0.7">
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
        alert('Debe haber al menos una fila de cuenta bancaria. Puedes dejarla vacía si no aplica, o usar "Registrar nuevo banco" para un banco que no existe todavía.');
    }
}

// ── Bancos nuevos (repetible, sin necesidad de guardar y editar) ──
let newBankIndex = {{ old('new_banks') ? count(old('new_banks')) : 0 }};

function addNewBankEntry() {
    const list = document.getElementById('new-banks-list');
    const idx = newBankIndex;
    const wrapper = document.createElement('div');
    wrapper.className = 'new-bank-form';
    wrapper.dataset.newBankIndex = idx;
    wrapper.innerHTML = `
        <span class="new-bank-badge">Banco nuevo</span>
        <div class="form-grid">
            <div class="field-group">
                <label>Nombre del banco <span class="req">*</span></label>
                <input type="text" name="new_banks[${idx}][bank_name]" placeholder="Ej: BBVA, Interbank...">
            </div>
            <div class="field-group">
                <label>Número de cuenta <span class="req">*</span></label>
                <input type="text" name="new_banks[${idx}][account_number]" placeholder="Número de cuenta">
            </div>
            <div class="field-group">
                <label>CCI</label>
                <input type="text" name="new_banks[${idx}][cci]" placeholder="CCI">
            </div>
            <div class="field-group">
                <label>Moneda</label>
                <select name="new_banks[${idx}][currency]">
                    <option value="">—</option>
                    <option value="PEN">PEN</option>
                    <option value="USD">USD</option>
                    <option value="EUR">EUR</option>
                </select>
            </div>
        </div>
        <div class="form-actions">
            <span style="font-size:12px;color:#7c3aed;flex:1;display:flex;align-items:center;gap:6px">
                <i class="ti ti-info-circle"></i> Banco y cuenta se crearán automáticamente
            </span>
            <button type="button" class="btn-close-bank" onclick="removeNewBankEntry(${idx})">
                <i class="ti ti-x"></i> Quitar
            </button>
        </div>
    `;
    list.appendChild(wrapper);
    newBankIndex++;
    updateBankCounter();

    const firstInput = wrapper.querySelector('input');
    if (firstInput) firstInput.focus();
}

function removeNewBankEntry(idx) {
    const el = document.querySelector(`#new-banks-list [data-new-bank-index="${idx}"]`);
    if (el) el.remove();
    updateBankCounter();
}

function updateBankCounter() {
    const existing = document.getElementById('bank-accounts-container').children.length;
    const nuevos = document.getElementById('new-banks-list').children.length;
    const total = existing + nuevos;
    document.getElementById('bank-counter').textContent = total;
    document.getElementById('bank-counter-label').textContent = total + ' cuenta(s) agregada(s)';
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

// ── CONTACTOS - TABLA ──
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

    contactos = contactos.filter(c => c.id !== id);

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

// ── Modal Contacto ──
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

// ── Cerrar modal con ESC ──
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModalContacto();
    }
});

// ── Cerrar modal haciendo clic fuera ──
document.getElementById('modal-contacto').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalContacto();
    }
});

// ════════════════════════════════════════════════════════════════
// ── ENVIAR FORMULARIO - Mapeo correcto de campos ──
// ════════════════════════════════════════════════════════════════
document.getElementById('form-supplier').addEventListener('submit', function(e) {
    // Eliminar inputs de contactos anteriores
    document.querySelectorAll('input[name^="contacts"]').forEach(el => el.remove());

    contactos.forEach((contacto, index) => {
        // Mapeo de campos: el nombre en el objeto → nombre esperado por el controlador
        const fields = [
            { key: 'name', value: contacto.name },
            { key: 'last_names', value: contacto.lastnames },
            { key: 'email', value: contacto.email },
            { key: 'qualification', value: contacto.qualification },
            { key: 'first_phone', value: contacto.phone1 },
            { key: 'second_phone', value: contacto.phone2 }
        ];

        fields.forEach(field => {
            if (field.value) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `contacts[${index}][${field.key}]`;
                input.value = field.value;
                this.appendChild(input);
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    updateBankCounter();
});
</script>
@endpush
@endsection
