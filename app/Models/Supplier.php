<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table      = 'suppliers';
    protected $primaryKey = 'id_supplier';

    protected $fillable = ['id_destinations', 'id_categories_suppliers', 'supplier_name', 'business_name', 'tax_code', 'general_phone', 'general_email'];

    public function destination()
    {
        return $this->belongsTo(Destination::class, 'id_destinations', 'id_destinations');
    }

    public function category()
    {
        return $this->belongsTo(CategorySupplier::class, 'id_categories_suppliers', 'id_categories_suppliers');
    }

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class, 'id_supplier', 'id_supplier');
    }
}
