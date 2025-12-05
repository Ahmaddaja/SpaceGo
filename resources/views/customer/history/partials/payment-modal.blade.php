<!-- Modal untuk Detail Pembayaran -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Detail Pembayaran</h3>
            <button onclick="closePaymentModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="paymentModalContent" class="space-y-3">
            <!-- Diisi via JS -->
        </div>
        <div class="mt-6 flex justify-end">
            <button onclick="closePaymentModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    function showPaymentDetails(details) {
        let html = '<div class="space-y-2">';
        for (let key in details) {
            let value = details[key];
            let formattedKey = key.replace(/_/g, ' ').toUpperCase();
            let formattedValue = typeof value === 'number' && key === 'amount'
                ? 'Rp ' + value.toLocaleString('id-ID')
                : value;
            html += `
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-700">${formattedKey}</span>
                    <span class="text-gray-900 font-semibold">${formattedValue}</span>
                </div>
            `;
        }
        html += '</div>';
        document.getElementById('paymentModalContent').innerHTML = html;
        document.getElementById('paymentModal').classList.remove('hidden');
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
    }

    document.getElementById('paymentModal').addEventListener('click', function(e) {
        if (e.target === this) closePaymentModal();
    });
</script>