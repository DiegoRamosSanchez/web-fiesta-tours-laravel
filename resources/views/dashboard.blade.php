@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
    .page-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1.5rem;
    }
    .page-title span { color: #6366f1; }

    /* Cards de stats */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .stat-card {
        background: #fff;
        border-radius: 12px;
        padding: 1.4rem 1.6rem;
        box-shadow: 0 1px 4px rgba(0,0,0,.06);
        border-left: 4px solid var(--accent, #6366f1);
    }
    .stat-card .label {
        font-size: .78rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .5px;
        font-weight: 600;
    }
    .stat-card .value {
        font-size: 1.8rem;
        font-weight: 800;
        color: #0f172a;
        margin-top: .3rem;
    }
    .stat-card .sub {
        font-size: .8rem;
        color: #94a3b8;
        margin-top: .3rem;
    }

    /* Panel admin */
    .admin-section {
        background: #fff;
        border-radius: 12px;
        padding: 1.6rem;
        box-shadow: 0 1px 4px rgba(0,0,0,.06);
        margin-bottom: 1.5rem;
    }
    .admin-section h2 {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 1rem;
        padding-bottom: .75rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .admin-links {
        display: flex;
        gap: .75rem;
        flex-wrap: wrap;
    }
    .admin-link {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .6rem 1.2rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        color: #475569;
        text-decoration: none;
        font-size: .88rem;
        font-weight: 500;
        transition: all .15s;
    }
    .admin-link:hover {
        background: #6366f1;
        color: #fff;
        border-color: #6366f1;
    }

    /* Perfil */
    .profile-card {
        background: #fff;
        border-radius: 12px;
        padding: 1.6rem;
        box-shadow: 0 1px 4px rgba(0,0,0,.06);
    }
    .profile-card h2 {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 1rem;
        padding-bottom: .75rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .profile-row {
        display: flex;
        align-items: center;
        padding: .6rem 0;
        border-bottom: 1px solid #f8fafc;
        font-size: .9rem;
    }
    .profile-row:last-child { border-bottom: none; }
    .profile-row .key {
        width: 130px;
        color: #64748b;
        font-weight: 500;
        flex-shrink: 0;
    }
    .profile-row .val { color: #1e293b; font-weight: 600; }

    .role-badge {
        display: inline-block;
        padding: .25rem .8rem;
        border-radius: 999px;
        font-size: .78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
    }
    .role-admin   { background: #ede9fe; color: #6d28d9; }
    .role-cliente { background: #dcfce7; color: #166534; }
</style>
@endpush

@section('content')

<p class="page-title">
    Bienvenido, <span>{{ $user->name }}</span> 👋
</p>

{{-- SECCIÓN SOLO ADMIN --}}
@if($user->isAdmin())
    <div class="stats-grid">
        <div class="stat-card" style="--accent:#6366f1">
            <div class="label">Usuarios totales</div>
            <div class="value">{{ \App\Models\User::count() }}</div>
            <div class="sub">Registrados en el sistema</div>
        </div>
        <div class="stat-card" style="--accent:#10b981">
            <div class="label">Clientes</div>
            <div class="value">{{ \App\Models\User::where('role','cliente')->count() }}</div>
            <div class="sub">Rol cliente activo</div>
        </div>
        <div class="stat-card" style="--accent:#f59e0b">
            <div class="label">Administradores</div>
            <div class="value">{{ \App\Models\User::where('role','admin')->count() }}</div>
            <div class="sub">Con acceso completo</div>
        </div>
    </div>

    <div class="admin-section">
        <h2>⚙️ Panel de Administración</h2>
        <div class="admin-links">
            <a href="{{ route('admin.usuarios') }}" class="admin-link">
                👥 Gestionar Usuarios
            </a>
            {{-- Aquí irán más enlaces cuando agregues más secciones --}}
        </div>
    </div>
@endif

{{-- PERFIL — visible para todos --}}
<div class="profile-card">
    <h2>👤 Mi Perfil</h2>
    <div class="profile-row">
        <span class="key">Nombre</span>
        <span class="val">{{ $user->name }}</span>
    </div>
    <div class="profile-row">
        <span class="key">Correo</span>
        <span class="val">{{ $user->email }}</span>
    </div>
    <div class="profile-row">
        <span class="key">Rol</span>
        <span class="val">
            <span class="role-badge {{ $user->isAdmin() ? 'role-admin' : 'role-cliente' }}">
                {{ $user->role }}
            </span>
        </span>
    </div>
    <div class="profile-row">
        <span class="key">Miembro desde</span>
        <span class="val">{{ $user->created_at->format('d/m/Y') }}</span>
    </div>
</div>

@endsection
