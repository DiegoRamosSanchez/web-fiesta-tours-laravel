@extends('layouts.app')
@section('title', 'Mi Perfil')
@section('content')

<div class="page-header">
    <div class="page-title">Mi Perfil</div>
    <div class="page-sub">Tu información personal registrada en el sistema</div>
</div>

<div style="max-width:600px">
    <div class="card">
        <div style="display:flex;align-items:center;gap:1.2rem;margin-bottom:1.5rem;padding-bottom:1.2rem;border-bottom:1px solid #f1f5f9">
            <div style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff;font-weight:700;flex-shrink:0">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div>
                <div style="font-size:18px;font-weight:700;color:#0f172a">{{ $user->name }}</div>
                <div style="font-size:13px;color:#64748b;margin-top:2px">{{ $user->email }}</div>
                <span style="display:inline-block;margin-top:6px;font-size:11px;font-weight:700;padding:3px 10px;border-radius:999px;
                    background:{{ $user->isAdmin() ? '#ede9fe' : '#dcfce7' }};
                    color:{{ $user->isAdmin() ? '#6d28d9' : '#166534' }}">
                    {{ ucfirst($user->role) }}
                </span>
            </div>
            <a href="{{ route('perfil.edit') }}" class="btn btn-primary" style="margin-left:auto">
                <i class="ti ti-edit" style="font-size:15px"></i> Editar perfil
            </a>
        </div>

        <div style="display:flex;flex-direction:column;gap:0">
            <div style="display:flex;justify-content:space-between;align-items:center;padding:.85rem 0;border-bottom:1px solid #f8fafc">
                <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#64748b">
                    <i class="ti ti-user" style="font-size:16px"></i> Nombre completo
                </div>
                <span style="font-size:13px;font-weight:600;color:#0f172a">{{ $user->name }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:.85rem 0;border-bottom:1px solid #f8fafc">
                <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#64748b">
                    <i class="ti ti-mail" style="font-size:16px"></i> Correo electrónico
                </div>
                <span style="font-size:13px;font-weight:600;color:#0f172a">{{ $user->email }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:.85rem 0;border-bottom:1px solid #f8fafc">
                <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#64748b">
                    <i class="ti ti-shield" style="font-size:16px"></i> Rol en el sistema
                </div>
                <span style="font-size:13px;font-weight:600;color:#0f172a">{{ ucfirst($user->role) }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:.85rem 0">
                <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#64748b">
                    <i class="ti ti-calendar" style="font-size:16px"></i> Miembro desde
                </div>
                <span style="font-size:13px;font-weight:600;color:#0f172a">{{ $user->created_at->format('d \d\e F \d\e Y') }}</span>
            </div>
        </div>
    </div>
</div>

@endsection
