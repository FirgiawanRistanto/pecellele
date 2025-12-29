<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('menus')->delete();

        $defaultMenu = [
            [ 'name' => 'Pecel Lele', 'price' => 15000, 'image' => 'pecel_lele.jpg', 'description' => 'Lele goreng renyah disajikan dengan sambal terasi pedas dan lalapan segar.', 'stock' => 10 ],
            [ 'name' => 'Pecel Ayam', 'price' => 17000, 'image' => 'pecel_ayam.jpg', 'description' => 'Ayam goreng empuk disajikan dengan sambal terasi pedas dan lalapan.', 'stock' => 10 ],
            [ 'name' => 'Ayam Geprek', 'price' => 18000, 'image' => 'ayam_geprek.jpg', 'description' => 'Ayam krispi digeprek dengan sambal bawang super pedas, level 1-5.', 'stock' => 10 ],
            [ 'name' => 'Nila Bakar', 'price' => 22000, 'image' => 'nila_bakar.jpg', 'description' => 'Ikan nila segar dibakar dengan bumbu manis gurih, disajikan dengan sambal kecap.', 'stock' => 10 ],
            [ 'name' => 'Seblak Original', 'price' => 12000, 'image' => 'seblak.jpg', 'description' => 'Kerupuk basah dimasak dengan bumbu pedas khas bandung, lengkap dengan sayur dan telur.', 'stock' => 10 ],
            [ 'name' => 'Es Teh Manis', 'price' => 5000, 'image' => 'esteh.jpg', 'description' => 'Minuman teh manis dingin yang menyegarkan dahaga.', 'stock' => 10 ],
            [ 'name' => 'Es Jeruk', 'price' => 6000, 'image' => 'esjeruk.jpg', 'description' => 'Es jeruk peras asli, kaya vitamin C dan menyegarkan.', 'stock' => 10 ],
        ];

        foreach ($defaultMenu as $menu) {
            Menu::create($menu);
        }
    }
}
