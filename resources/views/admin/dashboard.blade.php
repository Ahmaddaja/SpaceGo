@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Simple Welcome Message -->
    <div class="welcome-admin">
        <h3 class="mb-0">
            Selamat Datang, <strong>{{ Auth()->user()->name }}!</strong>
        </h3>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="simple-card">
                <div class="card-body">
                    <h5 class="card-title">Ini Halaman Dashboard</h5>
                    <p class="card-text">Gunakan menu navigasi untuk mengelola aplikasi.</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-3">
            <div class="simple-card">
                <div class="card-body">
                    <h5 class="card-title">Fitur Admin</h5>
                    <p class="card-text">Anda dapat mengakses semua fitur admin dari sini.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>