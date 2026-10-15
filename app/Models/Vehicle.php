<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Vehicle extends Model
{
    use HasFactory;
    protected $fillable = ['customer_id', 'brand', 'model', 'license_plate', 'year'];
    public function customer() { return $this->belongsTo(Customer::class); }
    public function bookings() { return $this->hasMany(Booking::class); }
    public function serviceRecords() { return $this->hasMany(ServiceRecord::class); }
}
