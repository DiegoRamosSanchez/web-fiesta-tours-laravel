<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\Destination;
use App\Models\CategorySupplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::with(['destination', 'category'])
            ->orderBy('created_at', 'desc')->get();
        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $destinations = Destination::orderBy('destination_name')->get();
        $categories   = CategorySupplier::orderBy('category_name')->get();
        return view('admin.suppliers.create', compact('destinations', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_name'           => 'required|string|max:100',
            'id_destinations'         => 'nullable|exists:destinations,id_destinations',
            'id_categories_suppliers' => 'nullable|exists:categories_suppliers,id_categories_suppliers',
            'new_destination_name'    => 'nullable|string|max:100',
            'new_category_name'       => 'nullable|string|max:100',
        ]);

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

        Supplier::create([
            'supplier_name'           => $request->supplier_name,
            'id_destinations'         => $destinationId ?: null,
            'id_categories_suppliers' => $categoryId ?: null,
        ]);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Proveedor creado correctamente.');
    }

    public function edit(Supplier $supplier)
    {
        $destinations = Destination::orderBy('destination_name')->get();
        $categories   = CategorySupplier::orderBy('category_name')->get();
        return view('admin.suppliers.edit', compact('supplier', 'destinations', 'categories'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'supplier_name'           => 'required|string|max:100',
            'id_destinations'         => 'nullable|exists:destinations,id_destinations',
            'id_categories_suppliers' => 'nullable|exists:categories_suppliers,id_categories_suppliers',
            'new_destination_name'    => 'nullable|string|max:100',
            'new_category_name'       => 'nullable|string|max:100',
        ]);

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

        $supplier->update([
            'supplier_name'           => $request->supplier_name,
            'id_destinations'         => $destinationId ?: null,
            'id_categories_suppliers' => $categoryId ?: null,
        ]);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return back()->with('success', 'Proveedor eliminado correctamente.');
    }
}
