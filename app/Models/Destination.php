<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    protected $table      = 'destinations';
    protected $primaryKey = 'id_destinations';
    protected $fillable   = ['destination_name'];
}
