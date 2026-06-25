@extends('layouts.app')
@section('title', 'Proveedores')
@section('content')

<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.6rem">
    <div>
        <div class="page-title">Proveedores</div>
        <div class="page-sub">Gestiona todos los proveedores del sistema</div>
    </div>
    <div style="display:flex;gap:.6rem;align-items:center;flex-wrap:wrap">
        <a href="{{ route('admin.suppliers.import.view') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:.5rem .9rem;
                  background:#fff;border:1px solid #e2e8f0;border-radius:8px;
                  font-size:13px;font-weight:600;color:#6366f1;text-decoration:none">
            <i class="ti ti-file-upload" style="font-size:16px"></i> Importar
        </a>
        <a href="#" id="btn-export-excel-suppliers"
           style="display:inline-flex;align-items:center;gap:6px;padding:.5rem .9rem;
                  background:#fff;border:1px solid #e2e8f0;border-radius:8px;
                  font-size:13px;font-weight:600;color:#16a34a;text-decoration:none">
            <i class="ti ti-file-type-xls" style="font-size:16px"></i> Excel
        </a>
        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">
            <i class="ti ti-plus" style="font-size:15px"></i> Nuevo proveedor
        </a>
    </div>
</div>

{{-- BARRA DE FILTROS --}}
<div style="margin-bottom:1.2rem; display:flex; flex-wrap:wrap; gap:8px; align-items:center; background:#fff; padding:.8rem 1rem; border-radius:10px; border:1px solid #e2e8f0;">
    {{-- Búsqueda --}}
    <div style="position:relative;flex:1;min-width:200px">
        <i class="ti ti-search" style="position:absolute;left:.7rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:15px"></i>
        <input type="text" id="f-search" class="filter-input"
               placeholder="Buscar por nombre, RUC, email, destino..."
               style="width:100%;padding:.5rem .7rem .5rem 2.2rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;outline:none;transition:border-color .15s;box-sizing:border-box">
    </div>

    <div style="width:1px;height:32px;background:#e2e8f0;flex-shrink:0"></div>

    {{-- Filtro País --}}
    <select id="f-country" class="filter-input" style="min-width:160px;padding:.5rem .7rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;background:#fff;outline:none;transition:border-color .15s;box-sizing:border-box">
        <option value="">Todos los países</option>
        @foreach($countries as $countryName)
            <option value="{{ $countryName }}" {{ $country == $countryName ? 'selected' : '' }}>
                {{ $countryName }}
            </option>
        @endforeach
    </select>

    {{-- Filtro Categoría --}}
    <select id="f-category" class="filter-input" style="min-width:160px;padding:.5rem .7rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;background:#fff;outline:none;transition:border-color .15s;box-sizing:border-box">
        <option value="">Todas las categorías</option>
        @foreach($categories as $id => $catName)
            <option value="{{ $id }}" {{ $category == $id ? 'selected' : '' }}>
                {{ $catName }}
            </option>
        @endforeach
    </select>

    {{-- Ordenamiento --}}
    <select id="f-sort" class="filter-input" style="min-width:170px;padding:.5rem .7rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a;background:#fff;outline:none;transition:border-color .15s;box-sizing:border-box">
        <option value="newest" {{ $sort == 'newest' ? 'selected' : '' }}>Más recientes</option>
        <option value="oldest" {{ $sort == 'oldest' ? 'selected' : '' }}>Más antiguos</option>
        <option value="az" {{ $sort == 'az' ? 'selected' : '' }}>Proveedor A → Z</option>
        <option value="za" {{ $sort == 'za' ? 'selected' : '' }}>Proveedor Z → A</option>
        <option value="tax-az" {{ $sort == 'tax-az' ? 'selected' : '' }}>Código Tributario A → Z</option>
        <option value="tax-za" {{ $sort == 'tax-za' ? 'selected' : '' }}>Código Tributario Z → A</option>
    </select>

    <div style="width:1px;height:32px;background:#e2e8f0;flex-shrink:0"></div>

    {{-- Botón Limpiar --}}
    <button onclick="clearFilters()"
            style="padding:.5rem .9rem;background:none;border:1px solid #e2e8f0;border-radius:8px;font-size:12px;color:#64748b;cursor:pointer;display:flex;align-items:center;gap:5px;white-space:nowrap;transition:all .15s"
            onmouseover="this.style.background='#f1f5f9'"
            onmouseout="this.style.background='none'">
        <i class="ti ti-filter-off" style="font-size:14px"></i> Limpiar
    </button>
