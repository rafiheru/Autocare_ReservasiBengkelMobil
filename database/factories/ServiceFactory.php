<?php
namespace Database\Factories;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
/** @extends Factory<Service> */
class ServiceFactory extends Factory
{
    protected $model = Service::class;
    public function definition(): array { return ['name'=>fake()->unique()->words(2,true),'description'=>fake()->sentence(),'duration'=>fake()->randomElement([60,90,120]),'price'=>fake()->numberBetween(100000,1000000),'status'=>'active']; }
}
