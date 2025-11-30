@extends('layouts.main', ['title' => 'Laporan Pendapatan'])

@section('title-content')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">Laporan Pendapatan</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Laporan Pendapatan</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')
    <div class="container-fluid">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.laporan.pendapatan') }}" class="row">
                    <div class="col-md-3">
                        <label class="form-label">Tahun</label>
                        <select name="year" class="form-control">
                            @foreach ($availableYears as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Bulan (Opsional)</label>
                        <select name="month" class="form-control">
                            <option value="">Semua Bulan</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>
                        <a href="{{ route('admin.laporan.pendapatan') }}" class="btn btn-secondary">
                            Reset
                        </a>
                    </div>
                    <div class="col-md-3 d-flex align-items-end justify-content-end">
                        <a href="{{ route('admin.laporan.export.pdf', ['year' => $year, 'month' => $month]) }}"
                            class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Total Pendapatan {{ $year }}</h6>
                        <h2 class="text-success mb-0">
                            Rp {{ number_format($yearlyTotal, 0, ',', '.') }}
                        </h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Total Transaksi {{ $year }}</h6>
                        <h2 class="text-primary mb-0">
                            {{ number_format($yearlyTransactions) }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Detail Pendapatan per Bulan</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Bulan</th>
                                <th>Tahun</th>
                                <th class="text-center">Total Transaksi</th>
                                <th class="text-center">Rak Disewa</th>
                                <th class="text-right">Total Pendapatan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($revenues as $revenue)
                                <tr>
                                    <td>{{ $revenue->month_name }}</td>
                                    <td>{{ $revenue->year }}</td>
                                    <td class="text-center">{{ number_format($revenue->total_transactions) }}</td>
                                    <td class="text-center">{{ number_format($revenue->total_raks_rented) }}</td>
                                    <td class="text-right text-success">
                                        {{ $revenue->formatted_revenue }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.laporan.detail', ['year' => $revenue->year, 'month' => $revenue->month]) }}"
                                            class="btn btn-sm btn-info">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        Tidak ada data untuk tahun {{ $year }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($revenues->isNotEmpty())
                            <tfoot class="thead-light">
                                <tr class="font-weight-bold">
                                    <td colspan="2">TOTAL</td>
                                    <td class="text-center">{{ number_format($yearlyTransactions) }}</td>
                                    <td class="text-center">{{ number_format($revenues->sum('total_raks_rented')) }}</td>
                                    <td class="text-right text-success">
                                        Rp {{ number_format($yearlyTotal, 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
