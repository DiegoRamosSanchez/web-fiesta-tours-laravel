@extends('layouts.app')
@section('title', 'Proveedores')
@section('content')

<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.6rem">
    <div>
        <div class="page-title">Proveedores</div>
        <div class="page-sub">Gestiona todos los proveedores del sistema</div>
    </div>
    <div style="display:flex;gap:.6rem;align-items:center;flex-wrap:wrap">
        <a href="#" id="btn-export-pdf-suppliers"
           style="display:inline-flex;align-items:center;gap:6px;padding:.5rem .9rem;
                  background:#fff;border:1px solid #e2e8f0;border-radius:8px;
                  font-size:13px;font-weight:600;color:#ef4444;text-decoration:none">
            <i class="ti ti-file-type-pdf" style="font-size:16px"></i> PDF
        </a>
        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">
            <i class="ti ti-plus" style="font-size:15px"></i> Nuevo proveedor
        </a>
    </div>
</div>

<div style="margin-bottom:1.2rem">
    <form method="GET" action="{{ route('admin.suppliers.index') }}" style="display:flex;gap:8px;max-width:420px">
        <div style="position:relative;flex:1">
            <i class="ti ti-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:14px"></i>
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Buscar por nombre, RUC, email, destino..."
                style="width:100%;padding:9px 12px 9px 34px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#0f172a"
            >
        </div>
        <button type="submit" class="btn btn-secondary btn-sm">Buscar</button>
        @if($search)
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-light btn-sm" style="display:flex;align-items:center">
                <i class="ti ti-x" style="font-size:13px"></i>
            </a>
        @endif
    </form>
</div>


