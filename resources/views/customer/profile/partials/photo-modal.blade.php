<div id="editFotoModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-300">
        <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h5 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-camera text-blue-500 mr-3"></i>
                Ubah Foto Profile
            </h5>
            <button onclick="closePhotoModal()" 
                    class="text-gray-500 hover:text-gray-700 transition-colors duration-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form action="{{ route('customer.profile.upload-foto') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6">
                <div class="text-center mb-6">
                    <div class="relative inline-block">
                        <img id="previewFoto" 
                             src="{{ $user->foto ? asset('storage/' . $user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=200&background=4A90E2&color=fff' }}" 
                             class="w-48 h-48 rounded-2xl object-cover border-4 border-white shadow-lg mx-auto">
                        <div class="absolute -bottom-2 -right-2 bg-blue-500 text-white p-2 rounded-full shadow-lg">
                            <i class="fas fa-user text-sm"></i>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-3 flex items-center">
                        <i class="fas fa-image text-purple-500 mr-2"></i>
                        Pilih Foto Baru
                    </label>
                    <input type="file" name="foto" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 input-focus" 
                           accept="image/*" 
                           id="fotoInput" 
                           required>
                    <small class="text-gray-500 text-sm mt-2 block">
                        <i class="fas fa-info-circle mr-1"></i>
                        Format: JPG, PNG. Maksimal 2MB
                    </small>
                </div>
            </div>
            
            <div class="border-t border-gray-200 px-6 py-4 flex justify-end gap-3">
                <button type="button" onclick="closePhotoModal()" 
                        class="px-6 py-3 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition-all duration-300 font-medium">
                    Batal
                </button>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium">
                    Upload Foto
                </button>
            </div>
        </form>
    </div>
</div>