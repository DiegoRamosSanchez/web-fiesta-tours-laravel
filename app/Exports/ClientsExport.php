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
    const NAVY    = '0B1F3A';
    const GOLD    = 'C9A84C';
    const GOLD2   = 'E8C97A';
    const ROW_ALT = 'F8F5EE';

    // 12 columnas fijas: ID, Agencia, Razón Social, Código Tributario, Teléfono General,
    // Email General, País, Ciudad, Dirección, Estado, Fecha Registro, Total Contactos
    const FIXED_COLUMNS = 12;

    private int $totalRows;
    private int $maxContacts;

    public function __construct()
    {
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
        $clients = Client::with(['contacts' => function ($q) {
            $q->orderBy('es_principal', 'desc')->orderBy('created_at');
        }])
            ->withCount('contacts')
            ->orderBy('name_client')
            ->get();

        $this->totalRows = $clients->count();

        return $clients->map(function ($client) {
            $row = [
                'id'             => $client->id_client,
                'agencia'        => $client->name_client,
                'business_name'  => $client->business_name  ?? '',
                'tax_code'       => $client->tax_code       ?? '',
                'general_phone'  => $client->general_phone  ?? '',
                'general_email'  => $client->general_email  ?? '',
                'country_name'   => $client->country_name   ?? '',
                'city_name'      => $client->city_name      ?? '',
                'address'        => $client->address        ?? '',
                'estado'         => 'Activo',
                'fecha_registro' => $client->created_at->format('d/m/Y'),
                'total_contactos'=> $client->contacts_count,
            ];

            foreach ($client->contacts as $idx => $contact) {
                $n = $idx + 1;
                $row["contacto_{$n}__nombre"]   = $contact->name ?? '';
                $row["contacto_{$n}_last_names"]= $contact->last_names ?? '';
                $row["contacto_{$n}_cargo"]     = $contact->qualification ?? '';
                $row["contacto_{$n}_email"]     = $contact->email         ?? '';
                $row["contacto_{$n}_telefono"]  = $contact->first_phone   ?? '';
                $row["contacto_{$n}_telefono2"] = $contact->second_phone  ?? '';
            }

            // Rellenar columnas vacías
            for ($i = $client->contacts_count + 1; $i <= $this->maxContacts; $i++) {
                $row["contacto_{$i}_nombre"]    = '';
                $row["contacto_{$i}_last_names"]= '';
                $row["contacto_{$i}_cargo"]     = '';
                $row["contacto_{$i}_email"]     = '';
                $row["contacto_{$i}_telefono"]  = '';
                $row["contacto_{$i}_telefono2"] = '';
            }

            return $row;
        });
    }

    public function headings(): array
    {
        $headings = [
            'ID',
            'Agencia / Cliente',
            'Razón Social',
            'Código Tributario',
            'Teléfono General',
            'Email General',
            'País',
            'Ciudad',
            'Dirección',
            'Estado',
            'Fecha Registro',
            'Total Contactos',
        ];

        for ($i = 1; $i <= $this->maxContacts; $i++) {
            $headings[] = "Contacto {$i}";
            $headings[] = "Apellidos {$i}";
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

                // 12 columnas fijas + 6 por contacto
                $totalColumns = self::FIXED_COLUMNS + ($this->maxContacts * 6);
                $lastColumn   = $this->getColumnLetter($totalColumns);
                $lastRow      = $this->totalRows + 4;

                $sheet->insertNewRowBefore(1, 3);

                // FILA 1: Franja dorada
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->getRowDimension(1)->setRowHeight(6);
                $sheet->getStyle("A1:{$lastColumn}1")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB(self::GOLD);

                // FILA 2: Título
                $sheet->mergeCells("A2:{$lastColumn}2");
                $sheet->setCellValue('A2', 'FIESTA TOURS PERU  ·  Listado de Clientes');
                $sheet->getRowDimension(2)->setRowHeight(28);
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF'.self::GOLD], 'name' => 'Arial'],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF'.self::NAVY]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 2],
                ]);

                // FILA 3: Subtítulo
                $sheet->mergeCells("A3:{$lastColumn}3");
                $sheet->setCellValue('A3', 'Generado: '.now()->format('d/m/Y H:i').' hrs  ·  Documento confidencial  ·  Uso interno  ·  www.fiestatoursperu.com');
                $sheet->getRowDimension(3)->setRowHeight(16);
                $sheet->getStyle('A3')->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 8, 'color' => ['argb' => 'FF94A3B8'], 'name' => 'Arial'],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF'.self::NAVY]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 2],
                ]);

                // FILA 4: Encabezados
                $headerRow = 4;
                $sheet->getRowDimension($headerRow)->setRowHeight(20);
                $sheet->getStyle("A{$headerRow}:{$lastColumn}{$headerRow}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF'.self::GOLD], 'name' => 'Arial'],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF'.self::NAVY]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF'.self::GOLD]]],
                ]);

                // Datos
                $dataStart = $headerRow + 1;
                $dataEnd   = $dataStart + $this->totalRows - 1;

                for ($row = $dataStart; $row <= $dataEnd; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(16);
                    $bgColor = ($row - $dataStart) % 2 === 0 ? 'FFFFFFFF' : 'FF'.self::ROW_ALT;

                    $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                        'font'      => ['size' => 9, 'name' => 'Arial'],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        'borders'   => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
                    ]);

                    $sheet->getStyle("B{$row}")->getFont()->setBold(true);
                }

                // Total de registros
                $totalsRow = $dataEnd + 1;
                $sheet->mergeCells("A{$totalsRow}:D{$totalsRow}");
                $sheet->setCellValue("A{$totalsRow}", 'TOTAL DE REGISTROS');
                $sheet->setCellValue("E{$totalsRow}", $this->totalRows);
                $sheet->getRowDimension($totalsRow)->setRowHeight(18);
                $sheet->getStyle("A{$totalsRow}:{$lastColumn}{$totalsRow}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF'.self::NAVY]],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFF8E7']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF'.self::GOLD]]],
                ]);

                // Footer
                $bottomRow = $totalsRow + 1;
                $sheet->mergeCells("A{$bottomRow}:{$lastColumn}{$bottomRow}");
                $sheet->setCellValue("A{$bottomRow}", 'Fiesta Tours Peru © '.now()->format('Y').'  ·  Lima, Perú  ·  Sistema de Gestión Interna');
                $sheet->getRowDimension($bottomRow)->setRowHeight(14);
                $sheet->getStyle("A{$bottomRow}")->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 8, 'color' => ['argb' => 'FF'.self::NAVY]],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF'.self::GOLD2]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // Anchos de columnas fijas (12 columnas: ID, Agencia, Razón Social, Cód.Tributario,
                // Tel.General, Email General, País, Ciudad, Dirección, Estado, Fecha, Total Contactos)
                $fixedWidths = [
                    'A' => 6,  'B' => 25, 'C' => 22, 'D' => 16,
                    'E' => 16, 'F' => 22, 'G' => 16, 'H' => 18,
                    'I' => 30, 'J' => 10, 'K' => 14, 'L' => 10,
                ];
                foreach ($fixedWidths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                // 6 columnas por contacto: Nombre, Apellidos, Cargo, Email, Teléfono, Teléfono 2
                $contactColWidths = [20, 20, 14, 22, 14, 14];
                $startColIndex = self::FIXED_COLUMNS + 1;
                for ($i = 0; $i < $this->maxContacts; $i++) {
                    foreach ($contactColWidths as $j => $w) {
                        $colLetter = $this->getColumnLetter($startColIndex + ($i * 6) + $j);
                        $sheet->getColumnDimension($colLetter)->setWidth($w);
                    }
                }

                $sheet->freezePane("A{$dataStart}");
            },
        ];
    }

    private function getColumnLetter(int $index): string
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
