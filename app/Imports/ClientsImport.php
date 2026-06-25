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
     * NOMBRE | PAIS | Contacto | Mail | Direccion | Telefono
     * Maatwebsite normaliza esto a: nombre, pais, contacto, mail, direccion, telefono
     * (Nota: el ORDEN de las columnas no afecta nada, porque WithHeadingRow
     * mapea por NOMBRE de encabezado, no por posición de columna).
     */
    public function headingRow(): int
    {
        return 1;
    }

    // Límites de longitud según la migración de la BD
    private const MAX_GENERAL_PHONE = 20;
    private const MAX_PHONE         = 20;
    private const MAX_ADDRESS       = 255;

    public function collection(Collection $rows)
    {
        $grouped = [];
        $currentAgencia  = null;
        $currentPais     = null;
        $currentTelefono = null;
        $currentDireccion = null;

        foreach ($rows as $row) {
            // ─── Soporta nombres de columnas alternativos por si cambia el export ───
            $nombreRaw    = trim((string) ($row['nombre'] ?? $row['agencia_cliente'] ?? ''));
            $pais         = trim((string) ($row['pais'] ?? $row['country_name'] ?? ''));
            $contacto     = trim((string) ($row['contacto'] ?? $row['contacto_1'] ?? ''));
            $mail         = trim((string) ($row['mail'] ?? $row['email_1'] ?? ''));
            $telefonoRaw  = trim((string) ($row['telefono'] ?? $row['telefono_1'] ?? ''));
            $direccionRaw = trim((string) ($row['direccion'] ?? $row['address'] ?? ''));

            // ─── Las celdas combinadas (merge) de Excel solo traen valor en la ───
            // ─── primera fila del grupo; el resto llega vacío. Hacemos forward-fill. ───
            if ($nombreRaw !== '') {
                $currentAgencia   = $nombreRaw;
                $currentPais      = $pais !== '' ? $pais : $currentPais;
                $currentTelefono  = $telefonoRaw !== '' ? $telefonoRaw : null;
                $currentDireccion = $direccionRaw !== '' ? $direccionRaw : null;
            }

            // Si no hay agencia activa (fila de cierre/footer/vacía), se omite
            if (empty($currentAgencia)) {
                $this->skipped++;
                continue;
            }

            $grouped[$currentAgencia] ??= [
                'pais'      => $currentPais,
                'telefono'  => $currentTelefono,
                'direccion' => $currentDireccion,
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
                $address     = $this->safeTruncate($data['direccion'] ?? null, self::MAX_ADDRESS);

                $client = Client::firstOrCreate(
                    ['name_client' => $agencia],
                    [
                        'business_name' => null,
                        'tax_code'      => null,
                        'general_phone' => $this->firstPhone($data['telefono']),
                        'general_email' => $data['contactos'][0]['email'] ?? null,
                        'country_name'  => $countryName,
                        'city_name'     => null,
                        'address'       => $address,
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
                if (empty($client->address) && !empty($address)) {
                    $clientUpdates['address'] = $address;
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
     * Divide un string de teléfono(s) separado por "/" en dos valores,
     * y los recorta para que nunca superen el límite de la columna en BD.
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
            $tel1 = $parts[0] ?: null;
            $tel2 = $parts[1] ?: null;
        } else {
            $tel1 = $telefono;
            $tel2 = null;
        }

        return [
            $this->safeTruncate($tel1, self::MAX_PHONE),
            $this->safeTruncate($tel2, self::MAX_PHONE),
        ];
    }

    /**
     * Obtiene solo el primer teléfono de un string que puede traer varios separados por "/",
     * ya recortado al límite permitido por la columna general_phone.
     */
    private function firstPhone(?string $telefono): ?string
    {
        [$tel1, ] = $this->splitPhones($telefono);
        return $tel1;
    }

    /**
     * Recorta un valor de forma segura para que nunca supere el límite de
     * la columna en BD (evita el error SQLSTATE[22001] "Data too long").
     */
    private function safeTruncate(?string $value, int $maxLength): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        if ($value === '') {
            return null;
        }

        return mb_substr($value, 0, $maxLength);
    }
}
