<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::withCount('contacts')->orderBy('created_at', 'desc')->get();
        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_client' => 'required|string|max:20',
        ]);

        Client::create($request->only('name_client'));

        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name_client' => 'required|string|max:20',
        ]);

        $client->update($request->only('name_client'));

        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return back()->with('success', 'Cliente eliminado correctamente.');
    }
}
