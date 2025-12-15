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
                
                // Opsi untuk lepas rak hanya tersedia jika TIDAK ada denda
                $showLepasRak = !$isOverdue;
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
                    {{-- Tombol Perpanjang --}}
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
                    
                    {{-- Opsi Kedua: Conditional berdasarkan status denda --}}
                    @if ($showLepasRak)
                        {{-- OPSI 1: Jika TIDAK ada denda (masih normal/masa tenggang) --}}
                        <form action="{{ route('customer.tagihan.process-expired', $transaction->id) }}" method="POST" id="form-lepas-{{ $transaction->id }}">
                            @csrf
                            <button type="button" onclick="confirmLepasRak({{ $transaction->id }})"
                                class="w-full border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2.5 rounded-lg transition">
                                <i class="fas fa-times mr-2"></i>
                                Lepas Rak
                            </button>
                        </form>
                    @else
                        {{-- OPSI 2: Jika sudah ada denda --}}
                        <button type="button" onclick="showDendaOptions({{ $transaction->id }}, {{ $totalDenda }})"
                            class="w-full border border-red-300 text-red-600 hover:bg-red-50 font-medium py-2.5 rounded-lg transition">
                            <i class="fas fa-file-invoice-dollar mr-2"></i>
                            Bayar Denda & Lepas Rak
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Modal untuk Bayar Denda & Lepas Rak --}}
<div id="dendaModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-800">Konfirmasi Pembayaran Denda</h3>
                <button type="button" onclick="closeDendaModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            
            <!-- Ubah class dari modal-body menjadi modal-content-body untuk menghindari konflik -->
            <div class="modal-content-body mb-6">                
                <div id="modalDetails">
                    <!-- Konten akan diisi oleh JavaScript -->
                </div>
            </div>
            
            <div class="flex gap-3">
                <button type="button" onclick="closeDendaModal()"
                    class="flex-1 border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-3 rounded-lg transition">
                    Kembali
                </button>
                <button type="button" onclick="processDendaPayment()" id="confirmDendaBtn"
                    class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-3 rounded-lg transition">
                    <span id="btnText">Konfirmasi & Bayar</span>
                    <span id="btnLoading" class="hidden">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Memproses...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentTransactionId = null;
let totalDenda = 0;

// **PERUBAHAN PENTING: Hapus parameter hargaSewa**
function showDendaOptions(transactionId, totalDenda) {
    currentTransactionId = transactionId;
    totalDenda = totalDenda;
    
    console.log('Show denda options:', { transactionId, totalDenda }); // Debug
    
    // Format angka
    const formatter = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    });
    
    // Update konten modal
    const modalDetails = document.getElementById('modalDetails');
    modalDetails.innerHTML = `
        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-4">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 mr-3 mt-0.5"></i>
                <div>
                    <p class="text-blue-700 text-sm">
                        Anda hanya membayar <span class="font-bold">DENDA</span> keterlambatan sebesar
                        <p class="text-blue-700 text-sm"> ({{ $latenessDays }} hari × Rp {{ number_format($dendaPerHari, 0, ',', '.') }}): </p>
                    </p>

                    <p class="text-2xl font-bold text-red-600 mt-2 text-center">
                        ${formatter.format(totalDenda)}
                    </p>
                    <p class="text-xs text-blue-600 mt-2 text-center">
                        Biaya sewa tidak perlu dibayar lagi
                    </p>
                </div>
            </div>
        </div>
        
        <div class="p-4 bg-red-50 border border-red-200 rounded-lg mb-4">
            <div class="flex items-start text-red-700">
                <i class="fas fa-exclamation-triangle mr-2 mt-0.5"></i>
                <div class="text-sm">
                    <span class="font-medium">Peringatan!</span>
                    <p class="mt-1 text-xs">
                        Setelah pembayaran denda berhasil, rak akan <strong>otomatis dilepaskan</strong> 
                        dan tersedia untuk penyewa lain.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="space-y-3">
            <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg border">
                <div>
                    <span class="text-gray-800 font-medium">Jumlah Denda:</span>
                    <p class="text-xs text-gray-600 mt-1">Keterlambatan berdasarkan hari telat</p>
                </div>
                <span class="font-bold text-red-600 text-lg">${formatter.format(totalDenda)}</span>
            </div>
        </div>
    `;
    
    // Show modal dengan efek
    const modal = document.getElementById('dendaModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Tambahkan animasi fade in
    setTimeout(() => {
        modal.style.opacity = '1';
        modal.style.transition = 'opacity 0.3s ease';
    }, 10);
}

function closeDendaModal() {
    const modal = document.getElementById('dendaModal');
    modal.style.opacity = '0';
    
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.style.opacity = '';
        currentTransactionId = null;
        totalDenda = 0;
    }, 300);
}

function processDendaPayment() {
    if (!currentTransactionId) {
        alert('Transaksi tidak ditemukan');
        return;
    }
    
    const btn = document.getElementById('confirmDendaBtn');
    const btnText = document.getElementById('btnText');
    const btnLoading = document.getElementById('btnLoading');
    
    console.log('Processing denda for transaction:', currentTransactionId, 'Amount:', totalDenda); // Debug
    
    // Show loading
    btn.disabled = true;
    btnText.classList.add('hidden');
    btnLoading.classList.remove('hidden');
    
    // Kirim HANYA total_denda
    fetch('{{ route("customer.tagihan.pay-denda-and-release") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            transaction_id: currentTransactionId,
            total_denda: totalDenda
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data); // Debug
        
        if (data.success) {
            if (data.redirect_url) {
                // Redirect ke halaman pembayaran denda
                window.location.href = data.redirect_url;
            } else {
                // Jika tidak ada redirect_url, tampilkan pesan sukses
                alert(data.message || 'Pembayaran denda berhasil diproses');
                window.location.reload();
            }
        } else {
            alert(data.message || 'Terjadi kesalahan dalam memproses pembayaran denda');
            // Reset button state
            resetButtonState();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
        // Reset button state
        resetButtonState();
    });
    
    function resetButtonState() {
        btn.disabled = false;
        btnText.classList.remove('hidden');
        btnLoading.classList.add('hidden');
    }
}

// Function untuk confirm lepas rak (existing)
function confirmLepasRak(transactionId) {
    if (confirm('Apakah Anda yakin ingin melepas rak? Rak akan tersedia untuk penyewa lain.')) {
        document.getElementById(`form-lepas-${transactionId}`).submit();
    }
}

// Tambahkan event listener untuk escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDendaModal();
    }
});

// Tambahkan event listener untuk klik di luar modal
document.getElementById('dendaModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeDendaModal();
    }
});
</script>

<style>
#dendaModal {
    transition: opacity 0.3s ease;
}

/* Style untuk spinner */
.fa-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>