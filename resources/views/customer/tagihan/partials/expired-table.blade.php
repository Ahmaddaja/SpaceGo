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
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($expiredTransactions as $transaction)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium">{{ $transaction->rak->nama_rak ?? 'Rak' }}</div>
                                <div class="text-sm text-gray-500">{{ $transaction->order_id }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Kadaluarsa
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if ($transaction->is_renewal)
                                    <span class="text-sm text-gray-600"><i class="fas fa-redo mr-1"></i> Pembayaran perpanjangan</span>
                                @else
                                    <span class="text-sm text-gray-600"><i class="fas fa-clock mr-1"></i> Pembayaran awal</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600">
                                    {{ $transaction->created_at->format('d M Y') }}
                                </div>
                                @if ($transaction->sewa_berakhir)
                                    <div class="text-xs text-gray-500">
                                        Berakhir: {{ \Carbon\Carbon::parse($transaction->sewa_berakhir)->format('d M Y') }}
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