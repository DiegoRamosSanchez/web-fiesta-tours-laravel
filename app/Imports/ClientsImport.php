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

    public function headingRow(): int
    {
        return 4;
    }

    public function collection(Collection $rows)
    {
        $grouped = [];

        // Convertir a array para saber el total de filas
        $rowsArray = $rows->toArray();
        $totalRows = count($rowsArray);

        foreach ($rowsArray as $index => $row) {
            // Saltar las últimas 2 filas (footer)
            if ($index >= $totalRows - 2) {
                $this->skipped++;
                continue;
            }

            $agencia = trim($row['agencia_cliente'] ?? '');

            if (empty($agencia)) {
                $this->skipped++;
                continue;
            }

            // ─── USAR LOS HEADERS CORRECTOS DEL EXPORT ───
            $businessName = trim($row['razon_social'] ?? '');
            $taxCode = trim($row['codigo_tributario'] ?? '');
            $generalPhone = trim($row['telefono_general'] ?? '');
            $generalEmail = trim($row['email_general'] ?? '');
            $countryName = trim($row['pais'] ?? '');
            $cityName = trim($row['ciudad'] ?? '');
            $address = trim($row['direccion'] ?? '');

            // Si no hay datos de empresa, intentar con los nombres alternativos
            if (empty($businessName)) {
                $businessName = trim($row['business_name'] ?? '');
            }
            if (empty($taxCode)) {
                $taxCode = trim($row['tax_code'] ?? $row['ruc'] ?? '');
            }
            if (empty($generalPhone)) {
                $generalPhone = trim($row['general_phone'] ?? '');
            }
            if (empty($generalEmail)) {
                $generalEmail = trim($row['general_email'] ?? '');
            }
            if (empty($countryName)) {
                $countryName = trim($row['country_name'] ?? $row['pais_cliente'] ?? '');
            }
            if (empty($cityName)) {
                $cityName = trim($row['city_name'] ?? $row['ciudad_cliente'] ?? '');
            }
            if (empty($address)) {
                $address = trim($row['address'] ?? $row['direccion_cliente'] ?? '');
            }

            $grouped[$agencia][] = [
                'business_name' => $businessName,
                'tax_code'      => $taxCode,
                'general_phone' => $generalPhone,
                'general_email' => $generalEmail,
                'country_name'  => $countryName,
                'city_name'     => $cityName,
                'address'       => $address,
                'row' => $row
            ];
        }

        foreach ($grouped as $agencia => $filas) {
            try {
                $primera = $filas[0];

                // Buscar o crear cliente con TODOS los campos
                $client = Client::firstOrCreate(
                    ['name_client' => $agencia],
                    [
                        'business_name' => $primera['business_name'] ?: null,
                        'tax_code'      => $primera['tax_code'] ?: null,
                        'general_phone' => $primera['general_phone'] ?: null,
                        'general_email' => $primera['general_email'] ?: null,
                        'country_name'  => $primera['country_name'] ?: null,
                        'city_name'     => $primera['city_name'] ?: null,
                        'address'       => $primera['address'] ?: null,
                    ]
                );

                // Si el cliente ya existía, actualizar campos vacíos
                $clientUpdates = [];
                foreach (['business_name', 'tax_code', 'general_phone', 'general_email', 'country_name', 'city_name', 'address'] as $field) {
                    if (empty($client->$field) && !empty($primera[$field])) {
                        $clientUpdates[$field] = $primera[$field];
                    }
                }
                if (!empty($clientUpdates)) {
                    $client->update($clientUpdates);
                }

                // Procesar contactos
                foreach ($filas as $fila) {
                    $row = $fila['row'];

                    $slots = [
                        1 => [
                            'nombre' => trim($row['contacto_1'] ?? ''),
                            'last_names' => trim($row['apellidos_1'] ?? ''),
                            'cargo'  => trim($row['cargo_1'] ?? ''),
                            'email'  => trim($row['email_1'] ?? ''),
                            'tel1'   => trim($row['telefono_1'] ?? ''),
                            'tel2'   => trim($row['telefono_2_1'] ?? ''),
                        ],
                        2 => [
                            'nombre' => trim($row['contacto_2'] ?? ''),
                            'last_names' => trim($row['apellidos_2'] ?? ''),
                            'cargo'  => trim($row['cargo_2'] ?? ''),
                            'email'  => trim($row['email_2'] ?? ''),
                            'tel1'   => trim($row['telefono_1_2'] ?? ''),
                            'tel2'   => trim($row['telefono_2_2'] ?? ''),
                        ],
                        3 => [
                            'nombre' => trim($row['contacto_3'] ?? ''),
                            'last_names' => trim($row['apellidos_3'] ?? ''),
                            'cargo'  => trim($row['cargo_3'] ?? ''),
                            'email'  => trim($row['email_3'] ?? ''),
                            'tel1'   => trim($row['telefono_1_3'] ?? ''),
                            'tel2'   => trim($row['telefono_2_3'] ?? ''),
                        ],
                    ];

                    foreach ($slots as $n => $data) {
                        if (empty($data['nombre'])) continue;

                        $email = $data['email'] ?: null;

                        if ($email && $client->contacts()->where('email', $email)->exists()) {
                            $this->skipped++;
                            continue;
                        }

                        $esPrincipal = ($n === 1) && $client->contacts()->count() === 0;

                        $client->contacts()->create([
                            'name'          => $data['nombre'],
                            'last_names'    => $data['last_names'] ?: null,
                            'qualification' => $data['cargo'] ?: null,
                            'email'         => $email,
                            'first_phone'   => $data['tel1'] ?: null,
                            'second_phone'  => $data['tel2'] ?: null,
                            'es_principal'  => $esPrincipal,
                        ]);
                    }
                }

                $this->imported++;

            } catch (\Exception $e) {
                $this->errors[] = "Error en '{$agencia}': " . $e->getMessage();
            }
        }
    }
}
