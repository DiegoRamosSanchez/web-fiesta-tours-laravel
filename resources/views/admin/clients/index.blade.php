@extends('layouts.app')
@section('title', 'Clientes')
@section('content')

<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.6rem">
    <div>
        <div class="page-title">Clientes</div>
        <div class="page-sub">Gestiona todos los clientes registrados</div>
    </div>
    <a href="{{ route('admin.clients.create') }}" class="btn btn-primary">
        <i class="ti ti-plus" style="font-size:15px"></i> Nuevo cliente
    </a>
</div>

@if($clients->isEmpty())
<div style="text-align:center;padding:4rem;background:#fff;border-radius:14px;border:1px solid #e2e8f0">
    <i class="ti ti-building-off" style="font-size:48px;color:#cbd5e1;display:block;margin-bottom:1rem"></i>
    <p style="font-size:15px;font-weight:600;color:#0f172a;margin-bottom:.4rem">No hay clientes aún</p>
    <p style="font-size:13px;color:#94a3b8;margin-bottom:1.2rem">Comienza creando tu primer cliente</p>
    <a href="{{ route('admin.clients.create') }}" class="btn btn-primary btn-sm">
        <i class="ti ti-plus"></i> Crear primer cliente
    </a>
</div>
@else
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Contactos</th>
                <th>Registro</th>
                <th style="text-align:center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clients as $c)
            <tr>
                <td style="color:#cbd5e1;font-size:12px;font-weight:600">#{{ $c->id_client }}</td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px">
                        <div class="avatar-sm" style="background:#dbeafe;color:#1d4ed8;font-size:12px">
                            {{ strtoupper(substr($c->name_client ?? 'NA', 0, 2)) }}
                        </div>
                        <span style="font-weight:600;color:#0f172a">{{ $c->name_client ?? '—' }}</span>
                    </div>
                </td>
                <td>
                    <span class="badge" style="background:#f0f9ff;color:#0369a1;border:1px solid #bae6fd">
                        <i class="ti ti-users" style="font-size:11px"></i>
                        {{ $c->contacts_count }} contacto(s)
                    </span>
                </td>
                <td style="color:#94a3b8;font-size:12px">
                    {{ $c->created_at->format('d/m/Y') }}
                </td>
                <td>
                    <div style="display:flex;justify-content:center;gap:6px">
                        <a href="{{ route('admin.clients.edit', $c->id_client) }}"
                           class="btn btn-secondary btn-sm">
                            <i class="ti ti-edit" style="font-size:13px"></i> Editar
                        </a>
                        <form action="{{ route('admin.clients.destroy', $c->id_client) }}"
                              method="POST"
                              onsubmit="return confirm('¿Eliminar a {{ addslashes($c->name_client) }}? Se eliminarán sus contactos.')">
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
    <div class="table-footer">Total: {{ $clients->count() }} cliente(s)</div>
</div>
@endif
@endsection
