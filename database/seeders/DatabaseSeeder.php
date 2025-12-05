<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Gudang;
use App\Models\Rak;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = now();

        // SEED ADMIN
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'phone' => '081234567890',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // SEED CUSTOMER
        User::create([
            'name' => 'Customer Utama',
            'username' => 'customer',
            'email' => 'customer@example.com',
            'phone' => '089876543210',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        // GUDANG 1
        Gudang::create([
            'kode_gudang' => 'GDG-001',
            'nama_gudang' => 'Gudang Utama Jakarta',
            'alamat' => 'Jl. Industri Raya No. 12',
            'kota' => 'Jakarta',
            'provinsi' => 'DKI Jakarta',
            'kode_pos' => '10110',
            'deskripsi' => 'Gudang besar dengan fasilitas penyimpanan lengkap.',
            'foto' => null,
        ]);

        // GUDANG 2
        Gudang::create([
            'kode_gudang' => 'GDG-002',
            'nama_gudang' => 'Gudang Bandung Timur',
            'alamat' => 'Jl. Soekarno Hatta No. 88',
            'kota' => 'Bandung',
            'provinsi' => 'Jawa Barat',
            'kode_pos' => '40292',
            'deskripsi' => 'Gudang penyimpanan barang ringan hingga menengah.',
            'foto' => null,
        ]);

        //RAK

        $rak1 = Rak::create([
            'gudang_id' => 1,
            'kode_rak' => 'RAK-005',
            'nama_rak' => 'Rak B2 (Custom)',
            'jenis_rak' => 'Medium Duty',
            'kapasitas_berat' => 350,
            'panjang' => 3.0,
            'lebar' => 2.0,
            'tinggi' => 2.0,
            'jumlah_tingkat' => 3,
            'lokasi_gudang' => 'Gudang 1 - Area B',
            'harga_sewa_perbulan' => 350000,
            'status' => 'tersedia',
        ]);

        // ============================
        // RAK GUDANG 2
        // ============================
        $rak2 = Rak::create([
            'gudang_id' => 2,
            'kode_rak' => 'RAK-006',
            'nama_rak' => 'Rak C3 (Custom)',
            'jenis_rak' => 'Light Duty',
            'kapasitas_berat' => 400,
            'panjang' => 4.0,
            'lebar' => 3.0,
            'tinggi' => 2.2,
            'jumlah_tingkat' => 3,
            'lokasi_gudang' => 'Gudang 2 - Area C',
            'harga_sewa_perbulan' => 400000,
            'status' => 'tersedia',
        ]);

        $rak2 = Rak::create([
            'gudang_id' => 2,
            'kode_rak' => 'RAK-007',
            'nama_rak' => 'Rak D3 (Custom)',
            'jenis_rak' => 'Light Duty',
            'kapasitas_berat' => 200,
            'panjang' => 4.0,
            'lebar' => 3.0,
            'tinggi' => 2.2,
            'jumlah_tingkat' => 3,
            'lokasi_gudang' => 'Gudang 2 - Area A',
            'harga_sewa_perbulan' => 250000,
            'durasi_sewa_hari' => 2,
            'status' => 'tersedia',
        ]);

    }
}
