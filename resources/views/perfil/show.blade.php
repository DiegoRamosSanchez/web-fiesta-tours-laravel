@extends('layouts.app')
@section('title', 'Mi Perfil')

@push('styles')
<style>
.profile-wrap { max-width: 640px; }

/* Cover / hero */
.profile-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 16px;
    padding: 2rem 2rem 6.5rem;
    position: relative;
    margin-bottom: 3rem;
    overflow: hidden;
}
.profile-hero::after {
    content: '';
    position: absolute;
    right: -40px; bottom: -40px;
    width: 200px; height: 200px;
    border-radius: 50%;
    background: rgba(99,102,241,.12);
}
.ph-edit-btn {
    position: absolute; top: 1.2rem; right: 1.2rem;
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.15);
    border-radius: 8px;
    color: #fff;
    padding: 7px 14px;
    font-size: 12px; font-weight: 600;
    display: flex; align-items: center; gap: 6px;
    text-decoration: none;
    transition: background .15s;
}
.ph-edit-btn:hover { background: rgba(255,255,255,.18); }

/* Floating avatar card */
.profile-avatar-float {
    position: absolute;
    bottom: 2.3rem;
    left: 2rem;
    display: flex;
    align-items: flex-end;
    gap: 1rem;
}
.pa-circle {
    width: 76px; height: 76px;
    border-radius: 50%;
    background: linear-gradient(135deg,#6366f1,#8b5cf6);
    border: 4px solid #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; color: #fff; font-weight: 800;
    box-shadow: 0 4px 16px rgba(99,102,241,.35);
    flex-shrink: 0;
}
.pa-info { padding-bottom: .3rem; }
.pa-name { font-size: 1.1rem; font-weight: 800; color: #fff; }
.pa-role-badge {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .7rem; font-weight: 700;
    padding: 3px 9px; border-radius: 999px;
    margin-top: 5px;
}
.ph-bg-text {
    position: absolute; right: 1.5rem; top: 50%; transform: translateY(-50%);
    font-size: 6rem; font-weight: 900; color: rgba(255,255,255,.04);
    pointer-events: none; user-select: none; line-height: 1;
    z-index: 0;
}

/* Info panel */
.info-panel {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
}
.ip-head {
    padding: 1rem 1.4rem;
    border-bottom: 1px solid #f1f5f9;
    font-size: .78rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .6px;
    color: #94a3b8;
    background: #fafafa;
}
.ip-row {
    display: flex; align-items: center;
    padding: 1rem 1.4rem;
    border-bottom: 1px solid #f8fafc;
    gap: 12px;
}
.ip-row:last-child { border-bottom: none; }
.ip-ico {
    width: 34px; height: 34px;
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; flex-shrink: 0;
}
.ip-key { font-size: .82rem; color: #64748b; flex: 1; }
.ip-val { font-size: .87rem; font-weight: 700; color: #0f172a; }

/* Bottom actions */
.profile-actions {
    display: flex; gap: .7rem; margin-top: 1rem;
    flex-wrap: wrap;
}
</style>
@endpush

@section('content')

<div class="page-header">
    <div class="page-title">Mi Perfil</div>
    <div class="page-sub">Tu información registrada en el sistema</div>
</div>

<div class="profile-wrap">

    {{-- Hero card --}}
    <div class="profile-hero">
        <div class="ph-bg-text">{{ strtoupper(substr($user->name,0,1)) }}</div>

        <a href="{{ route('perfil.edit') }}" class="ph-edit-btn">
            <i class="ti ti-edit" style="font-size:13px"></i> Editar perfil
        </a>

        <div style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:rgba(255,255,255,.35);margin-bottom:1.5rem">
            Fiesta Tours · Mi cuenta
        </div>

        <div class="profile-avatar-float">
            <div class="pa-circle">{{ strtoupper(substr($user->name,0,2)) }}</div>
            <div class="pa-info">
                <div class="pa-name">{{ $user->name }}</div>
                <span class="pa-role-badge {{ $user->isAdmin() ? 'ub-admin' : 'ub-usuario' }}"
                      style="{{ $user->isAdmin() ? 'background:rgba(109,40,217,.3);color:#c4b5fd' : 'background:rgba(22,101,52,.3);color:#86efac' }}">
                    <i class="ti {{ $user->isAdmin() ? 'ti-shield-check' : 'ti-user' }}" style="font-size:10px"></i>
                    {{ ucfirst($user->role) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Info rows --}}
    <div class="info-panel">
        <div class="ip-head">Información de la cuenta</div>

        <div class="ip-row">
            <div class="ip-ico" style="background:#ede9fe"><i class="ti ti-user" style="color:#6d28d9"></i></div>
            <span class="ip-key">Nombre completo</span>
            <span class="ip-val">{{ $user->name }}</span>
        </div>
        <div class="ip-row">
            <div class="ip-ico" style="background:#dbeafe"><i class="ti ti-mail" style="color:#1d4ed8"></i></div>
            <span class="ip-key">Correo electrónico</span>
            <span class="ip-val">{{ $user->email }}</span>
        </div>
        <div class="ip-row">
            <div class="ip-ico" style="{{ $user->isAdmin() ? 'background:#ede9fe' : 'background:#dcfce7' }}">
                <i class="ti ti-shield" style="color:{{ $user->isAdmin() ? '#6d28d9' : '#166534' }}"></i>
            </div>
            <span class="ip-key">Rol en el sistema</span>
            <span class="ip-val">
                <span style="display:inline-flex;align-items:center;gap:4px;font-size:.75rem;padding:3px 10px;border-radius:999px;
                    background:{{ $user->isAdmin() ? '#ede9fe' : '#dcfce7' }};
                    color:{{ $user->isAdmin() ? '#6d28d9' : '#166534' }}">
                    {{ ucfirst($user->role) }}
                </span>
            </span>
        </div>
        <div class="ip-row">
            <div class="ip-ico" style="background:#fef9c3"><i class="ti ti-calendar" style="color:#ca8a04"></i></div>
            <span class="ip-key">Miembro desde</span>
            <span class="ip-val">{{ $user->created_at->format('d \d\e F \d\e Y') }}</span>
        </div>
        <div class="ip-row">
            <div class="ip-ico" style="background:#f0fdf4"><i class="ti ti-clock" style="color:#16a34a"></i></div>
            <span class="ip-key">Último acceso</span>
            <span class="ip-val" style="color:#94a3b8">Hoy</span>
        </div>
    </div>

    {{-- Actions --}}
    <div class="profile-actions">
        <a href="{{ route('perfil.edit') }}" class="btn btn-primary">
            <i class="ti ti-edit" style="font-size:14px"></i> Editar perfil
        </a>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="ti ti-arrow-left" style="font-size:14px"></i> Volver al dashboard
        </a>
    </div>
</div>

@endsection
