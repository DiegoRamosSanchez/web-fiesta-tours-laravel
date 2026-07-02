<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name', 'iso2', 'iso3', 'numeric_code', 'phone_code', 'capital',
        'currency_code', 'currency_name', 'currency_symbol', 'tld',
        'nationality', 'latitude', 'longitude', 'is_active',
    ];

    public function states()
    {
        return $this->hasMany(State::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
