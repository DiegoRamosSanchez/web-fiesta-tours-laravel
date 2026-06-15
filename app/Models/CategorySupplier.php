<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategorySupplier extends Model
{
    protected $table      = 'categories_suppliers';
    protected $primaryKey = 'id_categories_suppliers';
    protected $fillable   = ['category_name'];
}
