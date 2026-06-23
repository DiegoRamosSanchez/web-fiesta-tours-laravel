<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Proveedores</title>
<style>
    @page {
        margin: 30px 36px;
    }

    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'DejaVu Sans', sans-serif;
        color: #1e293b;
        font-size: 12px;
        margin: 0;
        padding: 0;
    }

    /* ── HEADER ── */
    .header-table {
        width: 100%;
        margin-bottom: 22px;
        border-collapse: collapse;
    }
    .header-table td {
        vertical-align: top;
        padding: 0;
    }
    .doc-title {
        font-size: 26px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    .doc-sub {
        font-size: 11px;
        color: #64748b;
        margin: 2px 0 0;
    }
    .logo-cell {
        text-align: right;
    }
    .logo-fiesta {
        font-size: 13px;
        font-weight: 700;
        color: #1e3a2f;
        line-height: 1.2;
    }
    .logo-fiesta .accent {
        color: #2f7d4f;
    }
    .logo-peru {
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 1px;
        color: #0f172a;
        margin-top: 4px;
    }
    .logo-peru .accentx {
        color: #c9a227;
    }
    .logo-sub {
        font-size: 8px;
        letter-spacing: 2px;
        color: #64748b;
    }

    /* ── BLOQUE POR PROVEEDOR ── */
    .supplier-block {
        margin-bottom: 26px;
    }
    .supplier-block:not(:first-child) {
        page-break-before: always;
        padding-top: 4px;
    }

    /* ── SECTION TITLES ── */
    .section {
        margin-top: 18px;
        margin-bottom: 8px;
    }
    .section-title {
        font-size: 12px;
        font-weight: 700;
        color: #0f172a;
        text-transform: uppercase;
        border-left: 4px solid #2f7d4f;
        padding-left: 8px;
        margin: 0 0 10px 0;
    }

    /* ── KEY/VALUE INFO TABLE ── */
    .info-table {
        width: 100%;
        border-collapse: collapse;
    }
    .info-table tr td {
        padding: 7px 10px;
        font-size: 11.5px;
        vertical-align: middle;
    }
    .info-table tr:nth-child(odd) td {
        background-color: #f8fafc;
    }
    .info-label {
        color: #64748b;
        width: 38%;
    }
    .info-value {
        color: #0f172a;
        font-weight: 700;
    }

    /* ── CONTACTS TABLE ── */
    .contacts-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 4px;
    }
    .contacts-table thead th {
        background-color: #8fb89e;
        color: #0f2419;
        font-size: 10.5px;
        text-transform: uppercase;
        font-weight: 700;
        padding: 7px 9px;
        text-align: left;
        border: 1px solid #7aa688;
    }
    .contacts-table tbody td {
        font-size: 11px;
        padding: 7px 9px;
        border: 1px solid #e2e8f0;
        color: #1e293b;
        vertical-align: top;
    }
    .contacts-table tbody tr:nth-child(even) td {
        background-color: #f8fafc;
    }
    .badge-principal {
        display: inline-block;
        background-color: #dcfce7;
        color: #15803d;
        font-size: 8.5px;
        font-weight: 700;
        text-transform: uppercase;
        padding: 2px 6px;
        border-radius: 3px;
        margin-left: 4px;
    }
    .empty-note {
        color: #94a3b8;
        font-size: 11px;
        font-style: italic;
        padding: 10px 0;
    }
</style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td>
                <p class="doc-title">{{ $suppliers->count() === 1 ? 'Ficha de Proveedor' : 'Listado de Proveedores' }}</p>
                <p class="doc-sub">Generado el {{ now()->format('d/m/Y, h:i A') }}</p>
            </td>
            <td class="logo-cell">
                <div class="logo-fiesta">FIESTA<br><span class="accent">TOURS PERU</span></div>
                <div class="logo-peru">PERU LU<span class="accentx">X</span>URY</div>
                <div class="logo-sub">J&nbsp;&nbsp;O&nbsp;&nbsp;U&nbsp;&nbsp;R&nbsp;&nbsp;N&nbsp;&nbsp;E&nbsp;&nbsp;Y&nbsp;&nbsp;S</div>
            </td>
        </tr>
    </table>

    @forelse($suppliers as $supplier)
        <div class="supplier-block">

            {{-- ===================== INFORMACIÓN DEL PROVEEDOR ===================== --}}
            <div class="section">
                <p class="section-title">Información del Proveedor</p>
                <table class="info-table">
                    <tr>
                        <td class="info-label">Nombre Comercial</td>
                        <td class="info-value">{{ $supplier->supplier_name }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Razón Social</td>
                        <td class="info-value">{{ $supplier->business_name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Código Tributario / RUC</td>
                        <td class="info-value">{{ $supplier->tax_code ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Teléfono General</td>
                        <td class="info-value">{{ $supplier->general_phone ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Correo General</td>
                        <td class="info-value">{{ $supplier->general_email ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Destino / Ubicación</td>
                        <td class="info-value">{{ $supplier->destination->destination_name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Categoría</td>
                        <td class="info-value">{{ $supplier->category->category_name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Fecha de Registro</td>
                        <td class="info-value">{{ $supplier->created_at->format('d/m/Y, h:i A') }}</td>
                    </tr>
                </table>
            </div>

            {{-- ===================== CONTACTOS ===================== --}}
            <div class="section">
                <p class="section-title">Contactos ({{ $supplier->contacts->count() }})</p>

                @if($supplier->contacts->isEmpty())
                    <p class="empty-note">No hay contactos registrados para este proveedor.</p>
                @else
                    <table class="contacts-table">
                        <thead>
                            <tr>
                                <th style="width:20%">Nombre</th>
                                <th style="width:18%">Apellidos</th>
                                <th style="width:22%">Email</th>
                                <th style="width:18%">Cargo</th>
                                <th style="width:22%">Teléfono(s)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($supplier->contacts as $contact)
                                <tr>
                                    <td>
                                        {{ $contact->name }}
                                        @if($contact->es_principal)
                                            <span class="badge-principal">Principal</span>
                                        @endif
                                    </td>
                                    <td>{{ $contact->last_names ?? '—' }}</td>
                                    <td>{{ $contact->email ?? '—' }}</td>
                                    <td>{{ $contact->qualification ?? '—' }}</td>
                                    <td>
                                        {{ $contact->first_phone ?? '—' }}
                                        @if($contact->second_phone)
                                            / {{ $contact->second_phone }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

        </div>
    @empty
        <p class="empty-note">No se encontraron proveedores.</p>
    @endforelse

</body>
</html>