</div>

@if($suppliers->isEmpty())
    <div style="text-align:center;padding:4rem;background:#fff;border-radius:14px;border:1px solid #e2e8f0">
        <i class="ti ti-truck-off" style="font-size:48px;color:#cbd5e1;display:block;margin-bottom:1rem"></i>
        @if($search || $country || $category)
            <p style="font-size:15px;font-weight:600;color:#0f172a;margin-bottom:.4rem">No se encontraron proveedores</p>
            <p style="font-size:13px;color:#94a3b8;margin-bottom:1.2rem">
                @if($search) No hay resultados para "{{ $search }}" @endif
                @if($country) en {{ $country }} @endif
                @if($category) en la categoría seleccionada @endif
            </p>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary btn-sm">
                <i class="ti ti-arrow-left"></i> Ver todos los proveedores
            </a>
        @else
            <p style="font-size:15px;font-weight:600;color:#0f172a;margin-bottom:.4rem">No hay proveedores aún</p>
            <p style="font-size:13px;color:#94a3b8;margin-bottom:1.2rem">Comienza agregando tu primer proveedor</p>
            <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary btn-sm">
                <i class="ti ti-plus"></i> Crear proveedor
            </a>
        @endif
    </div>
@else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>PROVEEDORES</th>
                    <th>CÓDIGO TRIBUTARIO</th>
                    <th>EMAIL</th>
                    <th>TELÉFONO</th>
                    <th><i class="ti ti-map-pin" style="font-size:10px"></i>PAIS</th>
                    <th><i class="ti ti-tag" style="font-size:10px"></i>CATEGORIA</th>
                    <th>REGISTRO</th>
                    <th style="text-align:center">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @foreach($suppliers as $s)
                <tr>
                    <td style="color:#cbd5e1;font-size:12px;font-weight:600">#{{ $s->id_supplier }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="avatar-sm" style="background:#f0f9ff;color:#0369a1;display:flex;justify-content:center;align-items:center;width:32px;height:32px;border-radius:50%;font-size:12px;font-weight:bold">
                                {{ strtoupper(substr($s->supplier_name, 0, 2)) }}
                            </div>
                            <span style="font-weight:600">{{ $s->supplier_name }}</span>
                        </div>
                    </td>
                    
                    <td>
                        @if($s->tax_code)
                            <span class="badge" style="background: #fef3c7;color: #92400e;border:1px solid #fde68a;font-size:12px;padding:4px 8px;border-radius:6px">
                                {{ $s->tax_code }}
                            </span>
                        @else
                            <span style="color:#cbd5e1;font-size:12px">—</span>
                        @endif
                    </td>
                    <td>
                        @if($s->general_email)
                            <span style="font-size:12px;color:#0f172a">{{ $s->general_email }}</span>
                        @else
                            <span style="color:#cbd5e1;font-size:12px">—</span>
                        @endif
                    </td>
                    <td>
                        @if($s->general_phone)
                            <span style="font-size:12px;color:#0f172a">{{ $s->general_phone }}</span>
                        @else
                            <span style="color:#cbd5e1;font-size:12px">—</span>
                        @endif
                    </td>
                    <td>
                        @if($s->country_name)
                            <span class="badge" style="background: #b4d6e6;color: #4b6080;border:1px solid #8acffd;padding:4px 8px;border-radius:6px">
                                {{ $s->country_name }}
                            </span>
                        @else
                            <span style="color:#cbd5e1;font-size:12px">—</span>
                        @endif
                    </td>
                    <td>
                        @if($s->category)
                            <span class="badge" style="background:#ede9fe;color:#6d28d9;border:1px solid #ddd6fe;padding:4px 8px;border-radius:6px">
                                {{ $s->category->category_name }}
                            </span>
                        @else
                            <span style="color:#cbd5e1;font-size:12px">—</span>
                        @endif
                    </td>
                    
                    <td style="color:#94a3b8;font-size:12px">{{ $s->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div style="display:flex;justify-content:center;gap:6px">
                            <button type="button" 
                                    class="btn btn-info btn-sm btn-open-modal" 
                                    style="color: #fff; background-color: #3a4c61; border-color:#0284c7;"
                                    data-target="modal-{{ $s->id_supplier }}">
                                <i class="ti ti-eye" style="font-size:13px"></i>
                            </button>
                            <a href="{{ route('admin.suppliers.pdf', $s->id_supplier) }}" 
                                target="_blank"
                                style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:7px;text-decoration:none;color: #fff;background: #a34747fd;border:1px solid #be6868;"
                                title="Exportar PDF">
                                <i class="ti ti-file-type-pdf" style="font-size:13px"></i>
                            </a>

                            <a href="{{ route('admin.suppliers.edit', $s->id_supplier) }}" 
                                style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:7px;text-decoration:none;color: #ffffff;background: #b9c7d6fd;border:1px solid #98b8ce;">
                                <i class="ti ti-edit" style="font-size:13px"></i>
                            </a>
                            
                            <form action="{{ route('admin.suppliers.destroy', $s->id_supplier) }}"
                                method="POST"
                                onsubmit="return confirm('¿Eliminar proveedor {{ addslashes($s->supplier_name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="ti ti-trash" style="font-size:13px"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        {{-- ═══════ PAGINADOR ═══════ --}}
        <div class="table-footer" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem;padding:1rem 1.5rem;border-top:1px solid #e2e8f0">
            <span id="footer-count" style="font-size:13px;color:#64748b">
                Mostrando {{ $suppliers->firstItem() }}–{{ $suppliers->lastItem() }} de {{ $suppliers->total() }} proveedor(es)
            </span>
            
            <div style="display:flex;align-items:center;gap:.4rem">
                @if($suppliers->onFirstPage())
                    <span style="padding:.35rem .7rem;border:1px solid #e2e8f0;border-radius:7px;
                                font-size:12px;color:#cbd5e1;cursor:default">
                        <i class="ti ti-chevron-left"></i>
                    </span>
                @else
                    <a href="{{ $suppliers->previousPageUrl() }}"
                    style="padding:.35rem .7rem;border:1px solid #e2e8f0;border-radius:7px;
                            font-size:12px;color:#374151;text-decoration:none;background:#fff;
                            transition:border-color .15s"
                    onmouseover="this.style.borderColor='#6366f1'"
                    onmouseout="this.style.borderColor='#e2e8f0'">
                        <i class="ti ti-chevron-left"></i>
                    </a>
                @endif

                @foreach($suppliers->getUrlRange(1, $suppliers->lastPage()) as $page => $url)
                    @if($page == $suppliers->currentPage())
                        <span style="padding:.35rem .65rem;border:1px solid #6366f1;border-radius:7px;
                                    font-size:12px;font-weight:700;color:#fff;background:#6366f1;min-width:32px;
                                    text-align:center">
                            {{ $page }}
                        </span>
                    @elseif($page == 1 || $page == $suppliers->lastPage() || abs($page - $suppliers->currentPage()) <= 1)
                        <a href="{{ $url }}"
                        style="padding:.35rem .65rem;border:1px solid #e2e8f0;border-radius:7px;
                                font-size:12px;color:#374151;text-decoration:none;background:#fff;min-width:32px;
                                text-align:center;transition:border-color .15s"
                        onmouseover="this.style.borderColor='#6366f1'"
                        onmouseout="this.style.borderColor='#e2e8f0'">
                            {{ $page }}
                        </a>
                    @elseif(abs($page - $suppliers->currentPage()) == 2)
                        <span style="font-size:12px;color:#94a3b8;padding:0 .2rem">…</span>
                    @endif
                @endforeach

                @if($suppliers->hasMorePages())
                    <a href="{{ $suppliers->nextPageUrl() }}"
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
            
            @if($search || $country || $category)
                <span id="footer-filter" style="color:#6366f1;font-size:12px;">
                    Resultados filtrados
                </span>
            @endif
        </div>
    </div>

    {{-- MODALES --}}
    @foreach($suppliers as $s)
    <div id="modal-{{ $s->id_supplier }}" class="custom-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(15, 23, 42, 0.6); backdrop-filter:blur(4px); z-index:9999; justify-content:center; align-items:center; padding:1rem;">
        
        <div class="custom-modal-content" style="background: #fff; width:100%; max-width:800px; border-radius:14px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); display:flex; flex-direction:column; max-height:90vh; animation: modalFadeIn 0.2s ease-out;">
            
            <div style="background: #f8fafc; border-bottom:1px solid #e2e8f0; border-top-left-radius:14px; border-top-right-radius:14px; padding:1.2rem 1.5rem; display:flex; justify-content:space-between; align-items:center">
                <div style="display:flex; align-items:center; gap:12px">
                    <div style="background: #e0f2fe; color: #21354d; width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center">
                        <i class="ti ti-truck" style="font-size:22px"></i>
                    </div>
                    <div>
                        <h5 style="font-weight:700; color:#0f172a; margin:0; font-size:16px">{{ $s->supplier_name }}</h5>
                        <span style="font-size:12px; color:#64748b">ID Proveedor: #{{ $s->id_supplier }}</span>
                    </div>
                </div>
                <button type="button" class="btn-close-modal" data-close="modal-{{ $s->id_supplier }}" style="background:none; border:none; color:#64748b; cursor:pointer; font-size:20px; line-height:1">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            <div style="padding:1.5rem; overflow-y:auto; flex:1;">
                
                <div class="custom-tabs" data-supplier="{{ $s->id_supplier }}" style="display:flex; gap:8px; border-bottom:1px solid #f1f5f9; padding-bottom:12px; margin-bottom:1.5rem">
                    <button class="tab-trigger active" data-tab="info" style="border:none; cursor:pointer; font-size:13px; font-weight:600; padding:8px 16px; border-radius:8px; background: #21374d; color:#fff; display:flex; align-items:center; gap:6px">
                        <i class="ti ti-info-circle"></i> Info. General
                    </button>
                    <button class="tab-trigger" data-tab="contacts" style="border:none; cursor:pointer; font-size:13px; font-weight:600; padding:8px 16px; border-radius:8px; background:#f1f5f9; color:#475569; display:flex; align-items:center; gap:6px">
                        <i class="ti ti-users"></i> Contactos ({{ $s->contacts->count() }})
                    </button>
                    <button class="tab-trigger" data-tab="banks" style="border:none; cursor:pointer; font-size:13px; font-weight:600; padding:8px 16px; border-radius:8px; background:#f1f5f9; color:#475569; display:flex; align-items:center; gap:6px">
                        <i class="ti ti-credit-card"></i> Cuentas Bancarias ({{ $s->bankAccounts->count() }})
                    </button>
                </div>

                <div class="custom-tab-contents" data-supplier="{{ $s->id_supplier }}">
                    
                    <div class="tab-content-panel panel-info" style="display:block">
                        <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:1.2rem">
                            <div>
                                <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Razón Social</label>
                                <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $s->business_name ?? '—' }}</div>
                            </div>
                            <div>
                                <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Código Tributario / RUC</label>
                                <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $s->tax_code ?? '—' }}</div>
                            </div>
                            <div>
                                <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Teléfono General</label>
                                <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $s->general_phone ?? '—' }}</div>
                            </div>
                            <div>
                                <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Correo General</label>
                                <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $s->general_email ?? '—' }}</div>
                            </div>
                            <div>
                                <label style="display:block; font-size:11px; font-weight:700; color: #94a3b8; text-transform:uppercase; margin-bottom:4px">País</label>
                                <div style="font-size:14px; color: #1e293b; font-weight:500">{{ $s->country_name ? $s->country_name : '—' }}</div>
                            </div>
                            <div>
                                <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Ciudad</label>
                                <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $s->city_name ? $s->city_name : '—' }}</div>
                            </div>
                            <div>
                                <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Dirección</label>
                                <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $s->address ? $s->address : '—' }}</div>
                            </div>
                            <div>
                                <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Categoría</label>
                                <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $s->category ? $s->category->category_name : '—' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-content-panel panel-contacts" style="display:none">
                        @if($s->contacts->isEmpty())
                            <div style="text-align:center; padding:2rem; color:#94a3b8">
                                <i class="ti ti-users-minus" style="font-size:32px; display:block; margin-bottom:8px"></i>
                                <span style="font-size:13px">No hay contactos registrados para este proveedor.</span>
                            </div>
                        @else
                            <div style="display:flex; flex-direction:column; gap:12px">
                                @foreach($s->contacts as $contact)
                                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:1rem; display:flex; justify-content:space-between; align-items:center">
                                        <div style="display:flex; align-items:center; gap:12px">
                                            <div style="background:#f1f5f9; color:#475569; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px">
                                                {{ strtoupper(substr($contact->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div style="font-size:14px; font-weight:600; color:#0f172a">
                                                    {{ $contact->name }} {{ $contact->last_names }}
                                                    @if($contact->es_principal)
                                                        <span style="background:#dcfce7; color:#15803d; font-size:10px; font-weight:700; padding:2px 6px; border-radius:4px; margin-left:6px; text-transform:uppercase">Principal</span>
                                                    @endif
                                                </div>
                                                <div style="font-size:12px; color:#64748b">{{ $contact->qualification ?? 'Sin cargo asignado' }}</div>
                                            </div>
                                        </div>
                                        <div style="text-align:right; font-size:12px; color:#334155">
                                            <div><i class="ti ti-mail" style="color:#94a3b8"></i> {{ $contact->email ?? '—' }}</div>
                                            <div><i class="ti ti-phone" style="color:#94a3b8"></i> {{ $contact->first_phone }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="tab-content-panel panel-banks" style="display:none">
                        @if($s->bankAccounts->isEmpty())
                            <div style="text-align:center; padding:2rem; color:#94a3b8">
                                <i class="ti ti-credit-card-off" style="font-size:32px; display:block; margin-bottom:8px"></i>
                                <span style="font-size:13px">No hay cuentas bancarias registradas.</span>
                            </div>
                        @else
                            <div style="border:1px solid #e2e8f0; border-radius:10px; overflow:hidden">
                                <table style="width:100%; margin:0; font-size:13px; border-collapse:collapse">
                                    <thead style="background:#f8fafc">
                                        <tr>
                                            <th style="padding:10px 14px; font-weight:600; color:#64748b; text-align:left">Banco</th>
                                            <th style="padding:10px 14px; font-weight:600; color:#64748b; text-align:left">N° Cuenta</th>
                                            <th style="padding:10px 14px; font-weight:600; color:#64748b; text-align:left">CCI</th>
                                            <th style="padding:10px 14px; font-weight:600; color:#64748b; text-align:left">Moneda</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($s->bankAccounts as $account)
                                            <tr style="border-top:1px solid #f1f5f9">
                                                <td style="padding:12px 14px; font-weight:600; color:#1e293b">{{ $account->bank ? $account->bank->bank_name : '—' }}</td>
                                                <td style="padding:12px 14px; color:#475569; font-family:monospace">{{ $account->account_number }}</td>
                                                <td style="padding:12px 14px; color:#475569; font-family:monospace">{{ $account->cci ?? '—' }}</td>
                                                <td style="padding:12px 14px">
                                                    <span style="background:#f1f5f9; color:#1e293b; font-weight:600; padding:4px 8px; border-radius:4px">{{ $account->currency ?? '—' }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                </div>
            </div>

            <div style="background:#f8fafc; border-top:1px solid #e2e8f0; border-bottom-left-radius:14px; border-bottom-right-radius:14px; padding:0.8rem 1.5rem; display:flex; justify-content:flex-end">
                <button type="button" class="btn-close-modal" data-close="modal-{{ $s->id_supplier }}" style="font-size:13px; background:#64748b; color:#fff; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-weight:500">Cerrar</button>
            </div>

        </div>
    </div>
    @endforeach
@endif

{{-- ══════════ MODAL EXPORTAR ══════════ --}}
<div id="modal-export-suppliers">
    <div class="modal-box">
        <button class="modal-close" onclick="closeExportSuppliersModal()">
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

        <div class="export-option" onclick="exportAllSuppliers()">
            <div class="icon green">
                <i class="ti ti-list"></i>
            </div>
            <div style="flex:1">
                <div class="title">Todos los proveedores</div>
                <div class="sub">Exporta el listado completo de proveedores</div>
            </div>
            <i class="ti ti-chevron-right arrow"></i>
        </div>

        <div class="export-by-id-wrapper">
            <div class="header">
                <div class="icon blue">
                    <i class="ti ti-truck"></i>
                </div>
                <div style="flex:1">
                    <div class="title">Proveedor específico</div>
                    <div class="sub">Busca y elige un proveedor por nombre</div>
                </div>
            </div>
            <div style="position:relative; margin-top:.8rem; padding-left:52px; display:flex; gap:.6rem; align-items:center">
                <div style="flex:1; position:relative">
                    <input type="text"
                           id="export-supplier-search"
                           placeholder="Escribe para buscar proveedor..."
                           autocomplete="off"
                           style="width:100%; padding:.5rem .7rem; border:1px solid #e2e8f0;
                                  border-radius:7px; font-size:13px; outline:none;
                                  transition:border-color .15s; box-sizing:border-box">
                    <button type="button" id="export-supplier-clear"
                            style="display:none; position:absolute; right:.5rem; top:50%;
                                   transform:translateY(-50%); background:none; border:none;
                                   color:#cbd5e1; cursor:pointer; font-size:14px; padding:2px; line-height:1">
                        <i class="ti ti-x"></i>
                    </button>
                    <div id="export-supplier-list"
                         style="display:none; position:absolute; top:calc(100% + 4px); left:0; right:0;
                                background:#fff; border:1px solid #e2e8f0; border-radius:9px;
                                max-height:200px; overflow-y:auto; z-index:60;
                                box-shadow:0 10px 25px -5px rgba(0,0,0,.1)">
                    </div>
                </div>
                <button id="export-supplier-btn"
                        onclick="exportSelectedSupplier()"
                        disabled
                        style="padding:.5rem 1rem; background:#6366f1; color:#fff; border:none;
                               border-radius:7px; font-size:13px; font-weight:600; cursor:pointer;
                               transition:background .15s; white-space:nowrap; opacity:.45">
                    <i class="ti ti-arrow-right" style="font-size:14px"></i> Exportar
                </button>
            </div>
        </div>

        <button class="btn-cancel-export" onclick="closeExportSuppliersModal()">
            Cancelar
        </button>
    </div>
</div>

<style>
@keyframes modalFadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

#modal-export-suppliers {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    z-index: 1000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 1.2rem;
}
#modal-export-suppliers.show { display: flex; }

#modal-export-suppliers .modal-box {
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

#modal-export-suppliers .modal-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: none;
    border: none;
    font-size: 22px;
    cursor: pointer;
    color: #94a3b8;
}
#modal-export-suppliers .modal-close:hover { color: #0f172a; }

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
.export-by-id-wrapper .icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.export-by-id-wrapper .icon.blue { background: #eff6ff; color: #3b82f6; }
.export-by-id-wrapper .title { font-size: 14px; font-weight: 600; color: #0f172a; }
.export-by-id-wrapper .sub { font-size: 12px; color: #94a3b8; }

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
    margin-top: .4rem;
}
.btn-cancel-export:hover { background: #e2e8f0; }

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
.export-option .title { font-size: 14px; font-weight: 600; color: #0f172a; }
.export-option .sub { font-size: 12px; color: #94a3b8; }
.export-option .arrow { color: #94a3b8; font-size: 18px; margin-left: auto; }

.filter-input:focus {
    border-color: #6366f1 !important;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    
    // 1. ABRIR MODAL
    document.querySelectorAll('.btn-open-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            const modalId = this.getAttribute('data-target');
            const modal = document.getElementById(modalId);
            if(modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        });
    });

    // 2. CERRAR MODAL
    document.querySelectorAll('.btn-close-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            const modalId = this.getAttribute('data-close');
            const modal = document.getElementById(modalId);
            if(modal) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        });
    });

    // Cierre si hacen clic fuera de la caja blanca del modal
    document.querySelectorAll('.custom-modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if(e.target === this) {
                this.style.display = 'none';
                document.body.style.overflow = '';
            }
        });
    });

    // 3. CONTROL DE TABS/PESTAÑAS INTERNAS
    document.querySelectorAll('.custom-tabs .tab-trigger').forEach(tabBtn => {
        tabBtn.addEventListener('click', function() {
            const parentTabs = this.closest('.custom-tabs');
            const supplierId = parentTabs.getAttribute('data-supplier');
            const targetPanelName = this.getAttribute('data-tab');

            parentTabs.querySelectorAll('.tab-trigger').forEach(btn => {
                btn.style.background = '#f1f5f9';
                btn.style.color = '#475569';
            });

            this.style.background = '#19283b';
            this.style.color = '#fff';

            const contentsContainer = document.querySelector(`.custom-tab-contents[data-supplier="${supplierId}"]`);
            contentsContainer.querySelectorAll('.tab-content-panel').forEach(panel => {
                panel.style.display = 'none';
            });

            const activePanel = contentsContainer.querySelector(`.panel-${targetPanelName}`);
            if(activePanel) {
                activePanel.style.display = 'block';
            }
        });
    });

    // 4. FILTROS EN TIEMPO REAL
    const searchInput = document.getElementById('f-search');
    const countrySelect = document.getElementById('f-country');
    const categorySelect = document.getElementById('f-category');
    const sortSelect = document.getElementById('f-sort');

    function applyFilters() {
        const search = searchInput.value;
        const country = countrySelect.value;
        const category = categorySelect.value;
        const sort = sortSelect.value;
        
        let url = '{{ route("admin.suppliers.index") }}';
        let params = new URLSearchParams();
        
        if (search) params.set('search', search);
        if (country) params.set('country', country);
        if (category) params.set('category', category);
        if (sort && sort !== 'newest') params.set('sort', sort);
        
        const queryString = params.toString();
        if (queryString) {
            window.location.href = url + '?' + queryString;
        } else {
            window.location.href = url;
        }
    }

    // Delay para búsqueda (debounce)
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 400);
    });

    countrySelect.addEventListener('change', applyFilters);
    categorySelect.addEventListener('change', applyFilters);
    sortSelect.addEventListener('change', applyFilters);

    // Enter en búsqueda
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(searchTimeout);
            applyFilters();
        }
    });
});

