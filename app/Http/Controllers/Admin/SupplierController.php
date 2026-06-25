<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SuppliersExport;
use App\Http\Controllers\Controller;
use App\Imports\SuppliersImport;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\CategorySupplier;
use App\Models\Contact;
use App\Models\Destination;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
    // En SupplierController.php, modifica el método index:

    public function index(Request $request)
    {
        $search = $request->input('search');
        $country = $request->input('country');
        $category = $request->input('category');
        $sort = $request->input('sort', 'newest');

        // Obtener países y categorías para los filtros
        $countries = Supplier::whereNotNull('country_name')
            ->distinct()
            ->pluck('country_name')
            ->filter()
            ->values()
            ->toArray();

        $categories = CategorySupplier::orderBy('category_name')
            ->pluck('category_name', 'id_categories_suppliers')
            ->toArray();

        $suppliers = Supplier::with(['destination', 'category', 'bankAccounts.bank', 'contacts'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('supplier_name', 'like', "%{$search}%")
                        ->orWhere('business_name', 'like', "%{$search}%")
                        ->orWhere('tax_code', 'like', "%{$search}%")
                        ->orWhere('general_email', 'like', "%{$search}%")
                        ->orWhere('general_phone', 'like', "%{$search}%")
                        ->orWhere('country_name', 'like', "%{$search}%")
                        ->orWhere('city_name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhereHas('destination', function ($dq) use ($search) {
                            $dq->where('destination_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('category', function ($cq) use ($search) {
                            $cq->where('category_name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($country, function ($query, $country) {
                $query->where('country_name', $country);
            })
            ->when($category, function ($query, $category) {
                $query->where('id_categories_suppliers', $category);
            })
            ->when($sort, function ($query, $sort) {
                switch ($sort) {
                    case 'newest':
                        $query->orderBy('created_at', 'desc');
                        break;
                    case 'oldest':
                        $query->orderBy('created_at', 'asc');
                        break;
                    case 'az':
                        $query->orderBy('supplier_name', 'asc');
                        break;
                    case 'za':
                        $query->orderBy('supplier_name', 'desc');
                        break;
                    case 'tax-az':
                        $query->orderBy('tax_code', 'asc');
                        break;
                    case 'tax-za':
                        $query->orderBy('tax_code', 'desc');
                        break;
                    default:
                        $query->orderBy('created_at', 'desc');
                }
            })
            ->paginate(10)
            ->withQueryString();

        return view('admin.suppliers.index', compact('suppliers', 'search', 'country', 'category', 'sort', 'countries', 'categories'));
    }

    public function create()
    {
        $destinations = Destination::orderBy('destination_name')->get();
        $categories = CategorySupplier::orderBy('category_name')->get();
        $banks = Bank::orderBy('bank_name')->get();

        return view('admin.suppliers.create', compact('destinations', 'categories', 'banks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:100',
            'business_name' => 'nullable|string|max:150',
            'tax_code' => 'nullable|string|max:20',
            'general_phone' => 'nullable|string|max:20',
            'general_email' => 'nullable|email|max:120',
            'id_destinations' => 'nullable|exists:destinations,id_destinations',
            'id_categories_suppliers' => 'nullable|exists:categories_suppliers,id_categories_suppliers',
            'new_destination_name' => 'nullable|string|max:100',
            'new_category_name' => 'nullable|string|max:100',

            // Validaciones para cuentas bancarias
            'bank_accounts' => 'nullable|array',
            'bank_accounts.*.id_bank' => 'nullable|exists:bank,id_bank',
            'bank_accounts.*.account_number' => 'nullable|string|max:100',
            'bank_accounts.*.cci' => 'nullable|string|max:100',
            'bank_accounts.*.currency' => 'nullable|string|max:40',
            'new_bank_name' => 'nullable|string|max:50',
            'new_bank_account_number' => 'nullable|string|max:100',
            'new_bank_cci' => 'nullable|string|max:100',
            'new_bank_currency' => 'nullable|string|max:40',

            'contacts' => 'nullable|array',
            'contacts.*.name' => 'required_with:contacts|string|max:100',
            'contacts.*.last_names' => 'nullable|string|max:100',
            'contacts.*.email' => 'nullable|email|max:120',
            'contacts.*.qualification' => 'nullable|string|max:100',
            'contacts.*.first_phone' => 'nullable|string|max:20',
            'contacts.*.second_phone' => 'nullable|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            // Crear destino al vuelo si se escribió uno nuevo
            $destinationId = $request->id_destinations;
            if ($request->filled('new_destination_name')) {
                $dest = Destination::create(['destination_name' => $request->new_destination_name]);
                $destinationId = $dest->id_destinations;
            }

            // Crear categoría al vuelo si se escribió una nueva
            $categoryId = $request->id_categories_suppliers;
            if ($request->filled('new_category_name')) {
                $cat = CategorySupplier::create(['category_name' => $request->new_category_name]);
                $categoryId = $cat->id_categories_suppliers;
            }

            // Crear el proveedor
            $supplier = Supplier::create([
                'supplier_name' => $request->supplier_name,
                'business_name' => $request->business_name,
                'tax_code' => $request->tax_code,
                'general_phone' => $request->general_phone,
                'general_email' => $request->general_email,
                'country_name' => $request->country_name,
                'city_name' => $request->city_name,
                'address' => $request->address,
                'id_destinations' => $destinationId ?: null,
                'id_categories_suppliers' => $categoryId ?: null,
            ]);

            // ── PROCESAR CONTACTOS ──
            if ($request->has('contacts') && is_array($request->contacts)) {
                $first = true; // Para marcar el primer contacto como principal
                foreach ($request->contacts as $contactData) {
                    // Solo crear si tiene nombre (requerido)
                    if (! empty($contactData['name'])) {
                        Contact::create([
                            'id_supplier' => $supplier->id_supplier,
                            'id_client' => null,
                            'name' => $contactData['name'],
                            'last_names' => $contactData['last_names'] ?? null,
                            'email' => $contactData['email'] ?? null,
                            'qualification' => $contactData['qualification'] ?? null,
                            'first_phone' => $contactData['first_phone'] ?? null,
                            'second_phone' => $contactData['second_phone'] ?? null,
                            'es_principal' => $first,
                            'Date_of_birth' => null,
                        ]);
                        $first = false;
                    }
                }
            }

            // Procesar cuentas bancarias existentes
            if ($request->has('bank_accounts')) {
                foreach ($request->bank_accounts as $account) {
                    // Solo crear si tiene banco Y número de cuenta (ambos son obligatorios para una cuenta válida)
                    if (! empty($account['id_bank']) && ! empty($account['account_number'])) {
                        BankAccount::create([
                            'id_supplier' => $supplier->id_supplier,
                            'id_bank' => $account['id_bank'],
                            'account_number' => $account['account_number'],
                            'cci' => $account['cci'] ?? null,
                            'currency' => $account['currency'] ?? null,
                        ]);
                    }
                }
            }

            // Procesar nuevo banco y cuenta bancaria
            if ($request->filled('new_bank_name') && $request->filled('new_bank_account_number')) {
                // Crear el nuevo banco
                $bank = Bank::create(['bank_name' => $request->new_bank_name]);

                // Crear la cuenta bancaria asociada
                BankAccount::create([
                    'id_supplier' => $supplier->id_supplier,
                    'id_bank' => $bank->id_bank,
                    'account_number' => $request->new_bank_account_number,
                    'cci' => $request->new_bank_cci,
                    'currency' => $request->new_bank_currency,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.suppliers.index')
                ->with('success', 'Proveedor, contactos y cuentas bancarias creados correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error al crear el proveedor: '.$e->getMessage())
                ->withInput();
        }
    }

    public function edit(Supplier $supplier)
    {
        $destinations = Destination::orderBy('destination_name')->get();
        $categories = CategorySupplier::orderBy('category_name')->get();
        $banks = Bank::orderBy('bank_name')->get();
        $supplier->load('bankAccounts.bank');

        // Preparar datos de contactos para JavaScript
        $contactsData = $supplier->contacts->map(function ($c) {
            return [
                'id' => $c->id_contacts,
                'name' => $c->name,
                'lastnames' => $c->last_names,
                'email' => $c->email,
                'qualification' => $c->qualification,
                'phone1' => $c->first_phone,
                'phone2' => $c->second_phone,
                'principal' => (bool) $c->es_principal,
            ];
        })->values();

        return view('admin.suppliers.edit', compact('supplier', 'destinations', 'categories', 'banks', 'contactsData'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:100',
            'business_name' => 'nullable|string|max:150',
            'tax_code' => 'nullable|string|max:20',
            'general_phone' => 'nullable|string|max:20',
            'general_email' => 'nullable|email|max:120',
            'country_name' => 'nullable|string|max:100',
            'city_name' => 'nullable|string|max:150',
            'address' => 'nullable|string|max:255',
            'id_destinations' => 'nullable|exists:destinations,id_destinations',
            'id_categories_suppliers' => 'nullable|exists:categories_suppliers,id_categories_suppliers',
            'new_destination_name' => 'nullable|string|max:100',
            'new_category_name' => 'nullable|string|max:100',
            'bank_accounts' => 'nullable|array',
            'bank_accounts.*.id_bank' => 'nullable|exists:bank,id_bank',
            'bank_accounts.*.account_number' => 'nullable|string|max:100',
            'bank_accounts.*.cci' => 'nullable|string|max:100',
            'bank_accounts.*.currency' => 'nullable|string|max:40',
            'new_bank_name' => 'nullable|string|max:50',
            'new_bank_account_number' => 'nullable|string|max:100',
            'new_bank_cci' => 'nullable|string|max:100',
            'new_bank_currency' => 'nullable|string|max:40',
            'delete_bank_accounts' => 'nullable|array',
            'delete_bank_accounts.*' => 'exists:bank_account,id_bank_account',
            'contacts' => 'nullable|array',
            'contacts.*.id' => 'nullable|integer',
            'contacts.*.name' => 'required_with:contacts|string|max:100',
            'contacts.*.last_names' => 'nullable|string|max:100',
            'contacts.*.email' => 'nullable|email|max:120',
            'contacts.*.qualification' => 'nullable|string|max:100',
            'contacts.*.first_phone' => 'nullable|string|max:20',
            'contacts.*.second_phone' => 'nullable|string|max:20',
            'delete_contacts' => 'nullable|array',
            'delete_contacts.*' => 'integer|exists:contacts,id_contacts',
        ]);

        try {
            DB::beginTransaction();

            // Crear destino al vuelo
            $destinationId = $request->id_destinations;
            if ($request->filled('new_destination_name')) {
                $dest = Destination::create(['destination_name' => $request->new_destination_name]);
                $destinationId = $dest->id_destinations;
            }

            // Crear categoría al vuelo
            $categoryId = $request->id_categories_suppliers;
            if ($request->filled('new_category_name')) {
                $cat = CategorySupplier::create(['category_name' => $request->new_category_name]);
                $categoryId = $cat->id_categories_suppliers;
            }

            // Actualizar proveedor
            $supplier->update([
                'supplier_name' => $request->supplier_name,
                'business_name' => $request->business_name,
                'tax_code' => $request->tax_code,
                'general_phone' => $request->general_phone,
                'general_email' => $request->general_email,
                'country_name' => $request->country_name,
                'city_name' => $request->city_name,
                'address' => $request->address,
                'id_destinations' => $destinationId ?: null,
                'id_categories_suppliers' => $categoryId ?: null,
            ]);

            // ── PROCESAR CONTACTOS ──
            // 1. ELIMINAR contactos marcados
            if ($request->has('delete_contacts') && ! empty($request->delete_contacts)) {
                $deleted = Contact::whereIn('id_contacts', $request->delete_contacts)
                    ->where('id_supplier', $supplier->id_supplier)
                    ->delete();
            }

            // 2. ACTUALIZAR o CREAR contactos
            if ($request->has('contacts')) {
                // Obtener lista de IDs a eliminar para evitar actualizarlos
                $deleteIds = $request->input('delete_contacts', []);

                foreach ($request->contacts as $contactData) {
                    // Saltar si no tiene nombre
                    if (empty($contactData['name'])) {
                        continue;
                    }

                    // Si tiene ID, actualizar contacto existente
                    if (isset($contactData['id']) && ! empty($contactData['id'])) {
                        // Verificar que NO esté marcado para eliminar
                        if (in_array($contactData['id'], $deleteIds)) {
                            continue; // Saltar este contacto porque será eliminado
                        }

                        $contact = Contact::where('id_contacts', $contactData['id'])
                            ->where('id_supplier', $supplier->id_supplier)
                            ->first();

                        if ($contact) {
                            $contact->update([
                                'name' => $contactData['name'],
                                'last_names' => $contactData['last_names'] ?? null,
                                'email' => $contactData['email'] ?? null,
                                'qualification' => $contactData['qualification'] ?? null,
                                'first_phone' => $contactData['first_phone'] ?? null,
                                'second_phone' => $contactData['second_phone'] ?? null,
                                'es_principal' => isset($contactData['es_principal']) && $contactData['es_principal'] == 1,
                            ]);
                        }
                    } else {
                        // Crear nuevo contacto
                        Contact::create([
                            'id_supplier' => $supplier->id_supplier,
                            'id_client' => null,
                            'name' => $contactData['name'],
                            'last_names' => $contactData['last_names'] ?? null,
                            'email' => $contactData['email'] ?? null,
                            'qualification' => $contactData['qualification'] ?? null,
                            'first_phone' => $contactData['first_phone'] ?? null,
                            'second_phone' => $contactData['second_phone'] ?? null,
                            'es_principal' => isset($contactData['es_principal']) && $contactData['es_principal'] == 1,
                            'Date_of_birth' => null,
                        ]);
                    }
                }
            }

            // ── PROCESAR CUENTAS BANCARIAS ──
            if ($request->has('delete_bank_accounts')) {
                BankAccount::whereIn('id_bank_account', $request->delete_bank_accounts)->delete();
            }

            if ($request->has('bank_accounts')) {
                foreach ($request->bank_accounts as $account) {
                    if (empty($account['id_bank']) || empty($account['account_number'])) {
                        continue;
                    }

                    if (isset($account['id_bank_account']) && ! empty($account['id_bank_account'])) {
                        $bankAccount = BankAccount::find($account['id_bank_account']);
                        if ($bankAccount) {
                            $bankAccount->update([
                                'id_bank' => $account['id_bank'],
                                'account_number' => $account['account_number'],
                                'cci' => $account['cci'] ?? null,
                                'currency' => $account['currency'] ?? null,
                            ]);
                        }
                    } else {
                        BankAccount::create([
                            'id_supplier' => $supplier->id_supplier,
                            'id_bank' => $account['id_bank'],
                            'account_number' => $account['account_number'],
                            'cci' => $account['cci'] ?? null,
                            'currency' => $account['currency'] ?? null,
                        ]);
                    }
                }
            }

            if ($request->filled('new_bank_name') && $request->filled('new_bank_account_number')) {
                $bank = Bank::create(['bank_name' => $request->new_bank_name]);
                BankAccount::create([
                    'id_supplier' => $supplier->id_supplier,
                    'id_bank' => $bank->id_bank,
                    'account_number' => $request->new_bank_account_number,
                    'cci' => $request->new_bank_cci,
                    'currency' => $request->new_bank_currency,
                ]);
            }

            // ── ASEGURAR QUE SOLO UN CONTACTO SEA PRINCIPAL ──
            $principales = Contact::where('id_supplier', $supplier->id_supplier)
                ->where('es_principal', true)
                ->get();

            if ($principales->count() > 1) {
                $principales->skip(1)->each(function ($contact) {
                    $contact->update(['es_principal' => false]);
                });
            }

            DB::commit();

            return redirect()->route('admin.suppliers.index')
                ->with('success', 'Proveedor actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error al actualizar el proveedor: '.$e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Supplier $supplier)
    {
        try {
            DB::beginTransaction();

            $supplier->delete();

            DB::commit();

            return back()->with('success', 'Proveedor eliminado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error al eliminar el proveedor: '.$e->getMessage());
        }
    }

    public function exportPdfAll()
    {
        $suppliers = Supplier::with(['destination', 'category', 'contacts', 'bankAccounts.bank'])
            ->orderBy('supplier_name')
            ->get();

        $pdf = Pdf::loadView('admin.suppliers.pdf', compact('suppliers'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'dpi' => 96,
                'isPhpEnabled' => true,
                'encoding' => 'UTF-8',
            ]);

        $filename = 'proveedores_'.now()->format('Ymd').'.pdf';

        return $pdf->stream($filename);
    }

    public function exportPdf(Request $request, ?Supplier $supplier = null)
    {
        if ($request->has('supplier_id') && ! empty($request->supplier_id)) {
            $supplier = Supplier::with(['destination', 'category', 'contacts', 'bankAccounts.bank'])
                ->find($request->supplier_id);

            if (! $supplier) {
                abort(404, 'Proveedor no encontrado');
            }

            $suppliers = collect([$supplier]);
            $filename = 'proveedor_'.str($supplier->supplier_name)->slug().'.pdf';
        } elseif ($supplier) {
            $supplier->load(['destination', 'category', 'contacts', 'bankAccounts.bank']);
            $suppliers = collect([$supplier]);
            $filename = 'proveedor_'.str($supplier->supplier_name)->slug().'.pdf';
        } else {
            $suppliers = Supplier::with(['destination', 'category', 'contacts', 'bankAccounts.bank'])
                ->orderBy('supplier_name')
                ->get();
            $filename = 'proveedores_'.now()->format('Ymd').'.pdf';
        }

        $pdf = Pdf::loadView('admin.suppliers.pdf', compact('suppliers'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'dpi' => 96,
                'isPhpEnabled' => true,
                'encoding' => 'UTF-8',
            ]);

        return $pdf->download($filename);
    }

    public function showBankAccounts(Supplier $supplier)
    {
        $supplier->load('bankAccounts.bank');

        return view('admin.suppliers.bank_accounts', compact('supplier'));
    }

    public function exportExcel(Request $request)
    {
        if ($request->has('supplier_id') && ! empty($request->supplier_id)) {
            $supplier = Supplier::with(['destination', 'category', 'contacts', 'bankAccounts.bank'])
                ->find($request->supplier_id);
            if (! $supplier) {
                return back()->with('error', 'Proveedor no encontrado');
            }
            $suppliers = collect([$supplier]);
            $filename = 'proveedor_'.str($supplier->supplier_name)->slug().'_'.now()->format('Ymd').'.xlsx';
        } else {
            $suppliers = Supplier::with(['destination', 'category', 'contacts', 'bankAccounts.bank'])
                ->orderBy('supplier_name')
                ->get();
            $filename = 'proveedores_'.now()->format('Ymd').'.xlsx';
        }

        return Excel::download(new SuppliersExport($suppliers), $filename);
    }

    public function importView()
    {
        return view('admin.suppliers.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'archivo.required' => 'Debes seleccionar un archivo.',
            'archivo.mimes' => 'Solo se aceptan archivos .xlsx, .xls o .csv.',
            'archivo.max' => 'El archivo no puede superar los 5MB.',
        ]);

        $import = new SuppliersImport;

        try {
            Excel::import($import, $request->file('archivo'));
        } catch (\Exception $e) {
            return back()->withErrors(['archivo' => 'Error al procesar el archivo: '.$e->getMessage()]);
        }

        $msg = "Importación completada: {$import->imported} proveedor(es) procesado(s).";
        if ($import->skipped > 0) {
            $msg .= " {$import->skipped} fila(s) omitida(s).";
        }
        if (! empty($import->errors)) {
            $msg .= ' Con errores: '.implode(' | ', $import->errors);
        }

        return redirect()->route('admin.suppliers.index')->with('success', $msg);
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="plantilla_proveedores.csv"',
        ];

        $columns = [
            'supplier_name',
            'business_name',
            'tax_code',
            'general_phone',
            'general_email',
            'country_name',
            'city_name',
            'address',
            'category_name',
            'contact_name',
            'contact_last_names',
            'contact_email',
            'contact_qualification',
            'contact_first_phone',
            'contact_second_phone',
        ];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, [
                'Proveedor Ejemplo',
                'Razón Social Ejemplo',
                '20123456789',
                '987654321',
                'contacto@ejemplo.com',
                'Perú',
                'Lima',
                'Av. Ejemplo 123',
                'Hoteles',
                'Juan',
                'Pérez',
                'juan@ejemplo.com',
                'Gerente',
                '987654321',
                '987654322',
            ]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
