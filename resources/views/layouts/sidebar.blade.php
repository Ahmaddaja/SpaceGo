<aside class="w-64 h-screen bg-white shadow-md p-5">

    <h2 class="text-xl font-semibold text-gray-700 mb-5">
        Menu
    </h2>

    <nav class="space-y-2">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 p-3 rounded-lg hover:bg-blue-100 hover:text-blue-600 text-gray-700">
            <i class="fa-solid fa-house"></i>
            <span>Dashboard</span>
        </a>

        {{-- Profile --}}
        <a href="{{ route('profile.edit') }}"
           class="flex items-center gap-3 p-3 rounded-lg hover:bg-blue-100 hover:text-blue-600 text-gray-700">
            <i class="fa-solid fa-user"></i>
            <span>Profile</span>
        </a>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full flex items-center gap-3 p-3 rounded-lg hover:bg-red-100 hover:text-red-600 text-gray-700">
                <i class="fa-solid fa-right-from-brush"></i>
                <span>Logout</span>
            </button>
        </form>

    </nav>

</aside>
