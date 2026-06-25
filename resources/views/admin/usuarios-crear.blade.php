@extends('layouts.app')
@section('title', 'Crear Usuario')
@section('content')

<div class="page-header">
    <div class="page-title">Crear Usuario</div>
    <div class="page-sub">Agrega un nuevo usuario al sistema</div>
</div>

<div style="max-width:600px">
    @if($errors->any())
        <div class="alert alert-error">
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
                <div class="card-title">Nuevo usuario</div>
                <div class="card-sub">Completa los datos del usuario a registrar</div>
            </div>
            <a href="{{ route('admin.usuarios') }}" class="btn btn-secondary btn-sm">
                <i class="ti ti-arrow-left" style="font-size:14px"></i> Volver
            </a>
        </div>

        <form action="{{ route('admin.usuarios.store') }}" enctype="multipart/form-data" method="POST">
            @csrf
            <div class="form-grid" style="margin-bottom:1.1rem">


                <div class="form-field">
                    <label for="avatar">Sube tu foto de perfil: *</label>
                    <input type="file" name="avatar" id="avatar" accept="image/*">
                    @error('avatar')
                        <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-field">
                    <label>Nombre completo *</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="Nombre del usuario" required>
                </div>
                
                <div class="form-field">
                    <label>Correo electrónico *</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="correo@ejemplo.com" required>
                </div>
                <div class="form-field">
                    <label>Contraseña *</label>
                    <input type="password" name="password" placeholder="Mínimo 8 caracteres" required>
                </div>
                <div class="form-field">
                    <label>Confirmar contraseña *</label>
                    <input type="password" name="password_confirmation" placeholder="Repite la contraseña" required>
                </div>
                <div class="form-field">
                    <label>Rol *</label>
                    <select name="role">
                        <option value="usuario" {{ old('role','usuario') == 'usuario' ? 'selected' : '' }}>Usuario</option>
                        <option value="admin"   {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                    </select>
                </div>
            </div>

            <div style="display:flex;gap:.8rem;padding-top:1rem;border-top:1px solid #f1f5f9">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-user-plus" style="font-size:15px"></i> Crear usuario
                </button>
                <a href="{{ route('admin.usuarios') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

@endsection
