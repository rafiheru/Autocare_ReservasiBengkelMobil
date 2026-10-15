<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Booking extends Model
{
    use HasFactory;
    protected $fillable = ['customer_id', 'vehicle_id', 'service_id', 'booking_date', 'booking_time', 'complaint', 'status'];
    protected $casts = ['booking_date' => 'date'];
    public function customer() { return $this->belongsTo(Customer::class); }
    public function vehicle() { return $this->belongsTo(Vehicle::class); }
    public function service() { return $this->belongsTo(Service::class); }
    public function serviceRecord() { return $this->hasOne(ServiceRecord::class); }
    public function testimonial() { return $this->hasOne(Testimonial::class); }
}
