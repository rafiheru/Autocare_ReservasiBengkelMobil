<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Customer extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'phone', 'address'];
    public function user() { return $this->belongsTo(User::class); }
    public function vehicles() { return $this->hasMany(Vehicle::class); }
    public function bookings() { return $this->hasMany(Booking::class); }
    public function testimonials() { return $this->hasMany(Testimonial::class); }
}
