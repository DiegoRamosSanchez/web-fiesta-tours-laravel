<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeoController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DestinationController;
use App\Http\Controllers\Admin\CategorySupplierController;
use App\Http\Controllers\Admin\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// ── GEONAMES (paises, departamentos, ciudades) ──
// Sin middleware 'auth' porque se usan en formularios públicos/registro.
Route::prefix('api/geo')->group(function () {
    Route::get('/paises',        [GeoController::class, 'paises']);
    Route::get('/ciudades',      [GeoController::class, 'ciudades']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout',   [AuthController::class, 'logout'])->name('logout');

    // Perfil (cualquier usuario autenticado)
    Route::get('/perfil',        [PerfilController::class, 'show'])->name('perfil');
    Route::get('/perfil/editar', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil',        [PerfilController::class, 'update'])->name('perfil.update');

    // ── CLIENTES (admin y cliente) ──
    Route::prefix('clientes')->name('admin.clients.')->group(function () {
        Route::get('/',                [ClientController::class, 'index'])->name('index');
        Route::get('/exportar/pdf',    [ClientController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/exportar/excel',  [ClientController::class, 'exportExcel'])->name('export.excel');
        Route::get('/plantilla',       [ClientController::class, 'downloadTemplate'])->name('template');
        Route::get('/importar',        [ClientController::class, 'importView'])->name('import.view');
        Route::post('/importar',       [ClientController::class, 'import'])->name('import');
        Route::post('/',               [ClientController::class, 'store'])->name('store');
        Route::delete('/bulk',         [ClientController::class, 'bulkDestroy'])->name('bulk-destroy');

        // Rutas con parámetro DESPUÉS
        Route::get('/{client}/editar', [ClientController::class, 'edit'])->name('edit');
        Route::put('/{client}',        [ClientController::class, 'update'])->name('update');
        Route::delete('/{client}',     [ClientController::class, 'destroy'])->name('destroy');
    });

    // ── CONTACTOS (admin y cliente) ──
    Route::prefix('contactos')->name('admin.contacts.')->group(function () {
        Route::get('/',             [ContactController::class, 'index'])->name('index');
        Route::get('/crear',        [ContactController::class, 'create'])->name('create');
        Route::post('/',            [ContactController::class, 'store'])->name('store');
        Route::get('/{contact}/editar', [ContactController::class, 'edit'])->name('edit');
        Route::put('/{contact}',    [ContactController::class, 'update'])->name('update');
        Route::delete('/{contact}', [ContactController::class, 'destroy'])->name('destroy');
    });

    // ── DESTINOS ──
    Route::prefix('destinos')->name('admin.destinations.')->group(function () {
        Route::get('/',                    [DestinationController::class, 'index'])->name('index');
        Route::get('/crear',               [DestinationController::class, 'create'])->name('create');
        Route::post('/',                   [DestinationController::class, 'store'])->name('store');
        Route::get('/{destination}/editar',[DestinationController::class, 'edit'])->name('edit');
        Route::put('/{destination}',       [DestinationController::class, 'update'])->name('update');
        Route::delete('/{destination}',    [DestinationController::class, 'destroy'])->name('destroy');
    });

    // ── CATEGORÍAS DE PROVEEDORES ──
    Route::prefix('categorias-proveedores')->name('admin.categories.')->group(function () {
        Route::get('/',                  [CategorySupplierController::class, 'index'])->name('index');
        Route::get('/crear',             [CategorySupplierController::class, 'create'])->name('create');
        Route::post('/',                 [CategorySupplierController::class, 'store'])->name('store');
        Route::get('/{category}/editar', [CategorySupplierController::class, 'edit'])->name('edit');
        Route::put('/{category}',        [CategorySupplierController::class, 'update'])->name('update');
        Route::delete('/{category}',     [CategorySupplierController::class, 'destroy'])->name('destroy');
    });

    // ── PROVEEDORES ──
    Route::prefix('proveedores')->name('admin.suppliers.')->group(function () {
        Route::get('/',                   [SupplierController::class, 'index'])->name('index');
        Route::get('/crear',              [SupplierController::class, 'create'])->name('create');
        Route::post('/',                  [SupplierController::class, 'store'])->name('store');
        Route::get('/{supplier}/editar',  [SupplierController::class, 'edit'])->name('edit');
        Route::put('/{supplier}',         [SupplierController::class, 'update'])->name('update');
        Route::delete('/{supplier}',      [SupplierController::class, 'destroy'])->name('destroy');
    });

    // ── SOLO ADMIN: gestión de usuarios del sistema ──
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/usuarios',           [UserController::class, 'index'])->name('usuarios');
        Route::get('/usuarios/crear',     [UserController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios',          [UserController::class, 'store'])->name('usuarios.store');
        Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('usuarios.destroy');
    });

        Route::get('suppliers/{supplier}/pdf', [SupplierController::class, 'exportPdf'])
    ->name('admin.suppliers.pdf');


});
