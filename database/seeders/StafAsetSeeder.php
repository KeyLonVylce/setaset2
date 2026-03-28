<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StafAset;

class StafAsetSeeder extends Seeder
{
    public function run(): void
    {
        StafAset::create([
            'username' => 'admin',
            'nama' => 'Administrator',
            'nip' => '199001012020121001',
            'email' => 'admin@gmail.com',
            'password' => 'admin123',
            'role' => 'admin',
        ]);

        StafAset::create([
            'username' => 'staff',
            'nama' => 'Staff Aset',
            'nip' => '199505052021011001',
            'email' => 'staf@gmail.com',
            'password' => 'staff123',
            'role' => 'staff',
        ]);
    }
}
