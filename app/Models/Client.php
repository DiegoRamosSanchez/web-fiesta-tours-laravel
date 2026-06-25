<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table      = 'clients';
    protected $primaryKey = 'id_client';

    protected $fillable = [
        'name_client', 'business_name', 'tax_code', 'type_client', 'general_phone', 'general_email',
        'country_name', 'city_name', 'address',
    ];

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'id_client', 'id_client');
    }

    // Mutator para convertir país a mayúsculas automáticamente
    public function setCountryNameAttribute($value)
    {
        // Usar mb_strtoupper() para manejar correctamente caracteres UTF-8
        $this->attributes['country_name'] = $value ? mb_strtoupper($value) : null;
    }
}
