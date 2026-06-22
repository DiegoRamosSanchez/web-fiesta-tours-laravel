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
.export-by-id-wrapper .input-group {
    display: flex;
    gap: .6rem;
    margin-top: .8rem;
    padding-left: 52px;
}
.export-by-id-wrapper .input-group input {
    flex: 1;
    padding: .5rem .7rem;
    border: 1px solid #e2e8f0;
    border-radius: 7px;
    font-size: 13px;
    outline: none;
    transition: border-color .15s;
}
.export-by-id-wrapper .input-group input:focus {
    border-color: #6366f1;
}
.export-by-id-wrapper .input-group button {
    padding: .5rem 1rem;
    background: #6366f1;
    color: #fff;
    border: none;
    border-radius: 7px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background .15s;
    white-space: nowrap;
}
.export-by-id-wrapper .input-group button:hover {
    background: #4f46e5;
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
<div id="modal-crear" class="hidden"
     style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:999;
            display:flex;align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:560px;
                max-height:90vh;overflow-y:auto;padding:2rem;position:relative">

        <button onclick="document.getElementById('modal-crear').classList.add('hidden')"
                style="position:absolute;top:1rem;right:1rem;background:none;border:none;
                       font-size:22px;cursor:pointer;color:#94a3b8">
            <i class="ti ti-x"></i>
        </button>

        <h2 style="font-size:17px;font-weight:700;color:#0f172a;margin-bottom:1.4rem">
            Registrar Nuevo Cliente
        </h2>

        <form action="{{ route('admin.clients.store') }}" method="POST">
            @csrf

            <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:.6rem">
                Datos de la Empresa
            </p>

            <div class="form-field" style="margin-bottom:1rem">
                <label style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px">
                    Nombre comercial *
                </label>
                <input type="text" name="name_client" value="{{ old('name_client') }}"
                    placeholder="Ej: Fiesta Tours Perú S.A.C."
                    style="width:100%;padding:.6rem .8rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;margin-top:.4rem;box-sizing:border-box"
                    required>
            </div>

            <div class="form-field" style="margin-bottom:1rem">
                <label style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px">
                    Razón Social *
                </label>
                <input type="text" name="business_name" value="{{ old('business_name') }}"
                    placeholder="Ej: Fiesta Tours Perú S.A.C."
                    style="width:100%;padding:.6rem .8rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;margin-top:.4rem;box-sizing:border-box"
                    required>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.7rem;margin-bottom:1.2rem">
                <div class="form-field">
                    <label style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px">
                        Código tributario (RUC)
                    </label>
                    <input type="text" name="tax_code" value="{{ old('tax_code') }}"
                        placeholder="Ej: 20123456789"
                        style="width:100%;padding:.6rem .8rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;margin-top:.4rem;box-sizing:border-box">
                </div>
                <div class="form-field">
                    <label style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px">
                        Teléfono general
                    </label>
                    <input type="text" name="general_phone" value="{{ old('general_phone') }}"
                        placeholder="Ej: 01-234567"
                        style="width:100%;padding:.6rem .8rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;margin-top:.4rem;box-sizing:border-box">
                </div>
                <div class="form-field">
                    <label style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px">
                        Email general
                    </label>
                    <input type="email" name="general_email" value="{{ old('general_email') }}"
                        placeholder="contacto@empresa.com"
                        style="width:100%;padding:.6rem .8rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;margin-top:.4rem;box-sizing:border-box">
                </div>
            </div>

            <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:.6rem">
                Contactos
            </p>

            <div id="contactos-wrapper"></div>

            <button type="button" onclick="agregarContacto()"
                    style="background:#10b981;color:#fff;border:none;padding:.5rem 1rem;border-radius:8px;
                        font-size:13px;font-weight:600;cursor:pointer;margin-bottom:1.2rem;
                        display:flex;align-items:center;gap:6px">
                <i class="ti ti-plus"></i> Añadir contacto
            </button>

            <div style="display:flex;justify-content:flex-end;gap:.8rem;padding-top:1rem;border-top:1px solid #f1f5f9">
                <button type="button" onclick="document.getElementById('modal-crear').classList.add('hidden')"
                        class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Cliente</button>
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

        {{-- Opción 2: Por ID --}}
        <div class="export-by-id-wrapper">
            <div class="header">
                <div class="icon blue">
                    <i class="ti ti-user"></i>
                </div>
                <div style="flex:1">
                    <div class="title">Cliente específico</div>
                    <div class="sub">Exporta los datos de un solo cliente</div>
                </div>
            </div>
            <div class="input-group">
                <input type="number" id="export-client-id"
                       placeholder="Ingresa el ID del cliente"
                       min="1">
                <button onclick="exportById(document.getElementById('export-client-id').value)">
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

        // Búsqueda texto
        if (search && !name.includes(search) && !email.includes(search) && !phone.includes(search)) {
            return false;
        }

        // Filtro contactos
        if (contacts === '0'  && cnt !== 0) return false;
        if (contacts === '1'  && cnt !== 1) return false;
        if (contacts === '2'  && cnt < 2)   return false;
        if (contacts === '5'  && cnt < 5)   return false;

        // Filtro fecha
        if (date === 'today' && row.dataset.date !== today) return false;
        if (date === 'week'  && rowDate < weekStart)        return false;
        if (date === 'month' && rowDate < monthStart)       return false;
        if (date === 'year'  && rowDate < yearStart)        return false;

        return true;
    });

    // Ordenar
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

    // Aplicar visibilidad
    const tbody = document.getElementById('tabla-body');
    rows.forEach(r => r.style.display = 'none');
    visible.forEach(r => { r.style.display = ''; tbody.appendChild(r); });

    // Mostrar/ocultar "sin resultados"
    const noResults = document.getElementById('no-results');
    const tableContainer = document.getElementById('table-container');
    if (visible.length === 0) {
        noResults.style.display = 'block';
        tableContainer.style.display = 'none';
    } else {
        noResults.style.display = 'none';
        tableContainer.style.display = 'block';
    }

    // Actualizar contador
    const count = visible.length;
    document.getElementById('results-count').textContent = count + ' resultado(s)';
    document.getElementById('footer-count').textContent = count + ' cliente(s)';
    const footerFilter = document.getElementById('footer-filter');
    const hasFilters = search || contacts || date || sort !== 'newest';
    footerFilter.style.display = hasFilters ? 'block' : 'none';

    // Reset checkboxes al filtrar
    deselectAll();
}

