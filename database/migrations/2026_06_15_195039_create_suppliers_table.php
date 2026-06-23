<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id('id_supplier');
            $table->foreignId('id_destinations')->nullable()->constrained('destinations', 'id_destinations')->onDelete('cascade');
            $table->foreignId('id_categories_suppliers')->nullable()->constrained('categories_suppliers', 'id_categories_suppliers')->onDelete('cascade');

            $table->string('supplier_name', 100);
            $table->string('business_name', 150)->nullable();
            $table->string('tax_code', 20)->nullable();
            $table->string('general_phone', 20)->nullable();
            $table->string('general_email', 120)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