// 5. LIMPIAR FILTROS
function clearFilters() {
    const searchInput = document.getElementById('f-search');
    const countrySelect = document.getElementById('f-country');
    const categorySelect = document.getElementById('f-category');
    const sortSelect = document.getElementById('f-sort');
    
    searchInput.value = '';
    countrySelect.value = '';
    categorySelect.value = '';
    sortSelect.value = 'newest';
    
    window.location.href = '{{ route("admin.suppliers.index") }}';
}

// ══════════ EXPORTAR ══════════
let exportSupplierId = null;
let exportType = 'excel';

// Rutas desde Laravel
const EXPORT_EXCEL_ALL = '{{ route("admin.suppliers.export.excel") }}';
const EXPORT_EXCEL_SINGLE = '{{ route("admin.suppliers.export.excel") }}';

// Datos de proveedores desde PHP
const suppliersData = @json($suppliers->map(fn($s) => ['id' => $s->id_supplier, 'name' => $s->supplier_name, 'tax_code' => $s->tax_code]));

function exportAllSuppliers() {
    const url = EXPORT_EXCEL_ALL;
    window.location.href = url;
    closeExportSuppliersModal();
}

function exportSelectedSupplier() {
    if (!exportSupplierId) {
        alert('Por favor, selecciona un proveedor primero.');
        return;
    }
    window.location.href = EXPORT_EXCEL_SINGLE + '?supplier_id=' + exportSupplierId;
    closeExportSuppliersModal();
}

