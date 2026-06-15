<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ContactController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
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
        Route::get('/',           [ClientController::class, 'index'])->name('index');
        Route::get('/crear',      [ClientController::class, 'create'])->name('create');
        Route::post('/',          [ClientController::class, 'store'])->name('store');
        Route::get('/{client}/editar', [ClientController::class, 'edit'])->name('edit');
        Route::put('/{client}',   [ClientController::class, 'update'])->name('update');
        Route::delete('/{client}',[ClientController::class, 'destroy'])->name('destroy');
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

    // ── SOLO ADMIN: gestión de usuarios del sistema ──
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/usuarios',           [UserController::class, 'index'])->name('usuarios');
        Route::get('/usuarios/crear',     [UserController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios',          [UserController::class, 'store'])->name('usuarios.store');
        Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('usuarios.destroy');
    });
});
