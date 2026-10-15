<?php
namespace Database\Factories;
use App\Models\{Booking, Testimonial};
use Illuminate\Database\Eloquent\Factories\Factory;
/** @extends Factory<Testimonial> */
class TestimonialFactory extends Factory
{
    protected $model = Testimonial::class;
    public function definition(): array { $booking=Booking::factory();return ['customer_id'=>fn(array $a)=>Booking::find($a['booking_id'])->customer_id,'booking_id'=>$booking,'rating'=>fake()->numberBetween(1,5),'comment'=>fake()->paragraph(),'status'=>'pending']; }
}
