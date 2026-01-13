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
            'password' => 'admin123',
            'role' => 'admin',
            'can_edit' => true,
        ]);

        StafAset::create([
            'username' => 'staff',
            'nama' => 'Staff Aset',
            'nip' => '199505052021011001',
            'password' => 'staff123',
            'role' => 'staff',
            'can_edit' => true,
        ]);
    }
}
