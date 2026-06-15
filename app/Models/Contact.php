<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table      = 'contacts';
    protected $primaryKey = 'id_contacts';

    protected $fillable = [
        'id_client', 'id_supplier', 'name', 'last_names',
        'Date_of_birth', 'qualification', 'email',
        'first_phone', 'second_phone', 'es_principal',
    ];

    protected $casts = ['es_principal' => 'boolean'];

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client', 'id_client');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }
}
