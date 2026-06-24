@extends('layouts.app')
@section('title', 'Clientes')

@push('styles')
<link href="{{ asset('css/clients-index.css') }}" rel="stylesheet">
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
               placeholder="Buscar por agencia, código tributario, email, teléfono..."
               style="width:100%;padding-left:2.2rem">
    </div>

    <div class="filter-sep"></div>

    <select id="f-country" class="filter-input" style="min-width:160px">
        <option value="">Todos los países</option>
        @foreach($countries as $country)
            <option value="{{ $country }}">{{ $country }}</option>
        @endforeach
    </select>

    <select id="f-city" class="filter-input" style="min-width:150px">
        <option value="">Todas las ciudades</option>
        @foreach($cities as $city)
            <option value="{{ $city }}">{{ $city }}</option>
        @endforeach
    </select>

    <select id="f-date" class="filter-input" style="min-width:150px">
        <option value="">Cualquier fecha</option>
        <option value="today">Hoy</option>
        <option value="week">Esta semana</option>
        <option value="month">Este mes</option>
        <option value="year">Este año</option>
    </select>

    <select id="f-sort" class="filter-input" style="min-width:170px">
        <option value="newest">Más recientes</option>
        <option value="oldest">Más antiguos</option>
        <option value="az">Agencia A → Z</option>
        <option value="za">Agencia Z → A</option>
        <option value="tax-az">Código Tributario A → Z</option>
        <option value="tax-za">Código Tributario Z → A</option>
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
        <a href="{{ route('admin.clients.create') }}" class="btn btn-primary btn-sm" style="text-decoration:none">
            <i class="ti ti-plus"></i> Crear primer cliente
        </a>
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
                    <tr class="client-row"
                        data-id="{{ $c->id_client }}"
                        data-name="{{ strtolower($c->name_client) }}"
                        data-code="{{ strtolower($c->tax_code ?? '') }}"
                        data-type="{{ strtolower($c->type_client) ?? '' }}"
                        data-email="{{ strtolower($c->general_email ?? '') }}"
                        data-phone="{{ $c->general_phone ?? '' }}"
                        data-country="{{ $c->country_name ?? '' }}"
                        data-city="{{ $c->city_name ?? '' }}"
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
    // Variables para el JS
    window.clientsData = @json($clients->map(fn($c) => ['id' => $c->id_client, 'name' => $c->name_client]));
    window.exportExcelUrl = '{{ route("admin.clients.export.excel") }}';
    window.exportPdfUrl = '{{ route("admin.clients.export.pdf") }}';
</script>
<script src="{{ asset('js/clients-index.js') }}"></script>
@endpush
@endsection
