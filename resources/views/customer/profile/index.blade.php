@extends('layouts.app')

@section('title', 'Profile Saya - SPACEGO')

@push('styles')
<style>
    .profile-card {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    
    .profile-card:hover {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .input-focus:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        border-color: #3b82f6;
    }
    
    .success-alert {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border: 1px solid #a7f3d0;
    }
    
    .error-alert {
        background: linear-gradient(135deg, #fef2f2, #fee2e2);
        border: 1px solid #fecaca;
    }
    
    .modal-animation {
        animation: modalAppear 0.3s ease-out;
    }
    
    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6b7280;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        transition: all 0.3s ease;
    }
    
    .password-toggle:hover {
        color: #374151;
        background-color: #f3f4f6;
    }
    
    .password-input-container {
        position: relative;
    }
    
    .password-input-container input {
        padding-right: 45px;
    }
    
    @keyframes modalAppear {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(-10px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
</style>
@endpush

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-4 gap-8">
            
            <!-- Sidebar Profile -->
            @include('customer.profile.partials.sidebar')
            
            <!-- Form Profile -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 profile-card">
                    <div class="border-b border-gray-200 px-8 py-6">
                        <h5 class="text-2xl font-bold text-gray-800 flex items-center">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-3 rounded-xl mr-4">
                                <i class="fas fa-user-edit text-white text-xl"></i>
                            </div>
                            Informasi Profile
                        </h5>
                    </div>
                    
                    <div class="p-8">
                        @include('customer.profile.partials.alerts')
                        
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
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 input-focus @error('name') border-red-500 @enderror" 
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
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 input-focus @error('email') border-red-500 @enderror" 
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
                                    <input type="text" name="phone" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 input-focus @error('telepon') border-red-500 @enderror" 
                                           value="{{ old('phone', $user->phone ?? '') }}">
                                    @error('phone')
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
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 input-focus @error('perusahaan') border-red-500 @enderror" 
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
                                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 input-focus @error('alamat') border-red-500 @enderror">{{ old('alamat', $user->alamat ?? '') }}</textarea>
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
                                        <div class="password-input-container">
                                            <input type="password" name="password_lama" 
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 input-focus @error('password_lama') border-red-500 @enderror"
                                                   id="password_lama">
                                            <button type="button" class="password-toggle" data-target="password_lama">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        @error('password_lama')
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                                        </p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 font-semibold mb-3">Password Baru</label>
                                        <div class="password-input-container">
                                            <input type="password" name="password" 
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 input-focus @error('password') border-red-500 @enderror"
                                                   id="password">
                                            <button type="button" class="password-toggle" data-target="password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                                        </p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 font-semibold mb-3">Konfirmasi Password</label>
                                        <div class="password-input-container">
                                            <input type="password" name="password_confirmation" 
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 input-focus"
                                                   id="password_confirmation">
                                            <button type="button" class="password-toggle" data-target="password_confirmation">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
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
</div>

<!-- Modal Edit Foto -->
@include('customer.profile.partials.photo-modal')
@endsection

@push('scripts')
<script>
    // Preview foto sebelum upload
    document.getElementById('fotoInput')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewFoto').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    // Modal functions
    function openPhotoModal() {
        const modal = document.getElementById('editFotoModal');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.add('modal-animation');
        }, 10);
    }

    function closePhotoModal() {
        const modal = document.getElementById('editFotoModal');
        modal.classList.add('hidden');
        modal.classList.remove('modal-animation');
    }

    // Close modal when clicking outside
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('editFotoModal');
        modal?.addEventListener('click', function(e) {
            if (e.target === modal) {
                closePhotoModal();
            }
        });

        // Password toggle functionality
        const passwordToggles = document.querySelectorAll('.password-toggle');
        
        passwordToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    });
</script>
@endpush 