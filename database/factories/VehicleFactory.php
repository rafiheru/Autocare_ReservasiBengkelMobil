<?php
namespace Database\Factories;
use App\Models\{Customer, Vehicle};
use Illuminate\Database\Eloquent\Factories\Factory;
/** @extends Factory<Vehicle> */
class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;
    public function definition(): array { return ['customer_id'=>Customer::factory(),'brand'=>fake()->randomElement(['Toyota','Honda','Suzuki']),'model'=>fake()->word(),'license_plate'=>strtoupper(fake()->unique()->bothify('B #### ???')),'year'=>fake()->numberBetween(2000,(int)date('Y'))]; }
}
