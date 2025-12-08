<!-- Modal untuk Detail Pembayaran -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4 pb-3 border-b">
            <h3 class="text-lg font-bold text-gray-800">Detail Pembayaran</h3>
            <button onclick="closePaymentModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="paymentModalContent" class="space-y-3">
            <!-- Diisi via JS -->
        </div>
        <div class="mt-6 pt-4 border-t flex justify-end">
            <button onclick="closePaymentModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    function showPaymentDetails(data) {
        let html = '<div class="space-y-2">';
        
        // Format tanggal jika ada created_at
        if (data.created_at) {
            html += `
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-700">TANGGAL TRANSAKSI</span>
                    <span class="text-gray-900 font-semibold">${formatDate(data.created_at)}</span>
                </div>
            `;
        }
        
        // Loop melalui semua properti
        for (let key in data) {
            if (key === 'created_at' || key === 'updated_at') continue;
            
            let value = data[key];
            let formattedKey = formatKey(key);
            let formattedValue = formatValue(key, value);
            
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
    
    function formatKey(key) {
        // Format key menjadi readable
        const keyMap = {
            'order_id': 'ORDER ID',
            'transaction_id': 'ID TRANSAKSI',
            'user_id': 'ID USER',
            'rak_id': 'ID RAK',
            'amount': 'JUMLAH',
            'payment_type': 'METODE PEMBAYARAN',
            'transaction_status': 'STATUS',
            'fraud_status': 'STATUS FRAUD',
            'is_renewal': 'PERPANJANGAN',
            'parent_transaction_id': 'ID TRANSAKSI INDUK',
            'penalty_amount': 'DENDA',
            'description': 'DESKRIPSI',
            'activity_type': 'TIPE AKTIVITAS',
            'additional_data': 'DATA TAMBAHAN'
        };
        
        return keyMap[key] || key.replace(/_/g, ' ').toUpperCase();
    }
    
    function formatValue(key, value) {
        // Format value berdasarkan key
        if (key === 'amount' || key === 'penalty_amount') {
            return 'Rp ' + Number(value).toLocaleString('id-ID');
        }
        
        if (key === 'transaction_status') {
            const statusMap = {
                'settlement': '✅ Berhasil',
                'pending': '⏳ Pending',
                'expire': '❌ Kedaluwarsa',
                'deny': '❌ Ditolak',
                'cancel': '❌ Dibatalkan'
            };
            return statusMap[value] || value;
        }
        
        if (key === 'payment_type') {
            const typeMap = {
                'qris': 'QRIS',
                'bank_transfer': 'Transfer Bank',
                'credit_card': 'Kartu Kredit',
                'echannel': 'Mandiri Bill'
            };
            return typeMap[value] || value.toUpperCase();
        }
        
        if (key === 'is_renewal') {
            return value ? 'Ya' : 'Tidak';
        }
        
        if (key === 'fraud_status') {
            return value === 'accept' ? '✅ Aman' : value || '-';
        }
        
        if (value === null || value === undefined || value === '') {
            return '-';
        }
        
        return value;
    }
    
    function formatDate(dateString) {
        if (!dateString) return '-';
        
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString;
        
        return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
    }

    document.getElementById('paymentModal').addEventListener('click', function(e) {
        if (e.target === this) closePaymentModal();
    });
</script>