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

{{-- TAMBAHAN: Script untuk handle snap token kosong --}}
<script>
function continuePayment(snapToken, transactionId) {
    if (!snapToken || snapToken === '' || snapToken === 'null') {
        Swal.fire({
            icon: 'error',
            title: 'Token Tidak Tersedia',
            text: 'Token pembayaran tidak ditemukan. Silakan refresh halaman atau hubungi admin.',
            confirmButtonColor: '#3085d6'
        });
        return;
    }

    if (!transactionId || transactionId === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Transaksi Tidak Valid',
            text: 'ID transaksi tidak ditemukan. Silakan refresh halaman.',
            confirmButtonColor: '#3085d6'
        });
        return;
    }

    // Buka Midtrans Snap
    snap.pay(snapToken, {
        onSuccess: function(result) {
            console.log('Payment success:', result);
            updateTransactionStatus(transactionId, result.transaction_status, result.payment_type);
        },
        onPending: function(result) {
            console.log('Payment pending:', result);
            Swal.fire({
                icon: 'info',
                title: 'Pembayaran Pending',
                text: 'Pembayaran Anda sedang diproses.',
                confirmButtonColor: '#3085d6'
            }).then(() => {
                window.location.reload();
            });
        },
        onError: function(result) {
            console.error('Payment error:', result);
            Swal.fire({
                icon: 'error',
                title: 'Pembayaran Gagal',
                text: 'Terjadi kesalahan saat memproses pembayaran.',
                confirmButtonColor: '#3085d6'
            });
        },
        onClose: function() {
            console.log('Payment popup closed');
        }
    });
}

function updateTransactionStatus(transactionId, status, paymentType) {
    fetch('/customer/payment/update-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            transaction_id: transactionId,
            transaction_status: status,
            payment_type: paymentType
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Pembayaran Berhasil!',
                text: 'Transaksi Anda telah berhasil diproses.',
                confirmButtonColor: '#10b981'
            }).then(() => {
                window.location.reload();
            });
        }
    })
    .catch(error => {
        console.error('Error updating status:', error);
    });
}

// FUNGSI BARU: Lihat Detail Tagihan
function viewDetail(tagihanId) {
    fetch(`/customer/tagihan/${tagihanId}/detail`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const tagihan = data.tagihan;
            
            Swal.fire({
                title: '<strong>Detail Tagihan</strong>',
                html: `
                    <div class="text-left space-y-3">
                        <div class="border-b pb-2">
                            <p class="text-sm text-gray-500">Kode Tagihan</p>
                            <p class="font-semibold text-gray-800">${tagihan.tagihan_code}</p>
                        </div>
                        
                        <div class="border-b pb-2">
                            <p class="text-sm text-gray-500">Nama Rak</p>
                            <p class="font-semibold text-gray-800">${tagihan.rak_nama}</p>
                        </div>
                        
                        <div class="border-b pb-2">
                            <p class="text-sm text-gray-500">Order ID</p>
                            <p class="font-mono text-xs text-gray-600">${tagihan.order_id}</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 border-b pb-2">
                            <div>
                                <p class="text-sm text-gray-500">Harga Sewa</p>
                                <p class="font-semibold text-gray-800">${tagihan.harga_sewa}</p>
                            </div>
                            ${tagihan.penalty_amount > 0 ? `
                            <div>
                                <p class="text-sm text-gray-500">Denda</p>
                                <p class="font-semibold text-red-600">${tagihan.penalty}</p>
                            </div>
                            ` : ''}
                        </div>
                        
                        <div class="bg-blue-50 p-3 rounded">
                            <p class="text-sm text-gray-500">Total Tagihan</p>
                            <p class="text-xl font-bold text-blue-600">${tagihan.total_tagihan}</p>
                        </div>
                        
                        <div class="border-b pb-2">
                            <p class="text-sm text-gray-500">Status</p>
                            <p class="font-semibold text-yellow-600 capitalize">${tagihan.status}</p>
                        </div>
                        
                        ${tagihan.is_renewal ? `
                        <div class="bg-purple-50 p-2 rounded">
                            <p class="text-xs text-purple-700">
                                <i class="fas fa-redo mr-1"></i> Tagihan Perpanjangan
                            </p>
                        </div>
                        ` : ''}
                        
                        <div class="border-b pb-2">
                            <p class="text-sm text-gray-500">Dibuat</p>
                            <p class="text-sm text-gray-700">${tagihan.created_at}</p>
                        </div>
                        
                        <div class="bg-yellow-50 p-3 rounded">
                            <p class="text-sm text-gray-500">Batas Pembayaran</p>
                            <p class="font-semibold text-orange-600">${tagihan.expired_at}</p>
                            <p class="text-xs text-gray-600 mt-1">${tagihan.remaining_time}</p>
                        </div>
                    </div>
                `,
                width: 600,
                showCloseButton: true,
                showCancelButton: false,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#3085d6'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: data.message || 'Tidak dapat memuat detail tagihan',
                confirmButtonColor: '#3085d6'
            });
        }
    })
    .catch(error => {
        console.error('Error fetching detail:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan saat memuat detail',
            confirmButtonColor: '#3085d6'
        });
    });
}

// FUNGSI BARU: Batalkan Tagihan
function cancelPayment(tagihanId, tagihanCode) {
    Swal.fire({
        title: 'Batalkan Tagihan?',
        html: `
            <p class="text-gray-600 mb-2">Anda yakin ingin membatalkan tagihan ini?</p>
            <p class="text-sm text-gray-500 font-mono">${tagihanCode}</p>
            <p class="text-xs text-red-600 mt-3">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Tindakan ini tidak dapat dibatalkan
            </p>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Batalkan',
        cancelButtonText: 'Tidak',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang membatalkan tagihan',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/customer/tagihan/${tagihanId}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message || 'Tagihan berhasil dibatalkan',
                        confirmButtonColor: '#10b981'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Tidak dapat membatalkan tagihan',
                        confirmButtonColor: '#dc2626'
                    });
                }
            })
            .catch(error => {
                console.error('Error canceling payment:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat membatalkan tagihan',
                    confirmButtonColor: '#dc2626'
                });
            });
        }
    });
}
</script>