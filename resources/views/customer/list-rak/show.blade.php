@extends('layouts.app')

@section('title', 'Detail Rak - SPACEGO')

@push('styles')
    <style>
        .detail-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        .detail-card:hover {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .spec-card {
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }

        .spec-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            backdrop-filter: blur(8px);
        }

        .status-available {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .status-occupied {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .status-maintenance {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .status-pengosongan {
            background: linear-gradient(135deg, #9333ea, #db2777);
            color: white;
        }

        .type-badge {
            background: rgba(255, 255, 255, 0.95);
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .image-hover {
            transition: transform 0.5s ease;
        }

        .image-hover:hover {
            transform: scale(1.05);
        }

        .action-button {
            transition: all 0.3s ease;
        }

        .action-button:hover {
            transform: translateY(-2px);
        }

        .rental-info-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 1rem;
            padding: 1.5rem;
            color: white;
            box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.4);
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .rental-date-box {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 0.75rem;
            padding: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .rental-icon {
            background: rgba(255, 255, 255, 0.2);
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .renewal-card {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border-radius: 1rem;
            padding: 1.5rem;
            color: white;
            box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.4);
            margin-top: 1rem;
        }

        .penalty-badge {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            display: inline-block;
        }

        .price-breakdown {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 0.75rem;
            padding: 1rem;
            margin-top: 1rem;
        }

        /* Alert 10 Menit Styles */
        .alert-10-minutes {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            border-radius: 1rem;
            padding: 1.5rem;
            color: white;
            box-shadow: 0 10px 25px -5px rgba(220, 38, 38, 0.4);
            margin-bottom: 1rem;
            animation: pulseAlert 2s ease-in-out infinite;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        @keyframes pulseAlert {
            0%, 100% {
                box-shadow: 0 10px 25px -5px rgba(220, 38, 38, 0.4);
            }
            50% {
                box-shadow: 0 15px 35px -5px rgba(220, 38, 38, 0.6);
            }
        }

        .alert-icon-wrapper {
            background: rgba(255, 255, 255, 0.2);
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            animation: shake 0.5s ease-in-out infinite;
        }

        .alert-dikosongkan {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border-radius: 1rem;
            padding: 1.5rem;
            color: white;
            box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.4);
            animation: slideDown 0.5s ease-out;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .alert-icon-wrapper-dikosongkan {
            background: rgba(255, 255, 255, 0.2);
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .countdown-timer {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 0.75rem;
            padding: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
        }

        .timer-digit {
            font-size: 2rem;
            font-weight: bold;
            font-family: 'Courier New', monospace;
        }

        /* Time Display Badge */
        .time-display-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 0.75rem;
            padding: 0.75rem 1.25rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            font-weight: 600;
            font-size: 1.1rem;
        }

        .time-display-badge i {
            font-size: 1.2rem;
        }
    </style>
@endpush

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- TITLE -->
            <div class="mb-8 text-center">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Detail Rak</h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">Informasi lengkap mengenai Rak yang Anda pilih</p>
            </div>

            <!-- RENTAL INFO (Jika user sudah menyewa) -->
           @php
    $activeRental = null;
    $hasPendingRenewal = false;

    if (Auth::check()) {

        // Ambil tagihan terakhir yang sudah dibayar
        $activeRental = \App\Models\Tagihan::where('user_id', Auth::id())
            ->where('rak_id', $rak->id)
            ->whereIn('status', ['settlement']) // hanya settlement yang pasti paid
            ->orderBy('sewa_berakhir', 'desc')
            ->first();

        // Jika ada sewa aktif → cek apakah ada renewal pending
        if ($activeRental) {
            $hasPendingRenewal = \App\Models\Tagihan::where('user_id', Auth::id())
                ->where('rak_id', $rak->id)
                ->where('type', 'renewal')
                ->whereIn('status', ['pending', 'settlement']) 
                ->where('created_at', '>', $activeRental->created_at)
                ->exists();
        }
    }
@endphp


            @if ($activeRental)
                @php
    // Gunakan waktu database untuk konsistensi
    $currentDbTime = DB::selectOne('SELECT NOW() as db_time')->db_time;
    $now = \Carbon\Carbon::parse($currentDbTime);
    $end = \Carbon\Carbon::parse($activeRental->sewa_berakhir);

    // Hitung total menit selisih (boleh negatif)
    $totalMinutes = $now->diffInMinutes($end, false);

    // Ambil nilai absolut untuk perhitungan visual
    $absMinutes = abs($totalMinutes);

    // Hitung Hari-Jam-Menit
    $days = floor($absMinutes / 1440);                 // 1 hari = 1440 menit
    $hours = floor(($absMinutes % 1440) / 60);         // sisa jam
    $minutes = $absMinutes % 60;                       // sisa menit

    // Buat tampilan sesuai format
    if ($totalMinutes >= 0) {
        // Masih ada sisa waktu
        $timeRemainingDisplay = "{$days} Hari {$hours} Jam {$minutes} Menit Tersisa";
    } else {
        // Sudah lewat waktu → tanpa minus
        $timeRemainingDisplay = "Lewat {$days} Hari {$hours} Jam {$minutes} Menit";
    }

    // Alert 10 menit terakhir
    $showTenMinuteAlert = ($totalMinutes > 0 && $totalMinutes <= 10);

    // CEK APAKAH RAK SUDAH DIKOSONGKAN (37 HARI)
    $daysPassed = $now->diffInDays($end, false);
    $isDikosongkan = $daysPassed < -37 || $activeRental->is_dikosongkan;
@endphp


                <!-- ALERT RAK SUDAH DIKOSONGKAN -->
                @if ($isDikosongkan)
                    <div class="alert-dikosongkan mb-8">
                        <div class="flex items-start">
                            <div class="alert-icon-wrapper-dikosongkan mr-4">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-2xl font-bold mb-3">📦 Rak Sudah Dikosongkan</h3>
                                <p class="text-lg opacity-95 mb-2">
                                    Masa sewa Anda telah berakhir lebih dari 37 hari (3 hari masa tenggang + 30 hari keterlambatan + 7 hari pengosongan).
                                </p>
                                <p class="text-base opacity-90 mb-4">
                                    Rak telah dikosongkan dan kembali tersedia untuk penyewa lain.
                                </p>
                                
                                @if ($activeRental->dikosongkan_at)
                                    <div class="bg-white bg-opacity-20 rounded-lg p-3 mb-4">
                                        <p class="text-sm font-semibold mb-1">Tanggal Pengosongan:</p>
                                        <p class="text-base">
                                            {{ \Carbon\Carbon::parse($activeRental->dikosongkan_at)->format('d M Y H:i') }}
                                            ({{ \Carbon\Carbon::parse($activeRental->dikosongkan_at)->diffForHumans() }})
                                        </p>
                                    </div>
                                @endif

                                <div class="mt-4 p-4 bg-blue-600 bg-opacity-50 rounded-lg">
                                    <p class="text-sm font-semibold mb-2">💡 Ingin menyewa lagi?</p>
                                    <p class="text-sm opacity-90 mb-3">
                                        Anda dapat menyewa rak yang sama atau memilih rak lain yang tersedia.
                                    </p>
                                    <a href="{{ route('customer.list-rak.list-rak') }}"
                                       class="inline-flex items-center justify-center space-x-2 px-6 py-3 bg-white text-blue-600 rounded-xl hover:bg-blue-50 transition-all duration-300 font-bold shadow-lg hover:shadow-xl">
                                        <i class="fas fa-shopping-cart"></i>
                                        <span>Sewa Rak Kembali</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- ALERT 10 MENIT -->
                  @if ($showTenMinuteAlert)
    @php
        // Hitung sisa waktu dalam menit untuk alert 10 menit
        $totalMinutesRemaining = $totalMinutes; // Menggunakan $totalMinutes yang sudah dihitung sebelumnya
        $remainingSeconds = ($totalMinutesRemaining - floor($totalMinutesRemaining)) * 60;
    @endphp
    
    <div class="alert-10-minutes" id="tenMinuteAlert">
        <div class="flex items-start mb-4">
            <div class="alert-icon-wrapper mr-4">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-2xl font-bold mb-2">⚠️ PERINGATAN WAKTU SEWA HAMPIR HABIS!</h3>
                <p class="text-lg opacity-95 mb-3">
                    Masa sewa Anda akan berakhir dalam <strong id="minutesLeft">{{ floor($totalMinutesRemaining) }}</strong> menit lagi!
                </p>
                <p class="text-sm opacity-90">
                    Segera perpanjang masa sewa Anda untuk menghindari denda keterlambatan dan masa pengosongan.
                </p>
            </div>
        </div>

        <div class="countdown-timer">
            <p class="text-sm opacity-90 mb-2">Waktu Tersisa:</p>
            <div class="timer-digit" id="countdownDisplay">
                {{ sprintf('%02d:%02d', floor($totalMinutesRemaining), $remainingSeconds) }}
            </div>
            <p class="text-xs opacity-80 mt-2">Menit : Detik</p>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('customer.payment.renewal-checkout', ['transaction_id' => $activeRental->transaction_id]) }}"
                class="inline-flex items-center justify-center space-x-3 px-8 py-3 bg-white text-red-600 rounded-xl hover:bg-red-50 transition-all duration-300 font-bold shadow-lg hover:shadow-xl">
                <i class="fas fa-bolt"></i>
                <span>PERPANJANG SEKARANG</span>
            </a>
        </div>
    </div>
@endif

                    <div class="mb-8 rental-info-card">
                        <div class="flex items-center mb-4">
                            <div class="rental-icon mr-4">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold">Anda Sedang Menyewa Rak Ini</h3>
                                <p class="text-sm opacity-90">Order ID: {{ $activeRental->order_id }}</p>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4 mt-6">
                            <div class="rental-date-box">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-calendar-check mr-2 text-lg"></i>
                                    <span class="font-semibold">Tanggal Mulai Sewa</span>
                                </div>
                                <p class="text-2xl font-bold">
                                    {{ \Carbon\Carbon::parse($activeRental->sewa_mulai)->format('d M Y H:i') }}
                                </p>
                                <p class="text-sm opacity-80 mt-1">
                                    {{ \Carbon\Carbon::parse($activeRental->sewa_mulai)->diffForHumans() }}
                                </p>
                            </div>

                            <div class="rental-date-box">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-calendar-times mr-2 text-lg"></i>
                                    <span class="font-semibold">Tanggal Berakhir Sewa</span>
                                </div>
                                <p class="text-2xl font-bold">
                                    {{ \Carbon\Carbon::parse($activeRental->sewa_berakhir)->format('d M Y H:i') }}
                                </p>
                                <div class="mt-3">
                                    <span class="time-display-badge" id="timeRemainingBadge">
                                        <i class="fas fa-clock"></i>
                                        <span id="timeRemainingText">{{ $timeRemainingDisplay }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        @php
                            $now = \Carbon\Carbon::parse($currentDbTime);
                            $end = \Carbon\Carbon::parse($activeRental->sewa_berakhir);

                            $gracePeriodDays = 3;
                            $maxLateDays = 30;
                            $dendaPerHari = 50000;

                            // SINGLE SOURCE OF TRUTH (menit, integer)
                            $totalMinutesRemaining = (int) floor($now->diffInSeconds($end, false) / 60);

                            // Default
                            $isInGracePeriod = false;
                            $isOverdue = false;
                            $statusColor = 'bg-green-600';
                            $statusLabel = 'Aktif';
                            $lateDays = 0;

                            // STATUS BISNIS SAJA (TANPA JAM/MENIT)
                            if ($totalMinutesRemaining <= 0 && abs($totalMinutesRemaining) <= ($gracePeriodDays * 1440)) {
                                $statusColor = 'bg-yellow-500';
                                $statusLabel = 'Masa Tenggang';
                                $isInGracePeriod = true;
                            } elseif ($totalMinutesRemaining <= 0) {
                                $lateDays = ceil(abs($totalMinutesRemaining) / 1440) - $gracePeriodDays;
                                $statusColor = 'bg-red-600';
                                $statusLabel = 'Terlambat';
                                $isOverdue = true;
                            }

                            // DENDA
                            $totalDenda = $isOverdue ? max(0, $lateDays * $dendaPerHari) : 0;

                            // TOTAL BAYAR
                            $hargaSewa = $rak->harga_sewa_perbulan ?? 0;
                            $totalBayar = $hargaSewa + $totalDenda;

                            // PENGOSONGAN
                            $totalLateDays = $isOverdue ? $lateDays : 0;
                            $isEnteringPengosongan = $totalLateDays >= $maxLateDays;
                        @endphp

                        <div class="mt-4 p-3 rounded-lg text-white {{ $statusColor }}">
                            <div class="flex items-center justify-between">
                                <span class="text-xl font-bold text-white" id="statusLabel">
                                    {{ $statusLabel }}
                                </span>
                            </div>
                        </div>

                        @if ($isInGracePeriod)
                            <div class="mt-4 p-4 bg-yellow-500 bg-opacity-90 rounded-lg text-white">
                                <div class="flex items-start">
                                    <i class="fas fa-shield-alt mr-3 mt-1 text-xl"></i>
                                    <div>
                                        <p class="font-semibold mb-1">
                                            Masa Tenggang Aktif ({{ $gracePeriodDays }} Hari)
                                        </p>

                                        @php
                                            // Hari ke berapa dalam masa tenggang (1-based)
                                            $graceDay = min($gracePeriodDays, max(1, ceil(abs($totalMinutesRemaining) / 1440)));
                                        @endphp

                                        <p class="text-sm opacity-90 leading-relaxed">
                                            Anda berada di <strong>hari ke-{{ $graceDay }}</strong> dari
                                            {{ $gracePeriodDays }} hari masa tenggang.
                                            <br>
                                            <strong>Tidak ada denda</strong> selama masa ini.
                                            <br>
                                            Perpanjang segera untuk menghindari denda
                                            <strong>Rp {{ number_format($dendaPerHari, 0, ',', '.') }}/hari</strong>
                                            setelah masa tenggang berakhir.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- STATUS PENGOSONGAN SECTION -->
                        @if ($activeRental->is_pengosongan)
                            <div class="mt-4 p-4 bg-gradient-to-r from-purple-600 to-pink-600 rounded-lg text-white">
                                <div class="flex items-start">
                                    <i class="fas fa-box-open mr-3 mt-1 text-xl"></i>
                                    <div class="flex-1">
                                        <p class="font-semibold mb-2 text-lg">🚨 Masa Pengosongan Aktif</p>
                                        <div class="bg-white bg-opacity-20 rounded-lg p-3 mb-3">
                                            <div class="grid grid-cols-2 gap-3 text-sm">
                                                <div>
                                                    <p class="opacity-80 mb-1">Dimulai:</p>
                                                    <p class="font-bold">{{ \Carbon\Carbon::parse($activeRental->pengosongan_dimulai)->format('d M Y') }}</p>
                                                </div>
                                                <div>
                                                    <p class="opacity-80 mb-1">Berakhir:</p>
                                                    <p class="font-bold">{{ \Carbon\Carbon::parse($activeRental->pengosongan_berakhir)->format('d M Y') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-sm opacity-90 leading-relaxed">
                                            Rak Anda telah memasuki <strong>masa pengosongan selama 7 hari</strong> karena pembayaran tidak dilakukan 
                                            setelah masa tenggang + 30 hari keterlambatan. Setelah masa pengosongan berakhir, 
                                            rak akan otomatis dikosongkan dan kembali tersedia untuk penyewa lain.
                                        </p>
                                        <div class="mt-3 p-3 bg-red-700 bg-opacity-50 rounded-lg">
                                            <p class="text-xs font-semibold">
                                                ⚠️ PERHATIAN: Harap segera kosongkan barang Anda sebelum masa pengosongan berakhir!
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @elseif($isEnteringPengosongan)
                            <div class="mt-4 p-4 bg-gradient-to-r from-red-600 to-orange-600 rounded-lg text-white">
                                <div class="flex items-start">
                                    <i class="fas fa-exclamation-triangle mr-3 mt-1 text-xl"></i>
                                    <div class="flex-1">
                                        <p class="font-semibold mb-2 text-lg">⚠️ Rak Akan Memasuki Masa Pengosongan</p>
                                        <p class="text-sm opacity-90 leading-relaxed mb-3">
                                            Anda telah terlambat <strong>{{ $lateDays }} hari</strong> (melebihi batas maksimal 30 hari setelah masa tenggang).
                                            Rak akan segera memasuki <strong>masa pengosongan 7 hari</strong>.
                                        </p>
                                        <div class="bg-white bg-opacity-20 rounded-lg p-3">
                                            <p class="text-sm font-semibold mb-2">Yang Perlu Anda Ketahui:</p>
                                            <ul class="text-xs space-y-1 opacity-90">
                                                <li>• Masa pengosongan dimulai otomatis setelah 30 hari keterlambatan</li>
                                                <li>• Durasi pengosongan: 7 hari</li>
                                                <li>• Setelah 7 hari, rak akan dikosongkan dan kembali tersedia</li>
                                                <li>• Segera kosongkan barang Anda untuk menghindari kehilangan</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- RENEWAL SECTION - DENGAN BLOCKER PENGOSONGAN -->
                        @if ($activeRental->is_pengosongan || $isEnteringPengosongan)
                            <div class="mt-4 p-4 bg-gray-700 rounded-lg text-white">
                                <div class="flex items-center">
                                    <i class="fas fa-lock mr-3 text-xl"></i>
                                    <div>
                                        <p class="font-semibold">Perpanjangan Tidak Tersedia</p>
                                        <p class="text-sm opacity-90 mt-1">
                                            Anda tidak bisa membayar atau memperpanjang masa sewa lagi karena rak
                                            sudah memasuki atau akan memasuki masa pengosongan.
                                        </p>
                                    </div>
                                </div>
                            </div>

                        @elseif ($totalMinutesRemaining <= 1440 && !$hasPendingRenewal)
                            <div class="renewal-card">
                                <div class="flex items-center mb-4">
                                    <div class="rental-icon mr-4">
                                        <i class="fas fa-redo-alt"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold">
                                            @if ($isOverdue)
                                                Perpanjang Sewa & Bayar Denda
                                            @elseif ($isInGracePeriod)
                                                Perpanjang Sewa (Masih Tanpa Denda)
                                            @else
                                                Perpanjang Masa Sewa
                                            @endif
                                        </h3>
                                        <p class="text-sm opacity-90">
                                            @if ($isOverdue)
                                                Segera perpanjang untuk menghindari denda lebih lanjut
                                            @elseif ($isInGracePeriod)
                                                Perpanjang sekarang sebelum dikenakan denda
                                            @else
                                                Perpanjang sekarang untuk melanjutkan penyewaan
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="price-breakdown">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm">
                                            Harga Sewa ({{ $rak->durasi_sewa_hari ?? 30 }} hari)
                                        </span>
                                        <span class="font-bold">
                                            Rp {{ number_format($hargaSewa, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    @if ($lateDays > 0)
                                        <div class="flex justify-between items-center mb-2 text-red-200">
                                            <span class="text-sm">
                                                Denda Keterlambatan
                                                ({{ $lateDays }} hari × Rp {{ number_format($dendaPerHari, 0, ',', '.') }})
                                            </span>
                                            <span class="font-bold">
                                                Rp {{ number_format($totalDenda, 0, ',', '.') }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-red-200 opacity-80 mb-2">
                                            *Denda dihitung setelah {{ $gracePeriodDays }} hari masa tenggang
                                        </div>
                                    @endif

                                    <div class="flex justify-between items-center mt-3 border-t border-gray-600 pt-3">
                                        <span class="font-semibold">Total Pembayaran</span>
                                        <span class="font-bold text-lg">
                                            Rp {{ number_format($totalBayar, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>

                              <a href="{{ route('customer.payment.renewal-checkout', ['transaction_id' => $activeRental->transaction_id]) }}"
                                   class="mt-4 w-full flex items-center justify-center space-x-3 px-6 py-4
                                          bg-white text-orange-600 rounded-xl hover:bg-orange-50
                                          transition-all duration-300 font-bold shadow-lg hover:shadow-xl">
                                    <i class="fas fa-credit-card"></i>
                                    <span>
                                        @if ($isOverdue)
                                            Bayar Sekarang (Sewa + Denda)
                                        @elseif($isInGracePeriod)
                                            Perpanjang Sekarang (Tanpa Denda)
                                        @else
                                            Perpanjang Sewa Sekarang
                                        @endif
                                    </span>
                                </a>
                            </div>

                        @elseif($hasPendingRenewal)
                            <div class="mt-4 p-4 bg-blue-500 bg-opacity-90 rounded-lg text-white">
                                <div class="flex items-start">
                                    <i class="fas fa-info-circle mr-3 mt-1 text-xl"></i>
                                    <div>
                                        <p class="font-semibold mb-1">Pembayaran Perpanjangan Berhasil!</p>
                                        <p class="text-sm opacity-90">
                                            Anda sudah melakukan pembayaran perpanjangan. Silakan cek status pembayaran Anda di halaman riwayat transaksi.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- JavaScript untuk Real-time Countdown -->
                  @push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // =========================
    // 🔧 SINKRON WAKTU SERVER ↔ CLIENT
    // =========================
    const serverNow = {{ now()->timestamp }} * 1000; // waktu server (ms)
    const clientNow = Date.now();                    // waktu browser
    const timeOffset = serverNow - clientNow;        // selisih waktu

    // =========================
    // DATA SEWA
    // =========================
    const sewaBerakir = new Date(
        "{{ $activeRental->sewa_berakhir->toIso8601String() }}"
    ).getTime();

    const gracePeriodDays = {{ $gracePeriodDays }};
    const gracePeriodMs = gracePeriodDays * 24 * 60 * 60 * 1000;

    const alertElement = document.getElementById('tenMinuteAlert');
    const countdownDisplay = document.getElementById('countdownDisplay');
    const minutesLeftText = document.getElementById('minutesLeft');
    const timeRemainingText = document.getElementById('timeRemainingText');
    const timeRemainingBadge = document.getElementById('timeRemainingBadge');
    const statusLabel = document.getElementById('statusLabel');

    function updateCountdown() {

        // 🔧 GUNAKAN WAKTU SERVER (BUKAN JAM USER)
        const now = Date.now() + timeOffset;
        let distance = sewaBerakir - now;

        // =========================
        // WAKTU SUDAH HABIS
        // =========================
        if (distance <= 0) {
            const timeOverdue = Math.abs(distance);

            if (timeOverdue <= gracePeriodMs) {
                // MASA TENGGANG
                const graceDaysRemaining = Math.ceil((gracePeriodMs - timeOverdue) / (1000 * 60 * 60 * 24));
                const graceHoursRemaining = Math.floor((gracePeriodMs - timeOverdue) / (1000 * 60 * 60));
                const graceMinutesRemaining = Math.floor((gracePeriodMs - timeOverdue) / (1000 * 60));

                if (countdownDisplay) countdownDisplay.textContent = '00:00';

                if (timeRemainingText) {
                    if (graceDaysRemaining > 0) {
                        timeRemainingText.textContent = 'Masa Tenggang: ' + graceDaysRemaining + ' Hari Tersisa';
                    } else if (graceHoursRemaining > 0) {
                        timeRemainingText.textContent = 'Masa Tenggang: ' + graceHoursRemaining + ' Jam Tersisa';
                    } else {
                        timeRemainingText.textContent = 'Masa Tenggang: ' + graceMinutesRemaining + ' Menit Tersisa';
                    }
                }

                if (timeRemainingBadge) {
                    timeRemainingBadge.style.background = 'rgba(251, 191, 36, 0.3)';
                }

                if (statusLabel) statusLabel.textContent = 'Masa Tenggang';
                if (alertElement) alertElement.style.display = 'none';

            } else {
                // TERLAMBAT
                const lateDays = Math.floor((timeOverdue - gracePeriodMs) / (1000 * 60 * 60 * 24));
                const lateHours = Math.floor(((timeOverdue - gracePeriodMs) % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const lateMinutes = Math.floor(((timeOverdue - gracePeriodMs) % (1000 * 60 * 60)) / (1000 * 60));

                if (countdownDisplay) countdownDisplay.textContent = '00:00';

                if (timeRemainingText) {
                    timeRemainingText.textContent =
                        'Lewat ' + lateDays + ' Hari ' + lateHours + ' Jam ' + lateMinutes + ' Menit';
                }

                if (timeRemainingBadge) {
                    timeRemainingBadge.style.background = 'rgba(239, 68, 68, 0.3)';
                }

                if (statusLabel) statusLabel.textContent = 'Terlambat';
                if (alertElement) alertElement.style.display = 'none';
            }

            return;
        }

        // =========================
        // WAKTU MASIH TERSISA
        // =========================
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // ALERT 10 MENIT
        if (distance <= 600000) {
            if (alertElement) alertElement.style.display = 'block';

            if (countdownDisplay) {
                countdownDisplay.textContent =
                    String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
            }

            if (minutesLeftText) minutesLeftText.textContent = minutes;
        } else if (alertElement) {
            alertElement.style.display = 'none';
        }

        let displayText = '';

        if (days > 0) {
            displayText = days + ' Hari ' + hours + ' Jam ' + minutes + ' Menit Tersisa';
        } else if (hours > 0) {
            displayText = hours + ' Jam';
            if (minutes > 0) displayText += ' ' + minutes + ' Menit';
            displayText += ' Tersisa';
        } else if (minutes > 0) {
            displayText = minutes + ' Menit Tersisa';
        } else {
            displayText = seconds + ' Detik Tersisa';
        }

        if (timeRemainingText) timeRemainingText.textContent = displayText;
        if (statusLabel) statusLabel.textContent = 'Aktif';
    }

    // START
    updateCountdown();
    setInterval(updateCountdown, 1000);
});
</script>
@endpush

                @endif
            @endif

            <!-- MAIN CARD -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 detail-card">
                <div class="grid lg:grid-cols-2 gap-8 p-8">
                    @include('customer.list-rak.partials.photo-section')
                    @include('customer.list-rak.partials.info-section')
                </div>
            </div>

            @include('customer.list-rak.partials.specifications-section')

           <!-- ACTION BUTTONS -->
<div class="mt-8 flex flex-col sm:flex-row gap-4">
    <a href="{{ route('customer.list-rak.rak') }}"
        class="flex items-center justify-center space-x-3 px-8 py-4 bg-gray-200 text-gray-700 rounded-xl
               hover:bg-gray-300 transition-all duration-300 font-semibold shadow-md hover:shadow-lg group action-button">
        <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
        <span>Kembali ke Daftar Rak Anda</span>
    </a>

    {{-- ✅ PRIORITAS UTAMA: RAK SUDAH DIKOSONGKAN --}}
    @if ($activeRental && $isDikosongkan)
        <a href="{{ route('customer.list-rak.list-rak') }}"
            class="flex-1 flex items-center justify-center space-x-3 px-8 py-4
                   bg-gradient-to-r from-blue-600 to-purple-600 text-white
                   rounded-xl hover:shadow-xl transition-all duration-300
                   font-semibold shadow-lg action-button">
            <i class="fas fa-shopping-cart"></i>
            <span>Sewa Lagi</span>
        </a>

    {{-- ✅ MASIH AKTIF MENYEWA --}}
    @elseif ($activeRental)
        <button
            class="flex-1 flex items-center justify-center space-x-3 px-8 py-4
                   bg-gradient-to-r from-green-500 to-emerald-600
                   text-white rounded-xl cursor-default
                   font-semibold shadow-lg">
            <i class="fas fa-check-circle"></i>
            <span>Rak Sedang Anda Sewa</span>
        </button>

    {{-- ✅ BELUM MENYEWA & TERSEDIA --}}
    @elseif ($rak->status === 'tersedia')
        <a href="{{ route('customer.payment.checkout', $rak->id) }}"
            class="flex-1 flex items-center justify-center space-x-3 px-8 py-4
                   bg-gradient-to-r from-blue-600 to-purple-600
                   text-white rounded-xl hover:shadow-xl
                   transition-all duration-300 font-semibold shadow-lg action-button">
            <i class="fas fa-shopping-cart"></i>
            <span>Sewa Sekarang</span>
        </a>

    {{-- ❌ TIDAK TERSEDIA --}}
    @else
        <button
            class="flex-1 flex items-center justify-center space-x-3 px-8 py-4
                   bg-gray-300 text-gray-500 rounded-xl
                   cursor-not-allowed font-semibold">
            <i class="fas fa-ban"></i>
            <span>Tidak Tersedia</span>
        </button>
    @endif
</div>


        </div>
    </div>

    @include('customer.payment.partials.whatsapp-button')
@endsection