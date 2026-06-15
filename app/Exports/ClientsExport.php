<?php
namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ClientsExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle, WithEvents
{
    const NAVY  = '0B1F3A';
    const GOLD  = 'C9A84C';
    const GOLD2 = 'E8C97A';
    const ROW_ALT = 'F8F5EE';

    private int $totalRows;
    private int $maxContacts; // Máximo número de contactos por cliente

    public function __construct()
    {
        // Calcular el máximo de contactos que tiene un cliente
        $this->maxContacts = Client::withCount('contacts')
            ->get()
            ->max('contacts_count') ?? 1;
    }

    public function title(): string
    {
        return 'Clientes';
    }

    public function collection()
    {
        $clients = Client::with(['contacts' => function($q) {
                $q->orderBy('es_principal', 'desc')->orderBy('created_at');
            }])
            ->withCount('contacts')
            ->orderBy('name_client')
            ->get();

        $this->totalRows = $clients->count();

        return $clients->map(function($client) {
            $row = [
                'id' => $client->id_client,
                'agencia' => $client->name_client,
                'estado' => 'Activo',
                'fecha_registro' => $client->created_at->format('d/m/Y'),
                'total_contactos' => $client->contacts_count,
            ];

            // Agregar cada contacto en columnas
            foreach ($client->contacts as $idx => $contact) {
                $colPrefix = $idx + 1;
                $row["contacto_{$colPrefix}_nombre"] = trim($contact->name . ' ' . ($contact->last_names ?? ''));
                $row["contacto_{$colPrefix}_cargo"] = $contact->qualification ?? '—';
                $row["contacto_{$colPrefix}_email"] = $contact->email ?? '—';
                $row["contacto_{$colPrefix}_telefono"] = $contact->first_phone ?? '—';
                $row["contacto_{$colPrefix}_telefono2"] = $contact->second_phone ?? '—';
            }

            // Rellenar columnas vacías para los que tienen menos contactos
            for ($i = $client->contacts_count + 1; $i <= $this->maxContacts; $i++) {
                $row["contacto_{$i}_nombre"] = '—';
                $row["contacto_{$i}_cargo"] = '—';
                $row["contacto_{$i}_email"] = '—';
                $row["contacto_{$i}_telefono"] = '—';
                $row["contacto_{$i}_telefono2"] = '—';
            }

            return $row;
        });
    }

    public function headings(): array
    {
        $headings = [
            'ID',
            'Agencia / Cliente',
            'Estado',
            'Fecha Registro',
            'Total Contactos',
        ];

        // Generar encabezados dinámicos para cada contacto
        for ($i = 1; $i <= $this->maxContacts; $i++) {
            $headings[] = "Contacto {$i}";
            $headings[] = "Cargo {$i}";
            $headings[] = "Email {$i}";
            $headings[] = "Teléfono {$i}";
            $headings[] = "Teléfono 2 {$i}";
        }

        return $headings;
    }

    public function styles(Worksheet $sheet): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Calcular columnas totales
                $totalColumns = 5 + ($this->maxContacts * 5); // 5 fijas + 5 por cada contacto
                $lastColumn = $this->getColumnLetter($totalColumns);

                $lastRow = $this->totalRows + 4;

                // Insertar 3 filas arriba para el título
                $sheet->insertNewRowBefore(1, 3);

                // FILA 1: Franja dorada
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->setCellValue('A1', '');
                $sheet->getRowDimension(1)->setRowHeight(6);
                $sheet->getStyle("A1:{$lastColumn}1")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB(self::GOLD);

                // FILA 2: Título
                $sheet->mergeCells("A2:{$lastColumn}2");
                $sheet->setCellValue('A2', 'FIESTA TOURS PERU  ·  Listado de Clientes');
                $sheet->getRowDimension(2)->setRowHeight(28);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF' . self::GOLD], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . self::NAVY]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 2],
                ]);

                // FILA 3: Subtítulo
                $sheet->mergeCells("A3:{$lastColumn}3");
                $sheet->setCellValue('A3', 'Generado: ' . now()->format('d/m/Y H:i') . ' hrs  ·  Documento confidencial  ·  Uso interno  ·  www.fiestatoursperu.com');
                $sheet->getRowDimension(3)->setRowHeight(16);
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 8, 'color' => ['argb' => 'FF94A3B8'], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . self::NAVY]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 2],
                ]);

                // FILA 4: Encabezados de tabla
                $headerRow = 4;
                $sheet->getRowDimension($headerRow)->setRowHeight(20);
                $sheet->getStyle("A{$headerRow}:{$lastColumn}{$headerRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF' . self::GOLD], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . self::NAVY]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF' . self::GOLD]]],
                ]);

                // Datos
                $dataStart = $headerRow + 1;
                $dataEnd = $dataStart + $this->totalRows - 1;

                for ($row = $dataStart; $row <= $dataEnd; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(16);
                    $isEven = ($row - $dataStart) % 2 === 0;
                    $bgColor = $isEven ? 'FFFFFFFF' : 'FF' . self::ROW_ALT;

                    $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                        'font' => ['size' => 9, 'name' => 'Arial'],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
                    ]);

                    // Estilo para el nombre de agencia
                    $sheet->getStyle("B{$row}")->getFont()->setBold(true);
                }

                // Total de registros
                $totalsRow = $dataEnd + 1;
                $sheet->mergeCells("A{$totalsRow}:D{$totalsRow}");
                $sheet->setCellValue("A{$totalsRow}", 'TOTAL DE REGISTROS');
                $sheet->setCellValue("E{$totalsRow}", $this->totalRows);
                $sheet->getRowDimension($totalsRow)->setRowHeight(18);
                $sheet->getStyle("A{$totalsRow}:{$lastColumn}{$totalsRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF' . self::NAVY]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFF8E7']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF' . self::GOLD]]],
                ]);

                // Footer
                $bottomRow = $totalsRow + 1;
                $sheet->mergeCells("A{$bottomRow}:{$lastColumn}{$bottomRow}");
                $sheet->setCellValue("A{$bottomRow}", 'Fiesta Tours Peru © ' . now()->format('Y') . '  ·  Lima, Perú  ·  Sistema de Gestión Interna');
                $sheet->getRowDimension($bottomRow)->setRowHeight(14);
                $sheet->getStyle("A{$bottomRow}")->applyFromArray([
                    'font' => ['italic' => true, 'size' => 8, 'color' => ['argb' => 'FF' . self::NAVY]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . self::GOLD2]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // Anchos de columna
                $sheet->getColumnDimension('A')->setWidth(6);
                $sheet->getColumnDimension('B')->setWidth(25);
                $sheet->getColumnDimension('C')->setWidth(10);
                $sheet->getColumnDimension('D')->setWidth(14);
                $sheet->getColumnDimension('E')->setWidth(12);

                // Freeze pane
                $sheet->freezePane("A{$dataStart}");
            },
        ];
    }

    private function getColumnLetter($index)
    {
        $letter = '';
        while ($index > 0) {
            $index--;
            $letter = chr(65 + ($index % 26)) . $letter;
            $index = intdiv($index, 26);
        }
        return $letter;
    }
}
