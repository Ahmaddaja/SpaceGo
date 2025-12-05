<div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
    <div class="flex items-center justify-between">
        <div class="text-sm text-gray-700">
            Menampilkan 
            <span class="font-medium">{{ $histories->firstItem() }}</span> 
            sampai 
            <span class="font-medium">{{ $histories->lastItem() }}</span> 
            dari 
            <span class="font-medium">{{ $histories->total() }}</span> 
            hasil
        </div>
        <div class="flex space-x-2">
            <!-- Previous Page -->
            @if ($histories->onFirstPage())
                <span class="px-3 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed">
                    <i class="fas fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $histories->previousPageUrl() }}" class="px-3 py-2 bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50 transition duration-200">
                    <i class="fas fa-chevron-left"></i>
                </a>
            @endif

            <!-- Page Numbers -->
            @php
                $current = $histories->currentPage();
                $last = $histories->lastPage();
                $start = max(1, $current - 2);
                $end = min($last, $current + 2);
            @endphp

            @if($start > 1)
                <a href="{{ $histories->url(1) }}" class="px-3 py-2 bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50 transition duration-200">1</a>
                @if($start > 2)
                    <span class="px-3 py-2 text-gray-500">...</span>
                @endif
            @endif

            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $current)
                    <span class="px-3 py-2 bg-blue-600 text-white rounded-lg font-medium">{{ $page }}</span>
                @else
                    <a href="{{ $histories->url($page) }}" class="px-3 py-2 bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50 transition duration-200">{{ $page }}</a>
                @endif
            @endfor

            @if($end < $last)
                @if($end < $last - 1)
                    <span class="px-3 py-2 text-gray-500">...</span>
                @endif
                <a href="{{ $histories->url($last) }}" class="px-3 py-2 bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50 transition duration-200">{{ $last }}</a>
            @endif

            <!-- Next Page -->
            @if ($histories->hasMorePages())
                <a href="{{ $histories->nextPageUrl() }}" class="px-3 py-2 bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50 transition duration-200">
                    <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <span class="px-3 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed">
                    <i class="fas fa-chevron-right"></i>
                </span>
            @endif
        </div>
    </div>
</div>