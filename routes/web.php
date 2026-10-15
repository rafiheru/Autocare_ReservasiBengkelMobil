<?php

use App\Http\Controllers\AutoCareController as AutoCare;
use Illuminate\Support\Facades\Route;

Route::get('/',[AutoCare::class,'home'])->name('home');
Route::get('/services',[AutoCare::class,'services'])->name('services.index');
Route::get('/services/{service}',[AutoCare::class,'service'])->name('services.show');
Route::get('/testimonials',[AutoCare::class,'testimonials'])->name('testimonials.public');
Route::view('/contact','public.contact')->name('contact');

Route::middleware('guest')->group(function(){
    Route::get('/login',[AutoCare::class,'loginForm'])->name('login');Route::post('/login',[AutoCare::class,'login'])->name('login.store');
    Route::get('/register',[AutoCare::class,'registerForm'])->name('register');Route::post('/register',[AutoCare::class,'register'])->name('register.store');
});
Route::post('/logout',[AutoCare::class,'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth','role:customer'])->prefix('customer')->name('customer.')->group(function(){
    Route::get('/dashboard',[AutoCare::class,'customerDashboard'])->name('dashboard');
    Route::get('/profile',[AutoCare::class,'profile'])->name('profile');Route::put('/profile',[AutoCare::class,'updateProfile'])->name('profile.update');
    Route::get('/vehicles',[AutoCare::class,'vehicles'])->name('vehicles.index');Route::get('/vehicles/create',[AutoCare::class,'vehicleCreate'])->name('vehicles.create');Route::post('/vehicles',[AutoCare::class,'vehicleStore'])->name('vehicles.store');Route::get('/vehicles/{vehicle}/edit',[AutoCare::class,'vehicleEdit'])->name('vehicles.edit');Route::put('/vehicles/{vehicle}',[AutoCare::class,'vehicleUpdate'])->name('vehicles.update');Route::delete('/vehicles/{vehicle}',[AutoCare::class,'vehicleDelete'])->name('vehicles.delete');
    Route::get('/bookings',[AutoCare::class,'bookings'])->name('bookings.index');Route::get('/bookings/create',[AutoCare::class,'bookingCreate'])->name('bookings.create');Route::post('/bookings',[AutoCare::class,'bookingStore'])->name('bookings.store');Route::get('/bookings/{booking}/success',[AutoCare::class,'bookingSuccess'])->name('bookings.success');Route::get('/bookings/{booking}',[AutoCare::class,'bookingShow'])->name('bookings.show');
    Route::get('/service-history',[AutoCare::class,'history'])->name('history.index');Route::get('/service-history/{serviceRecord}',[AutoCare::class,'historyShow'])->name('history.show');
    Route::get('/testimonials',[AutoCare::class,'ownTestimonials'])->name('testimonials.index');Route::post('/testimonials',[AutoCare::class,'testimonialStore'])->name('testimonials.store');
});

Route::middleware(['auth','role:admin'])->prefix('admin')->name('admin.')->group(function(){
    Route::get('/dashboard',[AutoCare::class,'adminDashboard'])->name('dashboard');
    Route::get('/bookings',[AutoCare::class,'adminBookings'])->name('bookings.index');Route::get('/bookings/{booking}',[AutoCare::class,'adminBooking'])->name('bookings.show');Route::patch('/bookings/{booking}/status',[AutoCare::class,'adminBookingStatus'])->name('bookings.status');
    Route::get('/services',[AutoCare::class,'adminServices'])->name('services.index');Route::get('/services/create',[AutoCare::class,'adminServiceCreate'])->name('services.create');Route::post('/services',[AutoCare::class,'adminServiceStore'])->name('services.store');Route::get('/services/{service}/edit',[AutoCare::class,'adminServiceEdit'])->name('services.edit');Route::put('/services/{service}',[AutoCare::class,'adminServiceUpdate'])->name('services.update');Route::delete('/services/{service}',[AutoCare::class,'adminServiceDelete'])->name('services.delete');
    Route::get('/customers',[AutoCare::class,'adminCustomers'])->name('customers.index');Route::get('/customers/{customer}',[AutoCare::class,'adminCustomer'])->name('customers.show');
    Route::get('/vehicles',[AutoCare::class,'adminVehicles'])->name('vehicles.index');
    Route::get('/service-records',[AutoCare::class,'adminRecords'])->name('records.index');Route::get('/service-records/create',[AutoCare::class,'adminRecordCreate'])->name('records.create');Route::post('/service-records',[AutoCare::class,'adminRecordStore'])->name('records.store');Route::get('/service-records/{serviceRecord}/edit',[AutoCare::class,'adminRecordEdit'])->name('records.edit');Route::put('/service-records/{serviceRecord}',[AutoCare::class,'adminRecordUpdate'])->name('records.update');Route::patch('/service-records/{serviceRecord}/status',[AutoCare::class,'adminRecordStatus'])->name('records.status');
    Route::get('/testimonials',[AutoCare::class,'adminTestimonials'])->name('testimonials.index');Route::patch('/testimonials/{testimonial}/status',[AutoCare::class,'adminTestimonialStatus'])->name('testimonials.status');
    Route::get('/profile',[AutoCare::class,'adminProfile'])->name('profile');Route::put('/profile',[AutoCare::class,'adminProfileUpdate'])->name('profile.update');
});
