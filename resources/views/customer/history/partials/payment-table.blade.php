<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Transaksi</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @if(isset($tableName) && $tableName)
                @foreach($payments as $payment)
                    <tr class="hover:bg-gray-50 transition-all duration-300">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if(isset($payment->created_at))
                                <div class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($payment->created_at)->format('d M Y') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($payment->created_at)->format('H:i') }}
                                </div>
                            @else
                                <span class="text-sm text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                @if(isset($payment->order_id))
                                    {{ $payment->order_id }}
                                @elseif(isset($payment->id))
                                    #{{ $payment->id }}
                                @else
                                    -
                                @endif
                            </div>
                            @if(isset($payment->rak_id))
                                <div class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-cube mr-1"></i>
                                    Rak ID: {{ $payment->rak_id }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if(isset($payment->amount))
                                <span class="text-lg font-bold text-green-600">
                                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-sm text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if(isset($payment->payment_type))
                                <div class="flex items-center">
                                    @if($payment->payment_type == 'qris')
                                        <i class="fas fa-qrcode mr-2 text-blue-500"></i>
                                    @elseif($payment->payment_type == 'bank_transfer')
                                        <i class="fas fa-university mr-2 text-green-500"></i>
                                    @else
                                        <i class="fas fa-credit-card mr-2 text-gray-500"></i>
                                    @endif
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ strtoupper($payment->payment_type) }}
                                    </span>
                                </div>
                            @else
                                <span class="text-sm text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if(isset($payment->transaction_status))
                                @if($payment->transaction_status == 'settlement')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>Berhasil
                                    </span>
                                @elseif($payment->transaction_status == 'pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>Pending
                                    </span>
                                @elseif($payment->transaction_status == 'expire')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>Kedaluwarsa
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        {{ ucfirst($payment->transaction_status) }}
                                    </span>
                                @endif
                            @else
                                <span class="text-sm text-gray-500">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <!-- Fallback ke data lama dari CustomerHistory -->
                @foreach($payments as $payment)
                    <tr class="hover:bg-gray-50 transition-all duration-300">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $payment->created_at->format('d M Y') }}</div>
                            <div class="text-sm text-gray-500">{{ $payment->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $payment->description }}</div>
                            @if($payment->additional_data && isset($payment->additional_data['payment_method']))
                                <div class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-credit-card mr-1"></i>
                                    {{ $payment->additional_data['payment_method'] }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($payment->additional_data && isset($payment->additional_data['amount']))
                                <span class="text-lg font-bold text-green-600">
                                    Rp {{ number_format($payment->additional_data['amount'], 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-sm text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($payment->activity_type == 'PAYMENT_SUCCESS')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>Berhasil
                                </span>
                            @elseif($payment->activity_type == 'PAYMENT_FAILED')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times mr-1"></i>Gagal
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-info mr-1"></i>Pembayaran
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
    
    <!-- Pagination -->
    @if($payments->hasPages())
        <div class="mt-6">
            {{ $payments->links() }}
        </div>
    @endif
</div>

@if(isset($payments) && count($payments) == 0)
    <div class="text-center py-12">
        <i class="fas fa-receipt text-4xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-700">Belum ada riwayat pembayaran</h3>
        <p class="text-gray-500 mt-1">Semua transaksi pembayaran akan muncul di sini</p>
    </div>
@endif