<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: DejaVu Sans, Arial, sans-serif;
        font-size: 10px;
        color: #1a1a2e;
        background: #fff;
    }

    /* ── HEADER ── */
    .header {
        background: #0B1F3A;
        padding: 20px 28px 18px 28px;
    }
    .header-gold-line {
        height: 3px;
        background: #C9A84C;
        margin-bottom: 14px;
    }
    .header-table {
        width: 100%;
    }
    .brand-name {
        font-size: 20px;
        font-weight: 700;
        color: #ffffff;
        letter-spacing: 1px;
        line-height: 1.1;
    }
    .brand-name span { color: #C9A84C; }
    .brand-tagline {
        font-size: 7.5px;
        color: #94a3b8;
        letter-spacing: 2.5px;
        text-transform: uppercase;
        margin-top: 4px;
    }
    .doc-title {
        font-size: 13px;
        font-weight: 700;
        color: #C9A84C;
        letter-spacing: .8px;
        text-transform: uppercase;
        text-align: right;
    }
    .doc-meta {
        font-size: 8px;
        color: #94a3b8;
        margin-top: 5px;
        line-height: 1.7;
        text-align: right;
    }

    /* ── SUMMARY STRIP ── */
    .summary {
        background: #f8f5ee;
        border-left: 4px solid #C9A84C;
        padding: 12px 28px;
    }
    .summary-table { width: 100%; }
    .summary-label {
        font-size: 7px;
        font-weight: 700;
        color: #C9A84C;
        text-transform: uppercase;
        letter-spacing: .8px;
        display: block;
        margin-bottom: 2px;
    }
    .summary-value {
        font-size: 18px;
        font-weight: 700;
        color: #0B1F3A;
        line-height: 1;
    }
    .summary-value small {
        font-size: 8.5px;
        font-weight: 400;
        color: #64748b;
    }
    .summary-divider {
        width: 1px;
        background: #e2e8f0;
        padding: 0 12px;
    }

    /* ── SECTION TITLE ── */
    .section-wrap {
        padding: 14px 28px 8px 28px;
    }
    .section-title-text {
        font-size: 7.5px;
        font-weight: 700;
        color: #0B1F3A;
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    .section-line {
        border: none;
        border-top: 1px solid #e2e8f0;
        margin-top: 6px;
    }

    /* ── TABLE ── */
    .table-wrap { padding: 0 28px 20px 28px; }
    table.data { width: 100%; border-collapse: collapse; }

    table.data thead tr { background: #0B1F3A; }
    table.data thead th {
        padding: 9px 8px;
        text-align: left;
        font-size: 7.5px;
        font-weight: 700;
        color: #C9A84C;
        text-transform: uppercase;
        letter-spacing: .7px;
        border-right: 1px solid #1a3050;
    }
    table.data thead th:last-child { border-right: none; }

    table.data tbody tr { border-bottom: 1px solid #f1f5f9; }
    table.data tbody tr.even { background: #f8f5ee; }
    table.data tbody tr.odd  { background: #ffffff; }
    table.data tbody tr:last-child { border-bottom: 2px solid #C9A84C; }

    table.data tbody td {
        padding: 8px 8px;
        font-size: 9px;
        color: #374151;
        border-right: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    table.data tbody td:last-child { border-right: none; }

    .td-id    { color: #94a3b8; font-weight: 700; font-size: 8px; text-align: center; }
    .td-agency{ font-weight: 700; color: #0B1F3A; font-size: 9.5px; }
    .td-sub   { font-size: 7.5px; color: #94a3b8; margin-top: 2px; }
    .td-contact-name { font-weight: 600; color: #0f172a; }

    .badge-active {
        background: #dcfce7;
        color: #166534;
        font-size: 7px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 20px;
        border: 1px solid #bbf7d0;
        text-transform: uppercase;
        letter-spacing: .5px;
    }
    .badge-count {
        background: #eef2ff;
        color: #0B1F3A;
        font-size: 8px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 20px;
        border: 1px solid #c7d2fe;
    }
    .center { text-align: center; }

    /* ── FOOTER ── */
    .footer-line {
        height: 3px;
        background: #C9A84C;
    }
    .footer {
        background: #0B1F3A;
        padding: 10px 28px;
    }
    .footer-table { width: 100%; }
    .footer-brand {
        font-size: 8px;
        color: #C9A84C;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
    }
    .footer-info {
        font-size: 7.5px;
        color: #64748b;
        text-align: right;
        line-height: 1.7;
    }
</style>
</head>
<body>

{{-- ── HEADER ── --}}
<div class="header">
    <div class="header-gold-line"></div>
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="vertical-align:middle; width:55%">
                <div class="brand-name">FIESTA <span>TOURS</span></div>
                <div class="brand-tagline">Peru &nbsp;·&nbsp; Live your impossible dreams</div>
            </td>
            <td style="vertical-align:middle; width:45%">
                <div class="doc-title">Listado de Clientes</div>
                <div class="doc-meta">
                    Generado: {{ now()->format('d/m/Y') }} &nbsp;·&nbsp; {{ now()->format('H:i') }} hrs<br>
                    Documento confidencial &nbsp;·&nbsp; Uso interno
                </div>
            </td>
        </tr>
    </table>
</div>

{{-- ── SUMMARY STRIP ── --}}
<div class="summary">
    <table class="summary-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width:22%; padding-right:16px;">
                <span class="summary-label">Total Clientes</span>
                <span class="summary-value">{{ $clients->count() }} <small>registros</small></span>
            </td>
            <td style="width:1%; background:#ddd; padding:0 1px;">&nbsp;</td>
            <td style="width:22%; padding-left:16px; padding-right:16px;">
                <span class="summary-label">Total Contactos</span>
                <span class="summary-value">{{ $clients->sum('contacts_count') }} <small>personas</small></span>
            </td>
            <td style="width:1%; background:#ddd; padding:0 1px;">&nbsp;</td>
            <td style="width:30%; padding-left:16px; padding-right:16px;">
                <span class="summary-label">Con Contacto Principal</span>
                <span class="summary-value">{{ $clients->filter(fn($c) => $c->contacts->isNotEmpty())->count() }} <small>clientes</small></span>
            </td>
            <td style="width:1%; background:#ddd; padding:0 1px;">&nbsp;</td>
            <td style="width:22%; padding-left:16px;">
                <span class="summary-label">Período</span>
                <span class="summary-value" style="font-size:14px">{{ now()->format('Y') }} <small>en curso</small></span>
            </td>
        </tr>
    </table>
</div>

{{-- ── SECTION TITLE ── --}}
<div class="section-wrap">
    <div class="section-title-text">Directorio de Agencias</div>
    <hr class="section-line">
</div>

{{-- ── TABLE ── --}}
<div class="table-wrap">
    <table class="data" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width:32px">ID</th>
                <th style="width:22%">Agencia / Cliente</th>
                <th style="width:20%">Contacto Principal</th>
                <th style="width:19%">Email</th>
                <th style="width:12%">Teléfono</th>
                <th style="width:8%; text-align:center">Contactos</th>
                <th style="width:9%; text-align:center">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clients as $i => $c)
                @php $p = $c->contacts->first(); @endphp
                <tr class="{{ $i % 2 === 0 ? 'odd' : 'even' }}">
                    <td class="td-id">#{{ $c->id_client }}</td>
                    <td>
                        <div class="td-agency">{{ $c->name_client }}</div>
                        <div class="td-sub">Reg. {{ $c->created_at->format('d/m/Y') }}</div>
                    </td>
                    <td>
                        @if($p)
                            <div class="td-contact-name">{{ trim($p->name.' '.$p->last_names) }}</div>
                            @if($p->qualification)
                                <div class="td-sub">{{ $p->qualification }}</div>
                            @endif
                        @else
                            <span style="color:#cbd5e1">—</span>
                        @endif
                    </td>
                    <td style="color:#4b5563">{{ $p?->email ?? '—' }}</td>
                    <td style="color:#4b5563">{{ $p?->first_phone ?? '—' }}</td>
                    <td class="center"><span class="badge-count">{{ $c->contacts_count }}</span></td>
                    <td class="center"><span class="badge-active">Activo</span></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- ── FOOTER ── --}}
<div class="footer-line"></div>
<div class="footer">
    <table class="footer-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="vertical-align:middle; width:50%">
                <div class="footer-brand">Fiesta Tours Peru &copy; {{ now()->format('Y') }}</div>
            </td>
            <td style="vertical-align:middle; width:50%">
                <div class="footer-info">
                    Sistema de Gestión Interna &nbsp;·&nbsp; Documento generado automáticamente<br>
                    www.fiestatoursperu.com &nbsp;·&nbsp; Lima, Perú
                </div>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