@if($suppliers->isEmpty())
    <div style="text-align:center;padding:4rem;background:#fff;border-radius:14px;border:1px solid #e2e8f0">
        <i class="ti ti-truck-off" style="font-size:48px;color:#cbd5e1;display:block;margin-bottom:1rem"></i>
        @if($search)
            <p style="font-size:15px;font-weight:600;color:#0f172a;margin-bottom:.4rem">No se encontraron proveedores</p>
            <p style="font-size:13px;color:#94a3b8;margin-bottom:1.2rem">No hay resultados para "{{ $search }}"</p>
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
                    <th>DESTINO</th>
                    <th>CATEGORIA</th>
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
                            <span class="badge" style="background:#fef3c7;color:#92400e;border:1px solid #fde68a;font-size:12px;padding:4px 8px;border-radius:6px">
                                <i class="ti ti-receipt" style="font-size:10px"></i>
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
                        @if($s->destination)
                            <span class="badge" style="background:#fef3c7;color:#92400e;border:1px solid #fde68a;padding:4px 8px;border-radius:6px">
                                <i class="ti ti-map-pin" style="font-size:10px"></i>
                                {{ $s->destination->destination_name }}
                            </span>
                        @else
                            <span style="color:#cbd5e1;font-size:12px">—</span>
                        @endif
                    </td>
                    <td>
                        @if($s->category)
                            <span class="badge" style="background:#ede9fe;color:#6d28d9;border:1px solid #ddd6fe;padding:4px 8px;border-radius:6px">
                                <i class="ti ti-tag" style="font-size:10px"></i>
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

                            <a href="{{ route('admin.suppliers.edit', $s->id_supplier) }}" class="btn btn-secondary btn-sm">
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
        
        {{-- PAGINADOR CORREGIDO --}}
        @if($suppliers->hasPages())
            <div class="table-footer" style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;padding:1rem 1.5rem;border-top:1px solid #e2e8f0">
                <span style="font-size:13px;color:#64748b">
                    Mostrando {{ $suppliers->firstItem() }}–{{ $suppliers->lastItem() }} de {{ $suppliers->total() }} proveedor(es)
                </span>
                <div>
                    {{ $suppliers->appends(['search' => $search])->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @else
            <div class="table-footer" style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;padding:1rem 1.5rem;border-top:1px solid #e2e8f0">
                <span style="font-size:13px;color:#64748b">
                    Mostrando {{ $suppliers->firstItem() }}–{{ $suppliers->lastItem() }} de {{ $suppliers->total() }} proveedor(es)
                </span>
            </div>
        @endif
    </div>

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
                                <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:4px">Destino / Ubicación</label>
                                <div style="font-size:14px; color:#1e293b; font-weight:500">{{ $s->destination ? $s->destination->destination_name : '—' }}</div>
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

{{-- ══════════ MODAL EXPORTAR PDF (SOLO PROVEEDOR ESPECÍFICO) ══════════ --}}
<div id="modal-export-suppliers">
    <div class="modal-box">
        <button class="modal-close" onclick="closeExportSuppliersModal()">
            <i class="ti ti-x"></i>
        </button>

        <div style="display:flex;align-items:center;gap:12px;margin-bottom:1.5rem">
            <div style="width:44px;height:44px;background:#f0fdf4;border-radius:50%;
                        display:flex;align-items:center;justify-content:center;font-size:22px">
                <i class="ti ti-file-export" style="color:#2f7d4f"></i>
            </div>
            <div>
                <h2 style="font-size:17px;font-weight:700;color:#0f172a;margin:0">
                    Exportar <span style="color:#2f7d4f">PDF</span>
                </h2>
                <p style="font-size:12px;color:#94a3b8;margin:0">Selecciona un proveedor para exportar</p>
            </div>
        </div>

        {{-- Única opción: Buscar proveedor específico --}}
        <div class="export-search-wrapper">
            <div class="header">
                <div class="icon">
                    <i class="ti ti-truck"></i>
                </div>
                <div style="flex:1">
                    <div class="title">Proveedor específico</div>
                    <div class="sub">Busca por nombre y exporta solo ese proveedor</div>
                </div>
            </div>

            <div id="supplier-search-box">
                <input type="text" id="supplier-search-input"
                       placeholder="Escribe el nombre del proveedor..."
                       autocomplete="off">
                <div id="supplier-search-results"></div>
            </div>

            <div id="supplier-selected-chip">
                <div class="chip">
                    <span id="supplier-selected-name"></span>
                    <button type="button" onclick="clearSelectedSupplier()" title="Quitar selección">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
            </div>

            <button id="btn-export-supplier-confirm" onclick="exportSelectedSupplier()">
                <i class="ti ti-download" style="font-size:13px"></i> Exportar este proveedor
            </button>
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

.export-search-wrapper {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem 1.2rem;
    transition: all .2s;
    margin-bottom: .8rem;
}
.export-search-wrapper:hover { border-color: #6366f1; }
.export-search-wrapper .header { display: flex; align-items: center; gap: 12px; }
.export-search-wrapper .icon {
    width: 40px; height: 40px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
    background: #eff6ff; color: #3b82f6;
}
.export-search-wrapper .title { font-size: 14px; font-weight: 600; color: #0f172a; }
.export-search-wrapper .sub { font-size: 12px; color: #94a3b8; }

#supplier-search-box {
    position: relative;
    margin-top: .8rem;
    padding-left: 0;
}
#supplier-search-input {
    width: 100%;
    padding: .55rem .8rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
    outline: none;
    box-sizing: border-box;
}
#supplier-search-input:focus { border-color: #6366f1; }

#supplier-search-results {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 8px 20px -4px rgba(0,0,0,.15);
    max-height: 220px;
    overflow-y: auto;
    z-index: 10;
    display: none;
}
#supplier-search-results.show { display: block; }

.supplier-result-item {
    padding: .6rem .8rem;
    cursor: pointer;
    font-size: 13px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.supplier-result-item:last-child { border-bottom: none; }
.supplier-result-item:hover { background: #f8fafc; }
.supplier-result-item .name { font-weight: 600; color: #0f172a; }
.supplier-result-item .meta { font-size: 11px; color: #94a3b8; }

.supplier-result-empty {
    padding: .8rem;
    font-size: 12.5px;
    color: #94a3b8;
    text-align: center;
}

#supplier-selected-chip {
    display: none;
    align-items: center;
    justify-content: space-between;
    margin-top: .6rem;
    padding-left: 0;
}
#supplier-selected-chip.show { display: flex; }
#supplier-selected-chip .chip {
    background: #eef2ff;
    border: 1px solid #c7d2fe;
    border-radius: 8px;
    padding: .5rem .8rem;
    font-size: 13px;
    color: #4338ca;
    font-weight: 600;
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}
#supplier-selected-chip .chip button {
    background: none; border: none; cursor: pointer; color: #6366f1; font-size: 15px;
}

#btn-export-supplier-confirm {
    display: none;
    margin-top: .7rem;
    padding: .55rem;
    background: #6366f1;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    width: 100%;
}
#btn-export-supplier-confirm.show { display: block; }
#btn-export-supplier-confirm:hover { background: #4f46e5; }

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
});

