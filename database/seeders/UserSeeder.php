<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin Perpustakaan',
            'email' => 'admin@contoh.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'status_akun' => User::STATUS_AKTIF,
        ]);

        // Students
        $students = [
            [
                'name' => 'Alan',
                'email' => 'alan@contoh.com',
                'nis' => '11001',
                'jurusan' => 'PPLG',
                'kelas' => 'XII PPLG 1',
                'alamat' => 'Purwokerto',
                'no_hp' => '088812345678'
            ],
            [
                'name' => 'Darko',
                'email' => 'darko@contoh.com',
                'nis' => '11002',
                'jurusan' => 'PPLG',
                'kelas' => 'XII PPLG 1',
                'alamat' => 'Purwokerto',
                'no_hp' => '088812345678'
            ],
            [
                'name' => 'Nuha',
                'email' => 'nuha@contoh.com',
                'nis' => '11003',
                'jurusan' => 'PPLG',
                'kelas' => 'XII PPLG 1',
                'alamat' => 'Purwokerto',
                'no_hp' => '088812345678'
            ],
            [
                'name' => 'Rifqi',
                'email' => 'rifqi@contoh.com',
                'nis' => '11004',
                'jurusan' => 'PPLG',
                'kelas' => 'XII PPLG 1',
                'alamat' => 'Purwokerto',
                'no_hp' => '088812345678'
            ],
            [
                'name' => 'razya',
                'email' => 'razya@contoh.com',
                'nis' => '11005',
                'jurusan' => 'PPLG',
                'kelas' => 'XII PPLG 1',
                'alamat' => 'Purwokerto',
                'no_hp' => '088812345678'
            ]
        ];

        foreach ($students as $student) {
            $user = User::create([
                'name' => $student['name'],
                'email' => $student['email'],
                'password' => Hash::make('12345678'),
                'role' => User::ROLE_SISWA,
                'status_akun' => User::STATUS_AKTIF,
            ]);

            Student::create([
                'user_id' => $user->id,
                'nis' => $student['nis'],
                'jurusan' => $student['jurusan'],
                'kelas' => $student['kelas'],
                'tanggal_lahir' => now()->subYears(rand(15,18)),
                'alamat' => $student['alamat'],
                'no_hp' => $student['no_hp'],
                'status' => Student::STATUS_AKTIF,
            ]);
        }
    }
}
