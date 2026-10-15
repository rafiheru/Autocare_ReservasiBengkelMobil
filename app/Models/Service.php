<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Service extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'duration', 'price', 'status'];
    protected $casts = ['price' => 'decimal:2'];
    public function bookings() { return $this->hasMany(Booking::class); }
    public function serviceRecords() { return $this->hasMany(ServiceRecord::class); }
}
