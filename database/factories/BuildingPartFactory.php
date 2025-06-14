<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BuildingPart>
 */
class BuildingPartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

 public function definition()
{
    return [
        'project_id' => \App\Models\Project::factory(),
        'name' => $this->faker->word,
        'building_part_type' => $this->faker->randomElement(['floor', 'wall', 'beam', 'column']),
        'material_type' => $this->faker->randomElement(['CLT', 'GLT']),
        'supplier_name' => $this->faker->company,
    ];
}

}
