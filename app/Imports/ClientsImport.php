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

    // ← Esto es lo clave: los encabezados están en la fila 4
    public function headingRow(): int
    {
        return 4;
    }

    public function collection(Collection $rows)
    {
        $grouped = [];

        foreach ($rows as $row) {
            $agencia = trim($row['agencia_cliente'] ?? '');

            if (empty($agencia)) {
                $this->skipped++;
                continue;
            }

            $grouped[$agencia][] = $row;
        }

        foreach ($grouped as $agencia => $filas) {
            try {
                $client = Client::firstOrCreate(['name_client' => $agencia]);

                foreach ($filas as $row) {
                    // Los 3 slots de contacto por fila
                    $slots = [
                        1 => [
                            'nombre' => trim($row['contacto_1'] ?? ''),
                            'cargo'  => trim($row['cargo_1']    ?? ''),
                            'email'  => trim($row['email_1']    ?? ''),
                            'tel1'   => trim($row['telefono_1'] ?? ''),
                            'tel2'   => trim($row['telefono_2_1'] ?? ''),
                        ],
                        2 => [
                            'nombre' => trim($row['contacto_2']   ?? ''),
                            'cargo'  => trim($row['cargo_2']      ?? ''),
                            'email'  => trim($row['email_2']      ?? ''),
                            'tel1'   => trim($row['telefono_1_2'] ?? ''),
                            'tel2'   => trim($row['telefono_2_2'] ?? ''),
                        ],
                        3 => [
                            'nombre' => trim($row['contacto_3']   ?? ''),
                            'cargo'  => trim($row['cargo_3']      ?? ''),
                            'email'  => trim($row['email_3']      ?? ''),
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
                            'last_names'    => null,
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
