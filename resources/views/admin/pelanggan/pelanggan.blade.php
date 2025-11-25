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
                                            <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg d-flex align-items-center justify-content-center text-white font-weight-bold shadow-sm"
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
@endsection