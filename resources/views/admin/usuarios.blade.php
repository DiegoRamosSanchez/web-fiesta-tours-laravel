@extends('layouts.app')
@section('title', 'Usuarios')
@section('content')

<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start">
    <div>
        <div class="page-title">Usuarios</div>
        <div class="page-sub">Gestiona todos los usuarios del sistema</div>
    </div>
    <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">
        <i class="ti ti-user-plus" style="font-size:15px"></i> Crear usuario
    </a>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Usuario</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Registro</th>
                <th style="text-align:center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $u)
                <tr>
                    <td style="color:#cbd5e1;font-size:12px">{{ $u->id }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            @if($u->avatar)
                                <div class="avatar-sm"><img class="avatar-sm" src="{{ asset('storage/' . $u->avatar) }}" /></div>
                            @else
                                <div class="avatar-sm" style="background:{{ $u->isAdmin() ? '#ede9fe' : '#dcfce7' }};color:{{ $u->isAdmin() ? '#6d28d9' : '#166534' }}">
                                    {{ strtoupper(substr($u->name, 0, 2)) }}
                                </div>
                            @endif
                            <div style="font-weight:600;font-size:13px">{{ $u->name }}</div>
                        </div>
                    </td>
                    <td style="color:#64748b">{{ $u->email }}</td>
                    <td>
                        <span class="badge {{ $u->isAdmin() ? 'badge-admin' : 'badge-usuario' }}">
                            <i class="ti {{ $u->isAdmin() ? 'ti-shield' : 'ti-user' }}" style="font-size:11px"></i>
                            {{ ucfirst($u->role) }}
                        </span>
                    </td>
                    <td style="color:#94a3b8;font-size:12px">{{ $u->created_at->format('d/m/Y') }}</td>
                    <td style="text-align:center">
                        <div style="display:flex;gap:6px;justify-content:center">
                            <a href="{{ route('admin.usuarios.edit', $u) }}" class="btn btn-secondary btn-sm">
                                <i class="ti ti-edit" style="font-size:13px"></i> Editar
                            </a>
                            <form action="{{ route('admin.usuarios.destroy', $u) }}" method="POST"
                                  onsubmit="return confirm('¿Eliminar a {{ addslashes($u->name) }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="ti ti-trash" style="font-size:13px"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:2.5rem;color:#94a3b8">
                        <i class="ti ti-users-off" style="font-size:32px;display:block;margin-bottom:.5rem"></i>
                        No hay usuarios registrados
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="table-footer">Total: {{ $users->count() }} usuario(s)</div>
</div>

@endsection
