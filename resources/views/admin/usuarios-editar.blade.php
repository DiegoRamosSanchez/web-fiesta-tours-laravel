@extends('layouts.app')
@section('title', 'Editar Usuario')
@section('content')

<div class="page-header">
    <div class="page-title">Editar Usuario</div>
    <div class="page-sub">Actualiza los datos de {{ $user->name }}</div>
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
                <div class="card-title">Editar usuario</div>
                <div class="card-sub">Modifica los datos del usuario seleccionado</div>
            </div>
            <a href="{{ route('admin.usuarios') }}" class="btn btn-secondary btn-sm">
                <i class="ti ti-arrow-left" style="font-size:14px"></i> Volver
            </a>
        </div>

        <form action="{{ route('admin.usuarios.update', $user) }}" enctype="multipart/form-data" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid" style="margin-bottom:1.1rem">

                <div class="form-field">
                    <label for="avatar">Foto de perfil</label>

                    <div style="display:flex;align-items:center;gap:14px;margin-bottom:.6rem">
                        <div id="avatarPreviewWrap" style="position:relative">
                            <img id="avatarPreview"
                                 src="{{ $user->avatar ? asset('storage/' . $user->avatar) : '' }}"
                                 class="avatar-sm"
                                 style="width:56px;height:56px;border-radius:50%;object-fit:cover;border:1px solid #e2e8f0;cursor:{{ $user->avatar ? 'zoom-in' : 'default' }};{{ $user->avatar ? '' : 'display:none' }}"
                                 title="Clic para ver en grande">
                            <div id="avatarPlaceholder"
                                 style="width:56px;height:56px;border-radius:50%;background:#f1f5f9;color:#94a3b8;display:{{ $user->avatar ? 'none' : 'flex' }};align-items:center;justify-content:center;font-size:11px;text-align:center;border:1px dashed #cbd5e1">
                                Sin foto
                            </div>
                        </div>
                        <div>
                            <label for="avatar" class="btn btn-secondary btn-sm" style="cursor:pointer;display:inline-flex;align-items:center;gap:6px">
                                <i class="ti ti-upload" style="font-size:13px"></i>
                                <span id="avatarBtnText">Cambiar foto</span>
                            </label>
                            <input type="file" name="avatar" id="avatar" accept="image/*" style="display:none">
                            <div id="avatarFileName" style="font-size:11px;color:#94a3b8;margin-top:4px">
                                Déjalo vacío para no cambiar la foto
                            </div>
                        </div>
                    </div>

                    @error('avatar')
                        <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field">
                    <label>Nombre completo *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           placeholder="Nombre del usuario" required>
                </div>

                <div class="form-field">
                    <label>Correo electrónico *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           placeholder="correo@ejemplo.com" required>
                </div>

                <div class="form-field">
                    <label>Nueva contraseña</label>
                    <input type="password" name="password" placeholder="Déjalo vacío para no cambiarla">
                </div>

                <div class="form-field">
                    <label>Confirmar nueva contraseña</label>
                    <input type="password" name="password_confirmation" placeholder="Repite la nueva contraseña">
                </div>

                <div class="form-field">
                    <label>Rol *</label>
                    <select name="role">
                        <option value="usuario" {{ old('role', $user->role) == 'usuario' ? 'selected' : '' }}>Usuario</option>
                        <option value="admin"   {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrador</option>
                    </select>
                </div>
            </div>

            <div style="display:flex;gap:.8rem;padding-top:1rem;border-top:1px solid #f1f5f9">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy" style="font-size:15px"></i> Guardar cambios
                </button>
                <a href="{{ route('admin.usuarios') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<!-- ── MODAL / LIGHTBOX para ver la foto en grande ── -->
<div id="avatarLightbox" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.75);z-index:1000;align-items:center;justify-content:center;cursor:zoom-out">
    <button type="button" id="avatarLightboxClose"
            style="position:absolute;top:24px;right:28px;background:rgba(255,255,255,.15);border:none;color:#fff;width:38px;height:38px;border-radius:50%;font-size:20px;cursor:pointer;display:flex;align-items:center;justify-content:center">
        &times;
    </button>
    <img id="avatarLightboxImg" src="" alt="Foto de perfil"
         style="max-width:90vw;max-height:85vh;border-radius:12px;box-shadow:0 20px 60px rgba(0,0,0,.4)">
</div>

<script>
(function () {
    const avatarInput   = document.getElementById('avatar');
    const preview        = document.getElementById('avatarPreview');
    const placeholder     = document.getElementById('avatarPlaceholder');
    const fileNameLabel  = document.getElementById('avatarFileName');
    const btnText        = document.getElementById('avatarBtnText');

    const lightbox       = document.getElementById('avatarLightbox');
    const lightboxImg    = document.getElementById('avatarLightboxImg');
    const lightboxClose  = document.getElementById('avatarLightboxClose');

    function openLightbox(src) {
        if (!src) return;
        lightboxImg.src = src;
        lightbox.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        lightbox.style.display = 'none';
        document.body.style.overflow = '';
    }

    // Clic en la miniatura -> abrir en grande
    preview.addEventListener('click', () => openLightbox(preview.src));

    // Cerrar con la X, clic fuera de la imagen, o tecla Escape
    lightboxClose.addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', function (e) {
        if (e.target === lightbox) closeLightbox();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeLightbox();
    });

    // Cambiar imagen (preview en vivo al subir un archivo)
    avatarInput.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;

        if (file.size > 10 * 1024 * 1024) {
            alert('La imagen supera el tamaño máximo de 10MB.');
            e.target.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function (ev) {
            preview.src = ev.target.result;
            preview.style.display = 'block';
            preview.style.cursor = 'zoom-in';
            placeholder.style.display = 'none';
        };
        reader.readAsDataURL(file);

        fileNameLabel.textContent = file.name;
        btnText.textContent = 'Cambiar foto';
    });
})();
</script>

@endsection
