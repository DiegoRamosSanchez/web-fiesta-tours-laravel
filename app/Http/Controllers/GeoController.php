<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GeoController extends Controller
{
    private function username(): string
    {
        return config('services.geonames.username');
    }

    public function paises(Request $request)
    {
        $paises = Cache::remember('geo:paises:todos', now()->addHours(24), function () {
            $response = Http::withoutVerifying()->get('https://secure.geonames.org/countryInfoJSON', [
                'username' => $this->username(),
                'lang'     => 'es',
            ]);

            if ($response->failed()) {
                return [];
            }

            return collect($response->json('geonames', []))
                ->map(fn($p) => [
                    'codigo'     => $p['countryCode'] ?? null,
                    'nombre'     => $p['countryName'] ?? null,
                    'continente' => $p['continent'] ?? null,
                ])
                ->sortBy('nombre')
                ->values()
                ->all();
        });

        return response()->json($paises);
    }

    public function ciudades(Request $request)
    {
        $pais = strtoupper($request->query('country', 'PE'));

        $cacheKey = "geo:ciudades:{$pais}:all";

        $ciudades = Cache::remember($cacheKey, now()->addHours(24), function () use ($pais) {
            $response = Http::withoutVerifying()->get('https://secure.geonames.org/searchJSON', [
                'country'      => $pais,
                'featureClass' => 'P',
                'maxRows'      => 1000,
                'username'     => $this->username(),
                'lang'         => 'es',
                'orderby'      => 'population',
            ]);

            if ($response->failed()) {
                return [];
            }

            return collect($response->json('geonames', []))
                ->map(fn($c) => [
                    'nombre'    => $c['name'] ?? null,
                    'geoNameId' => $c['geonameId'] ?? null,
                ])
                ->unique('nombre')
                ->sortBy('nombre')
                ->values()
                ->all();
        });

        return response()->json($ciudades);
    }
}
