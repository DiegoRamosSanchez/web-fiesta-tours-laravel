@extends('layouts.app')
@section('title', 'Nuevo Cliente')

@push('styles')
<link href="{{ asset('css/clients-create.css') }}" rel="stylesheet">
@endpush

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Nuevo Cliente</div>
        <div class="page-sub">Completa los datos de la empresa y agrega sus contactos</div>
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

@if(session('error'))
    <div class="alert alert-error">
        <i class="ti ti-alert-circle"></i>
        {{ session('error') }}
    </div>
@endif

<form action="{{ route('admin.clients.store') }}" method="POST" id="form-client">
    @csrf

    <div class="edit-client-layout">

        {{-- ══════════ COLUMNA IZQUIERDA ══════════ --}}
        <div class="edit-client-left">

            {{-- Datos de la empresa --}}
            <div class="card-modern">
                <div class="card-header-custom">
                    <div>
                        <div class="card-title-custom">
                            <i class="ti ti-building"></i> Datos de la empresa
                        </div>
                        <div class="card-sub-custom">Información general que identifica a la agencia o cliente</div>
                    </div>
                </div>

                <div class="field-group">
                    <label>Nombre comercial <span class="req">*</span></label>
                    <input type="text" name="name_client"
                           value="{{ old('name_client') }}"
                           placeholder="Ej: Fiesta Tours Perú S.A.C."
                           maxlength="100" required autofocus>
                </div>

                <div class="field-group">
                    <label>Razón Social</label>
                    <input type="text" name="business_name"
                           value="{{ old('business_name') }}"
                           placeholder="Razón social de la empresa"
                           maxlength="150">
                </div>

                <div class="field-group">
                    <label>Código tributario (RUC)</label>
                    <input type="text" name="tax_code"
                           value="{{ old('tax_code') }}"
                           placeholder="Ej: 20123456789"
                           maxlength="20">
                </div>

                <div class="field-group">
                    <label>Email general</label>
                    <input type="email" name="general_email"
                           value="{{ old('general_email') }}"
                           placeholder="contacto@empresa.com"
                           maxlength="120">
                </div>

                <div class="field-group">
                    <label>Teléfono general</label>
                    <input type="text" name="general_phone"
                           value="{{ old('general_phone') }}"
                           placeholder="Ej: 01-234567"
                           maxlength="20">
                </div>
            </div>

            {{-- Botones de acción --}}
            <div class="btn-actions">
                <button type="submit" class="btn-primary">
                    <i class="ti ti-plus"></i> Crear cliente
                </button>
                <a href="{{ route('admin.clients.index') }}" class="btn-secondary">
                    <i class="ti ti-x"></i> Cancelar
                </a>
            </div>
        </div>

        {{-- ══════════ COLUMNA DERECHA ══════════ --}}
        <div class="edit-client-right">

            {{-- Ubicación --}}
            <div class="card-modern">
                <div class="card-header-custom">
                    <div>
                        <div class="card-title-custom">
                            <i class="ti ti-map-pin"></i> Ubicación
                        </div>
                        <div class="card-sub-custom">Escribe para buscar el país y la ciudad</div>
                    </div>
                </div>

                <div class="field-grid-2">
                    <div class="field-group">
                        <label>País</label>
                        <div class="combo-wrap" id="combo-pais">
                            <input type="text" class="combo-input" id="create-pais-input"
                                   placeholder="Cargando países..." autocomplete="off" disabled>
                            <button type="button" class="combo-clear" id="create-pais-clear" tabindex="-1">
                                <i class="ti ti-x"></i>
                            </button>
                            <div class="combo-list" id="create-pais-list"></div>
                        </div>
                        <input type="hidden" name="country_name" id="create-country-name" value="{{ old('country_name') }}">
                        <input type="hidden" id="create-country-code">
                    </div>

                    <div class="field-group">
                        <label>Ciudad</label>
                        <div class="combo-wrap" id="combo-ciudad">
                            <input type="text" class="combo-input" id="create-ciudad-input"
                                   placeholder="Seleccione país primero" autocomplete="off" disabled>
                            <button type="button" class="combo-clear" id="create-ciudad-clear" tabindex="-1">
                                <i class="ti ti-x"></i>
                            </button>
                            <div class="combo-list" id="create-ciudad-list"></div>
                        </div>
                        <input type="hidden" name="city_name" id="create-ciudad-name" value="{{ old('city_name') }}">
                    </div>
                </div>

                <div class="field-group">
                    <label>Dirección</label>
                    <input type="text" name="address"
                           value="{{ old('address') }}"
                           placeholder="Ej: Avenida Suecia, calle 124, primera casa"
                           maxlength="200">
                </div>
            </div>

            {{-- Contactos - TABLA --}}
            <div class="contact-section">
                <div class="section-header">
                    <div>
                        <div class="section-title">
                            <i class="ti ti-users"></i> Contactos
                            <span class="contact-counter" id="contact-counter">0</span>
                        </div>
                        <div class="section-hint">El primer contacto se registrará como el representante principal</div>
                    </div>
                    <button type="button" class="add-contact-btn" onclick="abrirModalContacto()">
                        <i class="ti ti-plus"></i> Añadir contacto
                    </button>
                </div>

                <div class="contact-table-wrapper">
                    <table class="contact-table">
                        <thead>
                            <tr>
                                <th style="width:40px">#</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th style="width:90px">Estado</th>
                                <th style="width:90px">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="contact-table-body">
                            <!-- Los contactos se agregan aquí dinámicamente -->
                        </tbody>
                    </table>
                    <div id="empty-contacts" class="empty-message">
                        <i class="ti ti-user-off"></i>
                        No hay contactos registrados
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- ══════════ MODAL PARA NUEVO/EDITAR CONTACTO ══════════ --}}
<div class="modal-overlay" id="modal-contacto">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title" id="modal-contact-title">
                <i class="ti ti-user-plus"></i>
                Nuevo Contacto
            </div>
            <button type="button" class="modal-close" onclick="cerrarModalContacto()">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="modal-contact-edit-id" value="">
            <div class="field-group">
                <label>Nombre <span class="req">*</span></label>
                <input type="text" id="modal-contact-name" placeholder="Nombre del contacto" required>
            </div>
            <div class="field-group">
                <label>Apellidos</label>
                <input type="text" id="modal-contact-lastnames" placeholder="Apellidos del contacto">
            </div>
            <div class="field-group">
                <label>Correo electrónico</label>
                <input type="email" id="modal-contact-email" placeholder="ejemplo@correo.com">
            </div>
            <div class="field-group">
                <label>Cargo / Puesto</label>
                <input type="text" id="modal-contact-qualification" placeholder="Ej: Gerente, Coordinador...">
            </div>
            <div class="field-group phone-grid">
                <div>
                    <label>Teléfono 1</label>
                    <input type="text" id="modal-contact-phone1" placeholder="Teléfono principal">
                </div>
                <div>
                    <label>Teléfono 2</label>
                    <input type="text" id="modal-contact-phone2" placeholder="Teléfono opcional">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="cerrarModalContacto()">
                <i class="ti ti-x"></i> Cancelar
            </button>
            <button type="button" class="btn btn-primary" id="modal-contact-save-btn" onclick="guardarContactoModal()">
                <i class="ti ti-check"></i> Agregar contacto
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/clients-create.js') }}"></script>
@endpush
@endsection
