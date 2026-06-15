<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table      = 'clients';
    protected $primaryKey = 'id_client';

    protected $fillable = ['name_client'];

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'id_client', 'id_client');
    }
}
