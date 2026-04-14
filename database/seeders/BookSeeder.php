<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            [
                'judul' => 'Laravel untuk Pemula',
                'penulis' => 'Angga Pratama',
                'kategori' => 'Pemrograman'
            ],
            [
                'judul' => 'Belajar PHP Modern',
                'penulis' => 'Budi Santoso',
                'kategori' => 'Pemrograman'
            ],
            [
                'judul' => 'Dasar MySQL',
                'penulis' => 'Citra Lestari',
                'kategori' => 'Database'
            ],
            [
                'judul' => 'Jaringan Komputer Dasar',
                'penulis' => 'Dedi Saputra',
                'kategori' => 'Jaringan Komputer'
            ],
            [
                'judul' => 'Linux untuk Pemula',
                'penulis' => 'Eka Putri',
                'kategori' => 'Sistem Operasi'
            ],
            [
                'judul' => 'Bahasa Indonesia SMK',
                'penulis' => 'Tim Edukasi',
                'kategori' => 'Bahasa Indonesia'
            ],
            [
                'judul' => 'Bahasa Inggris SMK',
                'penulis' => 'Tim Edukasi',
                'kategori' => 'Bahasa Inggris'
            ],
            [
                'judul' => 'Matematika Diskrit',
                'penulis' => 'Andi Publisher',
                'kategori' => 'Matematika'
            ],
        ];

        foreach ($books as $index => $book) {
            $category = Category::where('name', $book['kategori'])->first();

            Book::create([
                'category_id' => $category->id,
                'kode_buku' => 'BK-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'judul' => $book['judul'],
                'penulis' => $book['penulis'],
                'penerbit' => 'Penerbit Edukasi',
                'tahun_terbit' => rand(2018, 2024),
                'isbn' => '978-602-' . rand(1000,9999),
                'jumlah_halaman' => rand(100, 400),
                'lokasi_rak' => 'Rak ' . chr(65 + rand(0,3)),
                'stok' => rand(3,10),
                'deskripsi' => 'Buku tentang ' . $book['judul']
            ]);
        }
    }
}
