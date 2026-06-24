@extends('layouts.app')
@section('title', 'Contactos')

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

    input[type="checkbox"] {
        width: 15px; height: 15px;
        accent-color: #e63232;
        cursor: pointer;
    }
    tr.selected td { background: #fef2f2 !important; }

    .results-count {
        font-size: 12px;
        color: #94a3b8;
        margin-left: auto;
    }

    .avatar-sm {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .badge {
        display: inline-block;
        padding: .2rem .65rem;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
    }

    .btn-sm {
        padding: .3rem .7rem;
        font-size: 12px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
    }
    .btn-secondary:hover { background: #e2e8f0; }
    .btn-danger {
        background: #fef2f2;
        color: #ef4444;
    }
    .btn-danger:hover { background: #fee2e2; }

    .pagination-controls {
        display: flex;
        align-items: center;
        gap: .4rem;
        flex-wrap: wrap;
    }
    .pagination-controls a,
    .pagination-controls span.page-current {
        padding: .35rem .65rem;
        border: 1px solid #e2e8f0;
        border-radius: 7px;
        font-size: 12px;
        color: #374151;
        text-decoration: none;
        background: #fff;
        min-width: 32px;
        text-align: center;
        transition: border-color .15s;
    }
    .pagination-controls a:hover { border-color: #6366f1; }
    .pagination-controls span.page-current {
        border-color: #6366f1;
        color: #fff;
        background: #6366f1;
        font-weight: 700;
    }
    .pagination-controls .disabled {
        color: #cbd5e1;
        cursor: default;
        border-color: #e2e8f0;
    }
    .pagination-controls .ellipsis {
        font-size: 12px;
        color: #94a3b8;
        padding: 0 .2rem;
    }
</style>
@endpush

@section('content')

<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.2rem">
    <div>
        <div class="page-title">Contactos</div>
        <div class="page-sub">Gestiona todos los contactos registrados</div>
    </div>
    <div style="display:flex;gap:.6rem;align-items:center;flex-wrap:wrap">
        <a href="{{ route('admin.contacts.export.excel') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:.5rem .9rem;
                  background:#fff;border:1px solid #e2e8f0;border-radius:8px;
                  font-size:13px;font-weight:600;color:#16a34a;text-decoration:none">
            <i class="ti ti-file-type-xls" style="font-size:16px"></i> Excel
        </a>
        <a href="{{ route('admin.contacts.create') }}" class="btn btn-primary"
            style="text-decoration:none;display:inline-flex;align-items:center;gap:6px">
            <i class="ti ti-plus" style="font-size:15px"></i> Nuevo Contacto
        </a>
    </div>
</div>

{{-- BARRA DE FILTROS --}}
<div class="filter-bar">
    <div style="position:relative;flex:1;min-width:200px">
        <i class="ti ti-search" style="position:absolute;left:.7rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:15px"></i>
        <input type="text" id="f-search" class="filter-input"
               placeholder="Buscar por nombre, email, teléfono, cargo..."
               style="width:100%;padding-left:2.2rem">
    </div>

    <div class="filter-sep"></div>

    <select id="f-client" class="filter-input" style="min-width:160px">
        <option value="">Todos los clientes</option>
        @foreach($clients as $client)
            <option value="{{ $client->id_client }}">{{ $client->name_client }}</option>
        @endforeach
    </select>

    <select id="f-principal" class="filter-input" style="min-width:130px">
        <option value="">Todos</option>
        <option value="1">Solo principales</option>
        <option value="0">Solo secundarios</option>
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
        <option value="az">Nombre A → Z</option>
        <option value="za">Nombre Z → A</option>
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
<form id="bulk-delete-form" action="{{ route('admin.contacts.bulk-destroy') }}"
      method="POST" style="display:none">
    @csrf
    @method('DELETE')
    <div id="bulk-ids-container"></div>
</form>

@if($contacts->isEmpty())
    <div style="text-align:center;padding:4rem;background:#fff;border-radius:14px;border:1px solid #e2e8f0">
        <i class="ti ti-address-book-off" style="font-size:48px;color:#cbd5e1;display:block;margin-bottom:1rem"></i>
        <p style="font-size:15px;font-weight:600;color:#0f172a;margin-bottom:.4rem">No hay contactos aún</p>
        <p style="font-size:13px;color:#94a3b8;margin-bottom:1.2rem">Comienza creando tu primer contacto</p>
        <a href="{{ route('admin.contacts.create') }}" class="btn btn-primary btn-sm" style="text-decoration:none">
            <i class="ti ti-plus"></i> Crear primer contacto
        </a>
    </div>
@else
    <div class="table-wrap" id="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width:40px">
                        <input type="checkbox" id="check-all" title="Seleccionar todos"
                            onchange="toggleAll(this.checked)">
                    </th>
                    <th>ID</th>
                    <th>Contacto</th>
                    <th>Cliente</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Principal</th>
                    <th>Registro</th>
                    <th style="text-align:center">Acciones</th>
                </tr>
            </thead>
            <tbody id="tabla-body">
                @foreach($contacts as $c)
                    <tr class="contact-row"
                        data-id="{{ $c->id_contacts }}"
                        data-name="{{ strtolower($c->name . ' ' . ($c->last_names ?? '')) }}"
                        data-email="{{ strtolower($c->email ?? '') }}"
                        data-phone="{{ $c->first_phone ?? '' }}"
                        data-client="{{ $c->id_client ?? '' }}"
                        data-principal="{{ $c->es_principal ? '1' : '0' }}"
                        data-date="{{ $c->created_at->format('Y-m-d') }}"
                        data-ts="{{ $c->created_at->timestamp }}">

                        <td class="cb-wrap">
                            <input type="checkbox" class="row-check"
                                value="{{ $c->id_contacts }}"
                                onchange="updateBulk()">
                        </td>
                        <td style="color:#94a3b8;font-size:12px">#{{ $c->id_contacts }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:9px">
                                <div class="avatar-sm" style="background:#ede9fe;color:#6d28d9">
                                    {{ strtoupper(substr($c->name,0,1)) }}{{ strtoupper(substr($c->last_names ?? '',0,1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:13px;color:#0f172a">
                                        {{ $c->name }} {{ $c->last_names }}
                                    </div>
                                    @if($c->qualification)
                                        <div style="font-size:11px;color:#94a3b8">{{ $c->qualification }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($c->client)
                                <span class="badge" style="background:#dbeafe;color:#1d4ed8;border:1px solid #bfdbfe">
                                    {{ $c->client->name_client }}
                                </span>
                            @else
                                <span style="color:#cbd5e1;font-size:12px">—</span>
                            @endif
                        </td>
                        <td style="color:#64748b;font-size:12px">{{ $c->email ?? '—' }}</td>
                        <td style="color:#64748b;font-size:12px">{{ $c->first_phone ?? '—' }}</td>
                        <td>
                            @if($c->es_principal)
                                <span class="badge" style="background:#fef3c7;color:#92400e;border:1px solid #fde68a">
                                    <i class="ti ti-star-filled" style="font-size:10px"></i> Principal
                                </span>
                            @else
                                <span style="color:#e2e8f0;font-size:12px">—</span>
                            @endif
                        </td>
                        <td style="color:#94a3b8;font-size:12px">
                            {{ $c->created_at->format('d/m/Y') }}
                        </td>
                        <td>
                            <div style="display:flex;justify-content:center;gap:6px">
                                <a href="{{ route('admin.contacts.edit', $c->id_contacts) }}"
                                   class="btn-secondary btn-sm" style="text-decoration:none">
                                    <i class="ti ti-edit" style="font-size:13px"></i> Editar
                                </a>
                                <form action="{{ route('admin.contacts.destroy', $c->id_contacts) }}"
                                      method="POST"
                                      onsubmit="return confirm('¿Eliminar a {{ addslashes($c->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger btn-sm" style="border:none">
                                        <i class="ti ti-trash" style="font-size:13px"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="table-footer" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem;padding-top:.75rem">
            <span id="footer-count">
                Mostrando {{ $contacts->firstItem() }}–{{ $contacts->lastItem() }}
                de {{ $contacts->total() }} contacto(s)
            </span>
            <div class="pagination-controls" id="pagination-controls">
                @if($contacts->onFirstPage())
                    <span class="disabled"><i class="ti ti-chevron-left"></i></span>
                @else
                    <a href="{{ $contacts->previousPageUrl() }}"><i class="ti ti-chevron-left"></i></a>
                @endif

                @foreach($contacts->getUrlRange(1, $contacts->lastPage()) as $page => $url)
                    @if($page == $contacts->currentPage())
                        <span class="page-current">{{ $page }}</span>
                    @elseif($page == 1 || $page == $contacts->lastPage() || abs($page - $contacts->currentPage()) <= 1)
                        <a href="{{ $url }}">{{ $page }}</a>
                    @elseif(abs($page - $contacts->currentPage()) == 2)
                        <span class="ellipsis">…</span>
                    @endif
                @endforeach

                @if($contacts->hasMorePages())
                    <a href="{{ $contacts->nextPageUrl() }}"><i class="ti ti-chevron-right"></i></a>
                @else
                    <span class="disabled"><i class="ti ti-chevron-right"></i></span>
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

@push('scripts')
<script>
    // ============================================================
    // FILTROS
    // ============================================================
    function applyFilters() {
        const search = document.getElementById('f-search').value.toLowerCase().trim();
        const client = document.getElementById('f-client').value;
        const principal = document.getElementById('f-principal').value;
        const date = document.getElementById('f-date').value;
        const sort = document.getElementById('f-sort').value;

        const now = new Date();
        const today = now.toISOString().split('T')[0];
        const weekStart = new Date(now);
        weekStart.setDate(now.getDate() - now.getDay());
        const monthStart = new Date(now.getFullYear(), now.getMonth(), 1);
        const yearStart = new Date(now.getFullYear(), 0, 1);

        const rows = Array.from(document.querySelectorAll('.contact-row'));

        let visible = rows.filter(row => {
            const name = row.dataset.name || '';
            const email = row.dataset.email || '';
            const phone = row.dataset.phone || '';
            const rowClient = row.dataset.client || '';
            const rowPrincipal = row.dataset.principal || '0';
            const rowDate = new Date(row.dataset.date);

            if (search) {
                const searchable = `${name} ${email} ${phone}`.toLowerCase();
                if (!searchable.includes(search)) return false;
            }

            if (client && rowClient !== client) return false;
            if (principal !== '' && rowPrincipal !== principal) return false;

            if (date === 'today' && row.dataset.date !== today) return false;
            if (date === 'week' && rowDate < weekStart) return false;
            if (date === 'month' && rowDate < monthStart) return false;
            if (date === 'year' && rowDate < yearStart) return false;

            return true;
        });

        visible.sort((a, b) => {
            switch (sort) {
                case 'oldest':
                    return parseInt(a.dataset.ts) - parseInt(b.dataset.ts);
                case 'az':
                    return (a.dataset.name || '').localeCompare(b.dataset.name || '');
                case 'za':
                    return (b.dataset.name || '').localeCompare(a.dataset.name || '');
                default:
                    return parseInt(b.dataset.ts) - parseInt(a.dataset.ts);
            }
        });

        const tbody = document.getElementById('tabla-body');
        rows.forEach(r => r.style.display = 'none');
        visible.forEach(r => {
            r.style.display = '';
            tbody.appendChild(r);
        });

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
        const resultsCount = document.getElementById('results-count');
        if (resultsCount) resultsCount.textContent = count + ' resultado(s)';

        const footerCount = document.getElementById('footer-count');
        if (footerCount) {
            if (count === rows.length) {
                footerCount.textContent = window.originalPaginationText || footerCount.textContent;
            } else {
                footerCount.textContent = `Mostrando ${count} de ${count} contacto(s) filtrados`;
            }
        }

        const footerFilter = document.getElementById('footer-filter');
        const hasFilters = search || client || principal || date || sort !== 'newest';
        if (footerFilter) footerFilter.style.display = hasFilters ? 'block' : 'none';

        const paginationControls = document.getElementById('pagination-controls');
        if (paginationControls) {
            paginationControls.style.display = hasFilters ? 'none' : 'flex';
        }

        deselectAll();
    }

    function clearFilters() {
        document.getElementById('f-search').value = '';
        document.getElementById('f-client').value = '';
        document.getElementById('f-principal').value = '';
        document.getElementById('f-date').value = '';
        document.getElementById('f-sort').value = 'newest';
        applyFilters();
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

        if (!confirm(`¿Eliminar ${checked.length} contacto(s) seleccionado(s)?`)) return;

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
    // INICIALIZACIÓN
    // ============================================================
    document.addEventListener('DOMContentLoaded', function() {
        const footerCount = document.getElementById('footer-count');
        if (footerCount) {
            window.originalPaginationText = footerCount.textContent;
        }

        // Eventos de filtros
        document.getElementById('f-search').addEventListener('input', applyFilters);
        document.getElementById('f-client').addEventListener('change', applyFilters);
        document.getElementById('f-principal').addEventListener('change', applyFilters);
        document.getElementById('f-date').addEventListener('change', applyFilters);
        document.getElementById('f-sort').addEventListener('change', applyFilters);
    });
</script>
@endpush
@endsection
