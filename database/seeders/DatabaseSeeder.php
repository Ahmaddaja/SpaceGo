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

         Rak::create([
            'gudang_id' => 1,
            'kode_rak' => 'RAK-001',
            'nama_rak' => 'Rak Heavy Duty A1',
            'jenis_rak' => 'Heavy Duty',
            'deskripsi' => 'Rak untuk barang berat hingga 2000kg.',
            'kapasitas_berat' => 2000,
            'panjang' => 2.5,
            'lebar' => 1.2,
            'tinggi' => 2.0,
            'jumlah_tingkat' => 4,
            'lokasi_gudang' => 'Blok A',
            'harga_sewa_perbulan' => 500000,
            'status' => 'tersedia',
        ]);

        Rak::create([
            'gudang_id' => 1,
            'kode_rak' => 'RAK-002',
            'nama_rak' => 'Rak Medium Duty B2',
            'jenis_rak' => 'Medium Duty',
            'deskripsi' => 'Rak untuk barang hingga 500kg.',
            'kapasitas_berat' => 500,
            'panjang' => 2.0,
            'lebar' => 1.0,
            'tinggi' => 1.8,
            'jumlah_tingkat' => 3,
            'lokasi_gudang' => 'Blok B',
            'harga_sewa_perbulan' => 300000,
            'status' => 'tersedia',
        ]);

        // Rak untuk Gudang 2
        Rak::create([
            'gudang_id' => 2,
            'kode_rak' => 'RAK-003',
            'nama_rak' => 'Rak Light Duty C1',
            'jenis_rak' => 'Light Duty',
            'deskripsi' => 'Rak untuk barang ringan hingga 200kg.',
            'kapasitas_berat' => 200,
            'panjang' => 1.5,
            'lebar' => 0.8,
            'tinggi' => 1.5,
            'jumlah_tingkat' => 3,
            'lokasi_gudang' => 'Blok C',
            'harga_sewa_perbulan' => 150000,
            'status' => 'tersedia',
        ]);

        Rak::create([
            'nama_rak' => 'Rak A1',
            'ukuran' => '2 x 2 m',
            'lokasi' => 'Gudang 1',
            'status' => 'tersedia',
            'harga_sewa_perbulan' => 300000,
        ]);

        Rak::create([
            'nama_rak' => 'Rak B2',
            'ukuran' => '3 x 2 m',
            'lokasi' => 'Gudang 1',
            'status' => 'disewa',
            'harga_sewa_perbulan' => 350000,
        ]);

        Rak::create([
            'nama_rak' => 'Rak C3',
            'ukuran' => '4 x 3 m',
            'lokasi' => 'Gudang 2',
            'status' => 'disewa',
            'harga_sewa_perbulan' => 400000,
        ]);

        Transaction::create([
            'order_id' => 'ORDER-TEST-001',
            'user_id' => 1,
            'rak_id' => 2,
            'amount' => 300000,
            'payment_type' => 'bank_transfer',
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'transaction_time' => $now->copy()->subDays(29),
            'snap_token' => 'token1',
            'midtrans_response' => null,
            'sewa_mulai' => $now->copy()->subDays(29),
            'sewa_berakhir' => $now->copy()->addHours(10), // tinggal 10 jam
        ]);

        // 2️⃣ Sewa tinggal 10 menit lagi berakhir
        Transaction::create([
            'order_id' => 'ORDER-TEST-002',
            'user_id' => 1,
            'rak_id' => 3,
            'amount' => 350000,
            'payment_type' => 'bank_transfer',
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'transaction_time' => $now->copy()->subDays(29)->subHours(23),
            'snap_token' => 'token2',
            'midtrans_response' => null,
            'sewa_mulai' => $now->copy()->subDays(29),
            'sewa_berakhir' => $now->copy()->addMinutes(10), // tinggal 10 menit
        ]);

        // 3️⃣ Sewa yang sudah melewati masa tenggang (expired 5 hari)
        Transaction::create([
            'order_id' => 'ORDER-TEST-003',
            'user_id' => 1,
            'rak_id' => 3,
            'amount' => 400000,
            'payment_type' => 'bank_transfer',
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'transaction_time' => $now->copy()->subDays(35),
            'snap_token' => 'token3',
            'midtrans_response' => null,
            'sewa_mulai' => $now->copy()->subDays(35),
            'sewa_berakhir' => $now->copy()->subDays(5), // lewat 5 hari
        ]);

    }
}
