<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Client;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ContactsExport;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::with(['client', 'supplier'])
            ->orderBy('id_contacts', 'asc')
            ->paginate(10); // Cambiado a paginate para la paginación

        // Obtener clientes para el filtro
        $clients = Client::orderBy('name_client')->get();

        return view('admin.contacts.index', compact('contacts', 'clients'));
    }

    public function create()
    {
        $clients   = Client::orderBy('name_client')->get();
        $suppliers = Supplier::orderBy('supplier_name')->get();
        return view('admin.contacts.create', compact('clients', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:100',
            'last_names'    => 'nullable|string|max:100',
            'Date_of_birth' => 'nullable|string|max:20',
            'qualification' => 'nullable|string|max:30',
            'email'         => 'nullable|email|max:80',
            'first_phone'   => 'nullable|string|max:20',
            'second_phone'  => 'nullable|string|max:20',
            'id_client'     => 'nullable|exists:clients,id_client',
            'id_supplier'   => 'nullable|exists:suppliers,id_supplier',
            'es_principal'  => 'boolean',
        ]);

        Contact::create([
            ...$request->only([
                'name','last_names','Date_of_birth','qualification',
                'email','first_phone','second_phone','id_client','id_supplier',
            ]),
            'es_principal' => $request->boolean('es_principal'),
        ]);

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Contacto creado correctamente.');
    }

    public function edit(Contact $contact)
    {
        $clients   = Client::orderBy('name_client')->get();
        $suppliers = Supplier::orderBy('supplier_name')->get();
        return view('admin.contacts.edit', compact('contact', 'clients', 'suppliers'));
    }

    public function update(Request $request, Contact $contact)
    {
        $request->validate([
            'name'          => 'required|string|max:100',
            'last_names'    => 'nullable|string|max:100',
            'Date_of_birth' => 'nullable|string|max:20',
            'qualification' => 'nullable|string|max:30',
            'email'         => 'nullable|email|max:80',
            'first_phone'   => 'nullable|string|max:20',
            'second_phone'  => 'nullable|string|max:20',
            'id_client'     => 'nullable|exists:clients,id_client',
            'id_supplier'   => 'nullable|exists:suppliers,id_supplier',
            'es_principal'  => 'boolean',
        ]);

        $contact->update([
            ...$request->only([
                'name','last_names','Date_of_birth','qualification',
                'email','first_phone','second_phone','id_client','id_supplier',
            ]),
            'es_principal' => $request->boolean('es_principal'),
        ]);

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Contacto actualizado correctamente.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return back()->with('success', 'Contacto eliminado correctamente.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:contacts,id_contacts',
        ]);

        $count = Contact::whereIn('id_contacts', $request->ids)->count();
        Contact::whereIn('id_contacts', $request->ids)->delete();

        return redirect()->route('admin.contacts.index')
            ->with('success', "{$count} contacto(s) eliminado(s) correctamente.");
    }

    // ── EXPORTAR EXCEL CON OPCIONES ──────────────────────────
    public function exportExcel(Request $request)
    {
        $clientId = $request->filled('client_id') ? (int) $request->client_id : null;

        $export   = new ContactsExport($clientId);
        $filename = $clientId
            ? 'contactos_cliente_' . $clientId . '_' . now()->format('Ymd') . '.xlsx'
            : 'contactos_' . now()->format('Ymd') . '.xlsx';

        return Excel::download($export, $filename);
    }
}
