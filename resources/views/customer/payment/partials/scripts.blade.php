<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
// Variabel global untuk tracking payment processing
let isPaymentProcessing = false;

document.getElementById('pay-button').addEventListener('click', function () {
    // Cek jika sedang memproses pembayaran
    if (isPaymentProcessing) {
        showAlert('Sedang memproses pembayaran sebelumnya. Tunggu sebentar.', 'warning');
        return;
    }
    
    const snapToken = '{{ $snapToken }}';
    if (!snapToken || snapToken === '' || snapToken === 'null') {
        showAlert('Token pembayaran tidak tersedia. Silakan refresh halaman.', 'error');
        return;
    }
    
    // Set flag processing
    isPaymentProcessing = true;
    const payButton = document.getElementById('pay-button');
    const loadingOverlay = document.getElementById('loading-overlay');
    
    // Disable button dan tampilkan loading
    payButton.disabled = true;
    payButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Memproses...</span>';
    
    if (loadingOverlay) loadingOverlay.classList.remove('hidden');

    // Tunggu hingga Snap tersedia
    const checkSnapLoaded = setInterval(() => {
        if (typeof snap !== 'undefined') {
            clearInterval(checkSnapLoaded);
            console.log('💳 Opening Midtrans Snap...');
            
            snap.pay(snapToken, {
                onSuccess: function(result) {
                    console.log('✅ Payment Success:', result);
                    if (loadingOverlay) loadingOverlay.classList.add('hidden');
                    // Panggil fungsi update status
                    updateTransactionStatus(result.order_id, 'settlement', result.payment_type);
                },
                onPending: function(result) {
                    console.log('⏳ Payment Pending:', result);
                    if (loadingOverlay) loadingOverlay.classList.add('hidden');
                    // Reset button state
                    payButton.disabled = false;
                    payButton.innerHTML = '<i class="fas fa-credit-card"></i><span>Bayar Sekarang</span>';
                    isPaymentProcessing = false;
                    
                    showAlert('Pembayaran Anda dalam proses pending. Mohon selesaikan pembayaran dalam waktu 24 jam.', 'warning', true, "{{ route('customer.tagihan') }}");
                },
                onError: function(result) {
                    console.error('❌ Payment Error:', result);
                    if (loadingOverlay) loadingOverlay.classList.add('hidden');
                    // Reset button state
                    payButton.disabled = false;
                    payButton.innerHTML = '<i class="fas fa-credit-card"></i><span>Bayar Sekarang</span>';
                    isPaymentProcessing = false;
                    
                    showAlert('Pembayaran gagal! Silakan coba lagi atau hubungi customer service.', 'error');
                },
                onClose: function() {
                    console.log('🚪 Payment popup closed');
                    if (loadingOverlay) loadingOverlay.classList.add('hidden');
                    // Reset button state
                    payButton.disabled = false;
                    payButton.innerHTML = '<i class="fas fa-credit-card"></i><span>Bayar Sekarang</span>';
                    isPaymentProcessing = false;
                    
                    showAlert('Anda menutup popup pembayaran. Silakan klik "Bayar Sekarang" lagi untuk melanjutkan.', 'info');
                }
            });
        }
    }, 100);

    // Timeout jika Snap tidak load
    setTimeout(() => {
        if (typeof snap === 'undefined') {
            clearInterval(checkSnapLoaded);
            showAlert('Sistem pembayaran sedang loading. Coba beberapa saat lagi.', 'warning');
            if (loadingOverlay) loadingOverlay.classList.add('hidden');
            // Reset button state
            payButton.disabled = false;
            payButton.innerHTML = '<i class="fas fa-credit-card"></i><span>Bayar Sekarang</span>';
            isPaymentProcessing = false;
        }
    }, 5000);
});

// Fungsi untuk update status transaksi
function updateTransactionStatus(orderId, status, paymentType) {
    console.log('📤 Updating status...', { orderId, status, paymentType });
    
    const loadingOverlay = document.getElementById('loading-overlay');
    const payButton = document.getElementById('pay-button');
    
    if (loadingOverlay) loadingOverlay.classList.remove('hidden');

    fetch('{{ route("payment.update-status") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            order_id: orderId,
            transaction_status: status,
            payment_type: paymentType
        })
    })
    .then(response => {
        console.log('📡 Response status:', response.status);
        if (!response.ok) {
            throw new Error('HTTP error ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('✅ Response data:', data);
        if (loadingOverlay) loadingOverlay.classList.add('hidden');
        
        // Reset button state
        if (payButton) {
            payButton.disabled = false;
            payButton.innerHTML = '<i class="fas fa-credit-card"></i><span>Bayar Sekarang</span>';
        }
        
        isPaymentProcessing = false;
        
        if (data.success) {
            // SUKSES - TAMPILKAN ALERT DAN REDIRECT
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
            // Tetap redirect setelah 3 detik
            setTimeout(() => {
                window.location.href = "{{ route('customer.list-rak.rak') }}";
            }, 3000);
        }
    })
    .catch(error => {
        console.error('❌ Fetch error:', error);
        if (loadingOverlay) loadingOverlay.classList.add('hidden');
        
        // Reset button state
        if (payButton) {
            payButton.disabled = false;
            payButton.innerHTML = '<i class="fas fa-credit-card"></i><span>Bayar Sekarang</span>';
        }
        
        isPaymentProcessing = false;
        
        // Meskipun error, pembayaran mungkin sudah berhasil (callback server akan handle)
        showAlert(
            'Pembayaran berhasil! Terima kasih telah melakukan pembayaran.', 
            'success', 
            true, 
            "{{ route('customer.list-rak.rak') }}"
        );
    });
}
</script>
