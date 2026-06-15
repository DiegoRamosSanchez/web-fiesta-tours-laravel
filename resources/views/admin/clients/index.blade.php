@extends('layouts.app')
@section('title', 'Clientes')
@section('content')

<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.6rem">
    <div>
        <div class="page-title">Clientes</div>
        <div class="page-sub">Gestiona todos los clientes registrados</div>
    </div>
    <button onclick="document.getElementById('modal-crear').classList.remove('hidden')" class="btn btn-primary">
        <i class="ti ti-plus" style="font-size:15px"></i> Nuevo Cliente
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:1rem">
        <i class="ti ti-circle-check"></i> {{ session('success') }}
    </div>
@endif

@if($clients->isEmpty())
    <div style="text-align:center;padding:4rem;background:#fff;border-radius:14px;border:1px solid #e2e8f0">
        <i class="ti ti-building-off" style="font-size:48px;color:#cbd5e1;display:block;margin-bottom:1rem"></i>
        <p style="font-size:15px;font-weight:600;color:#0f172a;margin-bottom:.4rem">No hay clientes aún</p>
        <button onclick="document.getElementById('modal-crear').classList.remove('hidden')" class="btn btn-primary btn-sm">
            <i class="ti ti-plus"></i> Crear primer cliente
        </button>
    </div>
@else
    {{-- Buscador --}}
    <div style="margin-bottom:1rem">
        <input type="text" id="buscador" placeholder="Buscar por nombre o contacto..."
               style="width:280px;padding:.55rem .9rem;border:1px solid #e2e8f0;border-radius:9px;font-size:13px;outline:none">
    </div>

    <div class="table-wrap">
        <table id="tabla-clientes">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>AGENCIA</th>
                    <th>CONTACTO PRINCIPAL</th>
                    <th>EMAIL</th>
                    <th>TELÉFONO</th>
                    <th>ESTADO</th>
                    <th style="text-align:center">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $c)
                    @php $principal = $c->contacts->first(); @endphp
                    <tr>
                        <td style="color:#94a3b8;font-size:12px">{{ $c->id_client }}</td>
                        <td>
                            <div style="font-weight:600;color:#0f172a;font-size:13px">{{ $c->name_client }}</div>
                            <div style="font-size:11px;color:#94a3b8">{{ $c->contacts_count }} contacto(s)</div>
                        </td>
                        <td style="font-size:13px;color:#374151">
                            {{ $principal ? trim($principal->name.' '.$principal->last_names) : '---' }}
                        </td>
                        <td style="font-size:12px;color:#64748b">{{ $principal->email ?? '---' }}</td>
                        <td style="font-size:12px;color:#64748b">{{ $principal->first_phone ?? '---' }}</td>
                        <td>
                            <span style="background:#dcfce7;color:#166534;font-size:11px;font-weight:600;
                                         padding:2px 9px;border-radius:20px;border:1px solid #bbf7d0">
                                ACTIVO
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;justify-content:center;gap:6px">
                                {{-- Ver detalle (opcional: puedes enlazarlo a una vista show) --}}
                                <button title="Ver" style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:16px">
                                    <i class="ti ti-eye"></i>
                                </button>
                                <a href="{{ route('admin.clients.edit', $c->id_client) }}"
                                   title="Editar" style="color:#3b82f6;font-size:16px">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <form action="{{ route('admin.clients.destroy', $c->id_client) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar {{ addslashes($c->name_client) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background:none;border:none;cursor:pointer;color:#ef4444;font-size:16px">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