function openExportSuppliersModal() {
    document.getElementById('modal-export-suppliers').classList.add('show');
    
    const label = document.getElementById('export-type-label');
    const icon = document.getElementById('export-modal-icon');
    
    label.textContent = 'Excel';
    label.style.color = '#16a34a';
    icon.style.color = '#16a34a';
    
    const input = document.getElementById('export-supplier-search');
    const clear = document.getElementById('export-supplier-clear');
    const list = document.getElementById('export-supplier-list');
    const btn = document.getElementById('export-supplier-btn');
    input.value = '';
    input.style.borderColor = '#e2e8f0';
    clear.style.display = 'none';
    list.style.display = 'none';
    btn.disabled = true;
    btn.style.opacity = '.45';
    btn.style.cursor = 'default';
    exportSupplierId = null;
}

function closeExportSuppliersModal() {
    document.getElementById('modal-export-suppliers').classList.remove('show');
}

document.getElementById('btn-export-excel-suppliers').addEventListener('click', function(e) {
    e.preventDefault();
    exportType = 'excel';
    openExportSuppliersModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeExportSuppliersModal();
});

document.getElementById('modal-export-suppliers').addEventListener('click', function(e) {
    if (e.target === this) closeExportSuppliersModal();
});

// ── COMBO BUSCADOR PARA EXPORTAR ──
(function initExportCombo() {
    const input = document.getElementById('export-supplier-search');
    const list = document.getElementById('export-supplier-list');
    const clear = document.getElementById('export-supplier-clear');
    const btn = document.getElementById('export-supplier-btn');
    let activeIdx = -1;
    let filtered = [];

    function normalizeStr(str) {
        return (str || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }

    function renderList(term) {
        const q = normalizeStr(term);
        filtered = q
            ? suppliersData.filter(s => normalizeStr(s.name).includes(q))
            : suppliersData.slice(0, 50);

        if (filtered.length === 0) {
            list.innerHTML = '<div style="padding:.6rem .8rem;font-size:12.5px;color:#94a3b8">Sin resultados</div>';
        } else {
            list.innerHTML = filtered.map((s, i) =>
                `<div data-idx="${i}"
                      style="padding:.55rem .8rem;font-size:13px;color:#0f172a;cursor:pointer;
                             transition:background .1s">
                    <span style="color:#94a3b8;font-size:11px;margin-right:6px">#${s.id}</span>${s.name}
                </div>`
            ).join('');
        }
        activeIdx = -1;
        list.style.display = 'block';
    }

    function selectSupplier(s) {
        input.value = s.name + (s.tax_code ? ' · ' + s.tax_code : '');
        exportSupplierId = s.id;
        clear.style.display = 'block';
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
        list.style.display = 'none';
        input.style.borderColor = '#6366f1';
    }

    function clearSelection() {
        input.value = '';
        exportSupplierId = null;
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
        exportSupplierId = null;
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
            if (activeIdx >= 0 && filtered[activeIdx]) selectSupplier(filtered[activeIdx]);
        } else if (e.key === 'Escape') {
            list.style.display = 'none';
        }
    });

    list.addEventListener('mousedown', e => {
        e.preventDefault();
        const item = e.target.closest('[data-idx]');
        if (!item) return;
        selectSupplier(filtered[parseInt(item.dataset.idx)]);
    });

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
</script>
@endsection