@extends('layouts.app')
@section('title', 'Editar Perfil')

@push('styles')
<style>
/* ── Layout principal ── */
.edit-layout {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 1.25rem;
    align-items: start;
}
@media (max-width: 768px) {
    .edit-layout { grid-template-columns: 1fr; }
}

/* ── Columna izquierda: Preview card ── */
.preview-card {
    background: linear-gradient(160deg, #0f172a 0%, #1e293b 100%);
    border-radius: 16px;
    overflow: hidden;
    position: relative;
}
.pc-accent {
    position: absolute; bottom: -30px; right: -30px;
    width: 140px; height: 140px; border-radius: 50%;
    background: rgba(99,102,241,.15); pointer-events: none;
}
.pc-bg-letter {
    position: absolute; right: 1rem; bottom: 1.5rem;
    font-size: 5rem; font-weight: 900;
    color: rgba(255,255,255,.05);
    line-height: 1; pointer-events: none; user-select: none;
}
.pc-inner { padding: 1.5rem; }
.pc-eyebrow {
    font-size: .65rem; font-weight: 600; text-transform: uppercase;
    letter-spacing: .8px; color: rgba(255,255,255,.35);
    margin-bottom: 1.25rem;
}
.pc-avatar {
    width: 72px; height: 72px; border-radius: 50%;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border: 3px solid rgba(255,255,255,.25);
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; color: #fff; font-weight: 700;
    margin-bottom: 1rem;
}
.pc-avatar_feature {
    width: 72px; height: 72px; border-radius: 50%;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border: 3px solid rgba(255,255,255,.25);
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; color: #fff; font-weight: 700;
}
.pc-name { font-size: 1rem; font-weight: 700; color: #fff; line-height: 1.3; }
.pc-email { font-size: .75rem; color: rgba(255,255,255,.5); margin-top: 3px; }
.pc-role {
    display: inline-flex; align-items: center; gap: 4px;
    margin-top: .6rem;
    font-size: .68rem; font-weight: 700;
    padding: 3px 10px; border-radius: 999px;
}

.pc-view-btn {
    display: flex; align-items: center; justify-content: center; gap: 6px;
    margin: 0 1.5rem 1.5rem;
    padding: 9px;
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.15);
    border-radius: 10px;
    color: rgba(255,255,255,.6); font-size: .78rem; font-weight: 600;
    text-decoration: none; transition: background .15s;
}
.pc-view-btn:hover { background: rgba(255,255,255,.14); }

/* ── Tips card ── */
.tips-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
    margin-top: 1rem;
}
.tc-head {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f1f5f9;
    background: #fafafa;
    display: flex; align-items: center; justify-content: space-between;
}
.tc-title {
    font-size: .72rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .6px; color: #94a3b8;
}
.tips-list { padding: 1rem 1.25rem; display: flex; flex-direction: column; gap: .6rem; }
.tip-item {
    display: flex; align-items: flex-start; gap: 8px;
    font-size: .78rem; color: #64748b; line-height: 1.45;
}
.tip-dot {
    width: 18px; height: 18px; border-radius: 50%;
    background: #ede9fe; display: flex; align-items: center;
    justify-content: center; flex-shrink: 0; margin-top: 1px;
}
.tip-dot i { font-size: 10px; color: #6d28d9; }

/* ── Columna derecha: Forms ── */
.edit-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 1rem;
}
.ec-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1.1rem 1.6rem;
    border-bottom: 1px solid #f1f5f9;
    background: #fafafa;
}
.ec-title { font-size: .9rem; font-weight: 700; color: #0f172a; }
.ec-sub   { font-size: .75rem; color: #94a3b8; margin-top: 1px; }
.ec-body  { padding: 1.6rem; }

/* Avatar preview row */
.avatar-preview {
    display: flex; align-items: center; gap: 1rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem 1.2rem;
    margin-bottom: 1.4rem;
}
.ap-circle {
    width: 48px; height: 48px; border-radius: 50%;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; color: #fff; font-weight: 700; flex-shrink: 0;
}
.ap-name  { font-size: .88rem; font-weight: 700; color: #0f172a; }
.ap-email { font-size: .73rem; color: #94a3b8; margin-top: 2px; }

/* Form grid */
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; }
@media (max-width: 560px) { .form-row { grid-template-columns: 1fr; } }
.form-row.single { grid-template-columns: 1fr; }

/* Fields */
.ff label {
    display: block;
    font-size: .72rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .5px; color: #64748b;
    margin-bottom: 6px;
}
.ff .input-wrap { position: relative; }
.ff .input-wrap i {
    position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
    font-size: 15px; color: #94a3b8; pointer-events: none;
}
.ff input, .ff select {
    width: 100%; padding: 10px 13px 10px 36px;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    background: #f8fafc; color: #0f172a; font-size: .88rem;
    outline: none; transition: all .15s;
    font-family: inherit;
}
.ff input:focus, .ff select:focus {
    border-color: #6366f1; background: #fff;
    box-shadow: 0 0 0 3px rgba(99,102,241,.1);
}
.ff .hint {
    font-size: .72rem; color: #94a3b8;
    margin-top: 4px; display: flex; align-items: center; gap: 4px;
}

/* Password section */
.pwd-section {
    background: #fafafa;
    border: 1.5px dashed #e2e8f0;
    border-radius: 12px;
    padding: 1.2rem 1.4rem;
}
.pwd-section-head {
    display: flex; align-items: center; gap: 8px;
    font-size: .78rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .5px; color: #475569;
    margin-bottom: 1rem;
}
.pwd-section-head i { font-size: 15px; color: #64748b; }

/* Actions */
.form-actions {
    display: flex; align-items: center; gap: .7rem;
    padding: 1.2rem 1.6rem;
    border-top: 1px solid #f1f5f9;
    flex-wrap: wrap;
}
</style>
@endpush

@section('content')

<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:.8rem;margin-bottom:1.5rem">
    <div>
        <div class="page-title">Editar Perfil</div>
        <div class="page-sub">Actualiza tu información personal</div>
    </div>
    <a href="{{ route('perfil') }}" class="btn btn-secondary btn-sm">
        <i class="ti ti-arrow-left" style="font-size:13px"></i> Ver perfil
    </a>
</div>

@if($errors->any())
<div class="alert alert-error" style="margin-bottom:1.2rem">
    <i class="ti ti-alert-circle"></i>
    <div>
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
</div>
@endif

<div class="edit-layout">

    {{-- ── COLUMNA IZQUIERDA ── --}}
    <div>

        {{-- Preview card --}}
        <div class="preview-card">
            <div class="pc-accent"></div>
            <div class="pc-bg-letter">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>

            <div class="pc-inner">
                <div class="pc-eyebrow">Vista previa del perfil</div>
                
                @if(Auth::user()->avatar)
                    <div class="pc-avatar"><img class="pc-avatar_feature" src="{{ asset('storage/' . Auth::user()->avatar) }}" /></div>
                @else
                <div class="pc-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                @endif
                <div class="pc-name">{{ auth()->user()->name }}</div>
                <div class="pc-email">{{ auth()->user()->email }}</div>
                <span class="pc-role" style="{{ auth()->user()->isAdmin() ? 'background:rgba(109,40,217,.3);color:#c4b5fd' : 'background:rgba(22,101,52,.3);color:#86efac' }}">
                    <i class="ti {{ auth()->user()->isAdmin() ? 'ti-shield-check' : 'ti-user' }}" style="font-size:10px"></i>
                    {{ ucfirst(auth()->user()->role) }}
                </span>
            </div>

            <a href="{{ route('perfil') }}" class="pc-view-btn">
                <i class="ti ti-eye" style="font-size:13px"></i> Ver perfil completo
            </a>
        </div>

        {{-- Tips --}}
        <div class="tips-card">
            <div class="tc-head">
                <span class="tc-title">Recomendaciones</span>
                <i class="ti ti-info-circle" style="font-size:16px;color:#e2e8f0"></i>
            </div>
            <div class="tips-list">
                <div class="tip-item">
                    <div class="tip-dot"><i class="ti ti-check"></i></div>
                    Usa un correo al que tengas acceso siempre.
                </div>
                <div class="tip-item">
                    <div class="tip-dot"><i class="ti ti-check"></i></div>
                    Tu contraseña debe tener mínimo 8 caracteres.
                </div>
                <div class="tip-item">
                    <div class="tip-dot"><i class="ti ti-check"></i></div>
                    Deja el campo de contraseña vacío si no deseas cambiarla.
                </div>
                <div class="tip-item">
                    <div class="tip-dot"><i class="ti ti-check"></i></div>
                    Los cambios se aplican de inmediato al guardar.
                </div>
            </div>
        </div>

    </div>

    {{-- ── COLUMNA DERECHA ── --}}
    <div>
        <form action="{{ route('perfil.update') }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Información personal --}}
            <div class="edit-card">
                <div class="ec-header">
                    <div>
                        <div class="ec-title">Información personal</div>
                        <div class="ec-sub">Nombre y correo visibles en el sistema</div>
                    </div>
                    <i class="ti ti-user-circle" style="font-size:22px;color:#e2e8f0"></i>
                </div>
                <div class="ec-body">
                    <div class="avatar-preview">
                        @if(Auth::user()->avatar)
                            <div class="ap-circle"><img class="ap-circle" src="{{ asset('storage/' . Auth::user()->avatar) }}" /></div>
                        @else
                            <div class="ap-circle">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                        @endif
                        <div>
                            <div class="ap-name">{{ auth()->user()->name }}</div>
                            <div class="ap-email">{{ auth()->user()->email }}</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="ff">
                            <label>Nombre completo *</label>
                            <div class="input-wrap">
                                <i class="ti ti-user"></i>
                                <input type="text" name="name"
                                       value="{{ old('name', $user->name) }}"
                                       placeholder="Tu nombre completo"
                                       required autocomplete="name">
                            </div>
                        </div>
                        <div class="ff">
                            <label>Correo electrónico *</label>
                            <div class="input-wrap">
                                <i class="ti ti-mail"></i>
                                <input type="email" name="email"
                                       value="{{ old('email', $user->email) }}"
                                       placeholder="correo@ejemplo.com"
                                       required autocomplete="email">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cambiar contraseña --}}
            <div class="edit-card" id="password">
                <div class="ec-header">
                    <div>
                        <div class="ec-title">Cambiar contraseña</div>
                        <div class="ec-sub">Déjalo en blanco si no quieres cambiarla</div>
                    </div>
                    <i class="ti ti-lock" style="font-size:22px;color:#e2e8f0"></i>
                </div>
                <div class="ec-body">
                    <div class="pwd-section">
                        <div class="pwd-section-head">
                            <i class="ti ti-lock-password"></i> Nueva contraseña
                        </div>
                        <div class="form-row">
                            <div class="ff">
                                <label>Nueva contraseña</label>
                                <div class="input-wrap">
                                    <i class="ti ti-key"></i>
                                    <input type="password" name="password"
                                           placeholder="Mínimo 8 caracteres"
                                           autocomplete="new-password">
                                </div>
                                <div class="hint">
                                    <i class="ti ti-info-circle" style="font-size:12px"></i>
                                    Mínimo 8 caracteres
                                </div>
                            </div>
                            <div class="ff">
                                <label>Confirmar contraseña</label>
                                <div class="input-wrap">
                                    <i class="ti ti-key"></i>
                                    <input type="password" name="password_confirmation"
                                           placeholder="Repite la contraseña"
                                           autocomplete="new-password">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="edit-card">
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy" style="font-size:15px"></i> Guardar cambios
                    </button>
                    <a href="{{ route('perfil') }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </div>

        </form>
    </div>

</div>

@endsection
