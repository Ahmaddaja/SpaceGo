<div class="mb-10">
    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-calendar-times text-orange-500 mr-2"></i>
        Rak Perlu Perpanjangan
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($overdueTransactions as $transaction)
            @php
                $now = now()->startOfDay();
                $sewaBerahir = \Carbon\Carbon::parse($transaction->sewa_berakhir)->startOfDay();
                
                // Hitung selisih hari (+ = tersisa, 0 = hari terakhir, - = lewat)
                $daysDiff = $now->diffInDays($sewaBerahir, false);
                
                // MASA TENGGANG = 3 hari setelah sewa_berakhir
                $gracePeriodDays = 3;
                $dendaPerHari = 50000;
                
                // Tentukan status
                if ($daysDiff > 0) {
                    // Masih ada waktu tersisa
                    $statusColor = 'blue';
                    $statusText = $daysDiff . ' hari tersisa';
                    $statusBgClass = 'bg-blue-100 text-blue-800';
                    $borderClass = 'border-blue-200';
                    $isInGracePeriod = false;
                    $isOverdue = false;
                    $isCritical = false;
                } elseif ($daysDiff === 0) {
                    // Berakhir hari ini
                    $statusColor = 'yellow';
                    $statusText = 'Berakhir hari ini';
                    $statusBgClass = 'bg-yellow-100 text-yellow-800';
                    $borderClass = 'border-yellow-200';
                    $isInGracePeriod = false;
                    $isOverdue = false;
                    $isCritical = false;
                } elseif (abs($daysDiff) <= $gracePeriodDays) {
                    // Dalam masa tenggang (1-3 hari setelah berakhir)
                    $statusColor = 'yellow';
                    $statusText = 'Masa Tenggang - Hari ke-' . abs($daysDiff) . ' dari ' . $gracePeriodDays;
                    $statusBgClass = 'bg-yellow-100 text-yellow-800';
                    $borderClass = 'border-yellow-200';
                    $isInGracePeriod = true;
                    $isOverdue = false;
                    $isCritical = false;
                } else {
                    // Sudah lewat masa tenggang, kenakan denda
                    $latenessDays = abs($daysDiff) - $gracePeriodDays;
                    $statusColor = 'red';
                    $statusText = 'Terlambat ' . $latenessDays . ' hari (+ denda)';
                    $statusBgClass = 'bg-red-100 text-red-800';
                    $borderClass = 'border-red-200';
                    $isInGracePeriod = false;
                    $isOverdue = true;
                    $isCritical = $latenessDays > 7;
                }
                
                // Hitung denda
                $totalDenda = 0;
                if ($isOverdue) {
                    $latenessDays = abs($daysDiff) - $gracePeriodDays;
                    $totalDenda = $latenessDays * $dendaPerHari;
                }
                
                // Hitung total bayar
                $hargaSewa = $transaction->rak->harga_sewa_perbulan ?? 0;
                $totalBayar = $hargaSewa + $totalDenda;
            @endphp
            
            <div class="bg-white rounded-lg shadow p-6 border {{ $borderClass }}">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-bold text-gray-800">{{ $transaction->rak->nama_rak ?? 'Rak' }}</h3>
                        <p class="text-sm text-gray-500">Kode: {{ $transaction->order_id }}</p>
                    </div>
                    <span class="px-3 py-1 {{ $statusBgClass }} text-xs font-medium rounded-full">
                        {{ $statusText }}
                    </span>
                </div>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600 text-sm">Masa sewa berakhir:</span>
                        <span class="font-medium">{{ $sewaBerahir->format('d M Y') }}</span>
                    </div>
                    
                    @if ($transaction->rak)
                        <div class="flex justify-between">
                            <span class="text-gray-600 text-sm">Biaya Sewa ({{ $transaction->rak->durasi_sewa_hari ?? 30 }} hari):</span>
                            <span class="font-bold text-gray-800">
                                Rp {{ number_format($hargaSewa, 0, ',', '.') }}
                            </span>
                        </div>
                    @endif
                    
                    @if ($isOverdue && $totalDenda > 0)
                        @php
                            $latenessDays = abs($daysDiff) - $gracePeriodDays;
                        @endphp
                        <div class="flex justify-between">
                            <span class="text-gray-600 text-sm">Denda ({{ $latenessDays }} hari × Rp {{ number_format($dendaPerHari, 0, ',', '.') }}):</span>
                            <span class="font-bold text-red-600">
                                Rp {{ number_format($totalDenda, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="pt-2 border-t border-gray-200">
                            <div class="flex justify-between">
                                <span class="text-gray-800 font-semibold">Total Pembayaran:</span>
                                <span class="font-bold text-blue-600 text-lg">
                                    Rp {{ number_format($totalBayar, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="pt-2 border-t border-gray-200">
                            <div class="flex justify-between">
                                <span class="text-gray-800 font-semibold">Total Pembayaran:</span>
                                <span class="font-bold text-blue-600 text-lg">
                                    Rp {{ number_format($hargaSewa, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @endif
                    
                    {{-- Info Masa Tenggang --}}
                    @if ($isInGracePeriod)
                        <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-start text-yellow-700">
                                <i class="fas fa-shield-alt mr-2 mt-0.5"></i>
                                <div class="text-sm">
                                    <span class="font-medium">Masa Tenggang Aktif!</span>
                                    <p class="mt-1 text-xs">
                                        Anda berada di hari ke-{{ abs($daysDiff) }} dari {{ $gracePeriodDays }} hari masa tenggang. 
                                        <strong>Tidak ada denda</strong> jika perpanjang sekarang.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    {{-- Warning untuk Overdue --}}
                    @if ($isOverdue)
                        <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-start text-red-700">
                                <i class="fas fa-exclamation-triangle mr-2 mt-0.5"></i>
                                <div class="text-sm">
                                    <span class="font-medium">Denda Aktif!</span>
                                    <p class="mt-1 text-xs">
                                        Sudah melewati {{ $gracePeriodDays }} hari masa tenggang. 
                                        Denda akan bertambah Rp {{ number_format($dendaPerHari, 0, ',', '.') }}/hari.
                                        {{ $isCritical ? 'Segera perpanjang untuk menghindari sanksi!' : '' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="space-y-3">
                    <a href="{{ route('customer.payment.renewal-checkout', $transaction->id) }}"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition flex items-center justify-center">
                        <i class="fas fa-redo mr-2"></i>
                        @if ($isOverdue)
                            Bayar Sekarang (Sewa + Denda)
                        @elseif ($isInGracePeriod)
                            Perpanjang Sekarang (Tanpa Denda)
                        @else
                            Buat Permintaan Perpanjangan
                        @endif
                    </a>
                    <form action="{{ route('customer.tagihan.process-expired', $transaction->id) }}" method="POST" id="form-lepas-{{ $transaction->id }}">
                        @csrf
                        <button type="button" onclick="confirmLepasRak({{ $transaction->id }})"
                            class="w-full border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2.5 rounded-lg transition">
                            <i class="fas fa-times mr-2"></i>
                            Lepas Rak
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>