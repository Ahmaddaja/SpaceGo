@extends('layouts.main', ['title' => 'Detail Transaksi'])

@section('title-content')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">Detail Transaksi - {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }}</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.laporan.pendapatan') }}">Laporan</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<div class="container-fluid">

    <!-- Summary -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Total Pendapatan</h6>
                    <h3 class="text-success mb-0">
                        Rp {{ number_format($summary->total, 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Jumlah Transaksi</h6>
                    <h3 class="text-primary mb-0">
                        {{ number_format($summary->count) }}
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Rata-rata</h6>
                    <h3 class="text-info mb-0">
                        Rp {{ number_format($summary->average, 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Transaksi</h5>
            <a href="{{ route('admin.laporan.pendapatan', ['year' => $year]) }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Tanggal</th>
                            <th>Customer</th>
                            <th>Rak</th>
                            <th>Metode</th>
                            <th class="text-right">Jumlah</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->order_id }}</td>
                            <td>{{ $transaction->transaction_time->format('d/m/Y H:i') }}</td>
                            <td>{{ $transaction->user->name }}</td>
                            <td>{{ $transaction->rak->nama_rak ?? '-' }}</td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $transaction->payment_type ?? 'Midtrans' }}
                                </span>
                            </td>
                            <td class="text-right text-success">
                                Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                            </td>
                            <td>
                                @if(in_array($transaction->transaction_status, ['settlement', 'capture']))
                                    <span class="badge badge-success">Berhasil</span>
                                @else
                                    <span class="badge badge-secondary">{{ $transaction->transaction_status }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Tidak ada transaksi pada periode ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection