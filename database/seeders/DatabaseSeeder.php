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

        // =========================================
// TRANSAKSI CUSTOMER UNTUK RAK 2
// =========================================

$customer = User::where('role', 'customer')->first();
$rak2 = Rak::find(2); // Rak-006

if ($customer && $rak2) {

    $transaction = Transaction::create([
        'order_id' => 'ORDER-' . now()->format('YmdHis') . '-R2',
        'user_id' => $customer->id,
        'rak_id' => $rak2->id,
        'amount' => $rak2->harga_sewa_perbulan,
        'transaction_status' => 'settlement',
        'snap_token' => 'snap_test_rak2',
        'payment_type' => 'bank_transfer',
        'transaction_time' => now(),
        'fraud_status' => 'accept',
        'is_renewal' => false,
        'penalty_amount' => 0,
    ]);

    // ============================
    // AUTO GENERATE TAGIHAN RAK 2
    // ============================

    $tagihanCode = 'BILL-' . strtoupper(uniqid());

    \App\Models\Tagihan::create([
        'tagihan_code' => $tagihanCode,
        'transaction_id' => $transaction->id,
        'user_id' => $customer->id,
        'rak_id' => $rak2->id,

        // Detail
        'harga_sewa' => $rak2->harga_sewa_perbulan,
        'penalty_amount' => 0,
        'total_tagihan' => $rak2->harga_sewa_perbulan,

        // Status
        'status' => 'settlement',
        'type' => 'sewa_baru',
        'is_renewal' => false,

        // Waktu
        'created_at_db' => now(),
        'expired_at' => now()->addHours(24),
        'paid_at' => now(),

        // Info Sewa
        'sewa_mulai' => now(),
        'sewa_berakhir' => now()->addDays($rak2->durasi_sewa_hari ?? 30),

        // Pengosongan default
        'is_pengosongan' => false,
        'pengosongan_dimulai' => null,
        'pengosongan_berakhir' => null,
        'is_dikosongkan' => false,
        'dikosongkan_at' => null,

        'parent_tagihan_id' => null,
    ]);

    echo "✅ Seeder transaksi pembeli untuk Rak 2 berhasil dibuat.\n";
}


    }
}
