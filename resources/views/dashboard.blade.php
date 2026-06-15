@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
/* ── PAGE HEADER ── */
.dash-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 2rem;
    gap: 1rem;
    flex-wrap: wrap;
}
.dash-heading { font-size: 1.6rem; font-weight: 800; color: #0f172a; line-height: 1.2; }
.dash-heading span { color: #e63232; }
.dash-sub { font-size: .85rem; color: #94a3b8; margin-top: .3rem; }
.dash-actions { display: flex; gap: .6rem; flex-wrap: wrap; }

/* ── TIME FILTER ── */
.time-tabs {
    display: inline-flex;
    background: #f1f5f9;
    border-radius: 10px;
    padding: 3px;
    gap: 2px;
    margin-bottom: 1.8rem;
}
.time-tab {
    padding: 6px 18px;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    border-radius: 8px;
    cursor: pointer;
    border: none;
    background: transparent;
    transition: all .15s;
}
.time-tab.active {
    background: #fff;
    color: #0f172a;
    box-shadow: 0 1px 3px rgba(0,0,0,.1);
}

/* ── STATS GRID ── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.stat-card {
    background: #fff;
    border-radius: 14px;
    padding: 1.4rem 1.5rem 1.2rem;
    border: 1px solid #e2e8f0;
    position: relative;
    overflow: hidden;
    transition: box-shadow .2s, transform .2s;
}
.stat-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,.08); transform: translateY(-2px); }
.stat-card.dark {
    background: linear-gradient(135deg, #0f172a 60%, #1e293b);
    border-color: transparent;
}
.stat-card .sc-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    margin-bottom: 1rem;
}
.stat-card .sc-label {
    font-size: .72rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .6px; color: #94a3b8;
}
.stat-card.dark .sc-label { color: rgba(255,255,255,.45); }
.stat-card .sc-value {
    font-size: 2rem; font-weight: 800; color: #0f172a;
    margin: .25rem 0;
    line-height: 1;
}
.stat-card.dark .sc-value { color: #fff; }
.stat-card .sc-delta {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .75rem; font-weight: 600;
    padding: 3px 8px; border-radius: 6px;
    margin-top: .4rem;
}
.sc-delta.up   { background: #f0fdf4; color: #166534; }
.sc-delta.down { background: #fef2f2; color: #991b1b; }
.sc-delta.dark-up { background: rgba(34,197,94,.15); color: #4ade80; }
.stat-card .sc-sub { font-size: .78rem; color: #94a3b8; margin-top: .3rem; }
.stat-card.dark .sc-sub { color: rgba(255,255,255,.35); }
/* decorative blob */
.sc-blob {
    position: absolute; right: -20px; top: -20px;
    width: 90px; height: 90px;
    border-radius: 50%;
    opacity: .07;
}

/* ── TWO-COL LAYOUT ── */
.dash-cols {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 1rem;
    margin-bottom: 1rem;
}
@media (max-width: 960px) { .dash-cols { grid-template-columns: 1fr; } }

/* ── PANEL CARD ── */
.panel {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
}
.panel-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1.1rem 1.4rem;
    border-bottom: 1px solid #f1f5f9;
}
.panel-title { font-size: .9rem; font-weight: 700; color: #0f172a; }
.panel-sub   { font-size: .75rem; color: #94a3b8; margin-top: 1px; }
.panel-body  { padding: 1.2rem 1.4rem; }

/* ── MINI CHART ── */
.chart-bars {
    display: flex;
    align-items: flex-end;
    gap: 6px;
    height: 110px;
    padding-bottom: 8px;
    border-bottom: 1px solid #f1f5f9;
    margin-bottom: .8rem;
}
.bar-col { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 3px; }
.bar-inner {
    width: 100%; border-radius: 5px 5px 0 0;
    background: #e2e8f0;
    transition: background .2s;
    min-height: 4px;
}
.bar-inner.active { background: linear-gradient(to top, #e63232, #ff6b6b); }
.bar-lbl { font-size: 10px; color: #94a3b8; font-weight: 600; }
.chart-legend {
    display: flex; gap: 1rem;
    font-size: .75rem; color: #94a3b8;
}
.legend-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 4px; }

/* ── RECENT USERS ── */
.user-list { display: flex; flex-direction: column; }
.user-row {
    display: flex; align-items: center; gap: 10px;
    padding: .75rem 1.4rem;
    border-bottom: 1px solid #f8fafc;
    transition: background .15s;
    text-decoration: none;
}
.user-row:last-child { border-bottom: none; }
.user-row:hover { background: #fafafa; }
.u-av-sm {
    width: 36px; height: 36px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700; flex-shrink: 0;
}
.u-nm  { font-size: .83rem; font-weight: 600; color: #0f172a; }
.u-em  { font-size: .72rem; color: #94a3b8; }
.u-badge {
    margin-left: auto;
    font-size: .7rem; font-weight: 700;
    padding: 3px 9px; border-radius: 999px;
}
.ub-admin   { background: #ede9fe; color: #6d28d9; }
.ub-usuario { background: #dcfce7; color: #166534; }

/* ── ROLE BARS ── */
.role-bars { display: flex; flex-direction: column; gap: .9rem; }
.rb-row {}
.rb-top { display: flex; justify-content: space-between; font-size: .78rem; margin-bottom: 5px; }
.rb-label { font-weight: 600; color: #0f172a; }
.rb-pct   { color: #94a3b8; font-weight: 600; }
.rb-track { height: 7px; background: #f1f5f9; border-radius: 999px; overflow: hidden; }
.rb-fill  { height: 100%; border-radius: 999px; }

/* ── QUICK ACCESS ── */
.quick-grid {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: .7rem;
    padding: 1.2rem 1.4rem;
}
.qa-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: .9rem 1rem;
    text-decoration: none;
    display: block;
    transition: all .15s;
}
.qa-card:hover { background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,.07); border-color: #c7d2fe; transform: translateY(-1px); }
.qa-icon { font-size: 20px; margin-bottom: .4rem; }
.qa-nm   { font-size: .8rem; font-weight: 700; color: #0f172a; }
.qa-sub  { font-size: .7rem; color: #94a3b8; margin-top: 1px; }

/* ── SECOND ROW ── */
.dash-bottom {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
}
@media (max-width: 720px) { .dash-bottom { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')

{{-- HEADER --}}
<div class="dash-header">
    <div>
        <div class="dash-heading">Bienvenido, <span>{{ explode(' ', $user->name)[0] }}</span> 👋</div>
        <div class="dash-sub">{{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }} · Panel de control</div>
    </div>
    @if($user->isAdmin())
    <div class="dash-actions">
        <button class="btn btn-secondary btn-sm"><i class="ti ti-download" style="font-size:14px"></i> Exportar</button>
        <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary btn-sm">
            <i class="ti ti-user-plus" style="font-size:14px"></i> Nuevo usuario
        </a>
    </div>
    @endif
</div>

{{-- TIME FILTER --}}
@if($user->isAdmin())
<div class="time-tabs">
    <button class="time-tab">Día</button>
    <button class="time-tab">Semana</button>
    <button class="time-tab active">Mes</button>
    <button class="time-tab">Año</button>
</div>

{{-- STATS --}}
<div class="stats-grid">
    <div class="stat-card dark">
        <div class="sc-blob" style="background:#6366f1"></div>
        <div class="sc-icon" style="background:rgba(99,102,241,.2)"><i class="ti ti-users" style="color:#818cf8"></i></div>
        <div class="sc-label">Usuarios totales</div>
        <div class="sc-value">{{ \App\Models\User::count() }}</div>
        <span class="sc-delta dark-up"><i class="ti ti-trending-up" style="font-size:11px"></i> 12% vs mes pasado</span>
    </div>
    <div class="stat-card">
        <div class="sc-blob" style="background:#10b981"></div>
        <div class="sc-icon" style="background:#f0fdf4"><i class="ti ti-user-check" style="color:#10b981"></i></div>
        <div class="sc-label">Usuarios activos</div>
        <div class="sc-value">{{ \App\Models\User::where('role','usuario')->count() }}</div>
        <span class="sc-delta up"><i class="ti ti-trending-up" style="font-size:11px"></i> 3 nuevos este mes</span>
    </div>
    <div class="stat-card">
        <div class="sc-blob" style="background:#6366f1"></div>
        <div class="sc-icon" style="background:#ede9fe"><i class="ti ti-shield-check" style="color:#6d28d9"></i></div>
        <div class="sc-label">Administradores</div>
        <div class="sc-value">{{ \App\Models\User::where('role','admin')->count() }}</div>
        <div class="sc-sub">Con acceso total</div>
    </div>
    <div class="stat-card">
        <div class="sc-blob" style="background:#f59e0b"></div>
        <div class="sc-icon" style="background:#fffbeb"><i class="ti ti-user-plus" style="color:#d97706"></i></div>
        <div class="sc-label">Nuevos hoy</div>
        <div class="sc-value">2</div>
        <span class="sc-delta down"><i class="ti ti-trending-down" style="font-size:11px"></i> 1 vs ayer</span>
    </div>
</div>

{{-- MAIN COLS --}}
<div class="dash-cols">
    {{-- Actividad --}}
    <div class="panel">
        <div class="panel-head">
            <div>
                <div class="panel-title">Actividad de usuarios</div>
                <div class="panel-sub">Últimos 6 meses</div>
            </div>
            <a href="{{ route('admin.usuarios') }}" class="btn btn-secondary btn-sm">Ver más</a>
        </div>
        <div class="panel-body">
            <div class="chart-bars">
                @php $bars = [30,55,40,70,50,85,60,90,45,75,88,65]; $months = ['E','F','M','A','M','J','J','A','S','O','N','D']; $curMonth = now()->month - 1; @endphp
                @foreach($months as $i => $m)
                <div class="bar-col">
                    <div class="bar-inner {{ $i === $curMonth ? 'active' : '' }}" style="height:{{ $bars[$i] }}%"></div>
                    <div class="bar-lbl">{{ $m }}</div>
                </div>
                @endforeach
            </div>
            <div class="chart-legend" style="margin-top:.8rem">
                <span><span class="legend-dot" style="background:#e63232"></span>Registros</span>
                <span><span class="legend-dot" style="background:#e2e8f0"></span>Meses anteriores</span>
            </div>
        </div>
    </div>

    {{-- Usuarios recientes --}}
    <div class="panel">
        <div class="panel-head">
            <div>
                <div class="panel-title">Usuarios recientes</div>
                <div class="panel-sub">Últimos registros</div>
            </div>
            <a href="{{ route('admin.usuarios') }}" class="btn btn-secondary btn-sm">Ver todos</a>
        </div>
        <div class="user-list">
            @foreach(\App\Models\User::latest()->take(5)->get() as $u)
            <div class="user-row">
                <div class="u-av-sm" style="background:{{ $u->isAdmin() ? '#ede9fe' : '#dcfce7' }};color:{{ $u->isAdmin() ? '#6d28d9' : '#166534' }}">
                    {{ strtoupper(substr($u->name,0,2)) }}
                </div>
                <div>
                    <div class="u-nm">{{ $u->name }}</div>
                    <div class="u-em">{{ $u->email }}</div>
                </div>
                <span class="u-badge {{ $u->isAdmin() ? 'ub-admin' : 'ub-usuario' }}">{{ ucfirst($u->role) }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- BOTTOM ROW --}}
<div class="dash-bottom">
    {{-- Distribución roles --}}
    <div class="panel">
        <div class="panel-head">
            <div>
                <div class="panel-title">Distribución de roles</div>
                <div class="panel-sub">Estado actual</div>
            </div>
        </div>
        <div class="panel-body">
            @php
                $total = \App\Models\User::count() ?: 1;
                $usuarios = \App\Models\User::where('role','usuario')->count();
                $admins   = \App\Models\User::where('role','admin')->count();
            @endphp
            <div class="role-bars">
                <div class="rb-row">
                    <div class="rb-top"><span class="rb-label">Usuarios</span><span class="rb-pct">{{ round($usuarios/$total*100) }}%</span></div>
                    <div class="rb-track"><div class="rb-fill" style="width:{{ round($usuarios/$total*100) }}%;background:linear-gradient(90deg,#6366f1,#818cf8)"></div></div>
                </div>
                <div class="rb-row">
                    <div class="rb-top"><span class="rb-label">Admins</span><span class="rb-pct">{{ round($admins/$total*100) }}%</span></div>
                    <div class="rb-track"><div class="rb-fill" style="width:{{ round($admins/$total*100) }}%;background:linear-gradient(90deg,#e63232,#ff6b6b)"></div></div>
                </div>
                <div class="rb-row">
                    <div class="rb-top"><span class="rb-label">Activos hoy</span><span class="rb-pct">60%</span></div>
                    <div class="rb-track"><div class="rb-fill" style="width:60%;background:linear-gradient(90deg,#10b981,#34d399)"></div></div>
                </div>
                <div class="rb-row">
                    <div class="rb-top"><span class="rb-label">Nuevos este mes</span><span class="rb-pct">40%</span></div>
                    <div class="rb-track"><div class="rb-fill" style="width:40%;background:linear-gradient(90deg,#f59e0b,#fbbf24)"></div></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Accesos rápidos --}}
    <div class="panel">
        <div class="panel-head">
            <div>
                <div class="panel-title">Accesos rápidos</div>
                <div class="panel-sub">Atajos del sistema</div>
            </div>
        </div>
        <div class="quick-grid">
            <a href="{{ route('admin.usuarios.create') }}" class="qa-card">
                <div class="qa-icon">👤</div>
                <div class="qa-nm">Crear usuario</div>
                <div class="qa-sub">Agregar nuevo</div>
            </a>
            <a href="{{ route('admin.usuarios') }}" class="qa-card">
                <div class="qa-icon">👥</div>
                <div class="qa-nm">Ver usuarios</div>
                <div class="qa-sub">Gestionar todos</div>
            </a>
            <a href="{{ route('perfil') }}" class="qa-card">
                <div class="qa-icon">🪪</div>
                <div class="qa-nm">Mi perfil</div>
                <div class="qa-sub">Ver mis datos</div>
            </a>
            <a href="#" class="qa-card">
                <div class="qa-icon">📊</div>
                <div class="qa-nm">Reportes</div>
                <div class="qa-sub">Estadísticas</div>
            </a>
        </div>
    </div>
</div>

@else
{{-- VISTA USUARIO --}}
<div class="stats-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));margin-bottom:1.5rem">

    <div class="stat-card dark">
        <div class="sc-blob" style="background:#6366f1"></div>
        <div class="sc-icon" style="background:rgba(99,102,241,.2)">
            <i class="ti ti-building" style="color:#818cf8"></i>
        </div>
        <div class="sc-label">Clientes</div>
        <div class="sc-value">{{ $totalClients }}</div>
        <div class="sc-sub">Empresas registradas</div>
    </div>

    <div class="stat-card">
        <div class="sc-blob" style="background:#10b981"></div>
        <div class="sc-icon" style="background:#f0fdf4">
            <i class="ti ti-address-book" style="color:#10b981"></i>
        </div>
        <div class="sc-label">Contactos</div>
        <div class="sc-value">{{ $totalContacts }}</div>
        <div class="sc-sub">En total</div>
    </div>

    <div class="stat-card">
        <div class="sc-blob" style="background:#f59e0b"></div>
        <div class="sc-icon" style="background:#fffbeb">
            <i class="ti ti-truck" style="color:#d97706"></i>
        </div>
        <div class="sc-label">Proveedores</div>
        <div class="sc-value">{{ $totalSuppliers }}</div>
        <div class="sc-sub">Registrados</div>
    </div>

    <div class="stat-card">
        <div class="sc-blob" style="background:#6366f1"></div>
        <div class="sc-icon" style="background:#ede9fe">
            <i class="ti ti-map-pin" style="color:#6d28d9"></i>
        </div>
        <div class="sc-label">Destinos</div>
        <div class="sc-value">{{ $totalDestinations }}</div>
        <div class="sc-sub">Disponibles</div>
    </div>

</div>

{{-- DOS COLUMNAS --}}
<div class="dash-cols" style="margin-bottom:1rem">

    {{-- Clientes recientes --}}
    <div class="panel">
        <div class="panel-head">
            <div>
                <div class="panel-title">Clientes recientes</div>
                <div class="panel-sub">Últimos registrados</div>
            </div>
            <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary btn-sm">Ver todos</a>
        </div>
        <div class="user-list">
            @forelse($recentClients as $c)
            <a href="{{ route('admin.clients.index') }}" class="user-row">
                <div class="u-av-sm" style="background:#dbeafe;color:#1d4ed8">
                    {{ strtoupper(substr($c->name_client, 0, 2)) }}
                </div>
                <div>
                    <div class="u-nm">{{ $c->name_client }}</div>
                    <div class="u-em">{{ $c->contacts_count }} contacto(s)</div>
                </div>
                <span class="u-badge" style="background:#dcfce7;color:#166534">ACTIVO</span>
            </a>
            @empty
            <div style="padding:1.5rem;text-align:center;color:#94a3b8;font-size:13px">
                Sin clientes aún
            </div>
            @endforelse
        </div>
    </div>

    {{-- Mi cuenta --}}
    <div class="panel">
        <div class="panel-head">
            <div>
                <div class="panel-title">Mi cuenta</div>
                <div class="panel-sub">Información personal</div>
            </div>
            <a href="{{ route('perfil.edit') }}" class="btn btn-secondary btn-sm">
                <i class="ti ti-edit" style="font-size:13px"></i> Editar
            </a>
        </div>
        <div style="padding:1.2rem 1.4rem;display:flex;flex-direction:column;gap:.7rem">
            @foreach([
                ['ti-user',    'Nombre',        $user->name],
                ['ti-mail',    'Correo',         $user->email],
                ['ti-calendar','Miembro desde',  $user->created_at->format('d/m/Y')],
            ] as [$ico, $label, $val])
            <div style="display:flex;align-items:center;gap:10px;padding:.6rem 0;border-bottom:1px solid #f8fafc">
                <i class="ti {{ $ico }}" style="font-size:16px;color:#94a3b8;width:20px;text-align:center"></i>
                <span style="font-size:.83rem;color:#64748b;width:110px;flex-shrink:0">{{ $label }}</span>
                <span style="font-size:.83rem;font-weight:600;color:#0f172a">{{ $val }}</span>
            </div>
            @endforeach
        </div>
    </div>

</div>

{{-- FILA INFERIOR --}}
<div class="dash-bottom">

    {{-- Proveedores recientes --}}
    <div class="panel">
        <div class="panel-head">
            <div>
                <div class="panel-title">Proveedores recientes</div>
                <div class="panel-sub">Últimos registrados</div>
            </div>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary btn-sm">Ver todos</a>
        </div>
        <div class="user-list">
            @forelse($recentSuppliers as $s)
            <a href="{{ route('admin.suppliers.index') }}" class="user-row">
                <div class="u-av-sm" style="background:#f0f9ff;color:#0369a1">
                    {{ strtoupper(substr($s->supplier_name, 0, 2)) }}
                </div>
                <div>
                    <div class="u-nm">{{ $s->supplier_name }}</div>
                    <div class="u-em">
                        {{ $s->destination?->destination_name ?? 'Sin destino' }}
                        @if($s->category) · {{ $s->category->category_name }} @endif
                    </div>
                </div>
            </a>
            @empty
            <div style="padding:1.5rem;text-align:center;color:#94a3b8;font-size:13px">
                Sin proveedores aún
            </div>
            @endforelse
        </div>
    </div>

    {{-- Accesos rápidos --}}
    <div class="panel">
        <div class="panel-head">
            <div>
                <div class="panel-title">Accesos rápidos</div>
                <div class="panel-sub">Atajos del sistema</div>
            </div>
        </div>
        <div class="quick-grid">
            <a href="{{ route('admin.clients.index') }}" class="qa-card">
                <div class="qa-icon">🏢</div>
                <div class="qa-nm">Clientes</div>
                <div class="qa-sub">Ver todos</div>
            </a>
            <a href="{{ route('admin.contacts.index') }}" class="qa-card">
                <div class="qa-icon">📇</div>
                <div class="qa-nm">Contactos</div>
                <div class="qa-sub">Gestionar</div>
            </a>
            <a href="{{ route('admin.suppliers.index') }}" class="qa-card">
                <div class="qa-icon">🚚</div>
                <div class="qa-nm">Proveedores</div>
                <div class="qa-sub">Ver todos</div>
            </a>
            <a href="{{ route('perfil') }}" class="qa-card">
                <div class="qa-icon">🪪</div>
                <div class="qa-nm">Mi perfil</div>
                <div class="qa-sub">Ver mis datos</div>
            </a>
        </div>
    </div>

</div>
@endif

@endsection
