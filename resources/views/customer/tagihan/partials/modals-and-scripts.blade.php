<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white p-8 rounded-2xl shadow-2xl text-center">
        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-blue-600 mx-auto mb-4"></div>
        <p class="text-lg font-semibold text-gray-800">Memproses...</p>
        <p class="text-gray-600 mt-2">Mohon tunggu sebentar</p>
    </div>
</div>

<!-- Alert Container -->
<div id="alert-container" class="z-[9999]"></div>

<!-- Midtrans Snap Script -->
@if ($pendingTransactions->count() > 0)
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}" async defer></script>
@endif

<style>
    /* Fix untuk navbar */
    nav.navbar {
        z-index: 1000 !important;
        position: fixed !important;
        top: 0;
        left: 0;
        right: 0;
    }
    /* Loading Overlay */
    #loading-overlay {
        transition: opacity 0.3s ease;
        z-index: 9998;
    }
    #loading-overlay.hidden {
        display: none;
    }
    /* Alert Modal Styles - Perbaikan z-index */
    .alert-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        backdrop-filter: blur(4px);
        padding-top: 60px;
    }
    .alert-modal.show {
        opacity: 1;
        visibility: visible;
    }
    .alert-modal-content {
        background: white;
        border-radius: 16px;
        width: 90%;
        max-width: 450px;
        overflow: hidden;
        transform: translateY(20px);
        transition: transform 0.4s ease;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        max-height: 80vh;
        overflow-y: auto;
    }
    .alert-modal.show .alert-modal-content {
        transform: translateY(0);
    }
    .alert-modal-header {
        padding: 1.5rem;
        text-align: center;
        border-bottom: 1px solid #e5e7eb;
    }
    .alert-modal-success .alert-modal-header {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }
    .alert-modal-error .alert-modal-header {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }
    .alert-modal-warning .alert-modal-header {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }
    .alert-modal-info .alert-modal-header {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
    }
    .alert-modal-icon {
        font-size: 3rem;
        margin-bottom: 0.5rem;
    }
    .alert-modal-title {
        font-size: 1.5rem;
        font-weight: bold;
    }
    .alert-modal-body {
        padding: 1.5rem;
        text-align: center;
    }
    .alert-modal-message {
        font-size: 1rem;
        color: #4b5563;
        line-height: 1.5;
        margin-bottom: 1rem;
    }
    .alert-countdown {
        background: #f3f4f6;
        padding: 0.75rem;
        border-radius: 8px;
        font-size: 0.875rem;
        color: #6b7280;
        margin-top: 1rem;
    }
    .countdown-number {
        font-weight: bold;
        color: #3b82f6;
    }
    .alert-modal-footer {
        padding: 1.5rem;
        display: flex;
        gap: 0.75rem;
        border-top: 1px solid #e5e7eb;
        background: #f9fafb;
    }
    .alert-modal-button {
        flex: 1;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    .alert-modal-button-primary {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
    }
    .alert-modal-button-primary:hover {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        transform: translateY(-1px);
    }
    .alert-modal-button-secondary {
        background: white;
        color: #6b7280;
        border: 1px solid #d1d5db;
    }
    .alert-modal-button-secondary:hover {
        background: #f3f4f6;
        color: #374151;
    }
    .snap-midtrans-overlay {
        z-index: 9997 !important;
    }
</style>

<script>
    // ===== ALERT FUNCTIONS =====
    function showAlert(message, type = 'info', autoRedirect = false, redirectUrl = null) {
        const alertTypes = {
            success: { icon: 'fas fa-check-circle', class: 'alert-modal-success', title: 'Sukses!', buttonText: 'Lanjutkan' },
            error: { icon: 'fas fa-exclamation-triangle', class: 'alert-modal-error', title: 'Error!', buttonText: 'Coba Lagi' },
            warning: { icon: 'fas fa-bolt', class: 'alert-modal-warning', title: 'Peringatan!', buttonText: 'Mengerti' },
            info: { icon: 'fas fa-info-circle', class: 'alert-modal-info', title: 'Informasi', buttonText: 'OK' }
        };
        const alertConfig = alertTypes[type] || alertTypes.info;
        const alertId = 'alert-' + Date.now();
        const alertHTML = `
            <div id="${alertId}" class="alert-modal ${alertConfig.class}">
                <div class="alert-modal-content">
                    <div class="alert-modal-header">
                        <div class="alert-modal-icon"><i class="${alertConfig.icon}"></i></div>
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
                            <i class="fas fa-times mr-2"></i>Tutup
                        </button>
                        <button onclick="handleAlertAction('${alertId}', ${autoRedirect}, '${redirectUrl}')" 
                                class="alert-modal-button alert-modal-button-primary">
                            <i class="fas fa-check mr-2"></i>${alertConfig.buttonText}
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.getElementById('alert-container').insertAdjacentHTML('beforeend', alertHTML);
        const alertElement = document.getElementById(alertId);
        setTimeout(() => alertElement.classList.add('show'), 100);
        if (autoRedirect) startCountdown(alertId, redirectUrl);
        return alertId;
    }

    function startCountdown(alertId, redirectUrl) {
        let countdown = 5;
        const countdownElement = document.getElementById(`countdown-${alertId}`);
        const countdownInterval = setInterval(() => {
            countdown--;
            if (countdownElement) countdownElement.textContent = countdown;
            if (countdown <= 0) {
                clearInterval(countdownInterval);
                handleAlertAction(alertId, true, redirectUrl);
            }
        }, 1000);
        document.getElementById(alertId).dataset.countdownInterval = countdownInterval;
    }

    function handleAlertAction(alertId, autoRedirect = false, redirectUrl = null) {
        closeAlert(alertId);
        if (autoRedirect && redirectUrl) {
            setTimeout(() => window.location.href = redirectUrl, 300);
        }
    }

    function closeAlert(alertId) {
        const alertElement = document.getElementById(alertId);
        if (!alertElement) return;
        if (alertElement.dataset.countdownInterval) clearInterval(alertElement.dataset.countdownInterval);
        alertElement.classList.remove('show');
        setTimeout(() => alertElement.parentNode?.removeChild(alertElement), 400);
    }

    // ===== PAYMENT FUNCTIONS =====
    let isPaymentProcessing = false;
    function continuePayment(snapToken, transactionId) {
        if (isPaymentProcessing) {
            showAlert('Sedang memproses pembayaran sebelumnya. Tunggu sebentar.', 'warning');
            return;
        }
        if (!snapToken) {
            showAlert('Token pembayaran tidak tersedia.', 'error');
            return;
        }
        isPaymentProcessing = true;
        document.getElementById('loading-overlay').classList.remove('hidden');

        if (typeof snap === 'undefined') {
            showAlert('Sistem pembayaran sedang loading. Coba beberapa saat lagi.', 'warning');
            document.getElementById('loading-overlay').classList.add('hidden');
            isPaymentProcessing = false;
            return;
        }

        snap.pay(snapToken, {
            onSuccess: (result) => {
                document.getElementById('loading-overlay').classList.add('hidden');
                updatePaymentStatus(result, transactionId);
            },
            onPending: (result) => {
                document.getElementById('loading-overlay').classList.add('hidden');
                showAlert('Pembayaran Anda dalam proses pending. Mohon selesaikan pembayaran Anda dalam waktu 24 jam.', 'warning', true, "{{ route('customer.tagihan') }}");
                isPaymentProcessing = false;
            },
            onError: (result) => {
                document.getElementById('loading-overlay').classList.add('hidden');
                showAlert('Pembayaran gagal! Silakan coba lagi atau hubungi customer service jika masalah berlanjut.', 'error');
                isPaymentProcessing = false;
            },
            onClose: () => {
                document.getElementById('loading-overlay').classList.add('hidden');
                showAlert('Anda menutup popup pembayaran tanpa menyelesaikan transaksi. Silakan klik "Bayar Sekarang" lagi untuk melanjutkan.', 'info');
                isPaymentProcessing = false;
            }
        });
    }

    function updatePaymentStatus(result, transactionId = null) {
        const loadingOverlay = document.getElementById('loading-overlay');
        loadingOverlay.classList.remove('hidden');
        fetch("{{ route('payment.update-status') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                order_id: result.order_id,
                transaction_status: result.transaction_status,
                payment_type: result.payment_type || null,
                transaction_id: transactionId
            })
        })
        .then(async response => response.json().catch(() => ({ success: false, message: 'Invalid response' })))
        .then(data => {
            loadingOverlay.classList.add('hidden');
            if (data.success) {
                showAlert('Pembayaran berhasil! Terima kasih telah melakukan pembayaran. Anda akan dialihkan ke halaman rak.', 'success', true, "{{ route('customer.list-rak.rak') }}");
            } else {
                showAlert('Pembayaran berhasil tapi gagal update status. Silakan hubungi admin untuk konfirmasi.', 'warning');
            }
            isPaymentProcessing = false;
        })
        .catch(error => {
            loadingOverlay.classList.add('hidden');
            console.error('Error updating status:', error);
            showAlert('Pembayaran berhasil! Terima kasih telah melakukan pembayaran.', 'success', true, "{{ route('customer.list-rak.rak') }}");
            isPaymentProcessing = false;
        });
    }

    // ===== LEPAS RAK =====
    function confirmLepasRak(transactionId) {
        const alertId = showAlert('Apakah Anda yakin ingin melepas rak ini? Status akan berubah menjadi kadaluarsa.', 'warning');
        const alertElement = document.getElementById(alertId);
        const primaryButton = alertElement.querySelector('.alert-modal-button-primary');
        const secondaryButton = alertElement.querySelector('.alert-modal-button-secondary');
        if (primaryButton) {
            primaryButton.onclick = () => {
                closeAlert(alertId);
                submitLepasRak('form-lepas-' + transactionId);
            };
            primaryButton.innerHTML = '<i class="fas fa-check mr-2"></i>Ya, Lepas Rak';
        }
        if (secondaryButton) {
            secondaryButton.innerHTML = '<i class="fas fa-times mr-2"></i>Batal';
        }
        alertElement.querySelector('.alert-countdown')?.remove();
    }

    function submitLepasRak(formId, retryCount = 0) {
        const form = document.getElementById(formId);
        if (!form) {
            showAlert('Form tidak ditemukan. Silakan refresh halaman.', 'error');
            return;
        }
        const loadingOverlay = document.getElementById('loading-overlay');
        loadingOverlay?.classList.remove('hidden');

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(Object.fromEntries(new FormData(form)))
        })
        .then(response => response.json().catch(() => { throw new Error('Response bukan JSON') }))
        .then(data => {
            loadingOverlay?.classList.add('hidden');
            if (data.success) {
                showAlert('Rak berhasil dilepas dan status diubah menjadi kadaluarsa.', 'success', true, "{{ route('customer.tagihan') }}");
            } else {
                const retryAlertId = showAlert('Gagal melepas rak: ' + (data.message || 'Terjadi kesalahan'), 'error');
                const retryAlertElement = document.getElementById(retryAlertId);
                const retryButton = retryAlertElement.querySelector('.alert-modal-button-primary');
                if (retryButton) {
                    retryButton.innerHTML = '<i class="fas fa-redo mr-2"></i>Coba Lagi';
                    retryButton.onclick = () => {
                        closeAlert(retryAlertId);
                        submitLepasRak(formId, retryCount + 1);
                    };
                }
            }
        })
        .catch(error => {
            loadingOverlay?.classList.add('hidden');
            if (retryCount >= 3) {
                showAlert('Sudah 3 kali mencoba tetapi masih gagal. Silakan hubungi customer service.', 'error');
                return;
            }
            const errorAlertId = showAlert('Terjadi kesalahan: ' + error.message + '. Silakan coba lagi.', 'error');
            const errorAlertElement = document.getElementById(errorAlertId);
            const retryButton = errorAlertElement.querySelector('.alert-modal-button-primary');
            if (retryButton) {
                retryButton.innerHTML = '<i class="fas fa-redo mr-2"></i>Coba Lagi';
                retryButton.onclick = () => {
                    closeAlert(errorAlertId);
                    submitLepasRak(formId, retryCount + 1);
                };
            }
        });
    }

    // ===== EVENT LISTENERS =====
    document.addEventListener('click', e => {
        if (e.target.classList.contains('alert-modal')) closeAlert(e.target.id);
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.alert-modal.show');
            if (openModal) closeAlert(openModal.id);
        }
    });

    // ===== SESSION ALERTS =====
    @if (session('success')) showAlert('{{ session('success') }}', 'success', true, "{{ route('customer.list-rak.rak') }}"); @endif
    @if (session('error')) showAlert('{{ session('error') }}', 'error'); @endif
    @if (session('warning')) showAlert('{{ session('warning') }}', 'warning'); @endif
    @if (session('info')) showAlert('{{ session('info') }}', 'info'); @endif
</script>