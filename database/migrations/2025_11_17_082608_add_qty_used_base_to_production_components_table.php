<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom qty_used_base ke tabel production_components.
     * Kolom ini akan menyimpan jumlah bahan dalam satuan dasar item
     * (base_unit), sehingga rollback/edit produksi tidak perlu konversi lagi.
     */
    public function up(): void
    {
        Schema::table('production_components', function (Blueprint $table) {

            // Tambahkan kolom qty_used_base setelah qty_used
            if (!Schema::hasColumn('production_components', 'qty_used_base')) {
                $table->decimal('qty_used_base', 25, 8)
                    ->nullable()
                    ->after('qty_used')
                    ->comment('Jumlah bahan dalam satuan dasar item (base unit)');
            }

            // Opsional index untuk query cepat
            if (!Schema::hasColumn('production_components', 'qty_used_base_index')) {
                $table->index('ingredient_item_id', 'idx_comp_ingredient');
            }
        });
    }

    /**
     * Drop kolom saat rollback.
     */
    public function down(): void
    {
        Schema::table('production_components', function (Blueprint $table) {
            if (Schema::hasColumn('production_components', 'qty_used_base')) {
                $table->dropColumn('qty_used_base');
            }

            if (Schema::hasColumn('production_components', 'idx_comp_ingredient')) {
                $table->dropIndex('idx_comp_ingredient');
            }
        });
    }
};
