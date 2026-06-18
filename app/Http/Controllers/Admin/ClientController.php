<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Contact;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientsExport;
use App\Imports\ClientsImport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::with(['contacts' => fn($q) => $q->where('es_principal', true)])
            ->withCount('contacts')
            ->orderBy('created_at', 'desc')->get();
        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_client'             => 'required|string|max:100',
            'contacts.*.name'         => 'required|string|max:100',
            'contacts.*.last_names'   => 'nullable|string|max:100',
            'contacts.*.email'        => 'nullable|email|max:80',
            'contacts.*.first_phone'  => 'nullable|string|max:20',
            'contacts.*.second_phone' => 'nullable|string|max:20',
        ]);

        $client = Client::create(['name_client' => $request->name_client]);

        foreach ($request->input('contacts', []) as $i => $data) {
            if (empty($data['name'])) continue;
            $client->contacts()->create([
                'name'         => $data['name'],
                'last_names'   => $data['last_names']   ?? null,
                'email'        => $data['email']        ?? null,
                'first_phone'  => $data['first_phone']  ?? null,
                'second_phone' => $data['second_phone'] ?? null,
                'es_principal' => $i === 0,
            ]);
        }

        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    public function edit(Client $client)
    {
        $client->load('contacts');
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate(['name_client' => 'required|string|max:100']);
        $client->update(['name_client' => $request->name_client]);
        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return back()->with('success', 'Cliente eliminado correctamente.');
    }

    // ── EXPORTAR PDF ──────────────────────────────────────────
    public function exportPdf()
    {
        $clients = Client::with(['contacts' => function($q) {
                $q->orderBy('es_principal', 'desc')->orderBy('created_at');
            }])
            ->withCount('contacts')
            ->orderBy('name_client')
            ->get();

        // Configurar PDF en formato vertical (retrato)
        $pdf = Pdf::loadView('admin.clients.export-pdf', compact('clients'))
            ->setPaper('a4', 'portrait') // Cambiar de 'landscape' a 'portrait'
            ->setOptions([
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'dpi' => 96,
                'defaultPaperSize' => 'a4',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
            ]);

        return $pdf->download('clientes_' . now()->format('Ymd') . '.pdf');
    }

    // ── EXPORTAR EXCEL ────────────────────────────────────────
    public function exportExcel()
    {
        return Excel::download(
            new ClientsExport(),
            'clientes_' . now()->format('Ymd') . '.xlsx'
        );
    }

    // ── VISTA IMPORTAR ────────────────────────────────────────
    public function importView()
    {
        return view('admin.clients.import');
    }

    // ── PROCESAR IMPORTACIÓN ──────────────────────────────────
    public function import(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'archivo.required' => 'Debes seleccionar un archivo.',
            'archivo.mimes'    => 'Solo se aceptan archivos .xlsx, .xls o .csv.',
            'archivo.max'      => 'El archivo no puede superar los 5MB.',
        ]);

        $import = new ClientsImport();

        try {
            Excel::import($import, $request->file('archivo'));
        } catch (\Exception $e) {
            return back()->withErrors(['archivo' => 'Error al procesar el archivo: ' . $e->getMessage()]);
        }

        $msg = "Importación completada: {$import->imported} cliente(s) procesado(s).";
        if ($import->skipped > 0) {
            $msg .= " {$import->skipped} fila(s) omitida(s).";
        }
        if (!empty($import->errors)) {
            $msg .= ' Con errores: ' . implode(' | ', $import->errors);
        }

        return redirect()->route('admin.clients.index')->with('success', $msg);
    }

    // ── DESCARGAR PLANTILLA EXCEL ─────────────────────────────
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Clientes');

        // ── Colores
        $navy = '0B1F3A';
        $gold = 'C9A84C';
        $alt  = 'F8F5EE';

        // ── Fila 1: franja dorada
        $sheet->mergeCells('A1:Q1');
        $sheet->getRowDimension(1)->setRowHeight(5);
        $sheet->getStyle('A1:Q1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB($gold);

        // ── Fila 2: título
        $sheet->mergeCells('A2:Q2');
        $sheet->setCellValue('A2', 'FIESTA TOURS PERU  ·  Plantilla de Importación de Clientes');
        $sheet->getRowDimension(2)->setRowHeight(26);
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FF'.$gold], 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF'.$navy]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 2],
        ]);

        // ── Fila 3: instrucciones
        $sheet->mergeCells('A3:Q3');
        $sheet->setCellValue('A3', 'INSTRUCCIONES: Una fila por contacto. El primer contacto de cada Agencia será el Principal. No modifiques los encabezados de la fila 4.');
        $sheet->getRowDimension(3)->setRowHeight(14);
        $sheet->getStyle('A3')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 8, 'color' => ['argb' => 'FFFBBF24'], 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF'.$navy]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 2],
        ]);

        // ── Fila 4: encabezados
        $headers = [
            'A4' => 'agencia_cliente',
            'B4' => 'contacto_1',
            'C4' => 'cargo_1',
            'D4' => 'email_1',
            'E4' => 'telefono_1',
            'F4' => 'telefono_2_1',
            'G4' => 'contacto_2',
            'H4' => 'cargo_2',
            'I4' => 'email_2',
            'J4' => 'telefono_1_2',
            'K4' => 'telefono_2_2',
            'L4' => 'contacto_3',
            'M4' => 'cargo_3',
            'N4' => 'email_3',
            'O4' => 'telefono_1_3',
            'P4' => 'telefono_2_3',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $sheet->getRowDimension(4)->setRowHeight(18);
        $sheet->getStyle('A4:P4')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF'.$gold], 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF'.$navy]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF'.$gold]]],
        ]);

        // ── Filas de ejemplo
        $ejemplos = [
            5 => ['Empresa ABC S.A.C.', 'Juan', 'Gerente General', 'juan@abc.com', '987654321', '01-2345678', 'María', 'Asistente', 'maria@abc.com', '956789012', '', '', '', '', '', ''],
            6 => ['Turismo XYZ', 'Carlos', 'Director', 'carlos@xyz.com', '912345678', '', 'Ana', 'Coordinadora', 'ana@xyz.com', '934567890', '01-9876543', 'Pedro', 'Técnico', 'pedro@xyz.com', '945678901', ''],
            7 => ['Viajes Express', 'Luis', 'Gerente', 'luis@express.com', '923456789', '', '', '', '', '', '', '', '', '', '', ''],
        ];

        $cols = range('A', 'P');
        foreach ($ejemplos as $rowNum => $values) {
            foreach ($values as $i => $val) {
                $sheet->setCellValue($cols[$i] . $rowNum, $val);
            }
            $isEven = ($rowNum - 5) % 2 === 0;
            $sheet->getStyle('A'.$rowNum.':P'.$rowNum)->applyFromArray([
                'font'      => ['size' => 9, 'name' => 'Arial', 'color' => ['argb' => 'FF64748B']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $isEven ? 'FFFFFFFF' : 'FF'.$alt]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
            ]);
        }

        // ── Nota en fila 9
        $sheet->mergeCells('A9:P9');
        $sheet->setCellValue('A9', '↑  FILAS DE EJEMPLO — Bórralas antes de importar y agrega tus propios datos desde la fila 5');
        $sheet->getRowDimension(9)->setRowHeight(14);
        $sheet->getStyle('A9')->applyFromArray([
            'font'      => ['italic' => true, 'bold' => true, 'size' => 8, 'color' => ['argb' => 'FF'.$navy]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFF8E7']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 2],
            'borders'   => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF'.$gold]]],
        ]);

        // ── Anchos de columna
        $widths = ['A'=>28,'B'=>18,'C'=>14,'D'=>22,'E'=>14,'F'=>14,'G'=>18,'H'=>14,'I'=>22,'J'=>14,'K'=>14,'L'=>18,'M'=>14,'N'=>22,'O'=>14,'P'=>14];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // ── Freeze
        $sheet->freezePane('A5');

        // ── Footer
        $sheet->mergeCells('A10:P10');
        $sheet->setCellValue('A10', 'Fiesta Tours Peru © ' . now()->format('Y') . '  ·  Plantilla de importación  ·  www.fiestatoursperu.com');
        $sheet->getRowDimension(10)->setRowHeight(13);
        $sheet->getStyle('A10')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 8, 'color' => ['argb' => 'FF'.$navy]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE8C97A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // ── Descargar
        $writer = new Xlsx($spreadsheet);
        $filename = 'plantilla_clientes_' . now()->format('Ymd') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
