@extends('layouts.app')
@section('title', 'Editar Cliente')

@push('styles')
<link href="{{ asset('css/clients-edit.css') }}" rel="stylesheet">
@endpush

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Editar Cliente</div>
        <div class="page-sub">Modifica los datos y contactos del cliente</div>
    </div>
    <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary btn-sm">
        <i class="ti ti-arrow-left" style="font-size:13px"></i> Volver
    </a>
</div>

@if($errors->any())
    <div class="alert alert-error">
        <i class="ti ti-alert-circle"></i>
        <ul>
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.clients.update', $client->id_client) }}"
      method="POST" id="edit-form">
    @csrf @method('PUT')

    {{-- Campos ocultos para contactos a eliminar --}}
    <div id="delete-inputs"></div>

    <div class="edit-client-layout">

        {{-- ══════════ IZQUIERDA: DATOS DEL CLIENTE ══════════ --}}
        <div class="edit-client-left">

            {{-- ── DATOS DE LA EMPRESA ── --}}
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon">
                        <i class="ti ti-building"></i>
                    </div>
                    <div>
                        <p class="info-card-title">Datos de la empresa</p>
                        <p class="info-card-sub">ID #{{ $client->id_client }}</p>
                    </div>
                </div>

                <div class="field-group">
                    <label>Nombre comercial <span class="req">*</span></label>
                    <input type="text" name="name_client"
                        value="{{ old('name_client', $client->name_client) }}"
                        maxlength="120" required autofocus
                        placeholder="Ej: Fiesta Tours Perú S.A.C.">
                </div>

                <div class="field-group">
                    <label>Razón Social</label>
                    <input type="text" name="business_name"
                        value="{{ old('business_name', $client->business_name) }}"
                        maxlength="150" placeholder="Ej: Fiesta Tours Perú S.A.C.">
                </div>

                <div class="field-grid-2">
                    <div class="field-group">
                        <label>Código tributario (RUC)</label>
                        <input type="text" name="tax_code"
                            value="{{ old('tax_code', $client->tax_code) }}"
                            maxlength="20" placeholder="Ej: 20123456789">
                    </div>
                    <div class="field-group">
                        <label>Teléfono general</label>
                        <input type="text" name="general_phone"
                            value="{{ old('general_phone', $client->general_phone) }}"
                            maxlength="20" placeholder="Ej: 01-234567">
                    </div>
                </div>

                <div class="field-group">
                    <label>Email general</label>
                    <input type="email" name="general_email"
                        value="{{ old('general_email', $client->general_email) }}"
                        maxlength="120" placeholder="contacto@empresa.com">
                </div>
            </div>

            {{-- ── UBICACIÓN ── --}}
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon">
                        <i class="ti ti-map-pin"></i>
                    </div>
                    <div>
                        <p class="info-card-title">Ubicación</p>
                        <p class="info-card-sub">País, ciudad y dirección exacta del cliente</p>
                    </div>
                </div>

                <div class="field-grid-2">
                    <div class="field-group">
                        <label>País</label>
                        <div class="combo-wrap" id="combo-pais">
                            <input type="text" class="combo-input" id="edit-pais-input"
                                   placeholder="Cargando países..." autocomplete="off" disabled>
                            <button type="button" class="combo-clear" id="edit-pais-clear" tabindex="-1">
                                <i class="ti ti-x"></i>
                            </button>
                            <div class="combo-list" id="edit-pais-list"></div>
                        </div>
                        <input type="hidden" name="country_name" id="edit-country-name"
                            value="{{ old('country_name', $client->country_name) }}">
                        <input type="hidden" id="edit-country-code">
                    </div>

                    <div class="field-group">
                        <label>Ciudad</label>
                        <div class="combo-wrap" id="combo-ciudad">
                            <input type="text" class="combo-input" id="edit-ciudad-input"
                                   placeholder="Seleccione país primero" autocomplete="off" disabled>
                            <button type="button" class="combo-clear" id="edit-ciudad-clear" tabindex="-1">
                                <i class="ti ti-x"></i>
                            </button>
                            <div class="combo-list" id="edit-ciudad-list"></div>
                        </div>
                        <input type="hidden" name="city_name" id="edit-ciudad-name"
                            value="{{ old('city_name', $client->city_name) }}">
                    </div>
                </div>

                <div class="field-group">
                    <label>Dirección</label>
                    <input type="text" name="address"
                        value="{{ old('address', $client->address) }}"
                        maxlength="255"
                        placeholder="Ej: Avenida Suecia, calle 124, primera casa">
                </div>
            </div>

            <div class="btn-actions">
                <button type="submit" class="btn-primary">
                    <i class="ti ti-device-floppy" style="font-size:14px"></i> Guardar cambios
                </button>
                <a href="{{ route('admin.clients.index') }}" class="btn-secondary">Cancelar</a>
            </div>
        </div>

        {{-- ══════════ DERECHA: CONTACTOS EN TABLA ══════════ --}}
        <div class="edit-client-right">
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Contactos registrados</div>
                        <div class="card-sub" id="contacts-count">{{ $client->contacts->count() }} contacto(s)</div>
                    </div>
                    <button type="button" class="add-row-btn" onclick="addNewContact()">
                        <i class="ti ti-plus" style="font-size:14px"></i> Añadir contacto
                    </button>
                </div>

                <div class="contacts-table-wrap">
                    <table class="contacts-table">
                        <thead>
                            <tr>
                                <th title="Principal"><i class="ti ti-star"></i></th>
                                <th>Nombre *</th>
                                <th>Apellidos</th>
                                <th>Email</th>
                                <th>Cargo</th>
                                <th>Teléfono 1</th>
                                <th>Teléfono 2</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="contacts-tbody">
                            @foreach($client->contacts as $idx => $contact)
                            <tr class="contact-row" id="existing-row-{{ $contact->id_contacts }}">
                                <td class="col-principal">
                                    <input type="hidden" name="contacts[{{ $idx }}][id]" value="{{ $contact->id_contacts }}">
                                    <input type="checkbox" class="star-toggle principal-checkbox"
                                           name="contacts[{{ $idx }}][es_principal]" value="1"
                                           title="Marcar como principal"
                                           {{ old('contacts.'.$idx.'.es_principal', $contact->es_principal) ? 'checked' : '' }}>
                                </td>
                                <td class="col-name">
                                    <input type="text" name="contacts[{{ $idx }}][name]"
                                           value="{{ old('contacts.'.$idx.'.name', $contact->name) }}"
                                           placeholder="Nombre" required>
                                </td>
                                <td>
                                    <input type="text" name="contacts[{{ $idx }}][last_names]"
                                           value="{{ old('contacts.'.$idx.'.last_names', $contact->last_names) }}"
                                           placeholder="Apellidos">
                                </td>
                                <td>
                                    <input type="email" name="contacts[{{ $idx }}][email]"
                                           value="{{ old('contacts.'.$idx.'.email', $contact->email) }}"
                                           placeholder="correo@ejemplo.com">
                                </td>
                                <td>
                                    <input type="text" name="contacts[{{ $idx }}][qualification]"
                                           value="{{ old('contacts.'.$idx.'.qualification', $contact->qualification) }}"
                                           placeholder="Ej: Gerente">
                                </td>
                                <td>
                                    <input type="text" name="contacts[{{ $idx }}][first_phone]"
                                           value="{{ old('contacts.'.$idx.'.first_phone', $contact->first_phone) }}"
                                           placeholder="Principal">
                                </td>
                                <td>
                                    <input type="text" name="contacts[{{ $idx }}][second_phone]"
                                           value="{{ old('contacts.'.$idx.'.second_phone', $contact->second_phone) }}"
                                           placeholder="Opcional">
                                </td>
                                <td class="col-actions">
                                    <button type="button" class="row-del-btn"
                                            onclick="markDelete({{ $contact->id_contacts }}, this)"
                                            title="Eliminar contacto">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <p class="table-empty-note" id="no-contacts-note"
                       style="{{ $client->contacts->isEmpty() ? '' : 'display:none' }}">
                        Este cliente no tiene contactos aún. Usa "Añadir contacto" para crear uno.
                    </p>
                </div>
            </div>
        </div>

    </div>
</form>

@push('scripts')
<script>
    window.clientCountryName = @json(old('country_name', $client->country_name));
    window.clientCityName = @json(old('city_name', $client->city_name));
    window.geoPaisesUrl = "{{ url('api/geo/paises') }}";
    window.geoCiudadesUrl = "{{ url('api/geo/ciudades') }}";
</script>
<script src="{{ asset('js/clients-edit.js') }}"></script>
@endpush
@endsection
