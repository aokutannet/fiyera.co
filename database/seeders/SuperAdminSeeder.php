<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\SuperAdmin::create([
            'name' => 'Super Admin',
            'email' => 'admin@fiyera.co',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);
    }
}
