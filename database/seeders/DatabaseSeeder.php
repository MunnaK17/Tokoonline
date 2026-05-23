<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Customer;
use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);

        // kode seed kategori & produk kamu lanjutkan di bawah sini
    }
}