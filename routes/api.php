<?php

use App\Http\Controllers\Api\{AdminController, AuthController, CustomerController};
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/services', fn () => response()->json(['success'=>true,'data'=>Service::where('status','active')->orderBy('name')->paginate(15)]));
Route::get('/services/{service}', function (int $service) { $item=Service::where('status','active')->findOrFail($service);return response()->json(['success'=>true,'data'=>$item]); });
Route::get('/testimonials', fn () => response()->json(['success'=>true,'data'=>\App\Models\Testimonial::where('status','published')->with(['customer.user','booking.vehicle'])->latest()->paginate(15)]));
Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class,'logout']);
    Route::middleware('role:customer')->prefix('customer')->group(function () {
        Route::get('/dashboard',[CustomerController::class,'dashboard']);
        Route::get('/profile',[CustomerController::class,'profile']); Route::put('/profile',[CustomerController::class,'updateProfile']);
        Route::get('/vehicles',[CustomerController::class,'vehicles']); Route::post('/vehicles',[CustomerController::class,'storeVehicle']);
        Route::get('/vehicles/{vehicle}',[CustomerController::class,'showVehicle']); Route::put('/vehicles/{vehicle}',[CustomerController::class,'updateVehicle']); Route::delete('/vehicles/{vehicle}',[CustomerController::class,'deleteVehicle']);
        Route::get('/services',[CustomerController::class,'services']);
        Route::get('/bookings',[CustomerController::class,'bookings']); Route::post('/bookings',[CustomerController::class,'storeBooking']); Route::get('/bookings/{booking}',[CustomerController::class,'showBooking']);
        Route::get('/service-history',[CustomerController::class,'history']); Route::get('/service-history/{serviceRecord}',[CustomerController::class,'showHistory']);
        Route::get('/testimonials',[CustomerController::class,'testimonials']); Route::post('/testimonials',[CustomerController::class,'storeTestimonial']);
    });
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard',[AdminController::class,'dashboard']);
        Route::get('/bookings',[AdminController::class,'bookings']); Route::get('/bookings/{booking}',[AdminController::class,'showBooking']); Route::patch('/bookings/{booking}/status',[AdminController::class,'changeBookingStatus']);
        Route::get('/services',[AdminController::class,'services']); Route::post('/services',[AdminController::class,'storeService']); Route::get('/services/{service}',[AdminController::class,'showService']); Route::put('/services/{service}',[AdminController::class,'updateService']); Route::delete('/services/{service}',[AdminController::class,'deleteService']);
        Route::get('/customers',[AdminController::class,'customers']); Route::get('/customers/{customer}',[AdminController::class,'showCustomer']);
        Route::get('/vehicles',[AdminController::class,'vehicles']); Route::get('/vehicles/{vehicle}',[AdminController::class,'showVehicle']);
        Route::get('/service-records',[AdminController::class,'records']); Route::post('/service-records',[AdminController::class,'storeRecord']); Route::put('/service-records/{serviceRecord}',[AdminController::class,'updateRecord']); Route::patch('/service-records/{serviceRecord}/status',[AdminController::class,'changeRecordStatus']);
        Route::get('/testimonials',[AdminController::class,'testimonials']); Route::get('/testimonials/{testimonial}',[AdminController::class,'showTestimonial']); Route::patch('/testimonials/{testimonial}/status',[AdminController::class,'changeTestimonialStatus']);
        Route::get('/profile',fn(Request $r)=>response()->json(['success'=>true,'data'=>$r->user()]));
        Route::put('/profile',function(Request $r){$data=$r->validate(['name'=>'required|string|max:255']);$r->user()->update($data);return response()->json(['success'=>true,'data'=>$r->user()->fresh()]);});
    });
});
