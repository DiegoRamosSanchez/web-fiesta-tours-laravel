
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
