<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class ServiceRecord extends Model
{
    use HasFactory;
    protected $fillable = ['booking_id', 'vehicle_id', 'service_id', 'inspection', 'work_done', 'notes', 'status', 'service_date'];
    protected $casts = ['service_date' => 'date'];
    public function booking() { return $this->belongsTo(Booking::class); }
    public function vehicle() { return $this->belongsTo(Vehicle::class); }
    public function service() { return $this->belongsTo(Service::class); }
}
