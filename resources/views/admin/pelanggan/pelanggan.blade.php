@extends('layouts.main', ['title' => 'Daftar Pelanggan'])

@section('styles')
    @include('admin.pelanggan.partials.styles')
@endsection

@section('title-content')
<div class="d-flex justify-content-between align-items-right">
    <h1 class="m-0">Daftar Pelanggan</h1>
    @include('admin.pelanggan.partials.breadcrumb', ['page' => 'Kelola Pelanggan']) 
</div>   
@endsection

@section('content')
<div class="container-fluid">

    @include('admin.raks.partials.alert')

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 font-weight-bold">Daftar Pelanggan</h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>#</th>
                            <th>Foto Profile</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>No. Telepon</th>
                            <th>Perusahaan</th>
                            <th>Dibuat Pada</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($customers as $index => $customer)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($customer->foto)
                                            <img src="{{ asset('storage/' . $customer->foto) }}" 
                                                 alt="Profile {{ $customer->name }}" 
                                                 class="rounded-lg object-cover border-2 border-white shadow-sm"
                                                 width="40" 
                                                 height="40"
                                                 style="object-fit: cover;">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center customer-initial-text"
                                                 style="width: 40px; height: 40px;">
                                                {{ strtoupper(substr($customer->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <span class="font-weight-medium">{{ $customer->name }}</span>
                                        @if($customer->email_verified_at)
                                            <span class="badge badge-success ml-2" title="Terverifikasi">
                                                <i class="fas fa-check-circle"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="align-middle">{{ $customer->username }}</td>
                                <td class="align-middle">{{ $customer->email }}</td>
                                <td class="align-middle">{{ $customer->phone ?? '-' }}</td>
                                <td class="align-middle">{{ $customer->perusahaan ?? '-' }}</td>
                                <td class="align-middle">{{ $customer->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Belum ada customer terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>

        @if(method_exists($customers, 'links') && $customers->hasPages())
        <div class="card-footer bg-white border-0">
            {{ $customers->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Tambahkan Font Awesome untuk ikon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* Style untuk inisial customer yang transparan */
.customer-initial-text {
    background: transparent !important;
    border: none !important;
    font-weight: bold !important;
    font-size: 16px !important;
}

/* Default: Mode terang - warna gelap untuk kontras dengan background putih */
.customer-initial-text {
    color: #374151 !important; /* gray-700 - gelap untuk mode terang */
}

/* Mode gelap - warna putih untuk kontras dengan background gelap */
@media (prefers-color-scheme: dark) {
    .customer-initial-text {
        color: #ffffff !important; /* putih untuk mode gelap */
    }
}

/* Bootstrap 5 dark theme */
[data-bs-theme="dark"] .customer-initial-text {
    color: #ffffff !important;
}

/* Jika ada class dark mode custom */
.dark-mode .customer-initial-text,
body.dark .customer-initial-text,
.dark .customer-initial-text {
    color: #ffffff !important;
}

/* Untuk elemen dengan background gelap */
.bg-dark .customer-initial-text,
.table-dark .customer-initial-text,
.dark-theme .customer-initial-text {
    color: #ffffff !important;
}

/* Untuk elemen dengan background terang */
.bg-light .customer-initial-text,
.bg-white .customer-initial-text,
.light-theme .customer-initial-text {
    color: #374151 !important;
}

/* Fallback menggunakan CSS currentColor untuk inherit dari parent */
.customer-initial-text {
    color: currentColor !important;
}

/* Alternatif menggunakan CSS variables untuk kontrol yang lebih baik */
:root {
    --customer-initial-color: #374151; /* default mode terang */
}

[data-bs-theme="dark"] {
    --customer-initial-color: #ffffff; /* mode gelap */
}

.customer-initial-text {
    color: var(--customer-initial-color, currentColor) !important;
}
</style>
@endsection