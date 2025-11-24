@extends('layouts.main')

@section('title-content')
    @include('admin.raks.partials.breadcrumb', ['page' => 'Daftar Pelanggan'])
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
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->username }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone }}</td>
                                <td>{{ $customer->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
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
@endsection
