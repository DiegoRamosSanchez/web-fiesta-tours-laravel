@extends('layouts.app')
@section('title', 'Editar Perfil')
@section('content')

<div class="page-header">
    <div class="page-title">Editar Perfil</div>
    <div class="page-sub">Actualiza tu información personal</div>
</div>

<div style="max-width:600px">
    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:1.2rem">
            <i class="ti ti-alert-circle"></i>
            <ul style="margin-left:.5rem;list-style:none">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Información personal</div>
                <div class="card-sub">Los campos marcados con * son obligatorios</div>
            </div>
        </div>

        <form action="{{ route('perfil.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid" style="margin-bottom:1.1rem">
                <div class="form-field">
                    <label>Nombre completo *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           placeholder="Tu nombre completo" required>
                </div>
                <div class="form-field">
                    <label>Correo electrónico *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           placeholder="correo@ejemplo.com" required>
                </div>
            </div>

            <div style="background:#f8fafc;border-radius:10px;padding:1.1rem;margin-bottom:1.4rem;border:1px solid #e2e8f0">
                <div style="font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.5px;margin-bottom:.9rem;display:flex;align-items:center;gap:6px">
                    <i class="ti ti-lock" style="font-size:14px"></i> Cambiar contraseña
                </div>
                <div class="form-grid">
                    <div class="form-field">
                        <label>Nueva contraseña</label>
                        <input type="password" name="password" placeholder="Mínimo 8 caracteres">
                        <div class="hint">Déjalo vacío si no quieres cambiarla</div>
                    </div>
                    <div class="form-field">
                        <label>Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" placeholder="Repite la contraseña">
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:.8rem">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy" style="font-size:15px"></i> Guardar cambios
                </button>
                <a href="{{ route('perfil') }}" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
