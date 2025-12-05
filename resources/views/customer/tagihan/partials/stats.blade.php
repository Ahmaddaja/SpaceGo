<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
    <div class="bg-white p-4 rounded-lg shadow border-l-4 border-yellow-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-500">Menunggu Pembayaran</p>
                <p class="text-2xl font-bold">{{ $pendingTransactions->count() }}</p>
            </div>
            <i class="fas fa-clock text-yellow-500 text-2xl"></i>
        </div>
    </div>
    <div class="bg-white p-4 rounded-lg shadow border-l-4 border-red-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-500">Kadaluarsa</p>
                <p class="text-2xl font-bold">{{ $expiredTransactions->count() }}</p>
            </div>
            <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
        </div>
    </div>
    <div class="bg-white p-4 rounded-lg shadow border-l-4 border-orange-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-500">Perlu Perpanjangan</p>
                <p class="text-2xl font-bold">{{ $overdueTransactions->count() }}</p>
            </div>
            <i class="fas fa-calendar-times text-orange-500 text-2xl"></i>
        </div>
    </div>
</div>