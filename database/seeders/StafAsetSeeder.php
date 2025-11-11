<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\StafAset;

class StafAsetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StafAset::create([
            'username' => 'admin',
            'nama' => 'Administrator',
            'nip' => '199001012020121001',
            'password' => Hash::make('admin123'),
            'can_edit' => 1,
        ]);

        StafAset::create([
            'username' => 'staff',
            'nama' => 'Staff Aset',
            'nip' => '199505052021011001',
            'password' => Hash::make('staff123'),
            'can_edit' => 1,
        ]);
    }
}