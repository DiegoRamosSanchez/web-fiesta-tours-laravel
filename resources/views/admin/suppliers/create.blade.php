@extends('layouts.app')
@section('title', 'Nuevo Proveedor')
@section('content')

<div class="page-header">
    <div class="page-title">Nuevo Proveedor</div>
    <div class="page-sub">Registra un nuevo proveedor en el sistema</div>
</div>

<div style="max-width:600px">
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
                <div class="card-title">Datos del proveedor</div>
                <div class="card-sub">Completa la información requerida</div>
            </div>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary btn-sm">
                <i class="ti ti-arrow-left" style="font-size:13px"></i> Volver
            </a>
        </div>

        <form action="{{ route('admin.suppliers.store') }}" method="POST">
            @csrf
            <div class="form-field" style="margin-bottom:1.1rem">
                <label>Nombre del proveedor *</label>
                <input type="text" name="supplier_name"
                       value="{{ old('supplier_name') }}"
                       placeholder="Nombre del proveedor"
                       maxlength="100" required autofocus>
            </div>

            <div class="form-grid" style="margin-bottom:1.4rem">
                <div class="form-field">
                    <label>Destino asociado</label>
                    <select name="id_destinations">
                        <option value="">— Sin destino —</option>
                        @foreach($destinations as $d)
                            <option value="{{ $d->id_destinations }}"
                                {{ old('id_destinations') == $d->id_destinations ? 'selected' : '' }}>
                                {{ $d->destination_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field">
                    <label>Categoría</label>
                    <select name="id_categories_suppliers">
                        <option value="">— Sin categoría —</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id_categories_suppliers }}"
                                {{ old('id_categories_suppliers') == $c->id_categories_suppliers ? 'selected' : '' }}>
                                {{ $c->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display:flex;gap:.8rem;padding-top:1rem;border-top:1px solid #f1f5f9">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-plus" style="font-size:14px"></i> Crear proveedor
                </button>
                <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
