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
    // =========================================
    // ======= ALERT MODAL FUNCTIONS ===========
    // =========================================
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

    // =========================================
    // ======= PAYMENTS FUNCTIONS ===========
    // =========================================
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

    // =========================================
    // ======= LEPAS RAK FUNCTIONS ===========
    // =========================================
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
    // =========================================
    // ======= DETAILS PENDING FUNCTIONS =======
    // =========================================
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
        // Show loading animation
        Swal.fire({
            title: '<div class="flex flex-col items-center">' +
                '<div class="w-16 h-16 rounded-full border-4 border-blue-200 border-t-blue-600 animate-spin mb-4"></div>' +
                '<h3 class="text-xl font-semibold text-gray-800">Memuat Detail</h3>' +
                '</div>',
            html: '<p class="text-gray-600 mt-2">Sedang mengambil data tagihan...</p>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            showCancelButton: false,
            showCloseButton: false
        });

        fetch(`/customer/tagihan/${tagihanId}/detail`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            Swal.close(); // Close loading dialog
            
            if (data.success) {
                const tagihan = data.tagihan;
                
                // Determine status color
                let statusColor = 'gray';
                let statusIcon = 'fa-clock';
                if (tagihan.status === 'pending') {
                    statusColor = 'yellow';
                    statusIcon = 'fa-clock';
                } else if (tagihan.status === 'settlement') {
                    statusColor = 'green';
                    statusIcon = 'fa-check-circle';
                } else if (tagihan.status === 'cancel') {
                    statusColor = 'red';
                    statusIcon = 'fa-ban';
                } else if (tagihan.status === 'expired') {
                    statusColor = 'orange';
                    statusIcon = 'fa-hourglass-end';
                } else if (tagihan.status === 'overdue') {
                    statusColor = 'red';
                    statusIcon = 'fa-exclamation-triangle';
                } else if (tagihan.status === 'failed') {
                    statusColor = 'red';
                    statusIcon = 'fa-times-circle';
                }
                
                Swal.fire({
                    title: '<div class="flex items-center gap-4">' +
                        '<div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg">' +
                        '<i class="fas fa-file-invoice-dollar text-white text-2xl"></i>' +
                        '</div>' +
                        '<div class="text-left">' +
                        '<h2 class="text-2xl font-bold text-gray-800">Detail Tagihan</h2>' +
                        '<p class="text-gray-600 text-sm mt-1">Informasi lengkap tagihan sewa rak</p>' +
                        '</div>' +
                        '</div>',
                    html: `
                        <div class="text-left space-y-5 mt-6">
                            <!-- Header Card -->
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600 mb-1">Kode Tagihan</p>
                                        <p class="text-xl font-bold text-gray-800 font-mono tracking-wider">${tagihan.tagihan_code}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600 mb-1">Status</p>
                                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-${statusColor}-100 text-${statusColor}-800 font-semibold text-sm">
                                            <i class="fas ${statusIcon}"></i>
                                            ${tagihan.status}
                                        </span>
                                    </div>
                                </div>
                                ${tagihan.is_renewal ? `
                                <div class="mt-4 inline-flex items-center gap-2 px-3 py-2 bg-gradient-to-r from-purple-100 to-pink-100 rounded-lg border border-purple-200">
                                    <i class="fas fa-redo text-purple-600"></i>
                                    <span class="text-sm font-medium text-purple-700">Tagihan Perpanjangan</span>
                                </div>
                                ` : ''}
                            </div>
                            
                            <!-- Grid Section -->
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Rak Info -->
                                <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-300">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-pallet text-blue-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Rak</p>
                                            <p class="font-semibold text-gray-800">${tagihan.rak_nama}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Order Info -->
                                <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-300">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                                            <i class="fas fa-receipt text-green-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Order ID</p>
                                            <p class="font-semibold text-gray-800 font-mono text-xs">${tagihan.order_id}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Pricing Section -->
                            <div class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-xl p-5 border border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                    <i class="fas fa-money-bill-wave text-green-600"></i>
                                    Rincian Biaya
                                </h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                        <span class="text-gray-600">Harga Sewa</span>
                                        <span class="font-semibold text-gray-800">${tagihan.harga_sewa}</span>
                                    </div>
                                    ${tagihan.penalty_amount > 0 ? `
                                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                        <span class="text-gray-600 flex items-center gap-2">
                                            <i class="fas fa-exclamation-circle text-red-500"></i>
                                            Denda
                                        </span>
                                        <span class="font-bold text-red-600">${tagihan.penalty}</span>
                                    </div>
                                    ` : ''}
                                    <div class="flex justify-between items-center pt-3">
                                        <span class="text-lg font-bold text-gray-800">Total Tagihan</span>
                                        <span class="text-2xl font-bold text-green-600">${tagihan.total_tagihan}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Timeline Section -->
                            <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl p-5 border border-amber-200">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                    <i class="fas fa-history text-amber-600"></i>
                                    Timeline
                                </h3>
                                <div class="space-y-4">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-1">
                                            <i class="fas fa-plus-circle text-blue-600 text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Dibuat</p>
                                            <p class="font-semibold text-gray-800">${tagihan.created_at}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0 mt-1">
                                            <i class="fas fa-clock text-amber-600 text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Batas Pembayaran</p>
                                            <p class="font-semibold text-orange-600">${tagihan.expired_at}</p>
                                            <p class="text-xs text-gray-600 mt-1">
                                                <i class="fas fa-hourglass-half"></i>
                                                ${tagihan.remaining_time}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Button (Tengah) -->
                            <div class="flex justify-center mt-6 pt-4 border-t border-gray-100">
                                <button onclick="Swal.close()" 
                                        class="group relative px-8 py-3.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-xl transition-all duration-300 flex items-center justify-center gap-3 hover:shadow-xl hover:-translate-y-0.5 min-w-[180px]">
                                    <!-- Rotating X Icon -->
                                    <i class="fas fa-times group-hover:rotate-90 transition-transform duration-300 text-lg"></i>
                                    <span class="text-base font-semibold">Tutup</span>
                                    
                                    <!-- Glow effect on hover -->
                                    <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 opacity-0 group-hover:opacity-20 blur-md transition-opacity duration-300 -z-10"></div>
                                    
                                    <!-- Border animation -->
                                    <div class="absolute inset-0 rounded-xl border-2 border-transparent group-hover:border-white/30 transition-border duration-300"></div>
                                </button>
                            </div>
                        </div>
                    `,
                    width: 700,
                    showConfirmButton: false,
                    showCancelButton: false,
                    showCloseButton: true,
                    customClass: {
                        popup: 'rounded-2xl shadow-2xl',
                        closeButton: 'w-10 h-10 rounded-lg hover:bg-gray-100 flex items-center justify-center text-xl transition-colors hover:rotate-90 hover:scale-110 transform duration-300'
                    }
                });
            } else {
                // Error modal with modern design
                Swal.fire({
                    title: '<div class="flex flex-col items-center">' +
                        '<div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center mb-4">' +
                        '<i class="fas fa-exclamation-triangle text-red-600 text-3xl"></i>' +
                        '</div>' +
                        '<h3 class="text-2xl font-bold text-gray-800">Gagal Memuat</h3>' +
                        '</div>',
                    html: `
                        <div class="text-center space-y-4">
                            <p class="text-gray-600 text-lg">${data.message || 'Tidak dapat memuat detail tagihan'}</p>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-sm text-gray-700">
                                    <i class="fas fa-lightbulb mr-2"></i>
                                    Pastikan koneksi internet stabil dan coba beberapa saat lagi
                                </p>
                            </div>
                        </div>
                    `,
                    confirmButtonText: '<div class="flex items-center justify-center gap-2">' +
                                    '<i class="fas fa-redo"></i>' +
                                    '<span>Coba Lagi</span>' +
                                    '</div>',
                    confirmButtonColor: '#3b82f6',
                    showCancelButton: true,
                    cancelButtonText: '<div class="flex items-center justify-center gap-2 group">' +
                                    '<i class="fas fa-times group-hover:rotate-90 transition-transform duration-300"></i>' +
                                    '<span>Tutup</span>' +
                                    '</div>',
                    cancelButtonColor: '#6b7280',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'px-6 py-3 rounded-xl font-semibold transition-all duration-300 hover:shadow-lg',
                        cancelButton: 'px-6 py-3 rounded-xl font-semibold transition-all duration-300 hover:shadow-lg group'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        viewDetail(tagihanId);
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error fetching detail:', error);
            Swal.close(); // Close loading dialog
            
            Swal.fire({
                title: '<div class="flex flex-col items-center">' +
                    '<div class="w-20 h-20 rounded-full bg-yellow-100 flex items-center justify-center mb-4">' +
                    '<i class="fas fa-wifi-slash text-yellow-600 text-3xl"></i>' +
                    '</div>' +
                    '<h3 class="text-2xl font-bold text-gray-800">Koneksi Bermasalah</h3>' +
                    '</div>',
                html: `
                    <div class="text-center space-y-4">
                        <p class="text-gray-600 text-lg">Terjadi kesalahan saat memuat detail</p>
                        <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-200">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                Periksa koneksi internet Anda dan coba lagi
                            </p>
                            <p class="text-xs text-yellow-700 mt-2 font-mono">Error: ${error.message}</p>
                        </div>
                    </div>
                `,
                confirmButtonText: '<div class="flex items-center justify-center gap-2">' +
                                '<i class="fas fa-redo"></i>' +
                                '<span>Refresh</span>' +
                                '</div>',
                confirmButtonColor: '#f59e0b',
                showCancelButton: true,
                cancelButtonText: '<div class="flex items-center justify-center gap-2 group">' +
                                '<i class="fas fa-times group-hover:rotate-90 transition-transform duration-300"></i>' +
                                '<span>Tutup</span>' +
                                '</div>',
                cancelButtonColor: '#6b7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    viewDetail(tagihanId);
                }
            });
        });
    }

    // Helper function untuk payment dari detail view
    function continuePaymentFromDetail(snapToken, transactionId) {
        if (!snapToken || snapToken === '' || snapToken === 'null') {
            Swal.fire({
                title: '<div class="flex items-center gap-3">' +
                    '<div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">' +
                    '<i class="fas fa-exclamation-triangle text-orange-600 text-xl"></i>' +
                    '</div>' +
                    '<div>' +
                    '<h3 class="text-xl font-bold text-gray-800">Token Tidak Tersedia</h3>' +
                    '<p class="text-gray-600 text-sm mt-1">Silakan refresh halaman utama</p>' +
                    '</div>' +
                    '</div>',
                confirmButtonText: '<div class="flex items-center justify-center gap-2">' +
                                '<i class="fas fa-redo"></i>' +
                                '<span>Refresh</span>' +
                                '</div>',
                confirmButtonColor: '#f59e0b',
                showCancelButton: true,
                cancelButtonText: 'Tutup'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.reload();
                }
            });
            return;
        }
        continuePayment(snapToken, transactionId);
    }

    // FUNGSI BARU: Batalkan Tagihan
    function cancelPayment(tagihanId, tagihanCode) {
        Swal.fire({
            title: '<div class="flex items-center gap-3">' +
                '<div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">' +
                '<i class="fas fa-ban text-red-600 text-xl"></i>' +
                '</div>' +
                '<div class="text-left">' +
                '<h3 class="text-xl font-bold text-gray-800">Batalkan Tagihan?</h3>' +
                '<p class="text-gray-600 text-sm mt-1">Anda akan membatalkan tagihan berikut</p>' +
                '</div>' +
                '</div>',
            html: `
                <div class="text-left space-y-4 mt-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-file-invoice-dollar text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Kode Tagihan</p>
                                <p class="font-semibold text-gray-800 font-mono">${tagihanCode}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-red-50 border border-red-100 rounded-lg p-4 animate-pulse">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-exclamation-circle text-red-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-red-800">Perhatian</p>
                                <p class="text-sm text-red-600 mt-1">
                                    Tagihan yang dibatalkan tidak dapat dikembalikan. 
                                    Rak akan tersedia kembali untuk disewa oleh orang lain.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '<div class="flex items-center justify-center gap-2">' +
                            '<i class="fas fa-ban"></i>' +
                            '<span>Ya, Batalkan</span>' +
                            '</div>',
            cancelButtonText: '<div class="flex items-center justify-center gap-2">' +
                            '<i class="fas fa-times"></i>' +
                            '<span>Tidak, Kembali</span>' +
                            '</div>',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            showCloseButton: true,
            reverseButtons: true,
            customClass: {
                popup: 'rounded-2xl shadow-2xl',
                confirmButton: 'px-6 py-3 rounded-xl font-semibold transition-all duration-300 hover:shadow-lg',
                cancelButton: 'px-6 py-3 rounded-xl font-semibold transition-all duration-300 hover:shadow-lg',
                closeButton: 'hover:bg-gray-100 rounded-lg p-2 transition-colors'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Loading dengan animasi modern
                Swal.fire({
                    title: '<div class="flex flex-col items-center">' +
                        '<div class="w-16 h-16 rounded-full border-4 border-blue-200 border-t-blue-600 animate-spin mb-4"></div>' +
                        '<h3 class="text-xl font-semibold text-gray-800">Memproses Pembatalan</h3>' +
                        '</div>',
                    html: '<p class="text-gray-600 mt-2">Mohon tunggu sebentar...</p>',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    showCancelButton: false,
                    showCloseButton: false
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
                        // Success dengan animasi
                        Swal.fire({
                            title: '<div class="flex flex-col items-center">' +
                                '<div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mb-4 animate-bounce">' +
                                '<i class="fas fa-check-circle text-green-600 text-3xl"></i>' +
                                '</div>' +
                                '<h3 class="text-2xl font-bold text-gray-800">Berhasil!</h3>' +
                                '</div>',
                            html: `
                                <div class="text-center space-y-3">
                                    <p class="text-gray-600 text-lg">${data.message || 'Tagihan berhasil dibatalkan'}</p>
                                    <div class="bg-green-50 rounded-lg p-4 mt-4">
                                        <p class="text-sm text-green-700">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            Halaman akan dimuat ulang dalam <span class="font-bold countdown">3</span> detik
                                        </p>
                                    </div>
                                </div>
                            `,
                            showConfirmButton: false,
                            showCancelButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            willClose: () => {
                                window.location.reload();
                            },
                            didOpen: () => {
                                // Countdown timer
                                const timer = Swal.getPopup().querySelector('.countdown');
                                let countdown = 3;
                                const interval = setInterval(() => {
                                    countdown--;
                                    timer.textContent = countdown;
                                    if (countdown <= 0) {
                                        clearInterval(interval);
                                    }
                                }, 1000);
                            }
                        });
                    } else {
                        // Error dengan desain modern
                        Swal.fire({
                            title: '<div class="flex flex-col items-center">' +
                                '<div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center mb-4">' +
                                '<i class="fas fa-times-circle text-red-600 text-3xl"></i>' +
                                '</div>' +
                                '<h3 class="text-2xl font-bold text-gray-800">Gagal</h3>' +
                                '</div>',
                            html: `
                                <div class="text-center space-y-3">
                                    <p class="text-gray-600 text-lg">${data.message || 'Tidak dapat membatalkan tagihan'}</p>
                                    <div class="bg-red-50 rounded-lg p-4 mt-4">
                                        <p class="text-sm text-red-700">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                            Silakan coba beberapa saat lagi atau hubungi support
                                        </p>
                                    </div>
                                </div>
                            `,
                            confirmButtonText: '<div class="flex items-center justify-center gap-2">' +
                                            '<i class="fas fa-redo"></i>' +
                                            '<span>Coba Lagi</span>' +
                                            '</div>',
                            confirmButtonColor: '#dc2626',
                            cancelButtonText: '<div class="flex items-center justify-center gap-2">' +
                                            '<i class="fas fa-times"></i>' +
                                            '<span>Tutup</span>' +
                                            '</div>',
                            cancelButtonColor: '#6b7280',
                            showCancelButton: true,
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                cancelPayment(tagihanId, tagihanCode);
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error canceling payment:', error);
                    Swal.fire({
                        title: '<div class="flex flex-col items-center">' +
                            '<div class="w-20 h-20 rounded-full bg-yellow-100 flex items-center justify-center mb-4">' +
                            '<i class="fas fa-wifi-slash text-yellow-600 text-3xl"></i>' +
                            '</div>' +
                            '<h3 class="text-2xl font-bold text-gray-800">Koneksi Error</h3>' +
                            '</div>',
                        html: `
                            <div class="text-center space-y-3">
                                <p class="text-gray-600 text-lg">Terjadi masalah koneksi jaringan</p>
                                <div class="bg-yellow-50 rounded-lg p-4 mt-4">
                                    <p class="text-sm text-yellow-700">
                                        <i class="fas fa-lightbulb mr-2"></i>
                                        Periksa koneksi internet Anda dan coba lagi
                                    </p>
                                </div>
                            </div>
                        `,
                        confirmButtonText: '<div class="flex items-center justify-center gap-2">' +
                                        '<i class="fas fa-redo"></i>' +
                                        '<span>Coba Lagi</span>' +
                                        '</div>',
                        confirmButtonColor: '#f59e0b'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            cancelPayment(tagihanId, tagihanCode);
                        }
                    });
                });
            }
        });
    }
    // =========================================
    // ======= DETAILS EXPIRED FUNCTIONS =======
    // =========================================
        function viewExpiredDetail(tagihanId) {
        console.log('View Expired Detail clicked, ID:', tagihanId);
        
        // Show elegant loading animation
        Swal.fire({
            title: '<div class="flex flex-col items-center">' +
                '<div class="relative w-20 h-20 mb-4">' +
                '<div class="absolute inset-0 rounded-full border-4 border-red-200"></div>' +
                '<div class="absolute inset-2 rounded-full border-4 border-red-500 animate-spin border-t-transparent"></div>' +
                '<div class="absolute inset-0 flex items-center justify-center">' +
                '<i class="fas fa-hourglass-end text-red-500 text-xl"></i>' +
                '</div>' +
                '</div>' +
                '<h3 class="text-xl font-semibold text-gray-800">Memuat Detail</h3>' +
                '</div>',
            html: '<p class="text-gray-600 mt-2">Mengambil data tagihan kadaluarsa...</p>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            showCancelButton: false,
            showCloseButton: false
        });

        fetch(`/customer/tagihan/${tagihanId}/detail`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            Swal.close(); // Close loading dialog
            
            if (data.success) {
                const tagihan = data.tagihan;
                
                Swal.fire({
                    title: '<div class="flex items-center gap-4 pb-3 border-b border-red-100">' +
                        '<div class="w-16 h-16 rounded-xl bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center shadow-lg relative">' +
                        '<i class="fas fa-hourglass-end text-white text-2xl"></i>' +
                        '<div class="absolute -top-2 -right-2 w-8 h-8 rounded-full bg-white border-2 border-red-500 flex items-center justify-center shadow-lg">' +
                        '<i class="fas fa-times text-red-600 text-xs"></i>' +
                        '</div>' +
                        '</div>' +
                        '<div class="text-left">' +
                        '<h2 class="text-2xl font-bold text-gray-800">Tagihan Kadaluarsa</h2>' +
                        '<p class="text-gray-600 text-sm mt-1">Tagihan ini telah melewati batas waktu pembayaran</p>' +
                        '</div>' +
                        '</div>',
                    html: `
                        <div class="text-left space-y-5 mt-6">
                            <!-- Warning Banner -->
                            <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-xl p-5 border border-red-200 shadow-sm">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-red-800 mb-1">Tagihan Telah Kadaluarsa</h3>
                                        <p class="text-red-700 text-sm">
                                            Pembayaran tidak dapat dilakukan karena telah melewati batas waktu. 
                                            Rak telah tersedia kembali untuk disewa oleh pengguna lain.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- ID Card -->
                            <div class="bg-gradient-to-r from-gray-50 to-slate-100 rounded-xl p-5 border border-gray-200 shadow-sm">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Kode Tagihan</p>
                                        <p class="text-xl font-bold text-gray-800 font-mono tracking-wider">${tagihan.tagihan_code}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Status</p>
                                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-100 text-red-800 font-bold text-sm shadow-sm">
                                            <i class="fas fa-hourglass-end"></i>
                                            KADALUARSA
                                        </span>
                                    </div>
                                </div>
                                ${tagihan.is_renewal ? `
                                <div class="mt-4 inline-flex items-center gap-2 px-3 py-2 bg-gradient-to-r from-purple-100 to-pink-100 rounded-lg border border-purple-200">
                                    <i class="fas fa-redo text-purple-600"></i>
                                    <span class="text-sm font-medium text-purple-700">Tagihan Perpanjangan</span>
                                </div>
                                ` : ''}
                            </div>

                            <!-- Item Details -->
                            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                    <i class="fas fa-info-circle text-blue-600"></i>
                                    Informasi Rak
                                </h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <div>
                                            <p class="text-xs text-gray-500">Nama Rak</p>
                                            <p class="font-semibold text-gray-800">${tagihan.rak_nama}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Order ID</p>
                                            <p class="font-mono text-xs text-gray-600 bg-gray-50 p-2 rounded">${tagihan.order_id}</p>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <div>
                                            <p class="text-xs text-gray-500">Dibuat</p>
                                            <p class="font-semibold text-gray-800">${tagihan.created_at}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Kadaluarsa</p>
                                            <p class="font-semibold text-red-600">${tagihan.expired_at}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pricing Details -->
                            <div class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-xl p-5 border border-gray-200 shadow-sm">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                    <i class="fas fa-money-bill-wave text-green-600"></i>
                                    Ringkasan Pembayaran
                                </h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                        <span class="text-gray-600">Harga Sewa</span>
                                        <span class="font-semibold text-gray-800">${tagihan.harga_sewa}</span>
                                    </div>
                                    ${tagihan.penalty_amount > 0 ? `
                                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                        <span class="text-gray-600 flex items-center gap-2">
                                            <i class="fas fa-exclamation-circle text-red-500"></i>
                                            Denda
                                        </span>
                                        <span class="font-bold text-red-600">${tagihan.penalty}</span>
                                    </div>
                                    ` : ''}
                                    <div class="flex justify-between items-center pt-3">
                                        <span class="text-lg font-bold text-gray-800">Total Tagihan</span>
                                        <span class="text-2xl font-bold text-red-600">${tagihan.total_tagihan}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Timeline -->
                            <div class="relative">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                    <i class="fas fa-history text-amber-600"></i>
                                    Timeline
                                </h3>
                                <div class="relative">
                                    <!-- Timeline Line -->
                                    <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-red-200"></div>
                                    
                                    <div class="space-y-6 pl-10">
                                        <!-- Created -->
                                        <div class="relative">
                                            <div class="absolute -left-7 top-1 w-6 h-6 rounded-full bg-blue-100 border-2 border-blue-500 flex items-center justify-center">
                                                <i class="fas fa-plus text-blue-600 text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Dibuat</p>
                                                <p class="font-semibold text-gray-800">${tagihan.created_at}</p>
                                            </div>
                                        </div>
                                        
                                        <!-- Expired -->
                                        <div class="relative">
                                            <div class="absolute -left-7 top-1 w-6 h-6 rounded-full bg-red-100 border-2 border-red-500 flex items-center justify-center">
                                                <i class="fas fa-times text-red-600 text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Kadaluarsa</p>
                                                <p class="font-semibold text-red-600">${tagihan.expired_at}</p>
                                                <p class="text-xs text-gray-600 mt-1">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    ${tagihan.remaining_time}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="grid grid-cols-2 gap-3 pt-2">
                                <button onclick="Swal.close()" 
                                        class="py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium rounded-xl transition-all duration-300 flex items-center justify-center gap-2 hover:shadow-md group">
                                    <i class="fas fa-times group-hover:rotate-90 transition-transform duration-300"></i>
                                    Tutup
                                </button>
                                <button onclick="createNewOrder('${tagihan.rak_nama}', '${tagihan.tagihan_code}')" 
                                        class="py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium rounded-xl transition-all duration-300 flex items-center justify-center gap-2 hover:shadow-lg transform hover:-translate-y-0.5">
                                    <i class="fas fa-plus-circle"></i>
                                    Buat Order Baru
                                </button>
                            </div>
                            
                            <!-- Note -->
                            <div class="bg-gray-50 rounded-lg p-4 mt-4 border border-gray-200">
                                <p class="text-xs text-gray-600 flex items-start gap-2">
                                    <i class="fas fa-lightbulb text-amber-500 mt-0.5"></i>
                                    <span>
                                        <strong>Tips:</strong> Untuk menyewa rak yang sama, silakan buat order baru melalui halaman daftar rak.
                                        Rak ini sudah tersedia kembali untuk disewa.
                                    </span>
                                </p>
                            </div>
                        </div>
                    `,
                    width: 700,
                    showConfirmButton: false,
                    showCancelButton: false,
                    showCloseButton: true,
                    customClass: {
                        popup: 'rounded-2xl shadow-2xl border border-red-100',
                        closeButton: 'w-10 h-10 rounded-lg hover:bg-red-50 flex items-center justify-center text-xl transition-colors hover:text-red-600'
                    }
                });
            } else {
                // Error modal
                Swal.fire({
                    title: '<div class="flex flex-col items-center">' +
                        '<div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center mb-4 shadow-lg">' +
                        '<i class="fas fa-exclamation-triangle text-red-600 text-3xl"></i>' +
                        '</div>' +
                        '<h3 class="text-2xl font-bold text-gray-800">Gagal Memuat</h3>' +
                        '</div>',
                    html: `
                        <div class="text-center space-y-4">
                            <p class="text-gray-600 text-lg">${data.message || 'Tidak dapat memuat detail tagihan'}</p>
                            <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-xl p-4 border border-red-100">
                                <p class="text-sm text-red-800">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Pastikan tagihan masih tersedia atau coba beberapa saat lagi
                                </p>
                            </div>
                        </div>
                    `,
                    confirmButtonText: '<div class="flex items-center justify-center gap-2">' +
                                    '<i class="fas fa-redo"></i>' +
                                    '<span>Coba Lagi</span>' +
                                    '</div>',
                    confirmButtonColor: '#dc2626',
                    showCancelButton: true,
                    cancelButtonText: '<div class="flex items-center justify-center gap-2">' +
                                    '<i class="fas fa-times"></i>' +
                                    '<span>Tutup</span>' +
                                    '</div>',
                    cancelButtonColor: '#6b7280',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        viewExpiredDetail(tagihanId);
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error fetching detail:', error);
            Swal.close();
            
            Swal.fire({
                title: '<div class="flex flex-col items-center">' +
                    '<div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center mb-4">' +
                    '<i class="fas fa-wifi-slash text-red-600 text-3xl"></i>' +
                    '</div>' +
                    '<h3 class="text-2xl font-bold text-gray-800">Koneksi Error</h3>' +
                    '</div>',
                html: `
                    <div class="text-center space-y-4">
                        <p class="text-gray-600 text-lg">Gagal terhubung ke server</p>
                        <div class="bg-red-50 rounded-xl p-4 border border-red-200">
                            <p class="text-sm text-red-800">
                                <i class="fas fa-server mr-2"></i>
                                Periksa koneksi internet Anda dan coba lagi
                            </p>
                            <p class="text-xs text-red-700 mt-2 font-mono bg-white p-2 rounded">
                                ${error.message || 'Unknown error'}
                            </p>
                        </div>
                    </div>
                `,
                confirmButtonText: '<div class="flex items-center justify-center gap-2">' +
                                '<i class="fas fa-redo"></i>' +
                                '<span>Coba Lagi</span>' +
                                '</div>',
                confirmButtonColor: '#dc2626'
            }).then((result) => {
                if (result.isConfirmed) {
                    viewExpiredDetail(tagihanId);
                }
            });
        });
    }

    // Helper function untuk create new order
        function createNewOrder(rakNama, tagihanCode) {
        // Langsung redirect ke halaman list rak dengan parameter pencarian
        window.location.href = `/customer/list-rak?search_rak=${encodeURIComponent(rakNama)}`;
    }
    // =========================================
    // ======= EVENT LISTENER ===========
    // =========================================
    document.addEventListener('click', e => {
        if (e.target.classList.contains('alert-modal')) closeAlert(e.target.id);
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.alert-modal.show');
            if (openModal) closeAlert(openModal.id);
        }
    });

    // =========================================
    // ======= SESSION ALERTS ===========
    // =========================================
    @if (session('success')) showAlert('{{ session('success') }}', 'success', true, "{{ route('customer.list-rak.rak') }}"); @endif
    @if (session('error')) showAlert('{{ session('error') }}', 'error'); @endif
    @if (session('warning')) showAlert('{{ session('warning') }}', 'warning'); @endif
    @if (session('info')) showAlert('{{ session('info') }}', 'info'); @endif
</script>