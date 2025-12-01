<nav class="bg-white/90 backdrop-blur-md shadow-lg sticky top-0 z-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-6 py-2.5">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-2.5 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                    <svg class="w-5 h-5 text-white" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M3 10L12 3L21 10V21H3V10ZM5 12V19H19V12L12 7L5 12Z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">SPACEGO</span>
                    <p class="text-[10px] text-gray-500 font-medium">Storage Solution</p>
                </div>
            </div>

            <div class="flex items-center space-x-6">
                <!-- Home -->
                <a href="{{ route('customer.index') }}" 
                   class="flex flex-col items-center group transition-all duration-300 
                   {{ request()->routeIs('customer.index') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
                    <div class="p-2 rounded-lg shadow-sm transition-all duration-300 transform 
                        {{ request()->routeIs('customer.index') ? 'bg-blue-100 shadow-md scale-110' : 'bg-gray-100 group-hover:bg-blue-100 group-hover:shadow-md group-hover:scale-110' }}">
                        <i class="fas fa-home text-sm"></i>
                    </div>
                    <span class="text-[10px] mt-1 font-medium">Home</span>
                </a>

                <!-- Rak -->
                <a href="{{ route('customer.list-rak.list-rak') }}" 
                   class="flex flex-col items-center group transition-all duration-300 
                   {{ request()->routeIs('customer.list-rak.list-rak') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
                    <div class="p-2 rounded-lg shadow-sm transition-all duration-300 transform 
                        {{ request()->routeIs('customer.list-rak.list-rak') ? 'bg-blue-100 shadow-md scale-110' : 'bg-gray-100 group-hover:bg-blue-100 group-hover:shadow-md group-hover:scale-110' }}">
                        <i class="fas fa-pallet text-sm"></i>
                    </div>
                    <span class="text-[10px] mt-1 font-medium">Rak</span>
                    @if(request()->routeIs('customer.list-rak.list-rak'))
                        <div class="absolute -top-1 -right-1 w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                    @endif
                </a>

                <!-- Rak Anda -->
                <a href="{{ route('customer.list-rak.rak') }}" 
                   class="flex flex-col items-center group transition-all duration-300 
                   {{ request()->routeIs('customer.list-rak.rak') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
                    <div class="p-2 rounded-lg shadow-sm transition-all duration-300 transform 
                        {{ request()->routeIs('customer.list-rak.rak') ? 'bg-blue-100 shadow-md scale-110' : 'bg-gray-100 group-hover:bg-blue-100 group-hover:shadow-md group-hover:scale-110' }}">
                        <i class="fas fa-th-large text-sm"></i>
                    </div>
                    <span class="text-[10px] mt-1 font-medium">Rak Anda</span>
                    @if(request()->routeIs('customer.list-rak.rak'))
                        <div class="absolute -top-1 -right-1 w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                    @endif
                </a>

                <!-- History -->
                <a href="{{ route('customer.history') }}" 
                   class="flex flex-col items-center group transition-all duration-300 
                   {{ request()->routeIs('customer.history') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
                    <div class="p-2 rounded-lg shadow-sm transition-all duration-300 transform 
                        {{ request()->routeIs('customer.history') ? 'bg-blue-100 shadow-md scale-110' : 'bg-gray-100 group-hover:bg-blue-100 group-hover:shadow-md group-hover:scale-110' }}">
                        <i class="fas fa-history text-sm"></i>
                    </div>
                    <span class="text-[10px] mt-1 font-medium">History</span>
                </a>

<!-- Dropdown Profile -->
                <div class="relative group">
                    <button class="flex items-center space-x-3 bg-gray-100 hover:bg-gray-200 rounded-xl px-4 py-2 transition-all duration-300 shadow-sm hover:shadow-md transform hover:scale-105">
                        <img src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&size=32&background=4A90E2&color=fff' }}" 
                             alt="Profile" 
                             class="w-8 h-8 rounded-lg object-cover border-2 border-white shadow-sm">
                        <span class="text-sm font-medium text-gray-700 hidden md:block">{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down text-gray-500 text-xs transition-transform duration-300 group-hover:rotate-180"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div class="absolute right-0 top-full mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 z-50">
                        <div class="p-4 border-b border-gray-100">
                            <div class="flex items-center space-x-3">
                                <img src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&size=40&background=4A90E2&color=fff' }}" 
                                     alt="Profile" 
                                     class="w-10 h-10 rounded-lg object-cover border-2 border-blue-500 shadow-sm flex-shrink-0">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-2">
                            <a 
                                href="{{ route('customer.profile.index') }}" 
                                class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-blue-50 rounded-lg transition-all duration-300 text-sm font-medium mb-1 hover:text-blue-600 hover:transform hover:translate-x-1"
                            >
                                <i class="fas fa-user-edit text-blue-500"></i>
                                <span>Edit Profile</span>
                            </a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button 
                                    type="submit" 
                                    class="w-full flex items-center space-x-3 px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-300 text-sm font-medium hover:transform hover:translate-x-1"
                                >
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


<!-- Include AlpineJS if not already included -->
<script src="//unpkg.com/alpinejs" defer></script>