{{-- ══════════ MODAL CREAR CLIENTE ══════════ --}}
<div id="modal-crear" class="hidden"
     style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:999;display:flex;align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:560px;max-height:90vh;overflow-y:auto;padding:2rem;position:relative">

        <button onclick="document.getElementById('modal-crear').classList.add('hidden')"
                style="position:absolute;top:1rem;right:1rem;background:none;border:none;font-size:22px;cursor:pointer;color:#94a3b8">
            <i class="ti ti-x"></i>
        </button>

        <h2 style="font-size:17px;font-weight:700;color:#0f172a;margin-bottom:1.4rem">Registrar Nuevo Cliente</h2>

        <form action="{{ route('admin.clients.store') }}" method="POST" id="form-crear-cliente">
            @csrf

            <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:.6rem">
                Datos de la Empresa
            </p>
            <div class="form-field" style="margin-bottom:1.2rem">
                <label style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px">
                    NOMBRE DE LA EMPRESA / CLIENTE CORPORATIVO
                </label>
                <input type="text" name="name_client" value="{{ old('name_client') }}"
                       placeholder="Ej: Fiesta Tours Perú S.A.C."
                       style="width:100%;padding:.6rem .8rem;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;margin-top:.4rem;box-sizing:border-box"
                       required>
            </div>

            <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:.6rem">
                Contactos
            </p>

            <div id="contactos-wrapper"></div>

            <button type="button" onclick="agregarContacto()"
                    style="background:#10b981;color:#fff;border:none;padding:.5rem 1rem;border-radius:8px;
                           font-size:13px;font-weight:600;cursor:pointer;margin-bottom:1.2rem">
                <i class="ti ti-plus"></i> Añadir contacto
            </button>

            <div style="display:flex;justify-content:flex-end;gap:.8rem;padding-top:1rem;border-top:1px solid #f1f5f9">
                <button type="button" onclick="document.getElementById('modal-crear').classList.add('hidden')"
                        class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Cliente</button>
            </div>
        </form>
    </div>
</div>

<script>
let contactoIdx = 0;

function agregarContacto() {
    const i = contactoIdx++;
    const label = i === 0 ? 'Contacto #1 — Representante Principal' : `Contacto #${i+1}`;
    const wrapper = document.getElementById('contactos-wrapper');

    const div = document.createElement('div');
    div.id = `contacto-${i}`;
    div.style.cssText = 'border:1px solid #e2e8f0;border-radius:10px;padding:1rem;margin-bottom:.8rem;position:relative';
    div.innerHTML = `
        <div style="font-size:12px;font-weight:700;color:#374151;margin-bottom:.8rem">${label}</div>
        ${i > 0 ? `<button type="button" onclick="document.getElementById('contacto-${i}').remove()"
            style="position:absolute;top:.7rem;right:.7rem;background:none;border:none;cursor:pointer;color:#ef4444;font-size:16px">
            <i class="ti ti-x"></i></button>` : ''}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.6rem">
            <div>
                <label style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase">NOMBRE *</label>
                <input type="text" name="contacts[${i}][name]" placeholder="Nombre" required
                       style="width:100%;padding:.5rem .7rem;border:1px solid #e2e8f0;border-radius:7px;font-size:13px;margin-top:.3rem;box-sizing:border-box">
            </div>
            <div>
                <label style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase">APELLIDOS</label>
                <input type="text" name="contacts[${i}][last_names]" placeholder="Apellidos"
                       style="width:100%;padding:.5rem .7rem;border:1px solid #e2e8f0;border-radius:7px;font-size:13px;margin-top:.3rem;box-sizing:border-box">
            </div>
            <div style="grid-column:1/3">
                <label style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase">CORREO</label>
                <input type="email" name="contacts[${i}][email]" placeholder="ejemplo@correo.com"
                       style="width:100%;padding:.5rem .7rem;border:1px solid #e2e8f0;border-radius:7px;font-size:13px;margin-top:.3rem;box-sizing:border-box">
            </div>
            <div>
                <label style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase">TELÉFONO 1</label>
                <input type="text" name="contacts[${i}][first_phone]" placeholder="Principal"
                       style="width:100%;padding:.5rem .7rem;border:1px solid #e2e8f0;border-radius:7px;font-size:13px;margin-top:.3rem;box-sizing:border-box">
            </div>
            <div>
                <label style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase">TELÉFONO 2</label>
                <input type="text" name="contacts[${i}][second_phone]" placeholder="Opcional"
                       style="width:100%;padding:.5rem .7rem;border:1px solid #e2e8f0;border-radius:7px;font-size:13px;margin-top:.3rem;box-sizing:border-box">
            </div>
        </div>`;
    wrapper.appendChild(div);
}

// Abrir modal si hubo errores de validación
@if($errors->any())
    document.getElementById('modal-crear').classList.remove('hidden');
    agregarContacto();
@else
    agregarContacto(); // primer contacto por defecto al abrir
@endif

// Buscador
document.getElementById('buscador')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#tabla-clientes tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>

<style>
.hidden { display: none !important; }
</style>

@endsection
