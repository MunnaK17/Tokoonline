<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Customer;
use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate([
            'email' => 'admin@yahoo.com',
        ], [
            'nama' => 'Admin',
            'role' => '0',
            'status' => 1,
            'hp' => null,
            'password' => bcrypt('admin123'),
        ]);

        $customerUser = User::updateOrCreate([
            'email' => 'obattss@yahoo.com',
        ], [
            'nama' => 'obattss',
            'role' => '2',
            'status' => 1,
            'hp' => null,
            'password' => bcrypt('user123123'),
        ]);

        Customer::updateOrCreate([
            'user_id' => $customerUser->id,
        ], [
            'google_id' => null,
            'google_token' => null,
            'alamat' => null,
            'pos' => null,
        ]);

        $produkByKategori = [
            'Beer' => [
                ['Root Beer Botol', 18000], ['Ginger Beer Soda', 22000], ['Bir Pletok Betawi', 15000], ['Apple Malt Drink', 24000], ['Lemon Malt Sparkle', 21000],
                ['Sarsaparilla Classic', 19000], ['Honey Malt Zero', 25000], ['Berry Malt Fizz', 23000], ['Vanilla Root Beer', 20000], ['Tropical Malt Drink', 26000],
            ],
            'Brownies' => [
                ['Brownies Cokelat Lumer', 45000], ['Brownies Keju Panggang', 48000], ['Brownies Kukus Pandan', 42000], ['Brownies Almond', 52000], ['Brownies Red Velvet', 50000],
                ['Brownies Matcha', 49000], ['Brownies Tiramisu', 51000], ['Brownies Kacang Mede', 53000], ['Brownies Choco Chips', 47000], ['Brownies Mini Box', 35000],
            ],
            'Combro' => [
                ['Combro Original Pedas', 12000], ['Combro Oncom Keju', 16000], ['Combro Ayam Suwir', 18000], ['Combro Mini Isi 10', 22000], ['Combro Pedas Level 3', 14000],
                ['Combro Mozarella', 19000], ['Combro Kriuk', 13000], ['Combro Frozen Pack', 30000], ['Combro Sambal Ijo', 15000], ['Combro Isi Tempe', 12000],
            ],
            'Dawet' => [
                ['Dawet Ayu Original', 12000], ['Dawet Gula Aren', 14000], ['Dawet Durian', 22000], ['Dawet Nangka', 16000], ['Dawet Cokelat', 15000],
                ['Dawet Pandan Susu', 15000], ['Dawet Tape Ketan', 17000], ['Dawet Alpukat', 20000], ['Dawet Kelapa Muda', 18000], ['Dawet Family Pack', 45000],
            ],
            'Katsu' => [
                ['Chicken Katsu Original', 28000], ['Chicken Katsu Curry', 35000], ['Katsu Mentai Rice', 38000], ['Cheese Katsu', 36000], ['Spicy Katsu Bowl', 33000],
                ['Katsu Teriyaki', 34000], ['Katsu Sambal Matah', 32000], ['Katsu Donburi', 37000], ['Mini Katsu Bento', 30000], ['Katsu Frozen Pack', 55000],
            ],
            'Mochi' => [
                ['Mochi Kacang Original', 25000], ['Mochi Cokelat', 28000], ['Mochi Matcha', 30000], ['Mochi Strawberry', 29000], ['Mochi Durian', 35000],
                ['Mochi Keju', 31000], ['Mochi Wijen Hitam', 32000], ['Mochi Pandan', 27000], ['Mochi Ice Cream', 38000], ['Mochi Mix Box', 45000],
            ],
            'Wingko' => [
                ['Wingko Babat Original', 23000], ['Wingko Kelapa Muda', 25000], ['Wingko Cokelat', 26000], ['Wingko Keju', 28000], ['Wingko Pandan', 24000],
                ['Wingko Nangka', 27000], ['Wingko Durian', 32000], ['Wingko Mini Box', 35000], ['Wingko Panggang Premium', 30000], ['Wingko Mix Rasa', 42000],
            ],
        ];

        $sampleImages = [
            public_path('frontend/img/product01.jpg'),
            public_path('frontend/img/product02.jpg'),
            public_path('frontend/img/product03.jpg'),
            public_path('frontend/img/product04.jpg'),
            public_path('frontend/img/product05.jpg'),
            public_path('frontend/img/product06.jpg'),
            public_path('frontend/img/product07.jpg'),
            public_path('frontend/img/product08.jpg'),
        ];

        $destination = public_path('storage/img-produk');
        if (!File::isDirectory($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        foreach ($produkByKategori as $namaKategori => $items) {
            $kategori = Kategori::updateOrCreate(
                ['nama_kategori' => $namaKategori],
                ['nama_kategori' => $namaKategori]
            );

            foreach ($items as $index => [$namaProduk, $harga]) {
                $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $namaProduk));
                $fileName = 'seed_' . $slug . '.jpg';
                $source = $sampleImages[$index % count($sampleImages)];

                foreach (['', 'thumb_lg_', 'thumb_md_', 'thumb_sm_'] as $prefix) {
                    $target = $destination . DIRECTORY_SEPARATOR . $prefix . $fileName;
                    if (File::exists($source) && !File::exists($target)) {
                        File::copy($source, $target);
                    }
                }

                Produk::updateOrCreate(
                    ['nama_produk' => $namaProduk],
                    [
                        'kategori_id' => $kategori->id,
                        'user_id' => $admin->id,
                        'status' => 1,
                        'detail' => '<p>' . $namaProduk . ' dibuat dari bahan pilihan dan siap dipesan dari katalog toko.</p>',
                        'harga' => $harga,
                        'stok' => 50,
                        'berat' => 250,
                        'foto' => $fileName,
                    ]
                );
            }
        }
    }
}
