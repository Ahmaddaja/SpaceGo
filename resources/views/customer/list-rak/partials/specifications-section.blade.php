<div class="bg-white rounded-2xl shadow-xl p-8 mt-8 border border-gray-100">
    <div class="flex items-center space-x-4 mb-6 pb-4 border-b border-gray-200">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-3 rounded-xl">
            <i class="fas fa-clipboard-list text-white text-xl"></i>
        </div>
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Spesifikasi Teknis</h3>
            <p class="text-gray-600">Detail lengkap spesifikasi rak</p>
        </div>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @include('customer.list-rak.partials.spec-items')
    </div>
</div>