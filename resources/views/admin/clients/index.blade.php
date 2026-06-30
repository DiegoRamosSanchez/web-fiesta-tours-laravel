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

#modal-export {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    z-index: 1000;
    display: none;
    align-items: center;
    justify-content: center;
}
#modal-export.show { display: flex; }

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
.export-by-id-wrapper:hover { border-color: #6366f1; }
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
.btn-cancel-export:hover { background: #e2e8f0; }

/* MODAL VER CLIENTE */
@keyframes modalFadeInClient {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
.custom-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
    z-index: 9999;
    justify-content: center;
    align-items: center;
    padding: 1rem;
}
.custom-modal-content {
    background: #fff;
    width: 100%;
    max-width: 800px;
    border-radius: 14px;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    display: flex;
    flex-direction: column;
    max-height: 90vh;
    animation: modalFadeInClient 0.2s ease-out;
}
.btn-open-view {
    display:flex;align-items:center;justify-content:center;
    width:30px;height:30px;background:#eef2ff;border:none;
    border-radius:7px;color:#6366f1;cursor:pointer;font-size:15px;
}
.btn-open-view:hover { background:#e0e7ff; }
</style>
@endpush

@section('content')

@php
    $hayFiltros = request()->anyFilled(['search','country','city','date'])
        || (request('sort') && request('sort') !== 'newest');
@endphp

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
        <a href="{{ route('admin.clients.create') }}" class="btn btn-primary"
            style="text-decoration:none;display:inline-flex;align-items:center;gap:6px">
            <i class="ti ti-plus" style="font-size:15px"></i> Nuevo Cliente
        </a>
    </div>
</div>

{{-- BARRA DE FILTROS --}}
<div class="filter-bar">
    <div style="position:relative;flex:1;min-width:200px">
        <i class="ti ti-search" style="position:absolute;left:.7rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:15px"></i>
        <input type="text" id="f-search" class="filter-input"
               value="{{ request('search') }}"
               placeholder="Buscar por agencia, código tributario, email, teléfono..."
               style="width:100%;padding-left:2.2rem">
    </div>

    <div class="filter-sep"></div>

    <select id="f-country" class="filter-input" style="min-width:160px">
        <option value="">Todos los países</option>
        @foreach($countries as $country)
            <option value="{{ $country }}" {{ request('country') == $country ? 'selected' : '' }}>{{ $country }}</option>
        @endforeach
    </select>

    <select id="f-city" class="filter-input" style="min-width:150px">
        <option value="">Todas las ciudades</option>
        @foreach($cities as $city)
            <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
        @endforeach
    </select>

    <select id="f-date" class="filter-input" style="min-width:150px">
        <option value="">Cualquier fecha</option>
        <option value="today" {{ request('date') == 'today' ? 'selected' : '' }}>Hoy</option>
        <option value="week"  {{ request('date') == 'week'  ? 'selected' : '' }}>Esta semana</option>
        <option value="month" {{ request('date') == 'month' ? 'selected' : '' }}>Este mes</option>
        <option value="year"  {{ request('date') == 'year'  ? 'selected' : '' }}>Este año</option>
    </select>

    <select id="f-sort" class="filter-input" style="min-width:170px">
        <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Más recientes</option>
        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Más antiguos</option>
        <option value="az"     {{ request('sort') == 'az'     ? 'selected' : '' }}>Agencia A → Z</option>
        <option value="za"     {{ request('sort') == 'za'     ? 'selected' : '' }}>Agencia Z → A</option>
        <option value="tax-az" {{ request('sort') == 'tax-az' ? 'selected' : '' }}>Código Tributario A → Z</option>
        <option value="tax-za" {{ request('sort') == 'tax-za' ? 'selected' : '' }}>Código Tributario Z → A</option>
    </select>

    <div class="filter-sep"></div>

    <button onclick="clearFilters()"
            style="padding:.5rem .9rem;background:none;border:1px solid #e2e8f0;
                   border-radius:8px;font-size:12px;color:#64748b;cursor:pointer;
                   display:flex;align-items:center;gap:5px;white-space:nowrap">
        <i class="ti ti-filter-off" style="font-size:14px"></i> Limpiar
    </button>

    <span class="results-count" id="results-count">{{ $clients->total() }} resultado(s)</span>
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

    @if($hayFiltros)
        <div style="text-align:center;padding:3rem;background:#fff;border-radius:14px;border:1px solid #e2e8f0">
            <i class="ti ti-search-off" style="font-size:40px;color:#cbd5e1;display:block;margin-bottom:.7rem"></i>
            <p style="font-size:14px;font-weight:600;color:#475569">Sin resultados para tu búsqueda</p>
            <p style="font-size:12px;color:#94a3b8;margin-top:.3rem">Prueba con otros filtros</p>
        </div>
    @else
        <div style="text-align:center;padding:4rem;background:#fff;border-radius:14px;border:1px solid #e2e8f0">
            <i class="ti ti-building-off" style="font-size:48px;color:#cbd5e1;display:block;margin-bottom:1rem"></i>
            <p style="font-size:15px;font-weight:600;color:#0f172a;margin-bottom:.4rem">No hay clientes aún</p>
            <a href="{{ route('admin.clients.create') }}" class="btn btn-primary btn-sm" style="text-decoration:none">
                <i class="ti ti-plus"></i> Crear primer cliente
            </a>
        </div>
    @endif

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
                    <th>TIPO DE CLIENTE</th>
                    <th>EMAIL EMPRESARIAL</th>
                    <th>TELÉFONO EMPRESARIAL</th>
                    <th>UBICACIÓN</th>
                    <th>REGISTRO</th>
                    <th style="text-align:center">ACCIONES</th>
                </tr>
            </thead>
            <tbody id="tabla-body">
                @foreach($clients as $c)
                    <tr class="client-row" data-id="{{ $c->id_client }}">
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
                        <td style="font-size:12px;color:#64748b"> {{ strtoupper($c->type_client ?? '—') }}</td>
                        <td style="font-size:12px;color:#64748b">{{ $c->general_email ?? '—' }}</td>
                        <td style="font-size:12px;color:#64748b">{{ $c->general_phone ?? '—' }}</td>
                        <td style="color:#94a3b8;font-size:12px">
                            {{ $c->country_name . ', ' . $c->city_name }}
                        </td>
                        <td style="color:#94a3b8;font-size:12px">
                            {{ $c->created_at->format('d/m/Y') }}
                        </td>
                        <td>
                            <div style="display:flex;justify-content:center;gap:6px">
                                <button type="button"
                                        class="btn-open-view btn-open-modal"
                                        title="Ver detalles"
                                        data-target="modal-client-{{ $c->id_client }}">
                                    <i class="ti ti-eye"></i>
                                </button>
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
            <div class="pagination-controls" style="display:flex;align-items:center;gap:.4rem">
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
            <span id="footer-filter" style="color:#6366f1;font-size:12px;{{ $hayFiltros ? '' : 'display:none' }}">
                Mostrando resultados filtrados
            </span>
        </div>
    </div>

    {{-- MODALES VER CLIENTE --}}
    @foreach($clients as $c)
    <div id="modal-client-{{ $c->id_client }}" class="custom-modal-overlay" style="display:none">
        <div class="custom-modal-content">
            <div style="background: #f8fafc; border-bottom:1px solid #e2e8f0; border-top-left-radius:14px; border-top-right-radius:14px; padding:1.2rem 1.5rem; display:flex; justify-content:space-between; align-items:center">
                <div style="display:flex; align-items:center; gap:12px">
                    <div style="background: #e0e7ff; color: #4338ca; width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center">
                        <i class="ti ti-building" style="font-size:22px"></i>
                    </div>
                    <div>
                        <h5 style="font-weight:700; color:#0f172a; margin:0; font-size:16px">{{ $c->name_client }}</h5>
                        <span style="font-size:12px; color:#64748b">ID Cliente: #{{ $c->id_client }}</span>
                    </div>
                </div>
                <button type="button" class="btn-close-modal" data-close="modal-client-{{ $c->id_client }}" style="background:none; border:none; color:#64748b; cursor:pointer; font-size:20px; line-height:1">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            <div style="padding:1.5rem; overflow-y:auto; flex:1;">
                <div class="custom-tabs" data-client="{{ $c->id_client }}" style="display:flex; gap:8px; border-bottom:1px solid #f1f5f9; padding-bottom:12px; margin-bottom:1.5rem">
                    <button class="tab-trigger active" data-tab="info" style="border:none; cursor:pointer; font-size:13px; font-weight:600; padding:8px 16px; border-radius:8px; background: #4338ca; color:#fff; display:flex; align-items:center; gap:6px">
                        <i class="ti ti-info-circle"></i> Info. General
                    </button>
                    <button class="tab-trigger" data-tab="contacts" style="border:none; cursor:pointer; font-size:13px; font-weight:600; padding:8px 16px; border-radius:8px; background:#f1f5f9; color:#475569; display:flex; align-items:center; gap:6px">
                        <i class="ti ti-users"></i> Contactos ({{ $c->contacts->count() }})
                    </button>
                </div>

                <div class="custom-tab-contents" data-client="{{ $c->id_client }}">
                    <div class="tab-content-panel panel-info" style="display:block">
                        <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:1.2rem">
                            <div>
                                <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Razón Social</label>
                                <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $c->business_name ?? '—' }}</div>
                            </div>
                            <div>
                                <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Código Tributario / RUC</label>
                                <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $c->tax_code ?? '—' }}</div>
                            </div>
                            <div>
                                <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Teléfono General</label>
                                <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $c->general_phone ?? '—' }}</div>
                            </div>
                            <div>
                                <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Correo General</label>
                                <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $c->general_email ?? '—' }}</div>
                            </div>
                            <div>
                                <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">País</label>
                                <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $c->country_name ?? '—' }}</div>
                            </div>
                            <div>
                                <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Ciudad</label>
                                <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $c->city_name ?? '—' }}</div>
                            </div>
                            <div style="grid-column: span 2">
                                <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Dirección</label>
                                <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $c->address ?? '—' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-content-panel panel-contacts" style="display:none">
                        @if($c->contacts->isEmpty())
                            <div style="text-align:center; padding:2rem; color:#94a3b8">
                                <i class="ti ti-users-minus" style="font-size:32px; display:block; margin-bottom:8px"></i>
                                <span style="font-size:13px">No hay contactos registrados para este cliente.</span>
                            </div>
                        @else
                            <div style="display:flex; flex-direction:column; gap:12px">
                                @foreach($c->contacts as $index => $contact)
                                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:1rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:.6rem">
                                        <div style="display:flex; align-items:center; gap:12px">
                                            <div style="background:#f1f5f9; color:#475569; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px">
                                                {{ strtoupper(substr($contact->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div style="font-size:14px; font-weight:600; color:#0f172a">
                                                    {{ $contact->name }} {{ $contact->last_names }}
                                                    @if($index === 0)
                                                        <span style="background:#dcfce7; color:#15803d; font-size:10px; font-weight:700; padding:2px 6px; border-radius:4px; margin-left:6px; text-transform:uppercase">Principal</span>
                                                    @endif
                                                </div>
                                                <div style="font-size:12px; color:#64748b">{{ $contact->qualification ?? 'Sin cargo asignado' }}</div>
                                            </div>
                                        </div>
                                        <div style="text-align:right; font-size:12px; color:#334155">
                                            <div><i class="ti ti-mail" style="color:#94a3b8"></i> {{ $contact->email ?? '—' }}</div>
                                            <div><i class="ti ti-phone" style="color:#94a3b8"></i> {{ $contact->first_phone ?? '—' }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div style="background:#f8fafc; border-top:1px solid #e2e8f0; border-bottom-left-radius:14px; border-bottom-right-radius:14px; padding:0.8rem 1.5rem; display:flex; justify-content:flex-end">
                <button type="button" class="btn-close-modal" data-close="modal-client-{{ $c->id_client }}" style="font-size:13px; background:#64748b; color:#fff; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-weight:500">Cerrar</button>
            </div>
        </div>
    </div>
    @endforeach
@endif

{{-- MODAL EXPORTAR --}}
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
    // IMPORTANTE: ahora se usa $allClientsForExport (TODOS los clientes), no $clients (solo la página actual)
    window.clientsData = @json($allClientsForExport->map(fn($c) => ['id' => $c->id_client, 'name' => $c->name_client]));
    window.exportExcelUrl = '{{ route("admin.clients.export.excel") }}';
    window.exportPdfUrl = '{{ route("admin.clients.export.pdf") }}';
</script>

<script>
let exportType = 'excel';
let exportClientId = null;
const clientsData = window.clientsData || [];

// ============================================================
// FILTROS Y PAGINACIÓN
// ============================================================
let searchDebounce;

function buildFilterURL() {
    const params = new URLSearchParams(window.location.search);
    const setOrDelete = (key, value, skipIf) => {
        if (value && value !== skipIf) params.set(key, value);
        else params.delete(key);
    };

    setOrDelete('search', document.getElementById('f-search').value.trim());
    setOrDelete('country', document.getElementById('f-country').value);
    setOrDelete('city', document.getElementById('f-city').value);
    setOrDelete('date', document.getElementById('f-date').value);
    setOrDelete('sort', document.getElementById('f-sort').value, 'newest');

    params.delete('page'); // al cambiar un filtro, volvemos a la página 1

    return window.location.pathname + '?' + params.toString();
}

function applyFilters() {
    clearTimeout(searchDebounce);
    searchDebounce = setTimeout(() => {
        window.location.href = buildFilterURL();
    }, 400); // debounce para no recargar en cada tecla
}

function clearFilters() {
    window.location.href = window.location.pathname;
}

// ============================================================
// SELECCIÓN MÚLTIPLE
// ============================================================
function toggleAll(checked) {
    document.querySelectorAll('.row-check').forEach(cb => {
        if (cb.closest('tr').style.display !== 'none') {
            cb.checked = checked;
            cb.closest('tr').classList.toggle('selected', checked);
        }
    });
    updateBulk();
}

function selectAll(val) {
    toggleAll(val);
}

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
    if (checked.length === 0) {
        ca.checked = false;
        ca.indeterminate = false;
    } else if (checked.length === allVisible.length) {
        ca.checked = true;
        ca.indeterminate = false;
    } else {
        ca.indeterminate = true;
    }
}

function bulkDelete() {
    const checked = document.querySelectorAll('.row-check:checked');
    if (checked.length === 0) return;

    if (!confirm(`¿Eliminar ${checked.length} cliente(s) seleccionado(s)? Esta acción también eliminará sus contactos.`)) return;

    const container = document.getElementById('bulk-ids-container');
    container.innerHTML = '';
    checked.forEach(cb => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = cb.value;
        container.appendChild(input);
    });

    document.getElementById('bulk-delete-form').submit();
}

// ============================================================
// MODAL EXPORTAR
// ============================================================
function exportSelectedClient() {
    if (!exportClientId) return;
    const url = exportType === 'excel'
        ? window.exportExcelUrl + '?client_id=' + exportClientId
        : window.exportPdfUrl + '?client_id=' + exportClientId;
    window.location.href = url;
    closeExportModal();
}

function openExportModal(type) {
    exportType = type;
    document.getElementById('modal-export').classList.add('show');

    const label = document.getElementById('export-type-label');
    const icon = document.getElementById('export-modal-icon');

    if (type === 'excel') {
        label.textContent = 'Excel';
        label.style.color = '#16a34a';
        icon.style.color = '#16a34a';
    } else {
        label.textContent = 'PDF';
        label.style.color = '#ef4444';
        icon.style.color = '#ef4444';
    }

    const input = document.getElementById('export-client-search');
    const clear = document.getElementById('export-client-clear');
    const list = document.getElementById('export-client-list');
    const btn = document.getElementById('export-client-btn');
    input.value = '';
    input.style.borderColor = '#e2e8f0';
    clear.style.display = 'none';
    list.style.display = 'none';
    btn.disabled = true;
    btn.style.opacity = '.45';
    btn.style.cursor = 'default';
    exportClientId = null;
}

function closeExportModal() {
    document.getElementById('modal-export').classList.remove('show');
}

function exportAll() {
    const url = exportType === 'excel'
        ? window.exportExcelUrl
        : window.exportPdfUrl;
    window.location.href = url;
    closeExportModal();
}

// ============================================================
// COMBO DE EXPORTACIÓN (buscador de clientes)
// ============================================================
(function initExportCombo() {
    const input = document.getElementById('export-client-search');
    const list = document.getElementById('export-client-list');
    const clear = document.getElementById('export-client-clear');
    const btn = document.getElementById('export-client-btn');
    let activeIdx = -1;
    let filtered = [];

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
        input.value = c.name;
        exportClientId = c.id;
        clear.style.display = 'block';
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
        list.style.display = 'none';
        input.style.borderColor = '#6366f1';
    }

    function clearSelection() {
        input.value = '';
        exportClientId = null;
        clear.style.display = 'none';
        btn.disabled = true;
        btn.style.opacity = '.45';
        btn.style.cursor = 'default';
        list.style.display = 'none';
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
        btn.style.cursor = 'default';
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

    list.addEventListener('mouseover', e => {
        const item = e.target.closest('[data-idx]');
        if (!item) return;
        list.querySelectorAll('[data-idx]').forEach(el => {
            el.style.background = '';
            el.style.color = '#0f172a';
        });
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

// ============================================================
// INICIALIZACIÓN
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    // Guardar texto de paginación original
    const footerCount = document.getElementById('footer-count');
    if (footerCount) {
        window.originalPaginationText = footerCount.textContent;
    }

    // --- Eventos de filtros ---
    const searchInput = document.getElementById('f-search');
    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }

    ['f-country', 'f-city', 'f-date', 'f-sort'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('change', applyFilters);
        }
    });

    // --- Botones de exportación ---
    document.getElementById('btn-export-pdf').addEventListener('click', function(e) {
        e.preventDefault();
        openExportModal('pdf');
    });

    document.getElementById('btn-export-excel').addEventListener('click', function(e) {
        e.preventDefault();
        openExportModal('excel');
    });

    // --- Cerrar modal export al hacer clic fuera ---
    document.getElementById('modal-export').addEventListener('click', function(e) {
        if (e.target === this) closeExportModal();
    });

    // --- Modales ver cliente ---
    document.querySelectorAll('.btn-open-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            const modalId = this.getAttribute('data-target');
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        });
    });

    document.querySelectorAll('.btn-close-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            const modalId = this.getAttribute('data-close');
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        });
    });

    document.querySelectorAll('.custom-modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
                document.body.style.overflow = '';
            }
        });
    });

    // --- Tabs en modales ---
    document.querySelectorAll('.custom-tabs .tab-trigger').forEach(tabBtn => {
        tabBtn.addEventListener('click', function() {
            const parentTabs = this.closest('.custom-tabs');
            const clientId = parentTabs.getAttribute('data-client');
            const targetPanelName = this.getAttribute('data-tab');

            parentTabs.querySelectorAll('.tab-trigger').forEach(b => {
                b.style.background = '#f1f5f9';
                b.style.color = '#475569';
            });

            this.style.background = '#4338ca';
            this.style.color = '#fff';

            const contentsContainer = document.querySelector(`.custom-tab-contents[data-client="${clientId}"]`);
            contentsContainer.querySelectorAll('.tab-content-panel').forEach(panel => {
                panel.style.display = 'none';
            });

            const activePanel = contentsContainer.querySelector(`.panel-${targetPanelName}`);
            if (activePanel) {
                activePanel.style.display = 'block';
            }
        });
    });

    // --- Cerrar modales con ESC ---
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.custom-modal-overlay').forEach(overlay => {
                if (overlay.style.display === 'flex') {
                    overlay.style.display = 'none';
                    document.body.style.overflow = '';
                }
            });
            closeExportModal();
        }
    });
});
</script>
@endpush
@endsection
