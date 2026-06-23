<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Rate limiting: máximo 5 intentos por minuto
        $key = Str::lower($request->email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Demasiados intentos. Espera {$seconds} segundos.",
            ]);
        }

        if (Auth::attempt(
            $request->only('email', 'password'),
            $request->boolean('remember')
        )) {
            RateLimiter::clear($key);
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        RateLimiter::hit($key, 60);

        return back()->withErrors([
            'email' => 'Las credenciales no son correctas.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }









    public function obtenerPorContinente(Request $request)
    {
        $continente = strtoupper($request->query('continente', 'SA'));

        $username = "luistasayco";
        $lang = "es";

        $url = "http://api.geonames.org/countryInfoJSON";
        $response = Http::get($url, [
            'username' => $username,
            'lang' => $lang
        ]);

        $geonames = collect($response->json('geonames', []));

        $paisesFiltrados = $geonames
            ->where('continent', $continente)
            ->map(function ($pais) {
                return [
                    'codigo' => $pais['countryCode'] ?? null,
                    'nombre' => $pais['countryName'] ?? null,
                    'capital' => $pais['capital'] ?? null,
                    'geoNameId' => $pais['geonameId'] ?? null,
                    'continente' => $pais['continentName'] ?? null
                ];
            })
            ->values();
            
        return response()->json([
            'continente' => $continente,
            'total' => $paisesFiltrados->count(),
            'paises' => $paisesFiltrados
        ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    
    
    

    public function obtenerPorPais(Request $request)
    {
        
        $paisCodigo = strtoupper($request->query('country', 'PE'));
    
        $username = "luistasayco";
        $lang = "es";
    
    
        $url = "http://api.geonames.org/searchJSON";
        
        $response = Http::get($url, [
            'country'      => $paisCodigo, 
            'featureClass' => 'P',         
            'maxRows'      => 1000,
            'username'     => $username,
            'lang'         => $lang
        ]);
    
    
        if ($response->failed()) {
            return response()->json(['error' => 'No se pudo conectar con el servicio geográfico'], 500);
        }
    
    
        $geonames = collect($response->json('geonames', []));
    
        $lugaresFiltrados = $geonames->map(function ($lugar) {
            return [
                'codigo_admin' => $lugar['adminCode1'] ?? null,
                'toponymName'  => $lugar['toponymName'] ?? null,
                'nombre'       => $lugar['name'] ?? null,
            ];
        })->values();
    
        return response()->json([
            'pais_buscado' => $paisCodigo,
            'total'        => $lugaresFiltrados->count(),
            'resultados'   => $lugaresFiltrados
        ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
