<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Saya - SPACEGO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-50 min-h-screen">

   <!-- Navigation -->
    <nav class="bg-white/90 backdrop-blur-md shadow-lg sticky top-0 z-50 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-3 rounded-2xl shadow-lg">
                        <i class="fas fa-warehouse text-white text-xl"></i>
                    </div>
                    <div>
                        <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">SPACEGO</span>
                        <p class="text-xs text-gray-500 font-medium">Storage Solution</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-8">
                    <a href="{{ route('customer.index') }}" class="flex flex-col items-center text-gray-600 hover:text-blue-600 group transition-all duration-300">
                        <div class="bg-gray-100 p-3 rounded-xl shadow-sm group-hover:bg-blue-100">
                            <i class="fas fa-home"></i>
                        </div>
                        <span class="text-xs mt-2 font-medium">Home</span>
                    </a>
                    
                    <a href="{{ route('customer.list-rak.list-rak') }}" class="flex flex-col items-center text-gray-600 hover:text-blue-600 group transition-all duration-300">
                        <div class="bg-gray-100 p-3 rounded-xl shadow-sm group-hover:bg-blue-100">
                            <i class="fas fa-pallet"></i>
                        </div>
                        <span class="text-xs mt-2 font-medium">Rak</span>
                    </a>

                    <a href="{{ route('customer.list-rak.rak') }}" class="flex flex-col items-center text-gray-600 hover:text-blue-600 group transition-all duration-300">
                        <div class="bg-gray-100 p-3 rounded-xl shadow-sm group-hover:bg-blue-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="text-xs mt-2 font-medium">Rak Dibeli</span>
                    </a>
                    
                    <a href="{{ route('customer.profile.index') }}" class="flex flex-col items-center text-blue-600 group transition-all duration-300">
                        <div class="bg-blue-100 p-3 rounded-xl shadow-sm">
                            <i class="fas fa-user"></i>
                        </div>
                        <span class="text-xs mt-2 font-medium">Profile</span>
                    </a>
                    
                    <a href="#" class="flex flex-col items-center text-gray-600 hover:text-blue-600 group transition-all duration-300">
                        <div class="bg-gray-100 p-3 rounded-xl shadow-sm group-hover:bg-blue-100">
                            <i class="fas fa-history"></i>
                        </div>
                        <span class="text-xs mt-2 font-medium">History</span>
                    </a>
                    
                    <!-- Dropdown Profile -->
                    <div class="relative group">
                        <button class="flex items-center space-x-3 bg-gray-100 hover:bg-gray-200 rounded-xl px-4 py-2 transition-all duration-300 shadow-sm">
                            <img src="{{ $user->foto ? asset('storage/' . $user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=32&background=4A90E2&color=fff' }}" 
                                 alt="Profile" 
                                 class="w-8 h-8 rounded-lg object-cover border-2 border-white shadow-sm">
                            <span class="text-sm font-medium text-gray-700 hidden md:block">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div class="absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 z-50">
                            <div class="p-4 border-b border-gray-100">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $user->foto ? asset('storage/' . $user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=40&background=4A90E2&color=fff' }}" 
                                         alt="Profile" 
                                         class="w-10 h-10 rounded-lg object-cover border-2 border-blue-500 shadow-sm">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-2">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-300 text-sm font-medium">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>Keluar</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="grid lg:grid-cols-4 gap-8">
            
            <!-- Sidebar Profile -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
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
                        
                        <button onclick="document.getElementById('editFotoModal').classList.remove('hidden')" 
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
                                <strong class="text-gray-800">0 Rak</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Profile -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100">
                    <div class="border-b border-gray-200 px-8 py-6">
                        <h5 class="text-2xl font-bold text-gray-800 flex items-center">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-3 rounded-xl mr-4">
                                <i class="fas fa-user-edit text-white text-xl"></i>
                            </div>
                            Informasi Profile
                        </h5>
                    </div>
                    
                    <div class="p-8">
                        @if(session('success'))
                        <div class="bg-gradient-to-r from-green-50 to-green-100 border border-green-200 text-green-700 px-6 py-4 rounded-2xl mb-6 flex items-center shadow-sm">
                            <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                        @endif

                        @if($errors->any())
                        <div class="bg-gradient-to-r from-red-50 to-red-100 border border-red-200 text-red-700 px-6 py-4 rounded-2xl mb-6 flex items-center shadow-sm">
                            <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                            <div>
                                <strong class="font-medium">Error!</strong> Silakan periksa form kembali.
                            </div>
                        </div>
                        @endif

                        <form action="{{ route('customer.profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="grid md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-3 flex items-center">
                                        <i class="fas fa-user-circle text-blue-500 mr-2"></i>
                                        Nama Lengkap <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <input type="text" name="name" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 @error('name') border-red-500 @enderror" 
                                           value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-gray-700 font-semibold mb-3 flex items-center">
                                        <i class="fas fa-envelope text-blue-500 mr-2"></i>
                                        Email <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <input type="email" name="email" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 @error('email') border-red-500 @enderror" 
                                           value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-gray-700 font-semibold mb-3 flex items-center">
                                        <i class="fas fa-phone text-green-500 mr-2"></i>
                                        No. Telepon
                                    </label>
                                    <input type="text" name="telepon" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 @error('telepon') border-red-500 @enderror" 
                                           value="{{ old('telepon', $user->telepon ?? '') }}">
                                    @error('telepon')
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-gray-700 font-semibold mb-3 flex items-center">
                                        <i class="fas fa-building text-purple-500 mr-2"></i>
                                        Perusahaan/Instansi
                                    </label>
                                    <input type="text" name="perusahaan" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 @error('perusahaan') border-red-500 @enderror" 
                                           value="{{ old('perusahaan', $user->perusahaan ?? '') }}">
                                    @error('perusahaan')
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-gray-700 font-semibold mb-3 flex items-center">
                                    <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>
                                    Alamat Lengkap
                                </label>
                                <textarea name="alamat" rows="3" 
                                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 @error('alamat') border-red-500 @enderror">{{ old('alamat', $user->alamat ?? '') }}</textarea>
                                @error('alamat')
                                <p class="text-red-500 text-sm mt-2 flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div class="border-t border-gray-200 pt-6 mb-6">
                                <h6 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                                    <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 p-2 rounded-lg mr-3">
                                        <i class="fas fa-key text-white"></i>
                                    </div>
                                    Ubah Password (Opsional)
                                </h6>

                                <div class="grid md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-gray-700 font-semibold mb-3">Password Lama</label>
                                        <input type="password" name="password_lama" 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 @error('password_lama') border-red-500 @enderror">
                                        @error('password_lama')
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                                        </p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 font-semibold mb-3">Password Baru</label>
                                        <input type="password" name="password" 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 @error('password') border-red-500 @enderror">
                                        @error('password')
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                                        </p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 font-semibold mb-3">Konfirmasi Password</label>
                                        <input type="password" name="password_confirmation" 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-4 pt-4 border-t border-gray-200">
                                <button type="reset" class="px-8 py-3 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition-all duration-300 font-medium shadow-sm">
                                    <i class="fas fa-undo mr-2"></i> Reset
                                </button>
                                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium shadow-md">
                                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Foto -->
    <div id="editFotoModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95">
            <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h5 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-camera text-blue-500 mr-3"></i>
                    Ubah Foto Profile
                </h5>
                <button onclick="document.getElementById('editFotoModal').classList.add('hidden')" 
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
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300" 
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
                    <button type="button" onclick="document.getElementById('editFotoModal').classList.add('hidden')" 
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

    <script>
        // Preview foto sebelum upload
        document.getElementById('fotoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewFoto').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        // Animasi modal
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('editFotoModal');
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        });
    </script>

</body>
</html>