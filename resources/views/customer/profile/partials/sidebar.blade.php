<div class="lg:col-span-1">
    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 profile-card">
        <div class="text-center">
            <div class="mb-6 relative">
                <div class="relative inline-block">
                    <img src="{{ $user->foto ? asset('storage/' . $user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=150&background=4A90E2&color=fff' }}" 
                         alt="Profile Photo" 
                         class="w-32 h-32 rounded-2xl mx-auto object-cover border-4 border-white shadow-lg">
                    <div class="absolute -bottom-2 -right-2 bg-blue-500 text-white p-2 rounded-full shadow-lg">
                        <i class="fas fa-user text-sm"></i>
                    </div>
                </div>
            </div>
            
            <h5 class="text-xl font-bold text-gray-800 mb-2">{{ $user->name }}</h5>
            <p class="text-gray-600 text-sm mb-4 flex items-center justify-center">
                <i class="fas fa-envelope mr-2 text-blue-500"></i>{{ $user->email }}
            </p>
            
            <button onclick="openPhotoModal()" 
                    class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-3 rounded-xl hover:shadow-lg transition-all duration-300 font-medium mb-6">
                <i class="fas fa-camera mr-2"></i> Ubah Foto
            </button>
            
            <div class="space-y-4 pt-4 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 flex items-center">
                        <i class="fas fa-calendar-alt mr-2 text-green-500"></i>Member Sejak
                    </span>
                    <strong class="text-gray-800">{{ $user->created_at->format('d M Y') }}</strong>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 flex items-center">
                        <i class="fas fa-warehouse mr-2 text-purple-500"></i>Total Sewa
                    </span>
                    <strong class="text-gray-800">
                        {{ \App\Models\CustomerHistory::where('customer_id', Auth::id())->where('activity_type', 'NEW_RENTAL')->count() }} Rak
                    </strong>
                </div>
            </div>
        </div>
    </div>
</div>