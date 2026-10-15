<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate(['email' => 'admin@bengkel.com'], [
            'name' => 'Admin Bengkel', 'password' => 'password123', 'role' => 'admin',
        ]);

        $customerUser = \App\Models\User::updateOrCreate(['email' => 'user@bengkel.com'], [
            'name' => 'Customer Bengkel', 'password' => 'password123', 'role' => 'customer',
        ]);
        $customerUser->customer()->firstOrCreate([], ['phone' => '081234567890', 'address' => 'Alamat customer']);

        foreach ([
            ['Ganti Oli', 'Penggantian oli mesin dan pemeriksaan dasar.', 60, 250000],
            ['Tune Up', 'Pemeriksaan dan penyetelan performa mesin.', 120, 500000],
            ['Service AC', 'Pemeriksaan dan perawatan sistem pendingin kabin.', 120, 400000],
            ['Service Mesin', 'Pemeriksaan menyeluruh dan perawatan mesin.', 180, 750000],
        ] as [$name, $description, $duration, $price]) {
            \App\Models\Service::firstOrCreate(['name' => $name], compact('description', 'duration', 'price') + ['status' => 'active']);
        }
    }
}
