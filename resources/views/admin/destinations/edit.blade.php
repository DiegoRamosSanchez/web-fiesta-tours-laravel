@extends('layouts.app')
@section('title', 'Editar Destino')
@section('content')

<div class="page-header">
    <div class="page-title">Editar Destino</div>
    <div class="page-sub">Modifica los datos del destino</div>
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
                <div class="card-title">{{ $destination->destination_name }}</div>
                <div class="card-sub">ID #{{ $destination->id_destinations }}</div>
            </div>
            <a href="{{ route('admin.destinations.index') }}" class="btn btn-secondary btn-sm">
                <i class="ti ti-arrow-left" style="font-size:13px"></i> Volver
            </a>
        </div>
        <form action="{{ route('admin.destinations.update', $destination->id_destinations) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-field" style="margin-bottom:1.4rem">
                <label>Nombre del destino *</label>
                <input type="text" name="destination_name"
                       value="{{ old('destination_name', $destination->destination_name) }}"
                       maxlength="100" required autofocus>
            </div>
            <div style="display:flex;gap:.8rem;padding-top:1rem;border-top:1px solid #f1f5f9">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy" style="font-size:14px"></i> Guardar cambios
                </button>
                <a href="{{ route('admin.destinations.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
