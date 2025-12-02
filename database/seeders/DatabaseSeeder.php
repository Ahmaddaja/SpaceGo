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

        // ============================
        // RAK GUDANG 1
        // ============================

        // Rak 1
        $rak1 = Rak::create([
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

        // Rak 2
        $rak2 = Rak::create([
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

        // ============================
        // RAK TAMBAHAN GUDANG 1 (REVISI dari yang sebelumnya error)
        // ============================

        $rak3 = Rak::create([
            'gudang_id' => 1,
            'kode_rak' => 'RAK-004',
            'nama_rak' => 'Rak A1 (Custom)',
            'jenis_rak' => 'Light Duty',
            'kapasitas_berat' => 200,
            'panjang' => 2.0,
            'lebar' => 2.0,
            'tinggi' => 1.8,
            'jumlah_tingkat' => 3,
            'lokasi_gudang' => 'Gudang 1 - Area A',
            'harga_sewa_perbulan' => 300000,
            'status' => 'tersedia',
        ]);

        $rak4 = Rak::create([
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
            'status' => 'terisi',
        ]);

        // ============================
        // RAK GUDANG 2
        // ============================
        $rak5 = Rak::create([
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
            'status' => 'terisi',
        ]);

        // ============================
        // TRANSAKSI TESTING
        // ============================

        Transaction::create([
            'order_id' => 'ORDER-TEST-001',
            'user_id' => 1,
            'rak_id' => $rak2->id,
            'amount' => 300000,
            'payment_type' => 'bank_transfer',
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'transaction_time' => $now->copy()->subDays(29),
            'snap_token' => 'token1',
            'midtrans_response' => null,
            'sewa_mulai' => $now->copy()->subDays(29),
            'sewa_berakhir' => $now->copy()->addHours(10),
        ]);

        Transaction::create([
            'order_id' => 'ORDER-TEST-002',
            'user_id' => 1,
            'rak_id' => $rak3->id,
            'amount' => 350000,
            'payment_type' => 'bank_transfer',
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'transaction_time' => $now->copy()->subDays(29)->subHours(23),
            'snap_token' => 'token2',
            'midtrans_response' => null,
            'sewa_mulai' => $now->copy()->subDays(29),
            'sewa_berakhir' => $now->copy()->addMinutes(10),
        ]);

        Transaction::create([
            'order_id' => 'ORDER-TEST-003',
            'user_id' => 1,
            'rak_id' => $rak4->id,
            'amount' => 400000,
            'payment_type' => 'bank_transfer',
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'transaction_time' => $now->copy()->subDays(35),
            'snap_token' => 'token3',
            'midtrans_response' => null,
            'sewa_mulai' => $now->copy()->subDays(35),
            'sewa_berakhir' => $now->copy()->subDays(5),
        ]);
    }
}
