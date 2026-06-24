<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SuppliersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $suppliers;

    public function __construct($suppliers)
    {
        $this->suppliers = $suppliers;
    }

    public function collection()
    {
        return $this->suppliers;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Razón Social',
            'RUC',
            'Teléfono',
            'Email',
            'País',
            'Ciudad',
            'Dirección',
            'Categoría',
            'Contacto Principal',
            'Email Contacto',
            'Teléfono Contacto'
        ];
    }

    public function map($supplier): array
    {
        $principal = $supplier->contacts->where('es_principal', true)->first();
        
        return [
            $supplier->id_supplier,
            $supplier->supplier_name,
            $supplier->business_name ?? '',
            $supplier->tax_code ?? '',
            $supplier->general_phone ?? '',
            $supplier->general_email ?? '',
            $supplier->country_name ?? '',
            $supplier->city_name ?? '',
            $supplier->address ?? '',
            $supplier->category->category_name ?? '',
            $principal ? $principal->name . ' ' . ($principal->last_names ?? '') : '',
            $principal ? $principal->email ?? '' : '',
            $principal ? $principal->first_phone ?? '' : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}