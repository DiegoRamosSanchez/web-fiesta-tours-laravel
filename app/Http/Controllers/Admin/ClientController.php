<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Contact;
use Illuminate\Http\Request;

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
            'name_client'              => 'required|string|max:100',
            'contacts.*.name'          => 'required|string|max:100',
            'contacts.*.last_names'    => 'nullable|string|max:100',
            'contacts.*.email'         => 'nullable|email|max:80',
            'contacts.*.first_phone'   => 'nullable|string|max:20',
            'contacts.*.second_phone'  => 'nullable|string|max:20',
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
        $request->validate([
            'name_client' => 'required|string|max:100',
        ]);
        $client->update(['name_client' => $request->name_client]);
        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return back()->with('success', 'Cliente eliminado correctamente.');
    }
}
