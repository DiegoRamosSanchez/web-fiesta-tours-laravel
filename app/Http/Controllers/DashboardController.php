<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Supplier;
use App\Models\Destination;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return view('dashboard', compact('user'));
        }

        // Vista usuario: cargamos solo lo necesario
        $totalClients      = Client::count();
        $totalContacts     = Contact::count();
        $totalSuppliers    = Supplier::count();
        $totalDestinations = Destination::count();

        $recentClients = Client::withCount('contacts')
            ->latest()->take(5)->get();

        $recentSuppliers = Supplier::with(['destination', 'category'])
            ->latest()->take(4)->get();

        return view('dashboard', compact(
            'user',
            'totalClients',
            'totalContacts',
            'totalSuppliers',
            'totalDestinations',
            'recentClients',
            'recentSuppliers'
        ));
    }
}
