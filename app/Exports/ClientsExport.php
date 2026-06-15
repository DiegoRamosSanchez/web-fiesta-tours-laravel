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
    // Colores Fiesta Tours
    const NAVY  = '0B1F3A'; // Azul marino
    const GOLD  = 'C9A84C'; // Dorado
    const GOLD2 = 'E8C97A'; // Dorado claro
    const ROW_ALT = 'F8F5EE'; // Fondo alterno cálido

    private int $totalRows;

    public function title(): string
    {
        return 'Clientes';
    }

    public function collection()
    {
        $clients = Client::with(['contacts' => fn($q) => $q->where('es_principal', true)])
            ->withCount('contacts')
            ->orderBy('name_client')
            ->get();

        $this->totalRows = $clients->count();

        return $clients->map(fn($c) => [
            '#'                  => $c->id_client,
            'Agencia / Cliente'  => $c->name_client,
            'Contacto Principal' => $c->contacts->first()
                ? trim($c->contacts->first()->name . ' ' . $c->contacts->first()->last_names)
                : '—',
            'Cargo'              => $c->contacts->first()?->qualification ?? '—',
            'Email'              => $c->contacts->first()?->email ?? '—',
            'Teléfono Principal' => $c->contacts->first()?->first_phone ?? '—',
            'Teléfono Secundario'=> $c->contacts->first()?->second_phone ?? '—',
            'Total Contactos'    => $c->contacts_count,
            'Estado'             => 'Activo',
            'Fecha Registro'     => $c->created_at->format('d/m/Y'),
        ]);
    }

    public function headings(): array
    {
        return ['#', 'Agencia / Cliente', 'Contacto Principal', 'Cargo',
                'Email', 'Teléfono Principal', 'Teléfono Secundario',
                'Total Contactos', 'Estado', 'Fecha Registro'];
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
                $lastRow = $this->totalRows + 4; // título(1) + subtítulo(2) + encabezado(3) + datos

                // ── Insertar 3 filas arriba para el bloque de título ──
                $sheet->insertNewRowBefore(1, 3);

                // ── FILA 1: Franja dorada superior ──
                $sheet->mergeCells('A1:J1');
                $sheet->setCellValue('A1', '');
                $sheet->getRowDimension(1)->setRowHeight(6);
                $sheet->getStyle('A1:J1')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB(self::GOLD);

                // ── FILA 2: Título principal ──
                $sheet->mergeCells('A2:J2');
                $sheet->setCellValue('A2', 'FIESTA TOURS PERU  ·  Listado de Clientes');
                $sheet->getRowDimension(2)->setRowHeight(28);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'size'  => 14,
                        'color' => ['argb' => 'FF' . self::GOLD],
                        'name'  => 'Arial',
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF' . self::NAVY],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'indent'     => 2,
                    ],
                ]);

                // ── FILA 3: Subtítulo / metadata ──
                $sheet->mergeCells('A3:J3');
                $sheet->setCellValue('A3', 'Generado: ' . now()->format('d/m/Y H:i') . ' hrs  ·  Documento confidencial  ·  Uso interno  ·  www.fiestatoursperu.com');
                $sheet->getRowDimension(3)->setRowHeight(16);
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'size'   => 8,
                        'color'  => ['argb' => 'FF94A3B8'],
                        'name'   => 'Arial',
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF' . self::NAVY],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'indent'     => 2,
                    ],
                ]);

                // ── FILA 4: Encabezados de tabla ──
                $headerRow = 4;
                $sheet->getRowDimension($headerRow)->setRowHeight(20);
                $sheet->getStyle("A{$headerRow}:J{$headerRow}")->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'size'  => 9,
                        'color' => ['argb' => 'FF' . self::GOLD],
                        'name'  => 'Arial',
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF' . self::NAVY],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color'       => ['argb' => 'FF' . self::GOLD],
                        ],
                    ],
                ]);

                // ── FILAS DE DATOS: alternado ──
                $dataStart = $headerRow + 1;
                $dataEnd   = $dataStart + $this->totalRows - 1;

                for ($row = $dataStart; $row <= $dataEnd; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(16);
                    $isEven = ($row - $dataStart) % 2 === 0;
                    $bgColor = $isEven ? 'FFFFFFFF' : 'FF' . self::ROW_ALT;

                    $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                        'font' => ['size' => 9, 'name' => 'Arial'],
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['argb' => $bgColor],
                        ],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        'borders'   => [
                            'bottom' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color'       => ['argb' => 'FFE2E8F0'],
                            ],
                        ],
                    ]);

                    // ID centrado y gris
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'font'      => ['color' => ['argb' => 'FF94A3B8'], 'bold' => true, 'size' => 8],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);

                    // Nombre agencia bold
                    $sheet->getStyle("B{$row}")->getFont()->setBold(true)->setSize(9);
                    $sheet->getStyle("B{$row}")->getFont()->getColor()->setRGB(self::NAVY);

                    // Total contactos centrado
                    $sheet->getStyle("H{$row}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Estado: badge verde
                    $sheet->getStyle("I{$row}")->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 8, 'color' => ['argb' => 'FF166534']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);

                    // Fecha centrada
                    $sheet->getStyle("J{$row}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // ── LÍNEA DORADA inferior de tabla ──
                if ($this->totalRows > 0) {
                    $sheet->getStyle("A{$dataEnd}:J{$dataEnd}")->getBorders()
                        ->getBottom()->setBorderStyle(Border::BORDER_MEDIUM)
                        ->getColor()->setRGB(self::GOLD);
                }

                // ── FILA TOTALES ──
                $totalsRow = $dataEnd + 1;
                $sheet->mergeCells("A{$totalsRow}:G{$totalsRow}");
                $sheet->setCellValue("A{$totalsRow}", 'TOTAL DE REGISTROS');
                $sheet->setCellValue("H{$totalsRow}", $this->totalRows);
                $sheet->getRowDimension($totalsRow)->setRowHeight(18);
                $sheet->getStyle("A{$totalsRow}:J{$totalsRow}")->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'size'  => 9,
                        'color' => ['argb' => 'FF' . self::NAVY],
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFFF8E7'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'top' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color'       => ['argb' => 'FF' . self::GOLD],
                        ],
                    ],
                ]);
                $sheet->getStyle("A{$totalsRow}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                    ->setIndent(2);
                $sheet->getStyle("H{$totalsRow}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // ── FRANJA DORADA INFERIOR ──
                $bottomRow = $totalsRow + 1;
                $sheet->mergeCells("A{$bottomRow}:J{$bottomRow}");
                $sheet->setCellValue("A{$bottomRow}", 'Fiesta Tours Peru © ' . now()->format('Y') . '  ·  Lima, Perú  ·  Sistema de Gestión Interna');
                $sheet->getRowDimension($bottomRow)->setRowHeight(14);
                $sheet->getStyle("A{$bottomRow}")->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'size'   => 8,
                        'color'  => ['argb' => 'FF' . self::NAVY],
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF' . self::GOLD2],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // ── Anchos manuales para columnas con contenido fijo ──
                $sheet->getColumnDimension('A')->setWidth(6);
                $sheet->getColumnDimension('H')->setWidth(14);
                $sheet->getColumnDimension('I')->setWidth(10);
                $sheet->getColumnDimension('J')->setWidth(14);

                // ── Freeze pane debajo de encabezados ──
                $sheet->freezePane("A{$dataStart}");

                // ── Nombre de pestaña ──
                $event->sheet->getTitle();
            },
        ];
    }
}
