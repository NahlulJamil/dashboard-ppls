<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'email' => null,
            'username' => 'nahluljamil',
            'password' => Hash::make('Admin123!'),
            'role' => 'admin',
        ]);
    }
}
