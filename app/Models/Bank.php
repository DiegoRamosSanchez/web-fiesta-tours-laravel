<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table      = 'bank';
    protected $primaryKey = 'id_bank';
    protected $fillable   = [
        'bank_name',
    ];

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class, 'id_bank', 'id_bank');
    }
}
