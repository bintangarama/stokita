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
        // Schema::create('stock_movements', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('item_id')->constrained()->cascadeOnDelete();
        //     $table->foreignId('unit_id')->nullable()->constrained('units');
        //     $table->decimal('quantity', 25, 4);
        //     $table->decimal('quantity_base', 25, 4)->comment('Dalam satuan dasar');
        //     $table->enum('type', ['in', 'out']);
        //     $table->string('reference_type')->nullable()->comment('purchase, sale, adjustment, production');
        //     $table->unsignedBigInteger('reference_id')->nullable();
        //     $table->foreignId('created_by')->nullable()->constrained('users');
        //     $table->timestamps();
        // });
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->enum('movement_type', [
                'purchase_in',
                'production_out',
                'production_in',
                'sale_out',
                'adjustment',
                'transfer_in',
                'transfer_out',
            ]);
            $table->string('reference_table', 80)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->decimal('qty', 25, 4);
            $table->foreignId('unit_id')->constrained('units');
            $table->decimal('unit_cost', 25, 4)->nullable()->comment('Cost used for this movement');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
            // $table->softDeletes();

            // Indexes
            $table->index('item_id', 'idx_stock_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
