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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items');
            $table->string('name');
            $table->decimal('yield_qty', 25, 4)->default(1);
            $table->integer('version')->default(1);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('recipe_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained('recipes')->cascadeOnDelete();
            $table->foreignId('ingredient_item_id')->constrained('items');
            $table->foreignId('unit_id')->constrained('units');
            $table->decimal('qty', 25, 8);
            $table->timestamps();
        });

        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produced_item_id')->constrained('items');
            $table->foreignId('recipe_id')->nullable()->constrained('recipes');
            $table->foreignId('produced_unit_id')->constrained('units');
            $table->decimal('produced_qty', 25, 4);
            $table->decimal('cost_total', 25, 4)->default(0);
            $table->decimal('overhead_cost', 25, 4)->default(0);
            $table->decimal('selling_price', 25, 4)->nullable();
            $table->foreignId('produced_by')->nullable()->constrained('users');
            $table->timestamp('produced_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('production_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_id')->constrained('productions')->cascadeOnDelete();
            $table->foreignId('ingredient_item_id')->constrained('items');
            $table->foreignId('unit_id')->constrained('units');
            $table->decimal('qty_used', 25, 8);
            $table->decimal('unit_cost', 25, 4)->default(0);
            $table->decimal('line_total_cost', 25, 4)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_components');
        Schema::dropIfExists('productions');
        Schema::dropIfExists('recipe_ingredients');
        Schema::dropIfExists('recipes');
    }
};
