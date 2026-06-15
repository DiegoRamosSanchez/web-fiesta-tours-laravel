@extends('layouts.app')
@section('title', 'Destinos')
@section('content')

<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.6rem">
    <div>
        <div class="page-title">Destinos</div>
        <div class="page-sub">Gestiona los destinos disponibles</div>
    </div>
    <a href="{{ route('admin.destinations.create') }}" class="btn btn-primary">
        <i class="ti ti-plus" style="font-size:15px"></i> Nuevo destino
    </a>
</div>

@if($destinations->isEmpty())
    <div style="text-align:center;padding:4rem;background:#fff;border-radius:14px;border:1px solid #e2e8f0">
        <i class="ti ti-map-off" style="font-size:48px;color:#cbd5e1;display:block;margin-bottom:1rem"></i>
        <p style="font-size:15px;font-weight:600;color:#0f172a;margin-bottom:.4rem">No hay destinos aún</p>
        <p style="font-size:13px;color:#94a3b8;margin-bottom:1.2rem">Comienza agregando tu primer destino</p>
        <a href="{{ route('admin.destinations.create') }}" class="btn btn-primary btn-sm">
            <i class="ti ti-plus"></i> Crear destino
        </a>
    </div>
@else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del destino</th>
                    <th>Registro</th>
                    <th style="text-align:center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($destinations as $d)
                <tr>
                    <td style="color:#cbd5e1;font-size:12px;font-weight:600">#{{ $d->id_destinations }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="avatar-sm" style="background:#fef3c7;color:#92400e">
                                <i class="ti ti-map-pin" style="font-size:14px"></i>
                            </div>
                            <span style="font-weight:600">{{ $d->destination_name }}</span>
                        </div>
                    </td>
                    <td style="color:#94a3b8;font-size:12px">{{ $d->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div style="display:flex;justify-content:center;gap:6px">
                            <a href="{{ route('admin.destinations.edit', $d->id_destinations) }}"
                               class="btn btn-secondary btn-sm">
                                <i class="ti ti-edit" style="font-size:13px"></i> Editar
                            </a>
                            <form action="{{ route('admin.destinations.destroy', $d->id_destinations) }}"
                                  method="POST"
                                  onsubmit="return confirm('¿Eliminar destino {{ addslashes($d->destination_name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="ti ti-trash" style="font-size:13px"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="table-footer">Total: {{ $destinations->count() }} destino(s)</div>
    </div>
@endif
@endsection
