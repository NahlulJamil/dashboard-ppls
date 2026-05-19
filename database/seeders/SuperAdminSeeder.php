<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'email' => 'bpa.telkomuniversity@gmail.com',
            'username' => null,
            'password' => Hash::make('@Teknolog1!'),
            'role' => 'super_admin',
        ]);
    }
}
