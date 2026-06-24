<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Contact;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientsExport;
use App\Exports\ClientsExportById;
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
        $clients = Client::withCount('contacts')
            ->orderBy('id_client', 'asc')
            ->paginate(8);

        $countries = Client::whereNotNull('country_name')
            ->distinct()
            ->pluck('country_name')
            ->filter()
            ->values()
            ->toArray();

        $cities = Client::whereNotNull('city_name')
            ->distinct()
            ->pluck('city_name')
            ->filter()
            ->values()
            ->toArray();

        return view('admin.clients.index', compact('clients', 'countries', 'cities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_client'                   => 'required|string|max:120',
            'business_name'                 => 'nullable|string|max:150',
            'tax_code'                      => 'nullable|string|max:20',
            'general_phone'                 => 'nullable|string|max:20',
            'general_email'                 => 'nullable|email|max:120',
            'country_name'                  => 'nullable|string|max:100',
            'city_name'                     => 'nullable|string|max:150',
            'address'                       => 'nullable|string|max:255',
            'contacts.*.name'               => 'required|string|max:100',
            'contacts.*.last_names'         => 'nullable|string|max:100',
            'contacts.*.qualification'      => 'nullable|string|max:30',
            'contacts.*.email'              => 'nullable|email|max:80',
            'contacts.*.first_phone'        => 'nullable|string|max:20',
            'contacts.*.second_phone'       => 'nullable|string|max:20',
        ]);

        $client = Client::create([
            'name_client'   => $request->name_client,
            'business_name' => $request->business_name,
            'tax_code'      => $request->tax_code,
            'general_phone' => $request->general_phone,
            'general_email' => $request->general_email,
            'country_name'  => $request->country_name,
            'city_name'     => $request->city_name,
            'address'       => $request->address,
        ]);

        foreach ($request->input('contacts', []) as $i => $data) {
            if (empty($data['name'])) continue;
            $client->contacts()->create([
                'name'          => $data['name'],
                'last_names'    => $data['last_names']    ?? null,
                'qualification' => $data['qualification'] ?? null,
                'email'         => $data['email']         ?? null,
                'first_phone'   => $data['first_phone']   ?? null,
                'second_phone'  => $data['second_phone']  ?? null,
                'es_principal'  => $i === 0,
            ]);
        }

        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function edit(Client $client)
    {
        $client->load(['contacts' => fn($q) => $q->orderBy('es_principal', 'desc')->orderBy('created_at')]);
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name_client'                     => 'required|string|max:120',
            'business_name'                   => 'nullable|string|max:150',
            'tax_code'                        => 'nullable|string|max:20',
            'general_phone'                   => 'nullable|string|max:20',
            'general_email'                   => 'nullable|email|max:120',
            'country_name'                    => 'nullable|string|max:100',
            'city_name'                       => 'nullable|string|max:150',
            'address'                         => 'nullable|string|max:255',
            'contacts.*.id'                   => 'nullable|integer',
            'contacts.*.name'                 => 'required|string|max:100',
            'contacts.*.last_names'           => 'nullable|string|max:100',
            'contacts.*.qualification'        => 'nullable|string|max:30',
            'contacts.*.email'                => 'nullable|email|max:80',
            'contacts.*.first_phone'          => 'nullable|string|max:20',
            'contacts.*.second_phone'         => 'nullable|string|max:20',
            'contacts.*.es_principal'         => 'nullable|boolean',
            'new_contacts.*.name'             => 'nullable|string|max:100',
            'new_contacts.*.last_names'       => 'nullable|string|max:100',
            'new_contacts.*.qualification'    => 'nullable|string|max:30',
            'new_contacts.*.email'            => 'nullable|email|max:80',
            'new_contacts.*.first_phone'      => 'nullable|string|max:20',
            'new_contacts.*.second_phone'     => 'nullable|string|max:20',
            'delete_contacts'                 => 'nullable|array',
            'delete_contacts.*'               => 'integer|exists:contacts,id_contacts',
        ]);

        // Actualizar datos del cliente
        $client->update([
            'name_client'   => $request->name_client,
            'business_name' => $request->business_name,
            'tax_code'      => $request->tax_code,
            'general_phone' => $request->general_phone,
            'general_email' => $request->general_email,
            'country_name'  => $request->country_name,
            'city_name'     => $request->city_name,
            'address'       => $request->address,
        ]);

        // Eliminar contactos marcados
        if ($request->filled('delete_contacts')) {
            Contact::whereIn('id_contacts', $request->delete_contacts)
                ->where('id_client', $client->id_client)
                ->delete();
        }

        // Actualizar contactos existentes
        foreach ($request->input('contacts', []) as $data) {
            if (empty($data['id'])) continue;
            $contact = Contact::where('id_contacts', $data['id'])
                ->where('id_client', $client->id_client)
                ->first();
            if (!$contact) continue;
            $contact->update([
                'name'          => $data['name'],
                'last_names'    => $data['last_names']    ?? null,
                'qualification' => $data['qualification'] ?? null,
                'email'         => $data['email']         ?? null,
                'first_phone'   => $data['first_phone']   ?? null,
                'second_phone'  => $data['second_phone']  ?? null,
                'es_principal'  => isset($data['es_principal']),
            ]);
        }

        // Agregar nuevos contactos
        foreach ($request->input('new_contacts', []) as $data) {
            if (empty($data['name'])) continue;
            $client->contacts()->create([
                'name'          => $data['name'],
                'last_names'    => $data['last_names']    ?? null,
                'qualification' => $data['qualification'] ?? null,
                'email'         => $data['email']         ?? null,
                'first_phone'   => $data['first_phone']   ?? null,
                'second_phone'  => $data['second_phone']  ?? null,
                'es_principal'  => false,
            ]);
        }

        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return back()->with('success', 'Cliente eliminado correctamente.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:clients,id_client',
        ]);

        $count = Client::whereIn('id_client', $request->ids)->count();
        Client::whereIn('id_client', $request->ids)->delete();

        return redirect()->route('admin.clients.index')
            ->with('success', "{$count} cliente(s) eliminado(s) correctamente.");
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
        if ($import->skipped > 0) $msg .= " {$import->skipped} fila(s) omitida(s).";
        if (!empty($import->errors)) $msg .= ' Con errores: ' . implode(' | ', $import->errors);

        return redirect()->route('admin.clients.index')->with('success', $msg);
    }


    // ── EXPORTAR EXCEL CON OPCIONES ──────────────────────────
    public function exportExcel(Request $request)
    {
        // Si se envía un ID específico, usar el export por ID
        if ($request->has('client_id') && !empty($request->client_id)) {
            $export = new ClientsExportById($request->client_id);
            $filename = 'cliente_id_' . $request->client_id . '_' . now()->format('Ymd') . '.xlsx';
        } else {
            $export = new ClientsExport();
            $filename = 'clientes_' . now()->format('Ymd') . '.xlsx';
        }

        return Excel::download($export, $filename);
    }

    // ── EXPORTAR PDF CON OPCIONES ─────────────────────────────
    public function exportPdf(Request $request)
    {
        $query = Client::with(['contacts' => function($q) {
            $q->orderBy('es_principal', 'desc')->orderBy('created_at');
        }])
        ->withCount('contacts');

        // Si se envía un ID específico
        if ($request->has('client_id') && !empty($request->client_id)) {
            $query->where('id_client', $request->client_id);
            $suffix = '_id_' . $request->client_id;
        } else {
            $suffix = '';
        }

        $clients = $query->orderBy('name_client')->get();

        $pdf = Pdf::loadView('admin.clients.export-pdf', compact('clients'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'          => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'dpi'                  => 96,
                'isPhpEnabled'         => true,
                'encoding'             => 'UTF-8',
            ]);

        return $pdf->download('clientes' . $suffix . '_' . now()->format('Ymd') . '.pdf');
    }

    // ── OBTENER CLIENTE POR ID (PARA EL MODAL) ──────────────
    public function getClient(Request $request)
    {
        $client = Client::with(['contacts' => function($q) {
            $q->orderBy('es_principal', 'desc')->orderBy('created_at');
        }])
        ->withCount('contacts')
        ->find($request->id);

        if (!$client) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        return response()->json($client);
    }
}
