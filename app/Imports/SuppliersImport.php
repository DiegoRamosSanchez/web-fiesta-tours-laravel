<?php

namespace App\Imports;

use App\Models\Supplier;
use App\Models\CategorySupplier;
use App\Models\Contact;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SuppliersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsOnError
{
    use SkipsFailures, SkipsErrors;

    public $imported = 0;
    public $skipped = 0;
    public $errors = [];

    public function model(array $row)
    {
        try {
            DB::beginTransaction();

            // Buscar o crear categoría
            $categoryId = null;
            if (!empty($row['category_name'])) {
                $category = CategorySupplier::firstOrCreate(
                    ['category_name' => $row['category_name']]
                );
                $categoryId = $category->id_categories_suppliers;
            }

            // Crear proveedor
            $supplier = Supplier::create([
                'supplier_name' => $row['supplier_name'],
                'business_name' => $row['business_name'] ?? null,
                'tax_code' => $row['tax_code'] ?? null,
                'general_phone' => $row['general_phone'] ?? null,
                'general_email' => $row['general_email'] ?? null,
                'country_name' => $row['country_name'] ?? null,
                'city_name' => $row['city_name'] ?? null,
                'address' => $row['address'] ?? null,
                'id_categories_suppliers' => $categoryId,
            ]);

            // Crear contacto principal si existe
            if (!empty($row['contact_name'])) {
                Contact::create([
                    'id_supplier' => $supplier->id_supplier,
                    'id_client' => null,
                    'name' => $row['contact_name'],
                    'last_names' => $row['contact_last_names'] ?? null,
                    'email' => $row['contact_email'] ?? null,
                    'qualification' => $row['contact_qualification'] ?? null,
                    'first_phone' => $row['contact_first_phone'] ?? null,
                    'second_phone' => $row['contact_second_phone'] ?? null,
                    'es_principal' => true,
                    'Date_of_birth' => null,
                ]);
            }

            DB::commit();
            $this->imported++;

            return $supplier;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error importing supplier: ' . $e->getMessage());
            $this->errors[] = "Fila con nombre '{$row['supplier_name']}': " . $e->getMessage();
            $this->skipped++;
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'supplier_name' => 'required|string|max:100',
            'business_name' => 'nullable|string|max:150',
            'tax_code' => 'nullable|string|max:20',
            'general_phone' => 'nullable|string|max:20',
            'general_email' => 'nullable|email|max:120',
            'country_name' => 'nullable|string|max:100',
            'city_name' => 'nullable|string|max:150',
            'address' => 'nullable|string|max:255',
            'category_name' => 'nullable|string|max:100',
            'contact_name' => 'nullable|string|max:100',
            'contact_last_names' => 'nullable|string|max:100',
            'contact_email' => 'nullable|email|max:120',
            'contact_qualification' => 'nullable|string|max:100',
            'contact_first_phone' => 'nullable|string|max:20',
            'contact_second_phone' => 'nullable|string|max:20',
        ];
    }

    public function onFailure(\Maatwebsite\Excel\Validators\Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = "Fila {$failure->row()}: " . implode(', ', $failure->errors());
            $this->skipped++;
        }
    }
}