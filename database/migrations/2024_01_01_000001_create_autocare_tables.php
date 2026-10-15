<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('phone', 30)->nullable(); $table->text('address')->nullable(); $table->timestamps();
        });
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id(); $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('brand'); $table->string('model'); $table->string('license_plate')->unique();
            $table->unsignedSmallInteger('year'); $table->timestamps(); $table->index('customer_id');
        });
        Schema::create('services', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->text('description')->nullable();
            $table->unsignedInteger('duration'); $table->decimal('price', 12, 2); $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
        Schema::create('bookings', function (Blueprint $table) {
            $table->id(); $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->restrictOnDelete(); $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->date('booking_date'); $table->time('booking_time'); $table->text('complaint')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending'); $table->timestamps();
            $table->index(['booking_date', 'booking_time', 'status']); $table->index('customer_id');
        });
        Schema::create('service_records', function (Blueprint $table) {
            $table->id(); $table->foreignId('booking_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->restrictOnDelete(); $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->text('inspection')->nullable(); $table->text('work_done')->nullable(); $table->text('notes')->nullable();
            $table->enum('status', ['waiting', 'inspection', 'processing', 'completed', 'picked_up'])->default('waiting');
            $table->date('service_date')->nullable(); $table->timestamps(); $table->index(['vehicle_id', 'status']);
        });
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id(); $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating'); $table->text('comment');
            $table->enum('status', ['pending', 'published', 'hidden'])->default('pending'); $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials'); Schema::dropIfExists('service_records'); Schema::dropIfExists('bookings');
        Schema::dropIfExists('services'); Schema::dropIfExists('vehicles'); Schema::dropIfExists('customers');
    }
};
