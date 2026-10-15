<?php
namespace Database\Factories;
use App\Models\{Booking, ServiceRecord};
use Illuminate\Database\Eloquent\Factories\Factory;
/** @extends Factory<ServiceRecord> */
class ServiceRecordFactory extends Factory
{
    protected $model = ServiceRecord::class;
    public function definition(): array { $booking=Booking::factory();return ['booking_id'=>$booking,'vehicle_id'=>fn(array $a)=>Booking::find($a['booking_id'])->vehicle_id,'service_id'=>fn(array $a)=>Booking::find($a['booking_id'])->service_id,'inspection'=>fake()->sentence(),'work_done'=>fake()->sentence(),'notes'=>null,'status'=>'waiting','service_date'=>today()]; }
}
