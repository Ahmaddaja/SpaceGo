@extends('layouts.app')

@section('title', 'Bayar Rak - SPACEGO')

@push('styles')
    <style>
        .payment-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        .payment-card:hover {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .gradient-header {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        }

        .price-breakdown {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        }

        .pay-button {
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        }

        .pay-button:hover:not(:disabled) {
            background: linear-gradient(135deg, #1d4ed8, #7c3aed);
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.3);
        }

        .pay-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .back-button {
            transition: all 0.3s ease;
        }

        .back-button:hover {
            transform: translateX(-5px);
        }

        /* Loading overlay */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .loading-overlay.active {
            display: flex;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3b82f6;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Back Button -->
            <a href="{{ route('customer.list-rak.list-rak') }}"
                class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-8 transition-all duration-300 back-button group">
                <i class="fas fa-arrow-left mr-3 group-hover:-translate-x-1 transition-transform"></i>
                Kembali ke Daftar Rak
            </a>

            <!-- Payment Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 payment-card">
                <!-- Header with Gradient -->
                <div class="gradient-header p-8 text-white">
                    <div class="flex items-center mb-4">
                        <div class="bg-white/20 p-4 rounded-2xl mr-6 backdrop-blur-sm">
                            <i class="fas fa-credit-card text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">Pembayaran Sewa Rak</h1>
                            <p class="text-blue-100 mt-2 text-lg">Selesaikan pembayaran Anda dengan mudah dan aman</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="p-8">
                    <!-- Rack Info -->
                    <div
                        class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 mb-8 border border-blue-100 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4">
                                <div
                                    class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-4 rounded-xl shadow-md">
                                    <i class="fas fa-pallet text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 font-medium mb-1">Nama Rak</p>
                                    <h2 class="text-2xl font-bold text-gray-800">{{ $rak->nama_rak }}</h2>
                                    <p class="text-gray-600 mt-2 flex items-center">
                                        <i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>
                                        Lokasi: {{ $rak->lokasi_gudang ?? 'Gudang Utama' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="price-breakdown rounded-xl p-6 mb-8 border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-receipt text-blue-600 mr-3"></i>
                            Rincian Pembayaran
                        </h3>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-200">
                                <span class="text-gray-600 flex items-center">
                                    <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                                    Sewa per Bulan
                                </span>
                                <span class="font-semibold text-gray-800">Rp
                                    {{ number_format($rak->harga_sewa_perbulan, 0, ',', '.') }}</span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-200">
                                <span class="text-gray-600 flex items-center">
                                    <i class="fas fa-cog text-green-500 mr-2"></i>
                                    Biaya Admin
                                </span>
                                <span class="font-semibold text-green-600">Gratis</span>
                            </div>

                            <div class="flex justify-between items-center pt-4">
                                <span class="text-xl font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-tag text-purple-600 mr-2"></i>
                                    Total Pembayaran
                                </span>
                                <span class="text-2xl font-bold text-blue-600">Rp
                                    {{ number_format($rak->harga_sewa_perbulan, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Methods Info -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-600 text-lg mr-4 mt-1"></i>
                            <div>
                                <p class="font-semibold text-blue-900 mb-2 text-lg">Metode Pembayaran Tersedia</p>
                                <p class="text-blue-700">Transfer Bank, Kartu Kredit, E-Wallet, Virtual Account, dan lainnya
                                    melalui Midtrans</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Button -->
                    <button id="pay-button"
                        class="w-full pay-button text-white py-4 rounded-xl font-bold text-lg transition-all duration-300 shadow-lg flex items-center justify-center group">
                        <i class="fas fa-lock mr-3 group-hover:scale-110 transition-transform"></i>
                        Bayar Sekarang
                        <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
                    </button>

                    <!-- Security Info -->
                    <div class="mt-6 flex items-center justify-center text-sm text-gray-500">
                        <i class="fas fa-shield-alt text-green-600 mr-2"></i>
                        Pembayaran Anda aman dan terenkripsi
                    </div>
                </div>
            </div>

            <!-- Additional Info Cards -->
            <div class="mt-12 grid md:grid-cols-3 gap-8">
                @include('customer.payment.partials.info-cards')
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="loading-overlay">
        <div class="text-center">
            <div class="spinner mx-auto mb-4"></div>
            <p class="text-white text-lg font-semibold">Memproses pembayaran...</p>
        </div>
    </div>

    <!-- WhatsApp Button -->
    @include('customer.payment.partials.whatsapp-button')
@endsection

@push('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>
    <script>
        const payButton = document.getElementById('pay-button');
        const loadingOverlay = document.getElementById('loading-overlay');

        // Fungsi untuk reset button
        function resetButton() {
            payButton.disabled = false;
            payButton.innerHTML = `
            <i class="fas fa-lock mr-3"></i>
            Bayar Sekarang
            <i class="fas fa-arrow-right ml-3"></i>
        `;
        }

        // Fungsi untuk update status ke server
        function updatePaymentStatus(result) {
            loadingOverlay.classList.add('active');

            fetch("{{ route('payment.update-status') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        order_id: result.order_id,
                        transaction_status: result.transaction_status,
                        payment_type: result.payment_type || null
                    })
                })
                .then(response => response.json())
                .then(data => {
                    loadingOverlay.classList.remove('active');

                    if (data.success) {
                        alert("✅ Pembayaran berhasil! Terima kasih telah melakukan pembayaran.");
                        window.location.href = "{{ route('customer.list-rak.rak') }}";
                    } else {
                        alert("⚠️ Pembayaran berhasil tapi gagal update status. Silakan hubungi admin.");
                        console.error('Update status failed:', data.message);
                        window.location.href = "{{ route('customer.list-rak.rak') }}";
                    }
                })
                .catch(error => {
                    loadingOverlay.classList.remove('active');
                    console.error('Error updating status:', error);
                    alert("✅ Pembayaran berhasil! Redirect ke halaman rak...");
                    window.location.href = "{{ route('customer.list-rak.rak') }}";
                });
        }

        // Event listener untuk tombol bayar
        payButton.addEventListener('click', function() {
            // Disable button dan tampilkan loading
            payButton.disabled = true;
            payButton.innerHTML = `
            <i class="fas fa-spinner fa-spin mr-3"></i>
            Memproses Pembayaran...
        `;

            // Buka popup Midtrans
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
                    console.log('Payment Success:', result);
                    updatePaymentStatus(result);
                },

                onPending: function(result) {
                    console.log('Payment Pending:', result);
                    alert("⏳ Pembayaran pending! Mohon selesaikan pembayaran Anda.");
                    resetButton();
                },

                onError: function(result) {
                    console.error('Payment Error:', result);
                    alert("❌ Pembayaran gagal! Silakan coba lagi.");
                    resetButton();
                },

                onClose: function() {
                    console.log('Payment popup closed');
                    alert("ℹ️ Anda menutup popup pembayaran tanpa menyelesaikan transaksi.");
                    resetButton();
                }
            });
        });
    </script>
@endpush
