<?php

namespace App\Imports;

use App\Models\Client;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class ClientsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public int $imported = 0;
    public int $skipped  = 0;
    public array $errors = [];

    /**
     * Headers reales del archivo (fila 1):
     * NOMBRE | PAIS | Contacto | Mail | Telefono
     * Maatwebsite normaliza esto a: nombre, pais, contacto, mail, telefono
     */
    public function headingRow(): int
    {
        return 1;
    }

    public function collection(Collection $rows)
    {
        $grouped = [];
        $currentAgencia = null;
        $currentPais = null;
        $currentTelefono = null;

        foreach ($rows as $row) {
            // ─── Soporta nombres de columnas alternativos por si cambia el export ───
            $nombreRaw   = trim((string) ($row['nombre'] ?? $row['agencia_cliente'] ?? ''));
            $pais        = trim((string) ($row['pais'] ?? $row['country_name'] ?? ''));
            $contacto    = trim((string) ($row['contacto'] ?? $row['contacto_1'] ?? ''));
            $mail        = trim((string) ($row['mail'] ?? $row['email_1'] ?? ''));
            $telefonoRaw = trim((string) ($row['telefono'] ?? $row['telefono_1'] ?? ''));

            // ─── Las celdas combinadas (merge) de Excel solo traen valor en la ───
            // ─── primera fila del grupo; el resto llega vacío. Hacemos forward-fill. ───
            if ($nombreRaw !== '') {
                $currentAgencia = $nombreRaw;
                $currentPais = $pais !== '' ? $pais : $currentPais;
                $currentTelefono = $telefonoRaw !== '' ? $telefonoRaw : null;
            }

            // Si no hay agencia activa (fila de cierre/footer/vacía), se omite
            if (empty($currentAgencia)) {
                $this->skipped++;
                continue;
            }

            // Fila sin contacto (ej. nombre de agencia sin datos de contacto) se omite del detalle
            // pero igual permite crear el cliente si es la única fila del grupo
            $grouped[$currentAgencia] ??= [
                'pais'     => $currentPais,
                'telefono' => $currentTelefono,
                'contactos' => [],
            ];

            if ($contacto !== '') {
                $grouped[$currentAgencia]['contactos'][] = [
                    'nombre'   => $contacto,
                    'email'    => $mail !== '' ? $mail : null,
                    'telefono' => $currentTelefono,
                ];
            }
        }

        foreach ($grouped as $agencia => $data) {
            try {
                $countryName = $data['pais'] ?: null;

                $client = Client::firstOrCreate(
                    ['name_client' => $agencia],
                    [
                        'business_name' => null,
                        'tax_code'      => null,
                        'general_phone' => $this->firstPhone($data['telefono']),
                        'general_email' => $data['contactos'][0]['email'] ?? null,
                        'country_name'  => $countryName,
                        'city_name'     => null,
                        'address'       => null,
                    ]
                );

                // Si el cliente ya existía, completar campos vacíos sin pisar datos existentes
                $clientUpdates = [];
                if (empty($client->country_name) && !empty($countryName)) {
                    $clientUpdates['country_name'] = $countryName;
                }
                if (empty($client->general_phone) && !empty($data['telefono'])) {
                    $clientUpdates['general_phone'] = $this->firstPhone($data['telefono']);
                }
                if (empty($client->general_email) && !empty($data['contactos'][0]['email'] ?? null)) {
                    $clientUpdates['general_email'] = $data['contactos'][0]['email'];
                }
                if (!empty($clientUpdates)) {
                    $client->update($clientUpdates);
                }

                foreach ($data['contactos'] as $i => $contactoData) {
                    $email = $contactoData['email'];

                    if ($email && $client->contacts()->where('email', $email)->exists()) {
                        $this->skipped++;
                        continue;
                    }

                    // El teléfono general se reparte solo al primer contacto del grupo
                    $tel1 = null;
                    $tel2 = null;
                    if ($i === 0 && !empty($contactoData['telefono'])) {
                        [$tel1, $tel2] = $this->splitPhones($contactoData['telefono']);
                    }

                    $esPrincipal = ($i === 0) && $client->contacts()->count() === 0;

                    $client->contacts()->create([
                        'name'          => $contactoData['nombre'],
                        'last_names'    => null,
                        'qualification' => null,
                        'email'         => $email,
                        'first_phone'   => $tel1,
                        'second_phone'  => $tel2,
                        'es_principal'  => $esPrincipal,
                    ]);
                }

                $this->imported++;

            } catch (\Exception $e) {
                $this->errors[] = "Error en '{$agencia}': " . $e->getMessage();
            }
        }
    }

    /**
     * Divide un string de teléfono(s) separado por "/" en dos valores.
     * Ej: "55 51 3408 2198 / 55 51 98299 2137" -> ["55 51 3408 2198", "55 51 98299 2137"]
     */
    private function splitPhones(?string $telefono): array
    {
        if (empty($telefono)) {
            return [null, null];
        }

        // Normaliza espacios no separables (\xa0) que vienen del Excel
        $telefono = str_replace("\xc2\xa0", ' ', $telefono);
        $telefono = trim($telefono);

        if (str_contains($telefono, '/')) {
            $parts = array_map('trim', explode('/', $telefono, 2));
            return [$parts[0] ?: null, $parts[1] ?: null];
        }

        return [$telefono, null];
    }

    /**
     * Obtiene solo el primer teléfono de un string que puede traer varios separados por "/".
     * Usado para el campo general_phone del cliente.
     */
    private function firstPhone(?string $telefono): ?string
    {
        [$tel1, ] = $this->splitPhones($telefono);
        return $tel1;
    }
}
