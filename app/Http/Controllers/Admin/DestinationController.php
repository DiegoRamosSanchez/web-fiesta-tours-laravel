<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    public function index()
    {
        $destinations = Destination::orderBy('created_at', 'desc')->get();
        return view('admin.destinations.index', compact('destinations'));
    }

    public function create()
    {
        return view('admin.destinations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'destination_name' => 'required|string|max:100',
        ]);

        Destination::create($request->only('destination_name'));

        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destino creado correctamente.');
    }

    public function edit(Destination $destination)
    {
        return view('admin.destinations.edit', compact('destination'));
    }

    public function update(Request $request, Destination $destination)
    {
        $request->validate([
            'destination_name' => 'required|string|max:100',
        ]);

        $destination->update($request->only('destination_name'));

        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destino actualizado correctamente.');
    }

    public function destroy(Destination $destination)
    {
        $destination->delete();
        return back()->with('success', 'Destino eliminado correctamente.');
    }
}
