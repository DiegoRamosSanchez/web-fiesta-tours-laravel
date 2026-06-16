@extends('layouts.app')
@section('title', 'Importar Clientes')
@section('content')

<div class="page-header">
    <div class="page-title">Importar Clientes</div>
    <div class="page-sub">Carga masiva de clientes y contactos desde Excel</div>
</div>

<div style="max-width:680px">

    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:1.2rem">
            <i class="ti ti-alert-circle"></i>
            <ul style="list-style:none;margin-left:.5rem">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- PASO 1: Descargar plantilla --}}
    <div class="card" style="margin-bottom:1rem">
        <div class="card-header">
            <div>
                <div class="card-title">
                    <span style="background:#0f172a;color:#fff;border-radius:50%;width:22px;height:22px;
                                 display:inline-flex;align-items:center;justify-content:center;
                                 font-size:11px;font-weight:700;margin-right:6px">1</span>
                    Descarga la plantilla oficial
                </div>
                <div class="card-sub" style="margin-top:.4rem;margin-left:28px">
                    Usa la plantilla para asegurarte que el formato es correcto
                </div>
            </div>
            <a href="{{ route('admin.clients.template') }}"
               style="display:inline-flex;align-items:center;gap:7px;padding:.6rem 1.1rem;
                      background:#166534;color:#fff;border-radius:9px;font-size:13px;
                      font-weight:600;text-decoration:none;transition:background .15s"
               onmouseover="this.style.background='#14532d'"
               onmouseout="this.style.background='#166534'">
                <i class="ti ti-file-type-xls" style="font-size:16px"></i>
                Descargar plantilla .xlsx
            </a>
        </div>

        {{-- Instrucciones del formato --}}
        <div style="padding:1rem 1.4rem;background:#f8fafc;border-radius:0 0 12px 12px">
            <p style="font-size:12px;font-weight:700;color:#475569;margin-bottom:.7rem">
                Estructura de la plantilla (una fila por contacto):
            </p>
            <div style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse;font-size:11px">
                    <thead>
                        <tr style="background:#0f172a">
                            <th style="padding:6px 10px;color:#C9A84C;font-weight:700;border-right:1px solid #1e293b;white-space:nowrap">agencia_cliente</th>
                            <th style="padding:6px 10px;color:#C9A84C;font-weight:700;border-right:1px solid #1e293b;white-space:nowrap">contacto_1</th>
                            <th style="padding:6px 10px;color:#C9A84C;font-weight:700;border-right:1px solid #1e293b;white-space:nowrap">cargo_1</th>
                            <th style="padding:6px 10px;color:#C9A84C;font-weight:700;border-right:1px solid #1e293b;white-space:nowrap">email_1</th>
                            <th style="padding:6px 10px;color:#C9A84C;font-weight:700;border-right:1px solid #1e293b;white-space:nowrap">telefono_1</th>
                            <th style="padding:6px 10px;color:#C9A84C;font-weight:700;border-right:1px solid #1e293b;white-space:nowrap">telefono_2_1</th>
                            <th style="padding:6px 10px;color:#C9A84C;font-weight:700;white-space:nowrap">contacto_2 ... (hasta 3)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="background:#fff">
                            <td style="padding:6px 10px;font-weight:600;color:#0f172a;border-right:1px solid #f1f5f9">Empresa ABC</td>
                            <td style="padding:6px 10px;color:#475569;border-right:1px solid #f1f5f9">Juan</td>
                            <td style="padding:6px 10px;color:#475569;border-right:1px solid #f1f5f9">Gerente</td>
                            <td style="padding:6px 10px;color:#475569;border-right:1px solid #f1f5f9">juan@abc.com</td>
                            <td style="padding:6px 10px;color:#475569;border-right:1px solid #f1f5f9">987654321</td>
                            <td style="padding:6px 10px;color:#475569;border-right:1px solid #f1f5f9">01-234567</td>
                            <td style="padding:6px 10px;color:#94a3b8">María · Asistente · ...</td>
                        </tr>
                        <tr style="background:#f8f5ee">
                            <td style="padding:6px 10px;font-weight:600;color:#0f172a;border-right:1px solid #f1f5f9">Turismo XYZ</td>
                            <td style="padding:6px 10px;color:#475569;border-right:1px solid #f1f5f9">Carlos</td>
                            <td style="padding:6px 10px;color:#475569;border-right:1px solid #f1f5f9">Director</td>
                            <td style="padding:6px 10px;color:#475569;border-right:1px solid #f1f5f9">carlos@xyz.com</td>
                            <td style="padding:6px 10px;color:#475569;border-right:1px solid #f1f5f9">912345678</td>
                            <td style="padding:6px 10px;color:#475569;border-right:1px solid #f1f5f9">—</td>
                            <td style="padding:6px 10px;color:#94a3b8">Ana · ...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.7rem;margin-top:1rem">
                <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:.7rem .9rem">
                    <p style="font-size:11px;font-weight:700;color:#166534;margin-bottom:.3rem">
                        <i class="ti ti-circle-check" style="font-size:12px"></i> Reglas
                    </p>
                    <ul style="font-size:11px;color:#166534;list-style:none;display:flex;flex-direction:column;gap:2px">
                        <li>· La columna <strong>agencia_cliente</strong> es obligatoria</li>
                        <li>· El primer contacto será marcado como principal</li>
                        <li>· Si la agencia ya existe, se agregan los contactos</li>
                        <li>· Contactos duplicados por email son omitidos</li>
                    </ul>
                </div>
                <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:.7rem .9rem">
                    <p style="font-size:11px;font-weight:700;color:#991b1b;margin-bottom:.3rem">
                        <i class="ti ti-alert-circle" style="font-size:12px"></i> Evitar errores
                    </p>
                    <ul style="font-size:11px;color:#991b1b;list-style:none;display:flex;flex-direction:column;gap:2px">
                        <li>· No modifiques los nombres de los encabezados</li>
                        <li>· Borra las filas de ejemplo antes de importar</li>
                        <li>· Solo formatos .xlsx, .xls o .csv</li>
                        <li>· Máximo 5MB por archivo</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- PASO 2: Subir archivo --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">
                    <span style="background:#0f172a;color:#fff;border-radius:50%;width:22px;height:22px;
                                 display:inline-flex;align-items:center;justify-content:center;
                                 font-size:11px;font-weight:700;margin-right:6px">2</span>
                    Sube tu archivo Excel
                </div>
                <div class="card-sub" style="margin-top:.4rem;margin-left:28px">
                    Acepta .xlsx, .xls y .csv — Máximo 5MB
                </div>
            </div>
            <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary btn-sm">
                <i class="ti ti-arrow-left" style="font-size:13px"></i> Volver
            </a>
        </div>

        <form action="{{ route('admin.clients.import') }}" method="POST"
              enctype="multipart/form-data" id="form-import">
            @csrf

            {{-- Zona de drop --}}
            <div id="drop-zone"
                 onclick="document.getElementById('archivo').click()"
                 style="border:2px dashed #e2e8f0;border-radius:12px;padding:2.5rem;
                        text-align:center;cursor:pointer;transition:all .2s;margin-bottom:1.2rem">
                <i class="ti ti-cloud-upload" id="drop-icon"
                   style="font-size:40px;color:#cbd5e1;display:block;margin-bottom:.7rem"></i>
                <p id="drop-text" style="font-size:14px;font-weight:600;color:#475569">
                    Arrastra tu archivo aquí o haz clic para seleccionar
                </p>
                <p style="font-size:12px;color:#94a3b8;margin-top:.3rem">
                    .xlsx · .xls · .csv — máx. 5MB
                </p>
                <input type="file" name="archivo" id="archivo"
                       accept=".xlsx,.xls,.csv" style="display:none" required>
            </div>

            <div id="file-preview" style="display:none;background:#f0fdf4;border:1px solid #bbf7d0;
                                          border-radius:10px;padding:.9rem 1.1rem;margin-bottom:1.2rem;
                                          display:none;align-items:center;gap:10px">
                <i class="ti ti-file-type-xls" style="font-size:24px;color:#166534;flex-shrink:0"></i>
                <div style="flex:1">
                    <div id="file-name" style="font-size:13px;font-weight:600;color:#166534"></div>
                    <div id="file-size" style="font-size:11px;color:#4ade80"></div>
                </div>
                <button type="button" onclick="clearFile()"
                        style="background:none;border:none;cursor:pointer;color:#166534;font-size:18px">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            <div style="display:flex;gap:.8rem;padding-top:1rem;border-top:1px solid #f1f5f9">
                <button type="submit" class="btn btn-primary" id="btn-import" disabled>
                    <i class="ti ti-upload" style="font-size:14px"></i> Importar clientes
                </button>
                <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<script>
