<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Nabila A',
            'nama' => 'Nabila A',
            'email' => 'nabila@admin.com',
            'username' => 'Nabila-Admin1',
            'password' => Hash::make('password'),
        ]);
    }
}
