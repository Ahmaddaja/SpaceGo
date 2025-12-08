<div class="mb-10">
    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-clock text-yellow-500 mr-2"></i>
        Menunggu Pembayaran
    </h2>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rak</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batas Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($pendingTransactions as $tagihan)
                        @php
                            // PERBAIKAN: Akses dari relasi tagihan
                            $transaction = $tagihan->transaction;
                            $isRenewal = $tagihan->is_renewal ?? false;
                            
                            // PERBAIKAN: Gunakan expired_at dari tagihan (DB time)
                            $batasWaktu = $tagihan->expired_at;
                            
                            // PERBAIKAN: Gunakan DB time untuk cek expired
                            $currentDbTime = DB::selectOne('SELECT NOW() as db_time')->db_time;
                            $now = \Carbon\Carbon::parse($currentDbTime);
                            $isExpired = $now->gt($batasWaktu);
                            
                            // PERBAIKAN: Ambil snap_token dari transaction
                            $snapToken = $transaction->snap_token ?? null;
                        @endphp
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium">{{ $tagihan->rak->nama_rak ?? 'Rak' }}</div>
                                <div class="text-sm text-gray-500">
                                    Tagihan: {{ $tagihan->tagihan_code }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    Order: {{ $transaction->order_id ?? '-' }}
                                </div>
                                @if ($isRenewal)
                                    <div class="text-xs text-purple-600 font-medium mt-1">
                                        <i class="fas fa-redo mr-1"></i> Perpanjangan
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold">Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</div>
                                @if ($tagihan->penalty_amount > 0)
                                    <div class="text-xs text-red-600">
                                        Termasuk denda: Rp {{ number_format($tagihan->penalty_amount, 0, ',', '.') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Menunggu Pembayaran
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if ($isExpired)
                                    <div class="flex items-center gap-2">
                                        <span class="text-red-600 text-sm font-medium">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            Telah Kadaluarsa
                                        </span>
                                        <button
                                            onclick="viewDetail({{ $tagihan->id }})"
                                            class="inline-flex items-center justify-center w-9 h-9 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md"
                                            title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                    </div>
                                @elseif (!$snapToken)
                                    <div class="flex items-center gap-2">
                                        <span class="text-orange-600 text-sm font-medium">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Token tidak tersedia
                                        </span>
                                        <button
                                            onclick="viewDetail({{ $tagihan->id }})"
                                            class="inline-flex items-center justify-center w-9 h-9 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md"
                                            title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <div class="flex flex-col gap-2">
                                        <button
                                            onclick="continuePayment('{{ $snapToken }}', {{ $transaction->id ?? 0 }})"
                                            class="inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                            </svg>
                                            <span class="font-semibold">Bayar Sekarang</span>
                                        </button>
                                        
                                        <div class="flex gap-2">
                                            <button
                                                onclick="viewDetail({{ $tagihan->id }})"
                                                class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition-all duration-200 border border-slate-200 hover:border-slate-300"
                                                title="Lihat Detail">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                <span class="text-sm font-medium">Detail</span>
                                            </button>
                                            
                                            <button
                                                onclick="cancelPayment({{ $tagihan->id }}, '{{ $tagihan->tagihan_code }}')"
                                                class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-all duration-200 border border-red-200 hover:border-red-300"
                                                title="Batalkan Tagihan">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                <span class="text-sm font-medium">Batalkan</span>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($isExpired)
                                    <span class="text-red-600 text-sm">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Telah lewat
                                    </span>
                                @else
                                    <div class="text-sm text-gray-600">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $batasWaktu->format('d M Y H:i') }}
                                    </div>
                                    @php
                                        $remaining = $now->diffForHumans($batasWaktu, true);
                                    @endphp
                                    <div class="text-xs text-gray-500 mt-1">
                                        Sisa waktu: {{ $remaining }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    
                    @if($pendingTransactions->count() == 0)
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>Tidak ada tagihan pending</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
    @include('customer.tagihan.partials.modals-and-scripts')