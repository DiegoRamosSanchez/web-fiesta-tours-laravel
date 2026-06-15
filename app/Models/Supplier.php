<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table      = 'suppliers';
    protected $primaryKey = 'id_supplier';

    protected $fillable = ['id_destinations', 'id_categories_suppliers', 'supplier_name'];

    public function destination()
    {
        return $this->belongsTo(Destination::class, 'id_destinations', 'id_destinations');
    }

    public function category()
    {
        return $this->belongsTo(CategorySupplier::class, 'id_categories_suppliers', 'id_categories_suppliers');
    }
}
