{{-- admin.clients.export-pdf --}}
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: DejaVu Sans, Arial, sans-serif;
        font-size: 9px;
        color: #1a1a2e;
        background: #fff;
    }

    /* Header styles igual que antes... */
    .header { background: #0B1F3A; padding: 15px 20px; }
    .header-gold-line { height: 3px; background: #C9A84C; margin-bottom: 10px; }
    .brand-name { font-size: 18px; font-weight: 700; color: #fff; }
    .brand-name span { color: #C9A84C; }
    .doc-title { font-size: 12px; font-weight: 700; color: #C9A84C; text-align: right; }

    .summary { background: #f8f5ee; padding: 10px 20px; }
    .table-wrap { padding: 15px 20px; overflow-x: auto; }

    table.data {
        width: 100%;
        border-collapse: collapse;
        font-size: 8px;
    }

    table.data thead tr { background: #0B1F3A; }
    table.data thead th {
        padding: 6px 5px;
        text-align: left;
        font-weight: 700;
        color: #C9A84C;
        border-right: 1px solid #1a3050;
        white-space: nowrap;
    }

    table.data tbody tr { border-bottom: 1px solid #e2e8f0; }
    table.data tbody tr:nth-child(even) { background: #f8f5ee; }
    table.data tbody td {
        padding: 6px 5px;
        border-right: 1px solid #f1f5f9;
        vertical-align: top;
    }

    .badge-active {
        background: #dcfce7;
        color: #166534;
        font-size: 7px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 20px;
        white-space: nowrap;
    }

    .contact-group {
        margin-bottom: 8px;
        padding-bottom: 8px;
        border-bottom: 1px dashed #e2e8f0;
    }
    .contact-group:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
    .contact-name { font-weight: 700; color: #0B1F3A; }
    .contact-detail { font-size: 7px; color: #64748b; margin-top: 2px; }

    .footer { background: #0B1F3A; padding: 8px 20px; }
    .footer-line { height: 3px; background: #C9A84C; }
</style>
</head>
<body>

<div class="header">
    <div class="header-gold-line"></div>
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width:50%">
                <div class="brand-name">FIESTA <span>TOURS</span></div>
                <div style="font-size: 7px; color: #94a3b8;">Peru · Live your impossible dreams</div>
            </td>
            <td style="width:50%; text-align:right">
                <div class="doc-title">Listado de Clientes</div>
                <div style="font-size: 7px; color: #94a3b8;">
                    Generado: {{ now()->format('d/m/Y H:i') }} hrs<br>
                    Documento confidencial · Uso interno
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="summary">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width:33%">
                <strong style="color:#C9A84C; font-size:7px;">TOTAL CLIENTES</strong><br>
                <span style="font-size:16px; font-weight:700;">{{ $clients->count() }}</span>
            </td>
            <td style="width:33%">
                <strong style="color:#C9A84C; font-size:7px;">TOTAL CONTACTOS</strong><br>
                <span style="font-size:16px; font-weight:700;">{{ $clients->sum('contacts_count') }}</span>
            </td>
            <td style="width:33%">
                <strong style="color:#C9A84C; font-size:7px;">FECHA</strong><br>
                <span style="font-size:12px; font-weight:700;">{{ now()->format('d/m/Y') }}</span>
            </td>
        </tr>
    </table>
</div>

<div class="table-wrap">
    <table class="data" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width:35px">ID</th>
                <th style="width:22%">Agencia</th>
                <th style="width:45%">Contactos</th>
                <th style="width:10%">Total</th>
                <th style="width:10%">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clients as $client)
            <tr>
                <td style="text-align:center; color:#94a3b8;">#{{ $client->id_client }}</td>
                <td>
                    <strong style="color:#0B1F3A;">{{ $client->name_client }}</strong>
                    <div style="font-size:7px; color:#94a3b8;">Reg: {{ $client->created_at->format('d/m/Y') }}</div>
                </td>
                <td>
                    @foreach($client->contacts as $idx => $contact)
                    <div class="contact-group">
                        <div class="contact-name">
                            @if($idx === 0)⭐ @endif
                            {{ trim($contact->name . ' ' . ($contact->last_names ?? '')) }}
                            @if($contact->qualification)
                            <span style="font-weight:normal; color:#C9A84C;">({{ $contact->qualification }})</span>
                            @endif
                        </div>
                        <div class="contact-detail">
                            @if($contact->email) ✉ {{ $contact->email }} @endif
                            @if($contact->first_phone) | 📞 {{ $contact->first_phone }} @endif
                            @if($contact->second_phone) | 📱 {{ $contact->second_phone }} @endif
                        </div>
                    </div>
                    @endforeach
                    @if($client->contacts->isEmpty())
                    <span style="color:#cbd5e1;">— Sin contactos registrados —</span>
                    @endif
                </td>
                <td style="text-align:center">
                    <span style="background:#eef2ff; padding:2px 8px; border-radius:20px; font-weight:700;">
                        {{ $client->contacts_count }}
                    </span>
                </td>
                <td style="text-align:center">
                    <span class="badge-active">ACTIVO</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="footer-line"></div>
<div class="footer">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="color:#C9A84C; font-size:8px;">Fiesta Tours Peru &copy; {{ now()->format('Y') }}</td>
            <td style="text-align:right; color:#64748b; font-size:7px;">
                Sistema de Gestión Interna · www.fiestatoursperu.com · Lima, Perú
            </td>
        </tr>
    </table>
</div>

</body>
</html>
