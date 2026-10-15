<?php
namespace Database\Factories;
use App\Models\{Booking, Customer, Service, Vehicle};
use Illuminate\Database\Eloquent\Factories\Factory;
/** @extends Factory<Booking> */
class BookingFactory extends Factory
{
    protected $model = Booking::class;
    public function definition(): array { $customer=Customer::factory();return ['customer_id'=>$customer,'vehicle_id'=>Vehicle::factory()->for($customer),'service_id'=>Service::factory(),'booking_date'=>fake()->dateTimeBetween('now','+30 days')->format('Y-m-d'),'booking_time'=>fake()->randomElement(['09:00','11:00','14:00']),'complaint'=>fake()->sentence(),'status'=>'pending']; }
}
