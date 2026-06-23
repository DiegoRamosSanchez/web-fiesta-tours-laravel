<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\Destination;
use App\Models\CategorySupplier;
use App\Models\Bank;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Contact;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
public function index(Request $request)
{
    $search = $request->input('search');

    $suppliers = Supplier::with(['destination', 'category', 'bankAccounts.bank', 'contacts'])
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('supplier_name', 'like', "%{$search}%")
                  ->orWhere('business_name', 'like', "%{$search}%")
                  ->orWhere('tax_code', 'like', "%{$search}%")
                  ->orWhere('general_email', 'like', "%{$search}%")
                  ->orWhere('general_phone', 'like', "%{$search}%")
                  ->orWhereHas('destination', function ($dq) use ($search) {
                      $dq->where('destination_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('category', function ($cq) use ($search) {
                      $cq->where('category_name', 'like', "%{$search}%");
                  });
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->withQueryString();

    return view('admin.suppliers.index', compact('suppliers', 'search'));
}

    public function create()
    {
        $destinations = Destination::orderBy('destination_name')->get();
        $categories   = CategorySupplier::orderBy('category_name')->get();
        $banks        = Bank::orderBy('bank_name')->get();
        return view('admin.suppliers.create', compact('destinations', 'categories', 'banks'));
    }

public function store(Request $request)
{
    $request->validate([
        'supplier_name'           => 'required|string|max:100',
        'business_name'           => 'nullable|string|max:150',
        'tax_code'                => 'nullable|string|max:20',
        'general_phone'           => 'nullable|string|max:20',
        'general_email'           => 'nullable|email|max:120',
        'id_destinations'         => 'nullable|exists:destinations,id_destinations',
        'id_categories_suppliers' => 'nullable|exists:categories_suppliers,id_categories_suppliers',
        'new_destination_name'    => 'nullable|string|max:100',
        'new_category_name'       => 'nullable|string|max:100',

        // Validaciones para cuentas bancarias
        'bank_accounts'           => 'nullable|array',
        'bank_accounts.*.id_bank' => 'nullable|exists:bank,id_bank',
        'bank_accounts.*.account_number' => 'nullable|string|max:100',
        'bank_accounts.*.cci'     => 'nullable|string|max:100',
        'bank_accounts.*.currency' => 'nullable|string|max:40',
        'new_bank_name'           => 'nullable|string|max:50',
        'new_bank_account_number' => 'nullable|string|max:100',
        'new_bank_cci'            => 'nullable|string|max:100',
        'new_bank_currency'       => 'nullable|string|max:40',

        // Validaciones para contactos
        'contacts'                => 'nullable|array',
        'contacts.*.name'         => 'required_with:contacts|string|max:100',
        'contacts.*.last_names'   => 'nullable|string|max:100',
        'contacts.*.email'        => 'nullable|email|max:120',
        'contacts.*.qualification'=> 'nullable|string|max:100',
        'contacts.*.first_phone'  => 'nullable|string|max:20',
        'contacts.*.second_phone' => 'nullable|string|max:20',
    ]);

    try {
        DB::beginTransaction();

        // Crear destino al vuelo si se escribió uno nuevo
        $destinationId = $request->id_destinations;
        if ($request->filled('new_destination_name')) {
            $dest          = Destination::create(['destination_name' => $request->new_destination_name]);
            $destinationId = $dest->id_destinations;
        }

        // Crear categoría al vuelo si se escribió una nueva
        $categoryId = $request->id_categories_suppliers;
        if ($request->filled('new_category_name')) {
            $cat        = CategorySupplier::create(['category_name' => $request->new_category_name]);
            $categoryId = $cat->id_categories_suppliers;
        }

        // Crear el proveedor
        $supplier = Supplier::create([
            'supplier_name'           => $request->supplier_name,
            'business_name'           => $request->business_name,
            'tax_code'                => $request->tax_code,
            'general_phone'           => $request->general_phone,
            'general_email'           => $request->general_email,
            'id_destinations'         => $destinationId ?: null,
            'id_categories_suppliers' => $categoryId ?: null,
        ]);

        // ── PROCESAR CONTACTOS ──
        if ($request->has('contacts') && is_array($request->contacts)) {
            $first = true; // Para marcar el primer contacto como principal
            foreach ($request->contacts as $contactData) {
                // Solo crear si tiene nombre (requerido)
                if (!empty($contactData['name'])) {
                    Contact::create([
                        'id_supplier'   => $supplier->id_supplier,
                        'id_client'     => null, 
                        'name'          => $contactData['name'],
                        'last_names'    => $contactData['last_names'] ?? null,
                        'email'         => $contactData['email'] ?? null,
                        'qualification' => $contactData['qualification'] ?? null,
                        'first_phone'   => $contactData['first_phone'] ?? null,
                        'second_phone'  => $contactData['second_phone'] ?? null,
                        'es_principal'  => $first,
                        'Date_of_birth' => null, // Opcional, no está en el formulario
                    ]);
                    $first = false;
                }
            }
        }

        // Procesar cuentas bancarias existentes
        if ($request->has('bank_accounts')) {
            foreach ($request->bank_accounts as $account) {
                // Solo crear si tiene banco Y número de cuenta (ambos son obligatorios para una cuenta válida)
                if (!empty($account['id_bank']) && !empty($account['account_number'])) {
                    BankAccount::create([
                        'id_supplier'    => $supplier->id_supplier,
                        'id_bank'        => $account['id_bank'],
                        'account_number' => $account['account_number'],
                        'cci'            => $account['cci'] ?? null,
                        'currency'       => $account['currency'] ?? null,
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
                'id_supplier'    => $supplier->id_supplier,
                'id_bank'        => $bank->id_bank,
                'account_number' => $request->new_bank_account_number,
                'cci'            => $request->new_bank_cci,
                'currency'       => $request->new_bank_currency,
            ]);
        }

        DB::commit();

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Proveedor, contactos y cuentas bancarias creados correctamente.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Error al crear el proveedor: ' . $e->getMessage())
            ->withInput();
    }
}

    public function edit(Supplier $supplier)
    {
        $destinations = Destination::orderBy('destination_name')->get();
        $categories   = CategorySupplier::orderBy('category_name')->get();
        $banks        = Bank::orderBy('bank_name')->get();
        $supplier->load('bankAccounts.bank');
        return view('admin.suppliers.edit', compact('supplier', 'destinations', 'categories', 'banks'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'supplier_name'           => 'required|string|max:100',
            'business_name'           => 'nullable|string|max:150',
            'tax_code'                => 'nullable|string|max:20',
            'general_phone'           => 'nullable|string|max:20',
            'general_email'           => 'nullable|email|max:120',
            'id_destinations'         => 'nullable|exists:destinations,id_destinations',
            'id_categories_suppliers' => 'nullable|exists:categories_suppliers,id_categories_suppliers',
            'new_destination_name'    => 'nullable|string|max:100',
            'new_category_name'       => 'nullable|string|max:100',

            // Validaciones para cuentas bancarias - HACERLAS OPCIONALES
            'bank_accounts'           => 'nullable|array',
            'bank_accounts.*.id_bank' => 'nullable|exists:bank,id_bank',
            'bank_accounts.*.account_number' => 'nullable|string|max:100',
            'bank_accounts.*.cci'     => 'nullable|string|max:100',
            'bank_accounts.*.currency' => 'nullable|string|max:40',
            'new_bank_name'           => 'nullable|string|max:50',
            'new_bank_account_number' => 'nullable|string|max:100',
            'new_bank_cci'            => 'nullable|string|max:100',
            'new_bank_currency'       => 'nullable|string|max:40',
            'delete_bank_accounts'    => 'nullable|array',
            'delete_bank_accounts.*'  => 'exists:bank_account,id_bank_account',
        ]);

        try {
            DB::beginTransaction();

            // Crear destino al vuelo
            $destinationId = $request->id_destinations;
            if ($request->filled('new_destination_name')) {
                $dest          = Destination::create(['destination_name' => $request->new_destination_name]);
                $destinationId = $dest->id_destinations;
            }

            // Crear categoría al vuelo
            $categoryId = $request->id_categories_suppliers;
            if ($request->filled('new_category_name')) {
                $cat        = CategorySupplier::create(['category_name' => $request->new_category_name]);
                $categoryId = $cat->id_categories_suppliers;
            }

            // Actualizar proveedor
            $supplier->update([
                'supplier_name'           => $request->supplier_name,
                'business_name'           => $request->business_name,
                'tax_code'                => $request->tax_code,
                'general_phone'           => $request->general_phone,
                'general_email'           => $request->general_email,
                'id_destinations'         => $destinationId ?: null,
                'id_categories_suppliers' => $categoryId ?: null,
            ]);

            // Eliminar cuentas bancarias marcadas para eliminar
            if ($request->has('delete_bank_accounts')) {
                BankAccount::whereIn('id_bank_account', $request->delete_bank_accounts)->delete();
            }

            // Actualizar o crear cuentas bancarias existentes
            if ($request->has('bank_accounts')) {
                foreach ($request->bank_accounts as $account) {
                    // Saltar si no tiene banco o número de cuenta
                    if (empty($account['id_bank']) || empty($account['account_number'])) {
                        continue;
                    }

                    if (isset($account['id_bank_account']) && !empty($account['id_bank_account'])) {
                        // Actualizar cuenta existente
                        $bankAccount = BankAccount::find($account['id_bank_account']);
                        if ($bankAccount) {
                            $bankAccount->update([
                                'id_bank'        => $account['id_bank'],
                                'account_number' => $account['account_number'],
                                'cci'            => $account['cci'] ?? null,
                                'currency'       => $account['currency'] ?? null,
                            ]);
                        }
                    } else {
                        // Crear nueva cuenta
                        BankAccount::create([
                            'id_supplier'    => $supplier->id_supplier,
                            'id_bank'        => $account['id_bank'],
                            'account_number' => $account['account_number'],
                            'cci'            => $account['cci'] ?? null,
                            'currency'       => $account['currency'] ?? null,
                        ]);
                    }
                }
            }

            // Procesar nuevo banco y cuenta bancaria
            if ($request->filled('new_bank_name') && $request->filled('new_bank_account_number')) {
                $bank = Bank::create(['bank_name' => $request->new_bank_name]);
                BankAccount::create([
                    'id_supplier'    => $supplier->id_supplier,
                    'id_bank'        => $bank->id_bank,
                    'account_number' => $request->new_bank_account_number,
                    'cci'            => $request->new_bank_cci,
                    'currency'       => $request->new_bank_currency,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.suppliers.index')
                ->with('success', 'Proveedor y cuentas bancarias actualizados correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar el proveedor: ' . $e->getMessage())
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
            return back()->with('error', 'Error al eliminar el proveedor: ' . $e->getMessage());
        }
    }



    public function exportPdf(Supplier $supplier)
{
    $supplier->load(['destination', 'category', 'contacts']);
 
    $pdf = Pdf::loadView('admin.suppliers.pdf', compact('supplier'))
        ->setPaper('a4', 'portrait');
 
    $fileName = 'proveedor_' . str($supplier->supplier_name)->slug() . '.pdf';
 
    return $pdf->stream($fileName);
    // Si prefieres forzar la descarga en vez de abrir en el navegador, usa:
    // return $pdf->download($fileName);
}
 

    public function showBankAccounts(Supplier $supplier)
    {
        $supplier->load('bankAccounts.bank');
        return view('admin.suppliers.bank_accounts', compact('supplier'));
    }
}