const input    = document.getElementById('archivo');
const dropZone = document.getElementById('drop-zone');
const preview  = document.getElementById('file-preview');
const btnImport= document.getElementById('btn-import');

input.addEventListener('change', () => {
    if (input.files[0]) showFile(input.files[0]);
});

// Drag & drop
dropZone.addEventListener('dragover', e => {
    e.preventDefault();
    dropZone.style.borderColor = '#6366f1';
    dropZone.style.background  = '#f5f3ff';
});
dropZone.addEventListener('dragleave', () => {
    dropZone.style.borderColor = '#e2e8f0';
    dropZone.style.background  = '';
});
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.style.borderColor = '#e2e8f0';
    dropZone.style.background  = '';
    const file = e.dataTransfer.files[0];
    if (file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        showFile(file);
    }
});

function showFile(file) {
    document.getElementById('file-name').textContent = file.name;
    document.getElementById('file-size').textContent = (file.size / 1024).toFixed(1) + ' KB';
    preview.style.display  = 'flex';
    dropZone.style.display = 'none';
    btnImport.disabled     = false;
}

function clearFile() {
    input.value            = '';
    preview.style.display  = 'none';
    dropZone.style.display = 'block';
    btnImport.disabled     = true;
}

// Loading al enviar
document.getElementById('form-import').addEventListener('submit', () => {
    btnImport.innerHTML = '<i class="ti ti-loader-2" style="font-size:14px;animation:spin .8s linear infinite"></i> Procesando...';
    btnImport.disabled  = true;
});
</script>
<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush
@endsection
