{{-- admin.clients.export-pdf --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Listado de Clientes</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; font-size: 12px; color: #1a1a2e; background: #fff; padding: 20px 25px; line-height: 1.5; }
        .container { max-width: 100%; margin: 0 auto; }

        /* ── FRANJA DORADA ── */
        .gold-strip { background-color: #C9A84C; height: 5px; border-radius: 8px 8px 0 0; }

        /* ── ENCABEZADO ── */
        .header { background: #0B1F3A; padding: 18px 25px; border-radius: 0 0 8px 8px; margin-bottom: 20px; }
        .header h1 { font-size: 20px; font-weight: 700; color: #C9A84C; margin: 0; letter-spacing: 0.5px; }
        .header .subtitle { font-size: 9px; color: #94A3B8; font-style: italic; margin-top: 4px; }

        /* ── RESUMEN HORIZONTAL CON TABLA ── */
        .summary-table { width: 100%; border-collapse: collapse; margin-bottom: 22px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; }
        .summary-table td { padding: 12px 16px; text-align: center; vertical-align: middle; border-right: 1px solid #e2e8f0; }
        .summary-table td:last-child { border-right: none; }
        .summary-table .label { display: block; font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
        .summary-table .value { display: block; font-size: 20px; font-weight: 700; color: #0B1F3A; margin-top: 2px; }
        .summary-table .value.gold { color: #C9A84C; }

        /* ── DETALLE DE CONTACTOS ── */
        .section-title { font-size: 16px; font-weight: 700; color: #0B1F3A; margin: 20px 0 14px 0; padding-bottom: 8px; border-bottom: 3px solid #C9A84C; }
        .client-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; margin-bottom: 16px; padding: 16px 20px; page-break-inside: avoid; }
        .client-card .client-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 2px solid #f1f5f9; }
        .client-card .client-name { font-size: 15px; font-weight: 700; color: #0B1F3A; }
        .client-card .client-count { font-size: 11px; color: #64748b; background: #f1f5f9; padding: 2px 14px; border-radius: 20px; font-weight: 600; }
        .contacts-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .contact-item { background: #fafafa; border: 1px solid #e8ecf0; border-radius: 8px; padding: 10px 14px; }
        .contact-item.principal { background: #FFF8E7; border-color: #C9A84C; }
        .contact-item .contact-name { font-size: 13px; font-weight: 600; color: #0f172a; display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
        .contact-item .badge-principal { background: #C9A84C; color: #0B1F3A; font-size: 7px; font-weight: 700; padding: 1px 10px; border-radius: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        .contact-item .contact-detail { font-size: 10.5px; color: #475569; margin-top: 4px; display: flex; flex-direction: column; gap: 1px; }
        .contact-item .contact-detail .row { display: flex; align-items: baseline; gap: 4px; }
        .contact-item .contact-detail .label { color: #94a3b8; font-weight: 600; min-width: 60px; }
        .contact-item .contact-detail .value { color: #0f172a; }

        /* ── PIE DE PÁGINA ── */
        .footer { background-color: #E8C97A; color: #0B1F3A; text-align: center; padding: 10px 20px; font-size: 9px; font-style: italic; border-radius: 8px; margin-top: 25px; }

        /* ── RESPONSIVE ── */
        @media print { body { padding: 15px 20px; } .contacts-grid { grid-template-columns: 1fr; } .client-card { break-inside: avoid; page-break-inside: avoid; } }

        @page { margin: 12mm 10mm 12mm 10mm; }

        /* ── SIN REGISTROS ── */
        .empty-state { text-align: center; padding: 40px; color: #94a3b8; border: 2px dashed #e2e8f0; border-radius: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Franja dorada -->
        <div class="gold-strip"></div>

        <!-- Encabezado -->
        <div class="header">
            <h1>FIESTA TOURS PERU · Listado de Clientes</h1>
            <div class="subtitle">
                Generado: {{ now()->format('d/m/Y H:i') }} hrs · Documento confidencial · Uso interno · www.fiestatoursperu.com
            </div>
        </div>

        <!-- Resumen Horizontal con TABLA (más compatible) -->
        <table class="summary-table">
            <tr>
                <td>
                    <span class="label">Total Clientes</span>
                    <span class="value">{{ $clients->count() }}</span>
                </td>
                <td>
                    <span class="label">Total Contactos</span>
                    <span class="value gold">{{ $clients->sum('contacts_count') }}</span>
                </td>
                <td>
                    <span class="label">Clientes Activos</span>
                    <span class="value">{{ $clients->count() }}</span>
                </td>
                <td>
                    <span class="label">Fecha Generación</span>
                    <span class="value" style="font-size:14px;">{{ now()->format('d/m/Y') }}</span>
                </td>
            </tr>
        </table>

        <!-- Detalle de Contactos por Cliente -->
        <div class="section-title">
            Detalle de Contactos por Cliente
        </div>

        @forelse($clients as $client)
            <div class="client-card">
                <div class="client-header">
                    <div class="client-name">{{ $client->name_client }}</div>
                    <div class="client-count">{{ $client->contacts_count }} contacto(s)</div>
                </div>

                @if($client->contacts->isNotEmpty())
                    <div class="contacts-grid">
                        @foreach($client->contacts as $contact)
                            <div class="contact-item {{ $contact->es_principal ? 'principal' : '' }}">
                                <div class="contact-name">
                                    {{ trim($contact->name . ' ' . $contact->last_names) }}
                                    @if($contact->es_principal)
                                        <span class="badge-principal">Principal</span>
                                    @endif
                                </div>
                                <div class="contact-detail">
                                    <div class="row">
                                        <span class="label">Cargo:</span>
                                        <span class="value">{{ $contact->qualification ?? '—' }}</span>
                                    </div>
                                    <div class="row">
                                        <span class="label">Email:</span>
                                        <span class="value">{{ $contact->email ?? '—' }}</span>
                                    </div>
                                    <div class="row">
                                        <span class="label">Teléfonos:</span>
                                        <span class="value">
                                            {{ $contact->first_phone ?? '—' }}
                                            @if($contact->second_phone)
                                                | {{ $contact->second_phone }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align:center;padding:8px;color:#94a3b8;font-size:11px;font-style:italic;">
                        No hay contactos registrados
                    </div>
                @endif
            </div>
        @empty
            <div class="empty-state">
                <div style="font-size:28px;margin-bottom:8px;">📭</div>
                <div style="font-size:14px;font-weight:600;color:#64748b;">No hay clientes registrados</div>
                <div style="font-size:11px;margin-top:4px;color:#94a3b8;">Comienza creando tu primer cliente</div>
            </div>
        @endforelse

        <!-- Pie de página -->
        <div class="footer">
            Fiesta Tours Peru &copy; {{ now()->format('Y') }} · Lima, Peru · Sistema de Gestion Interna
        </div>
    </div>
</body>
</html>
