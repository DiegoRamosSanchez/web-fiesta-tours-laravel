@extends('layouts.app')
@section('title', 'Editar Cliente')
@section('content')

<div class="page-header">
    <div class="page-title">Editar Cliente</div>
    <div class="page-sub">Modifica los datos del cliente</div>
</div>

<div style="max-width:500px">
    @if($errors->any())
        <div class="alert alert-error">
            <i class="ti ti-alert-circle"></i>
            <ul style="list-style:none;margin-left:.5rem">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">{{ $client->name_client }}</div>
                <div class="card-sub">ID #{{ $client->id_client }}</div>
            </div>
            <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary btn-sm">
                <i class="ti ti-arrow-left" style="font-size:13px"></i> Volver
            </a>
        </div>

        <form action="{{ route('admin.clients.update', $client->id_client) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-field" style="margin-bottom:1.4rem">
                <label>Nombre del cliente *</label>
                <input type="text" name="name_client"
                       value="{{ old('name_client', $client->name_client) }}"
                       maxlength="20" required autofocus>
                <div class="hint">Máximo 20 caracteres</div>
            </div>

            <div style="display:flex;gap:.8rem;padding-top:1rem;border-top:1px solid #f1f5f9">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy" style="font-size:14px"></i> Guardar cambios
                </button>
                <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
