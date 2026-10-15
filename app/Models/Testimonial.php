<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Testimonial extends Model
{
    use HasFactory;
    protected $fillable = ['customer_id', 'booking_id', 'rating', 'comment', 'status'];
    public function customer() { return $this->belongsTo(Customer::class); }
    public function booking() { return $this->belongsTo(Booking::class); }
}
