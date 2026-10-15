<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Booking, Customer, Service, ServiceRecord, Testimonial, Vehicle};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    private function customer(Request $request): Customer { return $request->user()->customer()->firstOrFail(); }
    public function dashboard(Request $r) {
        $c=$this->customer($r); return response()->json(['success'=>true,'data'=>['total_vehicles'=>$c->vehicles()->count(),'total_bookings'=>$c->bookings()->count(),'active_booking'=>$c->bookings()->whereIn('status',['pending','confirmed'])->with(['vehicle','service'])->latest()->first(),'total_service_records'=>ServiceRecord::whereHas('booking',fn($q)=>$q->where('customer_id',$c->id))->count(),'recent_service_history'=>ServiceRecord::whereHas('booking',fn($q)=>$q->where('customer_id',$c->id))->with(['vehicle','service'])->latest()->limit(5)->get()]]);
    }
    public function profile(Request $r) { return response()->json(['success'=>true,'data'=>$r->user()->load('customer')]); }
    public function updateProfile(Request $r) {
        $d=$r->validate(['name'=>'required|string|max:255','phone'=>'nullable|string|max:30','address'=>'nullable|string']);
        DB::transaction(function()use($r,$d){$r->user()->update(['name'=>$d['name']]);$this->customer($r)->update(['phone'=>$d['phone']??null,'address'=>$d['address']??null]);});
        return $this->profile($r);
    }
    public function vehicles(Request $r) { return response()->json(['success'=>true,'data'=>$this->customer($r)->vehicles()->latest()->paginate(15)]); }
    public function storeVehicle(Request $r) { $d=$r->validate(['brand'=>'required|string|max:100','model'=>'required|string|max:100','license_plate'=>'required|string|max:30|unique:vehicles,license_plate','year'=>'required|integer|min:1900|max:'.(date('Y')+1)]); return response()->json(['success'=>true,'message'=>'Kendaraan berhasil ditambahkan.','data'=>$this->customer($r)->vehicles()->create($d)],201); }
    public function showVehicle(Request $r, Vehicle $vehicle) { abort_unless($vehicle->customer_id===$this->customer($r)->id,404); return response()->json(['success'=>true,'data'=>$vehicle]); }
    public function updateVehicle(Request $r, Vehicle $vehicle) { abort_unless($vehicle->customer_id===$this->customer($r)->id,404); $d=$r->validate(['brand'=>'sometimes|required|string|max:100','model'=>'sometimes|required|string|max:100','license_plate'=>'sometimes|required|string|max:30|unique:vehicles,license_plate,'.$vehicle->id,'year'=>'sometimes|required|integer|min:1900|max:'.(date('Y')+1)]); $vehicle->update($d); return response()->json(['success'=>true,'data'=>$vehicle]); }
    public function deleteVehicle(Request $r, Vehicle $vehicle) { abort_unless($vehicle->customer_id===$this->customer($r)->id,404); if($vehicle->bookings()->exists())return response()->json(['success'=>false,'message'=>'Kendaraan memiliki riwayat booking dan tidak dapat dihapus.'],409);$vehicle->delete();return response()->json(null,204); }
    public function services() { return response()->json(['success'=>true,'data'=>Service::where('status','active')->orderBy('name')->paginate(15)]); }
    public function bookings(Request $r) { return response()->json(['success'=>true,'data'=>$this->customer($r)->bookings()->with(['vehicle','service','serviceRecord'])->latest()->paginate(15)]); }
    public function storeBooking(Request $r) {
        $d=$r->validate(['vehicle_id'=>'required|integer','service_id'=>'required|exists:services,id','booking_date'=>'required|date|after_or_equal:today','booking_time'=>'required|date_format:H:i','complaint'=>'nullable|string|max:2000']);$c=$this->customer($r);
        return DB::transaction(function()use($d,$c){$vehicle=$c->vehicles()->findOrFail($d['vehicle_id']);$service=Service::where('status','active')->findOrFail($d['service_id']);
            $start=now()->parse($d['booking_date'].' '.$d['booking_time']);$end=$start->copy()->addMinutes($service->duration);
            $conflict=Booking::where('booking_date',$d['booking_date'])->whereIn('status',['pending','confirmed'])->with('service')->lockForUpdate()->get()->contains(function($b)use($start,$end){$bstart=now()->parse($b->booking_date->format('Y-m-d').' '.$b->booking_time);$bend=$bstart->copy()->addMinutes($b->service->duration);return $start->lt($bend)&&$bstart->lt($end);});
            if($conflict)throw ValidationException::withMessages(['booking_time'=>['Jadwal tersebut sudah digunakan.']]);
            $booking=$c->bookings()->create(['vehicle_id'=>$vehicle->id,'service_id'=>$service->id,'booking_date'=>$d['booking_date'],'booking_time'=>$d['booking_time'],'complaint'=>$d['complaint']??null,'status'=>'pending']);return response()->json(['success'=>true,'message'=>'Booking berhasil dibuat.','data'=>$booking->load(['vehicle','service'])],201);
        });
    }
    public function showBooking(Request $r, Booking $booking) { abort_unless($booking->customer_id===$this->customer($r)->id,404);return response()->json(['success'=>true,'data'=>$booking->load(['vehicle','service','serviceRecord'])]); }
    public function history(Request $r) { return response()->json(['success'=>true,'data'=>ServiceRecord::whereHas('booking',fn($q)=>$q->where('customer_id',$this->customer($r)->id))->with(['vehicle','service','booking'])->latest()->paginate(15)]); }
    public function showHistory(Request $r, ServiceRecord $serviceRecord) { abort_unless($serviceRecord->booking()->where('customer_id',$this->customer($r)->id)->exists(),404);return response()->json(['success'=>true,'data'=>$serviceRecord->load(['vehicle','service','booking'])]); }
    public function testimonials(Request $r) { return response()->json(['success'=>true,'data'=>$this->customer($r)->testimonials()->with('booking')->latest()->paginate(15)]); }
    public function storeTestimonial(Request $r) {
        $d=$r->validate(['booking_id'=>'required|integer','rating'=>'required|integer|between:1,5','comment'=>'required|string|max:3000']);$c=$this->customer($r);
        $b=$c->bookings()->with('serviceRecord')->findOrFail($d['booking_id']);if(!$b->serviceRecord||!in_array($b->serviceRecord->status,['completed','picked_up']))throw ValidationException::withMessages(['booking_id'=>['Testimoni hanya dapat diberikan setelah servis selesai.']]);if($b->testimonial()->exists())throw ValidationException::withMessages(['booking_id'=>['Booking ini sudah memiliki testimoni.']]);
        return response()->json(['success'=>true,'message'=>'Testimoni berhasil dikirim.','data'=>$c->testimonials()->create($d+['status'=>'pending'])],201);
    }
}
