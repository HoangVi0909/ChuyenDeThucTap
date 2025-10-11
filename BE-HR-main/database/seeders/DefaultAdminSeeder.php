<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class DefaultAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@hr-system.com',
            'password' => Hash::make('admin123'),
            'phone' => '0123456789',
            'address' => 'Hà Nội',
            'role' => 'super_admin',
            'status' => 'active'
        ]);
    }
}
