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
                    @foreach ($pendingTransactions as $transaction)
                        @php
                            $isRenewal = $transaction->is_renewal ?? false;
                            $batasWaktu = $transaction->created_at->addHours(24);
                            $isExpired = now()->gt($batasWaktu);
                        @endphp
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium">{{ $transaction->rak->nama_rak ?? 'Rak' }}</div>
                                <div class="text-sm text-gray-500">Order: {{ $transaction->order_id }}</div>
                                @if ($isRenewal)
                                    <div class="text-xs text-purple-600 font-medium mt-1">
                                        <i class="fas fa-redo mr-1"></i> Perpanjangan
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Menunggu Pembayaran
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if ($isExpired)
                                    <span class="text-red-600 text-sm font-medium">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        Telah Kadaluarsa
                                    </span>
                                @else
                                    <button
                                        onclick="continuePayment('{{ $transaction->snap_token }}', {{ $transaction->id }})"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                        <i class="fas fa-credit-card mr-2"></i>
                                        Bayar Sekarang
                                    </button>
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
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>