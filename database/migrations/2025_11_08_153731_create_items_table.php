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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->nullable()->unique();
            $table->string('name');
            $table->enum('type', ['raw_material', 'component', 'finish_good']);
            $table->foreignId('base_unit_id')->constrained('units');
            $table->boolean('track_stock')->default(true);
            $table->decimal('reorder_threshold', 15, 4)->nullable();
            $table->decimal('current_stock', 25, 4)->default(0);
            $table->decimal('average_cost', 25, 4)->default(0);
            $table->decimal('last_purchase_price', 25, 4)->nullable();
            $table->decimal('selling_price', 25, 4)->nullable();
            $table->timestamps();
            // $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
