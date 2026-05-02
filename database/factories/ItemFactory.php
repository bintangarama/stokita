<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####')),
            'name' => fake()->words(3, true),
            'type' => fake()->randomElement(['raw_material', 'component', 'finish_good']),
            'base_unit_id' => Unit::factory(),
            'track_stock' => true,
            'reorder_threshold' => fake()->randomFloat(2, 5, 20),
            'current_stock' => fake()->randomFloat(2, 0, 100),
            'average_cost' => fake()->randomFloat(2, 1000, 50000),
            'last_purchase_price' => fake()->randomFloat(2, 1000, 50000),
            'selling_price' => fake()->randomFloat(2, 1500, 100000),
        ];
    }
}
