<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
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
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check mr-1"></i>Berhasil
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($payment->additional_data)
                            <button onclick="showPaymentDetails({{ json_encode($payment->additional_data) }})" 
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 flex items-center">
                                <i class="fas fa-eye mr-2"></i>Detail
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>