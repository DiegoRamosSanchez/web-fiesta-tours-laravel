@extends('layouts.app')
@section('title', 'Editar Proveedor')
@section('content')

<div class="page-header">
    <div class="page-title">Editar Proveedor</div>
    <div class="page-sub">Modifica los datos del proveedor</div>
</div>

<div style="max-width:660px">
    @if($errors->any())
        <div class="alert alert-error">
            <i class="ti ti-alert-circle"></i>
            <ul style="list-style:none;margin-left:.5rem">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">{{ $supplier->supplier_name }}</div>
                <div class="card-sub">ID #{{ $supplier->id_supplier }}</div>
            </div>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary btn-sm">
                <i class="ti ti-arrow-left" style="font-size:13px"></i> Volver
            </a>
        </div>

        <form action="{{ route('admin.suppliers.update', $supplier->id_supplier) }}" method="POST">
            @csrf @method('PUT')

            <div class="form-field" style="margin-bottom:1.2rem">
                <label>Nombre del proveedor *</label>
                <input type="text" name="supplier_name"
                       value="{{ old('supplier_name', $supplier->supplier_name) }}"
                       maxlength="100" required autofocus>
            </div>

            {{-- DESTINO --}}
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:1rem;margin-bottom:1rem">
                <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:.8rem;display:flex;align-items:center;gap:6px">
                    <i class="ti ti-map-pin" style="font-size:14px"></i> Destino
                </p>
                <div style="display:flex;align-items:flex-end;gap:.6rem">
                    <div class="form-field" style="flex:1;margin:0">
                        <label>Seleccionar destino existente</label>
                        <select name="id_destinations" id="sel-destination" style="margin-top:.3rem">
                            <option value="">— Sin destino —</option>
                            @foreach($destinations as $d)
                                <option value="{{ $d->id_destinations }}"
                                    {{ old('id_destinations', $supplier->id_destinations) == $d->id_destinations ? 'selected' : '' }}>
                                    {{ $d->destination_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" onclick="toggleNew('destination')"
                            style="padding:.6rem .9rem;background:#fff;border:1px solid #e2e8f0;
                                   border-radius:9px;font-size:12px;font-weight:600;color:#6366f1;
                                   cursor:pointer;white-space:nowrap;flex-shrink:0">
                        <i class="ti ti-plus" style="font-size:13px"></i> Nuevo
                    </button>
                </div>
                <div id="new-destination" style="display:none;margin-top:.7rem">
                    <div style="background:#ede9fe;border-radius:8px;padding:.8rem">
                        <label style="font-size:10px;font-weight:700;color:#6d28d9;text-transform:uppercase;letter-spacing:.5px">
                            Nombre del nuevo destino
                        </label>
                        <div style="display:flex;gap:.5rem;margin-top:.4rem">
                            <input type="text" name="new_destination_name" id="new-destination-input"
                                   placeholder="Ej: Lima, Cusco, Cancún..."
                                   style="flex:1;padding:.55rem .8rem;border:1px solid #c4b5fd;border-radius:8px;font-size:13px;outline:none">
                            <button type="button" onclick="toggleNew('destination')"
                                    style="padding:.55rem .8rem;background:none;border:1px solid #c4b5fd;border-radius:8px;color:#6d28d9;cursor:pointer;font-size:13px">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                        <p style="font-size:11px;color:#7c3aed;margin-top:.4rem">
                            <i class="ti ti-info-circle" style="font-size:12px"></i>
                            Se creará automáticamente al guardar
                        </p>
                    </div>
                </div>
            </div>

            {{-- CATEGORÍA --}}
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:1rem;margin-bottom:1.4rem">
                <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:.8rem;display:flex;align-items:center;gap:6px">
                    <i class="ti ti-tag" style="font-size:14px"></i> Categoría
                </p>
                <div style="display:flex;align-items:flex-end;gap:.6rem">
                    <div class="form-field" style="flex:1;margin:0">
                        <label>Seleccionar categoría existente</label>
                        <select name="id_categories_suppliers" id="sel-category" style="margin-top:.3rem">
                            <option value="">— Sin categoría —</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id_categories_suppliers }}"
                                    {{ old('id_categories_suppliers', $supplier->id_categories_suppliers) == $c->id_categories_suppliers ? 'selected' : '' }}>
                                    {{ $c->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" onclick="toggleNew('category')"
                            style="padding:.6rem .9rem;background:#fff;border:1px solid #e2e8f0;
                                   border-radius:9px;font-size:12px;font-weight:600;color:#6366f1;
                                   cursor:pointer;white-space:nowrap;flex-shrink:0">
                        <i class="ti ti-plus" style="font-size:13px"></i> Nueva
                    </button>
                </div>
                <div id="new-category" style="display:none;margin-top:.7rem">
                    <div style="background:#fef3c7;border-radius:8px;padding:.8rem">
                        <label style="font-size:10px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.5px">
                            Nombre de la nueva categoría
                        </label>
                        <div style="display:flex;gap:.5rem;margin-top:.4rem">
                            <input type="text" name="new_category_name" id="new-category-input"
                                   placeholder="Ej: Hoteles, Aerolíneas, Transporte..."
                                   style="flex:1;padding:.55rem .8rem;border:1px solid #fde68a;border-radius:8px;font-size:13px;outline:none">
                            <button type="button" onclick="toggleNew('category')"
                                    style="padding:.55rem .8rem;background:none;border:1px solid #fde68a;border-radius:8px;color:#92400e;cursor:pointer;font-size:13px">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                        <p style="font-size:11px;color:#b45309;margin-top:.4rem">
                            <i class="ti ti-info-circle" style="font-size:12px"></i>
                            Se creará automáticamente al guardar
                        </p>
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:.8rem;padding-top:1rem;border-top:1px solid #f1f5f9">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy" style="font-size:14px"></i> Guardar cambios
                </button>
                <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleNew(type) {
    const box   = document.getElementById('new-' + type);
    const sel   = document.getElementById('sel-' + type);
    const input = document.getElementById('new-' + type + '-input');
    const open  = box.style.display === 'none';

    box.style.display = open ? 'block' : 'none';

    if (open) {
        sel.value    = '';
        sel.disabled = true;
        input.focus();
    } else {
        sel.disabled = false;
        input.value  = '';
    }
}
</script>
@endpush
@endsection
