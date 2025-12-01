@extends('layouts.app', ['title' => 'Bayar Rak - SPACEGO'])

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
        /* Modern Modal Styles */
        .alert-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .alert-modal.show {
            opacity: 1;
            visibility: visible;
        }

        .alert-modal-content {
            background: white;
            border-radius: 20px;
            padding: 0;
            max-width: 400px;
            width: 90%;
            transform: scale(0.7) translateY(-50px);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            text-align: center;
        }

        .alert-modal.show .alert-modal-content {
            transform: scale(1) translateY(0);
            opacity: 1;
        }

        .alert-modal-header {
            padding: 2rem 2rem 1rem;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white;
        }

        .alert-modal-body {
            padding: 2rem;
        }

        .alert-modal-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .alert-modal-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .alert-modal-message {
            color: #6b7280;
            line-height: 1.5;
        }

        .alert-modal-footer {
            padding: 0 2rem 2rem;
            display: flex;
            gap: 1rem;
        }

        .alert-modal-button {
            flex: 1;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .alert-modal-button-primary {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white;
        }

        .alert-modal-button-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.4);
        }

        .alert-modal-button-secondary {
            background: #f3f4f6;
            color: #374151;
        }

        .alert-modal-button-secondary:hover {
            background: #e5e7eb;
        }

        .alert-countdown {
            margin-top: 1rem;
            font-size: 0.875rem;
            color: #9ca3af;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .countdown-number {
            background: #3b82f6;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
            animation: pulse 1s infinite;
        }

        /* Success Theme */
        .alert-modal-success .alert-modal-header {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .alert-modal-success .alert-modal-button-primary {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        /* Error Theme */
        .alert-modal-error .alert-modal-header {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .alert-modal-error .alert-modal-button-primary {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        /* Warning Theme */
        .alert-modal-warning .alert-modal-header {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .alert-modal-warning .alert-modal-button-primary {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        /* Info Theme */
        .alert-modal-info .alert-modal-header {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
        }

        .alert-modal-info .alert-modal-button-primary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% {
                transform: translate3d(0,0,0);
            }
            40%, 43% {
                transform: translate3d(0,-8px,0);
            }
            70% {
                transform: translate3d(0,-4px,0);
            }
            90% {
                transform: translate3d(0,-2px,0);
            }
        }

        .alert-modal-icon {
            animation: bounce 1s ease infinite;
        }
    </style>
@endpush

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Back Button -->
            @include('customer.payment.partials.back-button')
            
            <!-- Payment Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 payment-card">
                <!-- Header with Gradient -->
                @include('customer.payment.partials.payment-header')
                
                <!-- Payment Details -->
                <div class="p-8">
                    <!-- Rack Info -->
                    @include('customer.payment.partials.rack-info', ['rak' => $rak])
                    
                    <!-- Price Breakdown -->
                    @include('customer.payment.partials.price-breakdown', ['rak' => $rak])
                    
                    <!-- Payment Methods Info -->
                    @include('customer.payment.partials.payment-methods-info')
                    
                    <!-- Payment Button -->
                    @include('customer.payment.partials.payment-button')
                    
                    <!-- Security Info -->
                    @include('customer.payment.partials.security-info')
                </div>
            </div>

            <!-- Additional Info Cards -->
            <div class="mt-12 grid md:grid-cols-3 gap-8">
                @include('customer.payment.partials.info-cards')
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    @include('customer.payment.partials.loading-overlay')
    
    <!-- WhatsApp Button -->
    @include('customer.payment.partials.whatsapp-button')
    
    <!-- Alert Container -->
    @include('customer.payment.partials.alert-container')
@endsection

@push('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        const payButton = document.getElementById('pay-button');
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
            
            // Show modal
            setTimeout(() => {
                alertElement.classList.add('show');
            }, 100);

            // Auto redirect countdown
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

            // Store interval ID for cleanup
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

            // Clear countdown interval
            if (alertElement.dataset.countdownInterval) {
                clearInterval(alertElement.dataset.countdownInterval);
            }

            // Close animation
            alertElement.classList.remove('show');
            
            setTimeout(() => {
                if (alertElement.parentNode) {
                    alertElement.parentNode.removeChild(alertElement);
                }
            }, 400);
        }

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
                        showAlert(
                            'Pembayaran berhasil! Terima kasih telah melakukan pembayaran. Anda akan dialihkan ke halaman rak.', 
                            'success', 
                            true, 
                            "{{ route('customer.list-rak.rak') }}"
                        );
                    } else {
                        showAlert(
                            'Pembayaran berhasil tapi gagal update status. Silakan hubungi admin untuk konfirmasi.', 
                            'warning'
                        );
                        console.error('Update status failed:', data.message);
                    }
                })
                .catch(error => {
                    loadingOverlay.classList.remove('active');
                    console.error('Error updating status:', error);
                    showAlert(
                        'Pembayaran berhasil! Terima kasih telah melakukan pembayaran.', 
                        'success', 
                        true, 
                        "{{ route('customer.list-rak.rak') }}"
                    );
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
                    showAlert(
                        'Pembayaran Anda dalam proses pending. Mohon selesaikan pembayaran Anda dalam waktu 24 jam.', 
                        'warning'
                    );
                    resetButton();
                },

                onError: function(result) {
                    console.error('Payment Error:', result);
                    showAlert(
                        'Pembayaran gagal! Silakan coba lagi atau hubungi customer service jika masalah berlanjut.', 
                        'error'
                    );
                    resetButton();
                },

                onClose: function() {
                    console.log('Payment popup closed');
                    showAlert(
                        'Anda menutup popup pembayaran tanpa menyelesaikan transaksi. Silakan klik "Bayar Sekarang" lagi untuk melanjutkan.', 
                        'info'
                    );
                    resetButton();
                }
            });
        });

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
    </script>
@endpush