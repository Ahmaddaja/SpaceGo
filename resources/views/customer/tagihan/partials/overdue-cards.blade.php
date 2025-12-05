<div class="mb-10">
    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-calendar-times text-orange-500 mr-2"></i>
        Rak Perlu Perpanjangan
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($overdueTransactions as $transaction)
            @php
                $sewaBerahir = \Carbon\Carbon::parse($transaction->sewa_berakhir);
                $now = now();
                $isOverdue = $now->gt($sewaBerahir);
                $totalHours = abs(floor($now->diffInHours($sewaBerahir)));
                if ($totalHours < 24) {
                    $overdueText = $totalHours . ' jam ' . ($isOverdue ? 'terlambat' : 'tersisa');
                    $isCritical = false;
                } else {
                    $totalDays = floor($totalHours / 24);
                    $remainingHours = $totalHours % 24;
                    $overdueText = $totalDays . ' hari';
                    if ($remainingHours > 0) $overdueText .= ' ' . $remainingHours . ' jam';
                    $overdueText .= ' ' . ($isOverdue ? 'terlambat' : 'tersisa');
                    $isCritical = $isOverdue && $totalDays > 7;
                }
                $statusColor = $isOverdue ? ($isCritical ? 'red' : 'orange') : 'blue';
            @endphp
            <div class="bg-white rounded-lg shadow p-6 border {{ $isCritical ? 'border-red-200' : 'border-orange-200' }}">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-bold text-gray-800">{{ $transaction->rak->nama_rak ?? 'Rak' }}</h3>
                        <p class="text-sm text-gray-500">Kode: {{ $transaction->order_id }}</p>
                    </div>
                    <span class="px-3 py-1 {{ $isCritical ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800' }} text-xs font-medium rounded-full">
                        {{ $overdueText }}
                    </span>
                </div>
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600 text-sm">Masa sewa berakhir:</span>
                        <span class="font-medium">{{ $sewaBerahir->format('d M Y') }}</span>
                    </div>
                    @if ($transaction->rak)
                        <div class="flex justify-between">
                            <span class="text-gray-600 text-sm">Biaya Perpanjangan:</span>
                            <span class="font-bold text-blue-600">
                                Rp {{ number_format($transaction->rak->harga_sewa_perbulan, 0, ',', '.') }}
                            </span>
                        </div>
                    @endif
                    @if ($isCritical)
                        <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center text-red-700">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <span class="text-sm font-medium">Segera perpanjang untuk menghindari sanksi</span>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="space-y-3">
                    <a href="{{ route('customer.payment.renewal-checkout', $transaction->id) }}"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition flex items-center justify-center">
                        <i class="fas fa-redo mr-2"></i>
                        Buat Permintaan Perpanjangan
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