<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Fiesta Tours')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #f1f5f9; display: flex; height: 100vh; overflow: hidden; color: #0f172a; }

        /* ══ SIDEBAR ══ */
        .sidebar { width: 220px; background: #0f172a; display: flex; flex-direction: column; flex-shrink: 0; }
        .sb-head { padding: 1.3rem 1.2rem 1rem; border-bottom: 1px solid rgba(255,255,255,.07); }
        .sb-brand { display: flex; align-items: center; gap: 10px; }
        .sb-icon { width: 34px; height: 34px; background: #e63232; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; color: #fff; flex-shrink: 0; }
        .sb-name { font-size: 13px; font-weight: 700; color: #fff; }
        .sb-tagline { font-size: 10px; color: rgba(255,255,255,.35); margin-top: 1px; }
        .sb-nav { flex: 1; padding: .8rem .8rem 0; overflow-y: auto; }
        .sb-group { font-size: 10px; font-weight: 600; color: rgba(255,255,255,.3); letter-spacing: .8px; text-transform: uppercase; padding: .8rem .6rem .3rem; }
        .sb-link { display: flex; align-items: center; gap: 10px; padding: .56rem .8rem; border-radius: 8px; font-size: 13px; color: rgba(255,255,255,.55); cursor: pointer; text-decoration: none; transition: all .15s; margin-bottom: 1px; }
        .sb-link i { font-size: 16px; flex-shrink: 0; }
        .sb-link:hover { background: rgba(255,255,255,.07); color: rgba(255,255,255,.85); }
        .sb-link.active { background: #e63232; color: #fff; }
        .sb-foot { padding: .8rem; border-top: 1px solid rgba(255,255,255,.07); }
        .sb-user { display: flex; align-items: center; gap: 9px; padding: .7rem .8rem; background: rgba(255,255,255,.05); border-radius: 10px; border: 1px solid rgba(255,255,255,.07); }
        .sb-av { width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(135deg,#6366f1,#8b5cf6); display: flex; align-items: center; justify-content: center; font-size: 11px; color: #fff; font-weight: 700; flex-shrink: 0; }
        .sb-uname { font-size: 12px; font-weight: 600; color: #fff; }
        .sb-urole { font-size: 10px; color: rgba(255,255,255,.35); }

        /* ══ MAIN ══ */
        .main-wrap { flex: 1; display: flex; flex-direction: column; overflow: hidden; }

        /* ══ TOPBAR ══ */
        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: .8rem 1.6rem; display: flex; align-items: center; gap: 1rem; flex-shrink: 0; }
        .search-box { display: flex; align-items: center; gap: 8px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 9px; padding: 7px 13px; width: 240px; }
        .search-box i { font-size: 15px; color: #94a3b8; }
        .search-box input { border: none; background: transparent; font-size: 13px; color: #0f172a; outline: none; width: 100%; }
        .tb-right { margin-left: auto; display: flex; align-items: center; gap: 8px; }
        .ib { width: 36px; height: 36px; border-radius: 9px; border: 1px solid #e2e8f0; background: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #64748b; font-size: 17px; position: relative; text-decoration: none; }
        .ib:hover { background: #f8fafc; }
        .nd { position: absolute; top: 8px; right: 8px; width: 6px; height: 6px; background: #e63232; border-radius: 50%; border: 1.5px solid #fff; }

        /* DROPDOWN PERFIL */
        .u-menu { position: relative; }
        .u-trigger { display: flex; align-items: center; gap: 9px; padding: 5px 10px; border-radius: 10px; cursor: pointer; border: 1px solid #e2e8f0; background: #fff; transition: background .15s; }
        .u-trigger:hover { background: #f8fafc; }
        .u-av { width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg,#6366f1,#8b5cf6); display: flex; align-items: center; justify-content: center; font-size: 12px; color: #fff; font-weight: 700; flex-shrink: 0; }
        .u-name { font-size: 12px; font-weight: 600; color: #0f172a; }
        .u-role { font-size: 10px; color: #94a3b8; }
        .u-dd { display: none; position: absolute; top: calc(100% + 8px); right: 0; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: .5rem; min-width: 190px; z-index: 200; box-shadow: 0 8px 30px rgba(0,0,0,.1); }
        .u-menu.open .u-dd { display: block; }
        .dd-header { padding: .7rem .9rem .5rem; border-bottom: 1px solid #f1f5f9; margin-bottom: .4rem; }
        .dd-fullname { font-size: 13px; font-weight: 600; color: #0f172a; }
        .dd-email { font-size: 11px; color: #94a3b8; margin-top: 1px; }
        .dd-item { display: flex; align-items: center; gap: 9px; padding: .55rem .9rem; font-size: 13px; color: #0f172a; border-radius: 7px; cursor: pointer; text-decoration: none; }
        .dd-item:hover { background: #f8fafc; }
        .dd-item i { font-size: 15px; color: #64748b; }
        .dd-sep { height: 1px; background: #f1f5f9; margin: .3rem 0; }
        .dd-danger { color: #e63232 !important; }
        .dd-danger i { color: #e63232 !important; }

        /* ══ CONTENT ══ */
        .page-content { flex: 1; overflow-y: auto; padding: 1.8rem 2rem; }
        .page-header { margin-bottom: 1.6rem; }
        .page-title { font-size: 22px; font-weight: 700; color: #0f172a; }
        .page-sub { font-size: 13px; color: #64748b; margin-top: 3px; }

        /* ALERTAS */
        .alert { padding: .85rem 1.1rem; border-radius: 10px; font-size: .88rem; margin-bottom: 1.2rem; display: flex; align-items: center; gap: 8px; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .alert-error   { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .alert i { font-size: 16px; flex-shrink: 0; }

        /* CARDS */
        .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.4rem; }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem; padding-bottom: .9rem; border-bottom: 1px solid #f1f5f9; }
        .card-title { font-size: 15px; font-weight: 700; color: #0f172a; }
        .card-sub { font-size: 12px; color: #64748b; margin-top: 2px; }

        /* FORM */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.1rem; }
        .form-field label { display: block; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 5px; }
        .form-field input, .form-field select {
            width: 100%; padding: 10px 13px; border: 1px solid #e2e8f0; border-radius: 9px;
            background: #f8fafc; color: #0f172a; font-size: 13px; outline: none;
            transition: border-color .15s, background .15s;
        }
        .form-field input:focus, .form-field select:focus { border-color: #6366f1; background: #fff; box-shadow: 0 0 0 3px rgba(99,102,241,.08); }
        .form-field .hint { font-size: 11px; color: #94a3b8; margin-top: 4px; }
        .form-field .field-error { font-size: 11px; color: #e63232; margin-top: 4px; }

        /* BOTONES */
        .btn { display: inline-flex; align-items: center; gap: 7px; padding: 10px 20px; border-radius: 9px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; transition: all .15s; text-decoration: none; }
        .btn-primary { background: #e63232; color: #fff; }
        .btn-primary:hover { background: #c42a2a; }
        .btn-secondary { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }
        .btn-secondary:hover { background: #f1f5f9; }
        .btn-danger { background: #fef2f2; color: #e63232; border: 1px solid #fecaca; }
        .btn-danger:hover { background: #fee2e2; }
        .btn-sm { padding: 6px 14px; font-size: 12px; }

        /* TABLA */
        .table-wrap { border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f8fafc; }
        th { padding: .85rem 1.1rem; text-align: left; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .5px; border-bottom: 1px solid #e2e8f0; }
        td { padding: .85rem 1.1rem; border-bottom: 1px solid #f8fafc; font-size: 13px; color: #0f172a; }
        tr:last-child td { border-bottom: none; }
        tr.highlight { background: #fefce8; }
        tr:hover td { background: #fafafa; }
        .avatar-sm { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0; }
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; }
        .badge-admin   { background: #ede9fe; color: #6d28d9; }
        .badge-usuario { background: #dcfce7; color: #166534; }
        .table-footer { padding: .7rem 1.1rem; background: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; }
    </style>
    @stack('styles')
</head>
<body>

{{-- ══ SIDEBAR ══ --}}
<aside class="sidebar">
    <div class="sb-head">
        <div class="sb-brand">
            <div class="sb-icon">FT</div>
            <div>
                <div class="sb-name">Fiesta Tours</div>
                <div class="sb-tagline">Panel de control</div>
            </div>
        </div>
    </div>

    <nav class="sb-nav">

        {{-- MENÚ PRINCIPAL --}}
        <div class="sb-group">Menú</div>
        <a href="{{ route('dashboard') }}"
           class="sb-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="ti ti-layout-dashboard"></i> Dashboard
        </a>

        {{-- GESTIÓN: visible para TODOS (admin y usuario) --}}
        <div class="sb-group">Gestión</div>
        <a href="{{ route('admin.clients.index') }}"
            class="sb-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">
            <i class="ti ti-building"></i> Clientes
        </a>
        <a href="{{ route('admin.contacts.index') }}"
            class="sb-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
            <i class="ti ti-address-book"></i> Contactos
        </a>

        <a href="{{ route('admin.suppliers.index') }}"
            class="sb-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
            <i class="ti ti-truck"></i> Proveedores
        </a>
        <a href="{{ route('admin.destinations.index') }}"
            class="sb-link {{ request()->routeIs('admin.destinations.*') ? 'active' : '' }}">
            <i class="ti ti-map-pin"></i> Destinos
        </a>
        <a href="{{ route('admin.categories.index') }}"
            class="sb-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <i class="ti ti-tag"></i> Categorías
        </a>

        {{-- ADMINISTRACIÓN: solo admin --}}
        @if(auth()->user()->isAdmin())
            <div class="sb-group">Administración</div>
            <a href="{{ route('admin.usuarios') }}"
               class="sb-link {{ request()->routeIs('admin.usuarios') ? 'active' : '' }}">
                <i class="ti ti-users"></i> Usuarios
            </a>
            <a href="{{ route('admin.usuarios.create') }}"
               class="sb-link {{ request()->routeIs('admin.usuarios.create') ? 'active' : '' }}">
                <i class="ti ti-user-plus"></i> Crear usuario
            </a>
            <a href="#" class="sb-link">
                <i class="ti ti-calendar"></i> Reservas
            </a>
            <a href="#" class="sb-link">
                <i class="ti ti-chart-bar"></i> Reportes
            </a>
        @endif

        {{-- GENERAL --}}
        <div class="sb-group">General</div>
        @if(auth()->user()->isAdmin())
            <a href="#" class="sb-link">
                <i class="ti ti-settings"></i> Configuración
            </a>
        @endif
        <a href="#" class="sb-link">
            <i class="ti ti-help"></i> Ayuda
        </a>
        <form action="{{ route('logout') }}" method="POST" style="margin:1px 0">
            @csrf
            <button type="submit" class="sb-link"
                style="width:100%;border:none;background:none;text-align:left;
                       cursor:pointer;color:rgba(255,255,255,.55)">
                <i class="ti ti-logout"></i> Cerrar sesión
            </button>
        </form>

    </nav>

    <div class="sb-foot">
        <div class="sb-user">
            <div class="sb-av">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
            <div>
                <div class="sb-uname">{{ Str::limit(auth()->user()->name, 16) }}</div>
                <div class="sb-urole">{{ ucfirst(auth()->user()->role) }}</div>
            </div>
        </div>
    </div>
</aside>

{{-- ══ MAIN ══ --}}
<div class="main-wrap">

    {{-- TOPBAR --}}
    <header class="topbar">
        <div class="search-box">
            <i class="ti ti-search"></i>
            <input type="text" placeholder="Buscar...">
        </div>

        <div class="tb-right">
            <a href="#" class="ib" title="Mensajes"><i class="ti ti-mail"></i></a>
            <a href="#" class="ib" title="Notificaciones">
                <i class="ti ti-bell"></i>
                <span class="nd"></span>
            </a>

            {{-- DROPDOWN PERFIL --}}
            <div class="u-menu" id="userMenu">
                <div class="u-trigger" onclick="toggleMenu()">
                    <div class="u-av">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                    <div>
                        <div class="u-name">{{ Str::limit(auth()->user()->name, 14) }}</div>
                        <div class="u-role">{{ ucfirst(auth()->user()->role) }}</div>
                    </div>
                    <i class="ti ti-chevron-down" style="font-size:13px;color:#94a3b8;margin-left:2px"></i>
                </div>
                <div class="u-dd">
                    <div class="dd-header">
                        <div class="dd-fullname">{{ auth()->user()->name }}</div>
                        <div class="dd-email">{{ auth()->user()->email }}</div>
                    </div>
                    <a href="{{ route('perfil') }}" class="dd-item">
                        <i class="ti ti-user"></i> Mi perfil
                    </a>
                    <a href="{{ route('perfil.edit') }}" class="dd-item">
                        <i class="ti ti-edit"></i> Editar datos
                    </a>
                    <div class="dd-sep"></div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dd-item dd-danger"
                            style="width:100%;border:none;background:none;text-align:left;cursor:pointer">
                            <i class="ti ti-logout"></i> Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- CONTENIDO --}}
    <main class="page-content">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="ti ti-circle-check"></i> {{ session('success') }}
            </div>
        @endif
        @if($errors->has('error'))
            <div class="alert alert-error">
                <i class="ti ti-alert-circle"></i> {{ $errors->first('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</div>

<script>
function toggleMenu() {
    document.getElementById('userMenu').classList.toggle('open');
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('#userMenu')) {
        document.getElementById('userMenu').classList.remove('open');
    }
});
</script>
@stack('scripts')
</body>
</html>
