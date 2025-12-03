@extends('layouts.app', ['title' => 'Checkout Rak - SPACEGO'])

@section('title', 'Checkout Rak - SPACEGO')

@push('styles')
    <style>
        /* Keep existing styles */
        .payment-card { transition: all 0.3s ease; border: 1px solid #e5e7eb; }
        .payment-card:hover { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
        .gradient-header { background: linear-gradient(135deg, #3b82f6, #8b5cf6); }
        .price-breakdown { background: linear-gradient(135deg, #f8fafc, #f1f5f9); }
        
        /* Update button styles */
        .action-button {
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            width: 100%;
            margin-top: 1rem;
        }
        
        .action-button:hover:not(:disabled) {
            background: linear-gradient(135deg, #1d4ed8, #7c3aed);
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.3);
        }
        
        .action-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .action-button-secondary {
            background: linear-gradient(135deg, #6b7280, #4b5563);
        }
        
        .action-button-secondary:hover:not(:disabled) {
            background: linear-gradient(135deg, #4b5563, #374151);
        }
        
        .action-button-success {
            background: linear-gradient(135deg, #10b981, #059669);
        }
        
        .action-button-success:hover:not(:disabled) {
            background: linear-gradient(135deg, #059669, #047857);
        }
        
        .checkout-info {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border-left: 4px solid #f59e0b;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }
        
        .checkout-info i {
            color: #d97706;
        }
        
        .checkout-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            background: #f3f4f6;
            border-radius: 8px;
            margin-top: 1rem;
        }
        
        .checkout-status i {
            color: #3b82f6;
        }
        
        /* Modal styles tetap sama */
        .alert-modal { /* ... existing styles ... */ }
    </style>
@endpush

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('customer.list-rak.list-rak') }}" 
                   class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium back-button">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Daftar Rak
                </a>
            </div>
            
            <!-- Checkout Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 payment-card">
                <!-- Header -->
                <div class="gradient-header p-8 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold">Checkout Rak</h1>
                            <p class="opacity-90 mt-1">Lengkapi proses penyewaan rak Anda</p>
                        </div>
                        <div class="p-3 bg-white/20 rounded-xl">
                            <i class="fas fa-shopping-cart text-2xl"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Checkout Info -->
                <div class="p-8">
                    <!-- Info Penting -->
                    <div class="checkout-info">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-xl mt-1 mr-3"></i>
                            <div>
                                <h3 class="font-bold text-gray-800">Informasi Penting</h3>
                                <p class="text-gray-700 mt-1">
                                    <strong>Transaksi akan dibuat setelah Anda klik "Lanjutkan ke Pembayaran".</strong><br>
                                    Setelah itu, Anda akan diarahkan ke halaman Tagihan untuk menyelesaikan pembayaran.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Rack Info -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Detail Rak</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-600">Nama Rak</p>
                                <p class="font-semibold text-lg">{{ $rak->nama_rak }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Kode Rak</p>
                                <p class="font-semibold text-lg">{{ $rak->kode_rak ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Lokasi Gudang</p>
                                <p class="font-semibold text-lg">{{ $rak->gudang->nama_gudang ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Status</p>
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                                    {{ ucfirst($rak->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Price Breakdown -->
                    <div class="price-breakdown rounded-xl p-6 mb-8">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Rincian Biaya</h2>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Harga Sewa/Bulan</span>
                                <span class="font-medium">Rp {{ number_format($rak->harga_sewa_perbulan, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Durasi Minimum</span>
                                <span class="font-medium">{{ $rak->durasi_sewa_hari ?? 30 }} hari</span>
                            </div>
                            <div class="border-t pt-4 mt-4">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total Pembayaran</span>
                                    <span class="text-blue-600">Rp {{ number_format($rak->harga_sewa_perbulan, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status Info -->
                    @php
                        // Cek apakah sudah ada transaksi pending untuk rak ini
                        $existingPending = \App\Models\Transaction::where('user_id', auth()->id())
                            ->where('rak_id', $rak->id)
                            ->where('transaction_status', 'pending')
                            ->exists();
                    @endphp
                    
                    @if($existingPending)
                    <div class="checkout-status">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            <p class="font-medium text-gray-800">Anda sudah memiliki transaksi pending untuk rak ini.</p>
                            <p class="text-sm text-gray-600">Silakan lanjutkan pembayaran di halaman Tagihan.</p>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Action Buttons -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-8">
                        <!-- Cancel Button -->
                        <button onclick="cancelCheckout()" class="action-button action-button-secondary">
                            <i class="fas fa-times"></i>
                            Batalkan
                        </button>
                        
                        <!-- Continue Button -->
                        @if($existingPending)
                        <a href="{{ route('customer.tagihan') }}" class="action-button action-button-success">
                            <i class="fas fa-arrow-right"></i>
                            Lanjut ke Tagihan
                        </a>
                        @else
                        <button onclick="processCheckout()" id="continue-button" class="action-button">
                            <i class="fas fa-lock"></i>
                            Lanjutkan ke Pembayaran
                        </button>
                        @endif
                    </div>
                    
                    <!-- Security Info -->
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-shield-alt mr-2 text-green-500"></i>
                            <span>Pembayaran aman dan terenkripsi dengan Midtrans</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Additional Info -->
            <div class="mt-8 grid md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-xl shadow border border-gray-100">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-blue-100 rounded-lg mr-4">
                            <i class="fas fa-clock text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Proses Cepat</h3>
                            <p class="text-sm text-gray-600">Checkout hanya membutuhkan 2 menit</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow border border-gray-100">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-green-100 rounded-lg mr-4">
                            <i class="fas fa-lock text-green-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Pembayaran Aman</h3>
                            <p class="text-sm text-gray-600">Dilindungi oleh Midtrans</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow border border-gray-100">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-purple-100 rounded-lg mr-4">
                            <i class="fas fa-headset text-purple-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Bantuan 24/7</h3>
                            <p class="text-sm text-gray-600">Customer Service siap membantu</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white p-8 rounded-2xl shadow-2xl text-center">
            <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-blue-600 mx-auto mb-4"></div>
            <p class="text-lg font-semibold text-gray-800">Memproses...</p>
            <p class="text-gray-600 mt-2">Mohon tunggu sebentar</p>
        </div>
    </div>
    
    <!-- Alert Container -->
    <div id="alert-container"></div>
@endsection

@push('scripts')
    <script>
        const continueButton = document.getElementById('continue-button');
        const loadingOverlay = document.getElementById('loading-overlay');
        const alertContainer = document.getElementById('alert-container');

        // Fungsi untuk menampilkan modal alert
        function showAlert(message, type = 'info', autoRedirect = false, redirectUrl = null) {
            const alertTypes = {
                success: {
                    icon: 'fas fa-check-circle',
                    class: 'alert-modal-success',
                    title: 'Sukses!',
                    buttonText: 'Lanjutkan'
                },
                error: {
                    icon: 'fas fa-exclamation-triangle',
                    class: 'alert-modal-error',
                    title: 'Error!',
                    buttonText: 'Coba Lagi'
                },
                warning: {
                    icon: 'fas fa-bolt',
                    class: 'alert-modal-warning',
                    title: 'Peringatan!',
                    buttonText: 'Mengerti'
                },
                info: {
                    icon: 'fas fa-info-circle',
                    class: 'alert-modal-info',
                    title: 'Informasi',
                    buttonText: 'OK'
                }
            };

            const alertConfig = alertTypes[type] || alertTypes.info;
            const alertId = 'alert-' + Date.now();
            
            const alertHTML = `
                <div id="${alertId}" class="alert-modal ${alertConfig.class}">
                    <div class="alert-modal-content">
                        <div class="alert-modal-header">
                            <div class="alert-modal-icon">
                                <i class="${alertConfig.icon}"></i>
                            </div>
                            <div class="alert-modal-title">${alertConfig.title}</div>
                        </div>
                        <div class="alert-modal-body">
                            <div class="alert-modal-message">${message}</div>
                            ${autoRedirect ? `
                                <div class="alert-countdown">
                                    <i class="fas fa-clock"></i>
                                    Akan dialihkan dalam 
                                    <span class="countdown-number" id="countdown-${alertId}">5</span> 
                                    detik
                                </div>
                            ` : ''}
                        </div>
                        <div class="alert-modal-footer">
                            <button onclick="closeAlert('${alertId}')" class="alert-modal-button alert-modal-button-secondary">
                                <i class="fas fa-times mr-2"></i>
                                Tutup
                            </button>
                            <button onclick="handleAlertAction('${alertId}', ${autoRedirect}, '${redirectUrl}')" 
                                    class="alert-modal-button alert-modal-button-primary">
                                <i class="fas fa-check mr-2"></i>
                                ${alertConfig.buttonText}
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            alertContainer.insertAdjacentHTML('beforeend', alertHTML);
            
            const alertElement = document.getElementById(alertId);
            
            setTimeout(() => {
                alertElement.classList.add('show');
            }, 100);

            if (autoRedirect) {
                startCountdown(alertId, redirectUrl);
            }

            return alertId;
        }

        // Fungsi countdown
        function startCountdown(alertId, redirectUrl) {
            let countdown = 5;
            const countdownElement = document.getElementById(`countdown-${alertId}`);
            const countdownInterval = setInterval(() => {
                countdown--;
                if (countdownElement) {
                    countdownElement.textContent = countdown;
                }
                
                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                    handleAlertAction(alertId, true, redirectUrl);
                }
            }, 1000);

            const alertElement = document.getElementById(alertId);
            alertElement.dataset.countdownInterval = countdownInterval;
        }

        // Fungsi untuk handle tombol action
        function handleAlertAction(alertId, autoRedirect = false, redirectUrl = null) {
            closeAlert(alertId);
            
            if (autoRedirect && redirectUrl) {
                setTimeout(() => {
                    window.location.href = redirectUrl;
                }, 300);
            }
        }

        // Fungsi untuk menutup alert
        function closeAlert(alertId) {
            const alertElement = document.getElementById(alertId);
            if (!alertElement) return;

            if (alertElement.dataset.countdownInterval) {
                clearInterval(alertElement.dataset.countdownInterval);
            }

            alertElement.classList.remove('show');
            
            setTimeout(() => {
                if (alertElement.parentNode) {
                    alertElement.parentNode.removeChild(alertElement);
                }
            }, 400);
        }

        // Fungsi untuk cancel checkout
        function cancelCheckout() {
            if (confirm('Apakah Anda yakin ingin membatalkan checkout?')) {
                window.location.href = "{{ route('payment.cancel-checkout') }}";
            }
        }

        // Fungsi untuk process checkout
        function processCheckout() {
            if (!continueButton) return;
            
            // Disable button dan tampilkan loading
            continueButton.disabled = true;
            continueButton.innerHTML = `
                <i class="fas fa-spinner fa-spin"></i>
                Memproses...
            `;
            loadingOverlay.classList.remove('hidden');

            // Kirim request untuk membuat transaksi
            fetch("{{ route('payment.process-checkout') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                loadingOverlay.classList.add('hidden');
                
                if (data.success) {
                    // Transaksi berhasil dibuat, redirect ke tagihan
                    showAlert(
                        'Transaksi berhasil dibuat! Anda akan dialihkan ke halaman Tagihan untuk menyelesaikan pembayaran.', 
                        'success', 
                        true, 
                        "{{ route('customer.tagihan') }}"
                    );
                } else {
                    // Jika sudah ada transaksi pending
                    if (data.redirect_url) {
                        showAlert(
                            data.message, 
                            'warning', 
                            true, 
                            data.redirect_url
                        );
                    } else {
                        showAlert(
                            'Gagal membuat transaksi: ' + data.message, 
                            'error'
                        );
                        // Reset button
                        continueButton.disabled = false;
                        continueButton.innerHTML = `
                            <i class="fas fa-lock"></i>
                            Lanjutkan ke Pembayaran
                        `;
                    }
                }
            })
            .catch(error => {
                loadingOverlay.classList.add('hidden');
                console.error('Error:', error);
                showAlert(
                    'Terjadi kesalahan. Silakan coba lagi atau hubungi customer service.', 
                    'error'
                );
                // Reset button
                continueButton.disabled = false;
                continueButton.innerHTML = `
                    <i class="fas fa-lock"></i>
                    Lanjutkan ke Pembayaran
                `;
            });
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('alert-modal')) {
                closeAlert(e.target.id);
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const openModal = document.querySelector('.alert-modal.show');
                if (openModal) {
                    closeAlert(openModal.id);
                }
            }
        });

        // Auto-close session jika ada warning
        @if(session('warning'))
            showAlert('{{ session('warning') }}', 'warning');
        @endif
        
        @if(session('info'))
            showAlert('{{ session('info') }}', 'info');
        @endif
    </script>
@endpush