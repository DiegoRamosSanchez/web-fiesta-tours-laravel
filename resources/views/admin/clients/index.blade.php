@extends('layouts.app')
@section('title', 'Clientes')

@push('styles')
<style>
.filter-bar {
    display: flex;
    align-items: center;
    gap: .6rem;
    flex-wrap: wrap;
    margin-bottom: 1rem;
    padding: .9rem 1.1rem;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
}
.filter-input {
    padding: .5rem .85rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
    color: #0f172a;
    background: #f8fafc;
    outline: none;
    transition: border-color .15s;
}
.filter-input:focus { border-color: #6366f1; background: #fff; }
.filter-sep { width: 1px; height: 24px; background: #e2e8f0; flex-shrink: 0; }

/* Bulk bar */
.bulk-bar {
    display: none;
    align-items: center;
    gap: .8rem;
    padding: .75rem 1.1rem;
    background: #0f172a;
    border-radius: 10px;
    margin-bottom: 1rem;
}
.bulk-bar.visible { display: flex; }
.bulk-count { font-size: 13px; font-weight: 600; color: #fff; }
.bulk-sep { color: rgba(255,255,255,.2); }

/* Checkbox */
.cb-wrap { display: flex; align-items: center; justify-content: center; }
input[type="checkbox"] {
    width: 15px; height: 15px;
    accent-color: #e63232;
    cursor: pointer;
}
tr.selected td { background: #fef2f2 !important; }

/* Contador de resultados */
.results-count {
    font-size: 12px;
    color: #94a3b8;
    margin-left: auto;
}

/* Modal styles */
.hidden { display: none !important; }

#modal-export {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    z-index: 1000;
    display: none;
    align-items: center;
    justify-content: center;
}

#modal-export.show {
    display: flex;
}

.modal-box {
    background: #fff;
    border-radius: 16px;
    width: 100%;
    max-width: 480px;
    max-height: 90vh;
    overflow-y: auto;
    padding: 2rem;
    position: relative;
    animation: modalFadeIn .2s ease-out;
}

@keyframes modalFadeIn {
    from { opacity: 0; transform: scale(0.95) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

.modal-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: none;
    border: none;
    font-size: 22px;
    cursor: pointer;
    color: #94a3b8;
    transition: color .15s;
}
.modal-close:hover { color: #0f172a; }

.export-option {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem 1.2rem;
    cursor: pointer;
    transition: all .2s;
    margin-bottom: .8rem;
    display: flex;
    align-items: center;
    gap: 12px;
}
.export-option:hover {
    border-color: #16a34a;
    background: #f0fdf4;
}
.export-option .icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.export-option .icon.green { background: #f0fdf4; color: #16a34a; }
.export-option .icon.blue { background: #eff6ff; color: #3b82f6; }
.export-option .title { font-size: 14px; font-weight: 600; color: #0f172a; }
.export-option .sub { font-size: 12px; color: #94a3b8; }
.export-option .arrow { color: #94a3b8; font-size: 18px; margin-left: auto; }

.export-by-id-wrapper {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem 1.2rem;
    transition: all .2s;
    margin-bottom: .8rem;
}
.export-by-id-wrapper:hover {
    border-color: #6366f1;
}
.export-by-id-wrapper .header {
    display: flex;
    align-items: center;
    gap: 12px;
}

.btn-cancel-export {
    width: 100%;
    padding: .6rem;
    background: #f1f5f9;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    transition: background .15s;
    margin-top: .4rem;
}
.btn-cancel-export:hover {
    background: #e2e8f0;
}

/* ══════════ MODAL CREAR CLIENTE (rediseñado) ══════════ */
.modal-overlay{
    position:fixed; inset:0; background:rgba(15,23,42,.55);
    z-index:999; display:flex; align-items:center; justify-content:center;
    padding:1.2rem;
}
.modal-crear-box{
    background:#fff; border-radius:18px; width:100%; max-width:760px;
    max-height:95vh; display:flex; flex-direction:column; overflow:hidden;
    animation:modalFadeIn .2s ease-out;
    box-shadow:0 25px 50px -12px rgba(0,0,0,.35);
}
.modal-crear-header{
    display:flex; align-items:flex-start; gap:14px;
    padding:1.5rem 1.8rem 1.2rem; border-bottom:1px solid #f1f5f9;
    flex-shrink:0; background:#fff; position:relative; z-index:2;
}
.modal-crear-header-icon{
    width:44px; height:44px; border-radius:12px; background:#eef2ff; color:#6366f1;
    display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0;
}
.modal-crear-header h2{ font-size:18px; font-weight:700; color:#0f172a; margin:0 0 2px; }
.modal-crear-header p{ font-size:12.5px; color:#94a3b8; margin:0; }
.modal-crear-header .modal-close{ position:static; margin-left:auto; flex-shrink:0; }

.modal-crear-body{
    padding:1.6rem 1.8rem;
    overflow-y:auto;
    flex:1;
    max-height: calc(95vh - 180px);
}

/* ── FIX: Scroll en el wrapper de contactos ── */
#contactos-wrapper {
    max-height: 380px;
    overflow-y: auto;
    padding-right: 6px;
    margin-right: -6px;
}

#contactos-wrapper::-webkit-scrollbar {
    width: 5px;
}
#contactos-wrapper::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}
#contactos-wrapper::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}
#contactos-wrapper::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.form-section{ margin-bottom:1.8rem; }
.form-section:last-child{ margin-bottom:0; }
.form-section-title{
    display:flex; align-items:center; gap:7px; font-size:12.5px; font-weight:700;
    color:#374151; text-transform:uppercase; letter-spacing:.4px; margin-bottom:.2rem;
}
.form-section-title i{ font-size:15px; color:#6366f1; }
.form-section-title-row{ display:flex; align-items:center; justify-content:space-between; margin-bottom:.2rem; flex-wrap:wrap; gap:.5rem; }
.form-section-hint{ font-size:11.5px; color:#94a3b8; margin:.1rem 0 .9rem; }
.form-section-divider{ border-top:1px solid #f1f5f9; margin:1.6rem 0; }

.field{ display:flex; flex-direction:column; gap:.35rem; }
.field label{
    font-size:10.5px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.4px;
}
.field label .req{ color:#ef4444; }
.field input, .field select{
    width:100%; padding:.62rem .8rem; border:1px solid #e2e8f0; border-radius:9px;
    font-size:13px; color:#0f172a; outline:none; transition:border-color .15s, background .15s;
    background:#fff; box-sizing:border-box;
}
.field input::placeholder{ color:#cbd5e1; }
.field input:focus, .field select:focus{ border-color:#6366f1; background:#fff; }

.form-grid-2{ display:grid; grid-template-columns:1fr 1fr; gap:.9rem; margin:1rem 0; }
.form-grid-2:first-of-type{ margin-top:0; }
.form-grid-3{ display:grid; grid-template-columns:1fr 1fr 1fr; gap:.9rem; }
@media (max-width:620px){
    .form-grid-2, .form-grid-3{ grid-template-columns:1fr; }
}

.add-contact-btn{
    display:inline-flex; align-items:center; gap:6px; padding:.45rem .85rem;
    background:#10b981; color:#fff; border:none; border-radius:8px;
    font-size:12.5px; font-weight:600; cursor:pointer; white-space:nowrap;
}
.add-contact-btn:hover{ background:#0d9b6c; }

.contact-card{
    border:1px solid #e2e8f0; border-radius:12px; padding:1.1rem 1.2rem;
    margin-bottom:.9rem; background:#fbfcfe;
}
.contact-card:last-child{ margin-bottom:0; }
.contact-card-head{
    display:flex; align-items:center; gap:10px; margin-bottom:1rem; flex-wrap:wrap;
}
.contact-badge{
    width:24px; height:24px; border-radius:50%; background:#e0e7ff; color:#4338ca;
    font-size:12px; font-weight:700; display:flex; align-items:center; justify-content:center;
    flex-shrink:0;
}
.contact-card-title{ font-size:12.5px; font-weight:700; color:#374151; }
.contact-principal-tag{
    font-size:10px; font-weight:700; color:#92400e; background:#fef3c7;
    border:1px solid #fde68a; padding:2px 8px; border-radius:999px;
    display:inline-flex; align-items:center; gap:3px;
}
.contact-remove-btn{
    margin-left:auto; background:none; border:none; cursor:pointer; color:#cbd5e1;
    font-size:15px; padding:.3rem; border-radius:6px;
}
.contact-remove-btn:hover{ color:#e63232; background:#fef2f2; }

.modal-crear-footer{
    display:flex; justify-content:flex-end; gap:.8rem;
    padding:1.1rem 1.8rem; border-top:1px solid #f1f5f9;
    flex-shrink:0; background:#fff; position:sticky; bottom:0; z-index:2;
}

/* ══════════ COMBOS BUSCABLES (País / Departamento / Ciudad) ══════════ */
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
</style>
@endpush

@section('content')

<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.2rem">
    <div>
        <div class="page-title">Clientes</div>
        <div class="page-sub">Gestiona todos los clientes registrados</div>
    </div>
    <div style="display:flex;gap:.6rem;align-items:center;flex-wrap:wrap">
        <a href="{{ route('admin.clients.import.view') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:.5rem .9rem;
                  background:#fff;border:1px solid #e2e8f0;border-radius:8px;
                  font-size:13px;font-weight:600;color:#6366f1;text-decoration:none">
            <i class="ti ti-file-upload" style="font-size:16px"></i> Importar
        </a>
        <a href="#" id="btn-export-pdf"
           style="display:inline-flex;align-items:center;gap:6px;padding:.5rem .9rem;
                  background:#fff;border:1px solid #e2e8f0;border-radius:8px;
                  font-size:13px;font-weight:600;color:#ef4444;text-decoration:none">
            <i class="ti ti-file-type-pdf" style="font-size:16px"></i> PDF
        </a>
        <a href="#" id="btn-export-excel"
           style="display:inline-flex;align-items:center;gap:6px;padding:.5rem .9rem;
                  background:#fff;border:1px solid #e2e8f0;border-radius:8px;
                  font-size:13px;font-weight:600;color:#16a34a;text-decoration:none">
            <i class="ti ti-file-type-xls" style="font-size:16px"></i> Excel
        </a>
        <button onclick="document.getElementById('modal-crear').classList.remove('hidden')"
                class="btn btn-primary">
            <i class="ti ti-plus" style="font-size:15px"></i> Nuevo Cliente
        </button>
    </div>
</div>

{{-- BARRA DE FILTROS --}}
<div class="filter-bar">
    <div style="position:relative;flex:1;min-width:200px">
        <i class="ti ti-search" style="position:absolute;left:.7rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:15px"></i>
        <input type="text" id="f-search" class="filter-input"
               placeholder="Buscar por nombre, email, teléfono..."
               style="width:100%;padding-left:2.2rem">
    </div>

    <div class="filter-sep"></div>

    <select id="f-contacts" class="filter-input" style="min-width:160px">
        <option value="">Todos los contactos</option>
        <option value="0">Sin contactos</option>
        <option value="1">1 contacto</option>
        <option value="2">2+ contactos</option>
        <option value="5">5+ contactos</option>
    </select>

    <select id="f-date" class="filter-input" style="min-width:150px">
        <option value="">Cualquier fecha</option>
        <option value="today">Hoy</option>
        <option value="week">Esta semana</option>
        <option value="month">Este mes</option>
        <option value="year">Este año</option>
    </select>

    <select id="f-sort" class="filter-input" style="min-width:150px">
        <option value="newest">Más recientes</option>
        <option value="oldest">Más antiguos</option>
        <option value="az">Nombre A → Z</option>
        <option value="za">Nombre Z → A</option>
        <option value="contacts-desc">Más contactos</option>
        <option value="contacts-asc">Menos contactos</option>
    </select>

    <div class="filter-sep"></div>

    <button onclick="clearFilters()"
            style="padding:.5rem .9rem;background:none;border:1px solid #e2e8f0;
                   border-radius:8px;font-size:12px;color:#64748b;cursor:pointer;
                   display:flex;align-items:center;gap:5px;white-space:nowrap">
        <i class="ti ti-filter-off" style="font-size:14px"></i> Limpiar
    </button>

    <span class="results-count" id="results-count"></span>
</div>

{{-- BARRA DE ACCIONES MASIVAS --}}
<div class="bulk-bar" id="bulk-bar">
    <i class="ti ti-checkbox" style="font-size:18px;color:#e63232"></i>
    <span class="bulk-count"><span id="bulk-count">0</span> seleccionado(s)</span>
    <span class="bulk-sep">|</span>
    <button onclick="selectAll(true)"
            style="background:rgba(255,255,255,.1);border:none;color:#fff;
                   padding:.4rem .8rem;border-radius:6px;font-size:12px;cursor:pointer">
        Seleccionar todos
    </button>
    <button onclick="selectAll(false)"
            style="background:rgba(255,255,255,.1);border:none;color:#fff;
                   padding:.4rem .8rem;border-radius:6px;font-size:12px;cursor:pointer">
        Deseleccionar
    </button>
    <button onclick="bulkDelete()"
            style="background:#e63232;border:none;color:#fff;padding:.4rem .9rem;
                   border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;
                   display:flex;align-items:center;gap:5px;margin-left:auto">
        <i class="ti ti-trash" style="font-size:14px"></i> Eliminar seleccionados
    </button>
</div>

{{-- Formulario oculto para eliminación masiva --}}
<form id="bulk-delete-form" action="{{ route('admin.clients.bulk-destroy') }}"
      method="POST" style="display:none">
    @csrf
    @method('DELETE')
    <div id="bulk-ids-container"></div>
</form>

@if($clients->isEmpty())
    <div style="text-align:center;padding:4rem;background:#fff;border-radius:14px;border:1px solid #e2e8f0">
        <i class="ti ti-building-off" style="font-size:48px;color:#cbd5e1;display:block;margin-bottom:1rem"></i>
        <p style="font-size:15px;font-weight:600;color:#0f172a;margin-bottom:.4rem">No hay clientes aún</p>
        <button onclick="document.getElementById('modal-crear').classList.remove('hidden')"
                class="btn btn-primary btn-sm">
            <i class="ti ti-plus"></i> Crear primer cliente
        </button>
    </div>
@else
    <div class="table-wrap" id="table-container">
        <table id="tabla-clientes">
            <thead>
                <tr>
                    <th style="width:40px">
                        <input type="checkbox" id="check-all" title="Seleccionar todos"
                               onchange="toggleAll(this.checked)">
                    </th>
                    <th>ID</th>
                    <th>AGENCIA</th>
                    <th>CODIGO TRIBUTARIO</th>
                    <th>CONTACTO PRINCIPAL</th>
                    <th>EMAIL EMPRESARIAL</th>
                    <th>TELÉFONO EMPRESARIAL</th>
                    <th>CONTACTOS</th>
                    <th>REGISTRO</th>
                    <th style="text-align:center">ACCIONES</th>
                </tr>
            </thead>
            <tbody id="tabla-body">
                @foreach($clients as $c)
                    @php $principal = $c->contacts->first(); @endphp
                    <tr class="client-row"
                        data-id="{{ $c->id_client }}"
                        data-name="{{ strtolower($c->name_client) }}"
                        data-code="{{ strtolower($c->tax_code) }}"
                        data-email="{{ strtolower($principal->email ?? '') }}"
                        data-phone="{{ $principal->first_phone ?? '' }}"
                        data-contacts="{{ $c->contacts_count }}"
                        data-date="{{ $c->created_at->format('Y-m-d') }}"
                        data-ts="{{ $c->created_at->timestamp }}">

                        <td class="cb-wrap">
                            <input type="checkbox" class="row-check"
                                   value="{{ $c->id_client }}"
                                   onchange="updateBulk()">
                        </td>
                        <td style="color:#94a3b8;font-size:12px">{{ $c->id_client }}</td>
                        <td>
                            <div style="font-weight:600;color:#0f172a;font-size:13px">{{ $c->name_client }}</div>
                        </td>
                        <td style="font-size:12px;color:#64748b">{{ $c->tax_code ?? '—' }}</td>
                        <td style="font-size:13px;color:#374151">
                            @if($principal)
                                {{ trim($principal->name . ' ' . ($principal->last_names ?? '')) }}
                                @if($principal->qualification)
                                    <span style="color:#94a3b8"> · {{ $principal->qualification }}</span>
                                @endif
                            @else
                                <span style="color:#cbd5e1">—</span>
                            @endif
                        </td>
                        <td style="font-size:12px;color:#64748b">{{ $c->general_email ?? '—' }}</td>
                        <td style="font-size:12px;color:#64748b">{{ $c->general_phone ?? '—' }}</td>
                        <td>
                            <span style="background:#f0f9ff;color:#0369a1;font-size:11px;font-weight:700;
                                         padding:2px 9px;border-radius:20px;border:1px solid #bae6fd">
                                {{ $c->contacts_count }}
                            </span>
                        </td>
                        <td style="color:#94a3b8;font-size:12px">
                            {{ $c->created_at->format('d/m/Y') }}
                        </td>
                        <td>
                            <div style="display:flex;justify-content:center;gap:6px">
                                <a href="{{ route('admin.clients.edit', $c->id_client) }}"
                                   title="Editar"
                                   style="display:flex;align-items:center;justify-content:center;
                                          width:30px;height:30px;background:#eff6ff;border-radius:7px;
                                          color:#3b82f6;text-decoration:none;font-size:15px">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <form action="{{ route('admin.clients.destroy', $c->id_client) }}"
                                      method="POST"
                                      onsubmit="return confirm('¿Eliminar {{ addslashes($c->name_client) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            title="Eliminar"
                                            style="display:flex;align-items:center;justify-content:center;
                                                   width:30px;height:30px;background:#fef2f2;border:none;
                                                   border-radius:7px;color:#ef4444;cursor:pointer;font-size:15px">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="table-footer" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem">
            <span id="footer-count">
                Mostrando {{ $clients->firstItem() }}–{{ $clients->lastItem() }}
                de {{ $clients->total() }} cliente(s)
            </span>
            <div style="display:flex;align-items:center;gap:.4rem">
                @if($clients->onFirstPage())
                    <span style="padding:.35rem .7rem;border:1px solid #e2e8f0;border-radius:7px;
                                font-size:12px;color:#cbd5e1;cursor:default">
                        <i class="ti ti-chevron-left"></i>
                    </span>
                @else
                    <a href="{{ $clients->previousPageUrl() }}"
                    style="padding:.35rem .7rem;border:1px solid #e2e8f0;border-radius:7px;
                            font-size:12px;color:#374151;text-decoration:none;background:#fff;
                            transition:border-color .15s"
                    onmouseover="this.style.borderColor='#6366f1'"
                    onmouseout="this.style.borderColor='#e2e8f0'">
                        <i class="ti ti-chevron-left"></i>
                    </a>
                @endif

                @foreach($clients->getUrlRange(1, $clients->lastPage()) as $page => $url)
                    @if($page == $clients->currentPage())
                        <span style="padding:.35rem .65rem;border:1px solid #6366f1;border-radius:7px;
                                    font-size:12px;font-weight:700;color:#fff;background:#6366f1;min-width:32px;
                                    text-align:center">
                            {{ $page }}
                        </span>
                    @elseif($page == 1 || $page == $clients->lastPage() || abs($page - $clients->currentPage()) <= 1)
                        <a href="{{ $url }}"
                        style="padding:.35rem .65rem;border:1px solid #e2e8f0;border-radius:7px;
                                font-size:12px;color:#374151;text-decoration:none;background:#fff;min-width:32px;
                                text-align:center;transition:border-color .15s"
                        onmouseover="this.style.borderColor='#6366f1'"
                        onmouseout="this.style.borderColor='#e2e8f0'">
                            {{ $page }}
                        </a>
                    @elseif(abs($page - $clients->currentPage()) == 2)
                        <span style="font-size:12px;color:#94a3b8;padding:0 .2rem">…</span>
                    @endif
                @endforeach

                @if($clients->hasMorePages())
                    <a href="{{ $clients->nextPageUrl() }}"
                    style="padding:.35rem .7rem;border:1px solid #e2e8f0;border-radius:7px;
                            font-size:12px;color:#374151;text-decoration:none;background:#fff;
                            transition:border-color .15s"
                    onmouseover="this.style.borderColor='#6366f1'"
                    onmouseout="this.style.borderColor='#e2e8f0'">
                        <i class="ti ti-chevron-right"></i>
                    </a>
                @else
                    <span style="padding:.35rem .7rem;border:1px solid #e2e8f0;border-radius:7px;
                                font-size:12px;color:#cbd5e1;cursor:default">
                        <i class="ti ti-chevron-right"></i>
                    </span>
                @endif
            </div>
            <span id="footer-filter" style="color:#6366f1;font-size:12px;display:none">
                Mostrando resultados filtrados
            </span>
        </div>
    </div>

    <div id="no-results" style="display:none;text-align:center;padding:3rem;
         background:#fff;border-radius:14px;border:1px solid #e2e8f0;margin-top:.5rem">
        <i class="ti ti-search-off" style="font-size:40px;color:#cbd5e1;display:block;margin-bottom:.7rem"></i>
        <p style="font-size:14px;font-weight:600;color:#475569">Sin resultados para tu búsqueda</p>
        <p style="font-size:12px;color:#94a3b8;margin-top:.3rem">Prueba con otros filtros</p>
    </div>
@endif

{{-- ══════════ MODAL CREAR CLIENTE ══════════ --}}
<div id="modal-crear" class="hidden modal-overlay">
    <div class="modal-crear-box">

        <div class="modal-crear-header">
            <div class="modal-crear-header-icon">
                <i class="ti ti-building-plus"></i>
            </div>
            <div>
                <h2>Registrar Nuevo Cliente</h2>
                <p>Completa los datos de la empresa y agrega sus contactos</p>
            </div>
            <button type="button" class="modal-close"
                    onclick="document.getElementById('modal-crear').classList.add('hidden')">
                <i class="ti ti-x"></i>
            </button>
        </div>

        <form action="{{ route('admin.clients.store') }}" method="POST" id="form-crear-cliente">
            @csrf

            <div class="modal-crear-body">

                {{-- ── DATOS DE LA EMPRESA ── --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="ti ti-building"></i> Datos de la empresa
                    </div>
                    <p class="form-section-hint">Información general que identifica a la agencia o cliente.</p>

                    <div class="form-grid-2">
                        <div class="field">
                            <label>Nombre comercial <span class="req">*</span></label>
                            <input type="text" name="name_client" value="{{ old('name_client') }}"
                                   placeholder="Ej: Fiesta Tours Perú S.A.C." required>
                        </div>
                        <div class="field">
                            <label>Razón Social <span class="req">*</span></label>
                            <input type="text" name="business_name" value="{{ old('business_name') }}"
                                   placeholder="Ej: Fiesta Tours Perú S.A.C." required>
                        </div>
                    </div>

                    <div class="form-grid-3">
                        <div class="field">
                            <label>Código tributario (RUC)</label>
                            <input type="text" name="tax_code" value="{{ old('tax_code') }}"
                                   placeholder="Ej: 20123456789">
                        </div>
                        <div class="field">
                            <label>Teléfono general</label>
                            <input type="text" name="general_phone" value="{{ old('general_phone') }}"
                                   placeholder="Ej: 01-234567">
                        </div>
                        <div class="field">
                            <label>Email general</label>
                            <input type="email" name="general_email" value="{{ old('general_email') }}"
                                   placeholder="contacto@empresa.com">
                        </div>
                    </div>
                </div>

                <div class="form-section-divider"></div>

                {{-- ── UBICACIÓN (combos buscables) ── --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="ti ti-map-pin"></i> Ubicación
                    </div>
                    <p class="form-section-hint">Escribe para buscar el país y la ciudad, y detalla la dirección exacta.</p>

                    <div class="form-grid-2">
                        <div class="field">
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

                        <div class="field">
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

                    <div class="field" style="margin-top:.9rem">
                        <label>Dirección</label>
                        <input type="text" name="address" value="{{ old('address') }}"
                               placeholder="Ej: Avenida Suecia, calle 124, primera casa">
                    </div>
                </div>

                <div class="form-section-divider"></div>

                {{-- ── CONTACTOS ── --}}
                <div class="form-section">
                    <div class="form-section-title-row">
                        <div class="form-section-title">
                            <i class="ti ti-users"></i> Contactos
                        </div>
                        <button type="button" class="add-contact-btn" onclick="agregarContacto()">
                            <i class="ti ti-plus" style="font-size:13px"></i> Añadir contacto
                        </button>
                    </div>
                    <p class="form-section-hint">El primer contacto se registrará como el representante principal.</p>

                    <div id="contactos-wrapper"></div>
                </div>

            </div>

            <div class="modal-crear-footer">
                <button type="button" onclick="document.getElementById('modal-crear').classList.add('hidden')"
                        class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy" style="font-size:14px"></i> Guardar Cliente
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════ MODAL EXPORTAR ══════════ --}}
<div id="modal-export">
    <div class="modal-box">
        <button class="modal-close" onclick="closeExportModal()">
            <i class="ti ti-x"></i>
        </button>

        <div style="display:flex;align-items:center;gap:12px;margin-bottom:1.5rem">
            <div style="width:44px;height:44px;background:#f0fdf4;border-radius:50%;
                        display:flex;align-items:center;justify-content:center;font-size:22px">
                <i class="ti ti-file-export" id="export-modal-icon" style="color:#16a34a"></i>
            </div>
            <div>
                <h2 style="font-size:17px;font-weight:700;color:#0f172a;margin:0">
                    Exportar <span id="export-type-label" style="color:#16a34a">Excel</span>
                </h2>
                <p style="font-size:12px;color:#94a3b8;margin:0">Selecciona qué datos quieres exportar</p>
            </div>
        </div>

        {{-- Opción 1: Todos los datos --}}
        <div class="export-option" onclick="exportAll()">
            <div class="icon green">
                <i class="ti ti-list"></i>
            </div>
            <div style="flex:1">
                <div class="title">Todos los clientes</div>
                <div class="sub">Exporta el listado completo de clientes</div>
            </div>
            <i class="ti ti-chevron-right arrow"></i>
        </div>

        {{-- Opción 2: Cliente específico (combo buscable) --}}
        <div class="export-by-id-wrapper" id="export-client-combo-wrap">
            <div class="header">
                <div class="icon blue">
                    <i class="ti ti-user"></i>
                </div>
                <div style="flex:1">
                    <div class="title">Cliente específico</div>
                    <div class="sub">Busca y elige un cliente por nombre</div>
                </div>
            </div>
            <div style="position:relative; margin-top:.8rem; padding-left:52px; display:flex; gap:.6rem; align-items:center">
                <div style="flex:1; position:relative">
                    <input type="text"
                           id="export-client-search"
                           placeholder="Escribe para buscar cliente..."
                           autocomplete="off"
                           style="width:100%; padding:.5rem .7rem; border:1px solid #e2e8f0;
                                  border-radius:7px; font-size:13px; outline:none;
                                  transition:border-color .15s; box-sizing:border-box">
                    <button type="button" id="export-client-clear"
                            style="display:none; position:absolute; right:.5rem; top:50%;
                                   transform:translateY(-50%); background:none; border:none;
                                   color:#cbd5e1; cursor:pointer; font-size:14px; padding:2px; line-height:1">
                        <i class="ti ti-x"></i>
                    </button>
                    <div id="export-client-list"
                         style="display:none; position:absolute; top:calc(100% + 4px); left:0; right:0;
                                background:#fff; border:1px solid #e2e8f0; border-radius:9px;
                                max-height:200px; overflow-y:auto; z-index:60;
                                box-shadow:0 10px 25px -5px rgba(0,0,0,.1)">
                    </div>
                </div>
                <button id="export-client-btn"
                        onclick="exportSelectedClient()"
                        disabled
                        style="padding:.5rem 1rem; background:#6366f1; color:#fff; border:none;
                               border-radius:7px; font-size:13px; font-weight:600; cursor:pointer;
                               transition:background .15s; white-space:nowrap; opacity:.45">
                    <i class="ti ti-arrow-right" style="font-size:14px"></i> Exportar
                </button>
            </div>
        </div>

        <button class="btn-cancel-export" onclick="closeExportModal()">
            Cancelar
        </button>
    </div>
</div>

@push('scripts')
<script>
// ── Datos de filas para filtrado ──────────────────────────────
const rows = Array.from(document.querySelectorAll('.client-row'));

// ── Filtros ───────────────────────────────────────────────────
function applyFilters() {
    const search   = document.getElementById('f-search').value.toLowerCase().trim();
    const contacts = document.getElementById('f-contacts').value;
    const date     = document.getElementById('f-date').value;
    const sort     = document.getElementById('f-sort').value;

    const now   = new Date();
    const today = now.toISOString().split('T')[0];
    const weekStart = new Date(now); weekStart.setDate(now.getDate() - now.getDay());
    const monthStart = new Date(now.getFullYear(), now.getMonth(), 1);
    const yearStart  = new Date(now.getFullYear(), 0, 1);

    let visible = rows.filter(row => {
        const name    = row.dataset.name;
        const email   = row.dataset.email;
        const phone   = row.dataset.phone;
        const cnt     = parseInt(row.dataset.contacts);
        const rowDate = new Date(row.dataset.date);

        if (search && !name.includes(search) && !email.includes(search) && !phone.includes(search)) {
            return false;
        }

        if (contacts === '0'  && cnt !== 0) return false;
        if (contacts === '1'  && cnt !== 1) return false;
        if (contacts === '2'  && cnt < 2)   return false;
        if (contacts === '5'  && cnt < 5)   return false;

        if (date === 'today' && row.dataset.date !== today) return false;
        if (date === 'week'  && rowDate < weekStart)        return false;
        if (date === 'month' && rowDate < monthStart)       return false;
        if (date === 'year'  && rowDate < yearStart)        return false;

        return true;
    });

    visible.sort((a, b) => {
        switch (sort) {
            case 'oldest':       return a.dataset.ts - b.dataset.ts;
            case 'az':           return a.dataset.name.localeCompare(b.dataset.name);
            case 'za':           return b.dataset.name.localeCompare(a.dataset.name);
            case 'contacts-desc':return parseInt(b.dataset.contacts) - parseInt(a.dataset.contacts);
            case 'contacts-asc': return parseInt(a.dataset.contacts) - parseInt(b.dataset.contacts);
            default:             return b.dataset.ts - a.dataset.ts;
        }
    });

    const tbody = document.getElementById('tabla-body');
    rows.forEach(r => r.style.display = 'none');
    visible.forEach(r => { r.style.display = ''; tbody.appendChild(r); });

    const noResults = document.getElementById('no-results');
    const tableContainer = document.getElementById('table-container');
    if (visible.length === 0) {
        noResults.style.display = 'block';
        tableContainer.style.display = 'none';
    } else {
        noResults.style.display = 'none';
        tableContainer.style.display = 'block';
    }

    const count = visible.length;
    document.getElementById('results-count').textContent = count + ' resultado(s)';
    document.getElementById('footer-count').textContent = count + ' cliente(s)';
    const footerFilter = document.getElementById('footer-filter');
    const hasFilters = search || contacts || date || sort !== 'newest';
    footerFilter.style.display = hasFilters ? 'block' : 'none';

    deselectAll();
}

function clearFilters() {
    document.getElementById('f-search').value   = '';
    document.getElementById('f-contacts').value = '';
    document.getElementById('f-date').value     = '';
    document.getElementById('f-sort').value     = 'newest';
    applyFilters();
}

['f-search','f-contacts','f-date','f-sort'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
        el.addEventListener('input',  applyFilters);
        el.addEventListener('change', applyFilters);
    }
});

// ── Selección múltiple ────────────────────────────────────────
function toggleAll(checked) {
    document.querySelectorAll('.row-check').forEach(cb => {
        if (cb.closest('tr').style.display !== 'none') {
            cb.checked = checked;
            cb.closest('tr').classList.toggle('selected', checked);
        }
    });
    updateBulk();
}

function selectAll(val) { toggleAll(val); }

function deselectAll() {
    document.querySelectorAll('.row-check').forEach(cb => {
        cb.checked = false;
        cb.closest('tr').classList.remove('selected');
    });
    document.getElementById('check-all').checked = false;
    updateBulk();
}

function updateBulk() {
    const checked = document.querySelectorAll('.row-check:checked');
    const bar = document.getElementById('bulk-bar');
    document.getElementById('bulk-count').textContent = checked.length;

    if (checked.length > 0) {
        bar.classList.add('visible');
    } else {
        bar.classList.remove('visible');
        document.getElementById('check-all').checked = false;
    }

    document.querySelectorAll('.row-check').forEach(cb => {
        cb.closest('tr').classList.toggle('selected', cb.checked);
    });

    const all = document.querySelectorAll('.row-check');
    const allVisible = [...all].filter(cb => cb.closest('tr').style.display !== 'none');
    const ca = document.getElementById('check-all');
    if (checked.length === 0) { ca.checked = false; ca.indeterminate = false; }
    else if (checked.length === allVisible.length) { ca.checked = true; ca.indeterminate = false; }
    else { ca.indeterminate = true; }
}

function bulkDelete() {
    const checked = document.querySelectorAll('.row-check:checked');
    if (checked.length === 0) return;

    if (!confirm(`¿Eliminar ${checked.length} cliente(s) seleccionado(s)? Esta acción también eliminará sus contactos.`)) return;

    const container = document.getElementById('bulk-ids-container');
    container.innerHTML = '';
    checked.forEach(cb => {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = 'ids[]';
        input.value = cb.value;
        container.appendChild(input);
    });

    document.getElementById('bulk-delete-form').submit();
}

// ── Modal crear cliente ───────────────────────────────────────
let contactoIdx = 0;

function actualizarNumeros() {
    const cards = document.querySelectorAll('#contactos-wrapper .contact-card');
    cards.forEach((card, index) => {
        const badge = card.querySelector('.contact-badge');
        const title = card.querySelector('.contact-card-title');
        if (badge) badge.textContent = index + 1;
        if (title) {
            title.textContent = index === 0 ? 'Representante principal' : 'Contacto adicional';
        }
    });
}

function agregarContacto() {
    const i = contactoIdx++;
    const wrapper = document.getElementById('contactos-wrapper');
    const div = document.createElement('div');
    div.className = 'contact-card';
    div.id = `contacto-${i}`;
    div.innerHTML = `
        <div class="contact-card-head">
            <span class="contact-badge">${i + 1}</span>
            <span class="contact-card-title">${i === 0 ? 'Representante principal' : 'Contacto adicional'}</span>
            ${i === 0 ? '<span class="contact-principal-tag"><i class="ti ti-star-filled"></i> Principal</span>' : ''}
            ${i > 0 ? `<button type="button" class="contact-remove-btn" title="Quitar contacto"
                onclick="document.getElementById('contacto-${i}').remove(); actualizarNumeros();"><i class="ti ti-trash"></i></button>` : ''}
        </div>

        <div class="form-grid-2">
            <div class="field">
                <label>Nombre <span class="req">*</span></label>
                <input type="text" name="contacts[${i}][name]" placeholder="Nombre" required>
            </div>
            <div class="field">
                <label>Apellidos</label>
                <input type="text" name="contacts[${i}][last_names]" placeholder="Apellidos">
            </div>
        </div>

        <div class="form-grid-2">
            <div class="field">
                <label>Correo</label>
                <input type="email" name="contacts[${i}][email]" placeholder="ejemplo@correo.com">
            </div>
            <div class="field">
                <label>Cargo</label>
                <input type="text" name="contacts[${i}][qualification]" placeholder="Ej: Gerente, Coordinador...">
            </div>
        </div>

        <div class="form-grid-2">
            <div class="field">
                <label>Teléfono 1</label>
                <input type="text" name="contacts[${i}][first_phone]" placeholder="Principal">
            </div>
            <div class="field">
                <label>Teléfono 2</label>
                <input type="text" name="contacts[${i}][second_phone]" placeholder="Opcional">
            </div>
        </div>`;
    wrapper.appendChild(div);
    wrapper.scrollTop = wrapper.scrollHeight;
}

@if($errors->any())
    document.getElementById('modal-crear').classList.remove('hidden');
    agregarContacto();
@else
    agregarContacto();
@endif

const totalVisible = rows.length;
document.getElementById('results-count').textContent = totalVisible + ' en esta página';

// ════════════════════════════════════════════════════════════════
// ── COMBO BUSCABLE GENÉRICO (país / ciudad) ──────────────────────
// ════════════════════════════════════════════════════════════════
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
    fetch(`/api/geo/paises`)
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
    fetch(`/api/geo/ciudades?country=${countryCode}`)
        .then(r => r.json())
        .then(ciudades => {
            const opciones = ciudades.map(c => ({ value: c.nombre, label: c.nombre, geoNameId: c.geoNameId }));
            comboCiudad.setOptions(opciones, 'Escribe para buscar ciudad...');
        })
        .catch(() => comboCiudad.setOptions([], 'No se pudo cargar'));
}

cargarPaises();

// ════════════════════════════════════════════════════════════════
// ── MODAL EXPORTAR ───────────────────────────────────────────────
// ════════════════════════════════════════════════════════════════
let exportType = 'excel';
let exportClientId = null;

// Lista de clientes de la página actual para el combo de exportar.
// Si necesitas TODOS los clientes (no solo la página), pasa una variable
// separada desde el controller: Client::all(['id_client','name_client'])
const clientsData = @json($clients->map(fn($c) => ['id' => $c->id_client, 'name' => $c->name_client]));

(function initExportCombo() {
    const input  = document.getElementById('export-client-search');
    const list   = document.getElementById('export-client-list');
    const clear  = document.getElementById('export-client-clear');
    const btn    = document.getElementById('export-client-btn');
    let activeIdx = -1;
    let filtered  = [];

    function normalizeStr(str) {
        return (str || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }

    function renderList(term) {
        const q = normalizeStr(term);
        filtered = q
            ? clientsData.filter(c => normalizeStr(c.name).includes(q))
            : clientsData.slice(0, 50);

        if (filtered.length === 0) {
            list.innerHTML = '<div style="padding:.6rem .8rem;font-size:12.5px;color:#94a3b8">Sin resultados</div>';
        } else {
            list.innerHTML = filtered.map((c, i) =>
                `<div data-idx="${i}"
                      style="padding:.55rem .8rem;font-size:13px;color:#0f172a;cursor:pointer;
                             transition:background .1s">
                    <span style="color:#94a3b8;font-size:11px;margin-right:6px">#${c.id}</span>${c.name}
                </div>`
            ).join('');
        }
        activeIdx = -1;
        list.style.display = 'block';
    }

    function selectClient(c) {
        input.value    = c.name;
        exportClientId = c.id;
        clear.style.display  = 'block';
        btn.disabled         = false;
        btn.style.opacity    = '1';
        btn.style.cursor     = 'pointer';
        list.style.display   = 'none';
        input.style.borderColor = '#6366f1';
    }

    function clearSelection() {
        input.value    = '';
        exportClientId = null;
        clear.style.display  = 'none';
        btn.disabled         = true;
        btn.style.opacity    = '.45';
        btn.style.cursor     = 'default';
        list.style.display   = 'none';
        input.style.borderColor = '#e2e8f0';
        input.focus();
    }

    function updateActive() {
        list.querySelectorAll('[data-idx]').forEach(el => {
            el.style.background = '';
            el.style.color = '#0f172a';
        });
        const el = list.querySelector(`[data-idx="${activeIdx}"]`);
        if (el) {
            el.style.background = '#eef2ff';
            el.style.color = '#4338ca';
            el.scrollIntoView({ block: 'nearest' });
        }
    }

    input.addEventListener('focus', () => renderList(input.value));

    input.addEventListener('input', () => {
        exportClientId = null;
        btn.disabled = true;
        btn.style.opacity = '.45';
        btn.style.cursor  = 'default';
        input.style.borderColor = '#e2e8f0';
        clear.style.display = input.value ? 'block' : 'none';
        renderList(input.value);
    });

    input.addEventListener('keydown', e => {
        if (list.style.display === 'none') return;
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIdx = Math.min(activeIdx + 1, filtered.length - 1);
            updateActive();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIdx = Math.max(activeIdx - 1, 0);
            updateActive();
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (activeIdx >= 0 && filtered[activeIdx]) selectClient(filtered[activeIdx]);
        } else if (e.key === 'Escape') {
            list.style.display = 'none';
        }
    });

    list.addEventListener('mousedown', e => {
        e.preventDefault();
        const item = e.target.closest('[data-idx]');
        if (!item) return;
        selectClient(filtered[parseInt(item.dataset.idx)]);
    });

    // Hover en items de la lista
    list.addEventListener('mouseover', e => {
        const item = e.target.closest('[data-idx]');
        if (!item) return;
        list.querySelectorAll('[data-idx]').forEach(el => { el.style.background = ''; el.style.color = '#0f172a'; });
        item.style.background = '#eef2ff';
        item.style.color = '#4338ca';
        activeIdx = parseInt(item.dataset.idx);
    });

    clear.addEventListener('click', clearSelection);

    document.addEventListener('click', e => {
        if (!input.contains(e.target) && !list.contains(e.target)) {
            list.style.display = 'none';
        }
    });
})();

function exportSelectedClient() {
    if (!exportClientId) return;
    const url = exportType === 'excel'
        ? '{{ route("admin.clients.export.excel") }}?client_id=' + exportClientId
        : '{{ route("admin.clients.export.pdf") }}?client_id=' + exportClientId;
    window.location.href = url;
    closeExportModal();
}

function openExportModal(type) {
    exportType = type;
    document.getElementById('modal-export').classList.add('show');

    const label = document.getElementById('export-type-label');
    const icon  = document.getElementById('export-modal-icon');

    if (type === 'excel') {
        label.textContent = 'Excel';
        label.style.color = '#16a34a';
        icon.style.color  = '#16a34a';
    } else {
        label.textContent = 'PDF';
        label.style.color = '#ef4444';
        icon.style.color  = '#ef4444';
    }

    // Reset combo al abrir
    const input = document.getElementById('export-client-search');
    const clear = document.getElementById('export-client-clear');
    const list  = document.getElementById('export-client-list');
    const btn   = document.getElementById('export-client-btn');
    input.value           = '';
    input.style.borderColor = '#e2e8f0';
    clear.style.display   = 'none';
    list.style.display    = 'none';
    btn.disabled          = true;
    btn.style.opacity     = '.45';
    btn.style.cursor      = 'default';
    exportClientId        = null;
}

function closeExportModal() {
    document.getElementById('modal-export').classList.remove('show');
}

function exportAll() {
    const url = exportType === 'excel'
        ? '{{ route("admin.clients.export.excel") }}'
        : '{{ route("admin.clients.export.pdf") }}';
    window.location.href = url;
    closeExportModal();
}

// ── Botones de exportación ────────────────────────────────────
document.getElementById('btn-export-pdf').addEventListener('click', function(e) {
    e.preventDefault();
    openExportModal('pdf');
});

document.getElementById('btn-export-excel').addEventListener('click', function(e) {
    e.preventDefault();
    openExportModal('excel');
});

// ── Cerrar modal con ESC ──────────────────────────────────────
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeExportModal();
});

// ── Cerrar modal al hacer clic fuera ─────────────────────────
document.getElementById('modal-export').addEventListener('click', function(e) {
    if (e.target === this) closeExportModal();
});
</script>
@endpush
@endsection
