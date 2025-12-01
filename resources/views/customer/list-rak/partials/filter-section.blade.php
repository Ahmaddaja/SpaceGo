<form method="GET" class="filter-card p-8 rounded-2xl">
    <div class="flex flex-col md:flex-row gap-6">
        
        <!-- SEARCH -->
        <div class="relative flex-1">
            <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}"
                   placeholder="Cari nama rak atau kode rak..."
                   class="w-full pl-12 pr-4 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 text-lg search-input">
        </div>

        <!-- FILTER JENIS -->
        <div class="relative md:w-64">
            <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                <i class="fas fa-filter text-gray-400"></i>
            </div>
            <select name="jenis" 
                    class="w-full pl-12 pr-4 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 appearance-none bg-white text-lg">
                <option value="">Semua Jenis Rak</option>
                @foreach ($jenisList as $jenis)
                    <option value="{{ $jenis }}" {{ request('jenis') == $jenis ? 'selected' : '' }}>
                        {{ $jenis }}
                    </option>
                @endforeach
            </select>
            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 pointer-events-none">
                <i class="fas fa-chevron-down text-gray-400"></i>
            </div>
        </div>

        <!-- BUTTON SUBMIT -->
        <button class="px-8 py-4 filter-button text-white rounded-xl hover:shadow-lg transition-all duration-300 shadow-md font-medium flex items-center justify-center space-x-3 text-lg">
            <i class="fas fa-sliders-h"></i>
            <span>Filter</span>
        </button>
    </div>
</form>