// ══════════ EXPORTAR PDF: buscador con autocompletar ══════════

let selectedSupplierId = null;

function openExportSuppliersModal() {
    document.getElementById('modal-export-suppliers').classList.add('show');
    // Limpiar estado previo
    clearSelectedSupplier();
    document.getElementById('supplier-search-input').value = '';
    document.getElementById('supplier-search-input').style.display = '';
}

function closeExportSuppliersModal() {
    document.getElementById('modal-export-suppliers').classList.remove('show');
    clearSelectedSupplier();
    document.getElementById('supplier-search-results').classList.remove('show');
}

function clearSelectedSupplier() {
    selectedSupplierId = null;
    document.getElementById('supplier-selected-chip').classList.remove('show');
    document.getElementById('btn-export-supplier-confirm').classList.remove('show');
    document.getElementById('supplier-search-input').value = '';
    document.getElementById('supplier-search-input').style.display = '';
}

function selectSupplier(id, name, taxCode) {
    selectedSupplierId = id;
    document.getElementById('supplier-selected-name').textContent =
        name + (taxCode ? ' · ' + taxCode : '');
    document.getElementById('supplier-selected-chip').classList.add('show');
    document.getElementById('btn-export-supplier-confirm').classList.add('show');
    document.getElementById('supplier-search-results').classList.remove('show');
    document.getElementById('supplier-search-input').style.display = 'none';
}

// Buscador con autocompletar
document.getElementById('supplier-search-input').addEventListener('input', function() {
    const query = this.value.trim();
    const resultsContainer = document.getElementById('supplier-search-results');

    if (query.length < 2) {
        resultsContainer.classList.remove('show');
        return;
    }

    fetch(`/admin/suppliers/search?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            resultsContainer.innerHTML = '';
            if (data.length === 0) {
                resultsContainer.innerHTML = '<div class="supplier-result-empty">No se encontraron proveedores</div>';
                resultsContainer.classList.add('show');
                return;
            }

            data.forEach(supplier => {
                const div = document.createElement('div');
                div.className = 'supplier-result-item';
                div.innerHTML = `
                    <span class="name">${supplier.supplier_name}</span>
                    <span class="meta">${supplier.tax_code || 'Sin RUC'} · ${supplier.business_name || 'Sin razón social'}</span>
                `;
                div.addEventListener('click', function() {
                    selectSupplier(supplier.id_supplier, supplier.supplier_name, supplier.tax_code);
                });
                resultsContainer.appendChild(div);
            });
            resultsContainer.classList.add('show');
        })
        .catch(() => {
            resultsContainer.innerHTML = '<div class="supplier-result-empty">Error al buscar proveedores</div>';
            resultsContainer.classList.add('show');
        });
});

// Cerrar resultados al hacer clic fuera
document.addEventListener('click', function(e) {
    const box = document.getElementById('supplier-search-box');
    if (box && !box.contains(e.target)) {
        document.getElementById('supplier-search-results').classList.remove('show');
    }
});

// Botón header
document.getElementById('btn-export-pdf-suppliers').addEventListener('click', function(e) {
    e.preventDefault();
    openExportSuppliersModal();
});

// Exportar proveedor seleccionado
function exportSelectedSupplier() {
    if (!selectedSupplierId) {
        alert('Por favor, selecciona un proveedor primero.');
        return;
    }
    window.location.href = `/admin/suppliers/${selectedSupplierId}/pdf`;
    closeExportSuppliersModal();
}

// Cerrar con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeExportSuppliersModal();
});

// Cerrar clic fuera del modal
document.getElementById('modal-export-suppliers').addEventListener('click', function(e) {
    if (e.target === this) closeExportSuppliersModal();
});
</script>

@endsection