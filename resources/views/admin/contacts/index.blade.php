@extends('layouts.app')
@section('title', 'Contactos')
@section('content')

<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.6rem">
    <div>
        <div class="page-title">Contactos</div>
        <div class="page-sub">Gestiona todos los contactos del sistema</div>
    </div>
    <a href="{{ route('admin.contacts.create') }}" class="btn btn-primary">
        <i class="ti ti-plus" style="font-size:15px"></i> Nuevo contacto
    </a>
</div>

@if($contacts->isEmpty())
<div style="text-align:center;padding:4rem;background:#fff;border-radius:14px;border:1px solid #e2e8f0">
    <i class="ti ti-address-book-off" style="font-size:48px;color:#cbd5e1;display:block;margin-bottom:1rem"></i>
    <p style="font-size:15px;font-weight:600;color:#0f172a;margin-bottom:.4rem">No hay contactos aún</p>
    <p style="font-size:13px;color:#94a3b8;margin-bottom:1.2rem">Comienza creando tu primer contacto</p>
    <a href="{{ route('admin.contacts.create') }}" class="btn btn-primary btn-sm">
        <i class="ti ti-plus"></i> Crear primer contacto
    </a>
</div>
@else
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Contacto</th>
                <th>Cliente</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Principal</th>
                <th>Fecha registro</th>
                <th style="text-align:center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contacts as $c)
            <tr>
                <td style="color:#cbd5e1;font-size:12px;font-weight:600">#{{ $c->id_contacts }}</td>
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
                <td style="color:#94a3b8;font-size:12px">{{ $c->created_at->format('d/m/Y') }}</td>
                <td>
                    <div style="display:flex;justify-content:center;gap:6px">
                        <a href="{{ route('admin.contacts.edit', $c->id_contacts) }}"
                           class="btn btn-secondary btn-sm">
                            <i class="ti ti-edit" style="font-size:13px"></i> Editar
                        </a>
                        <form action="{{ route('admin.contacts.destroy', $c->id_contacts) }}"
                              method="POST"
                              onsubmit="return confirm('¿Eliminar a {{ addslashes($c->name) }}?')">
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
    <div class="table-footer">Total: {{ $contacts->count() }} contacto(s)</div>
</div>
@endif
@endsection