function clearFilters() {
    document.getElementById('f-search').value   = '';
    document.getElementById('f-contacts').value = '';
    document.getElementById('f-date').value     = '';
    document.getElementById('f-sort').value     = 'newest';
    applyFilters();
}

// Escuchar cambios
['f-search','f-contacts','f-date','f-sort'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
        el.addEventListener('input',  applyFilters);
        el.addEventListener('change',  applyFilters);
    }
});

// ── Selección múltiple ────────────────────────────────────────
function toggleAll(checked) {
    document.querySelectorAll('.row-check').forEach(cb => {
        // Solo los visibles
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

    // Marcar/desmarcar fila
    document.querySelectorAll('.row-check').forEach(cb => {
        cb.closest('tr').classList.toggle('selected', cb.checked);
    });

    // Check-all indeterminado
    const all = document.querySelectorAll('.row-check');
    const allChecked = [...all].filter(cb => cb.closest('tr').style.display !== 'none');
    const ca = document.getElementById('check-all');
    if (checked.length === 0) { ca.checked = false; ca.indeterminate = false; }
    else if (checked.length === allChecked.length) { ca.checked = true; ca.indeterminate = false; }
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

function agregarContacto() {
    const i = contactoIdx++;
    const label = i === 0 ? 'Contacto #1 — Representante Principal' : `Contacto #${i+1}`;
    const wrapper = document.getElementById('contactos-wrapper');
    const div = document.createElement('div');
    div.id = `contacto-${i}`;
    div.style.cssText = 'border:1px solid #e2e8f0;border-radius:10px;padding:1rem;margin-bottom:.8rem;position:relative';
    div.innerHTML = `
        <div style="font-size:12px;font-weight:700;color:#374151;margin-bottom:.8rem">${label}</div>
        ${i > 0 ? `<button type="button" onclick="document.getElementById('contacto-${i}').remove()"
            style="position:absolute;top:.7rem;right:.7rem;background:none;border:none;
                   cursor:pointer;color:#ef4444;font-size:16px"><i class="ti ti-x"></i></button>` : ''}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.6rem">
            <div>
                <label style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase">NOMBRE *</label>
                <input type="text" name="contacts[${i}][name]" placeholder="Nombre" required
                       style="width:100%;padding:.5rem .7rem;border:1px solid #e2e8f0;border-radius:7px;font-size:13px;margin-top:.3rem;box-sizing:border-box">
            </div>
            <div>
                <label style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase">APELLIDOS</label>
                <input type="text" name="contacts[${i}][last_names]" placeholder="Apellidos"
                       style="width:100%;padding:.5rem .7rem;border:1px solid #e2e8f0;border-radius:7px;font-size:13px;margin-top:.3rem;box-sizing:border-box">
            </div>
            <div style="grid-column:1/3">
                <label style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase">CORREO</label>
                <input type="email" name="contacts[${i}][email]" placeholder="ejemplo@correo.com"
                       style="width:100%;padding:.5rem .7rem;border:1px solid #e2e8f0;border-radius:7px;font-size:13px;margin-top:.3rem;box-sizing:border-box">
            </div>
            <div>
                <label style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase">TELÉFONO 1</label>
                <input type="text" name="contacts[${i}][first_phone]" placeholder="Principal"
                       style="width:100%;padding:.5rem .7rem;border:1px solid #e2e8f0;border-radius:7px;font-size:13px;margin-top:.3rem;box-sizing:border-box">
            </div>
            <div>
                <label style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase">TELÉFONO 2</label>
                <input type="text" name="contacts[${i}][second_phone]" placeholder="Opcional"
                       style="width:100%;padding:.5rem .7rem;border:1px solid #e2e8f0;border-radius:7px;font-size:13px;margin-top:.3rem;box-sizing:border-box">
            </div>
        </div>`;
    wrapper.appendChild(div);
}

@if($errors->any())
    document.getElementById('modal-crear').classList.remove('hidden');
    agregarContacto();
@else
    agregarContacto();
@endif

const totalVisible = rows.length;
document.getElementById('results-count').textContent = totalVisible + ' en esta página';

// ── MODAL EXPORTAR ──────────────────────────────────────────────
let exportType = 'excel';

function openExportModal(type) {
    exportType = type;
    const modal = document.getElementById('modal-export');
    modal.classList.add('show');

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

    document.getElementById('export-client-id').value = '';
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

function exportById(id) {
    if (!id || id < 1) {
        alert('Por favor, ingresa un ID de cliente válido');
        return;
    }

    const url = exportType === 'excel'
        ? '{{ route("admin.clients.export.excel") }}?client_id=' + id
        : '{{ route("admin.clients.export.pdf") }}?client_id=' + id;

    window.location.href = url;
    closeExportModal();
}

// ── EVENTOS PARA BOTONES DE EXPORTACIÓN ──────────────────────
document.getElementById('btn-export-pdf').addEventListener('click', function(e) {
    e.preventDefault();
    openExportModal('pdf');
});

document.getElementById('btn-export-excel').addEventListener('click', function(e) {
    e.preventDefault();
    openExportModal('excel');
});

// ── ENTER EN EL INPUT DE ID ──────────────────────────────────
document.getElementById('export-client-id').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        exportById(this.value);
    }
});

// ── CERRAR MODAL CON ESC ──────────────────────────────────────
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeExportModal();
    }
});

// ── CERRAR MODAL CLICK FUERA ─────────────────────────────────
document.getElementById('modal-export').addEventListener('click', function(e) {
    if (e.target === this) {
        closeExportModal();
    }
});
</script>
@endpush
@endsection
