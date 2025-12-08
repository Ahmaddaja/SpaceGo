<div class="mb-10">
    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
        Riwayat Kadaluarsa
    </h2>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rak</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($expiredTransactions as $tagihan)
                        @php
                            // Akses dari relasi tagihan
                            $transaction = $tagihan->transaction;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $tagihan->rak->nama_rak ?? 'Rak' }}</div>
                                <div class="text-sm text-gray-500">Tagihan: {{ $tagihan->tagihan_code }}</div>
                                <div class="text-xs text-gray-400">Order: {{ $transaction->order_id ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">
                                    Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}
                                </div>
                                @if ($tagihan->penalty_amount > 0)
                                    <div class="text-xs text-red-600">
                                        +Denda: Rp {{ number_format($tagihan->penalty_amount, 0, ',', '.') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    Kadaluarsa
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    @if ($tagihan->is_renewal)
                                        <div class="inline-flex items-center text-sm text-purple-700 bg-purple-50 px-2 py-1 rounded">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                            Perpanjangan
                                        </div>
                                    @else
                                        <div class="inline-flex items-center text-sm text-blue-700 bg-blue-50 px-2 py-1 rounded">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Sewa Baru
                                        </div>
                                    @endif
                                    
                                    @if ($tagihan->cancelled_at)
                                        <div class="text-xs text-gray-500">
                                            Dibatalkan: {{ $tagihan->cancelled_at->format('d M Y H:i') }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    <svg class="w-4 h-4 inline mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $tagihan->created_at_db->format('d M Y') }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $tagihan->created_at_db->format('H:i') }} WIB
                                </div>
                                @if ($tagihan->expired_at)
                                    <div class="text-xs text-red-600 mt-1">
                                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        Expired: {{ $tagihan->expired_at->format('d M Y H:i') }}
                                    </div>
                                @endif
                                @if ($tagihan->sewa_berakhir)
                                    <div class="text-xs text-gray-500 mt-1">
                                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        Sewa berakhir: {{ \Carbon\Carbon::parse($tagihan->sewa_berakhir)->format('d M Y') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <button
                                    onclick="viewExpiredDetail({{ $tagihan->id }})"
                                    class="inline-flex items-center justify-center px-3 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition-all duration-200 border border-slate-200 hover:border-slate-300"
                                    title="Lihat Detail">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span class="text-sm font-medium">Detail</span>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    
                    @if($expiredTransactions->count() == 0)
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <svg class="w-16 h-16 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-500">Tidak ada tagihan kadaluarsa</p>
                                    <p class="text-sm text-gray-400 mt-1">Semua tagihan Anda dalam keadaan baik</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
    @include('customer.tagihan.partials.modals-and-scripts')
