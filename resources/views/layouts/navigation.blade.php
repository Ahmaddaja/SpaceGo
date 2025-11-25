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
                 
                <a href="{{ route('customer.list-rak.rak') }}" class="flex flex-col items-center text-blue-600 group transition-all duration-300">
                    <div class="bg-blue-100 p-3 rounded-xl shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <span class="text-xs mt-2 font-medium">Rak Dibeli</span>
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
                        <img src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&size=32&background=4A90E2&color=fff' }}" 
                             alt="Profile" 
                             class="w-8 h-8 rounded-lg object-cover border-2 border-white shadow-sm">
                        <span class="text-sm font-medium text-gray-700 hidden md:block">{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div class="absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 z-50">
                        <div class="p-4 border-b border-gray-100">
                            <div class="flex items-center space-x-3">
                                <img src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&size=40&background=4A90E2&color=fff' }}" 
                                     alt="Profile" 
                                     class="w-10 h-10 rounded-lg object-cover border-2 border-blue-500 shadow-sm">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-2">
                            <a href="{{ route('customer.profile.index') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-blue-50 rounded-lg transition-all duration-300 text-sm font-medium mb-1">
                                <i class="fas fa-user-edit text-blue-500"></i>
                                <span>Edit Profile</span>  <!-- Tetap "Edit Profile" seperti logout -->
                            </a>
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