@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Tagihan & Perpanjangan</h1>
                <p class="text-gray-600 mt-2">Kelola pembayaran dan perpanjangan sewa rak Anda</p>
            </div>
            <a href="{{ route('customer.tagihan.check-overdue') }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-sync-alt mr-2"></i>Refresh Status
            </a>
        </div>
        
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-yellow-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500">Menunggu Pembayaran</p>
                        <p class="text-2xl font-bold">{{ $pendingTransactions->count() }}</p>
                    </div>
                    <i class="fas fa-clock text-yellow-500 text-2xl"></i>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-red-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500">Kadaluarsa</p>
                        <p class="text-2xl font-bold">{{ $expiredTransactions->count() }}</p>
                    </div>
                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-orange-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500">Perlu Perpanjangan</p>
                        <p class="text-2xl font-bold">{{ $overdueTransactions->count() }}</p>
                    </div>
                    <i class="fas fa-calendar-times text-orange-500 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Menunggu Pembayaran -->
    @if($pendingTransactions->count() > 0)
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
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($pendingTransactions as $transaction)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium">{{ $transaction->rak->nama_rak ?? 'Rak' }}</div>
                            <div class="text-sm text-gray-500">Order: {{ $transaction->order_id }}</div>
                            <div class="text-xs text-gray-400 mt-1">
                                Dibuat: {{ $transaction->created_at->format('d M Y H:i') }}
                            </div>
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
                            <!-- Tombol untuk melanjutkan pembayaran ke Midtrans -->
                            <button onclick="continuePayment('{{ $transaction->snap_token }}', {{ $transaction->id }})" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                <i class="fas fa-credit-card mr-2"></i>
                                Bayar Sekarang
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
    @endif

    <!-- Perlu Perpanjangan -->
    @if($overdueTransactions->count() > 0)
    <div class="mb-10">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-calendar-times text-orange-500 mr-2"></i>
            Rak Perlu Perpanjangan
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($overdueTransactions as $transaction)
            @php
                $daysOverdue = now()->diffInDays($transaction->sewa_berakhir);
            @endphp
            
            <div class="bg-white rounded-lg shadow p-6 border border-orange-200">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-bold text-gray-800">{{ $transaction->rak->nama_rak ?? 'Rak' }}</h3>
                        <p class="text-sm text-gray-500">Kode: {{ $transaction->order_id }}</p>
                    </div>
                    <span class="px-3 py-1 bg-orange-100 text-orange-800 text-xs font-medium rounded-full">
                        {{ $daysOverdue }} hari terlambat
                    </span>
                </div>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600 text-sm">Masa Sewa Berakhir:</span>
                            <span class="font-medium">
                                {{ \Carbon\Carbon::parse($transaction->sewa_berakhir)->format('d M Y') }}
                            </span>
                    </div>
                    @if($transaction->rak)
                    <div class="flex justify-between">
                        <span class="text-gray-600 text-sm">Biaya Perpanjangan:</span>
                        <span class="font-bold text-blue-600">
                            Rp {{ number_format($transaction->rak->harga_sewa_perbulan, 0, ',', '.') }}
                        </span>
                    </div>
                    @endif
                </div>
                
                <div class="flex space-x-3">
                    <a href="{{ route('customer.tagihan.create-payment', $transaction->id) }}" 
                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition flex items-center justify-center">
                        <i class="fas fa-redo mr-2"></i>
                        Perpanjang
                    </a>
                    
                    <form action="{{ route('customer.tagihan.process-expired', $transaction->id) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" 
                                class="w-full border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2.5 rounded-lg transition">
                            Lepas
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Kadaluarsa -->
    @if($expiredTransactions->count() > 0)
    <div class="mb-10">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
            Pembayaran Kadaluarsa
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($expiredTransactions as $transaction)
            <div class="bg-white rounded-lg shadow p-6 border border-red-200">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-bold text-gray-800">{{ $transaction->rak->nama_rak ?? 'Rak' }}</h3>
                        <p class="text-sm text-gray-500">{{ $transaction->order_id }}</p>
                    </div>
                    <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                        Kadaluarsa
                    </span>
                </div>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600 text-sm">Jumlah:</span>
                        <span class="font-semibold">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 text-sm">Tanggal:</span>
                        <span class="text-gray-800">{{ $transaction->created_at->format('d M Y') }}</span>
                    </div>
                </div>
                
                <a href="{{ route('customer.tagihan.create-payment', $transaction->id) }}" 
                   class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2.5 rounded-lg transition flex items-center justify-center">
                    <i class="fas fa-redo mr-2"></i>
                    Bayar Ulang
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Empty State -->
    @if($pendingTransactions->count() == 0 && $expiredTransactions->count() == 0 && $overdueTransactions->count() == 0)
    <div class="text-center py-16">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 mb-6">
            <i class="fas fa-check-circle text-green-600 text-3xl"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Tagihan</h3>
        <p class="text-gray-500 mb-8">Semua pembayaran Anda sudah berhasil diproses.</p>
        <a href="{{ route('customer.list-rak.list-rak') }}" 
           class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-pallet mr-2"></i>
            Sewa Rak Baru
        </a>
    </div>
    @endif
</div>

<!-- Auto-check status untuk pending transactions -->
@if($pendingTransactions->count() > 0)
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    function continuePayment(snapToken, transactionId) {
        // Buka Midtrans popup untuk melanjutkan pembayaran
        snap.pay(snapToken, {
            onSuccess: function(result) {
                console.log('Payment Success:', result);
                // Update status pembayaran
                updatePaymentStatus(result, transactionId);
            },
            onPending: function(result) {
                console.log('Payment Pending:', result);
                alert('Pembayaran Anda dalam proses pending. Mohon selesaikan pembayaran Anda.');
                location.reload(); // Reload untuk update status
            },
            onError: function(result) {
                console.error('Payment Error:', result);
                alert('Pembayaran gagal! Silakan coba lagi.');
            },
            onClose: function() {
                console.log('Payment popup closed');
                alert('Anda menutup popup pembayaran.');
            }
        });
    }
    
    function updatePaymentStatus(result, transactionId) {
        fetch("{{ route('payment.update-status') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                order_id: result.order_id,
                transaction_status: result.transaction_status,
                payment_type: result.payment_type || null,
                transaction_id: transactionId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Pembayaran berhasil!');
                location.reload();
            } else {
                alert('Pembayaran berhasil tetapi gagal update status.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Pembayaran berhasil!');
            location.reload();
        });
    }
</script>
@endif
@endsection