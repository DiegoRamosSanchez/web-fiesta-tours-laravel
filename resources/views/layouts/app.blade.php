<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Fiesta Tours')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    {{-- Anti-flash: aplica el estado guardado ANTES del primer paint --}}
    <script>
        (function() {
            if (localStorage.getItem('sidebarCollapsed') === '1') {
                document.documentElement.classList.add('sidebar-collapsed-init');
            }
        })();
    </script>

    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #f1f5f9; display: flex; height: 100vh; overflow: hidden; color: #0f172a; }

        /* ══ SIDEBAR ══ */
        .sidebar {
            width: 210px;
            height: 100vh;
            background: #0f172a;
            flex-shrink: 0;
            overflow: hidden;
            transition: width .28s cubic-bezier(.4,0,.2,1);
        }
        .sidebar.collapsed { width: 64px; }

        .sidebar-inner {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            background: #0f172a;
        }

        .sb-head { padding: 1.3rem 1.2rem 1rem; border-bottom: 1px solid rgba(255,255,255,.07); overflow: hidden; }
        .sb-brand { display: flex; align-items: center; gap: 10px; }
        .sb-icon { width: 34px; height: 34px; background: #e63232; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; color: #fff; flex-shrink: 0; }
        .sb-name { font-size: 13px; font-weight: 700; color: #fff; white-space: nowrap; }
        .sb-tagline { font-size: 10px; color: rgba(255,255,255,.35); margin-top: 1px; white-space: nowrap; }

        .sb-nav { flex: 1; padding: .8rem .8rem 0; overflow-y: auto; overflow-x: hidden; }

        .sb-group { font-size: 10px; font-weight: 600; color: rgba(255,255,255,.3); letter-spacing: .8px; text-transform: uppercase; padding: .8rem .6rem .3rem; white-space: nowrap; }
        .sb-link { display: flex; align-items: center; gap: 10px; padding: .56rem .8rem; border-radius: 8px; font-size: 13px; color: rgba(255,255,255,.55); cursor: pointer; text-decoration: none; transition: all .15s; margin-bottom: 1px; white-space: nowrap; }
        .sb-link i { font-size: 16px; flex-shrink: 0; }
        .sb-link:hover { background: rgba(255,255,255,.07); color: rgba(255,255,255,.85); }
        .sb-link.active { background: #e63232; color: #fff; }
        .sb-text { transition: opacity .15s; }

        .sb-foot { padding: .8rem; border-top: 1px solid rgba(255,255,255,.07); overflow: hidden; }
        .sb-user { display: flex; align-items: center; gap: 9px; padding: .7rem .8rem; background: rgba(255,255,255,.05); border-radius: 10px; border: 1px solid rgba(255,255,255,.07); white-space: nowrap; }
        .sb-av { width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(135deg,#6366f1,#8b5cf6); display: flex; align-items: center; justify-content: center; font-size: 11px; color: #fff; font-weight: 700; flex-shrink: 0; }
        .sb-uname { font-size: 12px; font-weight: 600; color: #fff; white-space: nowrap; }
        .sb-urole { font-size: 10px; color: rgba(255,255,255,.35); white-space: nowrap; }

        /* ══ ESTADO COLAPSADO (íconos centrados, sin texto) ══ */
        .sidebar.collapsed .sb-group,
        .sidebar.collapsed .sb-name,
        .sidebar.collapsed .sb-tagline,
        .sidebar.collapsed .sb-text,
        .sidebar.collapsed .sb-uname,
        .sidebar.collapsed .sb-urole {
            display: none;
        }
        .sidebar.collapsed .sb-brand { justify-content: center; }
        .sidebar.collapsed .sb-link { justify-content: center; padding: .6rem; gap: 0; }
        .sidebar.collapsed .sb-user { justify-content: center; padding: .7rem; }
        .sidebar.collapsed .sb-head { padding: 1.3rem .6rem 1rem; }
        .sidebar.collapsed .sb-nav { padding: .8rem .5rem 0; }
        .sidebar.collapsed .sb-foot { padding: .8rem .5rem; }

        /* Estado inicial (aplicado antes del primer paint, sin transición) */
        html.sidebar-collapsed-init .sidebar { width: 64px; transition: none; }
        html.sidebar-collapsed-init .sidebar .sb-group,
        html.sidebar-collapsed-init .sidebar .sb-name,
        html.sidebar-collapsed-init .sidebar .sb-tagline,
        html.sidebar-collapsed-init .sidebar .sb-text,
        html.sidebar-collapsed-init .sidebar .sb-uname,
        html.sidebar-collapsed-init .sidebar .sb-urole { display: none; }
        html.sidebar-collapsed-init .sidebar .sb-brand,
        html.sidebar-collapsed-init .sidebar .sb-link,
        html.sidebar-collapsed-init .sidebar .sb-user { justify-content: center; }
        html.sidebar-collapsed-init .sidebar .sb-link { padding: .6rem; gap: 0; }
        html.sidebar-collapsed-init .search-box i { transform: rotate(180deg); }

        /* ══ MAIN ══ */
        .main-wrap { flex: 1; display: flex; flex-direction: column; overflow: hidden; }

        /* ══ TOPBAR ══ */
        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: .8rem  1.6rem .8rem 0rem; display: flex; align-items: center; gap: 1rem; flex-shrink: 0; }
        .search-box { border: none; outline: none; display: flex; align-items: center; gap: 8px; background: #0F172A; border-radius: 0px 9px 9px 0px; padding: 10px 10px 10px 5px; cursor: pointer; }
        .search-box i { font-size: 25px; color: #ffffff; transition: transform .28s cubic-bezier(.4,0,.2,1); }
        .search-box.is-collapsed i { transform: rotate(180deg); }
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
        .table-wrap { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 0.5rem; }
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
        
        .btn-support {
            width: 100%;
            border: none;
            border-radius: 9px;
            padding: 12px 15px;
            display: flex;
            gap:10px;
            align-items:center;
            color: rgba(255,255,255,.55);
            font-weight: 600;
            background-color:transparent;
            cursor: pointer;
        }
        .btn-support:hover {
            background: #111a30d2;
        }

        #supportModal {
            display: none;
            position: fixed;
            width: 100%;
            height: 100vh;
            background: rgba(17, 17, 17, 0.64);
            z-index: 300;
            align-items: center;
            justify-content: center;
            top: 0;
            left: 0;
        }
        #supportModal.show {
            display: flex;
        }

        .modal-content {
            background: #fff;
            border-radius: 12px;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            position: relative;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-close {
            position: absolute;
            top: 0.8rem;
            right: 0.8rem;
            background: none;
            border: none;
            font-size: 24px;
            color: #64748b;
            cursor: pointer;
            transition: color 0.2s;
        }
        .modal-close:hover {
            color: #0f172a;
        }

        .modal-title {
            font-size: 24px;
            font-weight: 800;
            display:flex;
            align-items:center;
            gap:10px;
            color: #6366F1;
            margin-bottom: 6px;
        }
        .modal-subtitle {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 4px;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            transition: border-color 0.2s;
            background: #f8fafc;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #6366F1;
            background: white;
            box-shadow: 0 0 0 3px rgba(48, 83, 60, 0.1);
        }
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        .form-group .char-count {
            font-size: 11px;
            color: #94a3b8;
            text-align: right;
            margin-top: 4px;
        }

        .btn-submit {
            width: 100%;
            background: #6366F1;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-submit:hover {
            background: #5254ce;
        }
        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-top: 12px;
            display: none;
        }
        .alert.success {
            display: block;
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }
        .alert.error {
            display: block;
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fca5a5;
        }
   
   </style>
    @stack('styles')
</head>
<body>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-inner">
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
               class="sb-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard">
                <i class="ti ti-layout-dashboard"></i> <span class="sb-text">Dashboard</span>
            </a>

            {{-- GESTIÓN: visible para TODOS (admin y usuario) --}}
            <div class="sb-group">Gestión</div>
            <a href="{{ route('admin.clients.index') }}"
                class="sb-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}" title="Clientes">
                <i class="ti ti-building"></i> <span class="sb-text">Clientes</span>
            </a>
            <a href="{{ route('admin.contacts.index') }}"
                class="sb-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}" title="Contactos">
                <i class="ti ti-address-book"></i> <span class="sb-text">Contactos</span>
            </a>

            <a href="{{ route('admin.suppliers.index') }}"
                class="sb-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}" title="Proveedores">
                <i class="ti ti-truck"></i> <span class="sb-text">Proveedores</span>
            </a>

            <a href="{{ route('admin.categories.index') }}"
                class="sb-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" title="Categorías">
                <i class="ti ti-tag"></i> <span class="sb-text">Categorías</span>
            </a>

            {{-- ADMINISTRACIÓN: solo admin --}}
            @if(auth()->user()->isAdmin())
                <div class="sb-group">Administración</div>
                <a href="{{ route('admin.usuarios') }}"
                   class="sb-link {{ request()->routeIs('admin.usuarios') ? 'active' : '' }}" title="Usuarios">
                    <i class="ti ti-users"></i> <span class="sb-text">Usuarios</span>
                </a>
                <a href="{{ route('admin.usuarios.create') }}"
                   class="sb-link {{ request()->routeIs('admin.usuarios.create') ? 'active' : '' }}" title="Crear usuario">
                    <i class="ti ti-user-plus"></i> <span class="sb-text">Crear usuario</span>
                </a>

                <a href="#" class="sb-link" title="Reportes">
                    <i class="ti ti-chart-bar"></i> <span class="sb-text">Reportes</span>
                </a>
            @endif

            {{-- GENERAL --}}
            <div class="sb-group">General</div>
            <a href="{{ route('perfil') }}" class="sb-link {{ request()->routeIs('perfil*') ? 'active' : '' }}" title="Configuración">
                <i class="ti ti-settings"></i> <span class="sb-text">Configuración</span>
            </a>
            <button href="#" class="btn-support" title="Ayuda" onclick="openModal()">
                <i class="ti ti-help"></i> <span class="sb-text">Ayuda</span>
            </button>
            <form action="{{ route('logout') }}" method="POST" style="margin:1px 0">
                @csrf
                <button type="submit" class="sb-link" title="Cerrar sesión"
                    style="width:100%;border:none;background:none;text-align:left;
                        cursor:pointer;color:rgba(255,255,255,.55)">
                    <i class="ti ti-logout"></i> <span class="sb-text">Cerrar sesión</span>
                </button>
            </form>

        </nav>

        <div class="sb-foot">
            <div class="sb-user">
                 @php
                    $user = auth()->user();
                @endphp
                 @if($user->avatar)
                  @php
                        $filename = basename($user->avatar);
                @endphp
                    <div class="sb-av"><img class="sb-av" src="{{ route('avatar.show', $filename) }}" /></div>
                @else
                    <div class="sb-av">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                @endif
                            <div>
                    <div class="sb-uname">{{ Str::limit(auth()->user()->name, 16) }}</div>
                    <div class="sb-urole">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
            </div>
        </div>
    </div>
</aside>

{{-- ══ MAIN ══ --}}
<div class="main-wrap">

    <header  class="topbar">
        <button class="search-box" id="sidebar-toggle" title="Colapsar/Expandir menú">
           <i class="ti ti-layout-sidebar-right-expand"></i>
        </button>

        <div class="tb-right">
            <a href="#" class="ib" title="Mensajes"><i class="ti ti-mail"></i></a>
            <a href="#" class="ib" title="Notificaciones">
                <i class="ti ti-bell"></i>
                <span class="nd"></span>
            </a>

            {{-- DROPDOWN PERFIL --}}
            <div class="u-menu" id="userMenu">
                <div class="u-trigger" onclick="toggleMenu()">
                    @php
                        $user = auth()->user();
                    @endphp
                    <div class="u-av">
                        @if($user->avatar)
                            @php
                                $filename = basename($user->avatar);
                            @endphp
                            <img class="u-av" src="{{ route('avatar.show', $filename) }}" alt="{{ $user->name }}" />
                        @else
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        @endif
                    </div>

                    <div>
                        <div class="u-name">{{ Str::limit($user->name, 14) }}</div>
                        <div class="u-role">{{ ucfirst($user->role) }}</div>
                    </div>
                    <i class="ti ti-chevron-down" style="font-size:13px;color:#94a3b8;margin-left:2px"></i>
                </div>
                <div class="u-dd">
                    <div class="dd-header">
                        <div class="dd-fullname">{{ $user->name }}</div>
                        <div class="dd-email">{{ $user->email }}</div>
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

<div id="supportModal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeModal()">&times;</button>
        
        <h2 class="modal-title"><i class="ti ti-help-circle"></i>Esto es grave</h2>
        <p class="modal-subtitle">Descripción del Problema</p>

        <form id="supportForm" action="{{ route('support.send') }}" method="POST">
            @csrf
            <input type="hidden" id="email" name="email" value="{{ auth()->user()->email }}">

            <div class="form-group">
                <textarea id="mensaje" name="mensaje" placeholder="Describe tu problema aquí..." required minlength="10" maxlength="1000"></textarea>
            </div>


            <button type="submit" class="btn-submit" id="btnSubmit">
                Enviar Mensaje
            </button>

            <div id="alertMessage" class="alert" style="display: none;"></div>
        </form>
    </div>
</div>


<script>


document.getElementById('supportForm').addEventListener('submit', async function(e) {
    e.preventDefault(); 
    const form = this;
    const btnSubmit = document.getElementById('btnSubmit');
    const alertMessage = document.getElementById('alertMessage');
    const originalBtnText = btnSubmit.innerHTML;

    btnSubmit.disabled = true;
    btnSubmit.innerHTML = 'Enviando...';

    const formData = new FormData(form);

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        alertMessage.style.display = 'block';
        
        if (response.ok && data.success) {
            alertMessage.className = 'alert alert-success';
            alertMessage.textContent = data.message;
            form.reset();
            setTimeout(() => {
                if (typeof closeModal === 'function') closeModal();
                alertMessage.style.display = 'none';
            }, 2000);

        } else {
            alertMessage.className = 'alert alert-danger';
            alertMessage.textContent = data.message || 'Ocurrió un error al procesar la solicitud.';
        }

    } catch (error) {
        alertMessage.style.display = 'block';
        alertMessage.className = 'alert alert-danger';
        alertMessage.textContent = 'Error de conexión. Inténtalo de nuevo más tarde.';
    } finally {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = originalBtnText;
    }
});
function openModal() {
            document.getElementById('supportModal').classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('supportModal').classList.remove('show');
            document.body.style.overflow = 'auto';
            const alert = document.getElementById('alertMessage');
            alert.className = 'alert';
            alert.style.display = 'none';
        }
    (function() {
        const html      = document.documentElement;
        const sidebar   = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sidebar-toggle');

        const savedCollapsed = localStorage.getItem('sidebarCollapsed') === '1';
        if (savedCollapsed) {
            sidebar.classList.add('collapsed');
            toggleBtn.classList.add('is-collapsed');
        }
        html.classList.remove('sidebar-collapsed-init');

        toggleBtn.addEventListener('click', function() {
            const collapsed = sidebar.classList.toggle('collapsed');
            toggleBtn.classList.toggle('is-collapsed', collapsed);
            localStorage.setItem('sidebarCollapsed', collapsed ? '1' : '0');
        });
    })();

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
