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
            'supplier_name'          => 'required|string|max:100',
            'id_destinations'        => 'nullable|exists:destinations,id_destinations',
            'id_categories_suppliers'=> 'nullable|exists:categories_suppliers,id_categories_suppliers',
        ]);

        Supplier::create($request->only([
            'supplier_name', 'id_destinations', 'id_categories_suppliers'
        ]));

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
            'supplier_name'          => 'required|string|max:100',
            'id_destinations'        => 'nullable|exists:destinations,id_destinations',
            'id_categories_suppliers'=> 'nullable|exists:categories_suppliers,id_categories_suppliers',
        ]);

        $supplier->update($request->only([
            'supplier_name', 'id_destinations', 'id_categories_suppliers'
        ]));

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return back()->with('success', 'Proveedor eliminado correctamente.');
    }
}
