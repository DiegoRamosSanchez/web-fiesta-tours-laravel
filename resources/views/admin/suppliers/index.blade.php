@extends('layouts.app')
@section('title', 'Proveedores')
@section('content')

<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.6rem">
    <div>
        <div class="page-title">Proveedores</div>
        <div class="page-sub">Gestiona todos los proveedores del sistema</div>
    </div>
    <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">
        <i class="ti ti-plus" style="font-size:15px"></i> Nuevo proveedor
    </a>
</div>

@if($suppliers->isEmpty())
    <div style="text-align:center;padding:4rem;background:#fff;border-radius:14px;border:1px solid #e2e8f0">
        <i class="ti ti-truck-off" style="font-size:48px;color:#cbd5e1;display:block;margin-bottom:1rem"></i>
        <p style="font-size:15px;font-weight:600;color:#0f172a;margin-bottom:.4rem">No hay proveedores aún</p>
        <p style="font-size:13px;color:#94a3b8;margin-bottom:1.2rem">Comienza agregando tu primer proveedor</p>
        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary btn-sm">
            <i class="ti ti-plus"></i> Crear proveedor
        </a>
    </div>
@else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Proveedor</th>
                    <th>Destino</th>
                    <th>Categoría</th>
                    <th>Registro</th>
                    <th style="text-align:center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($suppliers as $s)
                <tr>
                    <td style="color:#cbd5e1;font-size:12px;font-weight:600">#{{ $s->id_supplier }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="avatar-sm" style="background:#f0f9ff;color:#0369a1">
                                {{ strtoupper(substr($s->supplier_name, 0, 2)) }}
                            </div>
                            <span style="font-weight:600">{{ $s->supplier_name }}</span>
                        </div>
                    </td>
                    <td>
                        @if($s->destination)
                            <span class="badge" style="background:#fef3c7;color:#92400e;border:1px solid #fde68a">
                                <i class="ti ti-map-pin" style="font-size:10px"></i>
                                {{ $s->destination->destination_name }}
                            </span>
                        @else
                            <span style="color:#cbd5e1;font-size:12px">—</span>
                        @endif
                    </td>
                    <td>
                        @if($s->category)
                            <span class="badge" style="background:#ede9fe;color:#6d28d9;border:1px solid #ddd6fe">
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
                            <a href="{{ route('admin.suppliers.edit', $s->id_supplier) }}"
                               class="btn btn-secondary btn-sm">
                                <i class="ti ti-edit" style="font-size:13px"></i> Editar
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
        <div class="table-footer">Total: {{ $suppliers->count() }} proveedor(es)</div>
    </div>
@endif
@endsection
