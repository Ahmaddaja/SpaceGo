<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Saya - SPACEGO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-50">

    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-6xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-6">
                <div class="flex items-center space-x-2">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span class="text-2xl font-bold text-gray-800">SPACEGO</span>
                </div>
                
                <div class="hidden md:block text-gray-600 text-sm border-l pl-4">
                    Selamat datang, <span class="font-semibold text-blue-600">{{ $user->name }}</span>
                </div>
            </div>
            
            <div class="flex items-center space-x-6">
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center text-gray-600 hover:text-blue-600 group transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span class="text-xs mt-1 hidden md:block">Gudang</span>
                </a>
                
                <a href="{{ route('customer.profile.index') }}" class="flex flex-col items-center text-blue-600 group transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="text-xs mt-1 hidden md:block font-semibold">Profile</span>
                </a>

                <a href="#" class="flex flex-col items-center text-gray-600 hover:text-blue-600 group transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="text-xs mt-1 hidden md:block">History</span>
                </a>
                
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="max-w-6xl mx-auto px-6 py-8">
        <div class="grid md:grid-cols-3 gap-6">
            
            <!-- Sidebar Profile -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="text-center">
                        <div class="mb-4">
                            <img src="{{ $user->foto ? asset('storage/' . $user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=150&background=4A90E2&color=fff' }}" 
                                 alt="Profile Photo" 
                                 class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-blue-500">
                        </div>
                        
                        <h5 class="text-xl font-bold mb-1">{{ $user->name }}</h5>
                        <p class="text-gray-600 text-sm mb-4">
                            <i class="fas fa-envelope mr-1"></i>{{ $user->email }}
                        </p>
                        
                        <button onclick="document.getElementById('editFotoModal').classList.remove('hidden')" 
                                class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-camera mr-1"></i> Ubah Foto
                        </button>
                        
                        <div class="mt-6 pt-4 border-t">
                            <div class="flex justify-between mb-3">
                                <span class="text-gray-600">Member Sejak</span>
                                <strong>{{ $user->created_at->format('d M Y') }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Sewa</span>
                                <strong>0 Gudang</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Profile -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="border-b px-6 py-4">
                        <h5 class="text-xl font-bold text-gray-800">
                            <i class="fas fa-user-edit mr-2 text-blue-600"></i>Informasi Profile
                        </h5>
                    </div>
                    
                    <div class="p-6">
                        @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        </div>
                        @endif

                        @if($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <strong>Error!</strong> Silakan periksa form kembali.
                        </div>
                        @endif

                        <form action="{{ route('customer.profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="mb-4">
                                    <label class="block text-gray-700 font-semibold mb-2">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="name" 
                                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror" 
                                           value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 font-semibold mb-2">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" name="email" 
                                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror" 
                                           value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 font-semibold mb-2">
                                        No. Telepon
                                    </label>
                                    <input type="text" name="telepon" 
                                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('telepon') border-red-500 @enderror" 
                                           value="{{ old('telepon', $user->telepon ?? '') }}">
                                    @error('telepon')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 font-semibold mb-2">
                                        Perusahaan/Instansi
                                    </label>
                                    <input type="text" name="perusahaan" 
                                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('perusahaan') border-red-500 @enderror" 
                                           value="{{ old('perusahaan', $user->perusahaan ?? '') }}">
                                    @error('perusahaan')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">
                                    Alamat Lengkap
                                </label>
                                <textarea name="alamat" rows="3" 
                                          class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('alamat') border-red-500 @enderror">{{ old('alamat', $user->alamat ?? '') }}</textarea>
                                @error('alamat')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <hr class="my-6">
                            <h6 class="text-lg font-semibold mb-4">Ubah Password (Opsional)</h6>

                            <div class="grid md:grid-cols-3 gap-4">
                                <div class="mb-4">
                                    <label class="block text-gray-700 font-semibold mb-2">Password Lama</label>
                                    <input type="password" name="password_lama" 
                                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password_lama') border-red-500 @enderror">
                                    @error('password_lama')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 font-semibold mb-2">Password Baru</label>
                                    <input type="password" name="password" 
                                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                                    @error('password')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 font-semibold mb-2">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" 
                                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 mt-6">
                                <button type="reset" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                                    <i class="fas fa-undo mr-1"></i> Reset
                                </button>
                                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                    <i class="fas fa-save mr-1"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Foto -->
    <div id="editFotoModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
            <div class="border-b px-6 py-4 flex justify-between items-center">
                <h5 class="text-xl font-bold">Ubah Foto Profile</h5>
                <button onclick="document.getElementById('editFotoModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form action="{{ route('customer.profile.upload-foto') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6">
                    <div class="text-center mb-4">
                        <img id="previewFoto" 
                             src="{{ $user->foto ? asset('storage/' . $user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=200&background=4A90E2&color=fff' }}" 
                             class="w-48 h-48 rounded-full mx-auto object-cover border-4 border-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Pilih Foto Baru</label>
                        <input type="file" name="foto" class="w-full px-4 py-2 border rounded-lg" accept="image/*" id="fotoInput" required>
                        <small class="text-gray-500">Format: JPG, PNG. Maksimal 2MB</small>
                    </div>
                </div>
                
                <div class="border-t px-6 py-4 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('editFotoModal').classList.add('hidden')" 
                            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
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
    </script>

</body>
</html>