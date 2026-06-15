@extends('layouts.app')
@section('title', 'Editar Perfil')

@push('styles')
<style>
.edit-wrap { max-width: 660px; }

/* Section label */
.section-pill {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .6px;
    color: #64748b;
    background: #f1f5f9; border-radius: 999px;
    padding: 4px 12px;
    margin-bottom: 1rem;
}

/* Enhanced card */
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

/* Avatar preview */
.avatar-preview {
    display: flex; align-items: center; gap: 1rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem 1.2rem;
    margin-bottom: 1.4rem;
}
.ap-circle {
    width: 52px; height: 52px; border-radius: 50%;
    background: linear-gradient(135deg,#6366f1,#8b5cf6);
    display: flex; align-items: center; justify-content: center;
    font-size: 17px; color: #fff; font-weight: 700; flex-shrink: 0;
}
.ap-name { font-size: .9rem; font-weight: 700; color: #0f172a; }
.ap-meta { font-size: .75rem; color: #94a3b8; margin-top: 1px; }

/* Grid */
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; }
@media (max-width: 560px) { .form-row { grid-template-columns: 1fr; } }
.form-row.single { grid-template-columns: 1fr; }

/* Enhanced inputs */
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
.ff .hint { font-size: .72rem; color: #94a3b8; margin-top: 4px; display: flex; align-items: center; gap: 4px; }

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

/* Actions bar */
.form-actions {
    display: flex; align-items: center; gap: .7rem;
    padding-top: 1.2rem;
    border-top: 1px solid #f1f5f9;
    margin-top: .4rem;
    flex-wrap: wrap;
}
</style>
@endpush

@section('content')

<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:.8rem">
    <div>
        <div class="page-title">Editar Perfil</div>
        <div class="page-sub">Actualiza tu información personal</div>
    </div>
    <a href="{{ route('perfil') }}" class="btn btn-secondary btn-sm">
        <i class="ti ti-arrow-left" style="font-size:13px"></i> Ver perfil
    </a>
</div>

<div class="edit-wrap">

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

    <form action="{{ route('perfil.update') }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Personal info --}}
        <div class="edit-card">
            <div class="ec-header">
                <div>
                    <div class="ec-title">Información personal</div>
                    <div class="ec-sub">Nombre y correo visibles en el sistema</div>
                </div>
                <i class="ti ti-user-circle" style="font-size:22px;color:#e2e8f0"></i>
            </div>
            <div class="ec-body">
                {{-- Avatar preview --}}
                <div class="avatar-preview">
                    <div class="ap-circle">{{ strtoupper(substr(auth()->user()->name,0,2)) }}</div>
                    <div>
                        <div class="ap-name">{{ auth()->user()->name }}</div>
                        <div class="ap-meta">{{ auth()->user()->email }}</div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="ff">
                        <label>Nombre completo *</label>
                        <div class="input-wrap">
                            <i class="ti ti-user"></i>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                   placeholder="Tu nombre completo" required autocomplete="name">
                        </div>
                    </div>
                    <div class="ff">
                        <label>Correo electrónico *</label>
                        <div class="input-wrap">
                            <i class="ti ti-mail"></i>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                   placeholder="correo@ejemplo.com" required autocomplete="email">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Password --}}
        <div class="edit-card">
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
                                <input type="password" name="password" placeholder="Mínimo 8 caracteres" autocomplete="new-password">
                            </div>
                            <div class="hint"><i class="ti ti-info-circle" style="font-size:12px"></i> Mínimo 8 caracteres</div>
                        </div>
                        <div class="ff">
                            <label>Confirmar contraseña</label>
                            <div class="input-wrap">
                                <i class="ti ti-key"></i>
                                <input type="password" name="password_confirmation" placeholder="Repite la contraseña" autocomplete="new-password">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="edit-card">
            <div class="ec-body" style="padding:1.2rem 1.6rem">
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy" style="font-size:15px"></i> Guardar cambios
                    </button>
                    <a href="{{ route('perfil') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </div>
        </div>

    </form>
</div>

@endsection
